<tr>
	<td colspan="4" class="etichetta">
		<label>II.2.9) Informazioni relative ai limiti al numero di candidati che saranno invitati a partecipare <i>(ad eccezione delle procedure aperte)</i></label>
	</td>
</tr>
<tr>
	<td>
		<?
		$radio_for_nb_envisaged_candidate = FALSE;
		if(!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["NB_ENVISAGED_CANDIDATE"])) {
			$radio_for_nb_envisaged_candidate = TRUE;
		}
		?>
		<label><input name="item_<?= $item ?>_nb_candidati" <?= $radio_for_nb_envisaged_candidate ? 'checked="checked"' : null ?> type="radio" onchange="toggle_field($(this), ['#item_<?= $item ?>_nb_envisaged_candidate']);">Numero previsto:</label>
	</td>
	<td colspan="3">
		<input type="text" id="item_<?= $item ?>_nb_envisaged_candidate" <?= $radio_for_nb_envisaged_candidate ? null : 'disabled="disabled"' ?> rel="S;1;0;N" title="Numero previsto di candidati" value="<?= !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["NB_ENVISAGED_CANDIDATE"]) ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["NB_ENVISAGED_CANDIDATE"] : null ?>" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][NB_ENVISAGED_CANDIDATE]">
	</td>
</tr>
<tr>
	<td>
		<?
		$radio_for_nb_maxmin_envisaged_candidate = FALSE;
		if(!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["NB_MIN_LIMIT_CANDIDATE"]) || !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["NB_MAX_LIMIT_CANDIDATE"])) {
			$radio_for_nb_maxmin_envisaged_candidate = TRUE;
		}
		?>
		<label><input name="item_<?= $item ?>_nb_candidati" <?= $radio_for_nb_maxmin_envisaged_candidate ? 'checked="checked"' : null ?> type="radio" onchange="toggle_field($(this), ['#item_<?= $item ?>_nb_min_limit_candidate', '#item_<?= $item ?>_nb_max_limit_candidate']);">Max/Min:</label>
	</td>
	<td colspan="2">
		<input type="text" id="item_<?= $item ?>_nb_min_limit_candidate" <?= $radio_for_nb_maxmin_envisaged_candidate ? null : 'disabled="disabled"' ?> rel="S;1;0;N" title="Numero minimo previsto" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][NB_MIN_LIMIT_CANDIDATE]" value="<?= !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["NB_MIN_LIMIT_CANDIDATE"]) ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["NB_MIN_LIMIT_CANDIDATE"] : null ?>">
	</td>
	<td>
		<input type="text" id="item_<?= $item ?>_nb_max_limit_candidate" <?= $radio_for_nb_maxmin_envisaged_candidate ? null : 'disabled="disabled"' ?> rel="S;1;0;N" title="Numero massimo previsto" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][NB_MAX_LIMIT_CANDIDATE]" value="<?= !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["NB_MAX_LIMIT_CANDIDATE"]) ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["NB_MAX_LIMIT_CANDIDATE"] : null ?>">
	</td>
</tr>
<tr>
	<td colspan="4">
		<script type="text/javascript">
			function uncheck_nb_<?= $item ?> () {
				$("input:radio[name='item_<?= $item ?>_nb_candidati']").each(function(i) {
					$(this).removeAttr('checked');
					$(this).removeProp('checked');
					$(this).trigger('change');
				});
			}
		</script>
		<button class="submit_big" style="background-color: #999" type="button" onclick="uncheck_nb_<?= $item ?>()">Nessun Limite</button>
	</td>
</tr>
<tr>
	<td colspan="4" class="etichetta">
		<label style="font-size: 14px;">
			<input type="checkbox" <?= !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["CRITERIA_CANDIDATE"]) ? 'checked="checked"' : null ?> onChange="toggle_field($(this), '#item_<?= $item ?>_criteria_candidate')">Criteri obiettivi per la selezione del numero limitato di candidati <i>(Obbligatorio se &egrave; stato indicato un limite al numero dei candidati)</i>:
		</label>
	</td>
</tr>
<tr>
	<td colspan="4" class="etichetta">
		<textarea id="item_<?= $item ?>_criteria_candidate" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][CRITERIA_CANDIDATE]" rel="S;0;400;A" title="Criteri obiettivi per la selezione del numero limitato di candidati" class="ckeditor_simple" <?= empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["CRITERIA_CANDIDATE"]) ? 'disabled="disabled"' : null ?>>
			<?= (!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["CRITERIA_CANDIDATE"])) ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["CRITERIA_CANDIDATE"] : null ?>
		</textarea>
	</td>
</tr>