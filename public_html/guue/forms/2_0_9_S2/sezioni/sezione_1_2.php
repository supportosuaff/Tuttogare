<h3><b>I.2) Appalto congiunto</b></h3>
<table class="bordered">
	<tr>
		<td colspan="2">
			<label>
				<input type="checkbox" name="guue[CONTRACTING_BODY][JOINT_PROCUREMENT_INVOLVED]" <?= !empty($guue["CONTRACTING_BODY"]["JOINT_PROCUREMENT_INVOLVED"]) ? 'checked="checked"' : null ?> onchange="toggle_field($(this), '#joint_procurement_involved_procurement_law')">
				Il contratto prevede un appalto congiunto
			</label>
		</td>
	</tr>
	<tr>
		<td class="etichetta">
			Nel caso di appalto congiunto che coinvolge diversi paesi – normative nazionali sugli appalti in vigore:
		</td>
		<td width="50%">
			<input type="text" id="joint_procurement_involved_procurement_law" title="Normative nazionali sugli appalti in vigore" name="guue[CONTRACTING_BODY][PROCUREMENT_LAW]" rel="S;0;200;A" <?= !empty($guue["CONTRACTING_BODY"]["JOINT_PROCUREMENT_INVOLVED"]) ? (!empty($guue["CONTRACTING_BODY"]["PROCUREMENT_LAW"]) ? 'value="'.$guue["CONTRACTING_BODY"]["PROCUREMENT_LAW"].'"' : null) : 'disabled="disabled"' ?>>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<label>
				<input type="checkbox" name="guue[CONTRACTING_BODY][CENTRAL_PURCHASING]" <?= !empty($guue["CONTRACTING_BODY"]["CENTRAL_PURCHASING"]) ? 'checked="checked"' : null ?>>
				L&#39;appalto &egrave; aggiudicato da una centrale di committenza
			</label>
		</td>
	</tr>
</table>