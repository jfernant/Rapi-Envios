<?php

    session_start();

    if(!isset($_SESSION['rol'])){
        header('location: ../login.php');
    }else{
        if($_SESSION['rol'] != 3){
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
                            <img class="card-img-top" src="https://images.unsplash.com/photo-1580674285054-bed31e145f59?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=750&q=80" alt="Card image cap">   
                            <div class="card-body">
                                <div class="d-grid gap-2 mx-5 my-2">
                                    <a href="./todos_paquetes.php" class="btn btn-primary">Todos mis paquetes</a>
                                </div>
                                <p class="card-text text-center mt-3">Visualizar mis paquetes</p>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col p-5">
                        <div class="card h-100">
                            <img class="card-img-top" src="https://images.unsplash.com/photo-1577702312706-e23ff063064f?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=750&q=80" alt="Card image cap">   
                            <div class="card-body">
                                <div class="d-grid gap-2 mx-5 my-2">
                                    <a href="./agregar_paquete.php" class="btn btn-primary">Agregar Paquete</a>
                                </div>
                                <p class="card-text text-center mt-3">Generar una nuevo pedido</p>
                            </div>
                        </div>
                    </div>
                    <div class="col p-5">
                        <div class="card h-100">
                            <img class="card-img-top" src="https://images.unsplash.com/photo-1545591841-4a97f1da8d1f?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=750&q=80" alt="Card image cap">   
                            <div class="card-body">
                                <div class="d-grid gap-2 mx-5 my-2">
                                    <a href="./listadopaquete.php" class="btn btn-primary">Ver paquetes prealertados</a>
                                </div>
                                <p class="card-text text-center mt-3">Visualizar todos mis paquetes prealertados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include "../tools/footer.php" ?>