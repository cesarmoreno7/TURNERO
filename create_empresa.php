<?php
    include 'menu_ppal_maestros.php';
?>
<?php
include 'db.php';

// Guardar nueva empresa
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

    $query = "INSERT INTO empresas (nit_empresa, dig_verif_empresa, nom_empresa, dir_empresa, tel1_empresa, tel2_empresa, mail_empresa, cod_departamento, cod_municipio, cod_pais,tipo_empresa) 
              VALUES ('$nit_empresa', '$dig_verif_empresa', '$nom_empresa', '$dir_empresa', '$tel1_empresa', '$tel2_empresa', '$mail_empresa', '$cod_departamento', '$cod_municipio', '$cod_pais','$tipo_empresa')";
    if (mysqli_query($conn, $query)) {
        echo "Empresa creada exitosamente.";
        header('Location: form_consulta_empresa.php');
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Obtener lista de países
$paises_result = mysqli_query($conn, "SELECT * FROM pais");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Empresa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Crear Empresa</h2>
    <form action="create_empresa.php" method="POST">
        <div class="mb-3">
            <label for="nit_empresa" class="form-label">NIT</label>
            <input type="text" name="nit_empresa" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="dig_verif_empresa" class="form-label">Dígito de Verificación</label>
            <input type="text" name="dig_verif_empresa" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="nom_empresa" class="form-label">Nombre Empresa</label>
            <input type="text" name="nom_empresa" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="mes">Tipo Empresa</label>
            <select class="form-control" id="mes" name="tipo_empresa" required>
                <option value="">Seleccione tipo de empresa</option>
                <option value="Salud">Salud</option>
                <option value="Vigilancia">Vigilancia</option>
                <option value="Otro">Otro</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="dir_empresa" class="form-label">Dirección</label>
            <input type="text" name="dir_empresa" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="tel1_empresa" class="form-label">Teléfono 1</label>
            <input type="text" name="tel1_empresa" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="tel2_empresa" class="form-label">Teléfono 2</label>
            <input type="text" name="tel2_empresa" class="form-control">
        </div>
        <div class="mb-3">
            <label for="mail_empresa" class="form-label">Correo Electrónico</label>
            <input type="email" name="mail_empresa" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="cod_pais" class="form-label">País</label>
            <select id="cod_pais" name="cod_pais" class="form-select" required>
                <option value="">Seleccione un país</option>
                <?php while ($pais = mysqli_fetch_assoc($paises_result)) { ?>
                    <option value="<?= $pais['cod_pais'] ?>"><?= $pais['nom_pais'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="cod_departamento" class="form-label">Departamento</label>
            <select id="cod_departamento" name="cod_departamento" class="form-select" required>
                <option value="">Seleccione un departamento</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="cod_municipio" class="form-label">Municipio</label>
            <select id="cod_municipio" name="cod_municipio" class="form-select" required> 
                <option value="">Seleccione un municipio</option> 
            </select> 
        </div> <button type="submit" class="btn btn-primary">Guardar Empresa</button> 
        <a href="form_consulta_empresa.php" class="btn btn-secondary">Cancelar</a>
    </form>

</div> <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
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
</html> ```
