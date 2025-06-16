<?php
    include 'menu_ppal_maestros.php';
?>
<?php
include 'db.php';

$cod_empresa = $_GET['cod_empresa'];

// Obtener los datos de la empresa
$query = "SELECT * FROM empresas WHERE cod_empresa = '$cod_empresa'";
$result = mysqli_query($conn, $query);
$empresa = mysqli_fetch_assoc($result);

// Obtener lista de países
$paises_result = mysqli_query($conn, "SELECT * FROM pais");

// Obtener lista de departamentos y municipios relacionados con la empresa actual
$departamentos_result = mysqli_query($conn, "SELECT * FROM departamento WHERE cod_pais = '" . $empresa['cod_pais'] . "'");
$municipios_result = mysqli_query($conn, "SELECT * FROM municipio WHERE cod_departamento = '" . $empresa['cod_departamento'] . "'");


// Guardar cambios de la empresa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nit_empresa = $_POST['nit_empresa'];
    $dig_verif_empresa = $_POST['dig_verif_empresa'];
    $nom_empresa = $_POST['nom_empresa'];
    $dir_empresa = $_POST['dir_empresa'];
    $tel1_empresa = $_POST['tel1_empresa'];
    $tel2_empresa = $_POST['tel2_empresa'];
    $mail_empresa = $_POST['mail_empresa'];
    $cod_departamento = $_POST['cod_departamento'];
    $cod_municipio = $_POST['cod_municipio'];
    $cod_pais = $_POST['cod_pais'];
    $tipo_empresa = $_POST['tipo_empresa'];

    $query = "UPDATE empresas 
              SET nit_empresa = '$nit_empresa', dig_verif_empresa = '$dig_verif_empresa', nom_empresa = '$nom_empresa', 
                  dir_empresa = '$dir_empresa', tel1_empresa = '$tel1_empresa', tel2_empresa = '$tel2_empresa', 
                  mail_empresa = '$mail_empresa', cod_departamento = '$cod_departamento', 
                  cod_municipio = '$cod_municipio', cod_pais = '$cod_pais', tipo_empresa = '$tipo_empresa'
              WHERE cod_empresa = '$cod_empresa'";

    if (mysqli_query($conn, $query)) {
        echo "Empresa actualizada exitosamente.";
        header('Location: form_consulta_empresa.php');
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Empresa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Actualizar Empresa</h2>
    <form action="update_empresa.php?cod_empresa=<?= $cod_empresa ?>" method="POST">
        <div class="mb-3">
            <label for="nit_empresa" class="form-label">NIT</label>
            <input type="text" name="nit_empresa" class="form-control" value="<?= $empresa['nit_empresa'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="dig_verif_empresa" class="form-label">Dígito de Verificación</label>
            <input type="text" name="dig_verif_empresa" class="form-control" value="<?= $empresa['dig_verif_empresa'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="nom_empresa" class="form-label">Nombre Empresa</label>
            <input type="text" name="nom_empresa" class="form-control" value="<?= $empresa['nom_empresa'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="mes">Tipo Empresa</label>
            <select class="form-control" id="tipo_empresa" name="tipo_empresa" required>
                 <option value="Salud" <?php echo ($empleado['tipo_empresa'] == 'Salud') ? 'selected' : ''; ?>>Salud</option>
                <option value="Vigilancia" <?php echo ($empleado['tipo_empresa'] == 'Vigilancia') ? 'selected' : ''; ?>>Vigilancia</option>
                <option value="Otro" <?php echo ($empleado['tipo_empresa'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="dir_empresa" class="form-label">Dirección</label>
            <input type="text" name="dir_empresa" class="form-control" value="<?= $empresa['dir_empresa'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="tel1_empresa" class="form-label">Teléfono 1</label>
            <input type="text" name="tel1_empresa" class="form-control" value="<?= $empresa['tel1_empresa'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="tel2_empresa" class="form-label">Teléfono 2</label>
            <input type="text" name="tel2_empresa" class="form-control" value="<?= $empresa['tel2_empresa'] ?>">
        </div>
        <div class="mb-3">
            <label for="mail_empresa" class="form-label">Correo Electrónico</label>
            <input type="email" name="mail_empresa" class="form-control" value="<?= $empresa['mail_empresa'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="cod_pais" class="form-label">País</label>
            <select id="cod_pais" name="cod_pais" class="form-select" required>
                <option value="">Seleccione un país</option>
                <?php while ($pais = mysqli_fetch_assoc($paises_result)) { ?>
                    <option value="<?= $pais['cod_pais'] ?>" <?= ($pais['cod_pais'] == $empresa['cod_pais']) ? 'selected' : '' ?>><?= $pais['nom_pais'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="cod_departamento" class="form-label">Departamento</label>
            <select id="cod_departamento" name="cod_departamento" class="form-select" required>
                <option value="">Seleccione un departamento</option>
                <?php while ($departamento = mysqli_fetch_assoc($departamentos_result)) { ?>
                    <option value="<?= $departamento['cod_departamento'] ?>" <?= ($departamento['cod_departamento'] == $empresa['cod_departamento']) ? 'selected' : '' ?>><?= $departamento['nom_departamento'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="cod_municipio" class="form-label">Municipio</label>
            <select id="cod_municipio" name="cod_municipio" class="form-select" required>
                <option value="">Seleccione un municipio</option>
                <?php while ($municipio = mysqli_fetch_assoc($municipios_result)) { ?>
                    <option value="<?= $municipio['cod_municipio'] ?>" <?= ($municipio['cod_municipio'] == $empresa['cod_municipio']) ? 'selected' : '' ?>><?= $municipio['nom_municipio'] ?></option>
                <?php } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Empresa</button>
        <a href="form_consulta_empresa.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#cod_pais').change(function () {
            var cod_pais = $(this).val();
            $.ajax({
                url: 'get_departamentos.php',
                type: 'POST',
                data: {cod_pais: cod_pais},
                success: function (response) {
                    $('#cod_departamento').html(response);
                    $('#cod_municipio').html('<option value="">Seleccione un municipio</option>');
                }
            });
        });

        $('#cod_departamento').change(function () {
            var cod_departamento = $(this).val();
            $.ajax({
                url: 'get_municipios.php',
                type: 'POST',
                data: {cod_departamento: cod_departamento},
                success: function (response) {
                    $('#cod_municipio').html(response);
                }
            });
        });
    });
</script>
</body>
</html>
