<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFase("","/gare/apribuste/edit.php");
		if ($codice_fase !== false && check_permessi("conference",$_SESSION["codice_utente"])) {
			$esito = check_permessi_gara($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
		}
		if ($edit)
		{
			$strsql  = "SELECT b_emendamenti.* FROM b_emendamenti
						WHERE codice_gara = :codice_gara AND codice = :codice AND aperto = 'S'";
			$risultato = $pdo->bindAndExec($strsql,[":codice_gara"=>$_POST["codice_gara"],":codice"=>$_POST["codice"]]);
			if ($risultato->rowCount() > 0) {
				$emendamento = $risultato->fetch(PDO::FETCH_ASSOC);
				if ($_POST["accettato"] == "S") {
					$_POST["motivazione"] = "";
					$oggetto = "Emendamento accettatto";
					$corpo = "La tua richiesta di emedamento, per la gara in oggetto, è stata accettata";
				} else if ($_POST["accettato"] == "N") { 
					$oggetto = "Emendamento rifiutato";
					$corpo = "La tua richiesta di emedamento, per la gara in oggetto è stata rifiutata con la seguente motivazione:<br>". $_POST["motivazione"];
				}
				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_emendamenti";
				$salva->operazione = "UPDATE";
				$salva->oggetto = ["codice"=>$emendamento["codice"],"accettato"=>$_POST["accettato"],"motivazione"=>$_POST["motivazione"]];
				$codice = $salva->save();
				$sql = "SELECT codice,oggetto,codice_pec FROM b_gare WHERE codice = :codice_gara";
				$ris = $pdo->bindAndExec($sql,array(":codice_gara"=>$emendamento["codice_gara"]));
				if ($ris->rowCount() > 0) {
					$rec = $ris->fetch(PDO::FETCH_ASSOC);
					$mailer = new Communicator();
					$mailer->oggetto = "{$oggetto} - " . $rec["oggetto"];
					$mailer->corpo = "<h2>" . $mailer->oggetto . "</h2> {$corpo}<br><br>Maggiori informazioni al link: ";
					$mailer->corpo .= "<br><a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $rec["codice"] . "-dettagli\" title=\"Dettagli gara\"><strong>" . $rec["oggetto"] . "</strong></a><br><br>";
					$mailer->codice_pec = $rec["codice_pec"];
					$mailer->comunicazione = true;
					$mailer->coda = true;
					$mailer->sezione = "gara";
					$mailer->codice_gara = $rec["codice"];
					$mailer->codice_lotto = $emendamento["codice_lotto"];
					$esito = $mailer->send();
				}
				?>
				showInfoEmendamento(<?= $emendamento["codice_gara"] ?>,<?= $emendamento["codice_partecipante"] ?>,<?= $emendamento["codice"] ?>);
				<?
				die();
			}
		}
	} 
	header("403 Forbidden");
	echo "<h1>Permesso negato</h1>"
?>
