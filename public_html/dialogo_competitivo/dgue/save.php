<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("dialogo_competitivo",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	if ($edit) {

			$codice_gara = $_POST["codice_gara"];

			$sql = "DELETE FROM r_dgue_gare WHERE codice_gara = :codice_gara AND sezione = 'dialogo' ";
			$bind = array(":codice_gara"=>$codice_gara);
			$pdo->bindAndExec($sql,$bind);
			if (isset($_POST["form"])) {
				foreach($_POST["form"] AS $codice_form) {
					$object = array();
					$object["codice_gara"] = $codice_gara;
					$object["sezione"] = "dialogo";
					$object["codice_form"] = $codice_form;
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "r_dgue_gare";
					$salva->operazione = "INSERT";
					$salva->oggetto = $object;
					$salva->save();
				}
				$sql = "SELECT * FROM b_modulistica_dialogo WHERE titolo = 'DGUE' AND codice_bando = :codice_gara ";
				$bind = array(":codice_gara"=>$codice_gara);
				$ris = $pdo->bindAndExec($sql,$bind);
				if ($ris->rowCount() == 0) {
					$object = array();
					$object["codice_bando"] = $codice_gara;
					$object["codice_ente"] = $_SESSION["ente"]["codice"];
					$object["titolo"] = "DGUE";
					$object["obbligatorio"] = "S";
					$object["attivo"] = "S";
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_modulistica_dialogo";
					$salva->operazione = "INSERT";
					$salva->oggetto = $object;
					$salva->save();
				}
			}

			$href = "/dialogo_competitivo/dgue/edit.php?codice=" . $codice_gara;
			$href = str_replace('"',"",$href);
			$href = str_replace(' ',"-",$href);
			?>
			alert('Modifica effettuata con successo');
	    window.location.href = '<? echo $href ?>';
	    <?

		}
?>
