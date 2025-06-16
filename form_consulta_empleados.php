<?php
    include 'menu_ppal_maestros.php';
?>
<?php

    include 'db.php';
    
    $conn->set_charset('utf8');
    
    $empleado_id = $_SESSION['empleado_id'];
    $tipo_usu    = $_SESSION['user_type'];
    $cod_empresa =  $_SESSION['cod_empresa'];
    
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    // Consulta para obtener los empleados con las descripciones de las claves foráneas
    if ($tipo_usu === 'Admin'){
         $sql = "
        SELECT e.*, 
               ti.desc_tipo_id, 
               pn.nom_pais AS pais_nacimiento, 
               dn.nom_departamento AS depto_nacimiento, 
               mn.nom_municipio AS mpio_nacimiento,
               pe.nom_pais AS pais_exp, 
               de.nom_departamento AS depto_exp, 
               me.nom_municipio AS mpio_exp,
               emp.nom_empresa
        FROM empleados e
        LEFT JOIN tipo_identificacion ti ON e.cod_tipo_id = ti.cod_tipo_id
        LEFT JOIN pais pn ON e.cod_pais_nac = pn.cod_pais
        LEFT JOIN departamento dn ON e.cod_depto_nac = dn.cod_departamento
        LEFT JOIN municipio mn ON e.cod_mpio_nac = mn.cod_municipio
        LEFT JOIN pais pe ON e.cod_pais_exp = pe.cod_pais
        LEFT JOIN departamento de ON e.cod_depto_exp = de.cod_departamento
        LEFT JOIN municipio me ON e.cod_mpio_exp = me.cod_municipio
        LEFT JOIN empresas emp ON e.cod_empresa = emp.cod_empresa
        WHERE e.cod_empresa = $cod_empresa AND e.nombre LIKE '%$search%' OR e.apellido LIKE '%$search%'
        ORDER BY e.empleado_id";
    }else{
         $sql = "
        SELECT e.*, 
               ti.desc_tipo_id, 
               pn.nom_pais AS pais_nacimiento, 
               dn.nom_departamento AS depto_nacimiento, 
               mn.nom_municipio AS mpio_nacimiento,
               pe.nom_pais AS pais_exp, 
               de.nom_departamento AS depto_exp, 
               me.nom_municipio AS mpio_exp,
               emp.nom_empresa
        FROM empleados e
        LEFT JOIN tipo_identificacion ti ON e.cod_tipo_id = ti.cod_tipo_id
        LEFT JOIN pais pn ON e.cod_pais_nac = pn.cod_pais
        LEFT JOIN departamento dn ON e.cod_depto_nac = dn.cod_departamento
        LEFT JOIN municipio mn ON e.cod_mpio_nac = mn.cod_municipio
        LEFT JOIN pais pe ON e.cod_pais_exp = pe.cod_pais
        LEFT JOIN departamento de ON e.cod_depto_exp = de.cod_departamento
        LEFT JOIN municipio me ON e.cod_mpio_exp = me.cod_municipio
        LEFT JOIN empresas emp ON e.cod_empresa = emp.cod_empresa
        WHERE e.cod_empresa = $cod_empresa AND e.empleado_id = $empleado_id";
    }
    
    //echo $sql;
    
   $result = $conn->query($sql);
 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Empleados</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Registros de Empleados</h2>
    <form action="form_consulta_empleados.php" method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2 w-50" placeholder="Buscar por Nombre, Apellido o Nro. de identificaci&oacute;n" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>
    <a href="create_empleado.php" class="btn btn-success mb-3">Nuevo Empleado</a>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Tipo ID</th>
            <th>Nro ID</th>
            <th>Fecha Nac.</th>
            <th>Pais Nac.</th>
            <th>Depto Nac.</th>
            <th>Mpio Nac.</th>
            <th>Fecha Exp.</th>
            <th>Pais Exp.</th>
            <th>Depto Exp.</th>
            <th>Mpio Exp.</th>
            <th>Correo</th>
            <th>Tel. Fijo</th>
            <th>Celular</th>
            <th>Estado Civil</th>
            <th>Empresa</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['empleado_id']; ?></td>
                <td><?php echo $row['nombre']; ?></td>
                <td><?php echo $row['apellido']; ?></td>
                <td><?php echo $row['desc_tipo_id']; ?></td>
                <td><?php echo $row['nro_id']; ?></td>
                <td><?php echo $row['fec_nac']; ?></td>
                <td><?php echo $row['pais_nacimiento']; ?></td>
                <td><?php echo $row['depto_nacimiento']; ?></td>
                <td><?php echo $row['mpio_nacimiento']; ?></td>
                <td><?php echo $row['fecha_exp']; ?></td>
                <td><?php echo $row['pais_exp']; ?></td>
                <td><?php echo $row['depto_exp']; ?></td>
                <td><?php echo $row['mpio_exp']; ?></td>
                <td><?php echo $row['correo']; ?></td>
                <td><?php echo $row['tel_fijo']; ?></td>
                <td><?php echo $row['celular']; ?></td>
                <td><?php echo $row['est_civil']; ?></td>
                <td><?php echo $row['nom_empresa']; ?></td>
                <td>
                    <a href="update_empleado.php?id=<?php echo $row['empleado_id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
