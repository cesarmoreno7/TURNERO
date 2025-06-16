<?php
    include 'menu_ppal_maestros.php';
?>
<?php
include 'db.php';

$cod_cargo = $_GET['id'];
$query = "SELECT * FROM cargo WHERE cod_cargo = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $cod_cargo);
$stmt->execute();
$result = $stmt->get_result();
$cargo = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cod_cargo = $_POST['cod_cargo'];
    $cod_alterno_cargo = $_POST['cod_alterno_cargo'];
    $desc_cargo = $_POST['desc_cargo'];

    $update_query = "UPDATE cargo SET cod_alterno_cargo = ?, desc_cargo = ? WHERE cod_cargo = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('sss', $cod_alterno_cargo, $desc_cargo, $cod_cargo);

    if ($stmt->execute()) {
        echo "Cargo actualizado con éxito";
        header('Location: form_consulta_cargo.php');
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Cargo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Actualizar Cargo</h2>
    <form method="POST" action="update_cargo.php">
        <div class="mb-3">
            <label for="cod_cargo" class="form-label">Código del Cargo</label>
            <input type="text" class="form-control" id="cod_cargo" name="cod_cargo" value="<?php echo $cargo['cod_cargo']; ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="cod_alterno_cargo" class="form-label">Código Alterno</label>
            <input type="text" class="form-control" id="cod_alterno_cargo" name="cod_alterno_cargo" value="<?php echo $cargo['cod_alterno_cargo']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="desc_cargo" class="form-label">Descripción del Cargo</label>
            <input type="text" class="form-control" id="desc_cargo" name="desc_cargo" value="<?php echo $cargo['desc_cargo']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="form_consulta_cargo.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
