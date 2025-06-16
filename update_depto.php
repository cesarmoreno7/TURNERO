
<?php 
    include 'menu_ppal_maestros.php';
?>
<?php
include 'db.php';

$cod_departamento = $_GET['cod_departamento'];
$query = "SELECT * FROM departamento WHERE cod_departamento = $cod_departamento";
$result = $conn->query($query);
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_departamento = $_POST['nom_departamento'];
    $cod_pais = $_POST['cod_pais'];

    $updateQuery = "UPDATE departamento SET nom_departamento = '$nom_departamento', cod_pais = $cod_pais WHERE cod_departamento = $cod_departamento";
    if ($conn->query($updateQuery) === TRUE) {
        header('Location: form_consulta_depto.php');
    } else {
        echo "Error: " . $updateQuery . "<br>" . $conn->error;
    }
}

$paises = $conn->query("SELECT * FROM pais");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Departamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Actualizar Departamento</h2>
    <form method="POST" action="update_depto.php?cod_departamento=<?php echo $cod_departamento; ?>">
        <div class="mb-3">
            <label for="nom_departamento" class="form-label">Nombre del Departamento</label>
            <input type="text" class="form-control" id="nom_departamento" name="nom_departamento" value="<?php echo $row['nom_departamento']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="cod_pais" class="form-label">País</label>
            <select class="form-control" id="cod_pais" name="cod_pais" required>
                <?php while ($pais = $paises->fetch_assoc()) { ?>
                    <option value="<?php echo $pais['cod_pais']; ?>" <?php echo ($row['cod_pais'] == $pais['cod_pais']) ? 'selected' : ''; ?>>
                        <?php echo $pais['nom_pais']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
