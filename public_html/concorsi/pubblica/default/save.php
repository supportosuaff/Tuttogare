<?
session_start();
include_once("../../../../config.php");
include_once($root."/inc/funzioni.php");
$edit = false;
$lock = true;
if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
	$codice_fase = getFaseRefererConcorso($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
	if ($codice_fase !== false) {
		$esito = check_permessi_concorso($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
		$edit = $esito["permesso"];
		$lock = $esito["lock"];
	}
	if (!$edit) {
		die();
	}
} else {
	die();
}
if ($edit)
{
	include($root."/concorsi/pubblica/save_common.php");
	if (isset($codice_gara) && $codice_gara == $_POST["codice_gara"]) {

		$href = "/concorsi/pannello.php?codice=" . $_POST["codice_gara"];
		$href = str_replace('"',"",$href);
		$href = str_replace(' ',"-",$href);
		?>
		alert('Pubblicazione effettuata con successo');
		window.location.href = '<? echo $href ?>';
		<?
	} else {
		?>
		alert('Errore nel salvataggio. Riprovare.');
		<?
	}
}
		?>
