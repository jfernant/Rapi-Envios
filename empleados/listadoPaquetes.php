<?php
    include_once '../tools/database.php';
    include '../tools/utf-8.php';
    include '../tools/funciones.php';

    session_start();
    $mensaje = '';
    if(!isset($_SESSION['rol'])){
        header('location: ../login.php');
    }else{
        if($_SESSION['rol'] != 2){
            header('location: ../login.php');
        }
    }

    try{
        $database = new Database();
        $consulta  = "SELECT p.id,e.estado,u.casillero,p.fecha_prealerta,p.fecha_recepcion,p.fecha_entrega,p.numero_tracking,p.precio as 'valor',p.intrucciones_envio,p.peso_real,p.peso_volumen,p.peso_cobrar,t.tarifa,t.precio, p.total_pagar FROM `paquetes`AS p JOIN estado_paquete as e ON p.id_estado=e.id JOIN tarifas as t ON p.id_tarifa=t.id JOIN usuarios as u ON p.id_cliente=u.id WHERE e.id>1;";
        $datos = $database->conectar()->prepare($consulta);
        $datos->execute();
        $resultado = $datos->fetchAll();
    }catch(PDOException $Error) {
        $mensaje = "No se encontro esta búsqueda.";
    }

    if (isset($_POST['submit']) && isset($_POST['id_estado']) && $_POST['id_estado']!='' && isset($_POST['busqueda'])){
        try{
            $id_estado = decrypt($_POST['id_estado']);
            $search = $_POST['busqueda'];
            $database = new Database();
            $consulta  = "SELECT p.id,e.estado,u.casillero,p.fecha_prealerta,p.fecha_recepcion,p.fecha_entrega,p.numero_tracking,p.precio as 'valor',p.intrucciones_envio,p.peso_real,p.peso_volumen,p.peso_cobrar,t.tarifa,t.precio, p.total_pagar FROM `paquetes`AS p JOIN estado_paquete as e ON p.id_estado=e.id JOIN tarifas as t ON p.id_tarifa=t.id JOIN usuarios as u ON p.id_cliente=u.id WHERE e.id>1 AND e.id=".$id_estado." AND (p.id LIKE '%".$search."%' OR u.casillero LIKE '%".$search."%' OR p.fecha_prealerta LIKE '%".$search."%' OR p.fecha_recepcion LIKE '%".$search."%' OR p.fecha_entrega LIKE '%".$search."%' OR p.numero_tracking LIKE '%".$search."%' OR p.precio LIKE '%".$search."%' OR p.intrucciones_envio LIKE '%".$search."%' OR p.peso_real LIKE '%".$search."%' OR p.peso_volumen LIKE '%".$search."%' OR p.peso_cobrar LIKE '%".$search."%' OR t.tarifa LIKE '%".$search."%' OR t.precio LIKE '%".$search."%' OR  p.total_pagar LIKE '%".$search."%');";
            $datos = $database->conectar()->prepare($consulta);
            $datos->execute();
            $resultado = $datos->fetchAll();
        }catch(PDOException $Error) {
            $mensaje = "No se encontro esta búsqueda.";
        }
    }        
?>

