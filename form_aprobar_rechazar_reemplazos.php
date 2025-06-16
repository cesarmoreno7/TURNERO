<?php
    include 'menu_ppal_maestros.php';
?>
<?php
    include("session.php");
    include 'db.php';
    
    $empleado_id = $_SESSION['empleado_id'];
    $codigo_usu  = $_SESSION['codigo_usu'];
    $servicio_id = $_SESSION['servicio_id'];

    // Obtener el estado seleccionado del formulario
    $estado = isset($_POST['estado']) ? $_POST['estado'] : 'Pendiente';
    /* $search = '';
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
    } */

    // Consulta con b·Ñsqueda
    $sql = "SELECT r.*, e1.nombre AS reemplaza_nombre, e2.nombre AS reemplazado_nombre, s.nombre AS servicio_nombre, emp.nom_empresa
            FROM reemplazo r
            LEFT JOIN empleados e1 ON r.id_empleado_reemplaza = e1.empleado_id
            LEFT JOIN empleados e2 ON r.id_empleado_reemplazado = e2.empleado_id
            LEFT JOIN servicio s ON r.servicio_id = s.servicio_id
            LEFT JOIN empresas emp ON r.cod_empresa = emp.cod_empresa
            WHERE  r.estado = '$estado'
            AND r.servicio_id = $servicio_id
            AND r.cod_empresa = $cod_empresa";
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Solicitudes de Reemplazos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2 class="mt-4">Administrar Solicitudes de Reemplazos</h2>
    <!-- Formulario para filtrar por estado -->
    <form method="POST" action="form_aprobar_rechazar_reemplazos.php" class="mb-3">
        <div class="form-group">
            <label for="estado">Filtrar por Estado:</label>
            <select id="estado" name="estado" class="form-control" required>
                <option value="">Seleccione un Estado</option>
                <option value="Pendiente">Pendiente</option>
                <option value="Aprobado">Aprobado</option>
                <option value="Rechazado">Rechazado</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Consultar</button>
    </form>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Empleado que Reemplaza</th>
            <th>Empleado Reemplazado</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>   
            <th>Servicio</th>         
            <th>Fecha Aprobaci&oacute;n</th>
            <th>Fecha Rechazo</th>
            <th>Estado</th>
            <th>Observaciones</th>
            <th>Acciones</th>
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
                <td><?= $row['servicio_nombre'] ?></td>              
                <td><?= $row['fec_aprueba'] ?></td>
                <td><?= $row['fec_rechazo'] ?></td>
                <td><?= $row['estado'] ?></td>
                <td>
                    <!-- Campo para agregar observaciones -->
                    <textarea name="observaciones_<?= $row['id_reemplazo'] ?>" class="form-control" rows="2"></textarea>
                </td>
                <td>
                    <form action="aprobar_rechazar_reemplazos.php" method="POST" class="form-inline">
                        <input type="hidden" name="id_reemplazo" value="<?= $row['id_reemplazo'] ?>">
                        <input type="hidden" name="observaciones" value="" class="observaciones-input">
                        <button type="submit" name="aprobar" class="btn btn-success btn-sm mr-2">Aprobar</button>
                        <button type="submit" name="rechazar" class="btn btn-danger btn-sm">Rechazar</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
// Asignar observaciones al formulario antes de enviarlo
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const observacionesField = form.querySelector('.observaciones-input');
        const idReemplazo = form.querySelector('input[name="id_reemplazo"]').value;
        const observacionesText = document.querySelector(`textarea[name="observaciones_${idReemplazo}"]`).value;
        observacionesField.value = observacionesText;
    });
});
</script>

</body>
</html>
