<?php

    include_once '../tools/database.php';
    include '../tools/funciones.php';
    include '../tools/utf-8.php';
    
    session_start();
    $mensaje = '';
    if(!isset($_SESSION['rol'])){
        header('location: ../login.php');
    }else{
        if($_SESSION['rol'] != 2){
            header('location: ../login.php');
        }
    }

    if(isset($_POST['submit'])){ 
        if( isset($_POST['peso_real']) && $_POST['peso_real']!='' && isset($_POST['largo']) && $_POST['largo']!='' && isset($_POST['ancho']) && $_POST['ancho']!='' && isset($_POST['alto']) && $_POST['alto']!=''){
            if(validar_num($_POST['peso_real']) && validar_num($_POST['largo']) && validar_num($_POST['ancho']) && validar_num($_POST['alto'])){
            try{
                $id=$_SESSION['id_paquete_recibir'];
                unset($_SESSION['id_paquete_recibir']);

                $database = new Database();
                $query = "SELECT t.precio, t.dias FROM paquetes as p JOIN tarifas as t ON p.id_tarifa = t.id WHERE p.id=:id" ;
                $stmt = $database->conectar()->prepare($query);
                $stmt->bindParam(':id',$id);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_NUM);
                if($resultado == true){

                    $precio_libra=(double)$resultado[0];
                    $dias=(int)$resultado[1];
                
                    $fecha_recepcion=date("Y-m-d");
                    $fecha_entrega=date("Y-m-d",strtotime($fecha_recepcion."+ ".$dias." days"));
                    $peso_real=(double)$_POST['peso_real'];
                    $largo=(double)$_POST['largo'];
                    $ancho=(double)$_POST['ancho'];
                    $alto=(double)$_POST['alto'];
                    $peso_volumen=round((($largo*$ancho*$alto)/166),2);
                    $peso_cobrar= $peso_real>$peso_volumen? $peso_real : $peso_volumen;
                    $total_pagar=$precio_libra*$peso_cobrar;

                    $database = new Database(); 
                    $query = "UPDATE paquetes SET fecha_recepcion= :fecha_recepcion ,fecha_entrega= :fecha_entrega ,peso_real= :peso_real ,peso_volumen= :peso_volumen ,peso_cobrar= :peso_cobrar ,total_pagar= :total_pagar ,id_estado= 2  WHERE id= :id AND id_estado=1";
                    $stmt = $database->conectar()->prepare($query);
                    $stmt->bindParam(':fecha_recepcion',$fecha_recepcion);
                    $stmt->bindParam(':fecha_entrega',$fecha_entrega);
                    $stmt->bindParam(':peso_real',$peso_real);
                    $stmt->bindParam(':peso_volumen',$peso_volumen);
                    $stmt->bindParam(':peso_cobrar',$peso_cobrar);
                    $stmt->bindParam(':total_pagar',$total_pagar);
                    $stmt->bindParam(':id',$id);
                    $stmt->execute();
                    header('location: ./listadopaquetes.php');
                }else{
                    $mensaje = "Error al recibir paquete.";
                }
            }catch(Eception $e){
                unset($_SESSION['id_paquete_recibir']); 
                $mensaje = "Error al recibir paquete.";
            }
            }else{
                $mensaje = "El peso real, largo, ancho y alto debe ser un valor numérico mayor a 0.";
            }
        }else{
            $mensaje = "Debe llenar los campos requeridos.";
        }
    }

   if(isset($_GET['id'])){
        try{    
            $_SESSION['id_paquete_recibir']=decrypt($_GET['id']);
            $database = new Database();
            $query = "SELECT p.id as 'Paquete',u.casillero as 'Casillero',p.nombre_tienda as 'Nombre de Tienda',p.fecha_prealerta as 'Fecha de prealerta ',p.numero_tracking as 'Número de Tracking',p.empresa_envio as 'Empresa de Envío',p.intrucciones_envio as 'Instrucciones de envío',p.precio as 'Valor del artículo', t.tarifa as 'Tipo de envío', e.estado as 'Estado' FROM paquetes as p JOIN estado_paquete as e ON p.id_estado = e.id JOIN tarifas as t ON p.id_tarifa = t.id JOIN usuarios as u ON p.id_cliente=u.id WHERE p.id=:id" ;
            $stmt = $database->conectar()->prepare($query);
            $stmt->bindParam(':id',$_SESSION['id_paquete_recibir']);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(PDOEception $error){
            unset($_SESSION['id_paquete_recibir']); 
            $mensaje = "Paquete no encontrado.";
        }
    }else{
        unset($_SESSION['id_paquete_recibir']); 
        $mensaje = "Ocurrió un error al recibir Paquete.";
        exit;
    }

?>
<?php include "../tools/head.php" ?>
<body class="form-v10">
<?php include "../tools/navbar.php" ?>
	<div class="page-content">
		<div class="form-v10-content mt-3">
			<form class="form-detail" method="POST">
				<div class="form-left">
					<h2>Recibir Paquete</h2>
                    <?php if ($resultado && $stmt->rowCount()>0) {?>                    
                        <?php  foreach ($resultado as $key => $value) { ?>
                            <div class="form-row">
                                <label class="text-dark font-weight-bold" for="<?php echo $key; ?>"><?php echo ucfirst($key).': '; ?></label>
                                <input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo escape($value); ?>" readonly><br><br/>
                            </div>
                        <?php } ?>                        
                        </div>                       
				        <div class="form-right">
                            <br><br><br><br><div class="form-row">  
                                <label class="text-white" for="peso_real">Peso real:</label>
                                <input type="number" name="peso_real" placeholder="0.00 libras" min="0.1" step="0.01" required pattern="^\d*(\.\d{0,2})?$"><br/><br/>
                            </div>
                            <div class="form-row"> 
                                <label class="text-white" for="largo">Largo: </label>
                                <input type="number" name="largo" placeholder="0.00 pulgadas" min="0.1" step="0.01" required pattern="^\d*(\.\d{0,2})?$"><br/><br/>
                            </div>
                            <div class="form-row"> 
                                <label class="text-white" for="ancho">Ancho: </label>
                                <input type="number" name="ancho" placeholder="0.00 pulgadas" min="0.1" step="0.01" required pattern="^\d*(\.\d{0,2})?$"><br/><br/>
                            </div>
                            <div class="form-row"> 
                                <label class="text-white" for="alto">Alto: </label>
                                <input type="number" name="alto" placeholder="0.00 pulgadas" min="0.1" step="0.01" required pattern="^\d*(\.\d{0,2})?$"><br/><br/>
                            </div>
                        <br>
                        <div class="form-group justify-content-center">
                        <div class="form-row-last-2 form-row-1">
                            <a class="cancel" href="./paquetes_prealerta.php">Regresar</a>
                        </div>                   
                        <div class="form-row-last form-row-2">
                            <input type="button" class="btn btn-primary register" data-toggle="modal" data-target="#modalconfirmacion" value="Recibir">
                        </div>
                        <div class="modal fade" id="modalconfirmacion" tabindex="-1" role="dialog" aria-labelledby="modalconfirmacionTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                     <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">¿Está seguro de recibir este pqeuete en bodega?</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Está a punto de recibir el paquete en bodega.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit"  class="btn btn-success" name="submit">Recibir</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
	</div>
</body>
        <?php 
            }else{
                $mensaje = "Paquete no encontrado.";
            }
            if(!empty($mensaje)){
                echo '<div class="alert alert-danger alert-dismissible mx-3">';
                echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                echo '<strong>Mensaje: </strong>'.$mensaje.'.';
                echo '</div>';
            } 
        ?>
        <br><a href="./paquetes_prealerta.php">Regresar</a>
</body>
<?php include "../tools/footer.php" ?>

  

        


