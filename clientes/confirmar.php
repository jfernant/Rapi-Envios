<?php
    include_once '../tools/database.php';
    include '../tools/funciones.php';

    session_start();
    if(!isset($_SESSION['rol'])){
        header('location: ../login.php');
    }else{
        if($_SESSION['rol'] != 3){
            header('location: ../login.php');
        }
    }
    if (isset($_GET["id"]) && decrypt($_GET["id"])) {
            $id=decrypt($_GET["id"]);
?>
<?php include "../tools/head.php";?>
        <body>
        <script>
            $( document ).ready(function() {
                $('#modalconfirmacion').modal('toggle')
            });
        </script>
        <div class="modal fade" id="modalconfirmacion" tabindex="-1" role="dialog" aria-labelledby="modalconfirmacionTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">¿Está seguro de cancelar la prealerta?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Esta apunto de cancelar su prealerta. Presione "Cancelar prealerta" sí desea cancelarla, o presione "Atrás" si no desea hacerlo.
                </div>
                <div class="modal-footer">
                    <a class="btn btn-success" href="./cancelar_paquete.php?id=<?php echo encrypt($id)?>">Cancelar prealerta</a>
                    <a class="btn btn-danger" href="./listadopaquete.php">Atrás</a>
                </div>
            </div>
        </div>
        </div>
        </body>
<?php
    }else{
        header("location: ./listadoempleados.php");
    }  
?>
<?php include "../tools/footer.php";?>
