<?php
include 'menu_ppal_maestros.php';
include 'session.php';
include 'db.php';

$conn->set_charset("utf8");

$cod_empresa = $_SESSION['cod_empresa'];
$fecha_inicio = $_GET['fecha_inicio'] ?? null;
$fecha_fin = $_GET['fecha_fin'] ?? null;

// Consulta para obtener las horas laboradas por empleado
$query = "
    SELECT 
        e.empleado_id,
        CONCAT(e.nombre, ' ', e.apellido) AS nombre_empleado,
        SUM(dt.horas_laborar_turno) AS total_horas
    FROM 
        detalle_turno_def dt
    JOIN 
        empleados e ON dt.empleado_id = e.empleado_id
    WHERE 
        dt.cod_empresa = $cod_empresa";

if ($fecha_inicio && $fecha_fin) {
    $query .= " AND dt.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

$query .= " GROUP BY 
                e.empleado_id, e.nombre, e.apellido
            ORDER BY 
                total_horas DESC";

$result = $conn->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$labels = array_column($data, 'nombre_empleado');
$values = array_column($data, 'total_horas');

// Convertir los datos a JSON
$data_json = json_encode(['labels' => $labels, 'data' => $values]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga Laboral de Empleados</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Empleados con Mayor y Menor Carga Laboral</h2>

        <!-- Formulario para filtrar por fechas -->
        <form id="filterForm" class="row g-3" action="" method="get">
            <div class="col-md-4">
                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>">
            </div>
            <div class="col-md-4">
                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo $fecha_fin; ?>">
            </div>
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>
        <br>
        <center><h3>Desde: <?php echo $fecha_inicio ?> Hasta: <?php echo $fecha_fin ?></h3></center>

        <!-- Contenedor del gráfico -->
        <div class="mt-4">
            <canvas id="chartCargaLaboral"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Datos generados por PHP
            const dataJson = <?php echo $data_json; ?>;
            
            // Configurar el gráfico
            const ctx = document.getElementById('chartCargaLaboral').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dataJson.labels,
                    datasets: [{
                        label: 'Horas Laboradas',
                        data: dataJson.data,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true },
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
                            title: { display: true, text: 'Horas' }
                        },
                        x: {
                            title: { display: true, text: 'Empleados' }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
