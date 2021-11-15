<?php
    include_once '../tools/database.php';
    include_once '../tools/funciones.php';
    
    session_start();
    $mensaje = '';
    $error_encontrado='';
    if(isset( $_SESSION['id_user'])!=true &&  $_SESSION['correo']!=true){
        header('location: ../login.php');
    }
    
    if(isset($_POST['submit']) && isset($_POST['password']) && isset($_POST['new_password']) && isset($_POST['password_confirm'])){
        if ($_POST['password']!='' && $_POST['new_password']!='' && $_POST['password_confirm']!=''){
            if($_POST['password']!=$_POST['new_password']){
                if(strtolower($_POST['password'])!=strtolower($_POST['new_password'])){
                    if(strcmp($_POST['new_password'],$_POST['password_confirm']) === 0){
                        $id = $_SESSION['id_user'];
                        $correo = $_SESSION['correo'];
                        $password = $_POST['password'];
                        try{
                        $database = new Database();
                        $query = 'SELECT password FROM usuarios WHERE id=:id AND id_estado=1 AND BINARY correo = :correo';
                        $stmt = $database->conectar()->prepare($query);
                        $stmt->bindParam(':id', $id);
                        $stmt->bindParam(':correo', $correo);
                        $stmt->execute();
                        $registro = $stmt->fetch(PDO::FETCH_NUM);
                        }catch(PDOException $Error) { $mensaje = "Ocurrió un error al cargar los registros."; }
                        if($registro == true){
                            $hashed_password = $registro[0];

                            if(password_verify($password, $hashed_password)){
                                try{
                                $database = new Database();
                                $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
                                $query = "UPDATE usuarios SET password=:new_password WHERE id=:id AND id_estado=1 AND BINARY correo = :correo";
                                $stmt = $database->conectar()->prepare($query);
                                $stmt->bindParam(':new_password', $new_password);
                                $stmt->bindParam(':id', $id);
                                $stmt->bindParam(':correo', $correo);
                                }catch(PDOException $Error) { $mensaje = "Ocurrió un error al cargar los registros."; }
                                if ($stmt->execute()) {
                                    header('location: ../logout.php');
                                } else {
                                    $mensaje = 'Lo sentimos, ocurrió un error al cambiar contraseña.';
                                }

                            }else{
                                $mensaje = 'Contraseña anterior incorrecta.';
                            }

                        }else{
                            $mensaje = 'Contraseña anterior incorrecta.';
                        }
                    }else{
                        $mensaje = $error_encontrado;
                    }

                }else{
                    $mensaje="Las contraseñas deben coincidir.";
                }
            }else{
                $mensaje="La contraseña nueva no puede ser igual que la anterior.";
            }
        }else{
            $mensaje = 'Debe llenar los campos requeridos.';
        }
    }
?>

<?php include "../tools/head.php" ?>
<body class="form-v10">
<?php include "../tools/navbar.php" ?>
    <div class="page-content">
		<div class="form-v10-content mt-4">
			<form class="form-detail" method="POST">
				<div class="form-right">
                    <h2>Cambio de contraseña</h1>
                    <!--  -->
                    <div class="form-row">
                        <label class="text-white" for="password">Contraseña anterior:</label><br/>
                        <input type="password" name="password" required><br/><br/>
					</div>

                    <div class="form-row">
                        <label class="text-white" for="new_password">Contraseña nueva:</label><br/>
                        <input type="password" name="new_password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,16}" title="La contraseña debe contener al menos: una mayuscula, una minúscula y un número y una longitud entre 8 y 16 caracteres." placeholder="Contraseña"><br/>
                    </div>

                    <div class="form-row">
                        <label class="text-white" for="password_confirm">Confirmar Contraseña:</label><br/>
                        <input type="password" name="password_confirm" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,16}" title="La contraseña debe contener al menos: una mayuscula, una minúscula y un número y una longitud entre 8 y 16 caracteres." placeholder="Contraseña"><br/>
                    </div>
                        <?php if(!empty($mensaje)){
                            echo '<div class="alert alert-danger alert-dismissible mx-5">';
                            echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                            echo '<strong>Mensaje: </strong>'.$mensaje.'.';
                            echo '</div>';
                        }?>
                    <!--  -->
                    <div class="form-group justify-content-center">
                        <div class="form-row-last-2 form-row-1">
                            <a class="cancel" href="./empleado.php">Regresar</a>
                        </div>                   
                        <div class="form-row-last form-row-2">
                            <input type="button" class="btn btn-primary register" data-toggle="modal" data-target="#modalconfirmacion" value="Actualizar">
                        </div>
                        <div class="modal fade" id="modalconfirmacion" tabindex="-1" role="dialog" aria-labelledby="modalconfirmacionTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                     <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">¿Está seguro de actualizar contraseña?</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit"  class="btn btn-success" name="submit">Actualizar</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  -->
                </div>
            </form>
        </div>
    </div>
</body>
<?php include "../tools/footer.php" ?>