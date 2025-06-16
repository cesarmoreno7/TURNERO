<?php 
    include 'menu_ppal_maestros.php';
?>
<?php
    include 'db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $cod_alterno_tipo_contrato = $_POST['cod_alterno_tipo_contrato'];
        $desc_tipo_contrato = $_POST['desc_tipo_contrato'];

        $query = "INSERT INTO tipo_contrato (cod_alterno_tipo_contrato,desc_tipo_contrato) VALUES ('$cod_alterno_tipo_contrato','$desc_tipo_contrato')";
        if ($conn->query($query) === TRUE) {
            header('Location: form_consulta_tipo_contr.php');
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
    <title>Agregar Tipo de Contrato</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Agregar Tipo de Contrato</h2>
    <form method="POST" action="create_tipo_contr.php">
        <div class="mb-3">
            <label for="cod_alterno_tipo_contrato" class="form-label">Código Alterno</label>
            <input type="text" class="form-control" id="cod_alterno_tipo_contrato" name="cod_alterno_tipo_contrato" required>
        </div>
        <div class="mb-3">
            <label for="desc_tipo_contrato" class="form-label">Descripción</label>
            <input type="text" class="form-control" id="desc_tipo_contrato" name="desc_tipo_contrato" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="form_consulta_tipo_contr.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
