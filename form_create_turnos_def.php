<?php
    include 'menu_ppal_maestros.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Mes para Procesar</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th, .table td {
            text-align: center;
            font-size: 20px; /* Ajusta este valor según tus necesidades */
        }
        .day-column {
            width: 150px; /* Ajusta este valor según tus necesidades */
        }
        .zeros {
            color: white;
        }
        .day-header {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Grabar Turnos Definitivos</h2>
        <form action="form_create_turnos_def.php" method="post"> <!-- Ajusta el action al archivo PHP de procesamiento -->
            <div class="form-group">
                <label for="mes">Seleccione el mes:</label>
                <input type="month" name="mes" id="mes" class="form-control" required>
            </div>
            <button type="submit" name="procesar" class="btn btn-primary">Procesar</button>
            <button type="submit" name="ver_detalle" class="btn btn-primary">Ver detalle</button>
        </form>
    </div>
</body>
</html>

<?php

include 'db.php';


// Obtener el año y el mes
$anio = date("Y", strtotime($mes . "-01"));
$mes_num = date("m", strtotime($mes . "-01"));

// Calcular el número de días en el mes seleccionado
$dias_en_el_mes = cal_days_in_month(CAL_GREGORIAN, $mes_num, $anio);

// Contador de días laborales
$dias_laborales = 0;

// Consulta para obtener los festivos del mes
$sql = "SELECT fecha FROM festivos WHERE YEAR(fecha) = $anio AND MONTH(fecha) = $mes_num";
$result = $conn->query($sql);

$festivos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $festivos[] = $row['fecha'];
    }
}

