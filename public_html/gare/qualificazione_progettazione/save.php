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
	log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Qualificazione progettazione");
	$bind=array();
	$bind[":codice"] = $_POST["codice_gara"];
	$strsql = "DELETE FROM b_qualificazione_progettazione WHERE codice_gara = :codice";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if (isset($_POST["qualificazione"])) {
		foreach ($_POST["qualificazione"] as $record) {
			$record["codice_gara"] = $_POST["codice_gara"];
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_qualificazione_progettazione";
			$salva->operazione = "INSERT";
			$salva->oggetto = $record;
			$codice_qualificazione = $salva->save();
			if ($codice_qualificazione === false) $errore = true;
		}
	}
	if (!isset($errore)) {
		$href = "/gare/pannello.php?codice=" . $_POST["codice_gara"];
		$href = str_replace('"',"",$href);
		$href = str_replace(' ',"-",$href);
		?>
		alert('Modifica effettuata con successo');
		window.location.href = '<? echo $href ?>';
		<?
		} else {
			alert('Errore nel salvataggio. Riprovare.');
		}
	}
?>
