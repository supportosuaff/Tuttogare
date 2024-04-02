<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
		if ($codice_fase !== false) {
			$esito = check_permessi_gara($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
		if (!$edit) {
			die();
		}
	} else {
		die();
	}
	if ($edit && !$lock) {

			$codice_gara = $_POST["codice_gara"];

			$sql = "DELETE FROM r_dgue_gare WHERE codice_gara = :codice_gara AND sezione = 'gare' ";
			$bind = array(":codice_gara"=>$codice_gara);
			$pdo->bindAndExec($sql,$bind);
			if (isset($_POST["form"])) {
				foreach($_POST["form"] AS $codice_form) {
					$object = array();
					$object["codice_gara"] = $codice_gara;
					$object["sezione"] = "gare";
					$object["codice_form"] = $codice_form;
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "r_dgue_gare";
					$salva->operazione = "INSERT";
					$salva->oggetto = $object;
					$salva->save();
				}
			}

			log_gare($_SESSION["ente"]["codice"],$codice_gara,"UPDATE","Configurazione DGUE");

			?>
			alert('Modifica effettuata con successo');
	    window.location.href = window.location.href;
	    <?

		}
?>
