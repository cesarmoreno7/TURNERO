<?php
    include 'menu_ppal_maestros.php';
?>

<?php
    include 'session.php';
    include 'db.php';
    $conn->set_charset("utf8"); //Para grantizar las tildes
    
    $cod_empresa = $_SESSION['cod_empresa'];
    $codigo_usu = $_SESSION['codigo_usu'];
    
    // Obtener el empleado_id y los datos de servicio del usuario actual
    $usuarioQuery = "SELECT e.empleado_id, es.servicio_id, es.centro_costo_id, es.grupo_id 
                     FROM usuarios u
                     JOIN empleados_servicio es ON u.empleado_id = es.empleado_id
                     JOIN empleados e ON e.empleado_id = es.empleado_id
                     WHERE u.codigo_usu = '$codigo_usu' AND es.estado = 'Activo' AND u.cod_empresa = $cod_empresa";
    $usuarioResult = $conn->query($usuarioQuery);
    
    if ($usuarioResult->num_rows == 0) {
        echo "<script>alert('Usuario no encontrado o sin servicios asignados activos.'); window.location.href = 'index.php';</script>";
        exit;
    }
    
    $usuarioData = $usuarioResult->fetch_assoc();
    $empleado_id = $usuarioData['empleado_id'];
    $servicio_id_usuario = $usuarioData['servicio_id'];
    $centro_costo_id_usuario = $usuarioData['centro_costo_id'];
    $grupo_id_usuario = $usuarioData['grupo_id'];
    
    // Filtrar las listas desplegables basadas en los servicios del usuario
    $empleados = $conn->query("SELECT DISTINCT e.empleado_id, CONCAT(e.nombre, ' ', e.apellido) AS nombre_completo
                               FROM empleados_servicio es
                               JOIN empleados e ON es.empleado_id = e.empleado_id
                               WHERE es.servicio_id = $servicio_id_usuario AND es.estado = 'Activo' AND es.cod_empresa = $cod_empresa");
    
    $turnos = $conn->query("SELECT cod_turno FROM turnos where cod_empresa = $cod_empresa");
    
    $servicios = $conn->query("SELECT DISTINCT es.servicio_id, serv.nombre
                               FROM empleados_servicio es INNER JOIN servicio serv ON(es.servicio_id = serv.servicio_id)
                               WHERE es.servicio_id = $servicio_id_usuario AND es.estado = 'Activo' AND es.cod_empresa = $cod_empresa");
    
    $centros_costos = $conn->query("SELECT DISTINCT es.centro_costo_id, cc.nombre
                                    FROM empleados_servicio es INNER JOIN centro_costo cc ON(es.centro_costo_id = cc.centro_costo_id)
                                    WHERE es.servicio_id = $servicio_id_usuario AND es.estado = 'Activo' AND es.cod_empresa = $cod_empresa");
    
    $grupos = $conn->query("SELECT DISTINCT es.grupo_id, gr.nombre
                            FROM empleados_servicio es INNER JOIN grupo gr ON(es.grupo_id = gr.grupo_id)
                            WHERE es.servicio_id = $servicio_id_usuario AND es.estado = 'Activo' AND es.cod_empresa = $cod_empresa");
    
    // Variables para almacenar los filtros seleccionados
    $filtros = [];
    $sql_detalle = "SELECT dt.*, e.nombre, e.apellido 
            FROM detalle_turno_def dt
            JOIN empleados e ON dt.empleado_id = e.empleado_id
            WHERE dt.cod_empresa = $cod_empresa";
    
    $sql_horas = "SELECT htd.*, e.nombre, e.apellido
                  FROM horas_turnos_def htd
                  JOIN empleados e ON htd.empleado_id = e.empleado_id
                  WHERE htd.cod_empresa = $cod_empresa"; // Consulta para horas_turnos_def
    
    // Aplicar los filtros si están seleccionados
    if (isset($_GET['empleado_id']) && $_GET['empleado_id'] != '') {
        $empleado_id = $_GET['empleado_id'];
        $sql_detalle .= " AND dt.empleado_id = $empleado_id";
        $sql_horas .= " AND empleado_id = $empleado_id"; // Filtro para horas_turnos_def
        $filtros['empleado_id'] = $empleado_id;
    }
    
    if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin']) && $_GET['fecha_inicio'] != '' && $_GET['fecha_fin'] != '') {
        $fecha_inicio = $_GET['fecha_inicio'];
        $fecha_fin = $_GET['fecha_fin'];
        $sql_detalle .= " AND dt.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        $sql_horas .= " AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'"; // Filtro para horas_turnos_def
        $filtros['fecha_inicio'] = $fecha_inicio;
        $filtros['fecha_fin'] = $fecha_fin;
    }
    
    if (isset($_GET['turno_id']) && $_GET['turno_id'] != '') {
        $turno_id = $_GET['turno_id'];
        $sql_detalle .= " AND dt.turno_id = $turno_id";
        $filtros['turno_id'] = $turno_id;
    }
    
    if (isset($_GET['servicio_id']) && $_GET['servicio_id'] != '') {
        $servicio_id = $_GET['servicio_id'];
        $sql_detalle .= " AND dt.servicio_id = $servicio_id";
        $filtros['servicio_id'] = $servicio_id;
    }
    
    if (isset($_GET['centro_costo_id']) && $_GET['centro_costo_id'] != '') {
        $centro_costo_id = $_GET['centro_costo_id'];
        $sql_detalle .= " AND dt.centro_costo_id = $centro_costo_id";
        $filtros['centro_costo_id'] = $centro_costo_id;
    }
    
    if (isset($_GET['grupo_id']) && $_GET['grupo_id'] != '') {
        $grupo_id = $_GET['grupo_id'];
        $sql_detalle .= " AND dt.grupo_id = $grupo_id";
        $filtros['grupo_id'] = $grupo_id;
    }
    
    //echo $sql_detalle;
    
    // Ejecutar la consulta con los filtros aplicados
    $result_detalle = $conn->query($sql_detalle);
    $result_horas = $conn->query($sql_horas); // Ejecutar consulta para horas_turnos_def
    
    //echo $sql_horas;
    
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta Detalle Turno</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        table {
            width: 50%;
            border-collapse: collapse;
            margin: 20px auto;
            font-family: Arial, sans-serif;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        caption {
            margin-bottom: 10px;
            font-size: 1.5em;
            font-weight: bold;
        }
    </style>

</head>
<body>
<div class="container mt-4">
    <h2>Consulta de Turnos</h2>
    <form action="form_consulta_turnos_def.php" method="GET" class="mb-4">
        <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="fecha_inicio">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required value="<?php echo isset($filtros['fecha_inicio']) ? $filtros['fecha_inicio'] : ''; ?>">
                </div>
                <div class="form-group col-md-4">
                    <label for="fecha_fin">Fecha Fin</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required value="<?php echo isset($filtros['fecha_fin']) ? $filtros['fecha_fin'] : ''; ?>">
                </div>
                <div class="form-group col-md-4">
                    <label for="empleado_id">Empleado</label>
                    <select name="empleado_id" id="empleado_id" class="form-control">
                        <option value="">Seleccione un empleado</option>
                        <?php while ($row = $empleados->fetch_assoc()): ?>
                            <option value="<?php echo $row['empleado_id']; ?>" <?php echo (isset($filtros['empleado_id']) && $filtros['empleado_id'] == $row['empleado_id']) ? 'selected' : ''; ?>>
                                <?php echo $row['nombre_completo']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="servicio_id">Servicio</label>
                <select name="servicio_id" id="servicio_id" class="form-control">
                    <option value="">Seleccione un servicio</option>
                    <?php while ($row = $servicios->fetch_assoc()): ?>
                        <option value="<?php echo $row['servicio_id']; ?>" <?php echo (isset($filtros['servicio_id']) && $filtros['servicio_id'] == $row['servicio_id']) ? 'selected' : ''; ?>>
                            <?php echo $row['nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="centro_costo_id">Centro de Costo</label>
                <select name="centro_costo_id" id="centro_costo_id" class="form-control">
                    <option value="">Seleccione un centro de costo</option>
                    <?php while ($row = $centros_costos->fetch_assoc()): ?>
                        <option value="<?php echo $row['centro_costo_id']; ?>" <?php echo (isset($filtros['centro_costo_id']) && $filtros['centro_costo_id'] == $row['centro_costo_id']) ? 'selected' : ''; ?>>
                            <?php echo $row['nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
           <div class="form-group col-md-4">
                <label for="grupo_id">Grupo</label>
                <select name="grupo_id" id="grupo_id" class="form-control">
                    <option value="">Seleccione un grupo</option>
                    <?php while ($row = $grupos->fetch_assoc()): ?>
                        <option value="<?php echo $row['grupo_id']; ?>" <?php echo (isset($filtros['grupo_id']) && $filtros['grupo_id'] == $row['grupo_id']) ? 'selected' : ''; ?>>
                            <?php echo $row['nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
         
        <button type="submit" class="btn btn-primary">Buscar</button>
       
    </form>

    <!-- Mostrar resultados de detalle_turno_def -->
    <h3>Resultados Detalle de Turnos Definitivos</h3>
    <table id="tablaDetalleTurnoDef" class="table table-bordered">
        <thead>
            <tr>
                <th>Empleado ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Fecha</th>
                <th>Turno ID</th>
                <th>Servicio ID</th>
                <th>Centro de Costo ID</th>
                <th>Grupo ID</th>
                <th>HD</th>
                <th>HN</th>
                <th>HED</th>
                <th>HEN</th>
                <th>Horas Contratadas</th>
                <th>Horas a Laborar</th>
                <th>Código Usuario</th>
                <th>Estado</th>
                <th>Observaciones</th>
                <th>Fecha Registro</th>
                <th>Fecha Actualización</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_detalle->num_rows > 0): ?>
                <?php while ($row = $result_detalle->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['empleado_id']; ?></td>
                        <td><?php echo $row['nombre']; ?></td>
                        <td><?php echo $row['apellido']; ?></td>
                        <td><?php echo $row['fecha']; ?></td>
                        <td><?php echo $row['turno_id']; ?></td>
                        <td><?php echo $row['servicio_id']; ?></td>
                        <td><?php echo $row['centro_costo_id']; ?></td>
                        <td><?php echo $row['grupo_id']; ?></td>
                        <td><?php echo $row['hd']; ?></td>
                        <td><?php echo $row['hn']; ?></td>
                        <td><?php echo $row['hed']; ?></td>
                        <td><?php echo $row['hen']; ?></td>
                        <td><?php echo $row['horas_contratadas']; ?></td>
                        <td><?php echo $row['horas_laborar_turno']; ?></td>
                        <td><?php echo $row['codigo_usu']; ?></td>
                        <td><?php echo $row['estado']; ?></td>
                        <td><?php echo $row['observaciones']; ?></td>
                        <td><?php echo $row['fecha_reg']; ?></td>
                        <td><?php echo $row['fecha_act']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="21" class="text-center">No se encontraron resultados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Mostrar resultados de horas_turnos_def -->
    <h3>Resultados detalle Horas por Turnos Definitivos</h3>
    <table id="tablaHorasTurnosDef"  class="table table-bordered">
        <thead>
            <tr>
                <th>Empleado ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Código Turno</th>
                <th>Fecha</th>
                <th>Total Horas</th>
                <th>Horas Diurnas Totales</th>
                <th>Horas Nocturnas Totales</th>
                <th>Horas Diurnas Ordinario LS</th>
                <th>Horas Nocturnas Ordinario LS</th>
                <th>Horas Festivas Diurnas LS</th>
                <th>Horas Festivas Nocturnas LS</th>
                <th>Horas Diurnas Ordinarias SD</th>
                <th>Horas Nocturnas Ordinarias SD</th>
                <th>Horas Festivas Diurnas SD</th>
                <th>Horas Festivas Nocturnas SD</th>
                <th>Horas Diurnas Ordinarias DLF</th>
                <th>Horas Nocturnas Ordinarias DLF</th>
                <th>Horas Festivas Diurnas DLF</th>
                <th>Horas Festivas Nocturnas DLF</th>
                <th>Horas Diurnas Ordinarias DLO LFMO</th>
                <th>Horas Nocturnas Ordinarias DLO LFMO</th>
                <th>Horas Festivas Diurnas DLO LFMO</th>
                <th>Horas Festivas Nocturnas DLO LFMO</th>
                <th>THED</th>
                <th>THENOC</th>                
                <th>Estado</th>
                <th>Observaciones</th>
                <th>Fecha Registro</th>
                <th>Fecha Actualización</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_horas->num_rows > 0): ?>
                <?php while ($row = $result_horas->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['empleado_id']; ?></td>
                        <td><?php echo $row['nombre']; ?></td>
                        <td><?php echo $row['apellido']; ?></td>
                        <td><?php echo $row['cod_turno']; ?></td>
                        <td><?php echo $row['fecha']; ?></td>
                        <td><?php echo $row['total_horas']; ?></td>
                        <td><?php echo $row['horas_diurnas_tot']; ?></td>
                        <td><?php echo $row['horas_nocturnas_tot']; ?></td>
                        <td><?php echo $row['horas_diurnas_ordinario_ls']; ?></td>
                        <td><?php echo $row['horas_nocturnas_ordinario_ls']; ?></td>
                        <td><?php echo $row['horas_festivas_diurnas_ls']; ?></td>
                        <td><?php echo $row['horas_festivas_nocturnas_ls']; ?></td>
                        <td><?php echo $row['horas_diurnas_ordinarias_sd']; ?></td>
                        <td><?php echo $row['horas_nocturnas_ordinarias_sd']; ?></td>
                        <td><?php echo $row['horas_festivas_diurnas_sd']; ?></td>
                        <td><?php echo $row['horas_festivas_nocturnas_sd']; ?></td>
                        <td><?php echo $row['horas_diurnas_ordinarias_dlf']; ?></td>
                        <td><?php echo $row['horas_nocturnas_ordinarias_dlf']; ?></td>
                        <td><?php echo $row['horas_festivas_diurnas_dlf']; ?></td>
                        <td><?php echo $row['horas_festivas_nocturnas_dlf']; ?></td>
                        <td><?php echo $row['horas_diurnas_ordinarias_dlo_lfmo']; ?></td>
                        <td><?php echo $row['horas_nocturnas_ordinarias_dlo_lfmo']; ?></td>
                        <td><?php echo $row['horas_festivas_diurnas_dlo_lfmo']; ?></td>
                        <td><?php echo $row['horas_festivas_nocturnas_dlo_lfmo']; ?></td>
                        <td><?php echo $row['thed']; ?></td>
                        <td><?php echo $row['thenoc']; ?></td>                                             
                        <td><?php echo $row['estado']; ?></td>
                        <td><?php echo $row['observaciones']; ?></td>
                        <td><?php echo $row['fecha_reg']; ?></td>
                        <td><?php echo $row['fecha_act']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="29" class="text-center">No se encontraron resultados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <table>
        <caption>Convenciones</caption>
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>ls</td>
                <td>Lunes a Sábado</td>
            </tr>
            <tr>
                <td>sd</td>
                <td>Sábado a Domingo</td>
            </tr>
            <tr>
                <td>dlf</td>
                <td>Domingo a Lunes Festivo</td>
            </tr>
            <tr>
                <td>dlo</td>
                <td>Domingo a Lunes Ordinario</td>
            </tr>
            <tr>
                <td>lfmo</td>
                <td>Lunes Festivo a Martes Ordinario</td>
            </tr>
            <tr>
                <td>thed</td>
                <td>Total Horas Extras Diurnas</td>
            </tr>
            <tr>
                <td>thenoc</td>
                <td>Total Horas Extras Nocturnas</td>
            </tr>
        </tbody>
    </table>
    
</div>


<script>
$(document).ready(function() {
    $('#tablaDetalleTurnoDef').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Exportar a Excel',
                title: 'Detalle de Turnos Definitivos'
            },
            {
                extend: 'print',
                text: 'Imprimir'
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.3/i18n/es_es.json"
        }
    });
});

$(document).ready(function() {
    $('#tablaHorasTurnosDef').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Exportar a Excel',
                title: 'Detalle Horas por Turnos Definitivos'
            },
            {
                extend: 'print',
                text: 'Imprimir'
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.3/i18n/es_es.json"
        }
    });
});
</script>


</body>
</html>