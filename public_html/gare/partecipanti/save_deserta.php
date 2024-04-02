<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	$lock = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
		if ($codice_fase !== false) {
			$esito = check_permessi_gara($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
		if (!$edit) die();
	} else {
		die();
	}

	if ($edit) {
		$bind = array();
		$bind[":codice_gara"] = $_POST["codice_gara"];
		$stato = 98;
		$deserta = "S";
		if (isset($_POST["nonAggiudicata"])) {
			$stato = 100;	
			$deserta = "Y";
		}
		$updateGara = true;
		if (!empty($_POST["codice_lotto"])) {
			$updateGara = false;
			$bindLotto = $bind;
			$bindLotto[":codice_lotto"] = $_POST["codice_lotto"];
			$sql  = "UPDATE b_lotti SET deserta = '{$deserta}' WHERE codice = :codice_lotto AND codice_gara = :codice_gara";
			$log = "Lotto #" . $_POST["codice_lotto"] . " deserto";
			$update_stato = $pdo->bindAndExec($sql,$bindLotto);
			$checkLotto = $pdo->go("SELECT deserta FROM b_lotti WHERE codice_gara = :codice_gara",[":codice_gara"=>$_POST["codice_gara"]]);
			$countDeserta = 0;
			$countNonAgg = 0;
			if ($checkLotto->rowCount() > 0) {
				$totaleLotti = $checkLotto->rowCount();
				while($lotto = $checkLotto->fetch(PDO::FETCH_ASSOC)) {
					if ($lotto["deserta"] == "S") $countDeserta++;
					if ($lotto["deserta"] == "Y") $countNonAgg++;
				}
				if ($countDeserta == $totaleLotti) {
					$stato = 98;
					$deserta = 'S';
					$updateGara = true;
				}
				if ($countNonAgg == $totaleLotti) {
					$stato = 100;
					$deserta = 'Y';
					$updateGara = true;
				}
			}
		}
		if ($updateGara) {
			$sql  = "UPDATE b_gare SET deserta = '{$deserta}', stato = {$stato} WHERE codice = :codice_gara";
			$log = "Gara deserta";
			$update_stato = $pdo->bindAndExec($sql,$bind);
		}
		
		if ($update_stato->rowCount()>0) {
			if (class_exists("syncERP")) {
        $syncERP = new syncERP();
        if (method_exists($syncERP,"sendUpdateRequest")) {
          $syncERP->sendUpdateRequest($_POST["codice_gara"]);
        }
      }
			
			log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE",$log);
			$href = "/gare/pannello.php?codice=".$_POST["codice_gara"];
			?>
			alert('Modifica effettuata con successo');
			window.location.href = '<? echo $href ?>';
		<? } else { ?>
			alert('Impossibile impostare lo stato');
			<?
		}
	}
?>
