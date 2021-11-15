<?php

    include_once '../tools/database.php';
    include '../tools/utf-8.php';
    include '../tools/funciones.php';
    $mensaje = '';
    session_start();
    if(!isset($_SESSION['rol'])){
        header('location: ../login.php');
    }else{
        if($_SESSION['rol'] != 1){
            header('location: ../login.php');
        }
    }

    if(isset($_POST['submit']) && isset($_POST['precio']) && isset($_POST['dias']) && $_POST['precio']!='' && $_POST['dias']!=''){ 
        if(validar_num($_POST['dias'])){
            if(validar_num($_POST['precio'])){
                try{
                    $id=$_SESSION['id_tarifa_edit'];
                    unset($_SESSION['id_tarifa_edit']);
                    $precio=$_POST['precio'];
                    $dias=$_POST['dias'];
                    $database = new Database(); 
                    $query = "UPDATE tarifas SET precio = :precio, dias =:dias WHERE id = :id";
                    $stmt = $database->conectar()->prepare($query);
                    $stmt->bindParam(':precio', $precio);
                    $stmt->bindParam(':dias', $dias);
                    $stmt->bindParam(':id', $id);
                    if ($stmt->execute()) {
                        header('location: ./listadotarifas.php');
                    } else {
                        $mensaje = 'Lo sentimos, ocurrió un error al editar tarifa.';
                    }
                }catch(PDOEception $error){
                    unset($_SESSION['id_tarifa_edit']); 
                    $mensaje = "Ocurrió un error al cargar los registros."; 
                }
            }else{
                $mensaje = 'El precio ingresado debe ser entero o decimal mayor a 0';
            }
        }else{
            $mensaje = 'El número de dias ingresado debe un entero mayor a 0';
        }

    }

   if(isset($_GET['id'])){
        try{
            $_SESSION['id_tarifa_edit']=decrypt($_GET['id']);
            $database = new Database();
            $query = "SELECT id, tarifa, precio, dias FROM tarifas WHERE id = :id" ;
            $stmt = $database->conectar()->prepare($query);
            $stmt->bindParam(':id',$_SESSION['id_tarifa_edit']);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(PDOEception $error){
            unset($_SESSION['id_tarifa_edit']); 
            $mensaje = "Ocurrió un error al cargar los registros."; 
        }
    }else{
        unset($_SESSION['id_tarifa_edit']); 
        $mensaje = "Ocurrió un error al cargar los registros."; 
        exit;
    }

?>
<?php include "../tools/head.php" ?>
<body class="form-v10">
    <?php include "../tools/navbar.php" ?>
    <div class="page-content">
		<div class="form-v10-content w-50 m-3">    
                <?php if ($resultado && $stmt->rowCount()>0) {?>
                <form class="form-detail justify-content-center" method="POST">
                <div class="form-right text-center">
                    <h2>Editar Tarifas</h1>
                    <?php  foreach ($resultado as $key => $value) { ?>
                        <div class="form-row">
                            <label class="text-white" for="<?php echo $key; ?>"><?php echo ucfirst($key); ?></label>
                            <input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo escape($value);?>"<?php echo ($key==='id'||$key==='tarifa'? 'readonly':null); ?>> <br>
                        </div>
                    <?php } ?>
                    <div class="form-group justify-content-center">
                        <div class="form-row-last-2 form-row-1">
                            <a class="cancel" href="./listadotarifas.php">Regresar</a>
                        </div>                   
                        <div class="form-row-last form-row-2">
                            <input type="button" class="btn btn-primary register" data-toggle="modal" data-target="#modalconfirmacion" value="Actualizar">
                        </div>
                        <div class="modal fade" id="modalconfirmacion" tabindex="-1" role="dialog" aria-labelledby="modalconfirmacionTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">¿Está seguro de actualizar tarifa?</h5>
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
                
                <?php }else{
                    $mensaje = "Tarifa no encontrada.";
                    }
                ?>
                <?php if(!empty($mensaje)){
                    echo '<div class="alert alert-danger alert-dismissible mx-5">';
                    echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                    echo '<strong>Mensaje: </strong>'.$mensaje.'.';
                    echo '</div>';
                }?>
            
                </div>
                </form>
        </div>
    </div>
</body>
<?php include "../tools/footer.php" ?>