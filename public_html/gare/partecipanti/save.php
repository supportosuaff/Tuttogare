<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;

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
		if ($edit && !$lock)
	 {
			$array_id = array();
			$errore = false;
			foreach($_POST["partecipante"] as $partecipante) {

				if (isset($partecipante["codice_capogruppo"])) {
					$partecipante["codice_capogruppo"] = $array_id[$partecipante["codice_capogruppo"]];
				}

				$operazione = "INSERT";
				if (is_numeric($partecipante["codice"])) $operazione = "UPDATE";

				$partecipante["codice_gara"] = $_POST["codice_gara"];
				$partecipante["codice_lotto"] = $_POST["codice_lotto"];

				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "r_partecipanti";
				$salva->operazione = $operazione;
				$salva->oggetto = $partecipante;
				$codice = $salva->save();
				if ($codice != false) {
				?>
					$("#codice_partecipante_<? echo $partecipante["id"] ?>").val("<? echo $codice ?>");
					<?
					$array_id[$partecipante["id"]] = $codice;
				} else {
					$errore = true;
					?>
						$("#partecipante_<? echo $partecipante["id"] ?>").addClass("errore");
					<?
				}
			}
			if (!$errore) {
			$bind = array();
			$bind[":codice"] = $_POST["codice_gara"];

			$strsql = "SELECT * FROM r_partecipanti WHERE tipo = '04-CAPOGRUPPO' AND codice_gara = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$errore = false;
			if ($risultato->rowCount()>0) {
				while($capogruppo = $risultato->fetch(PDO::FETCH_ASSOC)) {
					$bind = array();
					$bind[":codice"] = $capogruppo["codice"];
					$sql = "SELECT * FROM r_partecipanti WHERE codice_capogruppo = :codice";
					$ris = $pdo->bindAndExec($sql,$bind);
					if ($ris->rowCount()==0) {
						$errore = true;
						$id_capogruppo = array_search($capogruppo["codice"],$array_id,true);
						?>
							$("#partecipante_<? echo $id_capogruppo ?>").addClass("errore");
						<?
					}
				}
			}
			if (class_exists("syncERP")) {
        $syncERP = new syncERP();
        if (method_exists($syncERP,"sendUpdateRequest")) {
          $syncERP->sendUpdateRequest($_POST["codice_gara"]);
        }
      }
			
			log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Partecipanti",false);
			if (!$errore) {
				$href = "/gare/pannello.php?codice=".$_POST["codice_gara"];
				?>
				alert('Modifica effettuata con successo');
            	window.location.href = '<? echo $href ?>';
           	 <?
			} else {
				?>
					alert('Inserire i partecipanti al raggruppamento.');
				<?
			}
		} else {
			?>
			alert('Si sono verificati degli errori durante il salvataggio');
			<?
		}
	}

?>
