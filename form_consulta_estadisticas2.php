<?php
    include 'menu_ppal_maestros.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadisticas</title>
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
        
        <center><h2>Estadisticas detalladas de los Turnos</h2></center>

        <form method="post" action="form_consulta_estadisticas2.php">
            
            <div class="form-group">
                <label for="mes">Seleccione el mes:</label>
                <input type="month" name="mes" id="mes" class="form-control" required>
            </div> 
            
            <input type="submit" class="btn btn-primary" name="consultar" value="Consultar">
             <center><a href="menu_ppal_admin.php" class="btn btn-success mb-3">Volver a Menu Principal</a></center>
        </form>
    </div>
    <br>
<?php
    include("session.php");
    // Conexión a la base de datos
    include "db.php";
    $conn->set_charset("utf8"); //Para grantizar las tildes
    
    $cod_empresa =  $_SESSION['cod_empresa'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['consultar'])) {

        $mes = isset($_POST['mes']) ? $_POST['mes'] : null;
        
        echo '<center><h3>Mes: '.$mes.'</h3></center>';
            
        /* if (!$mes) {
            echo "<script>alert('Por favor, especifique un mes para procesar.'); window.location.href = 'form_create_turnos_def.php';</script>";
            exit;
        }*/
        
        // Validar que el mes esté en el formato correcto (YYYY-MM)
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mes)) {
            echo "<script>alert('El formato del mes no es correcto. Use el formato YYYY-MM.'); window.location.href = 'form_consulta_estadiscticas2.php';</script>";
            exit;
        }
        
        $year = substr($mes, 0, 4);  // Extrae el año
        $month = substr($mes, 5, 2); // Extrae el mes

        // SQL para obtener los turnos del empleado en el mes seleccionado
        $sql = "SELECT dt.empleado_id, CONCAT(e.nombre, ' ', e.apellido) AS nombre_empleado, dt.turno_id, COUNT(dt.turno_id) as cantidad_turnos 
                FROM detalle_turno dt
                JOIN empleados e ON e.empleado_id = dt.empleado_id
                WHERE YEAR(dt.fecha) = ? AND MONTH(dt.fecha) = ? AND e.cod_empresa = ?
                GROUP BY dt.empleado_id, dt.turno_id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $year, $month, $cod_empresa);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        // Procesar los resultados
        $estadisticas_empleados = [];  // Este array almacenará las estadísticas por empleado
        
        // Procesar los resultados
        while ($fila = $resultado->fetch_assoc()) {

            $empleado_id = $fila['empleado_id'];            
            $cantidad_turnos = $fila['cantidad_turnos'];            
            $turno_id = $fila['turno_id'];
            $nombre_empleado = $fila['nombre_empleado'];
            
            // Si el empleado aún no está en el array, inicializamos su array de estadísticas
            if (!isset($estadisticas_empleados[$empleado_id])) {
                $estadisticas_empleados[$empleado_id] = [
                    'nombre_empleado' => "",
                    'NOCHES' => 0,
                    'CORRIDO' => 0,
                    'DIA' => 0,
                    'MAÑANA' => 0,
                    'TARDE' => 0,
                    'Descanso' => 0,
                    'TOTAL_TURNOS' => 0,
                    'HORAS_INCAPACIDAD' => 0,
                    'HORAS_INCAPACIDAD_ACCIDENTAL_LABORAL' => 0,
                    'HORAS_LICENCIA_MATRIMONIO' => 0,
                    'HORAS_LICENCIA_MATERNIDAD' => 0,
                    'HORAS_LICENCIA_PATERNIDAD' => 0,
                    'HORAS_CALAMIDAD' => 0,
                    'HORAS_PERMISO_LUTO' => 0,
                    'HORAS_LICENCIA_REMUNERADA' => 0,
                    'HORAS_LICENCIA_NO_REMUNERADA' => 0,
                    'HORAS_LICENCIA_ADEUDADAS' => 0,
                    'HORAS_REUNION_ADMINISTRATIVA' => 0,
                    'HORAS_VACACIONES' => 0,
                    'HORAS_DISPONIBILIDAD' => 0,
                    'HORAS_FORMACION_CAPACITACION' => 0,
                ];
            }

            // Aquí actualizamos las estadísticas según el turno del empleado
            switch ($turno_id) {
                case 'C':
                case 'CA':
                case 'CB':
                case 'C10A':
                case 'C10B':
                case 'C9A':
                case 'C9B':
                case 'C9C':
                case 'C9D':
                case 'C9F':
                case 'C8A':
                case 'C8B':
                case 'C8C':
                case 'C8D':
                case 'C8E':
                case 'C7A':
                case 'C7B':
                case 'C11A' : //Corrido
                    $estadisticas_empleados[$empleado_id]['CORRIDO'] = $cantidad_turnos;
                    break;
                case 'N':
                case 'NC':
                case 'NB':
                case 'N10A':
                case 'N9B':
                case 'N8C': // Turno de noche
                    $estadisticas_empleados[$empleado_id]['NOCHES'] = $cantidad_turnos;
                    break;
                case 'M6A':
                case 'M6B':
                case 'M6C':
                case 'M5A':
                case 'M5B':
                case 'M5C':
                case 'M5D':
                case 'M4A': // Turno de día
                    $estadisticas_empleados[$empleado_id]['DIA'] = $cantidad_turnos;
                    break;
                case 'M': // Turno de mañana
                    $estadisticas_empleados[$empleado_id]['MAÑANA'] = $cantidad_turnos;
                    break;
                case 'T6A':
                case 'T6B':
                case 'T8A':
                case 'T5A':
                case 'T4A':
                case 'T3A': // Turno de tarde
                    $estadisticas_empleados[$empleado_id]['TARDE'] = $cantidad_turnos;
                    break;
                case 'D':
                case 'P': // Descanso
                    $estadisticas_empleados[$empleado_id]['Descanso'] = $cantidad_turnos;
                    break;
                case 'V': // Vacaciones
                    $estadisticas_empleados[$empleado_id]['HORAS_VACACIONES'] = $cantidad_turnos;
                    break;
                case 'I': // Incapacidad
                    $estadisticas_empleados[$empleado_id]['HORAS_INCAPACIDAD'] = $cantidad_turnos;
                    break;
                case 'AT': // Incapacidad por accidente laboral
                    $estadisticas_empleados[$empleado_id]['HORAS_INCAPACIDAD_ACCIDENTAL_LABORAL'] = $cantidad_turnos;
                    break;
                // Añadir otros casos para diferentes códigos de turnos
            }
            
            $estadisticas_empleados[$empleado_id]['nombre_empleado'] = $nombre_empleado;
        
            // contar los turnos por mes para cada empleado
            $estadisticas_empleados[$empleado_id]['TOTAL_TURNOS'] += $cantidad_turnos;
        }

        // Cerrar conexión
        $conn->close();

        // Mostrar resultados en una tabla
        echo "<table id='tablaEstadisticas2' class='table table-striped'>";
        echo "<thead><tr><th>Empleado ID</th><th>Nombre y Apellidos</th><th>Noches</th><th>Corrido</th><th>Día</th><th>Mañana</th><th>Tarde</th><th>Descanso/Posturno</th><th>Total Turnos</th><th>Vacaciones</th><th>Incapacidad</th>
            <th>Incap. Acc. Laboral</th><th>Lic. Matr.</th><th>Lic. Maternidad</th> <th>Lic. Paternidad</th><th>Calamidad</th><th>Permiso Luto</th><th>Lic. Remunerada</th><th>Lic. No Remunerada</th>
            <th>Lic. Adeudadas</th><th>Reuniones Administrativas</th><th>Disponibilidad</th><th>Formación y Capacitación</th> </tr></thead>";
        echo "<tbody>";

        foreach ($estadisticas_empleados as $empleado_id => $estadisticas) {
            echo "<tr>
                <td>$empleado_id</td>
                <td>{$estadisticas['nombre_empleado']}</td>
                <td>{$estadisticas['NOCHES']}</td>
                <td>{$estadisticas['CORRIDO']}</td>
                <td>{$estadisticas['DIA']}</td>
                <td>{$estadisticas['MAÑANA']}</td>
                <td>{$estadisticas['TARDE']}</td>
                <td>{$estadisticas['Descanso']}</td>
                <td>{$estadisticas['TOTAL_TURNOS']}</td>
                <td>{$estadisticas['HORAS_VACACIONES']}</td>
                <td>{$estadisticas['HORAS_INCAPACIDAD']}</td>
                <td>{$estadisticas['HORAS_INCAPACIDAD_ACCIDENTAL_LABORAL']}</td>
                <td>{$estadisticas['HORAS_LICENCIA_MATRIMONIO']}</td>
                <td>{$estadisticas['HORAS_LICENCIA_MATERNIDAD']}</td>
                <td>{$estadisticas['HORAS_LICENCIA_PATERNIDAD']}</td>
                <td>{$estadisticas['HORAS_CALAMIDAD']}</td>
                <td>{$estadisticas['HORAS_PERMISO_LUTO']}</td>
                <td>{$estadisticas['HORAS_LICENCIA_REMUNERADA']}</td>
                <td>{$estadisticas['HORAS_LICENCIA_NO_REMUNERADA']}</td>
                <td>{$estadisticas['HORAS_LICENCIA_ADEUDADAS']}</td>
                <td>{$estadisticas['HORAS_REUNION_ADMINISTRATIVA']}</td>
                <td>{$estadisticas['HORAS_DISPONIBILIDAD']}</td>
                <td>{$estadisticas['HORAS_FORMACION_CAPACITACION']}</td>
            </tr>";
        }

        echo "</tbody></table>";
    }
?>

<script>
        $(document).ready(function() {
            $('#tablaEstadisticas2').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Exportar a Excel',
                        title: 'Estadisticas detalladas de los Turnos'
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
