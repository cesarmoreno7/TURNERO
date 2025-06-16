<?php
    if (isset($_GET['year']) && isset($_GET['month'])) {
        $year = intval($_GET['year']);
        $month = str_pad(intval($_GET['month']), 2, '0', STR_PAD_LEFT);
        $dias_habiles = 0;
        $numero_festivos = 0;

        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Conexión a la base de datos (ajusta los parámetros según tu configuración)
        $conn = new mysqli('localhost', 'prodsoft_turnero', 'turnero2024', 'prodsoft_turnero');

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Consulta para obtener los festivos del mes
        $sql = "SELECT fecha FROM festivos WHERE YEAR(fecha) = $year AND MONTH(fecha) = $month";
        $result = $conn->query($sql);

        $festivos = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $festivos[] = $row['fecha'];
            }
        }

        $conn->close();

        for ($day = 1; $day <= $days_in_month; $day++) {
            $date = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
            $weekday = date('N', strtotime($date));

            if ($weekday < 7 && !in_array($date, $festivos)) {
                $dias_habiles++;
            } elseif ($weekday == 7 || in_array($date, $festivos)) {
                $numero_festivos++;
            }
        }

        $horas_laborar = $dias_habiles * 8;

        echo json_encode([
            'dias_habiles' => $dias_habiles,
            'numero_festivos' => $numero_festivos,
            'horas_laborar' => $horas_laborar
        ]);
    }
?>

