<?php
    include 'menu_ppal_maestros.php';
?>
<?php
    include("session.php");
    include 'db.php';
    
    $CC_estado = obtenerEstadoPorLlave('CCOSTO');
    $CC_valor = obtenerValorPorLlave('CCOSTO');
    
    // Obtener el ID del personal desde la URL
    $id_personal = $_GET['id'];
    $cod_empresa = $_SESSION['cod_empresa'];
    
    // Obtener datos del personal a editar
    $personal = $conn->query("SELECT * FROM personal WHERE id_personal = $id_personal and cod_empresa = $cod_empresa")->fetch_assoc();
    
    // Obtener datos para listas desplegables
    $empleados = $conn->query("SELECT empleado_id, nombre, apellido FROM empleados");
    $cargos = $conn->query("SELECT cod_cargo, desc_cargo FROM cargo");
    $roles = $conn->query("SELECT cod_rol, desc_rol FROM rol");
    if ($CC_estado == 1 &&  $CC_valor == "S"){
     $centros_costos = $conn->query("SELECT centro_costo_id, nombre FROM centro_costo where cod_empresa = $cod_empresa");
    }
    $tipos_contrato = $conn->query("SELECT cod_tipo_contrato, desc_tipo_contrato FROM tipo_contrato");
    
    // Manejar la actualizaci贸n del formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $empleado_id = $_POST['empleado_id'];
        $cod_cargo = $_POST['cod_cargo'];
        $cod_rol = $_POST['cod_rol'];
        $centro_costo_id = $_POST['centro_costo_id'];
        $cod_tipo_contrato = $_POST['cod_tipo_contrato'];
        $fecha_ingreso = $_POST['fecha_ingreso'];
        //$antiguedad = $_POST['antiguedad'];
        $horas = $_POST['horas'];
        $tiempo_contratado = $_POST['tiempo_contratado'];
        $fecha_retiro = $_POST['fecha_retiro'];
        $motivo_retiro = $_POST['motivo_retiro'];
        
        // Calcular la antigüedad
       
        // Crear objetos DateTime
        $fecha_actual = new DateTime();  // Fecha actual
        $fecha_ingreso_dt = new DateTime($fecha_ingreso);  // Convertir fecha de ingreso a DateTime
        
        // Calcular la diferencia
        $intervalo = $fecha_actual->diff($fecha_ingreso_dt);
        
        // Obtener años, meses y días
        $antiguedad_anos = $intervalo->y;  // Años de diferencia
        $antiguedad_meses = $intervalo->m;  // Meses de diferencia
        $antiguedad_dias = $intervalo->d;  // Días de diferencia

        // Mostrar la antigüedad en el formato "X años, Y meses, Z días"
        $antiguedad = "$antiguedad_anos a&ntilde;os, $antiguedad_meses meses, $antiguedad_dias d&iacute;as.";

        
        //echo $antiguedad;
    
        // Actualizar los datos en la base de datos
        $sql = "UPDATE personal SET 
                empleado_id = $empleado_id, 
                cod_cargo = $cod_cargo, 
                cod_rol = $cod_rol, 
                centro_costo_id = $centro_costo_id, 
                cod_tipo_contrato = $cod_tipo_contrato, 
                fecha_ingreso = '$fecha_ingreso', 
                antiguedad = '$antiguedad', 
                horas = $horas, 
                tiempo_contratado = '$tiempo_contratado', 
                fecha_retiro = '$fecha_retiro', 
                motivo_retiro = '$motivo_retiro', 
                cod_empresa = $cod_empresa 
                WHERE id_personal = $id_personal and cod_empresa = $cod_empresa";
    
        if ($conn->query($sql) === TRUE) {
             echo "<script>
                alert('¡Actualización exitosa!');
                window.location.href = 'form_consulta_personal.php';
              </script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Personal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Editar Personal</h2>
    <form action="update_personal.php?id=<?php echo $id_personal; ?>" method="POST">
        <div class="form-group">
            <label>Empleado</label>
            <select name="empleado_id" class="form-control" required>
                <?php while ($row = $empleados->fetch_assoc()): ?>
                    <option value="<?php echo $row['empleado_id']; ?>" <?php echo ($row['empleado_id'] == $personal['empleado_id']) ? 'selected' : ''; ?>>
                        <?php echo $row['nombre'] . ' ' . $row['apellido']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Cargo</label>
            <select name="cod_cargo" class="form-control" required>
                <?php while ($row = $cargos->fetch_assoc()): ?>
                    <option value="<?php echo $row['cod_cargo']; ?>" <?php echo ($row['cod_cargo'] == $personal['cod_cargo']) ? 'selected' : ''; ?>>
                        <?php echo $row['desc_cargo']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Rol</label>
            <select name="cod_rol" class="form-control" required>
                <?php while ($row = $roles->fetch_assoc()): ?>
                    <option value="<?php echo $row['cod_rol']; ?>" <?php echo ($row['cod_rol'] == $personal['cod_rol']) ? 'selected' : ''; ?>>
                        <?php echo $row['desc_rol']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <?php if ($CC_estado == 1 &&  $CC_valor == "S"){ ?>
            <div class="form-group">
                <label>Centro de Costo</label>
                <select name="centro_costo_id" class="form-control" required>
                    <?php while ($row = $centros_costos->fetch_assoc()): ?>
                        <option value="<?php echo $row['centro_costo_id']; ?>" <?php echo ($row['centro_costo_id'] == $personal['centro_costo_id']) ? 'selected' : ''; ?>>
                            <?php echo $row['nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        <?php } ?>
        <div class="form-group">
            <label>Tipo de Contrato</label>
            <select name="cod_tipo_contrato" class="form-control" required>
                <?php while ($row = $tipos_contrato->fetch_assoc()): ?>
                    <option value="<?php echo $row['cod_tipo_contrato']; ?>" <?php echo ($row['cod_tipo_contrato'] == $personal['cod_tipo_contrato']) ? 'selected' : ''; ?>>
                        <?php echo $row['desc_tipo_contrato']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Fecha de Ingreso</label>
            <input type="date" name="fecha_ingreso" class="form-control" value="<?php echo $personal['fecha_ingreso']; ?>" required>
        </div>
        <div class="form-group">
            <label>Horas</label>
            <input type="number" name="horas" class="form-control" value="<?php echo $personal['horas']; ?>" required>
        </div>
        <div class="form-group">
            <label>Tiempo Contratado</label>
            <input type="text" name="tiempo_contratado" class="form-control" value="<?php echo $personal['tiempo_contratado']; ?>" required>
        </div>
        <div class="form-group">
            <label>Fecha de Retiro</label>
            <input type="date" name="fecha_retiro" class="form-control" value="<?php echo $personal['fecha_retiro']; ?>">
        </div>
        <div class="form-group">
            <label>Motivo de Retiro</label>
            <input type="text" name="motivo_retiro" class="form-control" value="<?php echo $personal['motivo_retiro']; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="form_consulta_personal.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
