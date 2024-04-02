<tr>
	<td class="etichetta" colspan="4">
		<label>IV.1.1) Tipo di procedura</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<script type="text/javascript">
			var radio_as_select_for_procedure_type_options = {
				'PT_NEGOTIATED_WITHOUT_PUBLICATION' : [
					'ajax_load',
					['sezioni', 'annex_d2_part1'],
					[],
					'allegato_d'
				],
				'PT_AWARD_CONTRACT_WITHOUT_CALL' : [
					'ajax_load',
					['sezioni', 'annex_d2_part2'],
					[],
					'allegato_d'
				]
			}
		</script>
		<?
		$radio_as_select_for_procedure_type = null;
		if(!empty($guue["PROCEDURE"]["DIRECTIVE_2014_25_EU"]["radio_as_select_for_procedure_type"])) {
			$radio_as_select_for_procedure_type = $guue["PROCEDURE"]["DIRECTIVE_2014_25_EU"]["radio_as_select_for_procedure_type"];
		}
		?>
		<select id="radio_as_select_for_procedure_type" name="guue[PROCEDURE][DIRECTIVE_2014_25_EU][radio_as_select_for_procedure_type]" rel="S;1;0;A" onchange="add_extra_info($(this).val(), radio_as_select_for_procedure_type_options)">
			<option value="">Seleziona..</option>
			<option <?= $radio_as_select_for_procedure_type == "PT_NEGOTIATED_WITHOUT_PUBLICATION" ? 'selected="selected"' : null ?> value="PT_NEGOTIATED_WITHOUT_PUBLICATION">Aggiudicazione di una concessione senza previa pubblicazione di un bando di concessione (in conformit&agrave; dell&#39;articolo 31, paragrafi 4 e 5, della direttiva 2014/23/UE)</option>
			<option <?= $radio_as_select_for_procedure_type == "PT_AWARD_CONTRACT_WITHOUT_CALL" ? 'selected="selected"' : null ?> value="PT_AWARD_CONTRACT_WITHOUT_CALL">Aggiudicazione di un appalto senza previa pubblicazione di un avviso di indizione di gara nella Gazzetta ufficiale dell&#39;Unione europea nei casi elencati di seguito (completare la parte 2 dell&#39;allegato D)</option>
		</select>
	</td>
</tr>