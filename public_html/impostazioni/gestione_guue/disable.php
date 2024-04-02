<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	} else {
		die();
	}

	if ($edit) {
		if (isset($_POST["codice"]) && $_SESSION["gerarchia"]==0) {
			$codice = $_POST["codice"];
			$strsql = "SELECT attivo FROM b_gestione_guue WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql, array(':codice' => $codice));
			$attivo = "S";
			$colore = "#3C0";
			if ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
				$attivo = $record["attivo"];
				if ($attivo == "S") {
					$attivo = "N";
					$colore = "#C00";
				} else {
					$attivo = "S";
					$colore = "#3C0";
				}
			}

			$strsql = "UPDATE b_gestione_guue SET attivo = :attivo WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql, array(':attivo' => $attivo, ':codice' => $codice));
			scrivilog("b_gestione_guue","UPDATE",$strsql,$_SESSION["codice_utente"]);
			
			?>
			if ($("#flag_<?= $codice ?>").length > 0){
      	$("#flag_<?= $codice ?>").css('background-color',"<?= $colore ?>");
      }
			<?
		}
	}

?>
