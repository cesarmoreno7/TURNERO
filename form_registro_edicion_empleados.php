<?php
    include 'session.php';
    include 'db.php';
    $empleado = [
        'empleado_id' => '',
        'nombre' => '',
        'apellido' => '',
        'cod_empresa' => ''
    ];
    $isEditing = false;

    $cod_empresa = $_SESSION['cod_empresa'];

    if (isset($_GET['empleado_id'])) {
        $isEditing = true;
        $stmt = $conn->prepare("SELECT * FROM empleados WHERE empleado_id = ? AND cod_empresa = ?");
        $stmt->bind_param('ii', $_GET['empleado_id'],$cod_empresa);
        $stmt->execute();
        $result = $stmt->get_result();
        $empleado = $result->fetch_assoc();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $isEditing ? 'Editar' : 'Agregar' ?> Empleado</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center"><?= $isEditing ? 'Editar' : 'Agregar' ?> Empleado</h2>
    <form action="<?= $isEditing ? 'editar_empleado.php' : 'registrar_empleado.php' ?>" method="POST">
        <input type="hidden" name="empleado_id" value="<?= $empleado['empleado_id'] ?>">
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $empleado['nombre'] ?>" required>
        </div>
        <div class="form-group">
            <label for="apellido">Apellido</label>
            <input type="text" class="form-control" id="apellido" name="apellido" value="<?= $empleado['apellido'] ?>" required>
        </div>
        <input type="hidden" class="form-control" name="cod_empresa" value="<?= $cod_empresa ?>">
        <button type="submit" class="btn btn-primary"><?= $isEditing ? 'Actualizar' : 'Guardar' ?></button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
