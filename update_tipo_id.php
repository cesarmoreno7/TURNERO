<?php 
    include 'menu_ppal_maestros.php';
?>
<?php
    include 'db.php';

    $cod_tipo_id = $_GET['cod_tipo_id'];
    $query = "SELECT * FROM tipo_identificacion WHERE cod_tipo_id = $cod_tipo_id";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $desc_tipo_id = $_POST['desc_tipo_id'];

        $updateQuery = "UPDATE tipo_identificacion SET desc_tipo_id = '$desc_tipo_id' WHERE cod_tipo_id = $cod_tipo_id";
        if ($conn->query($updateQuery) === TRUE) {
            header('Location: form_consulta_tipo_id.php');
        } else {
            echo "Error: " . $updateQuery . "<br>" . $conn->error;
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Tipo de Identificaci&oacute;</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Actualizar Tipo de Identificaci&oacute;n</h2>
    <form method="POST" action="update_tipo_id.php?cod_tipo_id=<?php echo $cod_tipo_id; ?>">
        <div class="mb-3">
            <label for="desc_tipo_id" class="form-label">Descripcii&oacute;n tipo de Id.</label>
            <input type="text" class="form-control" id="desc_tipo_id" name="desc_tipo_id" value="<?php echo $row['desc_tipo_id']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="form_consulta_tipo_id.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
