<?php
/**
 * Función para recuperar el estado basado en la llave de la tabla parametros.
 *
 * @param string $llave La llave para buscar el estado correspondiente.
 * @return string|null El valor encontrado o null si no se encuentra.
 */
function obtenerEstadoPorLlave($llave) {
    // Conexión a la base de datos
    include("session.php");
    include 'db.php';
    
    $cod_empresa = $_SESSION['cod_empresa'];

    // Establecer la codificación a UTF-8
    $conn->set_charset("utf8");

    // Preparar la consulta SQL
    $stmt = $conn->prepare("SELECT estado FROM parametros WHERE llave = ? AND cod_empresa = ?");
    if ($stmt) {
        // Vincular el parámetro y ejecutar la consulta
        $stmt->bind_param("si", $llave,$cod_empresa);
        $stmt->execute();
        $stmt->bind_result($estado);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    // Cerrar la conexión a la base de datos
    $conn->close();

    // Devolver el valor encontrado
    return $estado;
}

// Ejemplo de uso
/*$llave = 'OFE';
$valor = obtenerValorPorLlave($llave);
if ($valor !== null) {
    echo "El valor para la llave '$llave' es: $valor";
} else {
    echo "No se encontró ningún valor para la llave '$llave'";
}*/
?>
