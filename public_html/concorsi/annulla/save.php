<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFaseRefererConcorso($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
		if ($codice_fase !== false) {
			$esito = check_permessi_concorso($codice_fase,$_POST["codice"],$_SESSION["codice_utente"]);
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

		$_POST["stato"] = 99;
		$_POST["annullata"] = "S";

		$salva = new salva();
		$salva->debug = false;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_concorsi";
		$salva->operazione = "UPDATE";
		$salva->oggetto = $_POST;
		$codice_gara = $salva->save();
		if ($codice_gara != false) {
			log_concorso($_SESSION["ente"]["codice"],$_POST["codice"],"UPDATE","Annullamento concorso");

			$href = "/concorsi/pannello.php?codice=" . $_POST["codice"];
			$href = str_replace('"',"",$href);
			$href = str_replace(' ',"-",$href);
			?>
				alert('Annullamento effettuato con successo');
				window.location.href = '<? echo $href ?>';
		<?
		} else {
			?>
			alert('Errore nel salvataggio. Riprovare');
			<?
		}
	} ?>
