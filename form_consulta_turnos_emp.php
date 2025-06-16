<?php
    include 'menu_ppal_emp.php';
?>

<?php

    include 'sesion.php';
    include 'db.php';
    $conn->set_charset("utf8"); //Para grantizar las tildes
    
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['buscar'])) {
    
        $cod_empresa = $_SESSION['cod_empresa'];
        
        // Validar si el usuario ha iniciado sesión correctamente
        if (!isset($_SESSION['codigo_usu'])) {
            echo "<script>alert('Debe iniciar sesión para acceder a esta página.'); window.location.href = 'index.php';</script>";
            exit;
        }
        
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
        
        // Variables para almacenar los filtros seleccionados
        $filtros = [];
        $sql_detalle = "SELECT dt.*, e.nombre, e.apellido 
                FROM detalle_turno_def dt
                JOIN empleados e ON dt.empleado_id = e.empleado_id
                WHERE dt.empleado_id = $empleado_id";
        
        $sql_horas = "SELECT * FROM horas_turnos_def htd WHERE htd.empleado_id = $empleado_id"; // Consulta para horas_turnos_def
        
        // Aplicar los filtros si están seleccionados
        if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin']) && $_GET['fecha_inicio'] != '' && $_GET['fecha_fin'] != '') {
            $fecha_inicio = $_GET['fecha_inicio'];
            $fecha_fin = $_GET['fecha_fin'];
            $sql_detalle .= " AND dt.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' order by dt.empleado_id";
            $sql_horas .= " AND htd.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' order by htd.empleado_id"; 
            $filtros['fecha_inicio'] = $fecha_inicio;
            $filtros['fecha_fin'] = $fecha_fin;
        }
        
        //echo $sql_detalle;
        
        // Ejecutar la consulta con los filtros aplicados
        $result_detalle = $conn->query($sql_detalle);
        $result_horas = $conn->query($sql_horas); // Ejecutar consulta para horas_turnos_def
        
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Turnos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Consulta de Turnos</h2>
    <form action="form_consulta_turnos_emp.php" method="GET" class="mb-4">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="fecha_inicio">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required value="<?php echo isset($filtros['fecha_inicio']) ? $filtros['fecha_inicio'] : ''; ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="fecha_fin">Fecha Fin</label>
                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required value="<?php echo isset($filtros['fecha_fin']) ? $filtros['fecha_fin'] : ''; ?>">
            </div>         
        </div>  
        <button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
        
        <!-- Añadir botón para exportar a CSV -->
        <form action="exportar_turnos_csv.php" method="POST">
            <input type="hidden" name="sql_detalle" value="<?php echo htmlspecialchars($sql_detalle); ?>">
            <input type="hidden" name="sql_horas" value="<?php echo htmlspecialchars($sql_horas); ?>">
            <!--<button type="submit" class="btn btn-success mb-3">Exportar a CSV</button> -->
        </form>
        
    </form>

    <!-- Mostrar resultados de detalle_turno_def -->
    <h3>Resultados Detalle Turnos</h3>
    <table class="table table-bordered">
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
    <h3>Resultados detalle Horas Turnos</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Empleado ID</th>
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
</div>
</body>
</html>