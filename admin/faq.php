<?php
    require "include/template2.inc.php";
    require 'include/utils_dbms.php';
    require "include/dbms_ops.php";

    session_start();
    $nome_script = "admin/faq";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        echo "Unauthorized";
        exit;   
    }

    $page = new Template("skins/faq.html");
    $max_char_domanda = 300;
    $max_char_risposta = 500;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // caso in cui l'admin ha già visionato la pagina e fa "submit" della faq
        $domanda = $_POST['domanda'];
        $risposta = $_POST['risposta'];

        // controlla che la domanda e la risposta non siano vuote
        if (!isset($domanda) || 
            !isset($risposta) ||
            strlen(trim($domanda)) == 0 ||
            strlen(trim($risposta)) == 0)
        {
            // faq non valida
            header("Location: faq.php?empty_faq=1");
            exit;
        } else {
            $t_domanda = trim($domanda);
            $t_risposta = trim($risposta);
            // controlla il numero di caratteri della domanda e della risposta:
            if(strlen($t_domanda) > $max_char_domanda ||
               strlen($t_risposta) > $max_char_risposta)
            {
                header("Location: faq.php?out_of_limit=1");
                exit;
            }

            $faq = ["'".$t_domanda."'", "'".$t_risposta."'"];

            try {
                insert_query('faq', $faq);
                header("Location: faq.php?");
                exit;
            } catch (Exception $e){
                // ...
            }
        }
    } else {
        // caso in cui il client carica la pagina con il metodo GET
        if(isset($_GET['empty_faq']) && $_GET['empty_faq'] == 1){
            $page->setContent("faq_error", "Domanda e Risposta devono avere almeno un carattere");
        } 

        if(isset($_GET['out_of_limit']) && $_GET['out_of_limit'] == 1){
            $page->setContent("faq_error", "Domanda e Risposta non possono avere più di 
                                $max_char_domanda e $max_char_risposta caratteri rispettivamente");
        } 

        $page->close();
    } 
?>