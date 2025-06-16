<?php
    include 'menu_ppal_maestros.php';
?>
<?php

    include("session.php");
    include 'db.php';
    $conn->set_charset("utf8"); //Para grantizar las tildes
    
    $cod_empresa =  $_SESSION['cod_empresa'];

    $CC_estado = obtenerEstadoPorLlave('CCOSTO',$cod_empresa);
    $CC_valor = obtenerValorPorLlave('CCOSTO',$cod_empresa);
    
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    // Consulta para obtener los registros de personal con las descripciones de las claves foráneas
    $sql = "
        SELECT p.*, 
               e.nombre, e.apellido, 
               c.desc_cargo, 
               r.desc_rol, 
               cc.nombre AS centro_costo, 
               tc.desc_tipo_contrato, 
               em.nom_empresa
        FROM personal p
        LEFT JOIN empleados e ON p.empleado_id = e.empleado_id
        LEFT JOIN cargo c ON p.cod_cargo = c.cod_cargo
        LEFT JOIN rol r ON p.cod_rol = r.cod_rol
        LEFT JOIN centro_costo cc ON p.centro_costo_id = cc.centro_costo_id
        LEFT JOIN tipo_contrato tc ON p.cod_tipo_contrato = tc.cod_tipo_contrato
        LEFT JOIN empresas em ON p.cod_empresa = em.cod_empresa
        WHERE em.cod_empresa = $cod_empresa  AND e.nombre LIKE '%$search%' OR e.apellido LIKE '%$search%' OR c.desc_cargo LIKE '%$search%'
        ORDER BY p.id_personal";
    
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Personal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Listado de Personal</h2>
    <form action="index.php" method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2 w-50" placeholder="Buscar por Nombre, Apellido o Cargo" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>
    <a href="create_personal.php" class="btn btn-success mb-3">Nuevo Ingreso</a>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Empleado</th>
            <th>Cargo</th>
            <th>Rol</th>
             <?php
                 if ($CC_estado == 1 &&  $CC_valor == "S"){
                    echo '<th>Centro de Costo</th>';
                 }
            ?>
            <th>Tipo de Contrato</th>
            <th>Fecha Ingreso</th>
            <th>Antigüedad</th>
            <th>Horas</th>
            <th>Tiempo Contratado</th>
            <th>Fecha Retiro</th>
            <th>Motivo Retiro</th>
            <th>Empresa</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id_personal']; ?></td>
                <td><?php echo $row['nombre'] . ' ' . $row['apellido']; ?></td>
                <td><?php echo $row['desc_cargo']; ?></td>
                <td><?php echo $row['desc_rol']; ?></td>
                <?php
                    if ($CC_estado == 1 &&  $CC_valor == "S"){ ?>
                        <td><?php echo $row['centro_costo']; ?></td>
                <?php } ?>
                <td><?php echo $row['desc_tipo_contrato']; ?></td>
                <td><?php echo $row['fecha_ingreso']; ?></td>
                <td><?php echo $row['antiguedad']; ?></td>
                <td><?php echo $row['horas']; ?></td>
                <td><?php echo $row['tiempo_contratado']; ?></td>
                <td><?php echo $row['fecha_retiro']; ?></td>
                <td><?php echo $row['motivo_retiro']; ?></td>
                <td><?php echo $row['nom_empresa']; ?></td>
                <td>
                    <a href="update_personal.php?id=<?php echo $row['id_personal']; ?>" class="btn btn-warning btn-sm">Editar</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