<?php include "../tools/head.php" ?>
<body>
    <?php include "../tools/navbar.php"?>
    <div class="container-fluid px-3 py-2 px-md-5">
        <div class="d-flex bd-highlight text-center">
            <div class="p-2 flex-shrink-1 d-flex align-items-center">
                <a href="./empleado.php" class="btn btn-success mx-1 d-flex justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Atrás"><span class="material-icons">arrow_back</span></a> 
            </div>
            <div class="p-2 w-100">
                <h1 class="display-6">Todos los paquetes</h1>
            </div>
        </div>
        <form class="d-flex py-3" method="POST">
            <div class="form-floating w-25 pr-3">
                <select class="form-select" id="floatingSelect" aria-label="Floating label select example" name="id_estado" id="id_estado">
                    <option selected value="">-- Ninguno --</option>
                    <?php
                        $database = new Database();
                        $query = 'SELECT id, estado FROM estado_paquete WHERE id>1;';
                        $stm = $database->conectar()->prepare($query);
                        $stm->execute();
                        $estados = $stm->fetchAll();
                        if($estados && $stm->rowCount()>0) {
                            foreach ($estados as $row) {
                                echo '<option value="'.encrypt($row['id']).'">'.$row['estado'].'</option>';
                            }
                        }
                    ?>
                </select>
                <label for="floatingSelect">Seleccionar filtro</label>
            </div>
            <div class="form-floating pr-3 w-75">
                <input type="search" class="form-control" id="floatingInputGrid" placeholder="Buscar por ..." name="busqueda">
                <label for="floatingInputGrid">Búsqueda</label>
            </div>
            <button type="submit" name="submit" class="btn btn-outline-primary ml-1 mr-2 mr-md-5" data-bs-toggle="tooltip" data-bs-placement="top" title="Buscar">Buscar</button>
        </form>
        <div class="table-responsive table-hover shadow bg-body rounded">  
    <?php
    if ($resultado && $datos->rowCount()>0) {?>
        <table class="table align-middle mb-0">
            <thead class="text-dark" style="background-color:#F3CC4F;">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Casillero</th>
                    <th scope="col">Fecha prealerta</th>
                    <th scope="col">Fecha Recepción</th>    
                    <th scope="col">Fecha Entrega</th>
                    <th scope="col">Numero Tracking</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Instrucciones de envío</th>
                    <th scope="col">Peso Real</th>
                    <th scope="col">Peso Volúmen</th>
                    <th scope="col">Peso a Cobrar</th>    
                    <th scope="col">Tipo de envío</th>
                    <th scope="col">Precio Por libra</th>
                    <th scope="col">Total a pagar</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($resultado as $fila ) {?>
                <tr>
                    <td class="fw-bold"><?php echo escape($fila['id']); ?></td>
                    <td><?php echo escape($fila['estado']); ?></td>
                    <td><?php echo escape($fila['casillero']); ?></td>
                    <td class="text-nowrap"><?php echo escape($fila['fecha_prealerta']); ?></td>
                    <td class="text-nowrap"><?php echo escape($fila['fecha_recepcion']); ?></td>
                    <td class="text-nowrap"><?php echo escape($fila['fecha_entrega']); ?></td>
                    <td class="text-nowrap text-uppercase"><?php echo escape($fila['numero_tracking']); ?></td>
                    <td class="text-nowrap"><?php echo '$ '.escape($fila['valor']); ?></td>
                    <td class="text-break"><?php echo escape($fila['intrucciones_envio']); ?></td>
                    <td class="text-nowrap"><?php echo escape($fila['peso_real']).' Lb'; ?></td>
                    <td class="text-nowrap"><?php echo escape($fila['peso_volumen']).' Lb'; ?></td>
                    <td class="text-nowrap"><?php echo escape($fila['peso_cobrar']).' Lb'; ?></td>
                    <td><?php echo escape($fila['tarifa']); ?></td>
                    <td class="text-nowrap"><?php echo 'L. '.escape($fila['precio']); ?></td>
                    <td class="text-nowrap"><?php echo 'L. '.escape($fila['total_pagar']); ?></td>
                    <?php if($fila['estado']!='Cancelado' && $fila['estado']!='Entregado' && $fila['estado']!='Prealertado'){ ?>
                        <td><a href="./actualizar_paquetes.php?id=<?php echo encrypt($fila['id']);?>" class="btn btn-sm btn-success mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar paquete"><span class="material-icons">edit</span></a>               
                    <?php }else{ echo"<td></td>"; }?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
    </div> 
    <?php 
    } else { 
        $mensaje = 'No hay resultados para esta busqueda.';
    } 
    if(!empty($mensaje)){
        echo '<div class="alert m-0 alert-danger alert-dismissible">';
        echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        echo '<strong>Mensaje: </strong>'.$mensaje.'.';
        echo '</div>';
    }
    ?>
</body>
<?php include "../tools/footer.php" ?>