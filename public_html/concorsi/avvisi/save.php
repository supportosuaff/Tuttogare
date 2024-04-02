<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$strsql = "SELECT * FROM b_conf_gestione_concorsi WHERE link = '/concorsi/avvisi/index.php'";
			$risultato = $pdo->query($strsql);
			if ($risultato->rowCount()>0) {
				$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
				$esito = check_permessi_concorso($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
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

			$_POST["codice_ente"] = $_SESSION["ente"]["codice"];

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_avvisi_concorsi";
			$salva->operazione = $_POST["operazione"];
			$salva->oggetto = $_POST;
			$codice = $salva->save();
			if ($codice != false) {
				log_concorso($_SESSION["ente"]["codice"],$_POST["codice_gara"],$_POST["operazione"],"Avviso - " . $_POST["titolo"]);

				if ($_POST["operazione"]=="UPDATE") {

					$href = "/concorsi/avvisi/index.php?codice=".$_POST["codice_gara"];
					?>
					alert('Modifica effettuata con successo');
					window.location.href = '<? echo $href ?>';
					<?
				} elseif ($_POST["operazione"]=="INSERT") {
					$href = "/concorsi/avvisi/index.php?codice=".$_POST["codice_gara"];
					?>
					alert('Inserimento effettuato con successo');
					window.location.href = '<? echo $href ?>';
					<?
				}
			} else {
				?>
				alert('Errore nel salvataggio. Si prega di riprovare');
				<?
			}
		}
	}



?>
