<?
session_start();
include_once("../../config.php");
include_once("{$root}/inc/funzioni.php");

if (!is_operatore()) {
    if(isset($_GET["sezione"]) && isset($_GET["codice"])) {
        $bind = [":codice_ente"=>$_SESSION["ente"]["codice"],":codice"=>$_GET["codice"],":sezione"=>$_GET["sezione"]];
        $strsql = "SELECT b_comunicazioni.* FROM b_comunicazioni WHERE b_comunicazioni.codice_ente = :codice_ente AND
                             codice_gara = :codice AND sezione = :sezione ORDER BY b_comunicazioni.timestamp DESC ";
        $comunicazioni = $pdo->go($strsql,$bind);
        if ($comunicazioni->rowCount() > 0) {
            $archivio = sys_get_temp_dir() . "/ricevuteGara-" . date("YmdHis") . ".zip";
            $zip = new ZipArchive;
            if ($zip->open($archivio, ZipArchive::OVERWRITE) === TRUE) {
                $destinatari = $pdo->prepare("SELECT r_comunicazioni_utenti.codice FROM r_comunicazioni_utenti
                                              WHERE r_comunicazioni_utenti.codice_comunicazione = :codice");
                $found = false;    
                while ($comunicazione = $comunicazioni->fetch(PDO::FETCH_ASSOC)) {
                    $destinatari->bindValue(":codice",$comunicazione["codice"]);
                    $destinatari->execute();
                    if ($destinatari->rowCount()>0) {
                        while($destinatario = $destinatari->fetch(PDO::FETCH_ASSOC)) {
                            if(file_exists("{$config["arch_folder"]}/ricevutepec/{$_SESSION["ente"]["codice"]}/{$destinatario["codice"]}.zip")) {
                                $zip->addFile("{$config["arch_folder"]}/ricevutepec/{$_SESSION["ente"]["codice"]}/{$destinatario["codice"]}.zip","{$destinatario["codice"]}.zip");
                                $found = true;
                            }
                        }
                    }
                }
                $zip->close();
                if ($found) {
                    if(file_exists($archivio)) {
                        $size = filesize($archivio);
                        header("Pragma: public");
                        header("Expires: 0");
                        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                        header("Cache-Control: public");
                        header("Content-Description: File Transfer");
                        header("Content-type: application/octet-stream");
                        header("Content-Disposition: attachment; filename=\"ricevuteGara-" . date("YmdHis") . ".zip\"");
                        header("Content-Transfer-Encoding: binary");
                        header("Content-Length: {$size}");
                        ob_end_flush();
                        @readfile($archivio);
                        unlink($archivio);
                        die();
                    }
                } else {
                    echo "<h1>Nessuna ricevuta disponibile</h1>";
                    die();
                }
            }
        }
            
    }
}
die('<meta http-equiv="refresh" content="0;URL=/index.php">');
?>