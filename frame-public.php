<?php
    require "include/template2.inc.php";
    require "include/dbms_ops.php";

    $head = new Template("skins/frame-public.html");
    
    session_start();
    if (isset($_SESSION['user_id']) && 
        $_SESSION['user_id'] >= 1 &&
        get_group($_SESSION['user_id']) == 2)
    {
        // utente non amministratore loggato
        $head->setContent("label", "Profilo");
        $head->setContent("url", "account.php");
    } else {
        // utente non loggato
        $head->setContent("label", "Accedi");
        $head->setContent("url", "login.php");
    }
?>