<?php
include 'menu_ppal_maestros.php';
include 'session.php';
include 'db.php';

$conn->set_charset("utf8");

// Variables de sesión
$cod_empresa = $_SESSION['cod_empresa'];
$codigo_usu = $_SESSION['codigo_usu'];

// Variables GET
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

// Consulta para datos del gráfico
$picosTrabajoQuery = "
    SELECT fecha,
        SUM(horas_diurnas_tot) AS horas_diurnas_tot,
        SUM(horas_nocturnas_tot) AS horas_nocturnas_tot,
        SUM(horas_diurnas_ordinario_ls) AS horas_diurnas_ordinario_ls,
        SUM(horas_nocturnas_ordinario_ls) AS horas_nocturnas_ordinario_ls,
        SUM(horas_festivas_diurnas_ls) AS horas_festivas_diurnas_ls,
        SUM(horas_festivas_nocturnas_ls) AS horas_festivas_nocturnas_ls,
        SUM(horas_diurnas_ordinarias_sd) AS horas_diurnas_ordinarias_sd,
        SUM(horas_nocturnas_ordinarias_sd) AS horas_nocturnas_ordinarias_sd,
        SUM(horas_festivas_diurnas_sd) AS horas_festivas_diurnas_sd,
        SUM(horas_festivas_nocturnas_sd) AS horas_festivas_nocturnas_sd,
        SUM(horas_diurnas_ordinarias_dlf) AS horas_diurnas_ordinarias_dlf,
        SUM(horas_nocturnas_ordinarias_dlf) AS horas_nocturnas_ordinarias_dlf,
        SUM(horas_festivas_diurnas_dlf) AS horas_festivas_diurnas_dlf,
        SUM(horas_festivas_nocturnas_dlf) AS horas_festivas_nocturnas_dlf,
        SUM(horas_diurnas_ordinarias_dlo_lfmo) AS horas_diurnas_ordinarias_dlo_lfmo,
        SUM(horas_nocturnas_ordinarias_dlo_lfmo) AS horas_nocturnas_ordinarias_dlo_lfmo,
        SUM(horas_festivas_diurnas_dlo_lfmo) AS horas_festivas_diurnas_dlo_lfmo,
        SUM(horas_festivas_nocturnas_dlo_lfmo) AS horas_festivas_nocturnas_dlo_lfmo,
        SUM(thed) AS thed,
        SUM(thenoc) AS thenoc
    FROM horas_turnos_def
    WHERE cod_empresa = $cod_empresa
";

if ($fecha_inicio && $fecha_fin) {
    $picosTrabajoQuery .= " AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

$picosTrabajoQuery .= " GROUP BY fecha ORDER BY fecha";
$result_picosTrabajo = $conn->query($picosTrabajoQuery);

// Procesar resultados
$fechas = [];
$dataSets = [
    'Total Horas Diurnas' => [],
    'Total Horas Nocturnas' => [],
    'Horas Diurnas Ordinarias (LS)' => [],
    'Horas Nocturnas Ordinarias (LS)' => [],
    'Horas Festivas Diurnas (LS)' => [],
    'Horas Festivas Nocturnas (LS)' => [],
    'Horas Diurnas Ordinarias (SD)' => [],
    'Horas Nocturnas Ordinarias (SD)' => [],
    'Horas Festivas Diurnas (SD)' => [],
    'Horas Festivas Nocturnas (SD)' => [],
    'Horas Diurnas Ordinarias (DLF)' => [],
    'Horas Nocturnas Ordinarias (DLF)' => [],
    'Horas Festivas Diurnas (DLF)' => [],
    'Horas Festivas Nocturnas (DLF)' => [],
    'Horas Diurnas Ordinarias (DLO LFMO)' => [],
    'Horas Nocturnas Ordinarias (DLO LFMO)' => [],
    'Horas Festivas Diurnas (DLO LFMO)' => [],
    'Horas Festivas Nocturnas (DLO LFMO)' => [],
    'Total Horas Extra Diurnas (THED)' => [],
    'Total Horas Extra Nocturnas (THENOC)' => []
];

if ($result_picosTrabajo) {
    while ($row = $result_picosTrabajo->fetch_assoc()) {
        $fechas[] = $row['fecha'];
        foreach ($dataSets as $key => &$values) {
            $fieldKey = strtolower(str_replace([' ', '(', ')'], ['_', '', ''], $key));
            $values[] = $row[$fieldKey];
        }
    }
}

// Convertir a JSON para usar en JavaScript
$json_fechas = json_encode($fechas);
$json_dataSets = json_encode($dataSets);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráfico de Picos de Trabajo</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div style="width: 80%; margin: 0 auto;">
        <h2>Gráfico de Picos de Trabajo</h2>
        <form method="GET" action="">
            <label for="fecha_inicio">Fecha Inicio:</label>
            <input type="date" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>">
            <label for="fecha_fin">Fecha Fin:</label>
            <input type="date" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>">
            <button type="submit">Filtrar</button>
        </form>
        <canvas id="picosTrabajoChart"></canvas>
    </div>

    <script>
        const fechas = <?php echo $json_fechas; ?>;
        const dataSets = <?php echo $json_dataSets; ?>;

        const ctx = document.getElementById('picosTrabajoChart').getContext('2d');
        const datasetsConfig = Object.keys(dataSets).map((key, index) => ({
            label: key,
            data: dataSets[key],
            borderColor: `hsl(${index * 15}, 70%, 50%)`,
            backgroundColor: `hsla(${index * 15}, 70%, 50%, 0.2)`,
            borderWidth: 2
        }));

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: fechas,
                datasets: datasetsConfig
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Fecha'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Horas Laboradas'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
