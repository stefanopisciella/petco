<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";

    require "include/dbms.inc.php";
    require "include/utils_dbms.php";

    session_start();
    $nome_script = "admin/dettaglio-cane";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        echo "Unauthorized";
        exit;   
    }
   
    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/dettaglio-cane.html"); 
    $slides = new Template("skins/slide-cane.html"); 


    global $mysqli;

    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        
        if(isset($_GET['id'])) {
            $id_cane = $_GET['id']; 
            
            // inizio query
            $query_info_cane = "SELECT * FROM cane WHERE ID = '{$id_cane}';";

            try {
                $oid = $mysqli->query($query_info_cane);
            }
            catch (Exception $e) {
                throw new Exception("{$mysqli->errno}");
            }

            $info_cane = $oid->fetch_all(MYSQLI_ASSOC);
            // fine query
            
            $item->setContent("nome", $info_cane[0]["nome"]);
            $item->setContent("descrizione", $info_cane[0]["presentazione"]);
            $item->setContent("sesso", $info_cane[0]["sesso"]);
            
            // sistemazione stringa età
            $eta = $info_cane[0]['eta'];
            if (substr($eta, -1) == 'a') $eta = substr($eta, 0, -1)." anni";
            else $eta = substr($eta, 0, -1)." mesi";

            // sistemazione tipologia di adozione
            if($info_cane[0]["distanza"] == true) {
                $item->setContent("distanza", "A distanza");
            } else {
                $item->setContent("distanza", "Non a distanza");
            }
            
            $item->setContent("eta", $eta);
            $item->setContent("razza", $info_cane[0]["razza"]);
            $item->setContent("chip", $info_cane[0]["chip"]);
            $item->setContent("taglia", $info_cane[0]["taglia"]);
            $item->setContent("descrizione", $info_cane[0]["presentazione"]);

            // caricamento delle immagini del cane
            
            // query per trovare le immagini del cane in questione
            $oid = $mysqli->query("SELECT `path` as img FROM immagine WHERE ID_cane = '{$id_cane}';");

            if (!$oid) {
                echo "Error {$mysqli->errno}: {$mysqli->error}"; exit;
            }

            while($row = mysqli_fetch_array($oid)) {
                $slides->setContent("path", $row['img']);
            }

            $item->setContent("slides", $slides->get());
        } else {
            exit;
        }

        $main->setContent("contenuto", $item->get());
        $main->close();
    }
?>