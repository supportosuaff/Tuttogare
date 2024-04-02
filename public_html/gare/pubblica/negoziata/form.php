<? if(isset($record)) { ?><?
$bind=array();
$bind[":codice"]=$record["codice"];
$sql_estrazioni = "SELECT * FROM b_estrazioni WHERE codice_gara = :codice";
$codice_gara = $record["codice"];
$estrazione = false;
$risultato = $pdo->bindAndExec($sql_estrazioni,$bind);
if ($risultato->rowCount()>0) $estrazione = true;
$bind = array();
$bind[":codice_gara"] = $record["codice"];
$sql = "SELECT b_operatori_economici.codice_fiscale_impresa, b_operatori_economici.ragione_sociale
				FROM r_inviti_gare JOIN b_operatori_economici ON r_inviti_gare.codice_utente = b_operatori_economici.codice_utente
				WHERE r_inviti_gare.codice_gara = :codice_gara";
$ris_invitati = $pdo->bindAndExec($sql,$bind);
$show_invitati = false;
if ($ris_invitati->rowCount() > 0) $show_invitati = true;
$sql = "SELECT * FROM temp_inviti WHERE codice_gara = :codice_gara AND attivo = 'S'";
$ris_manuali = $pdo->bindAndExec($sql,$bind);
if ($ris_manuali->rowCount() > 0) $show_invitati = true;

?>
<ul>
	<li><a href="#pubblica">Pubblicazione</a></li>
  <li><a href="#estrazione">Estrazione</a></li>
	<? if (!$estrazione) { ?>
		<li class='label_invitati'><a href="#inviti">Selezione Diretta</a></li>
		<li class='label_invitati'><a href="#manuale">Manuale</a></li>
	<? } ?>
	<li><a href="#invitati" <?= (!$show_invitati) ? "style='display:none'" : "" ?>>Invitati</a></li>
</ul>

<div id="pubblica">
	<? include($root."/gare/pubblica/common.php"); ?>
	<table width="100%">
		<tr>
			<td colspan="4" class="etichetta"><strong>Invia comunicazione ad operatori non selezionati</strong> <input type="checkbox" name="avvisa_esclusi" id="avvisa_esclusi"><br><small>(Solo in caso di estrazione a seguito di indagine di mercato)</small></td>
		</tr>
	</table>
</div>
<div id="estrazione">
	<? if (!$estrazione) { ?>
		<div id="contenuto_estrazione">
		<script>
			 function estrazione(salva) {
				 if (confirm('Eseguendo l\'estrazione non sarà piu possibile la selezione diretta o ripetere il sorteggio. Continuare?'))
					 {
						 errore = "";
						 if (salva) $("#salva").val('S');
						 $('#estrazione :input').each(function() {
							 if (typeof $(this).attr("rel") != 'undefined' && $(this).attr("id") != "anni") {
								 rel = $(this).attr("rel");
								 rel = rel.split(";");
								 rel[0] = "S";
								 rel = rel.join(";");
								 $(this).attr("rel",rel);
							 }
							 errore += valida($(this));
						 })
						 if (errore==="") {
						 data = $('#estrazione :input').serialize();
						 $.ajax({
							 type: "POST",
							 url: "negoziata/estrai.php",
							 dataType: "html",
							 data: data,
							 async:false,
							 success: function(script) {
								 if (script.indexOf("<h2>Verbale Estrazione")!==-1) {
									 $("#contenuto_estrazione").replaceWith(script);
									 f_ready();
								 } else {
									 $("#contenuto_estrazione").append(script);
								 }
							 }
						 });
					 } else
					 { alert('Impossibile continuare! Errori di validazione'); }
			 }
		 }
	 </script>
		<table width="100%">
		<tr>
			<td class="etichetta">Albo di riferimento</td>
			<td colspan="3"><?
				$bind=array();
				$bind[":codice_ente"]= $_SESSION["ente"]["codice"];
				$bind[":codice_sua"]= $_SESSION["ente"]["sua"];
				$strsql  = "SELECT b_enti.denominazione, b_bandi_albo.*
										FROM b_bandi_albo JOIN r_cpv_bandi_albo ON b_bandi_albo.codice = r_cpv_bandi_albo.codice_bando
										JOIN b_enti ON b_bandi_albo.codice_gestore = b_enti.codice
										WHERE (b_bandi_albo.codice_gestore = :codice_ente OR b_bandi_albo.codice_ente = :codice_sua)";
				if (isset($string_cpv) && $string_cpv != "") {
					$strsql .= " AND (";
					$categorie = explode(";",$string_cpv);
					$cont=0;
					foreach($categorie as $codice) {
						$cont++;
							if ($codice != "") {
								$bind[":cpv_".$cont] = $codice;
								$strsql .= "(r_cpv_bandi_albo.codice = :cpv_" . $cont . " ";
								if (strlen($codice)>2) {
									$diff = strlen($codice) - 2;
									for($i=1;$i<=$diff;$i++) {
										$bind[":cpv_".$cont."_".$i] = substr($codice,0,$i*-1);
										$strsql .= " OR r_cpv_bandi_albo.codice = :cpv_".$cont."_".$i." ";
									}
								}
								$strsql.=") OR ";
							}
						}
					$strsql = substr($strsql,0,-4);
					$strsql .= ")";
				}
				$strsql .= " GROUP BY b_bandi_albo.codice ";
				$strsql .= " ORDER BY b_bandi_albo.oggetto, b_bandi_albo.codice DESC" ;
				$risultato = $pdo->bindAndExec($strsql,$bind);

				if ($risultato->rowCount()>0) {
					?>
					<div style="max-width:800px">
						<select name="estrazione[codice_bando]" title="Albo di riferimento" id="codice_bando" rel="N;1;0;N">
							<option value="">Seleziona...</option>
							<option value="0">Nessuno</option>
							<?
								while($bando = $risultato->fetch(PDO::FETCH_ASSOC)) {
									?>
									<option value="<?= $bando["codice"] ?>">
										<?= (!empty($bando["id"])) ? "ID {$bando["id"]} - " : "" ?> <?= $bando["oggetto"] ?> - Scadenza: <?= mysql2date($bando["data_scadenza"]) ?>
										<?
											if ($bando["codice_gestore"] != $_SESSION["ente"]["codice"]) {
												echo " - Gestore: " . $bando["denominazione"];
											}
										?>
									</option>
									<?
								}
							?>
						</select>
					</div>
					<?
				} else {
					?>Nessuno<?
				}
				?></td></tr>
			<tr>
				<td class="etichetta">Partecipanti da estrarre</td>
				<td><input type="text" name="estrazione[numero_partecipanti]" title="Numero di partecipanti" rel="N;1;0;N" id="numero_partecipanti" size="3">
				<input type="hidden" name="estrazione[salva]" id="salva" value="N" rel="N;0;0;A"></td>
				<td class="etichetta">Esclusioni</td>
					<td>
						<input type="hidden" name="estrazione[codice_gara]" value="<? echo $record["codice"]; ?>">
						<select name="estrazione[esclusioni]" title="Esclusioni" id="esclusioni" rel="N;1;0;A">
							<option value="">Seleziona...</option>
							<option value="N">Nessuno</option>
							<option value="A">Aggiudicatari</option>
							<option value="I">Invitati</option>
					</select>
					</td>
				</tr>
				<tr>
					<td class="etichetta">Filtro CPV</td>
						<td>
							<select name="estrazione[filtro_cpv]" title="Filtro CPV" id="filtro_cpv" rel="N;1;0;A">
								<option value="">Seleziona...</option>
								<option value="S">Attivo</option>
								<option value="N">Disattivo</option>
							</select>
						</td>
						<?
								$bind=array();
								$bind[":codice_gara"] = $record["codice"];
								$sql_soa = "SELECT * FROM b_qualificazione_lavori WHERE codice_gara = :codice_gara AND tipo = 'P'";
								$ris_soa = $pdo->bindAndExec($sql_soa,$bind);
								if ($ris_soa->rowCount()>0) {
									$importo_prevalente = $ris_soa->fetch(PDO::FETCH_ASSOC)["importo_base"];
									 ?>
								<td class="etichetta">Filtro SOA</td>
								<td>
									<select name="estrazione[filtro_soa]" title="Filtro SOA" id="filtro_soa" rel="N;1;0;A" onChange="if ($(this).val()=='F') { $('#anni').attr('rel','S;1;0;N'); $('#anni_row').show() } else { $('#anni_row').hide(); $('#anni').attr('rel','N;1;0;N'); }">
										<option value="">Seleziona...</option>
										<option value="S">Solo Categoria</option>
										<? if ($importo_prevalente >= 150000) { ?>
											<option value="C">Categoria e classifica</option>
										<? } else { ?>
											<option value="F">Categoria e fatturato</option>
										<? } ?>
										<option value="N">Disattivo</option>
									</select>
								</td>
								<? } else {
									$sql_progettazione = "SELECT * FROM b_qualificazione_progettazione WHERE codice_gara = :codice_gara ORDER BY importo DESC LIMIT 0,1";
									$ris_progettazione = $pdo->bindAndExec($sql_progettazione,$bind);
									if ($ris_progettazione->rowCount()>0) {
										 ?>
									<td class="etichetta">Filtro Progettazione</td>
									<td>
										<select name="estrazione[filtro_progettazione]" title="Filtro Progettazione" id="filtro_progettazione"  onChange="if ($(this).val()=='S') { $('#anni').attr('rel','S;1;0;N'); $('#anni_row').show() } else { $('#anni_row').hide(); $('#anni').attr('rel','N;1;0;N'); }" rel="N;1;0;A">
											<option value="">Seleziona...</option>
											<option value="S">Attivo</option>
											<option value="N">Disattivo</option>
										</select>
										<small>
											* Il filtro sar&agrave; applicato solo per la sola categoria con importo maggiore
										</small>
									</td>
								<? }
							} ?>
						</tr>
						<tr id="anni_row" style="display:none">
							<td  class="etichetta" colspan="2"></td>
							<td class="etichetta">Anni da considerare per fatturato</td>
							<td>
								<select name="estrazione[anni]" title="Anni" id="anni" rel="N;1;1;N">
									<option value="">Seleziona...</option>
									<option>3</option>
									<option>5</option>
									<? if (!isset($importo_prevalente)) { ?><option>10</option><? } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="etichetta">Conteggio rotazione</td>
							<td>
								<select title="Conteggio rotazione" name="estrazione[conteggio_rotazione]" id="conteggio_rotazione">
									<option value="">Generale</option>
									<option value="cpv_2">CPV 2 cifre (es. 03)</option>
									<option value="cpv_3">CPV 3 cifre (es. 031)</option>
									<option value="cpv_4">CPV 4 cifre (es. 0311)</option>
									<? if (isset($ris_soa) && $ris_soa->rowCount()>0) { ?>
										<option value="soa">SOA</option>
										<option value="soa_classifica">SOA e classifica</option>
									<? } ?>
									<? if (isset($ris_progettazione) && $ris_progettazione->rowCount()>0) { ?>
										<option value="progettazione">Progettazione</option>
									<? } ?>
								</select>
							</td>
						</tr>
					</table>
					<table style="width:100%">
						<thead>
							<tr>
								<th colspan="2" class="etichetta">Codice Fiscale Impresa</th>
							</tr>
						</thead>
						<tbody id="esclusioniOOOEE"></tbody>
						<tfoot>
							<tr>
								<td colspan="2">
									<button class="btn btn-sm btn-warning btn-block" type="button" onClick="aggiungi('/gare/pubblica/negoziata/escludiInput.html','#esclusioniOOOEE')">Aggiungi operatore da escludere</button>
								</td>
							</tr>
						</tfoot>
					</table>
					<input type="button" class="submit_big" style="background-color:#FC0" value="Esegui estrazione" onclick="estrazione(false);return false;">
					<input type="button" class="submit_big" style="background-color:#0F0" value="Esegui estrazione e salva" onclick="estrazione(true);return false;">
				</div>
		<? } else {
			include("report.php");
		} ?>
</div>
	<? if (!$estrazione) { ?>
	<div id="inviti">
		<? include_once($root."/gare/pubblica/directForm.php"); ?>
	</div>
	<div id="manuale">
		<div class="box">
			Inserendo manualmente i riferimenti, il sistema trasmetterà la richiesta, con un invito di registrazione, agli operatori economici non iscritti
		</div>
		<table width="100%">
			<thead>
				<tr>
					<th width="120">Codice Fiscale Azienda</th>
					<th width="120">identificativo Estero</th>
					<th>Ragione Sociale</th>
					<th>PEC</th>
					<th width="10"></th>
				</tr>
			</thead>
			<tbody id="partecipanti-manuali">
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5">
						<button type="button" class="submit_big" onClick="aggiungi('tr_partecipante.php','#partecipanti-manuali')">Aggiungi partecipante</button>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
<?
	}
?>
	<div id="invitati">
		<table width="100%">
			<thead>
				<tr>
					<td width="150">Codice fiscale Azienda</td>
					<td>Ragione Sociale</td>
					<td width="10"></td>
				</tr>
			</thead>
			<tbody id="table-invitati">
			<?
				if ($ris_invitati->rowCount() > 0) {
					while($partecipante=$ris_invitati->fetch(PDO::FETCH_ASSOC)) {
						?>
						<tr>
							<td><?= $partecipante["codice_fiscale_impresa"] ?></td>
							<td><?= $partecipante["ragione_sociale"] ?></td>
						</tr>
						<?
					}
				}
				?>
			</tbody>
		</table>
		<?
			if ($ris_manuali->rowCount() > 0) {
				?>
				<div class="box">
					<h3>Invitati non iscritti</h3>
					<table width="100%">
						<thead>
							<tr>
								<th width="120">Codice fiscale Azienda</th>
								<th width="120">identificativo Estero</th>
								<th>Ragione Sociale</th>
								<th>PEC</th>
							</tr>
						</thead>
						<tbody>
							<?
								while($partecipante = $ris_manuali->fetch(PDO::FETCH_ASSOC)) {
									?>
									<tr>
										<td><?= $partecipante["partita_iva"] ?></td>
										<td><?= $partecipante["identificativoEstero"] ?></td>
										<td><?= $partecipante["ragione_sociale"] ?></td>
										<td><?= $partecipante["pec"] ?></td>
									</tr>
									<?
								}
							?>
						</tbody>
					</table>
				</div>
				<?
			}
		?>
	</div>
<?
} ?>
