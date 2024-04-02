<?
	include_once("../../../config.php");

	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]) && !empty($_POST["codice_gara"])) {
		$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/configura_offerta/index.php'";
		$risultato = $pdo->query($strsql);
		if ($risultato->rowCount()>0) {
			$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
			$esito = check_permessi_gara($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
		if (!$edit) {
			die();
		}
	} else {
		die();
	}
	if ($edit)
	 {
	
		$salva = new salva();
		$salva->debug = false;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_gare";
		$salva->operazione = "UPDATE";
		$salva->oggetto = ["codice"=>$_POST["codice_gara"],"emendamenti"=>$_POST["emendamenti"]];
		$codice = $salva->save();
		if (empty($codice)) {
			?>
			alert('Errore nel salvataggio. Si prega di riprovare');
			<?
		}
	}



?>
