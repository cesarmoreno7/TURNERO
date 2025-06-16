<?php
    include 'db.php';
    include("session.php");

    if (isset($_GET['servicio_id'])) {
        $servicio_id = intval($_GET['servicio_id']);
        $cod_empresa = $_SESSION['cod_empresa'];
    
        $sql = "SELECT grupo_id, nombre FROM grupo_servicio WHERE servicio_id = $servicio_id AND cod_empresa = $cod_empresa";
        $result = $conn->query($sql);
    
        // Generar las opciones del select
        echo '<option value="">Seleccione un grupo</option>';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['grupo_id'] . '">' . htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') . '</option>';
        }
    
        $conn->close();
    
        echo json_encode($grupos);
    }
?>
