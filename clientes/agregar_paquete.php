<?php
    include_once '../tools/database.php';

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
        
        if(isset($_POST['nombre_tienda']) && isset($_POST['numero_tracking']) && isset($_POST['empresa_envio']) && isset($_POST['precio']) && isset($_POST['tarifa']) ){
            if ($_POST['nombre_tienda']!='' && $_POST['numero_tracking']!='' && $_POST['empresa_envio']!='' && $_POST['precio']!='' && $_POST['tarifa']!=''){
                
                $id_user=$_SESSION['id_user'];
                $nombre_tienda=$_POST['nombre_tienda'];
                $numero_tracking=$_POST['numero_tracking'];
                $empresa_envio=$_POST['empresa_envio'];
                $precio=$_POST['precio'];
                $id_tarifa=(int)$_POST['tarifa'];
                $intrucciones_envio=$_POST['intrucciones_envio'];
                $fecha_prealerta=date("Y-m-d");

                try{
                    $database = new Database();
                    $query = 'SELECT COUNT(*)+1 FROM paquetes';
                    $stmt = $database->conectar()->prepare($query);
                    $stmt->execute();
                    $registro = $stmt->fetch(PDO::FETCH_NUM);
                    $id_envio='';
                }catch(PDOException $Error) { $mensaje = "Ocurrió un error al cargar los registros."; }

                if($registro == true){

                    $contador = $registro[0];
                    $id_envio='ENV'.str_pad($contador,6, "0", STR_PAD_LEFT);
                    try{
                        $database = new Database();
                        $query = 'INSERT INTO paquetes(id, id_cliente, nombre_tienda, fecha_prealerta, numero_tracking, empresa_envio, precio, intrucciones_envio, id_tarifa, id_estado) VALUES (:id, :id_cliente, :nombre_tienda, :fecha_prealerta, :numero_tracking, :empresa_envio, :precio, :intrucciones_envio, :id_tarifa, 1)';
                        $stmt = $database->conectar()->prepare($query);
                        $stmt->bindParam(':id', $id_envio);
                        $stmt->bindParam(':id_cliente', $id_user);
                        $stmt->bindParam(':nombre_tienda', $nombre_tienda);
                        $stmt->bindParam(':fecha_prealerta', $fecha_prealerta);
                        $stmt->bindParam(':numero_tracking', $numero_tracking);
                        $stmt->bindParam(':empresa_envio', $empresa_envio);
                        $stmt->bindParam(':precio', $precio);
                        $stmt->bindParam(':intrucciones_envio', $intrucciones_envio);
                        $stmt->bindParam(':id_tarifa', $id_tarifa);

                        if ($stmt->execute()) {
                            $mensaje = 'Paquete prealertado correctamente.';
                            header('location: ./listadopaquete.php');
                        } else {
                            $mensaje = 'Lo sentimos, ocurrió un error al prealertar paquete.';
                        }

                    }catch(PDOException $Error) { $mensaje = "Ocurrió un error al cargar los registros."; }
                    
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
		<div class="form-v10-content mt-3">
                <form class="form-detail" method="POST">

                <div class="form-left">	
                <h2>Agregar Paquete</h2>						
					<div class="form-row">
                    <label for="nombre_tienda">Nombre de tienda:</label><br/>
                    <input type="text" name="nombre_tienda" required pattern="[0-9A-Za-z]{3,30}" placeholder="Ej. Amazon, Ebay" require></div>
					<div class="form-row">
                    <label for="numero_tracking">Número de Tracking:</label><br/>
                    <input type="text" name="numero_tracking" required pattern="^[0-9a-zA-Z\s,-]{8,15}" title="Solo debe ingresar letras y numeros entre 8 y 15 caracteres." placeholder="Letras y números" require></div>
					<div class="form-row">
					<label for="precio">Precio del artículo US$:</label><br/>
                    <input type="number" name="precio" required pattern="^\d*(\.\d{0,2})?$" title="Ingrese una cantidad válida." min="1" placeholder="0.00" step="0.01" require></div>
					<div class="form-row">
                        <label for="empresa_envio">Empresa de Envío:</label><br/>
                        <select name="empresa_envio" id="empresa_envio" required>
                            <option disabled selected value>Seleccione</option>
                            <option value="AMAZON LOGISTICS">AMAZON LOGISTICS</option>
                            <option value="DHL">DHL</option>
                            <option value="FEDEX">FEDEX</option>
                            <option value="UPS">UPS</option>
                            <option value="USPS">USPS</option>
                            <option value="OTROS">OTROS</option>
                        </select>
						<span class="select-btn">
						  	<i class="zmdi zmdi-chevron-down"></i>
						</span>
					</div>					
				</div>
                  
                <div class="form-right">
                <br><br><br><br><div class="form-row">
                    <label for="intrucciones_envio" class="text-white">Instrucciones de Envío  :</label><br/>
                    <input name="intrucciones_envio" rows="6" placeholder="Instrucciones a aclarar acerca de este paquete." pattern="^[#.0-9a-zA-Z\s,-]{8,40}" title="Las intrucciones de envío solo deben contener letras, números y simbolos .Una longituda entre 8 y 40 caracteres."></div>					
                    <div class="form-row">
                        <label class="text-white" for="tarifa">Seleccione tipo de Envío:</label><br/>
                        <select name="tarifa" id="tarifa" required>
                            <option disabled selected value>Seleccione</option>
                            <?php
                                try{
                                $database = new Database();
                                $query = 'SELECT id, tarifa FROM tarifas';
                                $stmt = $database->conectar()->prepare($query);
                                $stmt->execute();
                                $resultado = $stmt->fetchAll();
                                if($resultado && $stmt->rowCount()>0) {
                                    foreach ($resultado as $fila) {
                                        echo '<option value="'.$fila['id'].'">'.$fila['tarifa'].'</option>';
                                    }
                                }
                                }catch(PDOException $Error) { $mensaje = "Ocurrió un error al cargar los registros."; }
                            ?>
                        </select>
                        <span class="select-btn">
                                <i class="zmdi zmdi-chevron-down"></i>
                        </span>
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
                            <a class="cancel" href="./cliente.php">Cancelar</a>
                        </div>                   
                        <div class="form-row-last form-row-2">
                            <input type="submit" name="submit" class="register" value="Prealertar">
                        </div>
                    </div>                  					
				</div> 
            </form>
        </div>
    </div>
</body>
<?php include "../tools/footer.php" ?>