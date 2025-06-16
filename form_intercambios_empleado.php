<?php
    include 'menu_ppal_emp.php';
?>
<?php

    include("session.php");
    include 'db.php';
    
    $empleado_id = $_SESSION['empleado_id'];
    //$PTS_valor_INT = obtenerValorPorLlave('INT');
    //$PTS_estado_INT = obtenerEstadoPorLlave('INT');

?>

<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnos Empleados</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Historial Solicitudes de Intercambio de turnos</h2>
        <br>
        <?php
            //if ($PTS_valor_INT === 'S' && $PTS_estado_INT == 1) {
            //}
            echo '<a href="form_solicitar_intercambio_turno.php" class="btn btn-success ml-2">Solicitar Intercambio</a>';
        ?>
        <br><br>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Id. Solicitud</th>
                    <th>Id. Solicitante</th>
                    <th>Nombres y Apellidos</th>
                    <th>Id. Receptor</th>
                    <th>Nombres y Apellidos</th>
                    <th>Fecha del Turno</th>
                    <th>Turno Original</th>
                    <th>Turno Nuevo</th>
                    <th>Fecha Solicitud</th>
                    <th>Fecha Aprobaci&oacute;n</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                    //if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['buscar'])) {

                        // Obtener solicitudes de intercambio pendientes
                        $sql = "SELECT it.id_intercambio, it.empleado_id_solicitante, it.empleado_id_receptor, it.fecha_turno, it.id_turno_original, it.id_turno_nuevo, it.estado, 
                                       CONCAT(es1.nombre, ' ', es1.apellido) AS solicitante, CONCAT(es2.nombre, ' ', es2.apellido) AS receptor, it.fecha_solicitud, it.fecha_aprobacion
                                FROM intercambio_turnos it
                                JOIN empleados es1 ON it.empleado_id_solicitante = es1.empleado_id
                                JOIN empleados es2 ON it.empleado_id_receptor = es2.empleado_id 
                                WHERE es1.empleado_id =  $empleado_id
                                ORDER BY it.fecha_turno DESC
                                ";
                        //echo $sql;
                        $result = $conn->query($sql);
        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id_intercambio'] . "</td>";
                                echo "<td>" . $row['empleado_id_solicitante'] . "</td>";
                                echo "<td>" . $row['solicitante'] . "</td>";
                                echo "<td>" . $row['empleado_id_receptor'] . "</td>";
                                echo "<td>" . $row['receptor'] . "</td>";
                                echo "<td>" . $row['fecha_turno'] . "</td>";
                                echo "<td>" . $row['id_turno_original'] . "</td>";
                                echo "<td>" . $row['id_turno_nuevo'] . "</td>";
                                echo "<td>" . $row['fecha_solicitud'] . "</td>";
                                echo "<td>" . $row['fecha_aprobacion'] . "</td>";
                                echo "<td>" . $row['estado'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            //echo "<div class='alert alert-info'>No hay No hay solicitudes</div>";
                            echo "<tr><td colspan='9'<div class='alert alert-info'>No hay solicitudes de intercambios</div></td></tr>";
                        }
        
                        $conn->close();
                    //}
                ?>
            </tbody>
        </table>
    </div>
    
</body>
</html>
