<?
    include_once("../../config.php");
    if(empty($_SESSION["codice_utente"]) || !check_permessi("conservazione", $_SESSION["codice_utente"]) && ($_SESSION["gerarchia"] === "0" || $_SESSION["record_utente"]["codice_ente"] == $_SESSION["ente"]["codice"])) {
        die('<meta http-equiv="refresh" content="0;URL=/index.php">');
    } else {
        $bind = [":codice_ente" => $_SESSION["ente"]["codice"], ":codice_pacchetto" => $_GET["pacchetto"]];
        $where = "WHERE (b_conservazione.codice_gestore = :codice_ente OR b_conservazione.codice_ente = :codice_ente)";
        if($_SESSION["gerarchia"] > 0) $bind[":codice_ente"] = $_SESSION["record_utente"]["codice_ente"];
        $pacchetto = $pdo->go("SELECT b_conservazione.codice FROM b_conservazione {$where} AND codice = :codice_pacchetto", $bind)->fetch(PDO::FETCH_ASSOC);
        if(! empty($pacchetto["codice"])) {
            $documento = $pdo->go("SELECT documento FROM b_conservazione_documento WHERE codice = :codice", array(":codice" => $_GET["codice"]))->fetch(PDO::FETCH_COLUMN, 0);

            header("Content-type:application/pdf");
            echo base64_decode($documento);
        }
    }
?>