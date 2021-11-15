<?php

    include_once '../tools/database.php';
    include '../tools/funciones.php';
    include '../tools/utf-8.php';
    $mensaje = '';
    session_start();
    if(!isset($_SESSION['rol']) || !isset($_SESSION['id_user'])){
        header('location: ../login.php');
    }else{
        if($_SESSION['rol'] != 2){
            header('location: ../login.php');
        }
    }
    if(isset($_POST['submit']) && isset($_SESSION['id_user'])  && isset($_SESSION['correo'])){
        if(validar_direc($_POST['direccion'])){
            if(validar_cel($_POST['celular'])){            
                if(validar_tel($_POST['telefono'])){
                    try{
                        $id=$_SESSION['id_user'];
                        $correo=$_SESSION['correo'];
                        $direccion=$_POST['direccion'];
                        $celular=$_POST['celular']; 
                        $telefono=$_POST['telefono'];
                        $database = new Database(); 
                        $query = "UPDATE usuarios SET direccion = :direccion, celular = :celular, telefono = :telefono WHERE id = :id AND correo=:correo;";
                        $stmt = $database->conectar()->prepare($query);
                        $stmt->bindParam(':direccion', $direccion);
                        $stmt->bindParam(':celular', $celular);
                        $stmt->bindParam(':telefono', $telefono);
                        $stmt->bindParam(':id', $id);
                        $stmt->bindParam(':correo', $correo);
                        if ($stmt->execute()) {
                            header('location: ./empleado.php');
                        } else {
                            $mensaje = 'Lo sentimos, ocurrió un error al editar sus datos.';
                        }
                    }catch(Eception $e){
                        $mensaje = "Lo sentimos, ocurrió un error al editar sus datos."; 
                    }
                }else{
                    $mensaje = 'El número de telefono debe contener no menos de 8 digitos.'; 
                }
            }else{
                $mensaje = 'El número de celular debe contener no menos de 8 digitos.'; 
            }           
        }else{
            $mensaje = 'La dirección debe contener entre 8 y 30 carácteres.'; 
        }
    }

   if(isset($_SESSION['id_user'])){
        try{    
            $database = new Database();
            $query = "SELECT  direccion, celular, telefono FROM usuarios WHERE id = :id AND id_rol=2" ;
            $stmt = $database->conectar()->prepare($query);
            $stmt->bindParam(':id',$_SESSION['id_user']);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(PDOEception $error){
            $mensaje = "Lo sentimos, ocurrió un error al editar sus datos."; 
        }
    }else{
        $mensaje = 'Lo sentimos, ocurrió un error al editar sus datos.';
        exit;
    }

?>
<?php include "../tools/head.php" ?>
<body class="form-v10">
    <?php include "../tools/navbar.php" ?>
    <div class="page-content">
		<div class="form-v10-content m-3">
            <?php if ($resultado && $stmt->rowCount()>0) {?>
			<form class="form-detail" method="POST">
				<div class="form-right">
                    <h2>Actualizar mis Datos</h2>
                    <div class="form-row">
                        <?php  foreach ($resultado as $key => $value) { ?>
                            <label class="text-white" for="<?php echo $key; ?>"><?php echo ucfirst($key).': '; ?></label>
                            <input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo escape($value); ?>"><br>
                        <?php } ?>
                    </div>
                    <?php if(!empty($mensaje)){
                        echo '<div class="alert alert-danger alert-dismissible mx-5">';
                        echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                        echo '<strong>Mensaje: </strong>'.$mensaje.'.';
                        echo '</div>';
                    }?>
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
                                        <h5 class="modal-title" id="exampleModalLongTitle">¿Está seguro de actualizar datos?</h5>
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
            <?php }else{
                $mensaje = 'Lo sentimos, ocurrió un error al editar sus datos.';
            }
            ?>

        </div>
    </div>
</body>
<?php include "../tools/footer.php" ?>