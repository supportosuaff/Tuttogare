<?
	session_start();
	include_once("../../../config.php");
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
		if ($edit && !$lock) {
			$bind = array();
			$bind[":codice_gara"] = $_POST["codice_gara"];
			$bind[":codice_fase"] = $_POST["codice_fase"];
			$strsql = "SELECT * FROM r_partecipanti_concorsi WHERE codice_gara = :codice_gara AND codice_fase = :codice_fase AND ammesso = 'N'";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$array_esclusi = array();
			if ($risultato->rowCount()>0) {
				while($record=$risultato->fetch(PDO::FETCH_ASSOC)) {
					$array_esclusi[] = $record["codice"];
				}
			}

			$array_id = array();
			$bind = array();
			$bind[":codice_gara"] = $_POST["codice_gara"];
			$strsql = "SELECT * FROM b_concorsi WHERE codice = :codice_gara";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
			$cig = $record_gara["cig"];
			$errore_save = false;
			foreach($_POST["partecipante"] as $partecipante) {

				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "r_partecipanti_concorsi";
				$salva->operazione = "UPDATE";
				$salva->oggetto = $partecipante;
				$codice = $salva->save();
				if ($codice == false) $errore = true;
			}
			if (!isset($errore) || (isset($errore) && !$errore)) {
				$bind = array();
				$bind[":codice_gara"] = $_POST["codice_gara"];

					$errore = false;
					$msg = "";
					if ($_POST["calcola_graduatoria"] == 'S') {
						include("calcolo.php");
						if (!$errore) {
							$bind = array();
							$bind[":codice_gara"] = $_POST["codice_gara"];
							$sql = "UPDATE b_concorsi SET stato = 4 WHERE codice = :codice_gara";
							$update_stato = $pdo->bindAndExec($sql,$bind);
						} else {
							?>
								alert('<? echo trim($msg); ?>');
							<?
						}
					}
					$href = "/concorsi/pannello.php?codice=".$_POST["codice_gara"];
					if ($msg!="") { ?>
						alert('<? echo trim($msg); ?>');
					<? } else {	?>
						alert('Elaborazione effettuata con successo');
					<? }  ?>
						window.location.href = window.location.href;
					<?
						log_concorso($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Modifica graduatoria provvisoria");
			} else {
				?>
					alert('Si è verificato un errore durante il salvataggio. Riprovare');
				<?
			}
	}

?>
