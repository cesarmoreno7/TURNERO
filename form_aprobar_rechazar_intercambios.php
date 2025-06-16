<?php
    include 'menu_ppal_maestros.php';
?>
<?php
    include("session.php");
    include 'db.php';
    $cod_empresa = $_SESSION['cod_empresa'];
    $codigo_usu  = $_SESSION['codigo_usu'];
    $servicio_id = $_SESSION['servicio_id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
     <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin. Solicitudes de Intercambios</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Administrar Solicitudes de Intercambio de turnos</h2>
        <!-- Formulario para filtrar por estado -->
        <form method="POST" action="" class="mb-3">
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
                    <th>Id. Intercambio</th>
                    <th>Id. Solicitante</th>
                    <th>Nombres y Apellidos</th>
                    <th>Id. Receptor</th>
                    <th>Nombres y Apellidos</th>
                    <th>Fecha del Turno</th>
                    <th>Id. Turno Original</th>
                    <th>Id. Turno Nuevo</th>
                    <th>Fecha Solicitud</th>
                    <th>Fecha Aprobaci&oacute;n</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Obtener el estado seleccionado del formulario
                $estado = isset($_POST['estado']) ? $_POST['estado'] : 'Pendiente';
                
                // Obtener solicitudes de intercambio pendientes
                $sql = "SELECT it.id_intercambio , it.empleado_id_solicitante, empleado_id_receptor, it.fecha_turno, it.id_turno_original, it.id_turno_nuevo, it.estado, 
                               CONCAT(es1.nombre, ' ', es1.apellido) AS solicitante, CONCAT(es2.nombre, ' ', es2.apellido) AS receptor, it.fecha_solicitud, it.fecha_aprobacion
                        FROM intercambio_turnos it
                        JOIN empleados es1 ON it.empleado_id_solicitante = es1.empleado_id
                        JOIN empleados es2 ON it.empleado_id_receptor = es2.empleado_id
                        WHERE it.estado = '$estado'
                        AND it.servicio_id = $servicio_id
                        AND it.cod_empresa = $cod_empresa
                        ORDER BY it.fecha_turno DESC";
                        //echo $sql;
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id_intercambio'] . "</td>";
                        echo "<td>" . $row['empleado_id_solicitante'] . "</td>";
                        echo "<td>" . $row['solicitante'] . "</td>";
                        echo "<td>" . $row['empleado_id_receptor'] . "</td>";
                        echo "<td>" . $row['receptor'] . "</td>";
                        echo "<td>" . $row['fecha_turno'] . "</td>";
                        echo "<td>" . $row['id_turno_original'] . "</td>";
                        echo "<td>" . $row['id_turno_nuevo'] . "</td>";
                        echo "<td>" . $row['fecha_solicitud'] . "</td>";
                        echo "<td>" . $row['fecha_aprobacion'] . "</td>";
                        echo "<td>" . $row['estado'] . "</td>";
                        echo "<td><textarea name=\"observaciones_{$row['id_reemplazo']}\" class=\"form-control\" rows=\"2\"></textarea></td>";
                        echo '<td>
                                <form action="aprobar_rechazar.php" method="POST" class="form-inline">
                                    <input type="hidden" name="intercambio_id" value="' . $row['id_intercambio'] . '">
                                     <input type="hidden" name="observaciones" value="" class="observaciones-input">
                                    <button type="submit" name="aprobar" class="btn btn-success btn-sm mr-2">Aprobar</button>
                                    <button type="submit" name="rechazar" class="btn btn-danger btn-sm">Rechazar</button>
                                </form>
                              </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>No hay solicitudes con el estado seleccionado</td></tr>";
                }

                $conn->close();
                ?>
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
