<?php
    include 'menu_ppal_maestros.php';
?>
<?php
     include("session.php");
     include 'db.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Parámetros</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2>Editar Parámetros</h2>

    <?php
        
        if (isset($_GET['codigo'])) {
            
            $cod_empresa = $_SESSION['cod_empresa'];
            $codigo = $_GET['codigo'];
            $sql = "SELECT * FROM parametros WHERE codigo = ? AND  cod_empresa = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $codigo,$cod_empresa);
            $stmt->execute();
            $result = $stmt->get_result();
            $parametro = $result->fetch_assoc();
    
            if ($parametro) {
                ?>
                <form action="process_editar_parametro.php" method="post">
                    <input type="hidden" name="codigo" value="<?php echo $parametro['codigo']; ?>">
                    <div class="form-group">
                        <label for="descripcion">Descripción:</label>
                        <input type="text" name="descripcion" id="descripcion" class="form-control" value="<?php echo $parametro['descripcion']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="llave">Llave:</label>
                        <input type="text" name="llave" id="llave" class="form-control" value="<?php echo $parametro['llave']; ?>" required disabled>
                    </div>
                    <div class="form-group">
                        <label for="valor">Valor:</label>
                        <input type="text" name="valor" id="valor" class="form-control" value="<?php echo $parametro['valor']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado:</label>
                        <select name="estado" id="estado" class="form-control" required>
                            <option value="1" <?php if ($parametro['estado'] == 1) echo 'selected'; ?>>Activo</option>
                            <option value="0" <?php if ($parametro['estado'] == 0) echo 'selected'; ?>>Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fec_ini">Fecha Inicio:</label>
                        <input type="date" name="fec_ini" id="fec_ini" class="form-control" value="<?php echo $parametro['fec_ini']; ?>" >
                    </div>
                    <div class="form-group">
                        <label for="fec_fin">Fecha Fin:</label>
                        <input type="date" name="fec_fin" id="fec_fin" class="form-control" value="<?php echo $parametro['fec_fin']; ?>" >
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <a href="form_consultar_parametros.php" class="btn btn-secondary">Cancelar</a>
                </form>
                <?php
            } else {
                echo "<div class='alert alert-danger'>Parámetro no encontrado.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Código de parámetro no especificado.</div>";
        }
    
        $conn->close();
    ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
