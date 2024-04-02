<tr>
	<td class="etichetta">
		<label>IV.2.7) Modalit&agrave; di apertura delle offerte</label>
	</td>
</tr>
<tr>
	<td>
		<table width="100%">
			<tr>
				<td class="etichetta">
					Data:
				</td>
				<td>
					<input type="text" class="datepick" name="guue[PROCEDURE][OPENING_CONDITION][DATE_OPENING_TENDERS]" value="<?= !empty($guue["PROCEDURE"]["OPENING_CONDITION"]["DATE_OPENING_TENDERS"]) ? $guue["PROCEDURE"]["OPENING_CONDITION"]["DATE_OPENING_TENDERS"] : null ?>" style="font-size: 1.3em;" title="Data (gg/mm/aaaa)" rel="<?= isRequired("DATE_OPENING_TENDERS") ?>;1;0;D">
				</td>
			</tr>
			<tr>
				<td class="etichetta">
					Ora: 
				</td>
				<td>
					<input type="text" class="timepick"  name="guue[PROCEDURE][OPENING_CONDITION][TIME_OPENING_TENDERS]" value="<?= !empty($guue["PROCEDURE"]["OPENING_CONDITION"]["TIME_OPENING_TENDERS"]) ? $guue["PROCEDURE"]["OPENING_CONDITION"]["TIME_OPENING_TENDERS"] : null ?>" style="font-size: 1.3em;" title="Ora (hh:mm)" rel="<?= isRequired("TIME_OPENING_TENDERS") ?>;1;0;T">
				</td>
			</tr>
			<tr>
				<td class="etichetta">
					Luogo: 
				</td>
				<td>
					<input type="text" name="guue[PROCEDURE][OPENING_CONDITION][PLACE]" value="<?= !empty($guue["PROCEDURE"]["OPENING_CONDITION"]["PLACE"]) ? $guue["PROCEDURE"]["OPENING_CONDITION"]["PLACE"] : null ?>" style="font-size: 1.3em;" title="Luogo" rel="<?= isRequired("PLACE") ?>;1;400;A">
				</td>
			</tr>
			<tr>
				<td class="etichetta" colspan="4">
					Informazioni relative alle persone ammesse e alla procedura di apertura:
				</td>
			</tr>
			<tr>
				<td class="etichetta" colspan="4">
					<textarea class="ckeditor_simple" title="Informazioni relative alle persone ammesse e alla procedura di apertura" name="guue[PROCEDURE][OPENING_CONDITION][INFO_ADD]" rel="<?= isRequired("PROCEDURE-INFO_ADD") ?>;0;400;A"><?= !empty($guue["PROCEDURE"]["OPENING_CONDITION"]["INFO_ADD"]) ? $guue["PROCEDURE"]["OPENING_CONDITION"]["INFO_ADD"] : null ?></textarea>
				</td>
			</tr>
		</table>
	</td>
</tr>
