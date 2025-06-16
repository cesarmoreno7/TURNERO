<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnos Empleados</title>
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
<body>
    <div class="container">
        <h1 class="mt-5">Turnos Empleados</h1>
        <form action="consultar_detalle_turno_emp.php" method="post" class="mt-4">
            <div class="form-group">
                <label for="unidad_funcional">Unidad Funcional</label>
                <input type="text" class="form-control" id="unidad_funcional" name="unidad_funcional" required>
            </div>
            <div class="form-group">
                <label for="servicio">Servicio</label>
                <select class="form-control" id="servicio" name="servicio_id" required>
                    
                    <?php
                        echo "<option value=''>"."Seleccione Servicio"."</option>";
                        // Conexión a la base de datos
                        $servername = "localhost";
                        $username = "prodsoft_turnero";
                        $password = "turnero2024";
                        $dbname = "prodsoft_turnero";
    
                        $conn = new mysqli($servername, $username, $password, $dbname);
    
                        if ($conn->connect_error) {
                            die("Conexión fallida: " . $conn->connect_error);
                        }
    
                        $sql = "SELECT servicio_id, nombre FROM servicio";
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
            <div class="form-group">
                <label for="centro_costo">Centro de Costos</label>
                <select class="form-control" id="centro_costo" name="centro_costo_id" required>
                    <!-- Options will be populated by PHP -->
                </select>
            </div>
            <div class="form-group">
                <label for="grupo">Grupo</label>
                <select class="form-control" id="grupo" name="grupo_id" required>
                    <!-- Options will be populated by PHP -->
                </select>
            </div>
            <div class="form-group">
                <label for="ano">Año</label>
                <input type="number" class="form-control" id="ano" name="ano" required>
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
            <button type="submit" name="filtrar" class="btn btn-primary">Ver Detalle</button>
            <!--<button type="submit" name="guardar" class="btn btn-primary">Guardar Datos</button>-->
       
        </form>
    </div>
    
    <div class="container">
    
        <?php
            // Verificar si se ha enviado el formulario al presionar el botón: "filtrar"
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['filtrar'])) {
                
                // Obteniendo los datos del formulario del encabezado
                $unidad_funcional = $_POST['unidad_funcional'];
                $servicio_id = $_POST['servicio_id'];
                $centro_costo_id = $_POST['centro_costo_id'];
                $grupo_id = $_POST['grupo_id'];
                $ano = $_POST['ano'];
                $mes = $_POST['mes'];
                $dias_habiles = $_POST['dias_habiles'];
                $numero_festivos = $_POST['numero_festivos'];
                $horas_laborar = $_POST['horas_laborar'];
                $horas_contratadas = 240;
                
                // Conexión a la base de datos
                $conn = new mysqli('localhost', 'prodsoft_turnero', 'turnero2024', 'prodsoft_turnero');
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }
        
                // Obtener empleados
                $sql = "SELECT DISTINCT e.empleado_id, e.nombre, e.apellido 
                        FROM empleados e, empleados_servicio es
                        WHERE es.servicio_id = $servicio_id AND es.centro_costo_id = $centro_costo_id AND es.grupo_id = $grupo_id and e.empleado_id = es.empleado_id";
                $result = $conn->query($sql);
        
                // Obtener días del mes
                $mes_actual = $_POST['mes'];
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
                
                echo '<form id="detalleForm" action="guardar_datos_turnos_emp.php" method="post">';
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
                        echo '<tr>';
                        echo '<td><input type="number" name="empleado_id[]" value="'.$row['empleado_id'].'" class="form-control" /></td>';
                        echo '<td>' . $row['nombre'] . ' ' . $row['apellido'] . '</td>';
                        echo '<td><input type="number" name="horas_contratadas[]" value="'.$horas_contratadas.'" class="form-control" /></td>';
                        echo '<td><input type="number" name="horas_a_laborar_mes[]" value="'.$horas_laborar.'" class="form-control horas-a-laborar" /></td>';
                        
                        $empleado_id =  $row['empleado_id'];
                        $turnos_sql = "SELECT dt.empleado_id, dt.fecha, dt.turno_id, ht.cod_turno
                        FROM detalle_turno dt inner join horas_turnos ht on (dt.turno_id = ht.id_turno) 
                        WHERE dt.servicio_id = $servicio_id AND dt.centro_costo_id = $centro_costo_id AND dt.grupo_id = $grupo_id and dt.empleado_id = $empleado_id order by dt.fecha";
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

    <script>

        document.getElementById('servicio').addEventListener('change', function() {
            const servicioId = this.value;
            if (servicioId) {
                fetch(`get_centros_costo.php?servicio_id=${servicioId}`)
                    .then(response => response.json())
                    .then(data => {
                        let centroCostoSelect = document.getElementById('centro_costo');
                        centroCostoSelect.innerHTML = '';
                        data.forEach(item => {
                            let option = document.createElement('option');
                            option.value = item.centro_costo_id;
                            option.text = item.nombre;
                            centroCostoSelect.appendChild(option);
                        });
                        // Trigger change event to load groups
                        centroCostoSelect.dispatchEvent(new Event('change'));
                    });
            }
        });

        document.getElementById('centro_costo').addEventListener('change', function() {
            const centroCostoId = this.value;
            if (centroCostoId) {
                fetch(`get_grupos.php?centro_costo_id=${centroCostoId}`)
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
    
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>
</html>
