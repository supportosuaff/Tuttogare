<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	include_once($root."/inc/zoomMtg.class.php");
	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFase("","/gare/apribuste/edit.php");
		$strsql = "SELECT * FROM b_gestione_gare WHERE link LIKE '/gare/commissione/edit.php%'";
		$risultato = $pdo->query($strsql);
		if ($risultato->rowCount()>0 &&  check_permessi("conference",$_SESSION["codice_utente"])) {
			$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
			$esito = check_permessi_gara($gestione["codice"],$_GET["codice"],$_SESSION["codice_utente"]);
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
		$contesto = "seggio di gara";
		$bind = array();
		$bind[":codice"]=$_GET["codice"];
		$sql = "SELECT * FROM b_commissioni WHERE codice_gara = :codice AND valutatore = ";
		if (isset($_GET["tecnica"]) && $_GET["tecnica"] == "1") {
			$tecnica = "&tecnica=1";
			$contesto = "commissione valutatrice";
			$sql .= "'S'";
		} else {
			$tecnica = "";
			$sql .= "'N'";
		}
		$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
		if ($ris_partecipanti->rowCount() > 0) {
			$sql_gara = "SELECT `b_gare`.`oggetto`, `b_gare`.`codice_pec` FROM `b_gare` WHERE `b_gare`.`codice` = :codice";
			$ris_gara = $pdo->bindAndExec($sql_gara,$bind);
			if ($ris_gara->rowCount() > 0)
			{
				$record_gara = $ris_gara->fetch(PDO::FETCH_ASSOC);
				$zoom = new zoomMtg;
				$meeting = $zoom->getMeetingFromDB("gare",$_GET["codice"],0,$contesto);
				if (!empty($meeting)) {
					$meeting = json_decode($meeting["response"],true);
					$status = $zoom->getMeetingDetails($meeting["id"]);
					if (empty($status["status"]) || $status["status"] == "finished") {
						unset($meeting);
					}	else {
						if (empty($_GET["force"])) {
							?>
							<h1>Una conference room è già stata avviata</h1> 
							Per annullare il meeting in corso e lanciarne uno nuovo, <a href="conference.php?codice=<?= $_GET["codice"] ?>&sub_elemento=0<?= $tecnica ?>&force=1">Clicca qui</a>
							<?
							die();
						}
					}
				}
				if (empty($meeting)) {
					$meeting = $zoom->createMeeting("gare",$_GET["codice"],0,$contesto);
				}
				if (!empty($meeting)) {
					if (!empty($meeting["id"])) {
						$destinatari[] = "";
						while($commissario = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
							if (!empty($commissario["pec"])) $destinatari[] = $commissario["pec"];
						}
						if (!empty($destinatari)) {
							$url = $meeting["join_url"];
							$oggetto = "Conference Room Gara #" . $record_gara["id"] . " - " . $record_gara["oggetto"];
							$corpo = "Clicca sul link sottostante per accedere alla conference room<br>";
							$corpo.= 'URL: <a href="'.$url.'">'.$url.'</a>';
				
							$mailer = new Communicator();
							$mailer->oggetto = $oggetto;
							$mailer->corpo = $corpo;
							$mailer->codice_pec = -1;
							$mailer->comunicazione = false;
							$mailer->coda = false;
							$mailer->destinatari = $destinatari;
							$mailer->send();
						}
						header("location: {$meeting["start_url"]}");
						die();
					}
				}
			}
		}
	}
	echo "<h1>Si è verificato un errore</h1>";
?>
