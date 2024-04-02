<?
	session_start();
	include_once '../../config.php';
	include_once $root . '/inc/funzioni.php';

	$edit = FALSE;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("guue",$_SESSION["codice_utente"]);
		if (!$edit) die();
	} else {
		die();
	}

	if($edit) {
		if(!empty($_POST["codici"])) {
			$codici = implode(",", array_filter(explode(";", $_POST["codici"])));
			$sql = "SELECT * FROM b_cpv WHERE codice IN (".$codici.")";
			$ris = $pdo->bindAndExec($sql);
			if($ris->rowCount() > 0) {
				?><option value="">Seleziona..</option><?
				while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
					?><option value="<?= $rec["codice"] ?>"><?= $rec["descrizione"] ?></option><?
				}
			}
		}
	}
?>