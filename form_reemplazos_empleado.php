<?php
    include 'menu_ppal_emp.php';
?>
<?php
 include("session.php");
 include 'db.php';
 
 $empleado_id = $_SESSION['empleado_id'];

/* $search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
} */

// Consulta con búsqueda
$sql = "SELECT r.*,  CONCAT(e1.nombre, ' ', e1.apellido)  AS reemplaza_nombre,  CONCAT(e2.nombre, ' ', e2.apellido)  AS reemplazado_nombre, s.nombre AS servicio_nombre, emp.nom_empresa
        FROM reemplazo r
        LEFT JOIN empleados e1 ON r.id_empleado_reemplaza = e1.empleado_id
        LEFT JOIN empleados e2 ON r.id_empleado_reemplazado = e2.empleado_id
        LEFT JOIN servicio s ON r.servicio_id = s.servicio_id
        LEFT JOIN empresas emp ON r.cod_empresa = emp.cod_empresa
        WHERE  e2.empleado_id =  $empleado_id";
       // echo $sql;
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Reemplazos Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2 class="mt-4">Historial Solicitudes de Reemplazos</h2>

    <!-- <form method="GET" action="index.php" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar por empleado o empresa" value="<?= $search ?>">
            <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
    </form> -->
    <a href="create_reemplazo.php" class="btn btn-success">Agregar Nuevo Reemplazo</a>
    <br><br>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Empleado que Reemplaza</th>
            <th>Empleado Reemplazado</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Usuario Aprueba</th>
            <th>Fecha Aprobación</th>
            <th>Servicio</th>
            <th>Estado</th>
            <th>Fecha Rechazo</th>
            <th>Fecha Solicitud</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id_reemplazo'] ?></td>
                <td><?= $row['reemplaza_nombre'] ?></td>
                <td><?= $row['reemplazado_nombre'] ?></td>
                <td><?= $row['fecha_ini_reemplazo'] ?></td>
                <td><?= $row['fecha_fin_reemplazo'] ?></td>
                <td><?= $row['usu_aprueba'] ?></td>
                <td><?= $row['fec_aprueba'] ?></td>
                <td><?= $row['servicio_nombre'] ?></td>
                <td><?= $row['estado'] ?></td>
                <td><?= $row['fec_rechazo'] ?></td>
                <td><?= $row['fec_solicitud'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
