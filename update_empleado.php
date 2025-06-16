<?php
    include 'menu_ppal_maestros.php';
?>
<?php
    include 'db.php';
    
    // Obtener el ID del empleado desde la URL
    $empleado_id = $_GET['id'];
    
    $cod_empresa = $_SESSION['cod_empresa'];
    
    // Obtener datos del empleado a editar en empleados
    $empleado = $conn->query("SELECT * FROM empleados WHERE empleado_id = $empleado_id and cod_empresa = $cod_empresa")->fetch_assoc();
    
    // Obtener datos para listas desplegables
    $tipo_id = $conn->query("SELECT cod_tipo_id, desc_tipo_id FROM tipo_identificacion");
    $paises = $conn->query("SELECT cod_pais, nom_pais FROM pais");
    $empresas = $conn->query("SELECT cod_empresa, nom_empresa FROM empresa");
    
    // Obtener datos del registro a editar en empleados_servicio
    $empleado_servicio = "SELECT * FROM empleados_servicio WHERE empleado_id = $empleado_id AND estado = 'Activo' and cod_empresa = $cod_empresa";
    //echo $empleado_servicio;
    $result = mysqli_query($conn, $empleado_servicio);
    $empleado_servicios = mysqli_fetch_assoc($result);
    
    // Manejar la actualización del formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $cod_tipo_id = $_POST['cod_tipo_id'];
        $nro_id = $_POST['nro_id'];
        $fec_nac = $_POST['fec_nac'];
        $cod_pais_nac = $_POST['cod_pais_nac'];
        $cod_depto_nac = $_POST['cod_depto_nac'];
        $cod_mpio_nac = $_POST['cod_mpio_nac'];
        $fecha_exp = $_POST['fecha_exp'];
        $cod_pais_exp = $_POST['cod_pais_exp'];
        $cod_depto_exp = $_POST['cod_depto_exp'];
        $cod_mpio_exp = $_POST['cod_mpio_exp'];
        $cod_empresa = $_POST['cod_empresa'];
        $correo = $_POST['correo'];
        $tel_fijo = $_POST['tel_fijo'];
        $celular = $_POST['celular'];
        $est_civil = $_POST['est_civil'];
        $direccion = $_POST['direccion'];
    
        // Actualizar los datos en la base de datos
        $sql = "UPDATE empleados SET 
                nombre = '$nombre', 
                apellido = '$apellido', 
                cod_tipo_id = $cod_tipo_id, 
                nro_id = '$nro_id', 
                fec_nac = '$fec_nac', 
                cod_pais_nac = $cod_pais_nac, 
                cod_depto_nac = $cod_depto_nac, 
                cod_mpio_nac = $cod_mpio_nac, 
                fecha_exp = '$fecha_exp', 
                cod_pais_exp = $cod_pais_exp, 
                cod_depto_exp = $cod_depto_exp, 
                cod_mpio_exp = $cod_mpio_exp,
                correo = '$correo', 
                tel_fijo = '$tel_fijo', 
                celular = '$celular', 
                est_civil = '$est_civil',
                direccion = '$direccion' 
                WHERE empleado_id = $empleado_id";
        
        // Manejar la actualizaci贸n del formulario empleados_servicio
        $estado = $_POST['estado'];
        $fec_ini = $_POST['fec_ini'];
        $fec_fin = $_POST['fec_fin'];
    
        // Actualizar los datos en la base de datos
        $sql1 = "UPDATE empleados_servicio SET 
                estado = '$estado', 
                fec_ini = '$fec_ini', 
                fec_fin = '$fec_fin' 
                WHERE empleado_id = $empleado_id AND estado = 'Activo' and cod_empresa = $cod_empresa";
    
        if ($conn->query($sql) === TRUE && $conn->query($sql1) === TRUE) {
            echo "<script>
                    alert('¡Actualización exitosa!');
                    window.location.href = 'form_consulta_empleados.php';
                  </script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    
    // Obtener datos iniciales de departamentos y municipios según los valores del empleado
    $deptos_nac = $conn->query("SELECT cod_departamento, nom_departamento FROM departamento WHERE cod_pais = '{$empleado['cod_pais_nac']}'");
    $mpios_nac = $conn->query("SELECT cod_municipio, nom_municipio FROM municipio WHERE cod_departamento = '{$empleado['cod_depto_nac']}'");
    
    $deptos_exp = $conn->query("SELECT cod_departamento, nom_departamento FROM departamento WHERE cod_pais = '{$empleado['cod_pais_exp']}'");
    $mpios_exp = $conn->query("SELECT cod_municipio, nom_municipio FROM municipio WHERE cod_departamento = '{$empleado['cod_depto_exp']}'");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Empleado</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        fieldset {
            border: 2px solid #007bff;
            border-radius: 5px;
            padding: 15px;
        }
        legend {
            font-size: 1.2rem;
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2>Editar Empleado</h2>
    <form action="update_empleado.php?id=<?php echo $empleado_id; ?>" method="POST">
        <fieldset>
            <legend>Datos básicos</legend>
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo $empleado['nombre']; ?>" required>
            </div>
            <div class="form-group">
                <label>Apellido</label>
                <input type="text" name="apellido" class="form-control" value="<?php echo $empleado['apellido']; ?>" required>
            </div>
            <div class="form-group">
                <label>Tipo Identificación</label>
                <select name="cod_tipo_id" class="form-control" required>
                    <?php while ($row = $tipo_id->fetch_assoc()): ?>
                        <option value="<?php echo $row['cod_tipo_id']; ?>" <?php echo ($row['cod_tipo_id'] == $empleado['cod_tipo_id']) ? 'selected' : ''; ?>>
                            <?php echo $row['desc_tipo_id']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Nro Identificación</label>
                <input type="text" name="nro_id" class="form-control" value="<?php echo $empleado['nro_id']; ?>" required>
            </div>
            <div class="form-group">
                <label>Fecha de Nacimiento</label>
                <input type="date" name="fec_nac" class="form-control" value="<?php echo $empleado['fec_nac']; ?>" required>
            </div>
            <div class="form-group">
                <label>País de Nacimiento</label>
                <select name="cod_pais_nac" id="cod_pais_nac" class="form-control" required>
                    <option value="">Seleccione un país</option>
                    <?php while ($row = $paises->fetch_assoc()): ?>
                        <option value="<?php echo $row['cod_pais']; ?>" <?php echo ($row['cod_pais'] == $empleado['cod_pais_nac']) ? 'selected' : ''; ?>>
                            <?php echo $row['nom_pais']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Departamento de Nacimiento</label>
                <select name="cod_depto_nac" id="cod_depto_nac" class="form-control" required>
                    <option value="">Seleccione un departamento</option>
                    <?php while ($row = $deptos_nac->fetch_assoc()): ?>
                        <option value="<?php echo $row['cod_departamento']; ?>" <?php echo ($row['cod_departamento'] == $empleado['cod_depto_nac']) ? 'selected' : ''; ?>>
                            <?php echo $row['nom_departamento']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Municipio de Nacimiento</label>
                <select name="cod_mpio_nac" id="cod_mpio_nac" class="form-control" required>
                    <option value="">Seleccione un municipio</option>
                    <?php while ($row = $mpios_nac->fetch_assoc()): ?>
                        <option value="<?php echo $row['cod_municipio']; ?>" <?php echo ($row['cod_municipio'] == $empleado['cod_mpio_nac']) ? 'selected' : ''; ?>>
                            <?php echo $row['nom_municipio']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Fecha de Expedición</label>
                <input type="date" name="fecha_exp" class="form-control" value="<?php echo $empleado['fecha_exp']; ?>" required>
            </div>
            <div class="form-group">
                <label>País de Expedición</label>
                <select name="cod_pais_exp" id="cod_pais_exp" class="form-control" required>
                    <option value="">Seleccione un país</option>
                    <?php foreach ($paises as $row): ?>
                        <option value="<?php echo $row['cod_pais']; ?>" <?php echo ($row['cod_pais'] == $empleado['cod_pais_exp']) ? 'selected' : ''; ?>>
                            <?php echo $row['nom_pais']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Departamento de Expedición</label>
                <select name="cod_depto_exp" id="cod_depto_exp" class="form-control" required>
                    <option value="">Seleccione un departamento</option>
                    <?php while ($row = $deptos_exp->fetch_assoc()): ?>
                        <option value="<?php echo $row['cod_departamento']; ?>" <?php echo ($row['cod_departamento'] == $empleado['cod_depto_exp']) ? 'selected' : ''; ?>>
                            <?php echo $row['nom_departamento']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Municipio de Expedición</label>
                <select name="cod_mpio_exp" id="cod_mpio_exp" class="form-control" required>
                    <option value="">Seleccione un municipio</option>
                    <?php while ($row = $mpios_exp->fetch_assoc()): ?>
                        <option value="<?php echo $row['cod_municipio']; ?>" <?php echo ($row['cod_municipio'] == $empleado['cod_mpio_exp']) ? 'selected' : ''; ?>>
                            <?php echo $row['nom_municipio']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Correo</label>
                <input type="email" name="correo" class="form-control" value="<?php echo $empleado['correo']; ?>" required>
            </div>
             <div class="form-group">
                <label>Direcci&oacute;n</label>
                <input type="text" name="direccion" class="form-control" value="<?php echo $empleado['direccion']; ?>">
            </div>
            <div class="form-group">
                <label>Teléfono Fijo</label>
                <input type="number" name="tel_fijo" class="form-control" value="<?php echo $empleado['tel_fijo']; ?>">
            </div>
            <div class="form-group">
                <label>Celular</label>
                <input type="number" name="celular" class="form-control" value="<?php echo $empleado['celular']; ?>" required>
            </div>
            <div class="form-group">
                <label>Estado Civil</label>
                <select name="est_civil" class="form-control" required>
                    <option value="Soltero" <?php echo ($empleado['est_civil'] == 'Soltero') ? 'selected' : ''; ?>>Soltero(a)</option>
                    <option value="Casado" <?php echo ($empleado['est_civil'] == 'Casado') ? 'selected' : ''; ?>>Casado(a)</option>
                    <option value="Viudo" <?php echo ($empleado['est_civil'] == 'Viudo') ? 'selected' : ''; ?>>Viudo(a)</option>
                    <option value="Separado" <?php echo ($empleado['est_civil'] == 'Separado') ? 'selected' : ''; ?>>Separado(a)</option>
                    <option value="Union Libre" <?php echo ($empleado['est_civil'] == 'Union Libre') ? 'selected' : ''; ?>>Unión Libre</option>
                </select>
            </div>
        </fieldset>
        
        <!-- EMPLEADO X SERVICIO -->
        <fieldset>
            <legend>Datos del servicio</legend>
            <div class="form-group">
                <label>Estado</label>
                <select name="estado" class="form-control" required>
                    <option value="Activo" <?php echo ($empleado_servicios['estado'] == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                    <option value="Inactivo" <?php echo ($empleado_servicios['estado'] == 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
            <div class="form-group">
                <label>Fecha de Inicio</label>
                <input type="date" name="fec_ini" class="form-control" value="<?php echo $empleado_servicios['fec_ini']; ?>" required>
            </div>
            <div class="form-group">
                <label>Fecha de Fin</label>
                <input type="date" name="fec_fin" class="form-control" value="<?php echo $empleado_servicios['fec_fin']; ?>">
            </div>
        </fieldset>
        
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="form_consulta_empleados.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
    // Filtrar departamentos y municipios en función del país seleccionado
    $(document).ready(function() {
        $('#cod_pais_nac').change(function() {
            let cod_pais = $(this).val();
            $.ajax({
                url: 'get_departamentos.php',
                method: 'POST',
                data: {cod_pais: cod_pais},
                success: function(response) {
                    $('#cod_depto_nac').html(response);
                    $('#cod_mpio_nac').html('<option value="">Seleccione un municipio</option>');
                }
            });
        });

        $('#cod_depto_nac').change(function() {
            let cod_depto = $(this).val();
            $.ajax({
                url: 'get_municipios.php',
                method: 'POST',
                data: {cod_depto: cod_depto},
                success: function(response) {
                    $('#cod_mpio_nac').html(response);
                }
            });
        });

        $('#cod_pais_exp').change(function() {
            let cod_pais = $(this).val();
            $.ajax({
                url: 'get_departamentos.php',
                method: 'POST',
                data: {cod_pais: cod_pais},
                success: function(response) {
                    $('#cod_depto_exp').html(response);
                    $('#cod_mpio_exp').html('<option value="">Seleccione un municipio</option>');
                }
            });
        });

        $('#cod_depto_exp').change(function() {
            let cod_depto = $(this).val();
            $.ajax({
                url: 'get_municipios.php',
                method: 'POST',
                data: {cod_depto: cod_depto},
                success: function(response) {
                    $('#cod_mpio_exp').html(response);
                }
            });
        });
    });
</script>
</body>
</html>
