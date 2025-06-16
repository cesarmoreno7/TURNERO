<?php
    include 'menu_ppal_emp.php';
?>
<?php
    include("session.php");
    include "db.php";
    
    $servicio_id = $_SESSION['servicio_id'];
    $centro_costo_id = $_SESSION['centro_costo_id'];
    $grupo_id = $_SESSION['grupo_id'];
    $empleado_id = $_SESSION['empleado_id'];
    
    $sql = "SELECT e.empleado_id, CONCAT(e.nombre, ' ', e.apellido) AS nombre_completo 
    FROM empleados e inner join empleados_servicio es ON(e.empleado_id = es.empleado_id)
    where es.servicio_id = $servicio_id AND es.centro_costo_id = $centro_costo_id AND es.grupo_id = $grupo_id AND es.estado = 'Activo' AND e.empleado_id not in($empleado_id)";
    //echo $sql;
    $result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Solicitar Intercambio de turnos</title>
    <!-- Incluir el CSS de Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Solicitar Intercambio de Turnos</h2>
        <form action="solicitar_intercambio_turno.php" method="post">
            <div class="form-group">
                <label for="empleado_id_receptor">Empleado con quien desea intercambiar:</label>
                <select class="form-control" name="empleado_id_receptor" id="empleado_id_receptor" required>
                    <?php
                        echo "<option value=''>"."Seleccione empleado"."</option>";
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['empleado_id'] . "'>" . $row['nombre_completo'] . "</option>";
                            }
                        }
                        $conn->close();
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_turno">Fecha del turno a intercambiar:</label>
                <input type="date" class="form-control" name="fecha_turno" id="fecha_turno" required>
            </div>
            <button type="submit" class="btn btn-primary">Solicitar Intercambio</button>
            <a href="form_intercambios_empleado.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <!-- Incluir el JavaScript de Bootstrap y sus dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
