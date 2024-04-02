<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFase("","/gare/apribuste/edit.php");
		if ($codice_fase !== false) {
			$esito = check_permessi_gara($codice_fase,$_GET["codice_gara"],$_SESSION["codice_utente"]);
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
		$sql = "SELECT * FROM b_gare WHERE codice = :codice_gara";
		$ris = $pdo->bindAndExec($sql,array(":codice_gara"=>$_GET["codice_gara"]));
		if ($ris->rowCount() > 0) {
			$rec = $ris->fetch(PDO::FETCH_ASSOC);
			if ($rec["seduta_pubblica"] == "N") {
				$rec["seduta_pubblica"] = "S";
				$msg = "Chiudi Seduta pubblica";
			} else {
				$rec["seduta_pubblica"] = "N";
				$msg = "Apri seduta pubblica";
				$class= "btn-danger";
			}
			$sql = "UPDATE b_gare SET seduta_pubblica = :seduta_pubblica WHERE codice = :codice_gara";
			$ris = $pdo->bindAndExec($sql,array("seduta_pubblica"=>$rec["seduta_pubblica"],":codice_gara"=>$_GET["codice_gara"]));
			if ($ris->rowCount() > 0) {
				$msg_log = "Chiudi seduta pubblica";
				if ($rec["seduta_pubblica"] == "S") $msg_log = "Apri Seduta pubblica";
				log_gare($_SESSION["ente"]["codice"],$_GET["codice_gara"],"UPDATE",$msg_log);
				?>
				$("#seduta_pubblica").html('<?= $msg ?>');
				<?
				if ($rec["seduta_pubblica"] == "N") { ?>
					$("#seduta_pubblica").removeClass('button-action').addClass('btn-danger');
				<? } else { ?>
					$("#seduta_pubblica").removeClass('btn-danger').addClass('button-action');
				<?
				}
			}
		}
	}

?>
