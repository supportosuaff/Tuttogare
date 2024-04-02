<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && ! is_operatore()) {
			$edit = true;
	} else {
		die();
	}
	if (!$edit) {
		die();
	} else {
		if (isset($_POST["codice"])) {
			if (class_exists("syncERP")) {
				$sync = new syncERP();
				$sync->deleteAllegato($_POST["codice"]);
			}
			$codice = $_POST["codice"];
			$bind = array();
			$bind[":codice"] = $codice;
			$sql = "DELETE FROM b_allegati WHERE codice = :codice ";
			if ($_SESSION["gerarchia"]>0) {
				if (isset($_SESSION["ente"])) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$bind[":codice_utente"] = $_SESSION["codice_utente"];
					$sql .= " AND codice_ente = :codice_ente";
					$sql .= " AND (codice_ente = :codice_ente_utente OR utente_modifica = :codice_utente)";
				} else {
					die();
				}
			}
			$ris = $pdo->bindAndExec($sql,$bind);
			if ($ris->rowCount() > 0) {
				?>
				if ($("#allegato_<? echo $codice ?>").length > 0){
	            	$("#allegato_<? echo $codice ?>").slideUp();
	            }
	            if ($("#cod_allegati").length > 0) {
	           		var str_allegati = $("#cod_allegati").val();
					str_allegati  = str_allegati.trim();
					var allegati = new Array();
					allegati= str_allegati.split(";");
		            var pos = $.inArray('<? echo $codice ?>',allegati);
					allegati.splice(pos,1);
	                str_allegati = allegati.join(";");
					$("#cod_allegati").val(str_allegati);
	            }
				<?
			} else {
				?>
				alert('Non si dispone dei permessi necessari, impossibile procedere.');
				<?
			}
		}
	}

?>
