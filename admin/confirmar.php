<?php
    include_once '../tools/database.php';
    include '../tools/funciones.php';

    session_start();
    if(!isset($_SESSION['rol'])){
        header('location: ../login.php');
    }else{
        if($_SESSION['rol'] != 1){
            header('location: ../login.php');
        }
    }
    if (isset($_GET["id"]) && isset($_GET["accion"]) && decrypt($_GET["id"])!=$_SESSION['id_user']) {
            $id=decrypt($_GET["id"]);
            $accion=decrypt($_GET["accion"]);
            $nombre=decrypt($_GET["nombre"]);
            $correo=decrypt($_GET["correo"]);
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
                    <h5 class="modal-title" id="exampleModalLongTitle">¿Está seguro de <?php echo ucfirst($accion)?> este usuario?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <strong>Nombre: </strong><?php echo ucfirst($nombre)?><br/>
                <strong> Correo: </strong><?php echo ucfirst($correo)?>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-success" href="./activar_desactivar.php?id=<?php echo encrypt($id)?>&accion=<?php echo encrypt($accion)?>"><?php echo ucfirst($accion)?></a>
                    <a class="btn btn-danger" href="./listadoempleados.php">Cancelar</a>
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
