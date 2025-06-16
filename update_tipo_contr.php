<?php 
    include 'menu_ppal_maestros.php';
?>
<?php
    include 'db.php';

    $cod_tipo_contrato = $_GET['cod_tipo_contrato'];
    $query = "SELECT * FROM tipo_contrato WHERE cod_tipo_contrato = $cod_tipo_contrato";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $cod_alterno_tipo_contrato = $_POST['cod_alterno_tipo_contrato'];
        $desc_tipo_contrato = $_POST['desc_tipo_contrato'];

        $updateQuery = "UPDATE tipo_contrato SET cod_alterno_tipo_contrato = $cod_alterno_tipo_contrato, desc_tipo_contrato = '$desc_tipo_contrato' WHERE cod_tipo_contrato = $cod_tipo_contrato";
        if ($conn->query($updateQuery) === TRUE) {
            header('Location: form_consulta_tipo_contr.php');
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
    <title>Actualizar Tipo de Contrato</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Actualizar Tipo de Contrato</h2>
    <form method="POST" action="update_tipo_contr.php?cod_tipo_contrato=<?php echo $cod_tipo_contrato; ?>">
        <div class="mb-3">
            <label for="cod_alterno_tipo_contrato" class="form-label">Código Alterno</label>
            <input type="text" class="form-control" id="cod_alterno_tipo_contrato" name="cod_alterno_tipo_contrato" value="<?php echo $row['cod_alterno_tipo_contrato']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="desc_tipo_contrato" class="form-label">Descripción</label>
            <input type="text" class="form-control" id="desc_tipo_contrato" name="desc_tipo_contrato" value="<?php echo $row['desc_tipo_contrato']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="form_consulta_tipo_contr.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
