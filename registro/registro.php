<?php
    include_once '../tools/database.php';
    include_once '../tools/funciones.php';

    $mensaje = '';
    $error_correo = '';
    $error_password = '';
    if(isset($_POST['submit'])){

        if(isset($_POST['nombre']) && isset($_POST['apellido']) && isset($_POST['correo']) && isset($_POST['correo_confirm']) && isset($_POST['password']) && isset($_POST['password_confirm']) && isset($_POST['celular'])){
            
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
                                try{
                                $database = new Database();
                                $query = 'SELECT LOWER(correo) FROM usuarios WHERE correo = :correo';
                                $stmt = $database->conectar()->prepare($query);
                                $stmt->bindParam(':correo', $correo);
                                $stmt->execute();
                                }catch(PDOException $Error) { $mensaje = "Ocurrió un error al cargar los registros."; }
                                if($stmt->rowCount()<1){
                                    try{
                                    $database = new Database();
                                    $query = 'SELECT COUNT(*)+1 FROM usuarios WHERE id_rol = 3';
                                    $stmt = $database->conectar()->prepare($query);
                                    $stmt->execute();
                                    $registro = $stmt->fetch(PDO::FETCH_NUM);
                                    }catch(PDOException $Error) { $mensaje = "Ocurrió un error al cargar los registros."; }
                                    $casillero='';
                                    if($registro == true){

                                        $contador = $registro[0];
                                        $casillero='RE'.str_pad($contador,6, "0", STR_PAD_LEFT);
                                        try{
                                        $database = new Database();
                                        $query = 'INSERT INTO usuarios(nombre, apellido, correo, password, direccion, celular, telefono, casillero, id_rol, id_estado) VALUES (:nombre, :apellido, :correo, :password, :direccion, :celular, :telefono, :casillero, 3, 1)';
                                        $stmt = $database->conectar()->prepare($query);
                                        $stmt->bindParam(':nombre', $nombre);
                                        $stmt->bindParam(':apellido', $apellido);
                                        $stmt->bindParam(':correo', $correo);
                                        $stmt->bindParam(':password', $password);
                                        $stmt->bindParam(':direccion', $direccion);
                                        $stmt->bindParam(':celular', $celular);
                                        $stmt->bindParam(':telefono', $telefono);
                                        $stmt->bindParam(':casillero', $casillero);
                                        }catch(PDOException $Error) { $mensaje = "Ocurrió un error al cargar los registros."; }
                                        if ($stmt->execute()) {
                                            header('location: ../login.php');
                                        } else {
                                            $mensaje = 'Lo sentimos, ocurrió un error al crear su cuenta.';
                                        }
                                        
                                    }

                                }else{
                                    $mensaje = 'Este correo ya esta registrado.';
                                }
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
    <?php include "../tools/navbar-extern.php" ?>
	<div class="page-content">
		<div class="form-v10-content mt-3">
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
                        echo '<strong>Mensaje: </strong>'.$error_correo.'.';
                        echo '</div>';
                    } 
                    ?>
					<div class="form-row">
						<input type="password" name="password" class="input-text" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" placeholder="Contraseña">
					</div>
					<div class="form-row">
						<input type="password" name="password_confirm" class="input-text" required placeholder="Confirmar Contraseña">
					</div>
                    <?php if(!empty($error_password)){
                        echo '<div class="alert alert-warning alert-dismissible mx-5">';
                        echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                        echo '<strong>Mensaje: </strong>'.$error_password.'.';
                        echo '</div>';
                    } 
                    ?>									
				</div>
				<div class="form-right">
					<h2>Datos Generales</h2>
					<div class="form-group">
						<div class="form-row form-row-1">
							<input type="text" name="nombre" class="input-text" required pattern="[A-Za-z]{3,15}" placeholder="Nombre" required>
						</div>
						<div class="form-row form-row-2">
							<input type="text" name="apellido" class="input-text" required pattern="[A-Za-z]{3,15}" placeholder="Apellido" required>
						</div>
					</div>
					<div class="form-row">
						<input type="text" name="direccion" class="street" required pattern="^[#.0-9a-zA-Z\s,-]{8,30}" placeholder="Dirección">
					</div>					
					<div class="form-row">
                        <input type="tel" name="celular" class="phone" required pattern="[0-9]{8}" placeholder="Celular" required>
                    </div>
					<div class="form-row">
						<input type="tel" name="telefono" class="phone" pattern="[0-9]{8}" placeholder="Teléfono">
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
                            <a class="cancel" href="../login.php">Cancelar</a>
                        </div>                   
                        <div class="form-row-last form-row-2">
                            <input type="submit" name="submit" class="register" value="Registrarme">
                        </div>
                    </div>                  					
				</div>                
			</form>
		</div>
	</div>
</body>
<?php include "../tools/footer.php" ?>