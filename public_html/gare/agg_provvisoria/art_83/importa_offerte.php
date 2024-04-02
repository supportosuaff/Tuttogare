<?
	session_start();
	include_once("../../../../config.php");
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

		$coefficienteX = 0;
		//Verifico opzioni Coefficiente X
		$sql_coef = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 267";
		$ris_coef = $pdo->bindAndExec($sql_coef,$bind);
		if($ris_coef->rowCount() > 0) $coefficienteX = 0.80;
		unset($ris_coef);
		$sql_coef = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 268";
		$ris_coef = $pdo->bindAndExec($sql_coef,$bind);
		if($ris_coef->rowCount() > 0) $coefficienteX = 0.85;
		unset($ris_coef);
		$sql_coef = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 269";
		$ris_coef = $pdo->bindAndExec($sql_coef,$bind);
		if($ris_coef->rowCount() > 0) $coefficienteX = 0.90;
		unset($ris_coef);

		$bind[":codice_lotto"] = $_POST["codice_lotto"];


		$strsql = "SELECT r_partecipanti.codice, b_gare.nuovaOfferta
							 FROM r_partecipanti JOIN b_gare ON r_partecipanti.codice_gara = b_gare.codice
							 WHERE r_partecipanti.codice_gara = :codice_gara
							 AND r_partecipanti.codice_lotto = :codice_lotto
							 AND r_partecipanti.codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND r_partecipanti.ammesso = 'S'";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		$risultato = $risultato->fetchAll(PDO::FETCH_ASSOC);
		$numero_partecipanti = count($risultato);
		if ($numero_partecipanti>0) {
			if ($risultato[0]["nuovaOfferta"] == "N") {
				include('old-importa.php');
			} else {
				include('new-importa.php');
			}
		} else {
			?>
			 alert("Verificare che vi siano partecipanti ammessi alla gara.");
			<?
		}
	}
?>
