<?php
    include 'menu_ppal_maestros.php';
?>
<?php

    include 'db.php';
    $conn->set_charset("utf8"); //Para grantizar las tildes
    
    $servicio_id = $_SESSION['servicio_id'];
    $tipo_usu    = $_SESSION['user_type'];
    $cod_empresa =  $_SESSION['cod_empresa'];
    $empleado_id = $_SESSION['empleado_id'];
    $centro_costo_id = $_SESSION['centro_costo_id'];
    $grupo_id = $_SESSION['grupo_id'];
    
    $CC_estado = obtenerEstadoPorLlave('CCOSTO',$cod_empresa);
    $CC_valor = obtenerValorPorLlave('CCOSTO',$cod_empresa);
    
    $ACT_estado = obtenerEstadoPorLlave('ACT',$cod_empresa);
    $ACT_valor = obtenerValorPorLlave('ACT',$cod_empresa);
    
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    // Consulta para obtener los registros de empleados_servicio con las descripciones de las claves foráneas
    if ($tipo_usu === 'EmpleadoAdmin' && strpos((string)$ACT_valor, (string)$empleado_id) >= 0 && $ACT_estado == 1) {
        $sql = "
            SELECT es.*, 
                   e.nombre AS empleado_nombre, e.apellido AS empleado_apellido, 
                   s.nombre AS servicio_nombre, 
                   cc.nombre AS centro_costo_nombre, 
                   g.nombre AS grupo_nombre
            FROM empleados_servicio es
            INNER JOIN empleados e ON es.empleado_id = e.empleado_id
            INNER JOIN servicio s ON es.servicio_id = s.servicio_id
            INNER JOIN centro_costo cc ON es.centro_costo_id = cc.centro_costo_id
            INNER JOIN grupo g ON es.grupo_id = g.grupo_id
            WHERE es.cod_empresa = $cod_empresa AND es.servicio_id = $servicio_id AND es.centro_costo_id = $centro_costo_id AND es.grupo_id = $grupo_id 
            ORDER BY es.empleado_id";
            //echo  $sql;
    }else{//consulta como admin
        $sql = "
            SELECT es.*, 
                   e.nombre AS empleado_nombre, e.apellido AS empleado_apellido, 
                   s.nombre AS servicio_nombre, 
                   cc.nombre AS centro_costo_nombre, 
                   g.nombre AS grupo_nombre
            FROM empleados_servicio es
            INNER JOIN empleados e ON es.empleado_id = e.empleado_id
            INNER JOIN servicio s ON es.servicio_id = s.servicio_id
            INNER JOIN centro_costo cc ON es.centro_costo_id = cc.centro_costo_id
            INNER JOIN grupo g ON es.grupo_id = g.grupo_id
            WHERE es.cod_empresa = $cod_empresa 
            AND e.nombre LIKE '%$search%' OR e.apellido LIKE '%$search%' OR s.nombre LIKE '%$search%' 
            ORDER BY es.empleado_id";
    }
    
    $result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Empleados Servicio</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Listado de Empleados por Servicio</h2>
    <?php if ($tipo_usu === 'Admin') { ?>
        <form action="form_consulta_empleados_servicio.php" method="GET" class="form-inline mb-3">
            <input type="text" name="search" class="form-control mr-3" placeholder="Buscar por Nombre, Apellido o Servicio" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>
    <?php } ?>
    
    <?php   if ($CC_estado == 1 &&  $CC_valor == "S"){
        echo '<a href="create_empleado_servicio.php" class="btn btn-success mb-3">Nuevo Registro</a>';
     }else{
        echo '<a href="create_empleado_servicio_grupo.php" class="btn btn-success mb-3">Nuevo Registro</a>';
    } ?>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID Empleado</th>
            <th>Empleado</th>
            <th>Servicio</th>
            <?php if ($CC_estado == 1 && $CC_valor == "S") {?>
                <th>Centro de Costo</th>
            <?php } ?>
            <th>Grupo</th>
            <th>Estado</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['empleado_id']; ?></td>
                <td><?php echo $row['empleado_nombre'] . ' ' . $row['empleado_apellido']; ?></td>
                <td><?php echo $row['servicio_nombre']; ?></td>
                <?php if ($CC_estado == 1 && $CC_valor == "S") {?>
                    <td><?php echo $row['centro_costo_nombre']; ?></td>
                <?php } ?>
                <td><?php echo $row['grupo_nombre']; ?></td>
                <td><?php echo $row['estado']; ?></td>
                <td><?php echo $row['fec_ini']; ?></td>
                <td><?php echo $row['fec_fin']; ?></td>
                <td>
                <?php
                    if ($CC_estado == 1 && $CC_valor == "S") {
                        ?>
                        <a href="update_empleado_servicio.php?empleado_id=<?php echo $row['empleado_id']; ?>&servicio_id=<?php echo $row['servicio_id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <?php
                    } else {
                        ?>
                        <a href="update_empleado_servicio_grupo.php?empleado_id=<?php echo $row['empleado_id']; ?>&servicio_id=<?php echo $row['servicio_id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <?php
                    }
                ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
