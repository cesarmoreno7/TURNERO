<?php
include 'session.php';
include 'db.php';
$cod_empresa = $_SESSION['cod_empresa'];
$servicio = [
    'empleado_id' => '',
    'servicio_id' => '',
    'centro_costo_id' => '',
    'grupo_id' => '',
    'estado' => '',
    'fec_ini' => '',
    'fec_fin' => ''
];
$isEditing = false;

if (isset($_GET['empleado_id']) && isset($_GET['servicio_id']) && isset($_GET['centro_costo_id']) && isset($_GET['grupo_id'])) {
    $isEditing = true;
    $stmt = $conn->prepare("SELECT * FROM empleados_servicio WHERE empleado_id = ? AND servicio_id = ? AND centro_costo_id = ? AND grupo_id = ?");
    $stmt->bind_param('iiii', $_GET['empleado_id'], $_GET['servicio_id'], $_GET['centro_costo_id'], $_GET['grupo_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $servicio = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $isEditing ? 'Editar' : 'Agregar' ?> Servicios Empleado</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center"><?= $isEditing ? 'Editar' : 'Agregar' ?> Detalle Servicios del Empleado</h2>
    <form action="<?= $isEditing ? 'editar_empleado_servicio.php' : 'registrar_empleado_servicio.php' ?>" method="POST">
        <input type="hidden" name="empleado_id" value="<?= $servicio['empleado_id'] ?>">

        <div class="form-group">
            <label for="servicio">Servicio</label>
            <select class="form-control" id="servicio" name="servicio_id" required>
                <option value=''>Seleccione Servicio</option>
                <?php
                    $sql = "SELECT servicio_id, nombre FROM servicio WHERE cod_empresa = $cod_empresa";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $selected = $servicio['servicio_id'] == $row['servicio_id'] ? 'selected' : '';
                            echo "<option value='" . $row['servicio_id'] . "' $selected>" . $row['nombre'] . "</option>";
                        }
                    }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="centro_costo">Centro Costo</label>
            <select class="form-control" id="centro_costo" name="centro_costo_id" required>
                <!-- Opciones dinámicas cargadas por JavaScript -->
            </select>
        </div>

        <div class="form-group">
            <label for="grupo">Grupo</label>
            <select class="form-control" id="grupo" name="grupo_id" required>
                <!-- Opciones dinámicas cargadas por JavaScript -->
            </select>
        </div>

        <div class="form-group">
            <label for="estado">Estado</label>
            <select class="form-control" id="estado" name="estado" required>
                <option value="Activo" <?= $servicio['estado'] == 'Activo' ? 'selected' : '' ?>>Activo</option>
                <option value="Inactivo" <?= $servicio['estado'] == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary"><?= $isEditing ? 'Actualizar' : 'Guardar' ?></button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
    document.getElementById('servicio').addEventListener('change', function() {
        const servicioId = this.value;
        if (servicioId) {
            fetch(`get_centros_costo.php?servicio_id=${servicioId}`)
                .then(response => response.json())
                .then(data => {
                    let centroCostoSelect = document.getElementById('centro_costo');
                    centroCostoSelect.innerHTML = '';
                    data.forEach(item => {
                        let option = document.createElement('option');
                        option.value = item.centro_costo_id;
                        option.text = item.nombre;
                        centroCostoSelect.appendChild(option);
                    });
                    centroCostoSelect.dispatchEvent(new Event('change'));
                });
        }
    });

    document.getElementById('centro_costo').addEventListener('change', function() {
        const centroCostoId = this.value;
        if (centroCostoId) {
            fetch(`get_grupos.php?centro_costo_id=${centroCostoId}`)
                .then(response => response.json())
                .then(data => {
                    let grupoSelect = document.getElementById('grupo');
                    grupoSelect.innerHTML = '';
                    data.forEach(item => {
                        let option = document.createElement('option');
                        option.value = item.grupo_id;
                        option.text = item.nombre;
                        grupoSelect.appendChild(option);
                    });
                });
        }
    });

    // Preload centro_costo and grupo if editing
    window.addEventListener('load', function() {
        if (<?= $isEditing ? 'true' : 'false' ?>) {
            document.getElementById('servicio').dispatchEvent(new Event('change'));
            setTimeout(() => {
                document.getElementById('centro_costo').dispatchEvent(new Event('change'));
            }, 1000);
        }
    });
</script>

</body>
</html>
