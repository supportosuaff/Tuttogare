<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFaseRefererConcorso($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
		if ($codice_fase !== false) {
			$esito = check_permessi_concorso($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
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
		jalert("Operazione non consentita. Verificare lo stato del concorso.");
		<?
		die();
	}
	else
	{
		if (!isset($_POST["codice_gara"]) || !isset($_POST["codice_fase"]))
		{
			?>
			jalert("Errore nella richiesta. Si prega di riprovare.");
			<?
			die();
		}
		else
		{

			$codice_gara = $_POST["codice_gara"];
			$codice_fase = $_POST["codice_fase"];

			$bind = array();
			$bind[":codice_gara"] = $codice_gara;
			$bind[":codice_fase"] = $codice_fase;
			$sql_partecipanti = "SELECT * FROM r_partecipanti_concorsi WHERE codice_gara = :codice_gara AND codice_fase = :codice_fase AND ammesso = 'S' AND escluso = 'N' AND (r_partecipanti_concorsi.conferma = TRUE OR r_partecipanti_concorsi.conferma IS NULL) ORDER BY codice";
			$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,$bind);

			if ($ris_partecipanti->rowCount() > 0) {
				while($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
					$bind[":codice_partecipante"] = $partecipante["codice"];
					$sql = "SELECT SUM(punteggio) AS totale FROM b_punteggi_criteri_concorsi WHERE codice_gara = :codice_gara AND codice_fase = :codice_fase AND codice_partecipante = :codice_partecipante GROUP BY codice_partecipante ";
					$ris_punteggi = $pdo->bindAndExec($sql,$bind);
					if ($ris_punteggi->rowCount() === 1) {
						$punteggio = $ris_punteggi->fetch(PDO::FETCH_ASSOC)["totale"];
						if ($punteggio < 0) $punteggio = 0;
						?>
						$("#punteggio_partecipante_<?= $partecipante["codice"] ?>").val('<?= number_format($punteggio,3,".","") ?>');
						<?
					}
				}
			}

		}
	}
?>
