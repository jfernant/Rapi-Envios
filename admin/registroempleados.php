<?php
    include_once '../tools/database.php';
    include '../tools/funciones.php';
    session_start();

    if(!isset($_SESSION['rol'])){
        header('location: ../login.php');
    }else{
        if($_SESSION['rol'] != 1){
            header('location: ../login.php');
        }
    }
    $mensaje = '';
    $error_correo = '';
    $error_password = '';

    if(isset($_POST['submit'])){

        if(isset($_POST['nombre']) && isset($_POST['apellido']) && isset($_POST['correo']) && isset($_POST['correo_confirm']) && isset($_POST['password']) && isset($_POST['password_confirm']) && isset($_POST['celular']) && isset($_POST['rol'])){
            
            if ($_POST['nombre']!='' && $_POST['apellido']!='' && $_POST['correo']!='' && $_POST['correo_confirm']!='' && $_POST['password']!='' && $_POST['password_confirm']!='' && $_POST['celular']!=''){
                
                if(strcmp($_POST['correo'],$_POST['correo_confirm']) === 0){

                    if(validar_correo($_POST['correo'])){

                        if(strcmp($_POST['password'],$_POST['password_confirm']) === 0){

                            if(validar_clave($_POST['password'],$error_password)){
                        
                                $nombre=trim($_POST['nombre']);
                                $apellido=trim($_POST['apellido']);
                                $correo=trim(strtolower($_POST['correo']));
                                $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
                                $direccion=trim($_POST['direccion']);
                                $celular=trim($_POST['celular']);
                                $telefono=trim($_POST['telefono']);
                                $rol=decrypt($_POST['rol']);
                                try{
                                $database = new Database();
                                $query = 'SELECT LOWER(correo) FROM usuarios WHERE correo = :correo';
                                $stmt = $database->conectar()->prepare($query);
                                $stmt->bindParam(':correo', $correo);
                                $stmt->execute();

                                if($stmt->rowCount()<1){

                                        $database = new Database();
                                        $query = 'INSERT INTO usuarios(nombre, apellido, correo, password, direccion, celular, telefono, casillero, id_rol, id_estado) VALUES (:nombre, :apellido, :correo, :password, :direccion, :celular, :telefono, null, :id_rol, 1)';
                                        $stmt = $database->conectar()->prepare($query);
                                        $stmt->bindParam(':nombre', $nombre);
                                        $stmt->bindParam(':apellido', $apellido);
                                        $stmt->bindParam(':correo', $correo);
                                        $stmt->bindParam(':password', $password);
                                        $stmt->bindParam(':direccion', $direccion);
                                        $stmt->bindParam(':celular', $celular);
                                        $stmt->bindParam(':telefono', $telefono);
                                        $stmt->bindParam(':id_rol', $rol);
                                        if ($stmt->execute()) {
                                            $mensaje = 'Usuario registrado correctamente.';
                                        } else {
                                            $mensaje = 'Lo sentimos, ocurrió un error al registrar al usuario.';
                                        }
                                }else{
                                    $mensaje = 'Este correo ya esta registrado.';
                                }
                                }catch(PDOException $Error) {$mensaje = "Ocurrió un error al cargar los registros."; }
                            }                    
                        }else{
                            $error_password="Las contraseñas deben coincidir.";
                        }
                    }else{
                        $error_correo = 'Debe ingresar una dirección de correo válida';  
                    }
                }else{
                    $error_correo="Los correos electrónicos deben coincidir.";
                }
            }else{        
                $mensaje='Debe llenar los campos requeridos.'; 
            }
        }
    }
