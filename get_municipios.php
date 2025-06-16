<?php
    include 'db.php';
    
    if (isset($_POST['cod_depto'])) {
        $cod_departamento = $_POST['cod_depto'];
        $query = "SELECT * FROM municipio WHERE cod_departamento = $cod_departamento";
        $result = mysqli_query($conn, $query);
    
        if (mysqli_num_rows($result) > 0) {
            echo '<option value="">Seleccione un municipio</option>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<option value="' . $row['cod_municipio'] . '">' . $row['nom_municipio'] . '</option>';
            }
        } else {
            echo '<option value="">No hay municipios disponibles</option>';
        }
    }
?>
