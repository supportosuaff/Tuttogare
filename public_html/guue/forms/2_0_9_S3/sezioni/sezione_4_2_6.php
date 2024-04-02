<tr>
	<td class="etichetta">
		<label>IV.2.6) Periodo minimo durante il quale l&#39;offerente &egrave; vincolato alla propria offerta</label>
	</td>
</tr>
<tr>
	<td>
		<?
			$date_tender_valid = FALSE;
			$duration_tender_valid = FALSE;
			if(!empty($guue["PROCEDURE"]["DATE_TENDER_VALID"])) $date_tender_valid = TRUE;
			if(!empty($guue["PROCEDURE"]["DURATION_TENDER_VALID"])) $duration_tender_valid = TRUE;
		?>
		<table class="bordered valida" title="Durata del contratto" rel="<?= isRequired("date_tender_valid") ?>;0;0;checked;group_validate">
			<tbody>
				<tr>
					<td class="etichetta" style="width: 300px;">
						<label style="font-size: 14px;">
							<input type="radio" name="radio_as_select_for_date_tender_valid" <?= $date_tender_valid ? 'checked="checked"' : null ?> onchange="toggle_field($(this), ['#date_tender_valid']);" value="date_tender_valid"> L&#39;offerta deve essere valida fino al: 
						</label>
					</td>
					<td>
						<input type="text" id="date_tender_valid" <?= $date_tender_valid ? null : 'disabled="disabled"' ?> class="datepick" name="guue[PROCEDURE][DATE_TENDER_VALID]" value="<?= !empty($guue["PROCEDURE"]["DATE_TENDER_VALID"]) ? $guue["PROCEDURE"]["DATE_TENDER_VALID"] : null ?>" style="font-size: 1.3em;" title="Data (gg/mm/aaaa)" rel="<?= isRequired("DATE_TENDER_VALID") ?>;1;0;D">
					</td>
				</tr>
				<tr>
					<td class="etichetta">
						<label style="font-size: 14px;">
							<input type="radio" name="radio_as_select_for_date_tender_valid" <?= $duration_tender_valid ? 'checked="checked"' : null ?> onchange="toggle_field($(this), ['#duration_tender_valid']);" value="duration_tender_valid"> Durata in mesi: 
						</label>
					</td>
					<td>
						<input type="hidden" name="guue[PROCEDURE][DURATION_TENDER_VALID][ATTRIBUTE][TYPE]" value="MONTH">
						<input type="text" id="duration_tender_valid" <?= $duration_tender_valid ? null : 'disabled="disabled"' ?> name="guue[PROCEDURE][DURATION_TENDER_VALID][val]" value="<?= !empty($guue["PROCEDURE"]["DURATION_TENDER_VALID"]["val"]) ? $guue["PROCEDURE"]["DURATION_TENDER_VALID"]["val"] : null ?>" style="font-size: 1.3em;" title="Durata in mesi" rel="<?= isRequired("DURATION_TENDER_VALID") ?>;1;3;N">
					</td>
				</tr>
				<tr>
					<td colspan="2"><i>(dal termine ultimo per il ricevimento delle offerte)</i></td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>
