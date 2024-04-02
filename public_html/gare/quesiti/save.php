<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/quesiti/index.php'";
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
		if ($edit && !$lock)
	 {
		if (isset($_POST["operazione"])) {
			if ($_POST["operazione"] == "INSERT") {
				$quesito = array();
				$quesito["titolo"] = $_POST["titolo"];
				$quesito["codice_gara"] = $_POST["codice_gara"];
				$quesito["codice_ente"] = $_SESSION["ente"]["codice"];
				$quesito["codice"] = $_POST["codice"];

				$bind=array();
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

				$quesiti_residui = quesitiResidui();
				if ($quesiti_residui > 0) {
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_consulenze";
					$salva->operazione = "INSERT";
					$salva->oggetto = $quesito;
					$codice_quesito = $salva->save();
					if ($codice_quesito === false) $errore = true;
				}
			} else if ($_POST["codice"] > "0") {

				$bind=array();
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$bind[":codice"] = $_POST["codice"];
				$sql_quesiti = "SELECT * FROM b_consulenze WHERE codice_ente = :codice_ente AND codice = :codice";
				$ris_quesiti = $pdo->bindAndExec($sql_quesiti,$bind);
				if ($ris_quesiti->rowCount() > 0) $codice_quesito = $_POST["codice"];
			}
			$href = "/gare/quesiti/index.php?codice=".$_POST["codice_gara"];
			if ((isset($codice_quesito) && $codice_quesito != false) && $_POST["testo"] != "") {
				$msg = array();
				$operazione = "INSERT";
				$msg["codice"] = 0;
				if ($_POST["codice_msg"] > 0) {
					$operazione = "UPDATE";
					$msg["codice"] = $_POST["codice_msg"];
				}
				$msg["codice_quesito"] = $codice_quesito;
				$msg["testo"] = $_POST["testo"];

				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_conversazioni";
				$salva->operazione = $operazione;
				$salva->oggetto = $msg;
				$codice_msg = $salva->save();

				?>alert('Inserimento effettuato con successo');<?
			} else {
				?>alert('Errore nel salvataggio');<?
			}
		?>
			window.location.href = '<? echo $href ?>';
		<?
	}
}
?>
