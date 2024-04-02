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
		$bind = array();
		$bind[":codice_gara"] = $_POST["codice_gara"];
		$strsql = "SELECT * FROM b_gare WHERE codice = :codice_gara";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

		if ($_POST["codice_lotto"] != 0) {
			$bind = array();
			$bind[":codice"] = $_POST["codice_lotto"];
			$strsql = "SELECT * FROM b_lotti WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$record_lotto = $risultato->fetch(PDO::FETCH_ASSOC);
		}

		$saveScript = __DIR__."/201650/save.php";
		if ($record_gara["norma"] == "2023-36") {
			$saveScript = __DIR__."/202336/save.php";
		}
		include $saveScript;
		if (empty($error)) { 
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = $tabella;
			$salva->operazione = "UPDATE";
			$salva->oggetto = $array_update;
			$codice = $salva->save();
			if ($codice > 0) {
				$href = "/gare/pannello.php?codice=".$_POST["codice_gara"];
				log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Modalit&agrave; calcolo anomalia");
			?>
				alert('Elaborazione effettuata con successo');
				window.location.href = window.location.href;
				<?
			} else {
				?>
				alert('Errore nel salvataggio');
				<?
			}
		} else {
			?>
			alert('<?= $error ?>');
			<?
		}
	} else {
		?>
			alert('Si è verificato un errore durante il salvataggio. Riprovare');
		<?
	}

?>
