<?php
    include 'menu_ppal_maestros.php';
?>
<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cod_rol = $_POST['cod_rol'];
    $cod_alterno_rol = $_POST['cod_alterno_rol'];
    $desc_rol = $_POST['desc_rol'];

    $query = "INSERT INTO rol (cod_rol, cod_alterno_rol, desc_rol) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $cod_rol, $cod_alterno_rol, $desc_rol);

    if ($stmt->execute()) {
        echo "Rol creado con éxito";
        header('Location: form_consulta_rol.php');
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Rol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Crear Rol</h2>
    <form method="POST" action="create_rol.php">
        <div class="mb-3">
            <label for="cod_rol" class="form-label">Código del Rol</label>
            <input type="text" class="form-control" id="cod_rol" name="cod_rol" required>
        </div>
        <div class="mb-3">
            <label for="cod_alterno_rol" class="form-label">Código Alterno</label>
            <input type="text" class="form-control" id="cod_alterno_rol" name="cod_alterno_rol" required>
        </div>
        <div class="mb-3">
            <label for="desc_rol" class="form-label">Descripción del Rol</label>
            <input type="text" class="form-control" id="desc_rol" name="desc_rol" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="form_consulta_rol.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
