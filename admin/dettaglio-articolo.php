<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";

    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/dettaglio-articolo.html");

    // injection informazioni articolo selezionato
    $id_articolo = $_GET['art'];

    echo $id_articolo;

    $query = "SELECT titolo, contenuto, categoria, `path` AS img FROM articolo WHERE ID='{$id_articolo}';";

    try {
        $oid = $mysqli->query($query);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    $info_articolo = $oid->fetch_all(MYSQLI_ASSOC);

    $contenuto = $info_articolo[0]['contenuto'];

    $item->setContent("titolo", $info_articolo[0]['titolo']);
    $item->setContent("contenuto", $contenuto);
    $item->setContent("categoria", $info_articolo[0]['categoria']);
    $item->setContent("img", $info_articolo[0]['img']);

    $query = "SELECT nome FROM tag JOIN articolo_tag ON tag.ID=articolo_tag.ID_tag AND ID_articolo='{$id_articolo}'";

    try {
        $oid = $mysqli->query($query);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    //$tags = $oid->fetch_all(MYSQLI_ASSOC);

    // costruisco la stringa con i tags separati da virgola
    $tags = "";

    while($row = mysqli_fetch_array($oid)) {
        $tags = $tags.$row['nome'].",";
    }

    $tags = substr($tags, 0, -1);

    $item->setContent("tags", $tags);

    // check che sia richiesta la modifica dell'articolo
    if (isset($_GET['mod']) && $_GET['mod'] == 1) {

        // si vuole modificare l'articolo
        // injection contenuto
        $item->setContent("contenuto", $contenuto);
    }
    
    $main->setContent("contenuto", $item->get());
    $main->close(); 
?>
