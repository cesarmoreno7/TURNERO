<?php
    include 'menu_ppal_maestros.php';
?>
<?php
include 'db.php';

// Obtener detalles de la vereda
if (isset($_GET['cod_vereda'])) {
    $cod_vereda = $_GET['cod_vereda'];
    $vereda_result = mysqli_query($conn, "SELECT * FROM vereda WHERE cod_vereda = $cod_vereda");
    $vereda = mysqli_fetch_assoc($vereda_result);
}

// Actualizar vereda
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cod_vereda = $_POST['cod_vereda'];
    $nom_vereda = $_POST['nom_vereda'];
    $cod_municipio = $_POST['cod_municipio'];
    $cod_departamento = $_POST['cod_departamento'];
    $cod_pais = $_POST['cod_pais'];

    $query = "UPDATE vereda SET nom_vereda='$nom_vereda', cod_municipio='$cod_municipio', cod_departamento='$cod_departamento', cod_pais='$cod_pais' WHERE cod_vereda='$cod_vereda'";
    if (mysqli_query($conn, $query)) {
        echo "Vereda actualizada exitosamente.";        
        header('Location: form_consulta_vereda.php');
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Obtener lista de países
$paises_result = mysqli_query($conn, "SELECT * FROM pais");

// Obtener departamentos y municipios actuales según el valor seleccionado de la vereda
$departamentos_result = mysqli_query($conn, "SELECT * FROM departamento WHERE cod_pais = " . $vereda['cod_pais']);
$municipios_result = mysqli_query($conn, "SELECT * FROM municipio WHERE cod_departamento = " . $vereda['cod_departamento']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Vereda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Actualizar Vereda</h2>
    <form action="update_vereda.php?cod_vereda=<?= $cod_vereda ?>" method="POST">
        <input type="hidden" name="cod_vereda" value="<?= $vereda['cod_vereda'] ?>">
        
        <div class="mb-3">
            <label for="cod_pais" class="form-label">País</label>
            <select id="cod_pais" name="cod_pais" class="form-select" required>
                <option value="">Seleccione un país</option>
                <?php while ($pais = mysqli_fetch_assoc($paises_result)) { ?>
                    <option value="<?= $pais['cod_pais'] ?>" <?= ($pais['cod_pais'] == $vereda['cod_pais']) ? 'selected' : '' ?>><?= $pais['nom_pais'] ?></option>
                <?php } ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="cod_departamento" class="form-label">Departamento</label>
            <select id="cod_departamento" name="cod_departamento" class="form-select" required>
                <option value="">Seleccione un departamento</option>
                <?php while ($departamento = mysqli_fetch_assoc($departamentos_result)) { ?>
                    <option value="<?= $departamento['cod_departamento'] ?>" <?= ($departamento['cod_departamento'] == $vereda['cod_departamento']) ? 'selected' : '' ?>><?= $departamento['nom_departamento'] ?></option>
                <?php } ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="cod_municipio" class="form-label">Municipio</label>
            <select id="cod_municipio" name="cod_municipio" class="form-select" required>
                <option value="">Seleccione un municipio</option>
                <?php while ($municipio = mysqli_fetch_assoc($municipios_result)) { ?>
                    <option value="<?= $municipio['cod_municipio'] ?>" <?= ($municipio['cod_municipio'] == $vereda['cod_municipio']) ? 'selected' : '' ?>><?= $municipio['nom_municipio'] ?></option>
                <?php } ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="nom_vereda" class="form-label">Nombre Vereda</label>
            <input type="text" name="nom_vereda" class="form-control" value="<?= $vereda['nom_vereda'] ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="form_consulta_vereda.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#cod_pais').on('change', function() {
        var cod_pais = $(this).val();
        if (cod_pais) {
            $.ajax({
                type: 'POST',
                url: 'get_departamentos.php',
                data: 'cod_pais=' + cod_pais,
                success: function(html) {
                    $('#cod_departamento').html(html);
                    $('#cod_municipio').html('<option value="">Seleccione un municipio</option>');
                }
            });
        } else {
            $('#cod_departamento').html('<option value="">Seleccione un departamento</option>');
            $('#cod_municipio').html('<option value="">Seleccione un municipio</option>');
        }
    });

    $('#cod_departamento').on('change', function() {
        var cod_departamento = $(this).val();
        if (cod_departamento) {
            $.ajax({
                type: 'POST',
                url: 'get_municipios.php',
                data: 'cod_departamento=' + cod_departamento,
                success: function(html) {
                    $('#cod_municipio').html(html);
                }
            });
        } else {
            $('#cod_municipio').html('<option value="">Seleccione un municipio</option>');
        }
    });
</script>
</body>
</html>
