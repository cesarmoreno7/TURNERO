<?php
    include 'menu_ppal_maestros.php';    
?>
<?php
    include("session.php");
    include 'db.php';
    
     $cod_empresa =  $_SESSION['cod_empresa'];
    
    // Obtener datos para listas desplegables
    $empleados = $conn->query("SELECT empleado_id, nombre, apellido FROM empleados");
    $servicios = $conn->query("SELECT servicio_id, nombre FROM servicio WHERE cod_empresa = $cod_empresa");
    
    // Manejar el envío del formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $empleado_id = $_POST['empleado_id'];
        $servicio_id = $_POST['servicio_id'];
        $centro_costo_id = $_POST['centro_costo_id'];
        $grupo_id = $_POST['grupo_id'];
        $estado = $_POST['estado'];
        $fec_ini = $_POST['fec_ini'];
        $fec_fin = $_POST['fec_fin'];
    
        // Insertar datos en la base de datos
        $sql = "INSERT INTO empleados_servicio (empleado_id, servicio_id, centro_costo_id, grupo_id, estado, fec_ini, fec_fin, cod_empresa) 
                VALUES ($empleado_id, $servicio_id, $centro_costo_id, $grupo_id, '$estado', '$fec_ini', '$fec_fin',$cod_empresa)";
    
        if ($conn->query($sql) === TRUE) {
             echo "<script>
                    alert('¡Registro exitoso!');
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
    <title>Nuevo Registro</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
            <select name="servicio_id" class="form-control" id="servicio" required>
                <option value="">Seleccione un servicio</option>
                <?php while ($row = $servicios->fetch_assoc()): ?>
                    <option value="<?php echo $row['servicio_id']; ?>"><?php echo $row['nombre']; ?></option>
                <?php endwhile; ?>
            </select>
        </div> 
        <div class="form-group">
            <label>Centro de Costo</label>
            <select name="centro_costo_id" id="centro_costo" class="form-control" required>
                <option value="">Seleccione un centro de costo</option>
            </select>
        </div>
        <div class="form-group">
            <label>Grupo</label>
            <select name="grupo_id" id="grupo" class="form-control" required>
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
<!-- Script para manejar los filtros dinámicos -->
<script>
document.getElementById('servicio').addEventListener('change', function() {
    const servicioId = this.value;
    if (servicioId) {
        fetch(`get_centros_costo.php?servicio_id=${servicioId}`) // Usar GET en lugar de POST
            .then(response => response.text()) // Cambiado de JSON a texto porque se está devolviendo HTML (opciones)
            .then(data => {
                let centroCostoSelect = document.getElementById('centro_costo');
                centroCostoSelect.innerHTML = data; // Colocar directamente las opciones
                centroCostoSelect.dispatchEvent(new Event('change')); // Disparar evento de cambio
            });
    }
});

document.getElementById('centro_costo').addEventListener('change', function() {
    const centroCostoId = this.value;
    if (centroCostoId) {
        fetch(`get_grupos.php?centro_costo_id=${centroCostoId}`) // Usar GET en lugar de POST
            .then(response => response.text()) // Cambiado de JSON a texto porque se está devolviendo HTML (opciones)
            .then(data => {
                let centroCostoSelect = document.getElementById('grupo');
                centroCostoSelect.innerHTML = data; // Colocar directamente las opciones
                centroCostoSelect.dispatchEvent(new Event('change')); // Disparar evento de cambio
            });
    }
});
</script>
</body>
</html>
