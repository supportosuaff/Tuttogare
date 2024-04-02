<?
	session_start();
	include_once("../../../../config.php");
	include_once($root."/inc/funzioni.php");
	include_once($root."/inc/save.class.php");

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

	if (!$edit && $lock)
	{
		?>
		jalert("Operazione non consentita. Verificare lo stato di Gara.");
		<?
		die();
	}
	else
	{
		if (!isset($_POST["codice_gara"]) || !isset($_POST["codice_lotto"]) || !isset($_POST["criterio"]))
		{
			?>
			jalert("Errore nella richiesta. Si prega di riprovare.");
			<?
			die();
		}
		else
		{
			$strsql = "SELECT r_partecipanti.codice, b_gare.nuovaOfferta
								 FROM r_partecipanti JOIN b_gare ON r_partecipanti.codice_gara = b_gare.codice
								 WHERE r_partecipanti.codice_gara = :codice_gara
								 AND r_partecipanti.codice_lotto = :codice_lotto
								 AND r_partecipanti.codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND r_partecipanti.ammesso = 'S'";
			$risultato = $pdo->bindAndExec($strsql,[":codice_gara"=>$_POST["codice_gara"],":codice_lotto"=>$_POST["codice_lotto"]]);
			$risultato = $risultato->fetchAll(PDO::FETCH_ASSOC);
			$numero_partecipanti = count($risultato);
			if ($numero_partecipanti>0) {
				// if ($risultato[0]["nuovaOfferta"] == "N") {
				// 	include('old-importa-tecnico.php');
				// } else {
					$offerteTecniche = true;
					include('new-importa.php');
				// }
			} else {
				?>
				 jalert("Verificare che vi siano partecipanti ammessi alla gara.");
				<?
			}
		}
	}
?>
