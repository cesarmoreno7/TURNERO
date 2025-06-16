<?php
    include 'menu_ppal_emp.php';
?>
<?php

    include("session.php");
    include 'db.php'; // Archivo de conexión a la base de datos
    
    $servicio_id = $_SESSION['servicio_id'];
    $cod_empresa = $_SESSION['cod_empresa'];
    $centro_costo_id = $_SESSION['centro_costo_id'];
    $grupo_id = $_SESSION['grupo_id'];
    $empleado_id = $_SESSION['empleado_id'];

    // Obtener listas para selects
    $empleados = $conn->query("SELECT e.empleado_id, CONCAT(e.nombre, ' ', e.apellido) AS nombre_completo
                                FROM empleados e
                                INNER JOIN empleados_servicio es ON e.empleado_id = es.empleado_id
                                WHERE e.cod_empresa = $cod_empresa
                                AND es.servicio_id = $servicio_id 
                                AND es.centro_costo_id = $centro_costo_id 
                                AND es.grupo_id = $grupo_id 
                                AND es.estado = 'Activo'");
                                //AND e.empleado_id not in($empleado_id)");
    //echo $empleados;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
        $id_empleado_reemplaza = $_POST['id_empleado_reemplaza'];
        $fecha_ini_reemplazo = $_POST['fecha_ini_reemplazo'];
        $fecha_fin_reemplazo = $_POST['fecha_fin_reemplazo'];        
        $estado = "Pendiente";
    
        $sql = "INSERT INTO reemplazo (id_empleado_reemplaza, id_empleado_reemplazado, fecha_ini_reemplazo, fecha_fin_reemplazo, usu_aprueba, fec_aprueba, cod_empresa, servicio_id, estado, fec_rechazo, fec_solicitud)
                VALUES ($id_empleado_reemplaza, $empleado_id, '$fecha_ini_reemplazo', '$fecha_fin_reemplazo', null, null, $cod_empresa, $servicio_id, '$estado', null, NOW())";

        if ($conn->query($sql) === TRUE) {
            echo "Solicitud de reemplazo registrada exitosamente";
            header("Location: form_reemplazos_empleado.php");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear solicitud de Reemplazo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2 class="mt-4">Crear Solicitud de Reemplazo</h2>
    <form method="POST" action="create_reemplazo.php">
        <div class="mb-3">
            <label>Seleccione Empleado que va a ocupar el cargo</label>
            <select name="id_empleado_reemplaza" class="form-control" required>
                <?php while ($row = $empleados->fetch_assoc()): ?>
                    <option value="<?= $row['empleado_id'] ?>"><?= $row['nombre_completo'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Fecha Inicio Reemplazo</label>
            <input type="date" name="fecha_ini_reemplazo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Fecha Fin Reemplazo</label>
            <input type="date" name="fecha_fin_reemplazo" class="form-control" required>
        </div>

        <button type="submit" name="guardar" class="btn btn-primary">Guardar</button>
        <a href="form_reemplazos_empleado.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
