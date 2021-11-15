<?php
    session_start();

    if(!isset($_SESSION['rol'])){
        header('location: ../login.php');
    }else{
        if($_SESSION['rol'] != 2){
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
                            <img class="card-img-top" src="https://images.unsplash.com/photo-1556740714-a8395b3bf30f?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=750&q=80" alt="Card image cap">   
                            <div class="card-body">
                                <div class="d-grid gap-2 mx-5 my-2">
                                    <a href="./listadoclientes.php" class="btn btn-primary">Visualizar Clientes</a>
                                </div>
                                <p class="card-text text-center mt-3">Ver el registro de clientes existentes</p>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col p-5">
                        <div class="card h-100">
                            <img class="card-img-top" src="https://images.unsplash.com/photo-1595246135406-803418233494?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=750&q=80" alt="Card image cap">   
                            <div class="card-body">
                                <div class="d-grid gap-2 mx-5 my-2">
                                    <a href="./listadoPaquetes.php" class="btn btn-primary">Ver todos los paquetes</a>
                                </div>
                                <p class="card-text text-center mt-3">Visualizar el listado de paquetes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col p-5">
                        <div class="card h-100">
                            <img class="card-img-top" src="https://images.unsplash.com/photo-1616401784845-180882ba9ba8?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=750&q=80" alt="Card image cap">   
                            <div class="card-body">
                                <div class="d-grid gap-2 mx-5 my-2">
                                    <a href="./paquetes_prealerta.php" class="btn btn-primary">Ver paquetes prealertados</a>
                                </div>
                                <p class="card-text text-center mt-3">Visualizar todos los paquetes prealertados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include "../tools/footer.php" ?>