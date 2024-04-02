<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	if (is_operatore() && isset($_GET["codice_offerta"])) {
		$bind = array();
		$bind[":codice"] = $_GET["codice_offerta"];
		$bind[":utente_modifica"] = $_SESSION["codice_utente"];
		$strsql = "SELECT * FROM b_offerte_economiche_asta WHERE stato = 0 AND codice = :codice AND utente_modifica = :utente_modifica";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount()>0) {
			$offer = $risultato->fetch(PDO::FETCH_ASSOC);
			if (isset($_SESSION["offerFile"][$offer["codice_partecipante"]])) {
				$content = $_SESSION["offerFile"][$offer["codice_partecipante"]];
			} else {
				$content = openssl_decrypt($offer["cryptedContent"],$config["crypt_alg"],md5($offer["codice_partecipante"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
				$_SESSION["offerFile"][$offer["codice_partecipante"]] = $content;
			}
			if ($content !== false) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename=' . $_GET["codice_offerta"] . '_rilancio_offerta.pdf');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				echo $content;
			} else {
				echo "<h1>Impossibile accedere</h1>";
			}
		} else {
			echo "<h1>Impossibile accedere</h1>";
		}
	} else {
		echo "<h1>Impossibile accedere</h1>";
	}
	?>
