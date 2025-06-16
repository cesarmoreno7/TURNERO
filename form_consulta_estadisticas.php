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
        <center><h2>Estadisticas de Horas y Novedades</h2></center>

        <form method="post" action="form_consulta_estadisticas.php">
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
        // Conexi贸n a la base de datos
        include "db.php";
        $conn->set_charset("utf8"); //Para grantizar las tildes
        
        $cod_empresa =  $_SESSION['cod_empresa'];
        $saldo_meses_anteriores = 0;

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['consultar'])) {

            // Capturar el mes especificado por el usuario
            $mes = isset($_POST['mes']) ? $_POST['mes'] : null;
            
            // Función para calcular las horas laborales de lunes a sábado excluyendo domingos y festivos
            
            // Separar el año y el mes para la consulta
            $anio = substr($mes, 0, 4);
            $mes_num = substr($mes, 5, 2);
        
            // Horas laborables diarias
            $horas_diarias = 8;
        
            // Contador de días laborales
            $dias_laborales = 0;
        
            // Obtener el número de días en el mes
            $dias_en_mes = cal_days_in_month(CAL_GREGORIAN, $mes_num, $anio);
        
            // Obtener los días festivos desde la base de datos
            $query = "SELECT fecha FROM festivos WHERE fecha BETWEEN '$anio-$mes_num-01' AND '$anio-$mes_num-$dias_en_mes'";
            $resultado = $conn->query($query);
            
            $festivos = [];
            while ($fila = $resultado->fetch_assoc()) {
                $festivos[] = $fila['fecha'];
            }
        
            // Iterar por cada día del mes
            for ($dia = 1; $dia <= $dias_en_mes; $dia++) {
                // Crear una fecha
                $fecha = sprintf("%04d-%02d-%02d", $anio, $mes_num, $dia);
                $dia_semana = date("N", strtotime($fecha)); // 1 (lunes) a 7 (domingo)
        
                // Excluir domingos y festivos
                if ($dia_semana <= 6 && !in_array($fecha, $festivos)) {
                    $dias_laborales++;
                }
            }
        
            // Calcular horas laborales
            $horas_laborales = $dias_laborales * $horas_diarias;

            echo '<center><h3>Mes: '.$mes.'</h3></center>';

            // Validar que el mes esté en el formato correcto (YYYY-MM)
            if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mes)) {
                echo "<script>alert('El formato del mes no es correcto. Use el formato YYYY-MM.'); window.location.href = 'form_consulta_estadisticas.php';</script>";
                exit;
            }

            // Separar el año y el mes para la consulta
            $anio = substr($mes, 0, 4);
            $mes_num = substr($mes, 5, 2);

           // Consulta para obtener las horas por empleado en el mes
            $sql = "
            SELECT 
                dt.empleado_id,
                CONCAT(e.nombre, ' ', e.apellido) AS nombre_empleado,
                SUM(dt.hd) AS total_horas_diurnas,
                SUM(dt.hn) AS total_horas_nocturnas,
                SUM(dt.hed) AS total_horas_extras_diurnas,
                SUM(dt.hen) AS total_horas_extras_nocturnas,
                SUM(dt.horas_laborar_turno) AS total_horas_laboradas,
                sum(dt.hadic) AS total_horas_adicionales,
                SUM(CASE WHEN dt.turno_id IN ('I', 'L', 'LMT', 'LM', 'AT', 'LP', 'LU', 'LC', 'LNR', 'LH', 'V', 'D') THEN dt.horas_laborar_turno ELSE 0 END) AS horas_novedades,
                MAX(dt.horas_contratadas) AS horas_contratadas 
            FROM 
                detalle_turno dt
            JOIN 
                empleados e ON e.empleado_id = dt.empleado_id
            WHERE 
                YEAR(dt.fecha) = ? AND MONTH(dt.fecha) = ? AND e.cod_empresa = ?
            GROUP BY 
                dt.empleado_id
            ORDER BY 
                e.nombre ASC";
                
                //echo $sql;

            // Preparar la consulta
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $anio, $mes_num, $cod_empresa);
            $stmt->execute();
            $result_sql = $stmt->get_result();

            // Verificar si hay resultados
            if ($result_sql->num_rows > 0) {

                // Generar la tabla HTML
                echo "<table id='tablaEstadisticas' class='table table-bordered' cellpadding='10'>";
                echo "<thead>
                        <tr>
                            <th>Empleado ID</th>
                            <th>Nombre y Apellidos</th>
                            <th>Total Horas Ord. Diurnas</th>
                            <th>Total Horas Ord. Nocturnas</th>
                            <th>Horas Extras Diurnas</th>
                            <th>Horas Extras Nocturnas</th>
                            <th>Total Horas Laboradas</th>
                            
                            <th>Horas a Laborar Mes</th>
                            <th>Horas a Favor Mes</th>
                            <th>Horas Adicionales Mes</th>
                            <th>Horas en Contra Mes</th>
                            <th>Saldo Mensual</th>
                            <th>Saldo Neto</th>
                            
                            <th>Horas Novedades</th>
                        </tr>
                    </thead>
                    <tbody>";

                // Procesar los datos y generar las filas de la tabla
                while ($row = $result_sql->fetch_assoc()) {
                    
                    $total_horas_laboradas = 0;
                    $horas_contratadas = 0;
                    
                    $empleado_id = $row['empleado_id'];
                    $nombre_empleado = $row['nombre_empleado'];
                    $total_horas_diurnas = $row['total_horas_diurnas'];
                    $total_horas_nocturnas = $row['total_horas_nocturnas'];
                    $total_horas_extras_diurnas = $row['total_horas_extras_diurnas'];
                    $total_horas_extras_nocturnas = $row['total_horas_extras_nocturnas'];
                    $total_horas_laboradas = $row['total_horas_laboradas'];
                    $horas_novedades = $row['horas_novedades'];
                    $horas_contratadas = $row['horas_contratadas'];
                    
                    $Horas_a_Favor_Mes = 0;
                    $Horas_en_Contra_Mes = 0;
                    
                    if ($total_horas_laboradas > $horas_laborales){
                        $Horas_a_Favor_Mes = $total_horas_laboradas - $horas_laborales;
                    }else if ($total_horas_laboradas < $horas_laborales){
                        $Horas_en_Contra_Mes = $horas_laborales - $total_horas_laboradas;
                    }
                    
                    $horas_adicionales_Mes =  $row['total_horas_adicionales'];
                    $saldo_mensual = ($Horas_a_Favor_Mes + $horas_adicionales_Mes) - $Horas_en_Contra_Mes;
                    
                    //CALCULAR EL SALDO DE LOS MESES ANTERIORES PARA EL EMPLEADO
                    $SQL_meses_ant = "SELECT 
                        dt.empleado_id,
                        CONCAT(e.nombre, ' ', e.apellido) AS nombre_empleado,
                        SUM(dt.hd) AS total_horas_diurnas,
                        SUM(dt.hn) AS total_horas_nocturnas,
                        SUM(dt.hed) AS total_horas_extras_diurnas,
                        SUM(dt.hen) AS total_horas_extras_nocturnas,
                        SUM(dt.horas_laborar_turno) AS total_horas_laboradas,
                        SUM(dt.hadic) AS total_horas_adicionales,
                        SUM(
                            CASE 
                                WHEN dt.turno_id IN ('I', 'L', 'LMT', 'LM', 'AT', 'LP', 'LU', 'LC', 'LNR', 'LH', 'V', 'D') 
                                THEN dt.horas_laborar_turno 
                                ELSE 0 
                            END
                        ) AS horas_novedades,
                        MAX(dt.horas_contratadas) AS horas_contratadas -- Suponemos que existe un campo para las horas contratadas por empleado
                        FROM 
                            detalle_turno dt
                        JOIN 
                            empleados e ON e.empleado_id = dt.empleado_id
                        WHERE 
                            dt.fecha < DATE_FORMAT(NOW(), '%Y-%m-01') -- Excluye el mes actual
                            AND e.cod_empresa = ? -- Filtro por empresa
                            AND dt.empleado_id = ? -- Filtro por empleado
                        GROUP BY 
                            dt.empleado_id
                        ORDER BY 
                            e.nombre ASC";
                            
                        // Preparar la consulta
                        $stmt = $conn->prepare($SQL_meses_ant);
                        $stmt->bind_param("ii", $empleado_id, $cod_empresa);
                        $stmt->execute();
                        $result_sql_meses_ant = $stmt->get_result();
            
                        // Verificar si hay resultados
                        if ($result->num_rows > 0) {
                            while ($row_emp= $result_sql_meses_ant->fetch_assoc()) {
                                
                                $total_horas_laboradas_emp = $row_emp['total_horas_laboradas'];
                                
                                //pendiente calcular las horas laborales para todos los meses anteriores del empleado
                                $query = "
                                    SELECT 
                                        YEAR(dt.fecha) AS anio,
                                        MONTH(dt.fecha) AS mes,
                                        COUNT(DISTINCT dt.fecha) AS total_dias_laborales,
                                        COUNT(DISTINCT dt.fecha) * 8 AS total_horas_laborales
                                    FROM 
                                        detalle_turno dt
                                    LEFT JOIN 
                                        festivos f ON dt.fecha = f.fecha
                                    WHERE 
                                        DAYOFWEEK(dt.fecha) NOT IN (1)
                                        AND f.fecha IS NULL
                                        AND dt.cod_empresa = ?
                                        AND dt.empleado_id = ?
                                    GROUP BY 
                                        YEAR(dt.fecha), 
                                        MONTH(dt.fecha)
                                    ORDER BY 
                                        anio, mes;
                                ";
                                
                                // Preparar y ejecutar la consulta
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param('ii', $cod_empresa, $empleado_id);
                                $stmt->execute();
                                
                                // Obtener resultados
                                $result_query = $stmt->get_result();
                                
                                // Mostrar resultados
                                while ($row = $result_query->fetch_assoc()) {
                                    $horas_laborales_emp = $row['total_horas_laborales'];
                                }
                                //fin calculo
                                
                                if ($total_horas_laboradas_emp > $horas_laborales_emp){
                                    $Horas_a_Favor_emp = $total_horas_laboradas_emp - $horas_laborales;
                                }else if ($total_horas_laboradas_emp < $horas_laborales_emp){
                                    $Horas_en_Contra_emp = $horas_laborales - $total_horas_laboradas_emp;
                                }
                                
                                $saldo_meses_anteriores = ($Horas_a_Favor_emp + $horas_adicionales_emp) - $Horas_en_Contra_emp;
                            }
                        }
                        //FIN CALCULO DEL SALDO DE LOS MESES ANTERIORES
                        
                        $Saldo_Neto = $saldo_meses_anteriores + ($saldo_mensual);
    
                        // Cálculo de horas semanales aproximadas (usamos 4.33 semanas como promedio de un mes)
                        /*$horas_semanales_aproximadas = $total_horas_laboradas / 4.33;
    
                        // Cálculo de horas extras solo si las horas semanales exceden 48
                        $horas_extras_diurnas = 0;
                        $horas_extras_nocturnas = 0;
                        if ($horas_semanales_aproximadas > 48) {
                            $horas_extras_totales = $horas_semanales_aproximadas - 48;
                            $horas_extras_diurnas = round(min($horas_extras_totales, $total_horas_diurnas / 4.33), 0);
                            $horas_extras_nocturnas = round(max($horas_extras_totales - $horas_extras_diurnas, 0), 0);
                        }*/
    
                        // Cálculo de horas adicionales
                        //$horas_adicionales = 0;
                        $horas_adicionales = max($total_horas_laboradas - $horas_contratadas, 0);
    
                    echo "<tr>
                            <td>{$empleado_id}</td>
                            <td>{$nombre_empleado}</td>
                            <td>{$total_horas_diurnas}</td>
                            <td>{$total_horas_nocturnas}</td>
                            <td>{$total_horas_extras_diurnas}</td>
                            <td>{$total_horas_extras_nocturnas}</td>
                            <td>{$total_horas_laboradas}</td>
                            
                            <td>{$horas_laborales}</td>
                            <td>{$Horas_a_Favor_Mes}</td>
                            <td>{$horas_adicionales_Mes}</td>
                            <td>{$Horas_en_Contra_Mes}</td>
                            <td>{$saldo_mensual}</td> 
                            <td>{$Saldo_Neto}</td>
                            <td>{$horas_novedades}</td>
                        </tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p>No se encontraron resultados para el mes seleccionado.</p>";
            }

            // Cerrar la conexión
            $conn->close();
        }
    ?>

    <script>
        $(document).ready(function() {
            $('#tablaEstadisticas').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Exportar a Excel',
                        title: 'Estadísticas de Horas y Novedades'
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