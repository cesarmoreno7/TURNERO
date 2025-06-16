<?php

    include("session.php");

    function conectarBD() {
        $host = 'localhost';
        $db = 'prodsoft_turnero';
        $user = 'prodsoft_turnero';
        $password = 'turnero2024';
    
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Error en la conexión: " . $e->getMessage());
        }
    }
    
    // Función para verificar si una fecha es festiva
    function esFestivo($fecha) {
        $pdo = conectarBD();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM festivos WHERE fecha = :fecha");
        $stmt->execute(['fecha' => $fecha]);
        return $stmt->fetchColumn() > 0;
    }
    
    function calcularHoras($inicio, $fin, $fecha) {
        
        $cod_empresa = $_SESSION['cod_empresa'];
        
        $diurna_inicio = strtotime($fecha . ' 06:00:00');
        $diurna_fin = strtotime($fecha . ' 21:00:00');
        $inicio_ts = strtotime($fecha . ' ' . $inicio);
        $fin_ts = strtotime($fecha . ' ' . $fin);
        // Jornada ordinaria
        $jornada_diurna_inicio = strtotime($fecha . ' 08:00:00');
        $jornada_diurna_fin = strtotime($fecha . ' 18:00:00');
    
        $total_horas = ($fin_ts - $inicio_ts) / 3600;
        
         if ($total_horas < 0){
            $total_horas = $total_horas*(-1);
        }
    
        $horas_diurnas_tot = 0;
        $horas_nocturnas_tot = 0;
    
        if ($fin_ts < $inicio_ts) {
            $fin_ts += 86400; // Añadir 24 horas si la hora de fin es al día siguiente
        }
    
        $horas_diurnas_tot = 0;
        $horas_nocturnas_tot = 0;
        $thed = 0; // Total Horas Extras Diurnas
        $thenoc = 0; // Total Horas Extras Nocturnas
        
        $horas_diurnas_ordinario_ls = 0;
        $horas_nocturnas_ordinario_ls = 0;
        $horas_festivas_diurnas_ls = 0;
        $horas_festivas_nocturnas_ls = 0;
        $horas_diurnas_ordinarias_sd = 0;
        $horas_nocturnas_ordinarias_sd = 0;
        $horas_festivas_diurnas_sd = 0;
        $horas_festivas_nocturnas_sd = 0;
        $horas_diurnas_ordinarias_dlf = 0;
        $horas_nocturnas_ordinarias_dlf = 0;
        $horas_festivas_diurnas_dlf = 0;
        $horas_festivas_nocturnas_dlf = 0;
        $horas_diurnas_ordinarias_dlo_lfmo = 0;
        $horas_nocturnas_ordinarias_dlo_lfmo = 0;
        $horas_festivas_diurnas_dlo_lfmo = 0;
        $horas_festivas_nocturnas_dlo_lfmo = 0;
    
        $dia_semana = date('N', strtotime($fecha));
        $es_festivo = esFestivo($fecha);
        
        
        while ($inicio_ts < $fin_ts) {
            $es_diurno = ($inicio_ts >= $diurna_inicio && $inicio_ts < $diurna_fin);
            
            if ($es_diurno) {
                $horas_diurnas_tot++;
                if ($inicio_ts < $jornada_diurna_inicio || $inicio_ts >= $jornada_diurna_fin || $dia_semana >= 6 || $es_festivo) {
                    $thed++;
                }
            } else {
                $horas_nocturnas_tot++;
                if ($inicio_ts < $jornada_diurna_inicio || $inicio_ts >= $jornada_diurna_fin || $dia_semana >= 6 || $es_festivo) {
                    $then++;
                }
            }
    
            if ($dia_semana >= 1 && $dia_semana <= 5) { // Lunes a Viernes
                if ($es_diurno) {
                    if ($es_festivo) {
                        $horas_festivas_diurnas_ls++;
                    } else {
                        $horas_diurnas_ordinario_ls++;
                    }
                } else {
                    if ($es_festivo) {
                        $horas_festivas_nocturnas_ls++;
                    } else {
                        $horas_nocturnas_ordinario_ls++;
                    }
                }
            } elseif ($dia_semana == 6) { // Sábado
                if ($es_diurno) {
                    $horas_diurnas_ordinarias_sd++;
                } else {
                    $horas_nocturnas_ordinarias_sd++;
                }
            } elseif ($dia_semana == 7) { // Domingo
                if ($es_diurno) {
                    $horas_festivas_diurnas_sd++;
                } else {
                    $horas_festivas_nocturnas_sd++;
                }
            }
    
            $inicio_ts += 3600;
            
             // Si las horas pasan a un nuevo día, actualizar $dia_semana y $es_festivo
            if (date('Y-m-d', $inicio_ts) !== $fecha) {
                $fecha = date('Y-m-d', $inicio_ts);
                $dia_semana = date('N', strtotime($fecha));
                $es_festivo = esFestivo($fecha);
                $diurna_inicio = strtotime($fecha . ' 06:00:00');
                $diurna_fin = strtotime($fecha . ' 21:00:00');
                $jornada_diurna_inicio = strtotime($fecha . ' 08:00:00');
                $jornada_diurna_fin = strtotime($fecha . ' 18:00:00');
            }
            
        }
    
        // Cálculos para horas que pasan de un día a otro
        if ($dia_semana == 7) { // Domingo
            $fechaSiguiente = date('Y-m-d', strtotime('+1 day', strtotime($fecha)));
            $dia_siguiente = date('N', strtotime($fechaSiguiente));
            $es_festivo_siguiente = esFestivo($fechaSiguiente);
    
            if ($dia_siguiente == 1 && $es_festivo_siguiente) { // Domingo a Lunes Festivo
                $horas_diurnas_ordinarias_dlf = $horas_diurnas_tot;
                $horas_nocturnas_ordinarias_dlf = $horas_nocturnas_tot;
            } elseif ($dia_siguiente == 1 && !$es_festivo_siguiente) { // Domingo a Lunes Ordinario
                $horas_diurnas_ordinarias_dlo_lfmo = $horas_diurnas_tot;
                $horas_nocturnas_ordinarias_dlo_lfmo = $horas_nocturnas_tot;
            }
        } elseif ($dia_semana == 1 && esFestivo($fecha)) { // Lunes Festivo
            $fechaSiguiente = date('Y-m-d', strtotime('+1 day', strtotime($fecha)));
            if (date('N', strtotime($fechaSiguiente)) == 2) { // Lunes Festivo a Martes Ordinario
                $horas_diurnas_ordinarias_dlo_lfmo = $horas_diurnas_tot;
                $horas_nocturnas_ordinarias_dlo_lfmo = $horas_nocturnas_tot;
            }
        }
    
        return [
            'total_horas' => $total_horas,
            'horas_diurnas_tot' => $horas_diurnas_tot,
            'horas_nocturnas_tot' => $horas_nocturnas_tot,
            'horas_diurnas_ordinario_ls' => $horas_diurnas_ordinario_ls,
            'horas_nocturnas_ordinario_ls' => $horas_nocturnas_ordinario_ls,
            'horas_festivas_diurnas_ls' => $horas_festivas_diurnas_ls,
            'horas_festivas_nocturnas_ls' => $horas_festivas_nocturnas_ls,
            'horas_diurnas_ordinarias_sd' => $horas_diurnas_ordinarias_sd,
            'horas_nocturnas_ordinarias_sd' => $horas_nocturnas_ordinarias_sd,
            'horas_festivas_diurnas_sd' => $horas_festivas_diurnas_sd,
            'horas_festivas_nocturnas_sd' => $horas_festivas_nocturnas_sd,
            'horas_diurnas_ordinarias_dlf' => $horas_diurnas_ordinarias_dlf,
            'horas_nocturnas_ordinarias_dlf' => $horas_nocturnas_ordinarias_dlf,
            'horas_festivas_diurnas_dlf' => $horas_festivas_diurnas_dlf,
            'horas_festivas_nocturnas_dlf' => $horas_festivas_nocturnas_dlf,
            'horas_diurnas_ordinarias_dlo_lfmo' => $horas_diurnas_ordinarias_dlo_lfmo,
            'horas_nocturnas_ordinarias_dlo_lfmo' => $horas_nocturnas_ordinarias_dlo_lfmo,
            'horas_festivas_diurnas_dlo_lfmo' => $horas_festivas_diurnas_dlo_lfmo,
            'horas_festivas_nocturnas_dlo_lfmo' => $horas_festivas_nocturnas_dlo_lfmo,
            'thed' => $thed,
            'thenoc' => $thenoc
        ];
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];
    
        $pdo = conectarBD();
    
        // Preparar la consulta
        $stmtTurnos = $pdo->prepare("SELECT * FROM turnos WHERE cod_empresa = :cod_empresa");
        
        // Asignar valor al parámetro
        $stmtTurnos->bindParam(':cod_empresa', $cod_empresa, PDO::PARAM_STR);
        
        // Ejecutar la consulta
        $stmtTurnos->execute();
        
        // Obtener los resultados
        $turnos = $stmtTurnos->fetchAll(PDO::FETCH_ASSOC);
    
        $fechaActual = strtotime($fecha_inicio);
        $fechaFinal = strtotime($fecha_fin);
    
        while ($fechaActual <= $fechaFinal) {
            $fecha = date('Y-m-d', $fechaActual);
    
            foreach ($turnos as $turno) {
                
                // Verificar si ya existe un registro con el mismo cod_turno y fecha
                $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM horas_turnos WHERE cod_turno = :cod_turno AND fecha = :fecha AND cod_empresa = :cod_empresa");
                $stmtCheck->execute([
                    ':cod_turno' => $turno['cod_turno'],
                    ':fecha' => $fecha,
                    ':cod_empresa' => $cod_empresa
                ]);
                $count = $stmtCheck->fetchColumn();
                
                if ($count == 0) {
                    
                    $horas = calcularHoras($turno['hora_inicio'], $turno['hora_fin'], $fecha);
        
                    $stmtInsert = $pdo->prepare("
                        INSERT INTO horas_turnos (
                            cod_turno, fecha, total_horas, horas_diurnas_tot, horas_nocturnas_tot,
                            horas_diurnas_ordinario_ls, horas_nocturnas_ordinario_ls, horas_festivas_diurnas_ls, horas_festivas_nocturnas_ls,
                            horas_diurnas_ordinarias_sd, horas_nocturnas_ordinarias_sd, horas_festivas_diurnas_sd, horas_festivas_nocturnas_sd,
                            horas_diurnas_ordinarias_dlf, horas_nocturnas_ordinarias_dlf, horas_festivas_diurnas_dlf, horas_festivas_nocturnas_dlf,
                            horas_diurnas_ordinarias_dlo_lfmo, horas_nocturnas_ordinarias_dlo_lfmo, horas_festivas_diurnas_dlo_lfmo, horas_festivas_nocturnas_dlo_lfmo,
                            thed, thenoc, cod_empresa,empleado_id
                        ) VALUES (
                            :cod_turno, :fecha, :total_horas, :horas_diurnas_tot, :horas_nocturnas_tot,
                            :horas_diurnas_ordinario_ls, :horas_nocturnas_ordinario_ls, :horas_festivas_diurnas_ls, :horas_festivas_nocturnas_ls,
                            :horas_diurnas_ordinarias_sd, :horas_nocturnas_ordinarias_sd, :horas_festivas_diurnas_sd, :horas_festivas_nocturnas_sd,
                            :horas_diurnas_ordinarias_dlf, :horas_nocturnas_ordinarias_dlf, :horas_festivas_diurnas_dlf, :horas_festivas_nocturnas_dlf,
                            :horas_diurnas_ordinarias_dlo_lfmo, :horas_nocturnas_ordinarias_dlo_lfmo, :horas_festivas_diurnas_dlo_lfmo, :horas_festivas_nocturnas_dlo_lfmo,
                            :thed, :thenoc, :cod_empresa, :empleado_id
                        )
                    ");
        
                    $stmtInsert->execute([
                        ':cod_turno' => $turno['cod_turno'],
                        ':fecha' => $fecha,
                        ':total_horas' => $horas['total_horas'],
                        ':horas_diurnas_tot' => $horas['horas_diurnas_tot'],
                        ':horas_nocturnas_tot' => $horas['horas_nocturnas_tot'],
                        ':horas_diurnas_ordinario_ls' => $horas['horas_diurnas_ordinario_ls'],
                        ':horas_nocturnas_ordinario_ls' => $horas['horas_nocturnas_ordinario_ls'],
                        ':horas_festivas_diurnas_ls' => $horas['horas_festivas_diurnas_ls'],
                        ':horas_festivas_nocturnas_ls' => $horas['horas_festivas_nocturnas_ls'],
                        ':horas_diurnas_ordinarias_sd' => $horas['horas_diurnas_ordinarias_sd'],
                        ':horas_nocturnas_ordinarias_sd' => $horas['horas_nocturnas_ordinarias_sd'],
                        ':horas_festivas_diurnas_sd' => $horas['horas_festivas_diurnas_sd'],
                        ':horas_festivas_nocturnas_sd' => $horas['horas_festivas_nocturnas_sd'],
                        ':horas_diurnas_ordinarias_dlf' => $horas['horas_diurnas_ordinarias_dlf'],
                        ':horas_nocturnas_ordinarias_dlf' => $horas['horas_nocturnas_ordinarias_dlf'],
                        ':horas_festivas_diurnas_dlf' => $horas['horas_festivas_diurnas_dlf'],
                        ':horas_festivas_nocturnas_dlf' => $horas['horas_festivas_nocturnas_dlf'],
                        ':horas_diurnas_ordinarias_dlo_lfmo' => $horas['horas_diurnas_ordinarias_dlo_lfmo'],
                        ':horas_nocturnas_ordinarias_dlo_lfmo' => $horas['horas_nocturnas_ordinarias_dlo_lfmo'],
                        ':horas_festivas_diurnas_dlo_lfmo' => $horas['horas_festivas_diurnas_dlo_lfmo'],
                        ':horas_festivas_nocturnas_dlo_lfmo' => $horas['horas_festivas_nocturnas_dlo_lfmo'],
                        ':thed' => $horas['thed'],
                        ':thenoc' => $horas['thenoc'],
                        ':cod_empresa' => $cod_empresa,
                        ':empleado_id' => $empleado_id
                    ]);
                }
            }
    
            $fechaActual = strtotime('+1 day', $fechaActual);
        }
    
        echo "Proceso completado por favor verifique la tabla: horas_turnos.";
    }
?>
