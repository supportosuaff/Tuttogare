<?
	include_once("../../../config.php");
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
		$bind = array();
		$bind[":codice_gara"] = $_POST["codice_gara"];
		$strsql= "SELECT codice FROM b_gare WHERE codice = :codice_gara";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount()>0) {
			$feedback_codice_riferimento = (!empty($_POST["codice_gara"])) ? $_POST["codice_gara"] : "";
			$feedback_dettaglio_riferimento = (!empty($_POST["codice_lotto"])) ? $_POST["codice_lotto"] : 0;
			$feedback_soggetti = (!empty($_POST["soggetto"])) ? $_POST["soggetto"] : "";
			$feedback_tipologia = "G";
			$error = true;
			include_once($root."/moduli/saveFeedback.php");
			if (!$error) {
				log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"INSERT","Inserito Feedback");
				$href = "/gare/pannello.php?codice=".$_POST["codice_gara"];
				?>
					alert('Salvataggio effettuato con successo');
					window.location.href = '<? echo $href ?>';
				<?
			} else {
				?>
				alert('Si sono verificati degli errori. Si prega di riprovare...');
				window.location.reload();
				<?
			}
		}
	}
?>
