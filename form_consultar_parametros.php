<?php
    include 'menu_ppal_maestros.php';
?>

<?php
     include("session.php");
     include 'db.php';
     $conn->set_charset("utf8"); //Para grantizar las tildes
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Par&aacute;metros</title>
    <!--<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">-->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2>Lista de Par&aacute;metros</h2>

    <form method="get" action="form_consultar_parametros.php" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label for="descripcion" class="mr-2">Descripci&oacute;n:</label>
            <input type="text" name="descripcion" id="descripcion" class="form-control" placeholder="Buscar por Descripción" value="<?php echo isset($_GET['descripcion']) ? $_GET['descripcion'] : ''; ?>">
        </div>
        <div class="form-group mr-2">
            <label for="llave" class="mr-2">Llave:</label>
            <input type="text" name="llave" id="llave" class="form-control" placeholder="Buscar por llave" value="<?php echo isset($_GET['llave']) ? $_GET['llave'] : ''; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
        
        <!--<a href="form_registrar_parametro.php" class="btn btn-success ml-2">Crear Nuevo Par&aacute;metro</a>-->
    </form>

    <?php
        
        $cod_empresa = $_SESSION['cod_empresa'];
    
        $descripcion = isset($_GET['descripcion']) ? $_GET['descripcion'] : '';
        $llave = isset($_GET['llave']) ? $_GET['llave'] : '';
    
        $sql = "SELECT p.*
        FROM parametros p
        WHERE (p.descripcion LIKE '%$descripcion%' OR p.llave LIKE '%$llave%') 
        AND p.cod_empresa = $cod_empresa";
        $result = $conn->query($sql);
        
        //echo  $sql;
    
        if ($result->num_rows > 0) {
            echo "<table class='table table-bordered'><tr><th>C&oacute;digo</th><th>Descripci&oacute;n</th><th>Llave</th><th>Valor</th><th>Estado</th><th>Fecha Inicio</th><th>Fecha Fin</th><th>Cod. Empresa</th><th>Acciones</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>".$row["codigo"]."</td>
                        <td>".$row["descripcion"]."</td>
                        <td>".$row["llave"]."</td>
                        <td>".$row["valor"]."</td>
                        <td>".$row["estado"]."</td>
                        <td>".$row["fec_ini"]."</td>
                        <td>".$row["fec_fin"]."</td>
                        <td>".$row["cod_empresa"]."</td>
                        <td><a href='form_editar_parametro.php?codigo=".$row["codigo"]."' class='btn btn-warning btn-sm'>Editar</a></td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='alert alert-info'>No hay resultados</div>";
        }
    
        $conn->close();
    ?>
</div>
<!--
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
-->
</body>
</html>


