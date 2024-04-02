<?
	include_once("../../../config.php");
	$form_comunicazione = true;
	$codice_gara = $_GET["codice"];
	$form_upload["codice_gara"] = $_GET["codice"];
	$form_upload["online"] = 'S;S';
	$form_upload["sezione"] = 'concorsi';
	include_once($root."/layout/top.php");
	unset($form_upload);
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

		$bind = array();
		$bind[":codice"] = $_GET["codice"];
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

		$strsql = "SELECT b_comunicazioni.* FROM b_comunicazioni WHERE b_comunicazioni.codice_ente = :codice_ente AND codice_gara = :codice AND sezione = 'concorsi' ORDER BY b_comunicazioni.timestamp DESC ";
		$elenco_comunicazioni = $pdo->bindAndExec($strsql,$bind);

		$bind = array();
		$bind[":codice"] = $_GET["codice"];
		$strsql = "SELECT * FROM b_utenti
							 JOIN r_partecipanti_utenti_concorsi ON r_partecipanti_utenti_concorsi.codice_utente = b_utenti.codice
							 JOIN r_partecipanti_concorsi ON r_partecipanti_concorsi.codice = r_partecipanti_utenti_concorsi.codice_partecipante
							 WHERE r_partecipanti_concorsi.codice_gara = :codice";
		$risultato_partecipanti = $pdo->bindAndExec($strsql,$bind);

		?>
			<h1>COMUNICAZIONI</h1>
			<div id = "tabs">
				<ul>
					<? if ($elenco_comunicazioni->rowCount() > 0) echo "<li><a href=\"#archivio\">Archivio</a></li>"; ?>
					<? if ($risultato_partecipanti->rowCount() > 0) echo "<li><a href=\"#partecipanti\">Partecipanti</a></li>"; ?>
				</ul>
				<div id="archivio">
					<?
					if ($elenco_comunicazioni->rowCount() > 0) {
						include($root."/comunicazioni/pa/list.php");
					}
					?>
					<div class="clear"></div>
				</div>
				<?
				if ($risultato_partecipanti->rowCount() > 0) {
					?>
					<div id="partecipanti">
						<table width="100%" id="elenco_partecipanti">
							<thead>
								<tr>
									<td>Codice Fiscale</td>
									<td>Ragione Sociale</td>
									<td>PEC</td>
									<td><input id="invia_all" type="image" src="/img/newsletter.png" onClick="$('.invia_comunicazione').trigger('click'); return false;" width="24" title="Invia una comunicazione a tutti"></td>
								</tr>
							</thead>
							<tbody>
								<?
									while($record_partecipante = $risultato_partecipanti->fetch(PDO::FETCH_ASSOC)) {
										?>
										<tr id="<? echo $record_partecipante["codice"] ?>">
											<td width="10"><strong><? echo $record_partecipante["partita_iva"] ?></strong></td>
											<td><? if (!empty($record_partecipante["tipo"])) echo "<strong>RAGGRUPPAMENTO</strong> - " ?><? echo $record_partecipante["ragione_sociale"] ?></td>
											<td><?= $record_partecipante["pec"] ?></td>
											<td width="10"><? if ($record_partecipante["codice_utente"] != 0) { ?><input id="invia_<? echo $record_partecipante["codice_utente"] ?>" class="invia_comunicazione" type="image" src="/img/newsletter.png" onClick='aggiungi_destinario("<? echo $record_partecipante["codice_utente"] ?>","<? echo str_replace('"','',htmlentities(strtoupper($record_partecipante["ragione_sociale"]),ENT_QUOTES)) ?>");$("#comunicazione").slideDown("slow");return false;' width="24" title="Invia una comunicazione"><? } ?></td>
										</tr>
										<?
									}
								?>
							</tbody>
						</table>
						<div class="clear"></div>
					</div>
					<?
				}
				?>
			</div>
			<script>
				$("#tabs").tabs();
				var elenco = $("#elenco_partecipanti").dataTable({
					"lengthChange": false,
					"paging": false,
					"ordering": false,
					"searching": false,
					"info": false,
					"autoWidth": false,
					"pageLength": -1
				});
			</script>
		<?
		include($root."/concorsi/ritorna.php");
	}
	include_once($root."/layout/bottom.php");
?>