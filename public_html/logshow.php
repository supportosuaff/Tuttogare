<?
    session_start();
    include_once '../config.php';
    if($_SESSION["gerarchia"] === "0") {
        $sql = "SELECT * FROM b_log WHERE `nometab` = 'b_rdo_ad' AND `codice` > 5544882 AND operazione = 'UPDATE' LIMIT 100";
        $ris = $pdo->go($sql);
        if($ris->rowCount() > 0) {
            while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
                ?><h5><?= base64_decode(gzuncompress($rec["istruzione"])); ?></h5><hr><?
            }
        }
    }
?>