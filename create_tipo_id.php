<?php 
    include 'menu_ppal_maestros.php';
?>
<?php
    include 'db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $desc_tipo_id = $_POST['desc_tipo_id'];

        $query = "INSERT INTO tipo_identificacion (desc_tipo_id) VALUES ('$desc_tipo_id')";
        if ($conn->query($query) === TRUE) {
            header('Location: form_consulta_tipo_id.php');
        } else {
            echo "Error: " . $query . "<br>" . $conn->error;
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar tipo identificaci&oacute;n</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Agregar Tipo de Identificaci&oacute;n</h2>
    <form method="POST" action="create_tipo_id.php">
        <div class="mb-3">
            <label for="desc_tipo_id" class="form-label">Descripcii&oacute;n tipo de Id.</label>
            <input type="text" class="form-control" id="desc_tipo_id" name="desc_tipo_id" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="form_consulta_tipo_id.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
