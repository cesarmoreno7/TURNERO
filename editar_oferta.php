<?php
    // editar_oferta.php
    
    include 'db.php';
    
    // Obtener el ID del registro a editar
    $id = $_GET['id'];
    
    // Consultar los datos actuales del registro
    $sql = "SELECT * FROM oferta_turnos WHERE id = ? AND cod_empresa = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id,$cod_empresa);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // Verificar si se ha enviado el formulario de edición
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $empleado_id_solicitante = $_POST['empleado_id_solicitante'];
        $empleado_id_receptor = $_POST['empleado_id_receptor'];
        $fecha_turno = $_POST['fecha_turno'];
        $estado_oferta = $_POST['estado_oferta'];
        $estado_solicitud = $_POST['estado_solicitud'];
        $id_turno_original = $_POST['id_turno_original'];
        $id_turno_nuevo = $_POST['id_turno_nuevo'];
        $fecha_oferta = $_POST['fecha_oferta'];
        $fecha_aprobacion = $_POST['fecha_aprobacion'];
        $cod_usu_aprueba = $_POST['cod_usu_aprueba'];
    
        // Actualizar los datos del registro
        $sql_update = "UPDATE oferta_turnos SET estado_oferta = ?, estado_solicitud = ?, fecha_aprobacion = ? WHERE id = ? AND cod_empresa = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("isssiisssi",  $estado_oferta, $estado_solicitud, $fecha_aprobacion, $id, $cod_empresa);
    
        if ($stmt_update->execute()) {
            echo "Registro actualizado correctamente";
        } else {
            echo "Error: " . $stmt_update->error;
        }
    
        // Cerrar conexión
        $stmt_update->close();
        $conn->close();
        
        // Redirigir de vuelta a la página de consulta
        header("Location: form_aprobar_rechazar_ofertas.php");
        exit();
    }
?>

<?php
    include("session.php");
    $cod_empresa = $_SESSION['cod_empresa'];
    $codigo_usu  = $_SESSION['codigo_usu'];
?>
    
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Editar Oferta de Turno</title>
        <!-- Incluir el CSS de Bootstrap -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <h2 class="mb-4">Editar Oferta de Turno</h2>
            <form action="editar_oferta.php?id=<?php echo $id; ?>" method="post">
                <div class="form-group">
                    <label for="empleado_id_solicitante">Empleado ID Solicitante:</label>
                    <input type="number" class="form-control" name="empleado_id_solicitante" id="empleado_id_solicitante" value="<?php echo htmlspecialchars($row['empleado_id_solicitante']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="empleado_id_receptor">Empleado ID Receptor:</label>
                    <input type="number" class="form-control" name="empleado_id_receptor" id="empleado_id_receptor" value="<?php echo htmlspecialchars($row['empleado_id_receptor']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="fecha_turno">Fecha Turno:</label>
                    <input type="date" class="form-control" name="fecha_turno" id="fecha_turno" value="<?php echo htmlspecialchars($row['fecha_turno']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="estado_oferta">Estado Oferta:</label>
                    <input type="text" class="form-control" name="estado_oferta" id="estado_oferta" value="<?php echo htmlspecialchars($row['estado_oferta']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="estado_solicitud">Estado Solicitud:</label>
                    <input type="text" class="form-control" name="estado_solicitud" id="estado_solicitud" value="<?php echo htmlspecialchars($row['estado_solicitud']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="id_turno_original">ID Turno Original:</label>
                    <input type="number" class="form-control" name="id_turno_original" id="id_turno_original" value="<?php echo htmlspecialchars($row['id_turno_original']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="id_turno_nuevo">ID Turno Nuevo:</label>
                    <input type="number" class="form-control" name="id_turno_nuevo" id="id_turno_nuevo" value="<?php echo htmlspecialchars($row['id_turno_nuevo']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="fecha_oferta">Fecha Oferta:</label>
                    <input type="date" class="form-control" name="fecha_oferta" id="fecha_oferta" value="<?php echo htmlspecialchars($row['fecha_oferta']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="fecha_aprobacion">Fecha Aprobación:</label>
                    <input type="date" class="form-control" name="fecha_aprobacion" id="fecha_aprobacion" value="<?php echo htmlspecialchars($row['fecha_aprobacion']); ?>">
                </div>
                <div class="form-group">
                    <label for="cod_usu_aprueba">Cod Usu Aprueba:</label>
                    <input type="number" class="form-control" name="cod_usu_aprueba" id="cod_usu_aprueba" value="<?php echo htmlspecialchars($row['cod_usu_aprueba']); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </form>
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
