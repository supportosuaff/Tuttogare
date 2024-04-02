<? $function_i = $i; ?>
<tr>
	<td class="etichetta" colspan="4">
		<label>II.2.5) Criteri di aggiudicazione </label>
	</td>
</tr>
<tr>
	<td colspan="2" class="etichetta">
		Consentire la pubblicazione?
	</td>
	<td colspan="2">
		<? $criteria_pubblication = !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["AC"]["ATTRIBUTE"]["PUBLICATION"]) ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["AC"]["ATTRIBUTE"]["PUBLICATION"] : ""; ?>
		<select name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][AC][ATTRIBUTE][PUBLICATION]" rel="S;1;0;A" title="Consentire la pubblicazione">
			<option value="">Seleziona..</option>
			<option <?= $criteria_pubblication == "YES" ? 'selected="selected"' : null ?> value="YES">SI</option>
			<option <?= $criteria_pubblication == "NO" ? 'selected="selected"' : null ?> value="NO">NO</option>
		</select>
	</td>
</tr>
<tr>
	<td colspan="4" id="award_criteria_item_to_ignore_<?= $item ?>"><button class="aggiungi" type="button" onClick="aggiungi('forms/2_0_9_S2/common/criteri_di_aggiudicazione_2_2_5_f06.php','#award_criteria_item_to_ignore_<?= $item ?>', {item:'<?= $item ?>'});return false;"><img src="/img/add.png" alt="Aggiungi committente">Aggiungi criterio</button><?
		if(empty($v_form)) $v_form = $_SESSION["guue"]["v_form"];
		include $root . '/guue/forms/'.$v_form.'/common/criteri_di_aggiudicazione_2_2_5_f06.php';
	?></td>
</tr>
<tr>
	<td colspan="4">
		<table class="bordered">
			<tr>
				<td class="etichetta">
					<label>
						<i>I criteri possono essere:
							<ul>
						    <li>Criterio di qualit&agrave; e criterio di costo;</li>
						    <li>Criterio di qualit&agrave; e criterio di prezzo (&Egrave; necessario specificare la ponderazione);</li>
						    <li>Criterio di costo;</li>
						    <li>Criterio di Prezzo (Non si deve indicare la ponderazione);</li>
							</ul>
						</i>
					</label>
				</td>
			</tr>
		</table>
	</td>
</tr>
<? $i = $function_i; ?>
