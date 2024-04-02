<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
		if ($codice_fase !== false) {
			$esito = check_permessi_gara($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
		if (!$edit) {
			die();
		}
	} else {
		die();
	}
	if ($edit && !$lock) {
		$errore = false;
		if(! empty($_POST["aggiorna_stato"]) && $_POST["aggiorna_stato"] == "YES") {
			$pdo->bindAndExec("UPDATE b_gare SET stato = 5 WHERE codice = :codice", array(':codice' => $_POST["codice_gara"]));
		}
		foreach($_POST["partecipante"] as $partecipante) {
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "r_partecipanti";
			$salva->operazione = "UPDATE";
			$salva->oggetto = $partecipante;
			$codice = $salva->save();
			if ($codice === false) $errore = true;
		}
		if (!$errore) {
			log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Anomalie",false);
			$href = "/gare/pannello.php?codice=".$_POST["codice_gara"];
			?>
			alert('Elaborazione effettuata con successo');
			window.location.href = '<? echo $href ?>';
		<? } else { ?>
			alert('Si sono verificati errori nel salvataggio. Riprovare');
			<?
		}
	}
?>
