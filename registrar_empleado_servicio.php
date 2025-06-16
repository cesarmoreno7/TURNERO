<?php
    include 'db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             
        $_POST['fec_ini'] =  date('Y-m-d'); 
        
        $stmt = $conn->prepare("INSERT INTO empleados_servicio (empleado_id, servicio_id, centro_costo_id, grupo_id, estado, fec_ini, fec_fin) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iiissss', $_POST['empleado_id'], $_POST['servicio_id'], $_POST['centro_costo_id'], $_POST['grupo_id'], $_POST['estado'], $_POST['fec_ini'], $_POST['fec_fin']);
        $stmt->execute();
        header('Location: form_consulta_empleados_servicio.php');
    }
?>
