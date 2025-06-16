<?php
    include 'menu_ppal_maestros.php';
?>
<?php
    include("session.php");
    include 'db.php';
    $conn->set_charset("utf8"); //Para grantizar las tildes
    $cod_empresa = $_SESSION['cod_empresa'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Consulta de Turnos base</title>
    <!-- Incluir el CSS de Bootstrap -->
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Consulta de Turnos base</h2>
        <form action="" method="POST" class="form-inline mb-4">
            <label for="search" class="mr-2">Buscar:</label>
            <input type="text" name="search" id="search" class="form-control mr-2" placeholder="Nombre Turno">
            <button type="submit" name="submit" class="btn btn-primary">Consultar</button>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>C&oacute;digo Turno</th>
                    <th>Nombre Turno</th>
                    <th>C&oacute;digo Alterno</th>
                    <th>Tipo Turno</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Cod. Empresa</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_POST['submit'])) {
                    $search = $_POST['search'];

                    // Configurar la conexi¨®n para utilizar UTF-8
                    $conn->set_charset("utf8");

                    // Consulta SQL con LIKE para buscar por cod_turno o nombre_turno
                    $sql = "SELECT * FROM turnos WHERE nombre_turno LIKE '%$search%' and cod_empresa = $cod_empresa";
                    //echo $sql;
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['cod_turno'], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td>" . htmlspecialchars($row['nombre_turno'], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td>" . htmlspecialchars($row['codigo_alterno'], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td>" . htmlspecialchars($row['tipo_turno'], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td>" . htmlspecialchars($row['hora_inicio'], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td>" . htmlspecialchars($row['hora_fin'], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td>" . htmlspecialchars($row['cod_empresa'], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No se encontraron resultados</td></tr>";
                    }

                    $conn->close();
                }
                ?>
            </tbody>
        </table>
    </div>

    
</body>
</html>
