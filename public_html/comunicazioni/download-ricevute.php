<?
session_start();
include_once("../../config.php");
include_once("{$root}/inc/funzioni.php");

if (!is_operatore()) {
    if(isset($_GET["ricevuta"])) {
        $ricevuta = base64_decode($_GET["ricevuta"]);
        $ricevuta = simple_decrypt($ricevuta, "ricevute-pec");
        if(is_numeric($ricevuta)) {
            if(file_exists("{$config["arch_folder"]}/ricevutepec/{$_SESSION["ente"]["codice"]}/{$ricevuta}.zip")) {
                $size = filesize("{$config["arch_folder"]}/ricevutepec/{$_SESSION["ente"]["codice"]}/{$ricevuta}.zip");
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"{$ricevuta}.zip\"");
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: {$size}");
                ob_end_flush();
                @readfile("{$config["arch_folder"]}/ricevutepec/{$_SESSION["ente"]["codice"]}/{$ricevuta}.zip");
                die();
            }
        }
    }
}

die('<meta http-equiv="refresh" content="0;URL=/index.php">');
?>