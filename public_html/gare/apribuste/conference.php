<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	include_once($root."/inc/zoomMtg.class.php");
	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFase("","/gare/apribuste/edit.php");
		if ($codice_fase !== false && check_permessi("conference",$_SESSION["codice_utente"])) {
			$esito = check_permessi_gara($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
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
		$zoom = new zoomMtg;
		$meeting = $zoom->getMeetingFromDB("gare",$_GET["codice"],$_GET["sub_elemento"],"seduta pubblica");
		if (!empty($meeting)) {
			$meeting = json_decode($meeting["response"],true);
			$status = $zoom->getMeetingDetails($meeting["id"]);
			if (empty($status["status"]) || $status["status"] == "finished") {
				unset($meeting);
			} else {
				if (empty($_GET["force"])) {
					?>
					<h1>Una conference room è già stata avviata</h1> 
					Per annullare il meeting in corso e lanciarne uno nuovo, <a href="conference.php?codice=<?= $_GET["codice"] ?>&sub_elemento=<?= $_GET["sub_elemento"] ?>&force=1">Clicca qui</a>
					<?
					die();
				}
			}
		}
		if (empty($meeting)) {
			$meeting = $zoom->createMeeting("gare",$_GET["codice"],$_GET["sub_elemento"],"seduta pubblica");
		}
		if (!empty($meeting)) {
			if (!empty($meeting["id"])) {
				header("location: {$meeting["start_url"]}");
				die();
			}
		}
	}
	echo "<h1>Si è verificato un errore</h1>";
?>
