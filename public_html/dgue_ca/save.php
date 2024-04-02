<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("dgue_ca",$_SESSION["codice_utente"]);
	}
	if ($edit && !empty($_POST) && isset($_POST["codice"])) {
			$operazione = "INSERT";
			if ($_POST["codice"] > 0) {
				$operazione = "UPDATE";
			}

			$_POST["codice_ente"] = $_SESSION["ente"]["codice"];
			$_POST["codice_gestore"] = $_SESSION["ente"]["codice"];
			if (!empty($_SESSION["record_utente"]["codice_ente"])) $_POST["codice_ente"] = $_SESSION["record_utente"]["codice_ente"];

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_dgue_free";
			$salva->operazione = $operazione;
			$salva->oggetto = $_POST;
			$codice_gara = $salva->save();
			if ($codice_gara > 0) {
				$sql = "DELETE FROM r_dgue_gare WHERE codice_gara = :codice_gara AND sezione = 'free' ";
				$bind = array(":codice_gara"=>$codice_gara);
				$pdo->bindAndExec($sql,$bind);
				if (isset($_POST["form"])) {
					foreach($_POST["form"] AS $codice_form) {
						$object = array();
						$object["codice_gara"] = $codice_gara;
						$object["sezione"] = "free";
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

				$href = "/dgue_ca/index.php";
				$href = str_replace('"',"",$href);
				$href = str_replace(' ',"-",$href);
				$success = true;
				?>
				alert('Operazione effettuata con successo');
		    window.location.href = '<? echo $href ?>';
		    <?
			}
		}
		if (empty($success)) {
			?>
			alert('Si è verificato un errore');
			<?
		}
?>
