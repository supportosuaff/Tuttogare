<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
			if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
			if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]))
			{
				$codice_fase = getFase($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
				if ($codice_fase!==false) {
					$esito = check_permessi_gara($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
					$edit = $esito["permesso"];
					$lock = $esito["lock"];
				}
				if (!$edit)
				{
					echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
					die();
				}
			}
			else
			{
				echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
				die();
			}
			$codice = $_GET["codice"];
			$bind = array();
			$bind[":codice"]=$codice;
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
			$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
			if ($_SESSION["gerarchia"] > 0) {
				$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
				$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
			}
			$risultato = $pdo->bindAndExec($strsql,$bind);

			if ($risultato->rowCount() > 0) {
				$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
				?><h1>CONFIGURA OFFERTA</h1><?
				$st_index = json_decode(file_get_contents($root."/inc/status_standard_color.json"),TRUE);
				$formule = json_decode(file_get_contents($root."/gare/configura_offerta/formule.json"),TRUE);
				$bind=array();
				$bind[":codice_gara"] = $record_gara["codice"];
				$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara ORDER BY codice";
				$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
				$print_form = false;
				if ($record_gara["norma"] == "2023-36") { 
					?>
					<div class="box">
						<strong>Emendamento delle offerte tecniche/economiche</strong><br>
						<small>L'art. 101 c.4 del D.lgs 36/2023 prevede che, fino al giorno fissato per la loro apertura, l'operatore economico, con le stesse modalità di presentazione della domanda di partecipazione, può richiedere la rettifica di un errore materiale contenuto nell'offerta tecnica o nell'offerta economica di cui si sia avveduto dopo la scadenza del termine per la loro presentazione a condizione che la rettifica non comporti la presentazione di una nuova offerta, o comunque la sua modifica sostanziale, e che resti comunque assicurato l'anonimato.</small><br>
						<br>
						<form action="/gare/configura_offerta/emendamenti.php" rel="validate" id="form-emendamenti">
							<input type="hidden" name="codice_gara" value="<? echo $record_gara["codice"] ?>">
							Vuoi consentire l'emendamento delle offerte?
							<select name="emendamenti" id="emendamenti" rel="S;0;0;A" title="Consentire emendamenti" onchange="$('#form-emendamenti').submit();">
								<option value="S">Si</option>
								<option value="N">No</option>
							</select>
						</form>
					</div>
					<script>
						$("#emendamenti").val("<?= $record_gara["emendamenti"] ?>");
					</script>
					<?
				}
				if (!$lock && !isset($_GET["lotto"])) {
					?>
						<div class="box">
							<table width="100%">
								<tbody>
									<tr>
										<td style="text-align: center;vertical-align: middle;"><a href="#" onClick="$('#massive').slideToggle()">Caricamento massivo</strong></td>
									</tr>
								</tbody>
							</table>
							<form id="massive" action="massive.php" method="post" enctype="multipart/form-data" style="display:none">
								<input type="hidden" name="codice_gara" value="<? echo $record_gara["codice"]; ?>">
								<table class="dettaglio" width="100%">
									<tbody>
									<tr>
										<td width="25%">
											<img target="_blank" src="../../img/xls.png" alt="Modello"/><a href="tracciato.csv" download style="vertical-align:super">Modello CSV</a>
										</td>
										<td width="50%">
												<input type="file" name="tracciato" id="file">
										</td>
										<td width="5%">
												<input type="submit" name="submit" value="Upload">
										</td>
									</tr>
									</tbody>
								</table>
								<h2 style="text-align:center">Guida alla compilazione del CSV</h2>
								Il file da caricare dovrà essere generato includendo ogni campo in doppi apici <strong>(")</strong> ed utilizzando il separatore punto e virgola <strong>(;)</strong>
								<table>
									<tr><td><strong>ID</strong></td><td><strong>Campo obbligatorio</strong></td></tr>
									<tr><td><strong>ID_PADRE</strong></td><td>In caso di sub-criterio indicare il padre</td></tr>
									<tr><td><strong>NUMERO_LOTTO</strong></td><td>Lotto di riferimento, inserire 0 per tutti i lotti</td></tr>
									<tr><td><strong>DESCRIZIONE</strong></td><td><strong>Campo obbligatorio</strong></td></tr>
									<tr><td><strong>TIPO</strong></td><td>
										Q: Qualitativo<br>
										N: Quantitavivo<br>
										<strong>Campo obbligatorio</strong>
									</td></tr>
									<tr><td><strong>FORMULA</strong></td><td>
										<? 
											foreach ($formule AS $key => $formula) {
												echo  $key . ": " . $formula["titolo"] . "<br>";
											}
										?>
										Non compilare per disattivare la valutazione automatica
									</td></tr>
									<tr><td><strong>PUNTI</strong></td><td>Numerico <strong>Campo obbligatorio</strong></td></tr>
									<tr>
										<td><strong>RIFERIMENTO</strong></td>
										<td>
											<?
												$bind = array();
												$bind[":codice_gara"] = $_GET["codice"];
												$sql_punteggi_riferimento = "SELECT b_criteri_punteggi.* FROM b_criteri_punteggi JOIN b_gare ON b_criteri_punteggi.codice_criterio = b_gare.criterio
																										 WHERE b_gare.codice = :codice_gara ORDER BY ordinamento";
												$ris_punteggi_riferimento = $pdo->bindAndExec($sql_punteggi_riferimento,$bind);
												if ($ris_punteggi_riferimento->rowCount() > 0) {
													while ($punteggi=$ris_punteggi_riferimento->fetch(PDO::FETCH_ASSOC)) {
														echo  $punteggi["codice"] . ": " . $punteggi["nome"] . "<br>";
													}
												}
											?>	
											<strong>Campo obbligatorio</strong>
										</td>
									</tr>
									<tr><td><strong>COEF</strong></td><td>Coefficiente da utilizzare in caso di formule non lineari</td></tr>
									<tr><td><strong>DECIMAL</strong></td><td>Numerico intero <strong>Campo obbligatorio</strong></td></tr>
								</table>
						</div>
						<br/>
					<?
					}
				if ($ris_lotti->rowCount()>0)
				{
					if (isset($_GET["lotto"]))
					{
						$codice_lotto = $_GET["lotto"];
						$bind=array();
						$bind[":codice_lotto"] = $codice_lotto;

						$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice_lotto ORDER BY codice";
						$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
						if ($ris_lotti->rowCount()>0)
						{
							$print_form = true;
							$lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC);
							echo "<h2>" . $lotto["oggetto"] . "</h2>";
						} else if ($_GET["lotto"] == 0) {
							$print_form = true;
							echo "<h2>Tutti i lotti</h2>";
						}
					}
					else
					{
						?>
            <a class="submit_big" style="background-color:<?= $st_index[check_configurazione_offerta($record_gara["codice"])["status"]] ?>" href ="index.php?codice=<?= $record_gara["codice"] ?>&lotto=0">
              Tutti i lotti
            </a>
            <?
						while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC))
						{
              ?>
								<a class="submit_big" style="background-color:<?= $st_index[check_configurazione_offerta($record_gara["codice"],$lotto["codice"])["status"]] ?>" href ="index.php?codice=<?= $record_gara["codice"] ?>&lotto=<?= $lotto["codice"] ?>">
									<?= $lotto["oggetto"] ?><br>
									Totale: <?= check_configurazione_offerta($record_gara["codice"],$lotto["codice"])["totale"] ?>
								</a>
							<?
						}
					}
				}
				else
				{
					$print_form = true;
					$codice_lotto = 0;
				}
				if ($print_form)
				{

					if (!$lock && check_configurazione_offerta($record_gara["codice"],$codice_lotto)["status"] != "ok") { ?>
						<a href="edit.php?codice=0&codice_gara=<?= $record_gara["codice"] ?>&codice_lotto=<?= $codice_lotto ?>">
							<div class="add_new">
								<span class="fa fa-plus-circle fa-4x"></span><br>
								Aggiungi criterio
							</div>
						</a>
					<? }
					$sql = "SELECT b_valutazione_tecnica.*,
											 b_criteri_punteggi.nome, b_criteri_punteggi.economica, b_criteri_punteggi.temporale
									FROM b_valutazione_tecnica
									JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
									WHERE b_valutazione_tecnica.codice_gara = :codice_gara
									AND (b_valutazione_tecnica.codice_lotto = :codice_lotto OR b_valutazione_tecnica.codice_lotto = 0)
									AND codice_padre = 0 ORDER BY codice_lotto, codice";
					$risultato = $pdo->bindAndExec($sql,array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto));
					$preview_economica = false;
					$preview_tecnica = false;
					if ($risultato->rowCount() > 0) {
						?>
						<table width="100%">
							<thead>
								<tr>
									<th width="1">Tipologia</th>
									<th width="1">Riferimento</th>
									<th>Descrizione</th>
									<th width="100">Peso</th>
									<th width="1">Valutazione Automatica</th>
									<th width="1"></th>
									<? if (!$lock) { ?>
										<th width="1">Sub</th>
										<th width="1"></th>
									<? } ?>
								</tr>
							</thead>
							<tbody>
								<?
									$peso_totale = 0;
									$sql = "SELECT b_valutazione_tecnica.*, b_criteri_punteggi.nome, b_criteri_punteggi.economica, b_criteri_punteggi.temporale FROM b_valutazione_tecnica
													JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
													WHERE b_valutazione_tecnica.codice_padre = :codice_criterio ";
									$ris_sub = $pdo->prepare($sql);
									while($criterio = $risultato->fetch(PDO::FETCH_ASSOC)) {
										include("tr_criterio.php");
									}
								?>
							</tbody>
							<tfoot>
								<tr style="font-weight:bold; <?= ($peso_totale != 100) ? 'color:#C00' : '' ?>">
									<td colspan="3" style="text-align:right">
										Totale
									</td>
									<td style="text-align:center"><?= $peso_totale ?> / 100</td>
									<td colspan="4"></td>
								</tr>
							</tfoot>
						</table>
						<?
							$link_preview = "preview.php?codice_gara={$record_gara["codice"]}&codice_lotto={$codice_lotto}";
							if ($preview_tecnica) {
								?>
								<a target="_blank" class="submit_big" style="background-color:#C00" href="<?= $link_preview ?>&economica=N">
									<span class="fa fa-file"></span> Anteprima offerta tecnica
								</a>
								<?
							}
							if ($preview_economica) {
								?>
								<a target="_blank" class="submit_big" style="background-color:#C00" href="<?= $link_preview ?>&economica=S">
									<span class="fa fa-file"></span> Anteprima offerta economica
								</a>
								<?
							}
					} else {
						?>
						<h3 style="text-align:center"><span class="fa fa-exclamation fa-2x"></span><br>Nessun risultato</h3>
						<?
					}

				}
				?>
				<? if (isset($_GET["lotto"])) { ?>
					<a class="submit_big" style="background-color:#999" href="/gare/configura_offerta/index.php?codice=<?= $record_gara["codice"] ?>">Ritorna a scelta lotto</a>
				<?
				}
				include($root."/gare/ritorna.php");
			}
			else
			{
				echo "<h1>Gara non trovata</h1>";
			}
		}
		else
		{
			echo "<h1>Gara non trovata</h1>";
		}
	include_once($root."/layout/bottom.php");
?>
