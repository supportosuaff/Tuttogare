<tr>
	<td class="etichetta" colspan="4">
		<label><b>VII.1.1) Motivo della modifica</b></label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<select name="guue[CHANGES][radio_as_select_for_reason_for_change]"  title="Accesso ai documenti di gara" rel="<?= isRequired("radio_as_select_for_reason_for_change") ?>;1;0;A">
			<option value="">Seleziona..</option>
			<option <?= !empty($guue["CHANGES"]["radio_as_select_for_reason_for_change"]) && $guue["CHANGES"]["radio_as_select_for_reason_for_change"] == "MODIFICATION_ORIGINAL_PUBBLICATION_NO" ? 'selected="selected"' : null ?> value="MODIFICATION_ORIGINAL_PUBBLICATION_NO">Modifica delle informazioni originali fornite dall'amministrazione aggiudicatrice</option>
			<option <?= !empty($guue["CHANGES"]["radio_as_select_for_reason_for_change"]) && $guue["CHANGES"]["radio_as_select_for_reason_for_change"] == "PUBLICATION_TED_PUBBLICATION_NO" ? 'selected="selected"' : null ?> value="PUBLICATION_TED_PUBBLICATION_NO">Pubblicazione su TED non conforme alle informazioni originariamente fornite dall&#39;amministrazione aggiudicatrice</option>
		</select>
	</td>
</tr>
<tr>
	<td colspan="4" class="etichetta">
		<label>VII.1.2) Testo da correggere nell&#39;avviso originale 1 (indicare la sezione pertinente e il numero del paragrafo nell&#39;avviso originale)</label>
	</td>
</tr>
<tr>
	<td colspan="4" id="changes_container"><?
		$change_item = 1;
		if(!empty($guue["CHANGES"]["CHANGE"])) {
			foreach ($guue["CHANGES"]["CHANGE"] as $changes) {
				include $root . "/guue/forms/".(!empty($_SESSION["guue"]["v_form"]) ? $_SESSION["guue"]["v_form"] : '2_0_9')."/common/changes.php";
				$change_item += 1;
			}
		}
	?></td>
</tr>
<tr>
	<td colspan="4">
		<script type="text/javascript">
			var changes_num = <?= $change_item; ?>;
		</script>
		<?
		$href = "forms/".(!empty($_SESSION["guue"]["v_form"]) ? $_SESSION["guue"]["v_form"] : '2_0_9')."/common/changes.php";
		?>
		<button type="button" class="aggiungi" onclick="aggiungi('<?= $href ?>','#changes_container', {item: changes_num});changes_num++;return false;" ><img src="/img/add.png" alt="Aggiungi lotto">Aggiungi Informazioni da variare</button>
	</td>
</tr>