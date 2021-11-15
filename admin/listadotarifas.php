<?php
    include_once '../tools/database.php';
    include '../tools/utf-8.php';
    include '../tools/funciones.php';
    $mensaje='';
    session_start();

    if(!isset($_SESSION['rol'])){
        header('location: ../login.php');
    }else{
        if($_SESSION['rol'] != 1){
            header('location: ../login.php');
        }
    }

    try{
        $database = new Database();
        $consulta  = "SELECT id, tarifa, precio,dias FROM tarifas";
        $datos = $database->conectar()->prepare($consulta);
        $datos->execute();
        $resultado = $datos->fetchAll();
    }catch(PDOException $Error) {$mensaje = "Ocurrió un error al cargar los registros."; }

    if (isset($_POST['submit']) && isset($_POST['busqueda']) && $_POST['busqueda']!=''){
        try{
        $search = $_POST['busqueda'];
        $database = new Database();
        $consulta  = "SELECT id, tarifa, precio, dias FROM tarifas WHERE (id LIKE '%".$search."%' OR tarifa LIKE '%".$search."%' OR precio LIKE '%".$search."%' OR dias LIKE '%".$search."%')";
        $datos = $database->conectar()->prepare($consulta);
        $datos->execute();
        $resultado = $datos->fetchAll();
        }catch(PDOException $Error) {$mensaje = "Ocurrió un error al cargar los registros."; }
    }        
?>

<?php include "../tools/head.php" ?>
<body>
    <?php include "../tools/navbar.php"?>
    <div class="container-fluid px-3 py-2 px-md-5">
        <div class="d-flex bd-highlight text-center">
            <div class="p-2 flex-shrink-1 d-flex align-items-center">
                <a href="./admin.php" class="btn btn-success mx-1 d-flex justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Atrás"><span class="material-icons">arrow_back</span></a> 
            </div>
            <div class="p-2 w-100">
                <h1 class="display-6">Listado de tarifas</h1>
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
                    <th scope="col">Tarifa</th>
                    <th scope="col">Precio por libra</th>
                    <th scope="col">Dias</th>
                    <th colspan="2" scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($resultado as $fila ) {?>
                <tr>
                    <td class="fw-bold"><?php echo escape($fila['id']); ?></td>
                    <td><?php echo escape($fila['tarifa']); ?></td>
                    <td><?php echo 'L. '.escape($fila['precio']); ?></td>
                    <td><?php echo escape($fila['dias']); ?></td>
                    <?php if($fila['id']){?> 
                        <td><a href="editar_tarifas.php?id=<?php echo encrypt($fila['id']);?>" class="btn btn-sm btn-success mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar tarifa"><span class="material-icons">edit</span></a>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php 
        } else { 
            $mensaje = 'No hay resultados para esta busqueda: <strong>'.$_POST['busqueda'].'</strong>';
        } 
        if(!empty($mensaje)){
            echo '<div class="alert alert-danger m-0 alert-dismissible">';
            echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
            echo '<strong>Mensaje: </strong>'.$mensaje.'.';
            echo '</div>';
        } 
    ?>
</body>
<?php include "../tools/footer.php" ?>