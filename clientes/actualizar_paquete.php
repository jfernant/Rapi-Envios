<?php
    include_once '../tools/database.php';
    include '../tools/utf-8.php';
    include '../tools/funciones.php';
    session_start();

     if(!isset($_SESSION['rol'])){
         header('location: ../login.php');
     }else{
         if($_SESSION['rol'] != 3){
             header('location: ../login.php');
         }
     }
    $mensaje = '';

        if(isset($_POST['submit'])){
            
            if(validar_direc($_POST['intrucciones_envio'])){
               if(validar_num($_POST['precio'])){
                    try{
                        $id=$_SESSION['id_paquete_edit'];
                        unset($_SESSION['id_paquete_edit']);             
                        $precio=$_POST['precio'];
                        $id_tarifa=decrypt($_POST['tarifa']);
                        $intrucciones_envio=$_POST['intrucciones_envio'];
                        
                        $database = new Database();
                        $consulta = "UPDATE paquetes SET precio = :precio, id_tarifa = :id_tarifa, intrucciones_envio = :intrucciones_envio WHERE id = :id";
                        $stmt = $database->conectar()->prepare($consulta);
                        $stmt->bindParam(':precio', $precio);
                        $stmt->bindParam(':id_tarifa', $id_tarifa);
                        $stmt->bindParam(':intrucciones_envio', $intrucciones_envio);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();
                        header('location: ./listadopaquete.php');

                    }catch(PDOEception $error){
                        unset($_SESSION['id_paquete_edit']);
                        $mensaje = "Ocurrió un error al cargar los registros";
                    } 
                }else{
                    $mensaje = 'El precio ingresado debe ser un valor entero o decimal mayor a 0';
                } 
            }else{
                $mensaje = 'La instrucción debe contener entre 8 y 30 carácteres'; 
            }            
            
        }      

    if(isset($_GET['id'])){        
        try{
            $_SESSION['id_paquete_edit']=decrypt($_GET['id']);
            $database = new Database();
            $consulta  = "SELECT p.intrucciones_envio,p.precio, p.id_tarifa as 'id_tarifa' FROM `paquetes` as p JOIN tarifas as t ON p.id_tarifa = t.id WHERE p.id = :id";
            $stmt = $database->conectar()->prepare($consulta);
            $stmt->bindParam(':id',$_SESSION['id_paquete_edit']);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(PDOEception $error){
            unset($_SESSION['id_paquete_edit']);
            $mensaje= "Paquete no encontrado.";
        }
    }else{
        unset($_SESSION['id_paquete_edit']);
        $mensaje= "Hubo un error al cargar registro.";
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
                    <h2>Editar Paquetes</h2>
                    <form class="form-detail" method="POST">
                        <?php  foreach ($resultado as $key => $value) { ?>
                            <?php  if($key != "id_tarifa") { ?>
                                <?php $nombre = $key=='intrucciones_envio' ? 'Intrucciones de envío' : $key;?>
                                <div class="form-row">
                                    <br><label class="text-white" for="<?php echo $key; ?>"><?php echo ucfirst($nombre).':'; ?></label>                                 
                                    <br><input type="<?php echo ($key === 'id'? 'hidden': 'text'); ?>" name="<?php echo $key; ?>" id="<?php echo $key; ?>" class="input-text" value="<?php echo escape($value); ?>"
                                    <?php echo ($key==='tarifa'? 'readonly':null); ?>><br>
                                </div>
                            <?php } else{
                                    $tarifa_actual= $value;
                                    echo '<div class="form-row">'; 
                                        echo '<br><label class="text-white" for="tarifa">Tarifa:</label><br>';
                                        echo '<select required name="tarifa" id="tarifa">';
                                        try{
                                            $database = new Database();
                                            $query = 'SELECT id, tarifa FROM tarifas';
                                            $stmt = $database->conectar()->prepare($query);
                                            $stmt->execute();
                                            $resultado = $stmt->fetchAll();
                                            if($resultado && $stmt->rowCount()>0) {
                                                foreach ($resultado as $fila) {
                                                    $selected = $tarifa_actual==$fila['id'] ? 'selected="selected"' : '';
                                                    echo '<option value="'.encrypt($fila['id']).'" '.$selected.'>'.$fila['tarifa'].'</option>';
                                                }                                            
                                            }
                                        }catch(PDOException $Error) { $mensaje = "Ocurrió un error al cargar los registros."; }
                                        echo "</select><br/>";
                                        echo '<span class="select-btn"><i class="zmdi zmdi-chevron-down"></i></span>';
                                    echo '</div>';
                                    
                                        
                            } 
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
                            <a class="cancel" href="./listadopaquete.php">Regresar</a>
                        </div>                   
                        <div class="form-row-last form-row-2">
                            <input type="button" class="btn btn-primary register" data-toggle="modal" data-target="#modalconfirmacion" value="Actualizar">
                        </div>
                        <div class="modal fade" id="modalconfirmacion" tabindex="-1" role="dialog" aria-labelledby="modalconfirmacionTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">¿Está seguro de actualizar el paquete?</h5>
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
            }
            ?>
		</div>
	</div>
</body>
<?php include "../tools/footer.php" ?>