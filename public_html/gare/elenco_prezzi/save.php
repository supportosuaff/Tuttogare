<?
session_start();
include_once("../../../config.php");
include_once($root."/inc/funzioni.php");
$edit = false;
$lock = true;
if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
	$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/elenco_prezzi/edit.php'";
	$risultato = $pdo->query($strsql);
	if ($risultato->rowCount()>0) {
		$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
		$esito = check_permessi_gara($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
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
	log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Elenco Prezzi");
	if (isset($_POST["prezzo"])) {
		foreach ($_POST["prezzo"] as $record) {
			$record["codice_gara"] = $_POST["codice_gara"];
			if (isset($_POST["codice_criterio"])) $record["codice_criterio"] = $_POST["codice_criterio"];
			$operazione = "INSERT";
			$codice = 0;
			if (is_numeric($record["codice"])) {
				$operazione = "UPDATE";
				$codice = $record["codice"];
			}
			$record["codice"] = $codice;
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_elenco_prezzi";
			$salva->operazione = $operazione;
			$salva->oggetto = $record;
			$codice_prezzo = $salva->save();

		}
	}
	$href = "/gare/elenco_prezzi/edit.php?codice=" . $_POST["codice_gara"];
	$href = str_replace('"',"",$href);
	$href = str_replace(' ',"-",$href);
	?>
	alert('Modifica effettuata con successo');
	window.location.href = '<? echo $href ?>';
	<?
}
?>
