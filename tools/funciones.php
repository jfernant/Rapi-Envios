<?php

function encrypt($data){
    $key= bin2hex(openssl_random_pseudo_bytes(32));
    $iv = substr(hash('sha256',md5(uniqid())), 0, 16);
    $encrypted=openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    return base64_encode($encrypted."::".$key."::".$iv);
}
function decrypt($data){   
    if(explode('::',base64_decode($data))!=false && count(explode('::',base64_decode($data)))==3){
        list($encrypted,$key,$iv) = explode('::', base64_decode($data));
        if(strlen($iv)>=16){
            return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);   
        }
    }else{
        return false;   
    }
}

function validar_clave($clave,&$error_clave){
    if(strlen(trim($clave)) < 8){
        $error_clave = "La clave debe tener al menos 8 caracteres";
        return false;
    }
    if(strlen(trim($clave)) > 16){
        $error_clave = "La clave no puede tener más de 16 caracteres";
        return false;
    }
    if (!preg_match('`[a-z]`',trim($clave))){
        $error_clave = "La clave debe tener al menos una letra minúscula";
        return false;
    }
    if (!preg_match('`[A-Z]`',trim($clave))){
        $error_clave = "La clave debe tener al menos una letra mayúscula";
        return false;
    }
    if (!preg_match('`[0-9]`',trim($clave))){
        $error_clave = "La clave debe tener al menos un numéro";
        return false;
    }
    $error_clave = "";
    return true;
}

function validar_correo($correo){
    if(filter_var($correo, FILTER_VALIDATE_EMAIL) === FALSE){
       return false;
    }else{
       return true;
    }
 }

 function validar_cel($numero){
     if (!preg_match('`[0-9]`',$numero) || strlen($numero) < 7 || strlen($numero) > 15 || preg_match('`[a-z]`',$numero) || preg_match('`[A-Z]`',$numero)){         
        return false;
    }else{
        return true;
    }
 }

 function validar_tel($numero){
     if($numero==""){
         return true;
     }else{
        if (!preg_match('`[0-9]`',$numero) || strlen($numero) < 7 || strlen($numero) > 15 || preg_match('`[a-z]`',$numero) || preg_match('`[A-Z]`',$numero)){         
            return false;
        }else{
            return true;
        }
     }    
}

 function validar_direc($campo){
    if($campo==""){
        return true;
    }else{
        if(strlen($campo) < 8 || strlen($campo) > 30){        
            return false;
        }else{
            return true;
        }
    }
 }

 function validar_num($num){
    if(is_numeric($num) && $num>0){
        return true;
    }else{
        return false;
    }
    /*if (!preg_match('`[0-9]`',$num)){    
        return false;
    }         
    return true;*/
    
 }

?>