<?php
include 'db.php';

// Obtener datos para listas desplegables
$empleados = $conn->query("SELECT empleado_id, nombre, apellido FROM empleados");
$servicios = $conn->query("SELECT servicio_id, nombre FROM servicio");

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $empleado_id = $_POST['empleado_id'];
    $servicio_id = $_POST['servicio_id'];
    $centro_costo_id = 0; // Mantiene el valor por defecto como 0
    $grupo_id = $_POST['grupo_id'];
    $estado = $_POST['estado'];
    $fec_ini = $_POST['fec_ini'];
    $fec_fin = $_POST['fec_fin'];

    // Insertar datos en la base de datos
    $sql = "INSERT INTO empleados_servicio (empleado_id, servicio_id, centro_costo_id, grupo_id, estado, fec_ini, fec_fin) 
            VALUES ('$empleado_id', '$servicio_id', '$centro_costo_id', '$grupo_id', '$estado', '$fec_ini', '$fec_fin')";

    if ($conn->query($sql) === TRUE) {
        header("Location: form_consulta_empleados_servicio.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<?php
include 'menu_ppal_maestros.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Registro</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <h2>Nuevo Registro de Empleado Servicio</h2>
    <form action="create_empleado_servicio.php" method="POST">
        <div class="form-group">
            <label>Empleado</label>
            <select name="empleado_id" class="form-control" required>
                <option value="">Seleccione un empleado</option>
                <?php while ($row = $empleados->fetch_assoc()): ?>
                    <option value="<?php echo $row['empleado_id']; ?>"><?php echo $row['nombre'] . ' ' . $row['apellido']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Servicio</label>
            <select name="servicio_id" id="servicio_id" class="form-control" required>
                <option value="">Seleccione un servicio</option>
                <?php while ($row = $servicios->fetch_assoc()): ?>
                    <option value="<?php echo $row['servicio_id']; ?>"><?php echo $row['nombre']; ?></option>
                <?php endwhile; ?>
            </select>
        </div> 
        <div class="form-group">
            <label>Grupo</label>
            <select name="grupo_id" id="grupo_id" class="form-control" required>
                <option value="">Seleccione un grupo</option>
            </select>
        </div>
        <div class="form-group">
            <label>Estado</label>
            <select name="estado" class="form-control" required>
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
            </select>
        </div>
        <div class="form-group">
            <label>Fecha de Inicio</label>
            <input type="date" name="fec_ini" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Fecha de Fin</label>
            <input type="date" name="fec_fin" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="form_consulta_empleados_servicio.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<!-- Script para filtrar grupos según el servicio seleccionado -->
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
