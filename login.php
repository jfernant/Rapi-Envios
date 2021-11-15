<?php
    include_once './tools/database.php';
    session_start();
    $mensaje = '';
    if(isset($_SESSION['rol']) && $_SESSION['rol']){
        switch($_SESSION['rol']){
            case 1:
                header('location: ./admin/admin.php');
                break;
            case 2:
                header('location: ./empleados/empleado.php');
                break;
            case 3:
                header('location: ./clientes/cliente.php');
                break;
            default:
                header('location: ./login.php');
        }
    }
    
    if(isset($_POST['submit'])){

        if(isset($_POST['correo']) && isset($_POST['password'])){
            
            if($_POST['correo']!='' && $_POST['password']!=''){

                $correo = $_POST['correo'];
                $password = $_POST['password'];
                try{
                    $database = new Database();
                    $query = 'SELECT correo, password FROM usuarios WHERE id_estado=1 AND BINARY correo = :correo';
                    $stmt = $database->conectar()->prepare($query);
                    $stmt->bindParam(':correo', $correo);
                    $stmt->execute();
                    $registro = $stmt->fetch(PDO::FETCH_NUM);
                }catch(PDOException $Error) { $mensaje = "Ocurrió un error al cargar los registros."; }
                if($registro == true){

                    $correo_usuario = $registro[0];
                    $hashed_password = $registro[1];
                    
                    if(password_verify($password, $hashed_password)){
                        try{ 
                        $database = new Database();
                        $query = 'SELECT `id`, `nombre`, `apellido`, `correo`, `casillero`, `id_rol` FROM `usuarios` WHERE id_estado=1 AND BINARY correo = :correo';
                        $stmt = $database->conectar()->prepare($query);
                        $stmt->bindParam(':correo', $correo_usuario);
                        $stmt->execute();
                        $registro = $stmt->fetch(PDO::FETCH_NUM);
                        }catch(PDOException $Error) { $mensaje = "Ocurrió un error al cargar los registros."; }
                        if($registro == true){

                            $id_user = $registro[0];
                            $nombre_completo = $registro[1].' '.$registro[2];
                            $correo = $registro[3];
                            $casillero = $registro[4];
                            $rol = $registro[5];

                            $_SESSION['id_user'] = $id_user;
                            $_SESSION['nombre_completo'] = $nombre_completo;
                            $_SESSION['correo'] = $correo;
                            $_SESSION['casillero'] = $casillero;
                            $_SESSION['rol'] = $rol;
                            
                            switch($rol){
                                case 1:
                                    header('location: ./admin/admin.php');
                                    break;
                                case 2:
                                    header('location: ./empleados/empleado.php');
                                    break;
                                case 3:
                                    header('location: ./clientes/cliente.php');
                                    break;
                                default:
                                    header('location: ./login.php');
                            }
                        }
                    }else{
                        $mensaje = 'Correo o contraseña incorrectos.';
                    }
                }else{
                    $mensaje = 'Correo o contraseña incorrectos.';
                }
            }else{
                $mensaje = 'Debe llenar los campos requeridos.';
            }
        }
    }

?>
<?php include "./tools/head.php" ?>
<style>
        .bg{
            background-image: url('./img/logo.png');
            background-position: center ;
            background-repeat: no-repeat;
        }
        img{
            max-width:50vw;
        }
    </style>
<body>
    <div style="background-color: #1D4D9F;" class="m-0 vh-100 row justify-content-center align-items-center">
    <div class="container w-85 my-auto py-3 py-md-5 shadow-lg rounded bg-white">
            <div class="row align-items-stretch">
            <div class="col bg d-none d-lg-block col-md-5 col-lg-5 col-xl-6 rounded-end">
            </div>
                <div class="col bg-white px-5 rounded-end">
                    <div class="container-fluid col d-lg-none">
                        <div class="text-center">
                            <img src="./img/logo.png" alt="Logo">
                        </div>
                    </div>
                 <h2 class="fw-bold text-center py-5">Inicio de Sesión</h2>
                    <form action="#" method="POST">
                        <div class="mb-2">
                            <label for="correo" class="form-label">Correo electrónico: </label>
                            <input type="email" class="form-control" name="correo" placeholder="correo@ejemplo.com" require><br/>
                        </div>
                        <div class="mb-2">
                            <label for="password" class="form-label">Contraseña: </label>
                            <input type="password" class="form-control" name="password" require  placeholder="Contraseña"><br/><br/>
                        </div>
                        <div class="text-center">
                            <p class="text-danger">&nbsp;<?php if(!empty($mensaje)){ echo $mensaje;}?></p>
                            <input type="submit"  class="btn btn-outline-primary" name="submit" value="Iniciar sesión"><br/><br/>
                        </div>
                        <div class="text-center my-2">
                            <span>¿No tienes cuenta?<br><a href="./registro/registro.php">Registrate</a></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>
