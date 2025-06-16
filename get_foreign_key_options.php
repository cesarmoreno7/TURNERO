<?php
    include("session.php");
    include 'db.php';

    $table = $_GET['table'];
    $column = $_GET['column'];

    $query = "SELECT $column FROM $table";
    $result = $conn->query($query);

    $options = [];
    while ($row = $result->fetch_assoc()) {
        $options[] = $row[$column];
    }

    echo json_encode($options);

    $conn->close();
?>
