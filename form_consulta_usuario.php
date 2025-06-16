<?php
    include 'menu_ppal_maestros.php';
?>
<?php

    include("session.php");
    include 'db.php';
    
    $conn->set_charset('utf8');
   
    $cod_empresa = $_SESSION['cod_empresa'];

    // Obtener valores del formulario de búsqueda si existen
    $codigo_usu = isset($_GET['codigo_usu']) ? $_GET['codigo_usu'] : '';
    $nombre_empleado = isset($_GET['nombre_empleado']) ? $_GET['nombre_empleado'] : '';
    $mail_usu = isset($_GET['mail_usu']) ? $_GET['mail_usu'] : '';

    // Modificar la consulta SQL para agregar los filtros de búsqueda
    $sql = "SELECT u.*, CONCAT(e.nombre, ' ', e.apellido) AS nombre_empleado 
            FROM usuarios u 
            JOIN empleados e ON u.empleado_id = e.empleado_id 
            WHERE u.cod_empresa = $cod_empresa";

    if (!empty($codigo_usu)) {
        $sql .= " AND u.codigo_usu LIKE '%$codigo_usu%'";
    }

    if (!empty($nombre_empleado)) {
        $sql .= " AND (e.nombre LIKE '%$nombre_empleado%' OR e.apellido LIKE '%$nombre_empleado%')";
    }

    if (!empty($mail_usu)) {
        $sql .= " AND u.mail_usu LIKE '%$mail_usu%'";
    }

    // Ejecutar la consulta
    $usuarios = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD de Usuarios</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Usuarios</h2>
    
    <!-- Formulario de búsqueda -->
    <form method="GET" action="" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="codigo_usu" class="form-control" placeholder="Buscar por Código" value="<?= htmlspecialchars($codigo_usu) ?>">
            </div>
            <div class="col-md-3">
                <input type="text" name="nombre_empleado" class="form-control" placeholder="Buscar por Empleado" value="<?= htmlspecialchars($nombre_empleado) ?>">
            </div>
            <div class="col-md-3">
                <input type="text" name="mail_usu" class="form-control" placeholder="Buscar por Correo" value="<?= htmlspecialchars($mail_usu) ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-block">Buscar</button>
            </div>
        </div>
    </form>
    
    <a href="form_registro_usuario.php" class="btn btn-success mb-3">Registrar Nuevo Usuario</a>

    <!-- Tabla de usuarios -->
    <table class="table mt-4">
        <thead>
        <tr>
            <th>Código</th>
            <th>Empleado</th>
            <th>Estado</th>
            <th>Tipo</th>
            <th>Correo</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= $usuario['codigo_usu'] ?></td>
                <td><?= $usuario['nombre_empleado'] ?></td>
                <td><?= $usuario['estado_usu'] ?></td>
                <td><?= $usuario['tipo_usu'] ?></td>
                <td><?= $usuario['mail_usu'] ?></td>
                <td>
                    <a href="form_editar_usuario.php?codigo_usu=<?= $usuario['codigo_usu'] ?>" class="btn btn-sm btn-warning">Editar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
