<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Turnos para Empleados</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
<div class="container">
    <h2 class="mt-4">Gestión de Turnos para Empleados</h2>
    
    <form id="turnos-form" action="registrar_turnos_emp.php" method="post">
        
        <button type="submit" id="save-button" class="btn btn-primary mt-4">Guardar</button>
    
    
    <?php
        // Datos de prueba (estos deberían venir de $_GET)
        $servicio_id = 2;
        $centro_costo_id = 3;
        $grupo_id = 4;

        // Conexión a la base de datos
        include "db.php";
        $conn->set_charset("utf8"); //Para grantizar las tildes

        // Obtener empleados
        $sql = "SELECT DISTINCT e.empleado_id, e.nombre, e.apellido 
                FROM empleados e, empleados_servicio es
                WHERE es.servicio_id = $servicio_id AND es.centro_costo_id = $centro_costo_id AND es.grupo_id = $grupo_id and e.empleado_id = es.empleado_id";
        $result = $conn->query($sql);

        // Obtener días del mes
        $mes_actual = date('m');
        $anio_actual = date('Y');
        $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes_actual, $anio_actual);
        $dias_semana = ['D', 'L', 'M', 'X', 'J', 'V', 'S'];
        
        // Obtener festivos del mes actual
        $festivos_sql = "SELECT fecha FROM festivos WHERE (MONTH(fecha) = $mes_actual AND YEAR(fecha) = $anio_actual) OR (MONTH(fecha) = $mes_actual + 1 AND DAY(fecha) = 1 AND YEAR(fecha) = $anio_actual)";
        $festivos_result = $conn->query($festivos_sql);
        $festivos = [];
        while ($festivo = $festivos_result->fetch_assoc()) {
            $festivos[] = $festivo['fecha'];
        }

        echo '<table class="table table-bordered mt-4">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Cédula</th>';
        echo '<th>Nombres</th>';
        echo '<th>Horas Contratadas</th>';
        echo '<th>Horas a Laborar Mes</th>';
        
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
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                //echo '<td>' . $row['empleado_id'] . '</td>';
                echo '<td><input type="number" name="horas_contratadas[]" value="'.$row['empleado_id'].'" class="form-control" /></td>';
                echo '<td>' . $row['nombre'] . ' ' . $row['apellido'] . '</td>';
                echo '<td><input type="number" name="horas_contratadas[]" class="form-control" /></td>';
                echo '<td><input type="number" name="horas_a_laborar_mes[]" class="form-control horas-a-laborar" /></td>';
                
                // Campos para cada día del mes
                for ($i = 1; $i <= $dias_mes; $i++) {
                    echo '<td class="day-column"><select name="turno_dia[' . $row['empleado_id'] . '][' . $i . ']" class="form-control turno-select">';
                    echo '<option value=""></option>';
                    
                    // Opciones de turnos cargadas desde la base de datos
                    $turnos_sql = "SELECT DISTINCT id_turno, cod_turno FROM horas_turnos";
                    $turnos_result = $conn->query($turnos_sql);
                    while ($turno = $turnos_result->fetch_assoc()) {
                        echo '<option value="' . $turno['id_turno'] . '">' . $turno['cod_turno'] . '</option>';
                    }
                    echo '</select></td>';
                }
                
                // Campo para el primer día del siguiente mes
                echo '<td class="day-column"><select name="turno_dia[' . $row['empleado_id'] . '][next_month_1]" class="form-control turno-select">';
                echo '<option value=""></option>';
                
                // Opciones de turnos cargadas desde la base de datos
                $turnos_sql = "SELECT DISTINCT id_turno, cod_turno FROM horas_turnos";
                $turnos_result = $conn->query($turnos_sql);
                while ($turno = $turnos_result->fetch_assoc()) {
                    echo '<option value="' . $turno['id_turno'] . '">' . $turno['cod_turno'] . '</option>';
                }
                echo '</select></td>';

                echo '</tr>';
            }
        }

        echo '</tbody>';
        echo '</table>';
        
        $conn->close();
    ?>
    
    </form>
</div>
</body>

</html>
