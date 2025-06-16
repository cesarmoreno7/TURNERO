<?php 
    include 'menu_ppal_maestros.php';
?>
<?php
    include 'db.php';

    $cod_pais = $_GET['cod_pais'];
    $query = "SELECT * FROM pais WHERE cod_pais = $cod_pais";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nom_pais = $_POST['nom_pais'];

        $updateQuery = "UPDATE pais SET nom_pais = '$nom_pais' WHERE cod_pais = $cod_pais";
        if ($conn->query($updateQuery) === TRUE) {
            header('Location: form_consulta_pais.php');
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
    <title>Actualizar País</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Actualizar País</h2>
    <form method="POST" action="update_pais.php?cod_pais=<?php echo $cod_pais; ?>">
        <div class="mb-3">
            <label for="nom_pais" class="form-label">Nombre del País</label>
            <input type="text" class="form-control" id="nom_pais" name="nom_pais" value="<?php echo $row['nom_pais']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="form_consulta_pais.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
