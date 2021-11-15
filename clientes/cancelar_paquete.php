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

    if (isset($_GET["id"]) && $_GET["id"]!='' && isset($_SESSION['id_user'])) {
        try{
            $id=decrypt($_GET["id"]);   
            $database=new Database();
            $query= "UPDATE paquetes SET id_estado=6 WHERE id=:id AND id_estado=1 AND id_cliente=".$_SESSION['id_user'].";";
            $stmt = $database->conectar()->prepare($query);
            $stmt->bindParam(':id',$id);
            $stmt->execute();
        }catch(PDOException $Error){}
    }else{
        exit;
    }
    header("location: ./listadopaquete.php");
?>