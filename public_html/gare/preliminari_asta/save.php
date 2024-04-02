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
	log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Dati preliminari asta");
	if (isset($_POST["asta"])) {
		foreach ($_POST["asta"] as $record) {
			$record["codice_gara"] = $_POST["codice_gara"];
			$operazione = "INSERT";
			$codice = 0;
			$bind = array();
			$bind[":codice_gara"] = $record["codice_gara"];
			$bind[":codice_lotto"] = $record["codice_lotto"];
			$sql = "SELECT * FROM b_aste WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto";
			$ris = $pdo->bindAndExec($sql,$bind);
			if ($ris->rowCount()>0) {
				$operazione = "UPDATE";
				$existent = $ris->fetch(PDO::FETCH_ASSOC);
				$codice = $existent["codice"];
			}
			$record["codice"] = $codice;

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_aste";
			$salva->operazione = $operazione;
			$salva->oggetto = $record;
			$codice_asta = $salva->save();

		}
	}
	$href = "/gare/pannello.php?codice=" . $_POST["codice_gara"];
	$href = str_replace('"',"",$href);
	$href = str_replace(' ',"-",$href);
	?>
	alert('Modifica effettuata con successo');
	window.location.href = '<? echo $href ?>';
	<?
}
?>