// Iterar sobre todos los días del mes
for ($dia = 1; $dia <= $dias_en_el_mes; $dia++) {
    $date = "$anio-$mes_num-" . str_pad($dia, 2, '0', STR_PAD_LEFT);
    $weekday = date('N', strtotime($date));

    if ($weekday < 7 && !in_array($date, $festivos)) {
        $dias_habiles++;
    } 
}
// Calcular el total de horas
$horas_laborales_totales = $dias_habiles * 8;


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['procesar'])) {

    // Capturar el mes especificado por el usuario
    $mes = isset($_POST['mes']) ? $_POST['mes'] : null;
    
   /* if (!$mes) {
        echo "<script>alert('Por favor, especifique un mes para procesar.'); window.location.href = 'form_create_turnos_def.php';</script>";
        exit;
    }*/
    
    // Validar que el mes esté en el formato correcto (YYYY-MM)
    if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mes)) {
        echo "<script>alert('El formato del mes no es correcto. Use el formato YYYY-MM.'); window.location.href = 'form_create_turnos_def.php';</script>";
        exit;
    }
    
    // Verificar si las fechas del mes especificado ya están registradas en "detalle_turno_def"
    $validarDetalleMesQuery = "SELECT * FROM detalle_turno_def WHERE DATE_FORMAT(fecha, '%Y-%m') = '$mes'";
    $validarDetalleMesResult = $conn->query($validarDetalleMesQuery);
    
    if ($validarDetalleMesResult->num_rows > 0) {
        
        // Si existen registros, comparar los datos y actualizar si hay diferencias
        $detalleTurnoQuery = "SELECT * FROM detalle_turno WHERE fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)";
        $detalleTurnoResult = $conn->query($detalleTurnoQuery);
    
        if ($detalleTurnoResult->num_rows > 0) {
            while ($detalleRow = $detalleTurnoResult->fetch_assoc()) {
                $empleado_id = $detalleRow['empleado_id'];
                $fecha = $detalleRow['fecha'];
                $turno_id = $detalleRow['turno_id'];
    
                // Verificar si ya existe un registro con el mismo empleado_id, fecha y turno_id en detalle_turno_def
                $detalleDefQuery = "SELECT * FROM detalle_turno_def WHERE empleado_id = '$empleado_id' AND fecha = '$fecha' AND turno_id = '$turno_id'";
                $detalleDefResult = $conn->query($detalleDefQuery);
                $detalleDefRow = $detalleDefResult->fetch_assoc();
                
                if ($detalleDefResult->num_rows == 0) {
                    // Insertar en la tabla "detalle_turno_def"
                    $insertDetalleQuery = "INSERT INTO detalle_turno_def (encabezado_id, empleado_id, fecha, turno_id, servicio_id, centro_costo_id, grupo_id, hd, hn, hed, hen, horas_contratadas, horas_laborar_turno, codigo_usu, cod_empresa, estado, observaciones, fecha_reg, fecha_act)
                    VALUES ('{$detalleRow['encabezado_id']}', '{$detalleRow['empleado_id']}', '{$detalleRow['fecha']}', '{$detalleRow['turno_id']}', '{$detalleRow['servicio_id']}', '{$detalleRow['centro_costo_id']}', '{$detalleRow['grupo_id']}', '{$detalleRow['hd']}', '{$detalleRow['hn']}', '{$detalleRow['hed']}', '{$detalleRow['hen']}', '{$detalleRow['horas_contratadas']}', '{$detalleRow['horas_laborar_turno']}', '{$detalleRow['codigo_usu']}', '{$detalleRow['cod_empresa']}', 'Activo', '', NOW(), '')";
        
                    if (!$conn->query($insertDetalleQuery)) {
                        echo "<script>alert('Error al insertar en detalle_turno_def: " . $conn->error . "'); window.location.href = 'form_create_turnos_def.php';</script>";
                        exit;
                    }
                }
                
                /*else{
                
                    // Comparar los datos y actualizar si hay diferencias
                    if ($detalleDefRow && (
                        $detalleRow['turno_id'] != $detalleDefRow['turno_id'] 
                    )) {
                        // Actualizar detalle_turno_def
                        $updateDetalleDefQuery = "UPDATE detalle_turno_def SET 
                            hd = '{$detalleRow['hd']}', 
                            hn = '{$detalleRow['hn']}', 
                            hed = '{$detalleRow['hed']}', 
                            hen = '{$detalleRow['hen']}', 
                            horas_contratadas = '{$detalleRow['horas_contratadas']}', 
                            horas_laborar_turno = '{$detalleRow['horas_laborar_turno']}', 
                            turno_id = '{$detalleRow['turno_id']}',
                            fecha_act = NOW()
                            WHERE empleado_id = '$empleado_id' AND fecha = '$fecha' AND turno_id = '$turno_id'";
        
                        if (!$conn->query($updateDetalleDefQuery)) {
                            echo "<script>alert('Error al actualizar detalle_turno_def: " . $conn->error . "'); window.location.href = 'form_create_turnos_def.php';</script>";
                            exit;
                        }
                    }
                }*/
            }
        }
    } else {
        // Si no hay registros, insertar nuevos
        $detalleTurnoQuery = "SELECT * FROM detalle_turno WHERE fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)";
        $detalleTurnoResult = $conn->query($detalleTurnoQuery);
    
        if ($detalleTurnoResult->num_rows > 0) {
            while ($detalleRow = $detalleTurnoResult->fetch_assoc()) {
                // Insertar en la tabla "detalle_turno_def"
                $insertDetalleQuery = "INSERT INTO detalle_turno_def (encabezado_id, empleado_id, fecha, turno_id, servicio_id, centro_costo_id, grupo_id, hd, hn, hed, hen, horas_contratadas, horas_laborar_turno, codigo_usu, cod_empresa, estado, observaciones, fecha_reg, fecha_act)
                VALUES ('{$detalleRow['encabezado_id']}', '{$detalleRow['empleado_id']}', '{$detalleRow['fecha']}', '{$detalleRow['turno_id']}', '{$detalleRow['servicio_id']}', '{$detalleRow['centro_costo_id']}', '{$detalleRow['grupo_id']}', '{$detalleRow['hd']}', '{$detalleRow['hn']}', '{$detalleRow['hed']}', '{$detalleRow['hen']}', '{$detalleRow['horas_contratadas']}', '{$detalleRow['horas_laborar_turno']}', '{$detalleRow['codigo_usu']}', '{$detalleRow['cod_empresa']}', 'Activo', '', NOW(), '')";
    
                if (!$conn->query($insertDetalleQuery)) {
                    echo "<script>alert('Error al insertar en detalle_turno_def: " . $conn->error . "'); window.location.href = 'form_create_turnos_def.php';</script>";
                    exit;
                }
            }
        }
    }
    
    // Verificar si las fechas del mes especificado ya están registradas en "horas_turnos_def"
    $validarHorasMesQuery = "SELECT * FROM horas_turnos_def WHERE DATE_FORMAT(fecha, '%Y-%m') = '$mes'";
    $validarHorasMesResult = $conn->query($validarHorasMesQuery);
    
    if ($validarHorasMesResult->num_rows > 0) {
        // Si existen registros, comparar los datos y actualizar si hay diferencias
        $horasTurnosQuery = "SELECT * FROM horas_turnos WHERE fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)";
        $horasTurnosResult = $conn->query($horasTurnosQuery);
    
        if ($horasTurnosResult->num_rows > 0) {
            while ($horasRow = $horasTurnosResult->fetch_assoc()) {
                $cod_turno = $horasRow['cod_turno'];
                $fecha = $horasRow['fecha'];
                $empleado_id = $horasRow['empleado_id'];
    
                // Buscar el registro correspondiente en horas_turnos_def
                $horasDefQuery = "SELECT * FROM horas_turnos_def WHERE cod_turno = '$cod_turno' AND fecha = '$fecha' AND empleado_id = '$empleado_id'";
                $horasDefResult = $conn->query($horasDefQuery);
                $horasDefRow = $horasDefResult->fetch_assoc();
                
                if ($horasDefResult->num_rows == 0) {
                    // Insertar en la tabla "horas_turnos_def"
                    $insertHorasQuery = "INSERT INTO horas_turnos_def (cod_turno, fecha, total_horas, horas_diurnas_tot, horas_nocturnas_tot, horas_diurnas_ordinario_ls, horas_nocturnas_ordinario_ls, horas_festivas_diurnas_ls, horas_festivas_nocturnas_ls, horas_diurnas_ordinarias_sd, horas_nocturnas_ordinarias_sd, horas_festivas_diurnas_sd, horas_festivas_nocturnas_sd, horas_diurnas_ordinarias_dlf, horas_nocturnas_ordinarias_dlf, horas_festivas_diurnas_dlf, horas_festivas_nocturnas_dlf, horas_diurnas_ordinarias_dlo_lfmo, horas_nocturnas_ordinarias_dlo_lfmo, horas_festivas_diurnas_dlo_lfmo, horas_festivas_nocturnas_dlo_lfmo, thed, thenoc, cod_empresa, empleado_id, estado, observaciones, fecha_reg, fecha_act)
                    VALUES ('{$horasRow['cod_turno']}', '{$horasRow['fecha']}', '{$horasRow['total_horas']}', '{$horasRow['horas_diurnas_tot']}', '{$horasRow['horas_nocturnas_tot']}', '{$horasRow['horas_diurnas_ordinario_ls']}', '{$horasRow['horas_nocturnas_ordinario_ls']}', '{$horasRow['horas_festivas_diurnas_ls']}', '{$horasRow['horas_festivas_nocturnas_ls']}', '{$horasRow['horas_diurnas_ordinarias_sd']}', '{$horasRow['horas_nocturnas_ordinarias_sd']}', '{$horasRow['horas_festivas_diurnas_sd']}', '{$horasRow['horas_festivas_nocturnas_sd']}', '{$horasRow['horas_diurnas_ordinarias_dlf']}', '{$horasRow['horas_nocturnas_ordinarias_dlf']}', '{$horasRow['horas_festivas_diurnas_dlf']}', '{$horasRow['horas_festivas_nocturnas_dlf']}', '{$horasRow['horas_diurnas_ordinarias_dlo_lfmo']}', '{$horasRow['horas_nocturnas_ordinarias_dlo_lfmo']}', '{$horasRow['horas_festivas_diurnas_dlo_lfmo']}', '{$horasRow['horas_festivas_nocturnas_dlo_lfmo']}', '{$horasRow['thed']}', '{$horasRow['thenoc']}', '{$horasRow['cod_empresa']}', '{$horasRow['empleado_id']}', 'Activo', '', NOW(), '')";
        
                    if (!$conn->query($insertHorasQuery)) {
                        echo "<script>alert('Error al insertar en horas_turnos_def: " . $conn->error . "'); window.location.href = 'form_create_turnos_def.php';</script>";
                        exit;
                    }
                }
                
                /*else{
    
                    // Comparar los datos y actualizar si hay diferencias
                    if ($horasDefRow && (
                        $horasRow['cod_turno'] != $horasDefRow['cod_turno'] 
                    )) {
                        // Actualizar horas_turnos_def
                        $updateHorasDefQuery = "UPDATE horas_turnos_def SET 
                            total_horas = '{$horasRow['total_horas']}', 
                            horas_diurnas_tot = '{$horasRow['horas_diurnas_tot']}', 
                            horas_nocturnas_tot = '{$horasRow['horas_nocturnas_tot']}', 
                            horas_diurnas_ordinario_ls = '{$horasRow['horas_diurnas_ordinario_ls']}', 
                            horas_nocturnas_ordinario_ls = '{$horasRow['horas_nocturnas_ordinario_ls']}', 
                            horas_festivas_diurnas_ls = '{$horasRow['horas_festivas_diurnas_ls']}', 
                            horas_festivas_nocturnas_ls = '{$horasRow['horas_festivas_nocturnas_ls']}',
                            horas_diurnas_ordinarias_sd = '{$horasRow['horas_diurnas_ordinarias_sd']}',
                            horas_nocturnas_ordinarias_sd = '{$horasRow['horas_nocturnas_ordinarias_sd']}',
                            horas_festivas_diurnas_sd = '{$horasRow['horas_festivas_diurnas_sd']}',
                            horas_festivas_nocturnas_sd = '{$horasRow['horas_festivas_nocturnas_sd']}',
                            horas_diurnas_ordinarias_dlf = '{$horasRow['horas_diurnas_ordinarias_dlf']}',
                            horas_nocturnas_ordinarias_dlf = '{$horasRow['horas_nocturnas_ordinarias_dlf']}',
                            horas_festivas_diurnas_dlf = '{$horasRow['horas_festivas_diurnas_dlf']}',
                            horas_festivas_nocturnas_dlf = '{$horasRow['horas_festivas_nocturnas_dlf']}',
                            horas_diurnas_ordinarias_dlo_lfmo = '{$horasRow['horas_diurnas_ordinarias_dlo_lfmo']}',
                            horas_nocturnas_ordinarias_dlo_lfmo = '{$horasRow['horas_nocturnas_ordinarias_dlo_lfmo']}',
                            horas_festivas_diurnas_dlo_lfmo = '{$horasRow['horas_festivas_diurnas_dlo_lfmo']}',
                            horas_festivas_nocturnas_dlo_lfmo = '{$horasRow['horas_festivas_nocturnas_dlo_lfmo']}',
                            thed = '{$horasRow['thed']}',
                            thenoc = '{$horasRow['thenoc']}',
                            cod_turno = '{$horasRow['cod_turno']}',
                            fecha_act = NOW()
                            WHERE cod_turno = '$cod_turno' AND fecha = '$fecha' AND empleado_id = '$empleado_id'";
        
                        if (!$conn->query($updateHorasDefQuery)) {
                            echo "<script>alert('Error al actualizar horas_turnos_def: " . $conn->error . "'); window.location.href = 'form_create_turnos_def.php';</script>";
                            exit;
                        }
                    }
                }*/
            }
        }
    } else {
        // Si no hay registros, insertar nuevos
        $horasTurnosQuery = "SELECT * FROM horas_turnos WHERE fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)";
        $horasTurnosResult = $conn->query($horasTurnosQuery);
    
        if ($horasTurnosResult->num_rows > 0) {
            while ($horasRow = $horasTurnosResult->fetch_assoc()) {
                // Insertar en la tabla "horas_turnos_def"
                $insertHorasQuery = "INSERT INTO horas_turnos_def (cod_turno, fecha, total_horas, horas_diurnas_tot, horas_nocturnas_tot, horas_diurnas_ordinario_ls, horas_nocturnas_ordinario_ls, horas_festivas_diurnas_ls, horas_festivas_nocturnas_ls, horas_diurnas_ordinarias_sd, horas_nocturnas_ordinarias_sd, horas_festivas_diurnas_sd, horas_festivas_nocturnas_sd, horas_diurnas_ordinarias_dlf, horas_nocturnas_ordinarias_dlf, horas_festivas_diurnas_dlf, horas_festivas_nocturnas_dlf, horas_diurnas_ordinarias_dlo_lfmo, horas_nocturnas_ordinarias_dlo_lfmo, horas_festivas_diurnas_dlo_lfmo, horas_festivas_nocturnas_dlo_lfmo, thed, thenoc, cod_empresa, empleado_id, estado, observaciones, fecha_reg, fecha_act)
                VALUES ('{$horasRow['cod_turno']}', '{$horasRow['fecha']}', '{$horasRow['total_horas']}', '{$horasRow['horas_diurnas_tot']}', '{$horasRow['horas_nocturnas_tot']}', '{$horasRow['horas_diurnas_ordinario_ls']}', '{$horasRow['horas_nocturnas_ordinario_ls']}', '{$horasRow['horas_festivas_diurnas_ls']}', '{$horasRow['horas_festivas_nocturnas_ls']}', '{$horasRow['horas_diurnas_ordinarias_sd']}', '{$horasRow['horas_nocturnas_ordinarias_sd']}', '{$horasRow['horas_festivas_diurnas_sd']}', '{$horasRow['horas_festivas_nocturnas_sd']}', '{$horasRow['horas_diurnas_ordinarias_dlf']}', '{$horasRow['horas_nocturnas_ordinarias_dlf']}', '{$horasRow['horas_festivas_diurnas_dlf']}', '{$horasRow['horas_festivas_nocturnas_dlf']}', '{$horasRow['horas_diurnas_ordinarias_dlo_lfmo']}', '{$horasRow['horas_nocturnas_ordinarias_dlo_lfmo']}', '{$horasRow['horas_festivas_diurnas_dlo_lfmo']}', '{$horasRow['horas_festivas_nocturnas_dlo_lfmo']}', '{$horasRow['thed']}', '{$horasRow['thenoc']}', '{$horasRow['cod_empresa']}', '{$horasRow['empleado_id']}', 'Activo', '', NOW(), '')";
    
                if (!$conn->query($insertHorasQuery)) {
                    echo "<script>alert('Error al insertar en horas_turnos_def: " . $conn->error . "'); window.location.href = 'form_create_turnos_def.php';</script>";
                    exit;
                }
            }
        }
    }
    
    echo "<script>alert('El proceso ha terminado correctamente.'); window.location.href = 'form_create_turnos_def.php';</script>";
    
    $conn->close();
   
}
?>


 <div class="container">
    
        <?php
        
            include("session.php");
            
            include 'db.php';

            $cod_empresa = $_SESSION['cod_empresa'];
            $codigo_usu  = $_SESSION['codigo_usu'];

            // Verificar si se ha enviado el formulario al presionar el botón: "procesar"
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ver_detalle'])) {
                
                
                // Capturar el mes especificado por el usuario
                 $mes = isset($_POST['mes']) ? $_POST['mes'] : null;

                 $turnos_sql = "SELECT DISTINCT
                 MONTH(dtd.fecha) AS mes, 
                 YEAR(dtd.fecha) AS annio, 
                 dtd.servicio_id, 
                 dtd.centro_costo_id, 
                 dtd.grupo_id, 
                 dtd.codigo_usu, 
                 dtd.cod_empresa,
                 dtd.horas_contratadas,
                 dtd.estado
               FROM detalle_turno_def dtd
               WHERE dtd.cod_empresa = $cod_empresa 
                AND dtd.codigo_usu = '$codigo_usu'
               AND DATE_FORMAT(dtd.fecha, '%Y-%m') = '$mes'
               ORDER BY dtd.fecha"; 
                
                // Ejecutar la consulta
                $result = $conn->query($turnos_sql);

                if ($result->num_rows > 0) {
                    // Iterar sobre los resultados para capturar los datos
                    while ($row = $result->fetch_assoc()) {
                        //$mes = $row['mes']; // Capturar el mes
                        $ano = $row['annio']; // Capturar el año
                        $servicio_id = $row['servicio_id']; // Capturar servicio_id
                        $centro_costo_id = $row['centro_costo_id']; // Capturar centro_costo_id
                        $grupo_id = $row['grupo_id']; // Capturar grupo_id
                        $codigo_usu = $row['codigo_usu']; // Capturar codigo_usu
                        $cod_empresa = $row['cod_empresa']; // Capturar cod_empresa
                        $horas_contratadas = $row['horas_contratadas'];
                        $estado = $row['estado'];
                    }
                    //$mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
                }
                
                
                //$horas_laborar = OJO PENDIENTE CAPTURAR
                $horas_contratadas = 240;
                
                // Obtener días del mes
                $mes_actual = substr($mes, 5, 2);
                //echo $mes_actual;
                $anio_actual = $ano;
                $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes_actual, $anio_actual);
                $dias_semana = ['D', 'L', 'M', 'X', 'J', 'V', 'S'];
                
                // Obtener festivos del mes actual
                $festivos_sql = "SELECT fecha FROM festivos WHERE (MONTH(fecha) = $mes_actual AND YEAR(fecha) = $anio_actual) OR (MONTH(fecha) = $mes_actual + 1 AND DAY(fecha) = 1 AND YEAR(fecha) = $anio_actual)";
                //echo  $festivos_sql;
                $festivos_result = $conn->query($festivos_sql);
                $festivos = [];
                if ($festivos_result && $festivos_result->num_rows > 0) {
                    while ($festivo = $festivos_result->fetch_assoc()) {
                        $festivos[] = $festivo['fecha'];
                    }
                }
                
                echo '<br>';
                echo 'Datos registrados para el filtro: '. $mes;
                echo '<form id="detalleForm" action="" method="post">';
                echo '<table class="table table-bordered mt-4">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Cédula</th>';
                echo '<th>Nombres</th>';
                echo '<th>Horas Contratadas</th>';
                echo '<th>Horas a Laborar Mes</th>';
                echo '<th>Estado</th>';
               
                // Encabezados de los días del mes
                for ($i = 1; $i <= $dias_mes; $i++) {
                    $fecha_actual = "$anio_actual-$mes_actual-" . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime($fecha_actual);
                    $dia_semana = date('w', $timestamp);
                    $es_festivo = in_array($fecha_actual, $festivos);
                    
                    $label = $es_festivo || $dias_semana[$dia_semana] == 'D' ? 'F' : '';
                    $day_display = "<span class='zeros'>000000000</span>$i";
                    echo "<th class='day-column'><div class='day-header'>$day_display<br>" . $dias_semana[$dia_semana] . $label . "</div></th>";
                }
                
                // Agregar el primer día del siguiente mes
                $mes_siguiente = $mes_actual + 1;
                if ($mes_siguiente > 12) {
                    $mes_siguiente = 1;
                    $anio_actual++;
                }
                $fecha_siguiente = sprintf("%04d-%02d-%02d", $anio_actual, $mes_siguiente, 1);
                $timestamp = strtotime($fecha_siguiente);
                $dia_semana = date('w', $timestamp);
                $es_festivo = in_array($fecha_siguiente, $festivos);
        
                $label = $es_festivo || $dias_semana[$dia_semana] == 'D' ? 'F' : '';
                $day_display = "<span class='zeros'>000000000</span>1";
                echo "<th class='day-column'><div class='day-header'>$day_display<br>" . $dias_semana[$dia_semana] . $label . "</div></th>";
                
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
        
                // Obtener los turnos asignados de la tabla "detalle_turno"
                $turnos_asignados = [];
                
                 // Obtener empleados
                $sql = "SELECT DISTINCT e.empleado_id, e.nombre, e.apellido 
                        FROM empleados e, empleados_servicio es
                        WHERE es.servicio_id = $servicio_id AND es.centro_costo_id = $centro_costo_id AND es.grupo_id = $grupo_id 
                        AND e.empleado_id = es.empleado_id AND e.cod_empresa = $cod_empresa AND es.estado = 'Activo'  
                        AND e.empleado_id != $codigo_usu";
                        
                //echo $sql;
                
                $result_emp = $conn->query($sql);
                
                // Iterar sobre cada empleado y cada día del mes para mostrar los campos de selección de turno
                if ($result_emp->num_rows > 0) {
                    while ($row = $result_emp->fetch_assoc()) {
                        
                        $empleado_id = $row['empleado_id'];
                        
                        // Sumar hd, hn, hed, hen de la tabla detalle_turno
                        $sum_sql = "SELECT COUNT(*) AS total_inc 
                                    FROM detalle_turno_def
                                    WHERE empleado_id = $empleado_id 
                                    AND YEAR(fecha) = '$ano' 
                                    AND MONTH(fecha) = '$mes' 
                                    AND cod_empresa = $cod_empresa
                                    AND turno_id = 'I'";
                        //echo $sum_sql;
                        $sum_result = $conn->query($sum_sql);
                        $sum_data = $sum_result->fetch_assoc();
                        $total_inc = $sum_data['total_inc'];
                        
                        $horas_laborar = $horas_laborar - ($total_inc*8);
                        
                        echo '<tr>';
                        echo '<td><input type="number" name="empleado_id[]" value="'.$row['empleado_id'].'" class="form-control" /></td>';
                        echo '<td>' . $row['nombre'] . ' ' . $row['apellido'] . '</td>';
                        echo '<td><input type="number" name="horas_contratadas[]" value="'.$horas_contratadas.'" class="form-control" /></td>';
                        echo '<td><input type="number" name="horas_a_laborar_mes[]" value="'.$horas_laborales_totales.'" class="form-control horas-a-laborar" /></td>';
                        echo '<td><input type="text" name="estado[]" value="'.$estado.'" class="form-control estado" /></td>';
                        
                        $turnos_sql = "SELECT dtd.empleado_id, dtd.fecha, dtd.turno_id
                        FROM detalle_turno_def dtd 
                        WHERE dtd.servicio_id = $servicio_id AND dtd.centro_costo_id = $centro_costo_id AND dtd.grupo_id = $grupo_id and dtd.empleado_id = $empleado_id 
                        AND  dtd.codigo_usu = '$codigo_usu' AND dtd.cod_empresa = $cod_empresa ORDER BY dtd.fecha";

                        //echo $turnos_sql;                        
                        $turnos_result = $conn->query($turnos_sql);
                        while ($turno = $turnos_result->fetch_assoc()) {
                            $codigo_turno = $turno['turno_id'];
                            echo '<td class="day-column">' . $codigo_turno . '</td>'; 
                        }
                        
                        echo '</tr>';
                    }
                }
                
                echo '</tbody>';
                echo '</table>';
                echo '</form>';
        
        
                 //echo "<script>alert('El proceso ha terminado correctamente.'); window.location.href = 'form_create_turnos_def.php';</script>";
    
                $conn->close();
            }
           
            
        ?>
    </div>