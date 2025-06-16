<?php
    include("session.php");
    include 'db.php';
    $cod_empresa = $_SESSION['cod_empresa'];
    $codigo_usu  = $_SESSION['codigo_usu'];
    
    $tipo_usu    = $_SESSION['user_type'];
    $servicio_id = $_SESSION['servicio_id'];
    
    if ($_SESSION['user_type'] === 'Admin'){
        echo "<strong>Bienvenido:</strong> ".$_SESSION['user_name'];
    }else{
        echo "<strong>Bienvenido:</strong> ".$_SESSION['user_name']." --- "."<strong> Servicio: </strong>".$_SESSION['nom_serv'];
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnos Automaticos para Empleados</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th, .table td {
            text-align: center;
            font-size: 20px; /* Ajusta este valor según tus necesidades */
        }
        .day-column {
            width: 150px; /* Ajusta este valor según tus necesidades */
        }
        .zeros {
            color: white;
        }
        .day-header {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    </style>
</head>
<center>
    <a class="navbar-brand h2" href="#" style="color: #00BFFF; font-weight: bold;">
        Ruster
    </a>
</center>
<body>
    <div class="container">
        <h1 class="mt-5">Generar Turnos Automaticos para Empleados</h1>
        <center><a href="menu_ppal_admin.php" class="btn btn-success mb-3">Volver a Menu Principal</a></center>
        <form action="form_generar_turnos_aut.php" method="post" class="mt-4">
            <div class="form-group">
                <label for="unidad_funcional">Unidad Funcional</label>
                <input type="text" class="form-control" id="unidad_funcional" name="unidad_funcional" required>
            </div>
            <div class="form-group">
                <label for="servicio">Servicio</label>
                <select class="form-control" id="servicio" name="servicio_id" required>
                    
                    <?php
                        echo "<option value=''>"."Seleccione Servicio"."</option>";
                        //if ($tipo_usu === 'Admin'){
                            $sql = "SELECT servicio_id, nombre FROM servicio where servicio_id = $servicio_id AND cod_empresa = $cod_empresa";
                        /*}else{
                            $sql = "SELECT servicio_id, nombre FROM servicio where cod_empresa = $cod_empresa";
                        }*/
                        $result = $conn->query($sql);
    
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['servicio_id'] . "'>" . $row['nombre'] . "</option>";
                            }
                        }
    
                        $conn->close();
                    ?>
                </select>
            </div>
            <!-- <div class="form-group">
                <label for="centro_costo">Centro de Costos</label>
                <select class="form-control" id="centro_costo" name="centro_costo_id" required>
                    
                </select>
            </div> -->
            <div class="form-group">
                <label for="grupo">Grupo</label>
                <select class="form-control" id="grupo" name="grupo_id" required>
                    <!-- Options will be populated by PHP -->
                </select>
            </div>
            <div class="form-group">
                <label for="ano">Año</label>
                <input type="number" class="form-control" id="ano" name="ano" value="<?php echo date('Y'); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="mes">Mes</label>
                <select class="form-control" id="mes" name="mes" required>
                    <option value="1">Enero</option>
                    <option value="2">Febrero</option>
                    <option value="3">Marzo</option>
                    <option value="4">Abril</option>
                    <option value="5">Mayo</option>
                    <option value="6">Junio</option>
                    <option value="7">Julio</option>
                    <option value="8">Agosto</option>
                    <option value="9">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </select>
            </div>
            <div class="form-group">
                <label for="dias_habiles">Días Hábiles del Mes</label>
                <input type="number" class="form-control" id="dias_habiles" name="dias_habiles" readonly>
            </div>
            <div class="form-group">
                <label for="numero_festivos">Número de Festivos y Dominicales del Mes</label>
                <input type="number" class="form-control" id="numero_festivos" name="numero_festivos" readonly>
            </div>
            <div class="form-group">
                <label for="horas_laborar">Horas a Laborar en el Mes</label>
                <input type="number" class="form-control" id="horas_laborar" name="horas_laborar" readonly>
            </div>
            <button type="submit" name="ver_empleados" class="btn btn-secondary">Ver Empleados</button>
        </form>
    </div>
    
    
    <div class="container">
    
        <?php
            // Verificar si se ha enviado el formulario al presionar el botón: "filtrar" o "generar_automatico"
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ver_empleados'])) {
                
                // Obteniendo los datos del formulario del encabezado
                $unidad_funcional = $_POST['unidad_funcional'];
                $servicio_id = $_POST['servicio_id'];
                $centro_costo_id = 0;
                $grupo_id = $_POST['grupo_id'];
                $ano = $_POST['ano'];
                $mes = $_POST['mes'];
                $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
                $dias_habiles = $_POST['dias_habiles'];
                $numero_festivos = $_POST['numero_festivos'];
                $horas_laborar = $_POST['horas_laborar'];
                
                 // Verificar si ya existen registros para el año y mes especificados en la tabla "detalle_turno"
                $check_sql = "SELECT COUNT(*) AS total FROM detalle_turno WHERE YEAR(fecha) = $ano AND MONTH(fecha) = $mes";
                $check_result = $conn->query($check_sql);
                $check_row = $check_result->fetch_assoc();
                $procesado = $check_row['total'] > 0;
                
                // Obtener el número de registros en la tabla "turnos"
                $sql = "SELECT COUNT(*) AS total FROM turnos WHERE cod_empresa = '$cod_empresa'";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                $total_registros_turnos = $row['total'];
        
                // Obtener empleados
                $sql = "SELECT DISTINCT e.empleado_id, e.nombre, e.apellido 
                        FROM empleados e, empleados_servicio es, usuarios u
                        WHERE es.servicio_id = $servicio_id AND es.centro_costo_id = $centro_costo_id AND es.grupo_id = $grupo_id AND e.empleado_id = es.empleado_id AND e.empleado_id = u.empleado_id
                        AND e.cod_empresa = $cod_empresa
                        AND es.estado = 'Activo' 
                        AND u.tipo_usu = 'Empleado'";
                //echo  $sql;
                $result = $conn->query($sql);
        
                // Obtener días del mes
                //$mes_actual = date('m');
                //$anio_actual = date('Y');
                $mes_actual = $_POST['mes'];
                $mes_actual = str_pad($mes_actual, 2, '0', STR_PAD_LEFT);
                $anio_actual = $_POST['ano'];
                $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes_actual, $anio_actual);
                $dias_semana = ['D', 'L', 'M', 'X', 'J', 'V', 'S'];
                
                // Obtener festivos del mes actual
                $festivos_sql = "SELECT fecha FROM festivos WHERE (MONTH(fecha) = $mes_actual AND YEAR(fecha) = $anio_actual) OR (MONTH(fecha) = $mes_actual + 1 AND DAY(fecha) = 1 AND YEAR(fecha) = $anio_actual)";
                $festivos_result = $conn->query($festivos_sql);
                $festivos = [];
                while ($festivo = $festivos_result->fetch_assoc()) {
                    $festivos[] = $festivo['fecha'];
                }
                
                /*echo "Contenido del array \$festivos:<br>";
                print_r($festivos);
                echo "<br><br>";*/
                
                // Imprimir la leyenda en una sola fila
                echo '<div class="row mt-4 mb-2">';
                echo '<div class="col">';
                echo '<p class="small">';
                echo 'Filtro aplicado:';
                echo '<br>';
                echo '<strong>Unidad Funcional:</strong> ' . htmlspecialchars($unidad_funcional) . ' | ';
                echo '<strong>Servicio ID:</strong> ' . htmlspecialchars($servicio_id) . ' | ';
                echo '<strong>Centro de Costo ID:</strong> ' . htmlspecialchars($centro_costo_id) . ' | ';
                echo '<strong>Grupo ID:</strong> ' . htmlspecialchars($grupo_id) . ' | ';
                echo '<strong>Año:</strong> ' . htmlspecialchars($ano) . ' | ';
                echo '<br>';
                echo '<strong>Mes:</strong> ' . htmlspecialchars($mes) . ' | ';
                echo '<strong>Días Hábiles:</strong> ' . htmlspecialchars($dias_habiles) . ' | ';
                echo '<strong>Número de Festivos y Dominicales del Mes:</strong> ' . htmlspecialchars($numero_festivos) . ' | ';
                echo '<strong>Horas a Laborar:</strong> ' . htmlspecialchars($horas_laborar) . ' | ';
                echo '<strong>Horas Contratadas:</strong> ' . htmlspecialchars($horas_contratadas);
                echo '</p>';
                echo '</div>';
                echo '</div>';

                echo '<form id="detalleForm" action="guardar_datos_turnos_aut.php" method="post">';
                echo '<table class="table table-bordered mt-4">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Cédula</th>';
                echo '<th>Nombres</th>';
                echo '<th>Horas Contratadas</th>';
                echo '<th>Horas a Laborar Mes</th>';
                
                // Encabezados de los días del mes
                for ($i = 1; $i <= $dias_mes; $i++) {  
                    $dia_formateado = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $fecha_actual = "$anio_actual-$mes_actual-$dia_formateado";
                    $timestamp = strtotime($fecha_actual);
                    $dia_semana = date('w', $timestamp);
                    //$es_festivo = in_array($fecha_actual, $festivos);                    
                                       
                    $es_festivo = in_array($fecha_actual, $festivos);                    
                                       
                    $label = $es_festivo || $dias_semana[$dia_semana] == 'D' ? 'F' : '';
                    $day_display = "<span class='zeros'>000000000</span>$i";
                    echo "<th class='day-column'><div class='day-header'>$day_display<br>" . $dias_semana[$dia_semana] . $label . "</div></th>";
                }
                
                // Agregar el primer día del siguiente mes
                $mes_siguiente = $mes_actual + 1;
                if ($mes_siguiente > 12) {
                    $mes_siguiente = 1;
                    $anio_actual++;
                }
                $fecha_siguiente = sprintf("%04d-%02d-%02d", $anio_actual, $mes_siguiente, 1);
                $timestamp = strtotime($fecha_siguiente);
                $dia_semana = date('w', $timestamp);
                $es_festivo = in_array($fecha_siguiente, $festivos);
        
                $label = $es_festivo || $dias_semana[$dia_semana] == 'D' ? 'F' : '';
                $day_display = "<span class='zeros'>000000000</span>1";
                echo "<th class='day-column'><div class='day-header'>$day_display<br>" . $dias_semana[$dia_semana] . $label . "</div></th>";
        
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        
                        //Se consulta las horas contratadas para el empleado en la tabla "personal"
                        $empleado_id = $row['empleado_id'];
                        $sql_horas = "SELECT horas FROM personal WHERE empleado_id = $empleado_id AND cod_empresa = $cod_empresa AND motivo_retiro = ''";
                        $resultado = $conn->query($sql_horas);
                        $row_horas = $resultado->fetch_assoc();
                        $horas_contratadas = round($row_horas['horas']);
                        
                        echo '<tr>';
                        //echo '<td>' . $row['empleado_id'] . '</td>';
                        echo '<td><input type="number" name="empleado_id[]" value="'.$row['empleado_id'].'" class="form-control" /></td>';
                        echo '<td>' . $row['nombre'] . ' ' . $row['apellido'] . '</td>';
                        echo '<td><input type="number" name="horas_contratadas[]" value="'.round($horas_contratadas).'" class="form-control" /></td>';
                        echo '<td><input type="number" name="horas_a_laborar_mes[]" value="'.$horas_laborar.'" class="form-control horas-a-laborar" /></td>';
                        
                        // Campos para cada día del mes
                        for ($i = 1; $i <= $dias_mes; $i++) {
                            echo '<td class="day-column"><select name="turno_dia[' . $row['empleado_id'] . '][' . $i . ']" class="form-control turno-select">';
                            echo '<option value=""></option>';
                            
                            // Opciones de turnos cargadas desde la base de datos
                            $turnos_sql = "SELECT cod_turno, nombre_turno FROM turnos WHERE cod_empresa = '$cod_empresa'";
                            $turnos_result = $conn->query($turnos_sql);
                            while ($turno = $turnos_result->fetch_assoc()) {
                                echo '<option value="' . $turno['cod_turno'] . '">' . $turno['nombre_turno'] . '</option>';
                            }
                            echo '</select></td>';
                        }
                        
                        // Campo para el primer día del siguiente mes
                        echo '<td class="day-column"><select name="turno_dia[' . $row['empleado_id'] . '][next_month_1]" class="form-control turno-select">';
                        echo '<option value=""></option>';
                        
                        // Opciones de turnos cargadas desde la base de datos
                        $turnos_sql = "SELECT cod_turno, nombre_turno FROM turnos WHERE cod_empresa = '$cod_empresa'";
                        $turnos_result = $conn->query($turnos_sql);
                        while ($turno = $turnos_result->fetch_assoc()) {
                            echo '<option value="' . $turno['cod_turno'] . '">' . $turno['nombre_turno'] . '</option>';
                        }
                        echo '</select></td>';
        
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="' . ($dias_mes + 5) . '">No se encontraron empleados.</td></tr>';
                }
        
                echo '</tbody>';
                echo '</table>';
                
                echo '<td><input type="hidden" name="horas_laborar" value="'.$horas_laborar.'" class="form-control" /></td>';
                echo '<td><input type="hidden" name="unidad_funcional" value="'.$unidad_funcional.'" class="form-control" /></td>';
                echo '<td><input type="hidden" name="servicio_id" value="'.$servicio_id.'" class="form-control" /></td>';
                echo '<td><input type="hidden" name="centro_costo_id" value="'.$centro_costo_id.'" class="form-control" /></td>';
                echo '<td><input type="hidden" name="grupo_id" value="'.$grupo_id.'" class="form-control" /></td>';
                echo '<td><input type="hidden" name="ano" value="'.$ano.'" class="form-control" /></td>';
                echo '<td><input type="hidden" name="mes" value="'.$mes.'" class="form-control" /></td>';
                echo '<td><input type="hidden" name="dias_habiles" value="'.$dias_habiles.'" class="form-control" /></td>';
                echo '<td><input type="hidden" name="numero_festivos" value="'.$numero_festivos.'" class="form-control" /></td>';
                echo '<td><input type="hidden" name="cod_empresa" value="'.$cod_empresa.'" class="form-control" /></td>';
                echo '<td><input type="hidden" name="codigo_usu" value="'.$codigo_usu.'" class="form-control" /></td>';
                echo '<button type="submit" name="generar_automatico" class="btn btn-success">Generar Turnos</button>';
                echo '   ';
                echo '<button type="submit" name="actualizar_turnos" class="btn btn-success">Actualizar Turnos</button>';
                echo '</form>';
                
                $conn->close();
            }
        ?>
       
    </div>


    <div class="container">
    
        <?php
            // Verificar si se ha enviado el formulario al presionar el botón: "filtrar"
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                
                // Obteniendo los datos del formulario del encabezado
                $unidad_funcional = $_POST['unidad_funcional'];
                $servicio_id = $_POST['servicio_id'];
                $centro_costo_id = 0;
                $grupo_id = $_POST['grupo_id'];
                $ano = $_POST['ano'];
                $mes = $_POST['mes'];
                $dias_habiles = $_POST['dias_habiles'];
                $numero_festivos = $_POST['numero_festivos'];
                $horas_laborar = $_POST['horas_laborar'];
                
                // Obtener empleados
                $sql = "SELECT DISTINCT e.empleado_id, e.nombre, e.apellido 
                        FROM empleados e, empleados_servicio es, usuarios u
                        WHERE es.servicio_id = $servicio_id AND es.centro_costo_id = $centro_costo_id AND es.grupo_id = $grupo_id AND e.empleado_id = es.empleado_id AND e.empleado_id = u.empleado_id
                        AND e.cod_empresa = $cod_empresa
                        AND es.estado = 'Activo' 
                        AND u.tipo_usu = 'Empleado'";
                $result = $conn->query($sql);
        
                // Obtener días del mes
                $mes_actual = $_POST['mes'];
                $mes_actual = str_pad($mes_actual, 2, '0', STR_PAD_LEFT);
                $anio_actual = $_POST['ano'];
                $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes_actual, $anio_actual);
                $dias_semana = ['D', 'L', 'M', 'X', 'J', 'V', 'S'];
                
                // Obtener festivos del mes actual
                $festivos_sql = "SELECT fecha FROM festivos WHERE (MONTH(fecha) = $mes_actual AND YEAR(fecha) = $anio_actual) OR (MONTH(fecha) = $mes_actual + 1 AND DAY(fecha) = 1 AND YEAR(fecha) = $anio_actual)";
                $festivos_result = $conn->query($festivos_sql);
                $festivos = [];
                if ($festivos_result && $festivos_result->num_rows > 0) {
                    while ($festivo = $festivos_result->fetch_assoc()) {
                        $festivos[] = $festivo['fecha'];
                    }
                }
                echo '<br>';
                echo '<br>';
                echo 'Datos registrados para el filtro actual:';
                echo '<form id="detalleForm" action="" method="post">';
                echo '<table class="table table-bordered mt-4">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Cédula</th>';
                echo '<th>Nombres</th>';
                echo '<th>Horas Contratadas</th>';
                echo '<th>Horas a Laborar Mes</th>';
                
                // Encabezados de los días del mes
                for ($i = 1; $i <= $dias_mes; $i++) {
                    $fecha_actual = "$anio_actual-$mes_actual-" . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime($fecha_actual);
                    $dia_semana = date('w', $timestamp);
                    $es_festivo = in_array($fecha_actual, $festivos);
                    
                    $label = $es_festivo || $dias_semana[$dia_semana] == 'D' ? 'F' : '';
                    $day_display = "<span class='zeros'>000000000</span>$i";
                    echo "<th class='day-column'><div class='day-header'>$day_display<br>" . $dias_semana[$dia_semana] . $label . "</div></th>";
                }
                
                // Agregar el primer día del siguiente mes
                $mes_siguiente = $mes_actual + 1;
                if ($mes_siguiente > 12) {
                    $mes_siguiente = 1;
                    $anio_actual++;
                }
                $fecha_siguiente = sprintf("%04d-%02d-%02d", $anio_actual, $mes_siguiente, 1);
                $timestamp = strtotime($fecha_siguiente);
                $dia_semana = date('w', $timestamp);
                $es_festivo = in_array($fecha_siguiente, $festivos);
        
                $label = $es_festivo || $dias_semana[$dia_semana] == 'D' ? 'F' : '';
                $day_display = "<span class='zeros'>000000000</span>1";
                echo "<th class='day-column'><div class='day-header'>$day_display<br>" . $dias_semana[$dia_semana] . $label . "</div></th>";
                
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
        
                // Obtener los turnos asignados de la tabla "detalle_turno"
                $turnos_asignados = [];
                
                // Iterar sobre cada empleado y cada día del mes para mostrar los campos de selección de turno
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        
                        //Se consulta las horas contratadas para el empleado en la tabla "personal"
                        $empleado_id = $row['empleado_id'];
                        $sql_horas = "SELECT horas FROM personal WHERE empleado_id = $empleado_id AND cod_empresa = $cod_empresa AND motivo_retiro = ''";
                        $resultado = $conn->query($sql_horas);
                        $row_horas = $resultado->fetch_assoc();
                        $horas_contratadas = $row_horas['horas'];
                        
                        echo '<tr>';
                        echo '<td><input type="number" name="empleado_id[]" value="'.$row['empleado_id'].'" class="form-control" /></td>';
                        echo '<td>' . $row['nombre'] . ' ' . $row['apellido'] . '</td>';
                        echo '<td><input type="number" name="horas_contratadas[]" value="'.round($horas_contratadas).'" class="form-control" /></td>';
                        echo '<td><input type="number" name="horas_a_laborar_mes[]" value="'.$horas_laborar.'" class="form-control horas-a-laborar" /></td>';
                        
                        $empleado_id =  $row['empleado_id'];
                        $turnos_sql = "SELECT dt.empleado_id, dt.fecha, dt.turno_id, ht.cod_turno
                        FROM detalle_turno dt inner join horas_turnos ht on (dt.turno_id = ht.id_turno) 
                        WHERE dt.servicio_id = $servicio_id AND dt.centro_costo_id = $centro_costo_id AND dt.grupo_id = $grupo_id AND dt.empleado_id = $empleado_id 
                        AND  dt.codigo_usu = '$codigo_usu' AND dt.cod_empresa = $cod_empresa
                        ORDER BY dt.fecha";
                        $turnos_result = $conn->query($turnos_sql);
                        while ($turno = $turnos_result->fetch_assoc()) {
                            $codigo_turno = $turno['cod_turno'];
                            echo '<td class="day-column">' . $codigo_turno . '</td>'; 
                        }
                        
                        echo '</tr>';
                    }
                }
                
                echo '</tbody>';
                echo '</table>';
                echo '</form>';
        
                $conn->close();
            }
        ?>
    </div>
    
    
     <?php
        include 'db.php'; // Conexión a la base de datos
        
        // Obtener la lista de empleados
        $query_empleados = "SELECT DISTINCT e.empleado_id, CONCAT(e.nombre, ' ', e.apellido) AS nombre_completo 
                                FROM empleados e, empleados_servicio es, usuarios u
                                WHERE es.servicio_id = $servicio_id AND es.centro_costo_id = $centro_costo_id AND es.grupo_id = $grupo_id AND e.empleado_id = es.empleado_id AND e.empleado_id = u.empleado_id
                                AND e.cod_empresa = $cod_empresa
                                AND es.estado = 'Activo' 
                                AND u.tipo_usu = 'Empleado'";
        $result_empleados = $conn->query($query_empleados);
        
        $empleados = [];
        if ($result_empleados->num_rows > 0) {
            while ($row = $result_empleados->fetch_assoc()) {
                $empleados[] = $row;
            }
        }
    ?>


    <div class="container mt-4">
        <!-- Botón para abrir el modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formModal">
            Registrar Horas Adicionales
        </button>

        <!-- Modal -->
        <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModalLabel">Registro de Horas Adicionales</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="procesar_horas_adicionales.php" method="POST">
                        <div class="modal-body">
                            <!-- Lista desplegable de empleados -->
                            <div class="mb-3">
                                <label for="empleado_id" class="form-label">Empleado</label>
                                <select class="form-select" id="empleado_id" name="empleado_id" required>
                                    <option value="" disabled selected>Seleccione un empleado</option>
                                    <?php foreach ($empleados as $empleado): ?>
                                        <option value="<?php echo $empleado['empleado_id']; ?>">
                                            <?php echo $empleado['nombre_completo']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Campo para ingresar la fecha -->
                            <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" required>
                            </div>

                            <!-- Campo para ingresar las horas adicionales -->
                            <div class="mb-3">
                                <label for="horas" class="form-label">Horas Adicionales</label>
                                <input type="number" class="form-control" id="horas" name="horas" step="0.5" min="0" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
     
    <script>

       
        document.getElementById('servicio').addEventListener('change', function() {
            const servicioId = this.value;
            if (servicioId) {
                fetch(`get_serv_gru.php?servicio_id=${servicioId}`)
                    .then(response => response.json())
                    .then(data => {
                        let grupoSelect = document.getElementById('grupo');
                        grupoSelect.innerHTML = '';
                        data.forEach(item => {
                            let option = document.createElement('option');
                            option.value = item.grupo_id;
                            option.text = item.nombre;
                            grupoSelect.appendChild(option);
                        });
                    });
            }
        });

        document.getElementById('mes').addEventListener('change', function() {
            const year = document.getElementById('ano').value;
            const month = this.value;
            if (year && month) {
                fetch(`calculate_dates.php?year=${year}&month=${month}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('dias_habiles').value = data.dias_habiles;
                        document.getElementById('numero_festivos').value = data.numero_festivos;
                        document.getElementById('horas_laborar').value = data.horas_laborar;
                    });
            }
        });
    </script>
</body>
</html>
