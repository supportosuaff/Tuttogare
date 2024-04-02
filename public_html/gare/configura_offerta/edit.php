<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
		if ($codice_fase !== false) {
			$esito = check_permessi_gara($codice_fase,$_GET["codice_gara"],$_SESSION["codice_utente"]);
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
	if (isset($_GET["codice_lotto"]) && !empty($_GET["codice_gara"])) {

		$codice = $_GET["codice"];
		$bind = array();
		$bind[":codice"] = $codice;
		$bind[":codice_gara"] = $_GET["codice_gara"];
		$bind[":codice_lotto"] = $_GET["codice_lotto"];

		$strsql = "SELECT * FROM b_valutazione_tecnica WHERE codice = :codice
							 AND codice_lotto = :codice_lotto
							 AND codice_gara = :codice_gara ";

		$risultato = $pdo->bindAndExec($strsql,$bind);

		if ($risultato->rowCount() > 0) {
			$record = $risultato->fetch(PDO::FETCH_ASSOC);
			$operazione = "UPDATE";
		} else if ($codice == 0) {
				$record = get_campi("b_valutazione_tecnica");
				$record["codice_gara"] = $_GET["codice_gara"];
				$record["codice_lotto"] = $_GET["codice_lotto"];
				$record["codice_padre"] = !empty($_GET["codice_padre"]) ? $_GET["codice_padre"] : 0;
				$record["decimali"] = 3;
				$strsql = "SELECT * FROM b_valutazione_tecnica WHERE codice = :codice_padre AND codice_padre = 0";
				$ris = $pdo->bindAndExec($strsql,[":codice_padre"=>$record["codice_padre"]]);
				if ($ris->rowCount() > 0) {
					$padre = $ris->fetch(PDO::FETCH_ASSOC);
					$record["tipo"]=$padre["tipo"];
					$record["punteggio_riferimento"]=$padre["punteggio_riferimento"];
					$record["decimali"]=$padre["decimali"];
				}
				$operazione = "INSERT";
		} else {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
		$formule = json_decode(file_get_contents($root."/gare/configura_offerta/formule.json"),TRUE);
		if (!$lock) { ?>
			<form name="box" method="post" action="save.php" rel="validate">
		    <input type="hidden" name="codice" value="<? echo $codice; ?>">
		    <input type="hidden" name="operazione" value="<? echo $operazione ?>">
				<input type="hidden" name="codice_gara" value="<? echo $record["codice_gara"] ?>">
				<input type="hidden" name="codice_padre" value="<?= $record["codice_padre"] ?>">
		    <input type="hidden" name="codice_lotto" value="<? echo $record["codice_lotto"] ?>">
				<? if (!empty($record["codice_padre"])) { ?>
					<input type="hidden" name="tipo" value="<? echo $record["tipo"] ?>">
					<input type="hidden" name="punteggio_riferimento" value="<? echo $record["punteggio_riferimento"] ?>">
				<? } ?>
		<? } ?>
		<script>
			function check_valutazione() {
				$(".valutazione").hide();
				$(".valutazione-"+$("#tipo").val()).show();
				valutazione = $("input[name=valutazione]:checked").val();
				$(".options").hide();
				$("#option-"+valutazione).slideDown();
			}
		</script>
			<h1>Configurazione Criterio</h1>
			<table width="100%">
				<tr>
					<td class="etichetta">Tipo *</td>
					<td>
						<select <?= (!empty($record["codice_padre"])) ? "disabled" : "" ?> rel="S;1;1;A"
							name="tipo" title="Tipo" id="tipo"
							onChange="$('.valutazione').removeAttr('checked').slideUp('fast'); $('#valutazione-').attr('checked','checked'); check_valutazione();">
							<option value="">Seleziona...</option>
							<option value="Q">Qualitativo</option>
							<option value="N">Quantitativo</option>
						</select>
						<script>
								$("#tipo").val('<?= $record["tipo"] ?>');
						</script>
					</td>
					<td class="etichetta">Punteggio riferimento *</td>
					<td>
						<div style="width:200px">
						<?
							$bind = array();
							$bind[":codice_gara"] = $_GET["codice_gara"];
							$sql_punteggi_riferimento = "SELECT b_criteri_punteggi.* FROM b_criteri_punteggi JOIN b_gare ON b_criteri_punteggi.codice_criterio = b_gare.criterio
																					 WHERE b_gare.codice = :codice_gara ORDER BY ordinamento";
							$ris_punteggi_riferimento = $pdo->bindAndExec($sql_punteggi_riferimento,$bind);
							if ($ris_punteggi_riferimento->rowCount() > 0) {
								?>
								<select <?= (!empty($record["codice_padre"])) ? "disabled" : "" ?> rel="S;1;2;N" name="punteggio_riferimento" title="Riferimento" id="punteggio_riferimento">
									<option value="">Seleziona...</option>
									<? while ($punteggi=$ris_punteggi_riferimento->fetch(PDO::FETCH_ASSOC)) { ?>
											<option <?= ($punteggi["codice"] == $record["punteggio_riferimento"]) ? "selected" : "" ?> value="<?= $punteggi["codice"] ?>"><?= $punteggi["nome"] ?></option>
									<? } ?>
								</select>
								<?
							}
						?>
						</div>
					</td>
					<td class="etichetta">Peso *</td>
					<td width="100"><input class="titolo_edit" name="punteggio" id="punteggio" value="<?= $record["punteggio"] ?>" title="Peso" rel="S;1;0;N;100;<="></td>
					<td class="etichetta">Decimali *</td>
					<td>
						<select rel="S;1;1;N" name="decimali" title="Decimali" id="decimali">
							<option>Seleziona...</option>
							<option>0</option>
							<option>1</option>
							<option>2</option>
							<option>3</option>
							<option>4</option>
							<option>5</option>
						</select>
						<script>
								$("#decimali").val('<?= $record["decimali"] ?>');
						</script>
					</td>
				</tr>
				<tr>
					<td colspan="5" style="width:50%" class="etichetta">Descrizione</td>
					<td colspan="3" class="etichetta">Valutazione automatica</td>
				</tr>
				<tr>
					<td colspan="5">
						<textarea id="descrizione" name="descrizione" class="ckeditor_simple" title="descrizione" rel="S;2;0;A">
							<?= $record["descrizione"] ?>
						</textarea>
					</td>
					<td colspan="3">
						<input type="radio" id="valutazione-" onChange="check_valutazione()" name="valutazione" <?= ($record["valutazione"] == "") ? 'checked' : '' ?> value=""> Disattiva<br>
						<? foreach($formule AS $codice_formula => $formula) { ?>
							<div class="box valutazione valutazione-<?= $formula["tipo"] ?>" style="padding:10px;">
								<input type="radio" id="valutazione-<?= $codice_formula ?>" name="valutazione" <?= ($record["valutazione"] == $codice_formula) ? 'checked' : '' ?> onChange='check_valutazione();' value="<?= $codice_formula ?>"> <?= $formula["titolo"] ?>
								<? if (!empty($formula["formula"])) echo " <div style='font-weight:bold; float:right;'>" . $formula["formula"] . "</div><div class=\"clear\"></div>" ?>
								<? if ($codice_formula == "S") { ?>
									<div class="options box" id="option-S" style="display:none">
										<table width="100%">
											<thead>
												<tr>
													<th>Min</th>
													<th>Max</th>
													<th>Punti</th>
													<th></th>
												</tr>
											</thead>
											<tbody id="table-S">
												<?
													if (!empty($record["options"]) && $record["valutazione"] == "S") {
														$record["options"] = json_decode($record["options"],true);
														$id = 0;
														foreach($record["options"]["range"] AS $range) {
															$id++;
															include('tr_tabellare.php');
														}	
													}
												?>
											</tbody>
											<tfoot>
												<tr>
													<td colspan="4">
														<button class="submit_big" onClick='aggiungi("tr_tabellare.php","#table-S"); return false;'>
															Aggiungi range
														</button>
													</td>
												</tr>
											</tfoot>
										</table>
										<input name="option-S[riparametra]" type="checkbox" <?= (isset($record["options"]["riparametra"])) ? 'checked' : "" ?> id="options-S-riparametra"> Riparamentra punteggi
									</div>
								<? } ?>
								<? if ($codice_formula == "B") { ?>
									<div class="options box" id="option-B" style="display:none">
										<?
											$value_option = "";
											if ($record["valutazione"]=="B") {
												$value_option = $record["options"];
											}
										?>
										<strong>Coefficiente<br><small>Consigliati (0.80 - 0.85 - 0.90)</small></strong><br>
										<input name="option-B" type="text" title="Coefficiente" class="titolo_edit" value="<?= $value_option ?>" rel="N;0;0;N;1;<">
									</div>
								<? } ?>
								<? if ($codice_formula == "E") { ?>
									<div class="options box" id="option-E" style="display:none">
										<?
											$value_option = "";
											if ($record["valutazione"]=="E") {
												$value_option = $record["options"];
											}
										?>
										<strong>Coefficiente (Solo per applicazione formula quadratica su totale offerta)<br><small>Consigliati (0.80 - 0.85 - 0.90)</small></strong><br>
										<input name="option-E" type="text" title="Coefficiente" class="titolo_edit" value="<?= $value_option ?>" rel="N;0;0;N;1;<">
									</div>
								<? } ?>
								<? if ($codice_formula == "Q") { ?>
									<div class="options box" id="option-Q" style="display:none">
										<?
											$value_option = "";
											if ($record["valutazione"]=="Q") {
												$value_option = $record["options"];
											}
										?>
										<strong>Coefficiente &alpha;<br><small>Consigliato < 1</small></strong><br>
										<input name="option-Q" type="text" title="Coefficiente" class="titolo_edit" value="<?= $value_option ?>" rel="N;0;0;N;0;>">
									</div>
								<? } ?>
								<? if ($codice_formula == "K") { ?>
									<div class="options box" id="option-K" style="display:none">
										<?
											$value_option = "";
											if ($record["valutazione"]=="K") {
												$value_option = $record["options"];
											}
										?>
										<strong>Coefficiente K<br></strong><br>
										<input name="option-K" type="text" title="Coefficiente" class="titolo_edit" value="<?= $value_option ?>" rel="N;0;0;N;0;>">
									</div>
								<? } ?>
								<? if ($codice_formula == "W") { ?>
									<div class="options box" id="option-W" style="display:none">
										<?
											$value_option = "";
											if ($record["valutazione"]=="W") {
												$value_option = $record["options"];
											}
										?>
										<strong>Coefficiente K<br></strong><br>
										<input name="option-W" type="text" title="Coefficiente" class="titolo_edit" value="<?= $value_option ?>" rel="N;0;0;N;0;>">
									</div>
								<? } ?>
							</div>
						<? } ?>
					</td>
				</tr>
			</table>
			<script>
				check_valutazione();
			</script>
			<? if (!$lock) { ?>
				<input type="submit" class="submit_big" value="Salva">
		  </form>
		<? } else {
			?>
			<script>
				$("#contenuto_top :input").not('.espandi').prop("disabled", true);
			</script>
			<?
		} ?>
		<a href="/gare/configura_offerta/index.php?codice=<?= $_GET["codice_gara"] ?>&lotto=<?= $_GET["codice_lotto"] ?>" class="espandi ritorna_button submit_big" style="background-color:#999;">Ritorna all'elenco</a>
   <?
		} else {
			echo "<h1>Criterio non trovato</h1>";
		}
		$_GET["codice"] = $_GET["codice_gara"];

	include_once($root."/layout/bottom.php");
	?>
