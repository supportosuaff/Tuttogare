<?
include_once("../../../config.php");
include_once($root."/layout/top.php");
$edit = false;
$lock = true;
if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
	if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFaseConcorso($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
		if ($codice_fase!==false) {
			$esito = check_permessi_concorso($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	$codice = $_GET["codice"];
	$bind = array();
	$bind[":codice"]=$codice;
	$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
	$strsql = "SELECT * FROM b_concorsi WHERE codice = :codice ";
	$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
	if ($_SESSION["gerarchia"] > 0) {
		$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
		$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
	}
	$risultato = $pdo->bindAndExec($strsql,$bind);

	if ($risultato->rowCount() > 0) {
		$record = $risultato->fetch(PDO::FETCH_ASSOC);

		$operazione = "UPDATE";

		?><h1>AGGIUDICAZIONE PROVVISORIA</h1><?

		$bind = array();
		$bind[":codice"]=$record["codice"];

		$sql = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice AND attiva = 'S' AND apertura <= now() ORDER BY codice DESC LIMIT 0,1";
		$ris = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount() > 0) {
				$fase = $ris->fetch(PDO::FETCH_ASSOC);
				?>
				<h2><?= $fase["oggetto"] ?></h2>
				<?
				$bind = array();
				$bind[":codice"]=$record["codice"];
				$bind[":codice_fase"] = $fase["codice"];

				$sql = "SELECT * FROM r_partecipanti_concorsi WHERE codice_gara = :codice AND codice_fase = :codice_fase AND (r_partecipanti_concorsi.conferma = TRUE OR r_partecipanti_concorsi.conferma IS NULL) ";
				$ris_r_partecipanti = $pdo->bindAndExec($sql,$bind);

				if ($ris_r_partecipanti->rowCount()>0)
				{
					if (!$lock)
					{
						$bind = array();
						$bind[":codice"]=$record["codice"];

							$bind = array();
							$bind[":codice"]=$record["codice"];
							$bind[":codice_fase"] = $fase["codice"];

							$check_sql  = "SELECT * FROM b_punteggi_criteri_concorsi ";
							$check_sql .= "WHERE codice_fase = :codice_fase ";
							$check_sql .= "AND codice_gara = :codice ";
							$ris_check = $pdo->bindAndExec($check_sql,$bind);

							if ($ris_check->rowCount() > 0)
							{
								?>
								<form name="box" method="post" action="importa.php">
									<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
									<input type="hidden" name="codice_fase" value="<? echo $fase["codice"]; ?>">
									<input type="submit" class="submit_big" style="background-color: #FC0" value="Importa punteggi">
								</form>
								<?
							}

							?>
							<form name="box" method="post" action="save.php" rel="validate">
								<input type="hidden" id="calcola_graduatoria" name="calcola_graduatoria" value="N">
								<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
								<input type="hidden" name="codice_fase" value="<? echo $fase["codice"]; ?>">
								<div class="comandi">
									<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
								</div>
								<?
						}

						?>
						<table width="100%">
							<thead>
								<tr>
									<td width="200">Protocollo</td>
									<td>Identificativo</td>
									<td width="10">Ammesso</td>
									<td widty="10">Punteggio</td>
								</tr>
							</thead>
							<tbody>
								<?
								while ($record_partecipante = $ris_r_partecipanti->fetch(PDO::FETCH_ASSOC))
								{
									include("tr_partecipante.php");
								}
								?>
							</tbody>
						</table>
						<? if (!$lock) { ?>
						<input type="submit" class="submit_big" value="Salva">
						<?
							$bind = array();
							$bind[":codice"]=$record["codice"];
							$bind[":codice_fase"]=$fase["codice"];

							$sql = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice AND attiva = 'N' AND codice > :codice_fase ";
							$ris = $pdo->bindAndExec($sql,$bind);
							if ($ris->rowCount() == 0) { ?>
								<input class="submit_big" type="submit" onclick="$('#calcola_graduatoria').val('S');return true;" value="Salva ed Elabora Graduatoria">
							<? }
						?>
					</form>
					<script>
						$(".ammesso").change(function() {
							id = $(this).parents("tr").attr("id");
							if ($(this).val()=="S") {
								$("#"+id+" .motivazione").val("").attr("rel","N;3;0;A").slideUp('fast');
							} else {
								$("#"+id+" .motivazione").attr("rel","S;3;0;A").slideDown('fast');
							}
						});
					</script>
					<? } else { ?>
					<script>
						$(":input").not('.espandi').prop("disabled", true);
					</script>
					<?
				}
				?>
				<form action="download_csv.php" target="_blank" method="post" enctype="multipart/form-data">
					<input type="hidden" name="codice" value="<? echo $record["codice"]; ?>">
					<input type="hidden" name="codice_fase" value="<? echo $fase["codice"]; ?>">
					<input type="submit" class="submit_big" style="background-color:#900" name="submit" value="Esporta Partecipanti">
				</form>
				<?
				} else {
					echo "<h1>Partecipanti non presenti</h1>";
				}
			} else {
				echo "<h1>Attenzione</h1>";
				echo "<h3>Impossibile accedere</h3>";
			}
		} else {
			echo "<h1>Concorso non trovato</h1>";
		}
		include($root."/concorsi/ritorna.php");

	} else {
		echo "<h1>Concorso non trovato</h1>";
	}
include_once($root."/layout/bottom.php");
?>
