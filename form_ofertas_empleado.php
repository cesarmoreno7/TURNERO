<?php

    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit();
    }
    // consulta_ofertas.php
    
    // Conexión a la base de datos
    $conn = new mysqli('localhost', 'prodsoft_turnero', 'turnero2024', 'prodsoft_turnero');
    
    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    
    // Obtener datos del formulario
    $empleado_id_solicitante = 1; // Cambiar por el valor adecuado
    
    // Consultar la tabla "oferta_turnos"
    $sql = "SELECT * FROM oferta_turnos WHERE empleado_id_solicitante = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $empleado_id_solicitante);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $PTS_valor_OFE = obtenerValorPorLlave('OFE');
    $PTS_estado_OFE = obtenerEstadoPorLlave('OFE');
    
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Consulta de Ofertas de Turnos</title>
    <!-- Incluir el CSS de Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Ofertas de Turnos</h2>
        <?php
            if ($PTS_valor_OFE === 'S' && $PTS_estado_OFE == 1) {
            echo '<a href="form_ofertar_turno.php" class="btn btn-success ml-2">Crear Nueva Oferta</a>';
        ?>
        <br><br>
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
