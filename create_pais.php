<?php 
    include 'menu_ppal_maestros.php';
?>
<?php
    include 'db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nom_pais = $_POST['nom_pais'];

        $query = "INSERT INTO pais (nom_pais) VALUES ('$nom_pais')";
        if ($conn->query($query) === TRUE) {
            header('Location: form_consulta_pais.php');
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
    <title>Agregar País</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Agregar País</h2>
    <form method="POST" action="create_pais.php">
        <div class="mb-3">
            <label for="nom_pais" class="form-label">Nombre del País</label>
            <input type="text" class="form-control" id="nom_pais" name="nom_pais" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="form_consulta_pais.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
