<?php 
    include 'menu_ppal_maestros.php';
?>
<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_departamento = $_POST['nom_departamento'];
    $cod_pais = $_POST['cod_pais'];

    $query = "INSERT INTO departamento (nom_departamento, cod_pais) VALUES ('$nom_departamento', $cod_pais)";
    if ($conn->query($query) === TRUE) {
        header('Location: form_consulta_depto.php');
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}

$paises = $conn->query("SELECT * FROM pais");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Departamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Agregar Departamento</h2>
    <form method="POST" action="create_depto.php">
        <div class="mb-3">
            <label for="nom_departamento" class="form-label">Nombre del Departamento</label>
            <input type="text" class="form-control" id="nom_departamento" name="nom_departamento" required>
        </div>
        <div class="mb-3">
            <label for="cod_pais" class="form-label">País</label>
            <select class="form-control" id="cod_pais" name="cod_pais" required>
                <?php while ($pais = $paises->fetch_assoc()) { ?>
                    <option value="<?php echo $pais['cod_pais']; ?>"><?php echo $pais['nom_pais']; ?></option>
                <?php } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
