<?php
    require "include/dbms.inc.php";
    require "frame-public.php";
    require "include/utils_dbms.php";

    // per il redirect allo script "dettaglio-cane-distanza" una volta effettuato il login
     $_SESSION['previous_page'] = 'dettaglio-cane-distanza';
     if ($_SERVER["REQUEST_METHOD"] == "GET") {
         $_SESSION['query_string'] = $_SERVER['QUERY_STRING'];
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['importo']) && isset($_POST['cadenza'])) {
            if(!isset($_SESSION['user_id'])) {
                // caso in cui l'utente non è loggato ==>non può effettuare l'adozione a 
                // distanza. Pertanto C viene reindirizzato alla login
                header("Location: login.php");
                exit;   
            } else {
                // caso in cui l'utente è loggato
                $user_id = $_SESSION['user_id']; 
                $param_name = 'id=';
                // $param_value contiene l'ID del cane per il quale l'utente ha effettuato
                // la donazione a distanza
                $param_value = $_POST['id'];
                $importo = $_POST['importo'];
                $cadenza = $_POST['cadenza']; 
                $actual_date = date("Y/m/d");

                $adozione = [$user_id, $param_value, $cadenza, "'".$actual_date."'", $importo];

                try {
                    insert_query('adozione_distanza', $adozione);
                    header('Location: dettaglio-cane-distanza.php?' . $param_name . $param_value . '&success=1');
                } catch (Exception $e){
                    
                }
            }
        
        }
    } else {
        $dettaglio_cane = new Template("skins/dettaglio-cane-a-distanza.html");

        // injection informazioni cane
        $id_cane = $_GET['id'];
        $query_info_cane = "SELECT nome, presentazione, sesso, eta, razza FROM cane WHERE ID = '{$id_cane}';";

        try {
            $oid = $mysqli->query($query_info_cane);
        }
        catch (Exception $e) {
            throw new Exception("{$mysqli->errno}");
        }

        $info_cane = $oid->fetch_all(MYSQLI_ASSOC);

        $dettaglio_cane->setContent("nome", $info_cane[0]["nome"]);
        $dettaglio_cane->setContent("presentazione", $info_cane[0]["presentazione"]);
        $dettaglio_cane->setContent("sesso", $info_cane[0]["sesso"]);
        // sistemazione stringa età
        $eta = $info_cane[0]['eta'];
        if (substr($eta, -1) == 'a') $eta = substr($eta, 0, -1)." anni";
        else $eta = substr($eta, 0, -1)." mesi";
        $dettaglio_cane->setContent("eta", $eta);
        $dettaglio_cane->setContent("razza", $info_cane[0]["razza"]);
    
        $head->setContent("contenuto", $dettaglio_cane->get());
    
        $head->close();
    }
?>