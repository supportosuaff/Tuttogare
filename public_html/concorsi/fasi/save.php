<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;

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
		if ($edit && !$lock)
	 {
			$array_id = array();
			$errore = false;

			$bind = array();
			$bind[":codice_gara"] = $_POST["codice_gara"];
			/* $strsql = "DELETE FROM r_step_valutazione_concorsi WHERE codice_criterio IN (SELECT codice FROM b_criteri_valutazione_concorsi WHERE codice_gara = :codice_gara)";
			$risultato = $pdo->bindAndExec($strsql,$bind); */
			$strsql = "DELETE FROM b_criteri_valutazione_concorsi WHERE codice_gara = :codice_gara";
			$risultato = $pdo->bindAndExec($strsql,$bind);


			foreach($_POST["fase"] as $fase) {
				$operazione = "INSERT";
				if (is_numeric($fase["codice"])) $operazione = "UPDATE";
				$fase["codice_concorso"] = $_POST["codice_gara"];
				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_fasi_concorsi";
				$salva->operazione = $operazione;
				$salva->oggetto = $fase;
				$codice_fase = $salva->save();
				if ($codice_fase != false) {
					if (isset($fase["criterio_valutazione"])) {
						$ids = array();
						foreach ($fase["criterio_valutazione"] as $record) {
							$record["codice_gara"] = $_POST["codice_gara"];
							$record["codice_fase"] = $codice_fase;
							if ($record["codice_padre"] != "0" && $record["codice_padre"] != "") {
								$record["tipo"] = $ids[$record["codice_padre"]]["tipo"];
								$record["codice_padre"] = $ids[$record["codice_padre"]]["codice"];
							} else {
								$record["codice_padre"] = "0";
							}

							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_criteri_valutazione_concorsi";
							$salva->operazione = "INSERT";
							$salva->oggetto = $record;
							$codice_criterio = $salva->save();

							$ids[$record["codice"]] = array();
							$ids[$record["codice"]]["codice"] = $codice_criterio;
							$ids[$record["codice"]]["tipo"] = $record["tipo"];
							if (isset($record["valutazione"])) $ids[$record["codice"]]["valutazione"] = $record["valutazione"];
						}
						/* if (isset($_POST["step_valutazione"])) {
							foreach ($_POST["step_valutazione"] as $step) {
								if (isset($ids[$step["codice_criterio"]]["valutazione"]) && $ids[$step["codice_criterio"]]["valutazione"] == "S") {
									$step["codice_criterio"] = $ids[$step["codice_criterio"]]["codice"];
									$salva = new salva();
									$salva->debug = false;
									$salva->codop = $_SESSION["codice_utente"];
									$salva->nome_tabella = "r_step_valutazione_concorsi";
									$salva->operazione = "INSERT";
									$salva->oggetto = $step;
									$codice_step = $salva->save();
								}
							}
						} */
					}
					$bind = array();
					$bind[":codice"] = $_POST["codice_gara"];

					$sql = "UPDATE b_concorsi SET stato = 2 WHERE codice = :codice";
					$update_stato = $pdo->bindAndExec($sql,$bind);
				} else {
					$errore = true;
				}
			}
			log_concorso($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Fasi",false);
			if (!$errore) {
				$href = "/concorsi/pannello.php?codice=".$_POST["codice_gara"];
				?>
				alert('Modifica effettuata con successo');
        window.location.href = '<? echo $href ?>';
        <?
			} else {
				?>
				alert('Si sono verificati degli errori durante il salvataggio');
				window.location.reload();
				<?
			}
	}

?>
