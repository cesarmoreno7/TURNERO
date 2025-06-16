<?php
    include 'menu_ppal_maestros.php';
?>
<?php
    // Conexión a la base de datos
    include("session.php");
    include("db.php");
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

    // Filtrar las listas desplegables 
    $servicios = $conn->query("SELECT DISTINCT es.servicio_id, serv.nombre
                               FROM empleados_servicio es INNER JOIN servicio serv ON(es.servicio_id = serv.servicio_id)
                               WHERE es.servicio_id = $servicio_id_usuario AND es.estado = 'Activo' AND es.cod_empresa = $cod_empresa");

    $empleados_solicitante = $conn->query("SELECT DISTINCT e.empleado_id, CONCAT(e.nombre, ' ', e.apellido) AS nombre_completo
    FROM empleados_servicio es
    JOIN empleados e ON es.empleado_id = e.empleado_id
    WHERE es.servicio_id = $servicio_id_usuario AND es.estado = 'Activo' AND es.cod_empresa = $cod_empresa");
    
     $empleados_receptor = $conn->query("SELECT DISTINCT e.empleado_id, CONCAT(e.nombre, ' ', e.apellido) AS nombre_completo
    FROM empleados_servicio es
    JOIN empleados e ON es.empleado_id = e.empleado_id
    WHERE es.servicio_id = $servicio_id_usuario AND es.estado = 'Activo' AND es.cod_empresa = $cod_empresa");

     // Crear la consulta base
    $query = "SELECT * FROM intercambio_turnos WHERE ";

    if (isset($_GET['buscar'])) {
        // Filtrar por empleado solicitante
        if (!empty($_GET['empleado_id_solicitante'])) {
            $empleado_id_solicitante = $_GET['empleado_id_solicitante'];
            $query .= " cod_empresa = $cod_empresa AND servicio_id = $servicio_id_usuario AND empleado_id_solicitante = $empleado_id_solicitante";
        }

        // Filtrar por empleado receptor
        if (!empty($_GET['empleado_id_receptor'])) {
            $empleado_id_receptor = $_GET['empleado_id_receptor'];
            $query .= " cod_empresa = $cod_empresa AND servicio_id = $servicio_id_usuario AND empleado_id_receptor = $empleado_id_receptor";
        }

        // Filtrar por estado
        if (!empty($_GET['estado'])) {
            $estado = $_GET['estado'];
            $query .= " cod_empresa = $cod_empresa AND servicio_id = $servicio_id_usuario AND estado = '$estado'";
        }

        // Filtrar por fecha de solicitud
        if (!empty($_GET['fecha_solicitud'])) {
            $fecha_solicitud = $_GET['fecha_solicitud'];
            $query .= " cod_empresa = $cod_empresa AND servicio_id = $servicio_id_usuario AND fecha_solicitud BETWEEN '$fecha_solicitud_ini' AND '$fecha_solicitud_fin'";
        }

        // Filtrar por servicio
        if (!empty($_GET['servicio_id'])) {
            $servicio_id = $_GET['ser vicio_id'];
            $query .= " cod_empresa = $cod_empresa AND servicio_id = $servicio_id_usuario";
        }
    }

    // Ejecutar la consulta
    $resultado = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda Intercambios de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Consultar Intercambios de Turnos</h2>
        <form method="GET" class="mb-3" action="form_consulta_intercambios_def.php">
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="empleado_id_solicitante">Empleado Solicitante</label>
                    <select name="empleado_id_solicitante" id="empleado_id_solicitante" class="form-control" required>
                        <option value="">Seleccione un empleado</option>
                        <?php while ($row = $empleados_solicitante->fetch_assoc()): ?>
                            <option value="<?php echo $row['empleado_id']; ?>" <?php echo (isset($filtros['empleado_id']) && $filtros['empleado_id'] == $row['empleado_id']) ? 'selected' : ''; ?>>
                                <?php echo $row['nombre_completo']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-12">
                    <label for="empleado_id_receptor">Empleado Receptor</label>
                    <select name="empleado_id_receptor" id="empleado_id_receptor" class="form-control" required>
                        <option value="">Seleccione un empleado</option>
                        <?php while ($row = $empleados_receptor->fetch_assoc()): ?>
                            <option value="<?php echo $row['empleado_id']; ?>" <?php echo (isset($filtros['empleado_id']) && $filtros['empleado_id'] == $row['empleado_id']) ? 'selected' : ''; ?>>
                                <?php echo $row['nombre_completo']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">Seleccione Estado</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Aprobado">Aprobado</option>
                        <option value="Rechazado">Rechazado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Fecha de Solicitud Inicial</label>
                    <input type="date" name="fecha_solicitud_ini" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Fecha de Solicitud Final</label>
                    <input type="date" name="fecha_solicitud_fin" class="form-control">
                </div>
               
                <div class="form-group col-md-12">
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
             </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
                </div>
            </div>
        </form>

        <table id="tablaIntercambioTurnoDef" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Intercambio</th>
                    <th>Solicitante</th>
                    <th>Receptor</th>
                    <th>Fecha Turno</th>
                    <th>Estado</th>
                    <th>Turno Original</th>
                    <th>Turno Nuevo</th>
                    <th>Fecha Solicitud</th>
                    <th>Fecha Aprobacio&acute;n</th>
                    <th>Usuario Aprueba</th>
                    <th>Empresa</th>
                    <th>Servicio</th>
                    <th>Fecha Rechazo</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $fila['id_intercambio']; ?></td>
                        <td><?php echo $fila['empleado_id_solicitante']; ?></td>
                        <td><?php echo $fila['empleado_id_receptor']; ?></td>
                        <td><?php echo $fila['fecha_turno']; ?></td>
                        <td><?php echo $fila['estado']; ?></td>
                        <td><?php echo $fila['id_turno_original']; ?></td>
                        <td><?php echo $fila['id_turno_nuevo']; ?></td>
                        <td><?php echo $fila['fecha_solicitud']; ?></td>
                        <td><?php echo $fila['fecha_aprobacion']; ?></td>
                        <td><?php echo $fila['cod_usu_aprueba']; ?></td>
                        <td><?php echo $fila['cod_empresa']; ?></td>
                        <td><?php echo $fila['servicio_id']; ?></td>
                        <td><?php echo $fila['fecha_rechazo']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>


    <script>
        $(document).ready(function() {
            $('#tablaIntercambioTurnoDef').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Exportar a Excel',
                        title: 'Intercambio de Turnos Definitivos'
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

<?php
// Cerrar conexión
$conexion->close();
?>
