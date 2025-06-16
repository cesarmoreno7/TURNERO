<?php
$conn = new mysqli('localhost', 'prodsoft_turnero', 'turnero2024', 'prodsoft_turnero');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empleado_ids = $_POST['empleado_id'];
    $horas_contratadas = $_POST['horas_contratadas'];
    $horas_a_laborar = $_POST['horas_a_laborar'];

    $detalle_turno_values = [];
    $horas_totales = [];

    foreach ($empleado_ids as $index => $empleado_id) {
        $horas_contratadas_value = $horas_contratadas[$index];
        $horas_a_laborar_value = $horas_a_laborar[$index];

        foreach ($_POST as $key => $value) {
            if (strpos($key, "turno_dia[$empleado_id]") !== false && !empty($value)) {
                preg_match('/\d+/', $key, $matches);
                $dia = $matches[0];
                $fecha = date('Y-m-d', strtotime(date('Y-m-') . $dia));
                $turno_id = $value;

                // Validar que no se registre dos veces el mismo horario para la misma fecha
                $duplicate_check_sql = "SELECT COUNT(*) as count FROM detalle_turno WHERE empleado_id = $empleado_id AND fecha = '$fecha' AND turno_id = $turno_id";
                $duplicate_check_result = $conn->query($duplicate_check_sql);
                $duplicate_count = $duplicate_check_result->fetch_assoc()['count'];

                if ($duplicate_count > 0) {
                    echo json_encode(['success' => false, 'message' => 'Se encontro un horario duplicado para el empleado ' . $empleado_id . ' en la fecha ' . $fecha]);
                    exit;
                }

                $turno_sql = "SELECT ht.horas_diurnas_tot, ht.horas_nocturnas_tot, t.tipo_turno, ht.thed, ht.thenoc
                              FROM horas_turnos ht inner join turnos t on(ht.cod_turno = t.cod_turno) WHERE ht.id_turno = $turno_id";
                $turno_result = $conn->query($turno_sql);
                $turno_data = $turno_result->fetch_assoc();

                $hd = $turno_data['horas_diurnas_tot'];
                $hn = $turno_data['horas_nocturnas_tot'];
                $hed = $turno_data['thed'];
                $hen = $turno_data['thenoc'];

                if ($turno_data['tipo_turno'] == -1) {
                    $horas_a_laborar_value -= 8;
                }

                $detalle_turno_values[] = "($empleado_id, '$fecha', $turno_id, $servicio_id, $centro_costo_id, $grupo_id, $hd, $hn, $hed, $hen, $horas_contratadas_value, $horas_a_laborar_value)";

                if (!isset($horas_totales[$empleado_id])) {
                    $horas_totales[$empleado_id] = ['hd' => 0, 'hn' => 0, 'hed' => 0, 'hen' => 0];
                }

                $horas_totales[$empleado_id]['hd'] += $hd;
                $horas_totales[$empleado_id]['hn'] += $hn;
                $horas_totales[$empleado_id]['hed'] += $hed;
                $horas_totales[$empleado_id]['hen'] += $hen;
            }
        }
    }

    if (!empty($detalle_turno_values)) {
        $detalle_turno_sql = "INSERT INTO detalle_turno (empleado_id, fecha, turno_id, servicio_id, centro_costo_id, grupo_id, hd, hn, hed, hen, horas_contratadas, horas_a_laborar_mes) VALUES " . implode(',', $detalle_turno_values);
        if ($conn->query($detalle_turno_sql) === TRUE) {
            foreach ($horas_totales as $empleado_id => $totals) {
                $update_sql = "UPDATE detalle_turno SET hd = {$totals['hd']}, hn = {$totals['hn']}, hed = {$totals['hed']}, hen = {$totals['hen']} WHERE empleado_id = $empleado_id";
                $conn->query($update_sql);
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al insertar datos: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No hay datos para insertar']);
    }
}

$conn->close();
?>
