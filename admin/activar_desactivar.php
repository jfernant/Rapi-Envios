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
        try{
            $database = new Database();
            $id=decrypt($_GET["id"]);
            $accion=decrypt($_GET["accion"]);  
            $nuevo_estado = $accion=="activar" ? 1:2;
            $query= "UPDATE usuarios SET id_estado=".$nuevo_estado." WHERE id=:id";
            $stmt = $database->conectar()->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }catch(PDOException $Error){}
    }else{
        exit;
    }
    header("location: ./listadoempleados.php");
?>