?>
<?php include "../tools/head.php" ?> 
<body class="form-v10">
    <?php include "../tools/navbar.php" ?>    
	<div class="page-content">
		<div class="form-v10-content m-3">
			<form class="form-detail" method="POST">
				<div class="form-left">
					<h2>Datos Inicio de Sesión</h2>								
					<div class="form-row">
						<input type="email" name="correo" class="input-text" required pattern="[^@]+@[^@]+.[a-zA-Z]{2,6}" placeholder="Correo Electrónico">
					</div>
					<div class="form-row">
						<input type="email" name="correo_confirm" class="input-text" required placeholder="Confirmar Correo Electrónico">
					</div>
                    <?php if(!empty($error_correo)){
                        echo '<div class="alert alert-warning alert-dismissible mx-5">';
                        echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                        echo '<strong>Error: </strong>'.$error_correo.'.';
                        echo '</div>';
                        } 
                    ?>
					<div class="form-row">
						<input type="password" name="password" class="input-text" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,16}" title="La contraseña debe contener al menos: una mayuscula, una minúscula y un número y una longitud entre 8 y 16 caracteres." placeholder="Contraseña temporal">
					</div>
					<div class="form-row">
						<input type="password" name="password_confirm" class="input-text" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,16}" title="La contraseña debe contener al menos: una mayuscula, una minúscula y un número y una longitud entre 8 y 16 caracteres." placeholder="Confirmar Contraseña temporal">
					</div>
                    <?php if(!empty($error_password)){
                        echo '<div class="alert alert-warning alert-dismissible mx-5">';
                        echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                        echo '<strong>Error: </strong>'.$error_password.'.';
                        echo '</div>';
                        } 
                    ?>
					<div class="form-row">
						<select name="rol" id="rol" required>
						    <option disabled selected value>Rol del empleado</option>
						    <option value="<?php echo encrypt("1")?>">Administrador</option>
                            <option value="<?php echo encrypt("2")?>">Empleado</option>
						</select>
						<span class="select-btn">
						  	<i class="zmdi zmdi-chevron-down"></i>
						</span>
					</div>					
				</div>
				<div class="form-right">
					<h2>Datos Generales</h2>
					<div class="form-group">
						<div class="form-row form-row-1">
							<input type="text" name="nombre" class="input-text" required pattern="[A-Za-z]{3,40}" title="El nombre solo debe contener letras, una longituda entre 3 y 40 caracteres." placeholder="Nombre" required>
						</div>
						<div class="form-row form-row-2">
							<input type="text" name="apellido" class="input-text" required pattern="[A-Za-z]{3,40}" title="El apellido solo debe contener letras, una longituda entre 3 y 40 caracteres." placeholder="Apellido" required>
						</div>
					</div>
					<div class="form-row">
						<input type="text" name="direccion" class="street" required pattern="^[#.0-9a-zA-Z\s,-]{8,100}" title="La dirección solo debe contener letras, números y simbolos .Una longituda entre 8 y 40 caracteres." placeholder="Dirección">
					</div>					
					<div class="form-row">
                        <input type="tel" name="celular" class="phone" required pattern="[0-9]{8}" placeholder="Celular" title="El celular solo debe contener 8 dígitos sin guiones" required>
                    </div>
					<div class="form-row">
						<input type="tel" name="telefono" class="phone" pattern="[0-9]{8}" placeholder="Teléfono" title="El Teléfono solo debe contener 8 dígitos sin guiones">
					</div>			
					<div class="form-checkbox">
						<label class="container"><p>Acepto los <a href="#" class="text">Términos y Condiciones</a> de RapiEnvios.</p>
						  	<input type="checkbox" name="checkbox" required>
						  	<span class="checkmark"></span>
						</label>
					</div>
                    <?php 
                        if(!empty($mensaje)){
                            echo '<div class="alert alert-danger alert-dismissible mx-5">';
                            echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                            echo '<strong>Mensaje: </strong>'.$mensaje.'.';
                            echo '</div>';
                        }
                    ?>                            
                    <div class="form-group">
                        <div class="form-row-last-2 form-row-1">
                            <a class="cancel" href="./admin.php">Cancelar</a>
                        </div>                   
                        <div class="form-row-last form-row-2">
                            <input type="submit" name="submit" class="register" value="Registrar">
                        </div>
                    </div>                  					
				</div>                
			</form>
		</div>
	</div>
</body>
<?php include "../tools/footer.php" ?>