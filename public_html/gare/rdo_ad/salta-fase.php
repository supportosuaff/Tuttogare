<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/rdo_ad/index.php'";
		$risultato = $pdo->query($strsql);
		if ($risultato->rowCount()>0) {
			$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
			$esito = check_permessi_gara($gestione["codice"],$_POST["codice"],$_SESSION["codice_utente"]);
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
		if (!empty($_POST["partecipanti"])) {
			log_gare($_SESSION["ente"]["codice"],$_POST["codice"],"INSERT","Inseriti dati aggiudicatario");

			$bind = array();
			$bind[":codice_gara"] = $_POST["codice"];
			$sql = "DELETE FROM r_partecipanti WHERE codice_gara = :codice_gara ";
			$delete_partecipanti = $pdo->bindAndExec($sql,$bind);
			foreach($_POST["partecipanti"] AS $tmp) {
				$codice_lotto = $pdo->go("SELECT codice FROM b_lotti WHERE codice_gara = :codice_gara",[":codice_gara"=>$_POST["codice"]])->fetch(PDO::FETCH_COLUMN);
				$partecipante = array();
				$partecipante["codice_gara"] = $_POST["codice"];
				$partecipante["codice_lotto"] = (!empty($codice_lotto)) ? $codice_lotto : 0;
				$partecipante["partita_iva"] = $tmp["partita_iva"];
				$partecipante["ragione_sociale"] = $tmp["ragione_sociale"];
				$partecipante["pec"] = $tmp["pec"];
				if(! empty($partecipante["pec"])) {
					$bind = [":pec"=>$partecipante["pec"]];
					$strsql = "SELECT b_operatori_economici.*, b_utenti.pec FROM b_operatori_economici JOIN b_utenti on b_utenti.codice = b_operatori_economici.codice_utente WHERE b_utenti.pec = :pec ORDER BY ragione_sociale LIMIT 0,1";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount()>0) {
						$record = $risultato->fetch(PDO::FETCH_ASSOC);
						$partecipante["ragione_sociale"] = $record["ragione_sociale"];
						$partecipante["partita_iva"] = $record["codice_fiscale_impresa"];
						$partecipante["codice_utente"] = $record["codice_utente"];
						$partecipante["codice_operatore"] = $record["codice"];
					}
				}
				$partecipante["identificativoEstero"] = $tmp["identificativoEstero"];
				$partecipante["ammesso"] = 'S';
				$partecipante["conferma"] = 1;
				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "r_partecipanti";
				$salva->operazione = "INSERT";
				$salva->oggetto = $partecipante;
				if ($salva->save() && !empty($partecipante["codice_utente"])) {
					$sql = "SELECT * FROM r_inviti_gare WHERE codice_utente = :codice_utente AND codice_gara = :codice_gara";
					$ris_inviti = $pdo->bindAndExec($sql,array(":codice_utente"=>$partecipante["codice_utente"],":codice_gara"=>$_POST["codice"]));
					if ($ris_inviti->rowCount() == 0) {
						$invito = array();
						$invito["codice_gara"] = $_POST["codice"];
						$invito["codice_utente"] = $partecipante["codice_utente"];
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "r_inviti_gare";
						$salva->operazione = "INSERT";
						$salva->oggetto = $invito;
						$salva->save();
					}
				}
			}

			$bind = array();
			$bind[":codice"] = $_POST["codice"];
			$bind[":data_scadenza"] = datetime2mysql($_POST["data_pubblicazione"]);
			$bind[":data_accesso"] = datetime2mysql($_POST["data_pubblicazione"]);

			$sql = "UPDATE b_gare SET stato = '3', data_scadenza = :data_scadenza,	data_pubblicazione = :data_accesso, pubblica='1' WHERE codice = :codice";
			if (class_exists("syncERP")) {
        $syncERP = new syncERP();
        if (method_exists($syncERP,"sendUpdateRequest")) {
          $syncERP->sendUpdateRequest($_POST["codice"]);
        }
      }
			
			$update_stato = $pdo->bindAndExec($sql,$bind);
			$href = "/gare/pannello.php?codice=".$_POST["codice"];
			?>
			alert('Modifica effettuata con successo');
			window.location.href = '<? echo $href ?>';
			<?
		} else {
			?>
			alert('Partecipanti è obbligatorio!');
			<?
		}
	}
?>
