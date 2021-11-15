<?php

    include_once '../tools/database.php';
    include '../tools/utf-8.php';
    include '../tools/funciones.php';
    $mensaje = '';
    session_start();
    if(!isset($_SESSION['rol'])){
        header('location: ../login.php');
    }else{
        if($_SESSION['rol'] != 2){
            header('location: ../login.php');
        }
    }

    // Funcion que se encarga de actualizar el estado de los paquete dependiendo del id estado requerido
    function actualizar($estado_nuevo, $id, $id_requerido){
            include_once '../tools/database.php';
            try{
                $database = new Database(); 
                $query = "UPDATE paquetes SET id_estado = :estado_nuevo WHERE id = :id AND id_estado = :id_requerido";
                $stmt = $database->conectar()->prepare($query);
                $stmt->bindParam(':estado_nuevo', $estado_nuevo);
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':id_requerido', $id_requerido);
                if ($stmt->execute()) {
                    unset($_SESSION['id_paq']); 
                    header('location: ./listadoPaquetes.php');
                } else {
                    $mensaje = 'Lo sentimos, ocurrió un error al editra registro.';
                }
            }catch(PDOException $Error) { $mensaje = "Ocurrió un error al cargar los registros."; }
    }

    // Funcion que se encarga de retornar el estado actual que tiene el paquete
    function estado_actual($id){
            include_once '../tools/database.php';
            try{
            $database = new Database();
            $consulta_estado = "SELECT id_estado FROM paquetes WHERE id = :id";
            $result = $database->conectar()->prepare($consulta_estado);
            $result->bindParam(':id', $id);
            $result->execute();
            $estado = $result->fetch(PDO::FETCH_NUM);
                if ($estado == true) {
                    return $estado_actual= $estado[0];
                }
            }catch(PDOException $Error) { $mensaje = "Ocurrió un error al cargar los registros."; }
    }
    
    if(isset($_POST['submit'])){ 

        if(isset($_POST['estado']) && $id=$_SESSION['id_paq']){
            try{
                $id=$_SESSION['id_paq'];
                $estado_nuevo = decrypt($_POST['estado']);
                $estado_actual = estado_actual($id);
            
                // Dependiendo del estado nuevo elegido se actualizara o no el estado del paquete

                if($estado_nuevo != $estado_actual){ //evalua si el id estado seleccionado es diferente al actual para entrar al switch

                    switch($estado_nuevo){
                            case 3:
                                $id_requerido= 2;
                                if($estado_actual == $id_requerido){
                                    actualizar($estado_nuevo, $id, $id_requerido);
                                }else{
                                    $mensaje = 'Para que el paquete sea enviado a Honduras se requiere que sea recibido en bodega ';
                                }
                                break;
                            case 4:
                                $id_requerido= 3;
                                if($estado_actual == $id_requerido){
                                    actualizar($estado_nuevo, $id, $id_requerido);
                                }else{
                                    $mensaje = 'Para que el paquete este disponible en bodega Honduras se requiere que sea enviado a Honduras';
                                }
                                break;
                            case 5:
                                $id_requerido= 4;
                                if($estado_actual == $id_requerido){
                                    actualizar($estado_nuevo, $id, $id_requerido);
                                }else{
                                    $mensaje = 'Para que el paquete pueda ser entregado al cliente se requiere que este disponible en bodega Honduras';
                                }
                                break;
                            case 6:
                                $id_requerido= 1;
                                if($estado_actual == $id_requerido){
                                    actualizar($estado_nuevo, $id, $id_requerido);
                                }else{
                                    $mensaje = 'Para que el paquete pueda ser Cancelado se requiere que sea prealertado ';
                                }
                                break;
                    }
                }else{
                    $mensaje = 'Seleccione un estado diferente al actual.';
                }
            }catch(PDOEception $error){
                $mensaje =  "Paquete no encontrado.";
            }
        }
    }
    // Obtiene los datos del registro seleccionado para editar
   if(isset($_GET['id'])){
        try{
            $_SESSION['id_paq']=decrypt($_GET['id']);
            $database = new Database();
            $query = "SELECT p.id as 'Paquete', u.casillero as 'Casillero', e.id as 'id_estado' FROM `paquetes` as p JOIN `usuarios`as u ON p.id_cliente = u.id JOIN `roles`as r ON u.id_rol = r.id JOIN `estado_paquete`as e ON p.id_estado = e.id WHERE p.id = :id AND r.id=3";
            $stmt = $database->conectar()->prepare($query);
            $stmt->bindParam(':id',$_SESSION['id_paq']);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(PDOEception $error){
            $mensaje = "Ocurrió un error al cargar los registros.";
        }
    }else{
        unset($_SESSION['id_paq']); 
        $mensaje =  "Hubo un error al cargar el paquete.";
        exit;
    }

?>
<?php include "../tools/head.php" ?>
<body class="form-v10">
<?php include "../tools/navbar.php" ?>
	<div class="page-content">
		<div class="form-v10-content mt-3">
            <?php if ($resultado && $stmt->rowCount()>0) {?>
			<form class="form-detail" method="POST">
                <div class="form-right">
                    <h2>Editar Paquetes</h2>
                    <?php  foreach ($resultado as $key => $value) { ?>
                        <!-- evalua si el campo es el id_estado para colocarlo en un combobox de lo contrario en un input -->
                        <?php  if($key == "id_estado") { ?>
                            <div class="form-row">
                                <label class="text-white" for="id_estado"><?php echo "Estado: "; ?></label>
                                <select name="estado" required>
                                    <?php $estado_actual= $value; ?>
                                    <option disabled value=<?php echo encrypt(2)?> <?php if($estado_actual==2) {echo "selected=\"selected\"";} ?>>Recibido en Bodega</option>
                                    <option value=<?php echo encrypt(3)?> <?php if($estado_actual==3) {echo "selected=\"selected\"";} ?>>Enviado a Honduras</option>
                                    <option value=<?php echo encrypt(4)?> <?php if($estado_actual==4) {echo "selected=\"selected\"";} ?>>Disponible en Bodega Honduras</option>
                                    <option value=<?php echo encrypt(5)?> <?php if($estado_actual==5) {echo "selected=\"selected\"";} ?>>Entregado</option>
                                    <option value=<?php echo encrypt(6)?> <?php if($estado_actual==6) {echo "selected=\"selected\"";} ?>>Cancelado</option>
                                </select>
                                <span class="select-btn">
                                      <i class="zmdi zmdi-chevron-down"></i>
                                </span>
                            </div>
                            <?php } else if($key != "id_estado") {?>
                                <div class="form-row">  
                                    <label class="text-white" for="<?php echo $key; ?>"><?php echo $key.': '; ?></label>                          
                                    <input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo escape($value); ?>" class="input-text" readonly><br>
                                </div>
                            <?php }
                        }
                        if(!empty($mensaje)){
                            echo '<div class="alert alert-danger alert-dismissible mx-5">';
                            echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                            echo '<strong>Mensaje: </strong>'.$mensaje.'.';
                            echo '</div>';
                        }
                        ?>			
                                           
                        <div class="form-group justify-content-center">
                            <div class="form-row-last-2 form-row-1">
                                <a class="cancel" href="./listadoPaquetes.php">Regresar</a>
                            </div>                   
                            <div class="form-row-last form-row-2">
                                <input type="button" class="btn btn-primary register" data-toggle="modal" data-target="#modalconfirmacion" value="Actualizar">
                            </div>
                            <div class="modal fade" id="modalconfirmacion" tabindex="-1" role="dialog" aria-labelledby="modalconfirmacionTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">¿Está seguro de Actualizar el paquete?</h5>
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
                    </div>                  					
				</div>                
			</form>
            <?php 
            }else{
                echo '<p>Paquete no encontrado.</p>';
            }
            ?>
		</div>
	</div>
</body>
<?php include "../tools/footer.php" ?>

            