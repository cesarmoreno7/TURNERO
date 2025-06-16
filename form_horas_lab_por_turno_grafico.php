<?php
include 'menu_ppal_maestros.php';
include 'session.php';
include 'db.php';

$conn->set_charset("utf8");

$cod_empresa = $_SESSION['cod_empresa'];
$fecha_inicio = $_GET['fecha_inicio'] ?? null;
$fecha_fin = $_GET['fecha_fin'] ?? null;

// Consulta para obtener las horas laboradas por turno con filtro de fechas
$query = "
    SELECT CONCAT(t.nombre_turno, ' : ', t.hora_inicio, ' - ', t.hora_fin) AS rango_horas, SUM(dt.horas_laborar_turno) AS total_horas
    FROM detalle_turno_def dt
    JOIN turnos t ON dt.turno_id = t.cod_turno
    WHERE dt.cod_empresa = $cod_empresa";

if ($fecha_inicio && $fecha_fin) {
    $query .= " AND dt.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

$query .= " GROUP BY t.cod_turno, t.nombre_turno ORDER BY total_horas DESC";

//echo $query;

$result = $conn->query($query);


// Transformar los datos en un formato adecuado para Chart.js
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Convertir los datos a JSON para usarlos en JavaScript
//echo json_encode($data);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horas Laboradas por Turno</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Horas Laboradas por Turno</h2>

        <!-- Formulario para filtrar por fechas -->
        <form id="filterForm" class="row g-3">
            <div class="col-md-4">
                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
            </div>
            <div class="col-md-4">
                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
            </div>
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>
        <br>
        <center><h3>Desde: <?php echo $fecha_inicio ?> Hasta: <?php echo $fecha_fin ?></h3></center>

        <!-- Contenedor del gráfico -->
        <div class="mt-4">
            <canvas id="chartHorasPorTurno"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const chartCanvas = document.getElementById('chartHorasPorTurno').getContext('2d');

            // Datos generados en PHP
            const chartData = <?= json_encode($data) ?>;

            // Transformar los datos para Chart.js
            const labels = chartData.map(item => item.rango_horas);
            const values = chartData.map(item => parseFloat(item.total_horas));

            // Crear el gráfico
            new Chart(chartCanvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Horas Laboradas',
                        data: values,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.raw + " horas";
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Horas'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Turnos'
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
