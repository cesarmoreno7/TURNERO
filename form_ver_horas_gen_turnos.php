<?php
    include("session.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Turnos</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Consultar Horas Generadas para los Turnos</h2>
        <form action="" method="GET">
            <div class="form-group">
                <label for="cod_turno">C&oacute;digo de Turno:</label>
                <input type="text" class="form-control" id="cod_turno" name="cod_turno">
            </div>
            <button type="submit" class="btn btn-primary">Consultar</button>
        </form>
        <div class="mt-4">
            <?php
            
                // Conexión a la base de datos
                include 'db.php';
                
                $cod_empresa = $_SESSION['cod_empresa'];
                
                // Obtener el número de registros en la tabla "turnos"
                $sql = "SELECT COUNT(*) AS total FROM turnos WHERE cod_empresa = '$cod_empresa'";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                $total_registros_turnos = $row['total'];

                $sql = "SELECT * FROM horas_turnos  WHERE cod_empresa = '$cod_empresa' LIMIT $total_registros_turnos";
                if (isset($_GET['cod_turno']) && !empty($_GET['cod_turno'])) {
                    $cod_turno = $_GET['cod_turno'];
                    $sql = "SELECT * FROM horas_turnos WHERE cod_turno = ? AND cod_empresa = '$cod_empresa' LIMIT $total_registros_turnos";
                }
    
                $stmt = $conn->prepare($sql);
                if (isset($cod_turno)) {
                    $stmt->bind_param("s", $cod_turno);
                }
                $stmt->execute();
                $result = $stmt->get_result();
    
                if ($result->num_rows > 0) {
                    echo "<table class='table table-bordered mt-4'>";
                    echo "<thead class='thead-dark'>";
                    echo "<tr>";
                    echo "<th>ID Turno</th>";
                    echo "<th>C&oacute;digo de Turno</th>";
                    echo "<th>Total Horas</th>";
                    echo "<th>Horas Diurnas Totales</th>";
                    echo "<th>Horas Nocturnas Totales</th>";
                    echo "<th>THED</th>";
                    echo "<th>THENOC</th>";
                    echo "<th>Cod. Empresa</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                    // Mostrar resultados en la tabla
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["id_turno"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["cod_turno"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["total_horas"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["horas_diurnas_tot"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["horas_nocturnas_tot"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["thed"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["thenoc"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["cod_empresa"]) . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<div class='alert alert-warning'>No se encontraron resultados.</div>";
                }
    
                $stmt->close();
                $conn->close();
                ?>
            </div>
        </div>
    
        <!-- Bootstrap JS and dependencies -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
</html>