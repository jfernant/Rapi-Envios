<?php
    include_once '../tools/database.php';
    include '../tools/funciones.php';
    include '../tools/utf-8.php';
    $mensaje = '';
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
        $consulta  = "SELECT u.id, u.nombre,u.apellido,u.correo,u.direccion,u.celular,u.telefono, r.rol, e.id as 'id_estado', e.estado FROM `usuarios` as u JOIN `roles`as r ON u.id_rol = r.id JOIN `estado_usuarios`as e ON u.id_estado = e.id WHERE r.id=1 OR r.id=2";
        $datos = $database->conectar()->prepare($consulta);
        $datos->execute();
        $resultado = $datos->fetchAll();
    }catch(PDOException $Error) {$mensaje = "Ocurrió un error al cargar los registros."; }

    if (isset($_POST['submit']) && isset($_POST['busqueda']) && $_POST['busqueda']!=''){
        try{
        $search = $_POST['busqueda'];
        $database = new Database();
        $consulta  = "SELECT u.id, u.nombre,u.apellido,u.correo,u.direccion,u.celular,u.telefono, r.rol, e.id as 'id_estado', e.estado FROM `usuarios` as u JOIN `roles`as r ON u.id_rol = r.id JOIN `estado_usuarios`as e ON u.id_estado = e.id WHERE (u.id LIKE '%".$search."%' OR u.nombre LIKE '%".$search."%' OR u.apellido LIKE '%".$search."%' OR u.correo LIKE '%".$search."%' OR u.direccion LIKE '%".$search."%' OR u.celular LIKE '%".$search."%' OR u.telefono LIKE '%".$search."%' OR r.rol LIKE '%".$search."%' OR e.estado LIKE '%".$search."%') AND (r.id=1 OR r.id=2);";
        $datos = $database->conectar()->prepare($consulta);
        $datos->execute();
        $resultado = $datos->fetchAll();
        }catch(PDOException $Error) {$mensaje = "Ocurrió un error al cargar los registros.";}
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
                <h1 class="display-6">Listado de usuarios internos</h1>
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
                    <th scope="col">Nombre</th>
                    <th scope="col">Apellido</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Dirección</th>    
                    <th scope="col">Celular</th>
                    <th scope="col">Teléfono</th>
                    <th scope="col">Rol</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($resultado as $fila ) {?>
                <tr>
                    <td class="fw-bold"><?php echo escape($fila['id']); ?></td>
                    <td><?php echo escape($fila['nombre']); ?></td>
                    <td><?php echo escape($fila['apellido']); ?></td>
                    <td class="text-nowrap"><?php echo escape($fila['correo']); ?></td>
                    <td class="text-break"><?php echo escape($fila['direccion']); ?></td>
                    <td><?php echo escape($fila['celular']); ?></td>
                    <td><?php echo escape($fila['telefono']); ?></td>
                    <td><?php echo escape($fila['rol']); ?></td>
                    <td><span class="badge rounded-pill <?php if($fila['estado']=='activo'){echo ' bg-success ';}else{echo ' bg-danger ';}?> p-2"><?php echo escape($fila['estado']); ?></span></td>
                    <?php 
                        if($fila['id']!=$_SESSION['id_user']) { 
                        $accion = $fila['id_estado'] == 1 ? 'desactivar' : 'activar';
                        $color = $fila['id_estado'] == 1 ? 'danger' : 'success';
                        echo '<td><a class="btn btn-sm btn-'.$color.' mx-1" href="./confirmar.php?id='.encrypt($fila['id']).'&accion='.encrypt($accion).'&nombre='.encrypt(ucfirst($fila['nombre']).' '.ucfirst($fila['apellido'])).'&correo='.encrypt($fila['correo']).'" class="btn btn-sm btn-success mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="'.$accion.' usuario"><span class="material-icons">toggle_on</span></a>';
                        }else{ 
                            echo "<td></td>";
                        } 
                    ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
    </div> 
    <?php 
        } else { 
            if(isset($_POST['busqueda']) && $_POST['busqueda']!=''){
                $mensaje = 'No hay resultados para esta busqueda: <strong>'.escape($_POST['busqueda']).'</strong>';
            }else{
                $mensaje = 'No se encontraron paquetes';
            }
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