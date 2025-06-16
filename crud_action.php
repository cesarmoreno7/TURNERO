<?php
    include("session.php");
    include 'db.php';

    $tabla = $_POST['tabla'];
    $action = $_GET['action'];

    if ($action == 'create' || $action == 'update') {
        $columns = array_keys($_POST);
        $values = array_values($_POST);
        
        if ($action == 'create') {
            $query = "INSERT INTO $tabla (" . implode(', ', $columns) . ") VALUES ('" . implode("', '", $values) . "')";
        } else if ($action == 'update') {
            $id = $_POST[$columns[0]];
            $setQuery = [];
            for ($i = 1; $i < count($columns); $i++) {
                $setQuery[] = "{$columns[$i]} = '{$values[$i]}'";
            }
            $query = "UPDATE $tabla SET " . implode(', ', $setQuery) . " WHERE {$columns[0]} = '$id'";
        }
        
        if ($conn->query($query) === TRUE) {
            echo "Registro guardado/actualizado exitosamente";
        } else {
            echo "Error: " . $conn->error;
        }
    } else if ($action == 'delete') {
        $id = $_GET['id'];
        $query = "DELETE FROM $tabla WHERE id = '$id'";
        
        if ($conn->query($query) === TRUE) {
            echo "Registro eliminado exitosamente";
        } else {
            echo "Error: " . $conn->error;
        }
    }

    $conn->close();
    header("Location: form_crud_dinamico.php"); // Redirigir de nuevo al CRUD principal
?>
