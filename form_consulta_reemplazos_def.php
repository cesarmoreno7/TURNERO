<?php
    include 'menu_ppal_maestros.php';
?>
<?php
    // Conexión a la base de datos
    include("session.php");
    include("db.php");
    $conn->set_charset("utf8"); //Para grantizar las tildes;

    $cod_empresa = $_SESSION['cod_empresa'];
    $codigo_usu = $_SESSION['codigo_usu'];

    // Obtener el empleado_id y los datos de servicio del usuario actual
    $usuarioQuery = "SELECT e.empleado_id, es.servicio_id, es.centro_costo_id, es.grupo_id 
                    FROM usuarios u
                    JOIN empleados_servicio es ON u.empleado_id = es.empleado_id
                    JOIN empleados e ON e.empleado_id = es.empleado_id
                    WHERE u.codigo_usu = '$codigo_usu' AND es.estado = 'Activo' AND u.cod_empresa = $cod_empresa";
    $usuarioResult = $conn->query($usuarioQuery);

    if ($usuarioResult->num_rows == 0) {
        echo "<script>alert('Usuario no encontrado o sin servicios asignados activos.'); window.location.href = 'index.php';</script>";
        exit;
    }

    $usuarioData = $usuarioResult->fetch_assoc();
    $empleado_id = $usuarioData['empleado_id'];
    $servicio_id_usuario = $usuarioData['servicio_id'];

    // Filtrar las listas desplegables 
     $empleados_reemplaza = $conn->query("SELECT DISTINCT e.empleado_id, CONCAT(e.nombre, ' ', e.apellido) AS nombre_completo
    FROM empleados_servicio es
    JOIN empleados e ON es.empleado_id = e.empleado_id
    WHERE es.servicio_id = $servicio_id_usuario AND es.estado = 'Activo' AND es.cod_empresa = $cod_empresa");
    
    $empleados_reemplazado = $conn->query("SELECT DISTINCT e.empleado_id, CONCAT(e.nombre, ' ', e.apellido) AS nombre_completo
    FROM empleados_servicio es
    JOIN empleados e ON es.empleado_id = e.empleado_id
    WHERE es.servicio_id = $servicio_id_usuario AND es.estado = 'Activo' AND es.cod_empresa = $cod_empresa");
    
    
    $servicios = $conn->query("SELECT DISTINCT es.servicio_id, serv.nombre
                               FROM empleados_servicio es INNER JOIN servicio serv ON(es.servicio_id = serv.servicio_id)
                               WHERE es.servicio_id = $servicio_id_usuario AND es.estado = 'Activo' AND es.cod_empresa = $cod_empresa");

    // Crear la consulta base
    $sql = "SELECT * FROM reemplazo WHERE ";

    if (isset($_GET['buscar'])) {

        // Agregar condiciones dinámicas a la consulta
        if (!empty($id_empleado_reemplaza)) {
            $sql .= " cod_empresa = $cod_empresa AND servicio_id = $servicio_id_usuario AND id_empleado_reemplaza = " . $conn->real_escape_string($id_empleado_reemplaza);
        }

        if (!empty($id_empleado_reemplazado)) {
            $sql .= " cod_empresa = $cod_empresa AND servicio_id = $servicio_id_usuario AND id_empleado_reemplazado = " . $conn->real_escape_string($id_empleado_reemplazado);
        }
        if (!empty($servicio_id)) {
            $sql .= " cod_empresa = $cod_empresa AND servicio_id = " . $conn->real_escape_string($servicio_id);
        }
    }

    // Ejecutar la consulta
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Reemplazos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Consultar Reemplazos</h2>
        
        <!-- Formulario de búsqueda -->
        <form method="GET" action="form_consulta_reemplazos_def.php">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="id_empleado_reemplaza">Empleado que Reemplaza</label>
                    <select name="id_empleado_reemplaza" id="id_empleado_reemplaza" class="form-control" required>
                        <option value="">Seleccione un empleado</option>
                        <?php while ($row = $empleados_reemplaza->fetch_assoc()): ?>
                            <option value="<?php echo $row['empleado_id']; ?>" <?php echo (isset($filtros['empleado_id']) && $filtros['empleado_id'] == $row['empleado_id']) ? 'selected' : ''; ?>>
                                <?php echo $row['nombre_completo']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-12">
                    <label for="id_empleado_reemplazado">Empleado Reemplazado</label>
                    <select name="id_empleado_reemplazado" id="id_empleado_reemplazado" class="form-control" required>
                        <option value="">Seleccione un empleado</option>
                        <?php while ($row = $empleados_reemplazado->fetch_assoc()): ?>
                            <option value="<?php echo $row['empleado_id']; ?>" <?php echo (isset($filtros['empleado_id']) && $filtros['empleado_id'] == $row['empleado_id']) ? 'selected' : ''; ?>>
                                <?php echo $row['nombre_completo']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>               
                <div class="form-group col-md-12">
                <label for="servicio_id">Servicio</label>
                <select name="servicio_id" id="servicio_id" class="form-control">
                    <option value="">Seleccione un servicio</option>
                    <?php while ($row = $servicios->fetch_assoc()): ?>
                        <option value="<?php echo $row['servicio_id']; ?>" <?php echo (isset($filtros['servicio_id']) && $filtros['servicio_id'] == $row['servicio_id']) ? 'selected' : ''; ?>>
                            <?php echo $row['nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>        
            </div>
            <button type="submit"  name="buscar" class="btn btn-primary">Buscar</button>
        </form>

        <!-- Resultados de la búsqueda -->
        <div class="mt-5">
            <h2>Resultados de la Búsqueda</h2>
            <table id="tablaReemplazoTurnoDef" class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID Reemplazo</th>
                        <th>Empleado que Reemplaza</th>
                        <th>Empleado Reemplazado</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Usuario que Aprueba</th>
                        <th>Fecha Aprobación</th>
                        <th>Empresa</th>
                        <th>Servicio</th>
                        <th>Estado</th>
                        <th>Fecha Rechazo</th>
                        <th>Fecha Solicitud</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Mostrar los resultados
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id_reemplazo'] . "</td>";
                            echo "<td>" . $row['id_empleado_reemplaza'] . "</td>";
                            echo "<td>" . $row['id_empleado_reemplazado'] . "</td>";
                            echo "<td>" . $row['fecha_ini_reemplazo'] . "</td>";
                            echo "<td>" . $row['fecha_fin_reemplazo'] . "</td>";
                            echo "<td>" . $row['usu_aprueba'] . "</td>";
                            echo "<td>" . $row['fec_aprueba'] . "</td>";
                            echo "<td>" . $row['cod_empresa'] . "</td>";
                            echo "<td>" . $row['servicio_id'] . "</td>";
                            echo "<td>" . $row['estado'] . "</td>";
                            echo "<td>" . $row['fec_rechazo'] . "</td>";
                            echo "<td>" . $row['fec_solicitud'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='12'>No se encontraron resultados.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    // Cerrar la conexión
    $conn->close();
    ?>

    <script>
        $(document).ready(function() {
            $('#tablaReemplazoTurnoDef').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Exportar a Excel',
                        title: 'Reemplazos de Turnos Definitivos'
                    },
                    {
                        extend: 'print',
                        text: 'Imprimir'
                    }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.11.3/i18n/es_es.json"
                }
            });
        });        
    </script>

</body>
</html>
