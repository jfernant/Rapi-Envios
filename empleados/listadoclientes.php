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
        $consulta  = "SELECT casillero,nombre,apellido,correo,direccion,celular,telefono,e.estado FROM `usuarios` as u JOIN estado_usuarios as e ON u.id_estado=e.id WHERE id_rol=3";
        $datos = $database->conectar()->prepare($consulta);
        $datos->execute();
        $resultado = $datos->fetchAll();
    }catch(PDOException $Error) {
        $mensaje = "Ocurrió un error al realizar la búsqueda.";
    }

    if (isset($_POST['submit']) && isset($_POST['busqueda']) && $_POST['busqueda']!=''){
        try{
            $search = $_POST['busqueda'];
            $database = new Database();
            $consulta  = "SELECT casillero,nombre,apellido,correo,direccion,celular,telefono,e.estado FROM `usuarios` as u JOIN estado_usuarios as e ON u.id_estado=e.id WHERE id_rol=3 AND (casillero LIKE '%".$search."%' OR nombre LIKE '%".$search."%' OR apellido LIKE '%".$search."%' OR correo LIKE '%".$search."%' OR direccion LIKE '%".$search."%' OR celular LIKE '%".$search."%' OR telefono LIKE '%".$search."%' OR e.estado LIKE '%".$search."%');";
            $datos = $database->conectar()->prepare($consulta);
            $datos->execute();
            $resultado = $datos->fetchAll();
        }catch(PDOException $Error) {
            $mensaje = "Ocurrió un error al realizar la búsqueda.";
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
                <h1 class="display-6">Clientes</h1>
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
                    <th scope="col">Casillero</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Apellido</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Dirección</th>
                    <th scope="col">Celular</th>
                    <th scope="col">Teléfono</th>
                    <th scope="col">Estado</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($resultado as $fila ) {?>
                <tr>
                    <td class="fw-bold"><?php echo escape($fila['casillero']); ?></td>
                    <td class="text-wrap text-capitalize"><?php echo escape($fila['nombre']); ?></td>
                    <td class="text-wrap text-capitalize"><?php echo escape($fila['apellido']); ?></td>
                    <td class="text-lowercase"><?php echo escape($fila['correo']); ?></td>
                    <td class="text-wrap text-capitalize"><?php echo escape($fila['direccion']); ?></td>
                    <td><?php echo escape($fila['celular']); ?></td>
                    <td><?php echo escape($fila['telefono']); ?></td>
                    <td><span class="badge rounded-pill bg-success p-2"><?php echo escape($fila['estado']); ?></span></td>             
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