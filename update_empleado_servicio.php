<?php
    include 'menu_ppal_maestros.php';
?>
<?php

    include 'db.php';
    
    // Obtener el ID del registro a editar desde la URL
    $empleado_id = $_GET['empleado_id'];
    $servicio_id = $_GET['servicio_id'];
    
    // Obtener datos del registro a editar
    $empleado_servicio = "SELECT * FROM empleados_servicio WHERE empleado_id = $empleado_id AND servicio_id = $servicio_id";
    $result = mysqli_query($conn, $empleado_servicio);
    $empleado_servicios = mysqli_fetch_assoc($result);
    
    
    // Manejar la actualización del formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $estado = $_POST['estado'];
        $fec_ini = $_POST['fec_ini'];
        $fec_fin = $_POST['fec_fin'];
    
        // Actualizar los datos en la base de datos
        $sql = "UPDATE empleados_servicio SET 
                estado = '$estado', 
                fec_ini = '$fec_ini', 
                fec_fin = '$fec_fin' 
                WHERE empleado_id = $empleado_id AND servicio_id = $servicio_id";
    
        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    alert('¡actualización exitosa!');
                    window.location.href = 'form_consulta_empleados_servicio.php';
                  </script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Registro</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <h2>Editar Registro de Servicio para un Empleado</h2>
    <form action="update_empleado_servicio.php?empleado_id=<?php echo $empleado_id; ?>&servicio_id=<?php echo $servicio_id; ?>" method="POST">
        <div class="form-group">
            <label>Estado</label>
            <select name="estado" class="form-control" required>
                <option value="Activo" <?php echo ($empleado_servicios['estado'] == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                <option value="Inactivo" <?php echo ($empleado_servicios['estado'] == 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
            </select>
        </div>
        <div class="form-group">
            <label>Fecha de Inicio</label>
            <input type="date" name="fec_ini" class="form-control" value="<?php echo $empleado_servicios['fec_ini']; ?>" required>
        </div>
        <div class="form-group">
            <label>Fecha de Fin</label>
            <input type="date" name="fec_fin" class="form-control" value="<?php echo $empleado_servicios['fec_fin']; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="form_consulta_empleados_servicio.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

</body>
</html>
