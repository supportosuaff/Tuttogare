<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$content = false;
	if (is_operatore()) {
		if (isset($_GET["codice_partecipante"]) && isset($_GET["tipo"]) && isset($_SESSION["offerFileConcorso"][$_GET["codice_partecipante"]][$_GET["tipo"]])) {
			$content = $_SESSION["offerFileConcorso"][$_GET["codice_partecipante"]][$_GET["tipo"]];
		} else if (isset($_POST["salt"]) && !empty($_SESSION["concorsi"][$_POST["codice_gara"]][$_POST["codice_fase"]]["salt"])) {
			$sql = "SELECT * FROM b_offerte_concorso WHERE codice_partecipante = :codice_partecipante AND tipo = :tipo ";
			$ris = $pdo->bindAndExec($sql,array(":codice_partecipante"=>$_POST["codice_partecipante"],":tipo"=>$_POST["tipo"]));
			if ($ris->rowCount()>0) {
				$offer = $ris->fetch(PDO::FETCH_ASSOC);
				$content = openssl_decrypt($offer["cryptedContent"],$config["crypt_alg"],$_POST["salt"],OPENSSL_RAW_DATA,$config["enc_salt"]);
				$_SESSION["offerFileConcorso"][$_POST["codice_partecipante"]][$_POST["tipo"]] = $content;
			}
			$_GET["codice_partecipante"] = $_POST["codice_partecipante"];
			$_GET["tipo"] = $_POST["tipo"];
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
			echo "<h1>Impossibile accedere</h1>";
		}
	} else {
		echo "<h1>Impossibile accedere</h1>";
	}
	?>
