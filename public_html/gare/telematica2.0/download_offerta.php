<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$content = false;
	if (is_operatore() && isset($_GET["codice_partecipante"]) && isset($_GET["tipo"])) {
		if (isset($_SESSION["offerFile"][$_GET["codice_partecipante"]][$_GET["tipo"]])) {
			$content = $_SESSION["offerFile"][$_GET["codice_partecipante"]][$_GET["tipo"]];
		} else if ($_GET["salt"]) {
			$sql = "SELECT * FROM b_offerte_economiche WHERE codice_partecipante = :codice_partecipante AND tipo = :tipo AND utente_modifica = :codice_utente ";
			$ris = $pdo->bindAndExec($sql,array(":codice_partecipante"=>$_GET["codice_partecipante"],":tipo"=>$_GET["tipo"],":codice_utente"=>$_SESSION["codice_utente"]));
			if ($ris->rowCount()>0) {
				$offer = $ris->fetch(PDO::FETCH_ASSOC);
				$content = openssl_decrypt($offer["cryptedContent"],$config["crypt_alg"],$_GET["salt"],OPENSSL_RAW_DATA,$config["enc_salt"]);
				$_SESSION["offerFile"][$_GET["codice_partecipante"]][$_GET["tipo"]] = $content;
			}
		}
		if ($content !== false) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.$_GET["codice_partecipante"].'_offerta_'.$_GET["tipo"].'.pdf');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			echo $content;
		} else {
			echo "<h1>". traduci("Impossibile accedere") . "</h1>";
		}
	} else {
		echo "<h1>". traduci("Impossibile accedere") . "</h1>";
	}
	?>
