<?
if (isset($record_partecipante)) {
	if (!isset($art80)) $art80 = (check_permessi("verifica-art-80", $_SESSION["codice_utente"]) && check_permessi("docxplorer", $_SESSION["codice_utente"])) ? true : false;
	if (!isset($cont)) $cont = 0;
	$cont++;
	$color = "";
	$color_stato = "";
	$posizione = "";
	if ($record_partecipante["anomalia"] == "S") $color = "#FFCC00 !important";
	if (($record_partecipante["ammesso"] == "N") || ($record_partecipante["escluso"] == "S")) $color = "#FF6600 !important";
	if ($record_partecipante["secondo"] == "S") {
		$color_stato = "#33CCFF";
		$posizione = "Secondo";
	}
	if ($record_partecipante["primo"] == "S") {
		$color_stato = "#99FF66";
		$aggiudicato = true;
		$posizione = "Aggiudicatario";
	}
	$showArt80 = true;
	?>
	<tr style="background-color:<? echo $color ?>" id="<? echo $record_partecipante["codice"] ?>">
		<td width="5"><strong><?= $cont ?></strong></td>
		<?
		$check80 = checkStatoArt80($record_partecipante["partita_iva"]);
		if ($check80 != false) {
			echo '<td width="10"><div class="status_indicator" style="background-color: ' .$check80["color"]  .'"></div></td>';	
		} else {
			echo '<td width="10"></td>';
		}
	?>
		<td width="200">
			<?
			if ($record_partecipante["numero_protocollo"] != "") { ?>
			<strong><? echo $record_partecipante["numero_protocollo"] ?></strong> del <? echo mysql2date($record_partecipante["data_protocollo"]);
		} else { ?>

		<? echo $record_partecipante["codice"] ?> del <? echo mysql2date($record_partecipante["timestamp"]); ?>
		<br>Assegnato dal sistema
		<?
	} ?>

	</td>
	<td width="10">
		<input type="hidden" name="partecipante[<? echo $record_partecipante["codice"] ?>][codice]" id="codice_partecipante_<? echo $record_partecipante["codice"] ?>" value="<? echo $record_partecipante["codice"] ?>">
		<strong><? echo $record_partecipante["partita_iva"] ?></strong>
	</td>
		<td><? if ($record_partecipante["tipo"] != "") echo "<strong>RAGGRUPPAMENTO</strong> - " ?><? echo $record_partecipante["ragione_sociale"] ?>
			<strong style="background-color:<? echo $color_stato ?>; padding:5px;"><? echo $posizione ?></strong>
			<? if (!empty($art80) && !empty($record_partecipante["codice_operatore"]) && $showArt80) { ?><div style="text-align:right"><a href="#" onClick="sendArt80Request('<?= $record_partecipante["codice_operatore"] ?>')" title="Richiedi verifica art.80">Verifica Articolo 80</a></div><? } ?>
			<?
			$rel = "N;3;0;A";
			$style = "display:none";
			if ($record_partecipante["ammesso"] == "N") {
				$rel = "S;3;0;A";
				$style = "";
			}
			?>
			<textarea style="width:98%; <? echo $style ?>" rows="5" name="partecipante[<? echo $record_partecipante["codice"] ?>][motivazione]" rel="<? echo $rel ?>" id="motivazione_partecipante_<? echo $record_partecipante["codice"] ?>" class="motivazione" title="Motivazione esclusione"><? echo $record_partecipante["motivazione"] ?></textarea>
			<?
			$rel = "N;3;0;A";
			$style = "display:none";
			if ($record_partecipante["anomalia"] == "S") {
				$rel = "S;3;0;A";
				$style = "";
			}
			?>
			<textarea style="width:98%; <? echo $style ?>" rows="5" name="partecipante[<? echo $record_partecipante["codice"] ?>][motivazione_anomalia]" rel="<? echo $rel ?>" id="motivazione_anomalia_partecipante_<? echo $record_partecipante["codice"] ?>" class="motivazione_anomalia" title="Motivazione anomalia"><? echo $record_partecipante["motivazione_anomalia"] ?></textarea>
		</td>
		<td width="10"><select onChange="$('#msg_conferma_invio_esclusione').slideDown();" class="ammesso" name="partecipante[<? echo $record_partecipante["codice"] ?>][ammesso]" id="ammesso_partecipante_<? echo $record_partecipante["codice"] ?>">
			<option value="S">Si</option>
			<option value="N">No</option>
		</select></td>
		<td width="10">
			<input type="hidden" name="partecipante[<? echo $record_partecipante["codice"] ?>][anomalia_facoltativa]" class="facoltativa" id="anomalia_facoltativa_partecipante_<? echo $record_partecipante["codice"] ?>" value="<? echo $record_partecipante["anomalia_facoltativa"] ?>">
			<select class="anomalia" name="partecipante[<? echo $record_partecipante["codice"] ?>][anomalia]" id="anomalia_partecipante_<? echo $record_partecipante["codice"] ?>">
				<option value="S">Si</option>
				<option value="N">No</option>
			</select></td>
			<td width="7">
				<select class="controllo_possesso_requisiti" name="partecipante[<? echo $record_partecipante["codice"] ?>][controllo_possesso_requisiti]" id="controllo_possesso_requisiti_partecipante_<? echo $record_partecipante["codice"] ?>">
					<option value="S">Si</option>
					<option value="N">No</option>
				</select>
			</td>
			<?
			if (count($ris_punteggi)>0)
			{
				foreach($ris_punteggi AS $punteggio) {
					$punti = 0;
					$rel_punteggio = "S;0;0;N;0;>=";
					if (is_numeric($record_partecipante["codice"])) {

						$bind = array();
						$bind[":codice_partecipante"] = $record_partecipante["codice"];
						$bind[":codice_gara"] = $record["codice"];
						$bind[":codice_lotto"] = $record_partecipante["codice_lotto"];
						$bind[":codice_punteggio"] = $punteggio["codice"];
						$sql_punteggi  = "SELECT * FROM r_punteggi_gare WHERE codice_partecipante = :codice_partecipante";
						$sql_punteggi .= " AND codice_gara = :codice_gara ";
						$sql_punteggi .= " AND codice_lotto = :codice_lotto ";
						$sql_punteggi .= " AND codice_punteggio = :codice_punteggio ";
						$ris_punteggio = $pdo->bindAndExec($sql_punteggi,$bind);
						if ($ris_punteggio->rowCount()>0) {
							$arr_punti = $ris_punteggio->fetch(PDO::FETCH_ASSOC);
							$punti = $arr_punti["punteggio"];
						}
					}

					$bind = array();
					$bind[":codice"] = $record["codice"];
					$bind[":codice_lotto"] = $record_partecipante["codice_lotto"];
					$bind[":codice_punteggio"] = $punteggio["codice"];

					$sql_punteggi = "SELECT SUM(punteggio) AS massimo FROM b_valutazione_tecnica WHERE codice_gara = :codice AND codice_padre = 0 AND (codice_lotto = :codice_lotto OR codice_lotto = 0) ";
					$sql_punteggi .= " AND punteggio_riferimento = :codice_punteggio GROUP BY punteggio_riferimento";
					$ris_punteggio = $pdo->bindAndExec($sql_punteggi,$bind);
					if ($ris_punteggio->rowCount()>0) {
						$arr_punti = $ris_punteggio->fetch(PDO::FETCH_ASSOC);
						$rel_punteggio = "S;0;0;N;" . $arr_punti["massimo"] . ";<=";
					} else {
						$bind = array();
						$bind[":codice"] = $record["codice"];
						$sql_punteggi = "SELECT * FROM b_valutazione_tecnica WHERE codice_gara = :codice";
						$ris_punteggio = $pdo->bindAndExec($sql_punteggi,$bind);
						if ($ris_punteggio->rowCount()>0) $rel_punteggio = "S;0;0;N;0;=";
					}

					?>
					<td width="10">
						<input type="text" size="5" name="punteggio[<? echo $record_partecipante["codice"] ?>][<? echo $punteggio["codice"] ?>][punteggio]"  title="<? echo $punteggio["nome"] ?>" id="punteggio_<? echo $record_partecipante["codice"] ?>_<? echo $punteggio["codice"] ?>" value="<? echo floatval($punti) ?>" rel="<? echo $rel_punteggio ?>">
						<input type="hidden" name="punteggio[<? echo $record_partecipante["codice"] ?>][<? echo $punteggio["codice"] ?>][codice_punteggio]" value="<? echo $punteggio["codice"] ?>">
					</td>
					<?
				}
			}
			?>
		</tr>
		<script>
			$("#ammesso_partecipante_<? echo $record_partecipante["codice"] ?>").val("<? echo $record_partecipante["ammesso"] ?>");
			$("#anomalia_partecipante_<? echo $record_partecipante["codice"] ?>").val("<? echo $record_partecipante["anomalia"] ?>");
			$("#controllo_possesso_requisiti_partecipante_<? echo $record_partecipante["codice"] ?>").val("<? echo $record_partecipante["controllo_possesso_requisiti"] ?>");
		</script>
<? } ?>
