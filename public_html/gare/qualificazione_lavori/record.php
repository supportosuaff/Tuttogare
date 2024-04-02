<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$qualificazione = get_campi("b_qualificazione_lavori");
		$qualificazione["tipo"] = "S";
		$target = explode("_",$_POST["target"]);
		$qualificazione["codice_lotto"] = $target[2];
		$qualificazione["importo_base"] = 0;
		$id = $_POST["id"];
	}
?>
        	<tr>
            	<td>
								<div style="max-width:600px !important">
									<input type="hidden" rel="S;1;1;A" name="qualificazione[<? echo $id ?>][tipo]" id="tipo_qualificazione_<? echo $id ?>_<?= $qualificazione["codice_lotto"] ?>" value="<?= $qualificazione["tipo"] ?>">
									<input type="hidden" rel="S;0;0;N" name="qualificazione[<? echo $id ?>][codice_lotto]" id="lotto_<? echo $id ?>_<?= $qualificazione["codice_lotto"] ?>" value="<?= $qualificazione["codice_lotto"] ?>">
	                <select rel="S;0;0;N" title="Categoria" name="qualificazione[<? echo $id ?>][codice_categoria]" id="codice_categoria_qualificazione_<? echo $id ?>_<?= $qualificazione["codice_lotto"] ?>">
										<option value="">Seleziona...</option>
	                	<?
											$sql = "SELECT * FROM b_categorie_soa WHERE attivo = 'S' ORDER BY codice";
											$ris_elenco = $pdo->query($sql);
											if ($ris_elenco->rowCount()>0) {
												while($oggetto = $ris_elenco->fetch(PDO::FETCH_ASSOC)) {
													?>
														<option value="<? echo $oggetto["codice"] ?>"><strong><? echo $oggetto["id"] . "</strong> - " . $oggetto["descrizione"] ?></option>
													<?
												}
											}
											?>
										</select>
									</div>
								</td>
								<td width="10">
									<input size="12" class="lotto_<?= $qualificazione["codice_lotto"] ?>" onchange="check_importi();" title="Importo Base" rel="S;1;0;N" name="qualificazione[<? echo $id ?>][importo_base]" id="importo_base_qualificazione_<? echo $id ?>_<?= $qualificazione["codice_lotto"] ?>" value="<? echo $qualificazione["importo_base"] ?>">
								</td>
								<td width="10">
									<? if ($qualificazione["tipo"] == "S") { ?><input type="image" onClick="$(this).parents('tr').first().remove();" src="/img/del.png" title="Elimina"><? }
									else { ?>
										<strong>Prevalente</strong>
										<? } ?>
									</td>
								</tr>
<? if (!isset($_POST["id"])) { ?>
	<script>
		$("#codice_categoria_qualificazione_<? echo $id ?>_<?= $qualificazione["codice_lotto"] ?>").val('<? echo $qualificazione["codice_categoria"] ?>');
	</script>
<? } ?>
