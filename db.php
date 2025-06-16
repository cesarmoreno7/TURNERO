<?php
    /*$host = 'localhost';
    $db = 'prodsoft_turnero';
    $user = 'prodsoft_turnero';
    $password = 'turnero2024';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Error en la conexiÃ³n: " . $e->getMessage());
    }*/
    
    $servername = "localhost";
    $username = "prodsoft_turnero";
    $password = "turnero2024";
    $dbname = "prodsoft_turnero";
    
    // Crear conexi¨®n
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Verificar conexi¨®n
    if ($conn->connect_error) {
        die("Conexi¨®n fallida: " . $conn->connect_error);
    }
    
     // Establecer el conjunto de caracteres
    $conn->set_charset("utf8");
?>
