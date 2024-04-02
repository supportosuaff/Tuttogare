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
			$bind = array();
			$bind[":codice_gara"] = $_POST["codice_gara"];
			$strsql= "SELECT b_gare.*, b_procedure.nome AS nome_procedura FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice WHERE b_gare.codice = :codice_gara";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount()>0) {
				$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
				$sql = "UPDATE b_gare SET stato = 8 WHERE codice = :codice_gara";
				$update_stato = $pdo->bindAndExec($sql,$bind);
				log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"Invio","Esito di gara");

				$documentale = array();
				$documentale["codice"] = $_POST["codice"];
				$documentale["codice_gara"] = $_POST["codice_gara"];
				$documentale["codice_lotto"] = $_POST["codice_lotto"];
				$documentale["codice_ente"] = $_SESSION["ente"]["codice"];
				$documentale["tipo"] = "comunicazione_esito";
				$documentale["corpo"] = $_POST["corpo"];

				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_documentale";
				$salva->operazione = $_POST["operazione"];
				$salva->oggetto = $documentale;
				$codice_elemento = $salva->save();

				$oggetto = "Esito di gara: " . $record_gara["oggetto"];

				$corpo = $_POST["corpo"];

				$mailer = new Communicator();
				$mailer->oggetto = $oggetto;
				$mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
				$mailer->codice_pec = $record_gara["codice_pec"];
				$mailer->comunicazione = true;
				$mailer->coda = false;
				$mailer->sezione = "gara";
				$mailer->codice_gara = $record_gara["codice"];
				$mailer->codice_lotto = $_POST["codice_lotto"];
				$esito = $mailer->send();
				if ($esito !== true) {
					echo "alert(\"" . $esito . "\");";
				} else {
					$href = "/gare/pannello.php?codice=".$_POST["codice_gara"];
					if (class_exists("syncERP")) {
						$syncERP = new syncERP();
						if (method_exists($syncERP,"sendUpdateRequest")) {
							$syncERP->sendUpdateRequest($_POST["codice_gara"],"esito");
						}
					}
					
					?>
					alert('Invio effettuato con successo');
			    	window.location.href = '<? echo $href ?>';
          <?
				}
			}
		}
?>
