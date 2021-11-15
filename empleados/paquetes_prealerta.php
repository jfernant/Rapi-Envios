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
        $consulta  = "SELECT p.id,e.estado,u.casillero,p.fecha_prealerta,p.numero_tracking,p.precio,p.intrucciones_envio,t.tarifa FROM `paquetes`AS p JOIN estado_paquete as e ON p.id_estado=e.id JOIN tarifas as t ON p.id_tarifa=t.id JOIN usuarios as u ON p.id_cliente=u.id WHERE e.id=1";
        $datos = $database->conectar()->prepare($consulta);
        $datos->execute();
        $resultado = $datos->fetchAll();
    }catch(PDOException $Error) {
        $mensaje = "Ocurrió un error al cargar los registros."; 
    }
    if (isset($_POST['submit']) && isset($_POST['busqueda']) && $_POST['busqueda']!=''){
        try{ 
            $search = $_POST['busqueda'];
            $database = new Database();
            $consulta  = "SELECT p.id,e.estado,u.casillero,p.fecha_prealerta,p.numero_tracking,p.precio,p.intrucciones_envio,t.tarifa FROM `paquetes`AS p JOIN estado_paquete as e ON p.id_estado=e.id JOIN tarifas as t ON p.id_tarifa=t.id JOIN usuarios as u ON p.id_cliente=u.id WHERE e.id=1 AND (p.id LIKE '%".$search."%' OR e.estado LIKE '%".$search."%' OR u.casillero LIKE '%".$search."%' OR p.fecha_prealerta LIKE '%".$search."%' OR p.numero_tracking LIKE '%".$search."%' OR p.precio LIKE '%".$search."%' OR p.intrucciones_envio LIKE '%".$search."%' OR t.tarifa LIKE '%".$search."%');";
            $datos = $database->conectar()->prepare($consulta);
            $datos->execute();
            $resultado = $datos->fetchAll();
        }catch(PDOException $Error) { $mensaje = "Ocurrió un error al cargar los registros."; }
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
                <h1 class="display-6">Paquetes Prealertados</h1>
            </div>
        </div>
        <form class="d-flex py-3" method="POST">
                <input class="form-control ml-2 ml-md-5 mr-1" name="busqueda" type="search" placeholder="Buscar ...">
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
                    <th scope="col">Fecha de Prealerta</th>
                    <th scope="col">Número de Tracking</th>
                    <th scope="col">Valor del artículo</th>
                    <th scope="col">Instrucciones de Envío</th>
                    <th scope="col">Tipo de Envío</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($resultado as $fila ) {?>
                <tr>
                    <td class="fw-bold"><?php echo escape($fila['id']); ?></td>
                    <td><span class="badge rounded-pill bg-success p-2"><?php echo escape($fila['estado']); ?></span></td>
                    <td><?php echo escape($fila['casillero']); ?></td>
                    <td class="text-nowrap"><?php echo escape($fila['fecha_prealerta']); ?></td>
                    <td class="text-nowrap"><?php echo escape($fila['numero_tracking']); ?></td>
                    <td><?php echo '$ '.escape($fila['precio']); ?></td>
                    <td class="text-nowrap"><?php echo escape($fila['intrucciones_envio']); ?></td>
                    <td><?php echo escape($fila['tarifa']); ?></td>
                    <?php if($fila['id']){?> 
                        <td><a href="./recibir_paquete.php?id=<?php echo encrypt($fila['id']);?>" class="btn btn-sm btn-primary mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Recibir paquete"><span class="material-icons">open_in_new</span></a>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php 
    } else { 
        if(isset($_POST['busqueda']) && $_POST['busqueda']!=''){
            $mensaje = 'No hay resultados para esta busqueda: <strong>'.escape($_POST['busqueda']).'</strong>';
        }else{
            $mensaje = 'No se encontraron paquetes prealertados';
        }
    } 
    if(!empty($mensaje)){
        echo '<div class="alert alert-danger m-0 alert-dismissible mx-5">';
        echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        echo '<strong>Mensaje: </strong>'.$mensaje.'.';
        echo '</div>';
    }
    ?>
</body>
<?php include "../tools/footer.php" ?>