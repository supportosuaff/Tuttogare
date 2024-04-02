<tr>
	<td class="etichetta">
		<label>IV.3.4) Decisione della commissione giudicatrice:</label>
	</td>
</tr>
<tr>
	<td class="etichetta">
		<label>La decisione della commissione giudicatrice &egrave; vincolante per l&#39;amministrazione aggiudicatrice/ente aggiudicatore?</label>
	</td>
</tr>
<tr>
	<td>
		<?
		$radio_as_select_for_decision_binding_contracting = !empty($guue['PROCEDURE']['radio_as_select_for_decision_binding_contracting']) ? $guue['PROCEDURE']['radio_as_select_for_decision_binding_contracting'] : null;
		?>
		<select name="guue[PROCEDURE][radio_as_select_for_decision_binding_contracting]" title="Informazioni relative ai premi" rel="S;1;0;A">
			<option value="">Seleziona..</option>
			<option <?= $radio_as_select_for_decision_binding_contracting == "DECISION_BINDING_CONTRACTING" ? 'selected="selected"' : null ?> value="DECISION_BINDING_CONTRACTING">Si</option>
			<option <?= $radio_as_select_for_decision_binding_contracting == "NO_DECISION_BINDING_CONTRACTING" ? 'selected="selected"' : null ?> value="NO_DECISION_BINDING_CONTRACTING">No</option>
		</select>
	</td>
</tr>