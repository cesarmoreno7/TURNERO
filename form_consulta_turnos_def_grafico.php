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
    SELECT dt.fecha, SUM(dt.horas_laborar_turno) AS total_horas
    FROM detalle_turno_def dt
    WHERE dt.cod_empresa = $cod_empresa
";

if ($fecha_inicio && $fecha_fin) {
    $picosTrabajoQuery .= " AND dt.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

$picosTrabajoQuery .= " GROUP BY dt.fecha ORDER BY dt.fecha";
$result_picosTrabajo = $conn->query($picosTrabajoQuery);

// Procesar resultados
$fechas = [];
$horas = [];
if ($result_picosTrabajo) {
    while ($row = $result_picosTrabajo->fetch_assoc()) {
        $fechas[] = $row['fecha'];
        $horas[] = $row['total_horas'];
    }
}

// Convertir a JSON para usar en JavaScript
$json_fechas = json_encode($fechas);
$json_horas = json_encode($horas);
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
        const horas = <?php echo $json_horas; ?>;

        const ctx = document.getElementById('picosTrabajoChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: fechas,
                datasets: [{
                    label: 'Horas Laboradas',
                    data: horas,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
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
