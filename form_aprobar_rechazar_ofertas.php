<?php

 //include("session.php");
    include 'db.php';
    $cod_empresa = $_SESSION['cod_empresa'];
    $codigo_usu  = $_SESSION['codigo_usu'];
   
    
    // Obtener datos del formulario
    $empleado_id_solicitante = 1; // Cambiar por el valor adecuado
    $estado_oferta = $_GET['estado_oferta'];
    
    // Consultar la tabla "oferta_turnos"
    $sql = "SELECT * FROM oferta_turnos WHERE estado_oferta = ? and cod_usu_aprueba = ?  AND cod_empresa = ? order by fecha_oferta DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $estado_oferta,$cod_usu_aprueba,$cod_empresa);
    $stmt->execute();
    $result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Consulta de Ofertas de Turnos</title>
    <!-- Incluir el CSS de Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Ofertas de Turnos</h2>
        <form method="get" class="mb-4">
            <div class="form-group">
                <label for="estado_oferta">Estado Oferta:</label>
                <select class="form-control" id="estado_oferta" name="estado_oferta">
                    <option value="">Todos</option>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Aprobada">Aprobada</option>
                    <option value="Rechazada">Rechazada</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </form>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Empleado ID Solicitante</th>
                    <th>Empleado ID Receptor</th>
                    <th>Fecha Turno</th>
                    <th>Estado Oferta</th>
                    <th>Estado Solicitud</th>
                    <th>ID Turno Original</th>
                    <th>ID Turno Nuevo</th>
                    <th>Fecha Oferta</th>
                    <th>Fecha Aprobación</th>
                    <th>Cod Usu Aprueba</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['empleado_id_solicitante']); ?></td>
                        <td><?php echo htmlspecialchars($row['empleado_id_receptor']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_turno']); ?></td>
                        <td><?php echo htmlspecialchars($row['estado_oferta']); ?></td>
                        <td><?php echo htmlspecialchars($row['estado_solicitud']); ?></td>
                        <td><?php echo htmlspecialchars($row['id_turno_original']); ?></td>
                        <td><?php echo htmlspecialchars($row['id_turno_nuevo']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_oferta']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_aprobacion']); ?></td>
                        <td><?php echo htmlspecialchars($row['cod_usu_aprueba']); ?></td>
                        <td>
                            <a href="editar_oferta.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Incluir el JavaScript de Bootstrap y sus dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Cerrar conexión
$stmt->close();
$conn->close();
?>
