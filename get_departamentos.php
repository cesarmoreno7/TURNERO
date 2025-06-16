<?php
include 'db.php';

if (isset($_POST['cod_pais'])) {
    $cod_pais = $_POST['cod_pais'];
    $query = "SELECT * FROM departamento WHERE cod_pais = $cod_pais";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo '<option value="">Seleccione un departamento</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['cod_departamento'] . '">' . $row['nom_departamento'] . '</option>';
        }
    } else {
        echo '<option value="">No hay departamentos disponibles</option>';
    }
}
?>
