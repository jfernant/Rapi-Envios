<?php
    session_start();

    if(!isset($_SESSION['rol'])){
        header('location: ../login.php');
    }else{
        if($_SESSION['rol'] != 1){
            header('location: ../login.php');
        }
    }

?>
<?php include "../tools/head.php" ?>

<body>
    <?php include "../tools/navbar.php"?>
    <div class="d-flex justify-content-center">  
        <div class="d-flex justify-content-center w-85 mt-5">
            <div class="mx-5 d-flex justify-content-center rounded-end shadow-lg" style="background:#D1AE45">
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <div class="col p-5">
                        <div class="card h-100">
                            <img class="card-img-top" src="https://images.unsplash.com/photo-1502404768591-f24d06b7a366?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" alt="Card image cap">   
                            <div class="card-body">
                                <div class="d-grid gap-2 mx-5 my-2">
                                    <a href="./registroempleados.php" class="btn btn-primary">Registrar usuarios</a>
                                </div>
                                <p class="card-text text-center mt-3">Agregar un nuevo usuario interno al sistema</p>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col p-5">
                        <div class="card h-100">
                            <img class="card-img-top" src="https://images.unsplash.com/photo-1606146485652-75b352ce408a?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=750&q=80" alt="Card image cap">   
                            <div class="card-body">
                                <div class="d-grid gap-2 mx-5 my-2">
                                    <a href="./listadoempleados.php" class="btn btn-primary">Ver y Editar Usuarios</a>
                                </div>
                                <p class="card-text text-center mt-3">Ver y editar estado de usuarios internos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col p-5">
                        <div class="card h-100">
                            <img class="card-img-top" src="https://images.unsplash.com/photo-1565891741441-64926e441838?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1051&q=80" alt="Card image cap">   
                            <div class="card-body">
                                <div class="d-grid gap-2 mx-5 my-2">
                                    <a href="./listadotarifas.php" class="btn btn-primary">Ver y Editar Tarifas</a>
                                </div>
                                <p class="card-text text-center mt-3">Ver y editar datos de tarifas y medios de envío</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include "../tools/footer.php" ?>