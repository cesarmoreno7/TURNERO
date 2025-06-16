<?php
    include 'db.php';
    
    $sql_detalle = $_POST['sql_detalle'];
   // $sql_horas = $_POST['sql_horas'];
						
	//$tabla =  $_POST['sql_detalle'];
	//$sql_tabla = "SELECT * FROM ".$tabla;
	//echo $sql_tabla;
	$query_tabla = $con->query($sql_detalle);
	$tabla = array();
	while ($datos_tabla = $query_tabla->fetch_array(MYSQLI_ASSOC)){	
		$tabla[] = $datos_tabla;	
	}
	if(!empty($tabla)) {
		$filename = $tabla.".xls";
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename);
		$mostrar_columnas = false;
		foreach($tabla as $tabl) {
			if(!$mostrar_columnas) {
				echo implode("\t", array_keys($tabl)) . "\n";
				$mostrar_columnas = true;
			}
			echo implode("\t", array_values($tabl)) . "\n";
		}
	}else{
		echo "No hay datos para exportar";
	}
?>
