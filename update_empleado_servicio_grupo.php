<?php
include 'db.php';

// Obtener el ID del registro a editar desde la URL
$empleado_id = $_GET['empleado_id'];
$servicio_id = $_GET['servicio_id'];

// Obtener datos del registro a editar
$empleado_servicio = $conn->query("SELECT * FROM empleados_servicio WHERE empleado_id = $empleado_id AND servicio_id = $servicio_id")->fetch_assoc();

// Obtener datos para listas desplegables
$empleados = $conn->query("SELECT empleado_id, nombre, apellido FROM empleados");
$servicios = $conn->query("SELECT servicio_id, nombre FROM servicio");

// Obtener grupos filtrados según el centro de costo seleccionado
$grupos = $conn->query("SELECT grupo_id, nombre FROM grupo WHERE centro_costo_id = {$empleado_servicio['centro_costo_id']}");

// Manejar la actualización del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $empleado_id = $_POST['empleado_id'];
    $servicio_id = $_POST['servicio_id'];
    $centro_costo_id = $_POST['centro_costo_id'];
    $grupo_id = $_POST['grupo_id'];
    $estado = $_POST['estado'];
    $fec_ini = $_POST['fec_ini'];
    $fec_fin = $_POST['fec_fin'];

    // Actualizar los datos en la base de datos
    $sql = "UPDATE empleados_servicio SET 
            centro_costo_id = '$centro_costo_id', 
            grupo_id = '$grupo_id', 
            estado = '$estado', 
            fec_ini = '$fec_ini', 
            fec_fin = '$fec_fin' 
            WHERE empleado_id = $empleado_id AND servicio_id = $servicio_id";

    if ($conn->query($sql) === TRUE) {
        header("Location: form_consulta_empleados_servicio.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<?php include 'menu_ppal_maestros.php'; ?>
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
            <label>Empleado</label>
            <select name="empleado_id" class="form-control" required disabled>
                <?php while ($row = $empleados->fetch_assoc()): ?>
                    <option value="<?php echo $row['empleado_id']; ?>" <?php echo ($row['empleado_id'] == $empleado_servicio['empleado_id']) ? 'selected' : ''; ?>>
                        <?php echo $row['nombre'] . ' ' . $row['apellido']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Servicio</label>
            <select name="servicio_id" id="servicio_id" class="form-control" required disabled>
                <?php while ($row = $servicios->fetch_assoc()): ?>
                    <option value="<?php echo $row['servicio_id']; ?>" <?php echo ($row['servicio_id'] == $empleado_servicio['servicio_id']) ? 'selected' : ''; ?>>
                        <?php echo $row['nombre']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Grupo</label>
            <select name="grupo_id" id="grupo_id" class="form-control" required>
                <option value="">Seleccione un grupo</option>
                <?php while ($row = $grupos->fetch_assoc()): ?>
                    <option value="<?php echo $row['grupo_id']; ?>" <?php echo ($row['grupo_id'] == $empleado_servicio['grupo_id']) ? 'selected' : ''; ?>>
                        <?php echo $row['nombre']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Estado</label>
            <select name="estado" class="form-control" required>
                <option value="Activo" <?php echo ($empleado_servicio['estado'] == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                <option value="Inactivo" <?php echo ($empleado_servicio['estado'] == 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
            </select>
        </div>
        <div class="form-group">
            <label>Fecha de Inicio</label>
            <input type="date" name="fec_ini" class="form-control" value="<?php echo $empleado_servicio['fec_ini']; ?>" required>
        </div>
        <div class="form-group">
            <label>Fecha de Fin</label>
            <input type="date" name="fec_fin" class="form-control" value="<?php echo $empleado_servicio['fec_fin']; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="form_consulta_empleados_servicio.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<!-- Script para manejar el filtrado de grupos según el servicio seleccionado -->
<script>
$(document).ready(function() {
    // Detectar cambio en el servicio seleccionado
    $('#servicio_id').change(function() {
        let servicioId = $(this).val();
        $.ajax({
            url: 'get_servicio_grupos.php', // Archivo que obtiene los grupos según el servicio
            type: 'POST',
            data: { servicio_id: servicioId },
            success: function(response) {
                $('#grupo_id').html(response); // Actualizar las opciones del select de grupos
            }
        });
    });
});
</script>

</body>
</html>
