<?php
    include 'menu_ppal_maestros.php';
?>
<?php
    include("session.php");
    include 'db.php';
    
    $CC_estado = obtenerEstadoPorLlave('CCOSTO',$cod_empresa);
    $CC_valor = obtenerValorPorLlave('CCOSTO',$cod_empresa);
    
    $servicios = $conn->query("SELECT servicio_id, nombre FROM servicio WHERE cod_empresa = $cod_empresa");
    
    $cod_empresa =  $_SESSION['cod_empresa'];
    
    // Obtener datos para listas desplegables
    $tipo_id = $conn->query("SELECT cod_tipo_id, desc_tipo_id FROM tipo_identificacion");
    $paises = $conn->query("SELECT cod_pais, nom_pais FROM pais");
    $paises_exp = $conn->query("SELECT cod_pais, nom_pais FROM pais");
    
    // Manejar el envío del formulario
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
    
        // Insertar datos en la base de datos
        $sql = "INSERT INTO empleados (nombre, apellido, cod_tipo_id, nro_id, fec_nac, cod_pais_nac, cod_depto_nac, cod_mpio_nac, fecha_exp, cod_pais_exp, cod_depto_exp, cod_mpio_exp, cod_empresa, correo, tel_fijo, celular, est_civil,direccion) 
                VALUES ('$nombre', '$apellido', $cod_tipo_id, '$nro_id', '$fec_nac', $cod_pais_nac, $cod_depto_nac, $cod_mpio_nac, $fecha_exp, $cod_pais_exp, $cod_depto_exp, $cod_mpio_exp, $cod_empresa, '$correo', '$tel_fijo', '$celular', '$est_civil', '$direccion')";
        
        if ($conn->query($sql) === TRUE) {
            // Obtener el ID del último registro insertado
            $empleado_id = $conn->insert_id;
            /*echo "<script>
                            alert('¡Registro exitoso!');
                            window.location.href = 'form_consulta_empleados.php';
                          </script>";*/
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    
    //GRABAR EMPLEADO X SERVICIO
    // Obtener datos para listas desplegables
    //$empleados = $conn->query("SELECT empleado_id, nombre, apellido FROM empleados");
    $servicios = $conn->query("SELECT servicio_id, nombre FROM servicio WHERE cod_empresa = $cod_empresa");
    
    // Manejar el envío del formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //$empleado_id = $_POST['empleado_id'];
        $servicio_id = $_POST['servicio_id'];
        $centro_costo_id = $_POST['centro_costo_id'];
        $grupo_id = $_POST['grupo_id'];
        $estado = $_POST['estado'];
        $fec_ini = $_POST['fec_ini'];
        $fec_fin = $_POST['fec_fin'];
        
        
        // Insertar datos en la base de datos
        $sql1 = "INSERT INTO empleados_servicio (empleado_id, servicio_id, centro_costo_id, grupo_id, estado, fec_ini, fec_fin, cod_empresa) 
                VALUES ($empleado_id, $servicio_id, $centro_costo_id, $grupo_id, '$estado', '$fec_ini', '$fec_fin',$cod_empresa)";
    
        if ($conn->query($sql1) === TRUE) {
             echo "<script>
                    alert('¡Registro exitoso!');
                    window.location.href = 'form_consulta_empleados.php';
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
    <title>Nuevo Empleado</title>
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
        <h2>Nuevo Empleado</h2>
        <form action="create_empleado.php" method="POST">
            <fieldset>
                <legend>Datos básicos</legend>
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Apellido</label>
                    <input type="text" name="apellido" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tipo Identificación</label>
                    <select name="cod_tipo_id" class="form-control" required>
                        <?php while ($row = $tipo_id->fetch_assoc()): ?>
                            <option value="<?php echo $row['cod_tipo_id']; ?>"><?php echo $row['desc_tipo_id']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nro. Identificación</label>
                    <input type="text" name="nro_id" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Fecha de Nacimiento</label>
                    <input type="date" name="fec_nac" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>País de Nacimiento</label>
                    <select name="cod_pais_nac" id="cod_pais_nac" class="form-control" required>
                        <option value="">Seleccione un país</option>
                        <?php while ($row = $paises->fetch_assoc()): ?>
                            <option value="<?php echo $row['cod_pais']; ?>"><?php echo $row['nom_pais']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Departamento de Nacimiento</label>
                    <select name="cod_depto_nac" id="cod_depto_nac" class="form-control" required>
                        <option value="">Seleccione un departamento</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Municipio de Nacimiento</label>
                    <select name="cod_mpio_nac" id="cod_mpio_nac" class="form-control" required>
                        <option value="">Seleccione un municipio</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha Expedición del Documento de Identidad</label>
                    <input type="date" name="fecha_exp" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>País de Expedición</label>
                    <select name="cod_pais_exp" id="cod_pais_exp" class="form-control" required>
                        <option value="">Seleccione un país</option>
                        <?php while ($row = $paises_exp->fetch_assoc()): ?>
                            <option value="<?php echo $row['cod_pais']; ?>"><?php echo $row['nom_pais']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Departamento de Expedición</label>
                    <select name="cod_depto_exp" id="cod_depto_exp" class="form-control" required>
                        <option value="">Seleccione un departamento</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Municipio de Expedición</label>
                    <select name="cod_mpio_exp" id="cod_mpio_exp" class="form-control" required>
                        <option value="">Seleccione un municipio</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Correo Electr&oacute;nico</label>
                    <input type="email" name="correo" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Direcci&oacute;n</label>
                    <input type="text" name="direccion" class="form-control">
                </div>
                <div class="form-group">
                    <label>Teléfono Fijo</label>
                    <input type="text" name="tel_fijo" class="form-control">
                </div>
                <div class="form-group">
                    <label>Celular</label>
                    <input type="number" name="celular" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Estado Civil</label>
                    <select name="est_civil" class="form-control" required>
                        <option value="Soltero(a)">Soltero</option>
                        <option value="Casado(a)">Casado</option>
                        <option value="Viudo(a)">Viudo</option>
                        <option value="Separado(a)">Separado</option>
                        <option value="Union Libre">Unión Libre</option>
                    </select>
                </div>
            </fieldset>
        
            <!-- EMPLEADO X SERVICIO -->
            <fieldset>
                <legend>Datos del servicio</legend>
                <div class="form-group">
                    <label>Servicio</label>
                    <select name="servicio_id" class="form-control" id="servicio" required>
                        <option value="">Seleccione un servicio</option>
                        <?php while ($row = $servicios->fetch_assoc()): ?>
                            <option value="<?php echo $row['servicio_id']; ?>"><?php echo $row['nombre']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div> 
                
                <?php   if ($CC_estado == 1 &&  $CC_valor == "S"){  ?>
                    <div class="form-group">
                        <label>Centro de Costo</label>
                        <select name="centro_costo_id" id="centro_costo" class="form-control" required>
                            <option value="">Seleccione un centro de costo</option>
                        </select>
                    </div>
                <?php  } ?>
                 
                <div class="form-group">
                    <label>Grupo</label>
                    <select name="grupo_id" id="grupo" class="form-control" required>
                        <option value="">Seleccione un grupo</option>
                    </select>
                </div>
                 
                
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado" class="form-control" required>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha de Inicio</label>
                    <input type="date" name="fec_ini" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Fecha de Fin</label>
                    <input type="date" name="fec_fin" class="form-control">
                </div>
            </fieldset>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="form_consulta_empleados.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
<!-- Script para manejar los filtros dinámicos -->
<script>
document.getElementById('servicio').addEventListener('change', function() {
    const servicioId = this.value;
    if (servicioId) {
        fetch(`get_centros_costo.php?servicio_id=${servicioId}`) // Usar GET en lugar de POST
            .then(response => response.text()) // Cambiado de JSON a texto porque se está devolviendo HTML (opciones)
            .then(data => {
                let centroCostoSelect = document.getElementById('centro_costo');
                centroCostoSelect.innerHTML = data; // Colocar directamente las opciones
                centroCostoSelect.dispatchEvent(new Event('change')); // Disparar evento de cambio
            });
    }
});

document.getElementById('centro_costo').addEventListener('change', function() {
    const centroCostoId = this.value;
    if (centroCostoId) {
        fetch(`get_grupos.php?centro_costo_id=${centroCostoId}`) // Usar GET en lugar de POST
            .then(response => response.text()) // Cambiado de JSON a texto porque se está devolviendo HTML (opciones)
            .then(data => {
                let centroCostoSelect = document.getElementById('grupo');
                centroCostoSelect.innerHTML = data; // Colocar directamente las opciones
                centroCostoSelect.dispatchEvent(new Event('change')); // Disparar evento de cambio
            });
    }
});
</script>

<!-- FIN DE EMPLEADO X SERVICIO -->

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
