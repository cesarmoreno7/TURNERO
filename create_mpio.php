<?php
    include 'menu_ppal_maestros.php';
?>
<?php
include 'db.php';

// Insertar nuevo municipio
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_municipio = $_POST['nom_municipio'];
    $cod_departamento = $_POST['cod_departamento'];
    $cod_pais = $_POST['cod_pais'];

    $query = "INSERT INTO municipio (nom_municipio, cod_departamento, cod_pais) VALUES ('$nom_municipio', '$cod_departamento', '$cod_pais')";
    if (mysqli_query($conn, $query)) {
        echo "Municipio creado exitosamente.";
        header('Location: form_consulta_mpio.php');
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
    <title>Crear Municipio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Crear Municipio</h2>
    <form action="create_mpio.php" method="POST">
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
            <label for="nom_municipio" class="form-label">Nombre Municipio</label>
            <input type="text" name="nom_municipio" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="form_consulta_mpio.php" class="btn btn-secondary">Cancelar</a>
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
                }
            });
        } else {
            $('#cod_departamento').html('<option value="">Seleccione un departamento</option>');
        }
    });
</script>
</body>
</html>
