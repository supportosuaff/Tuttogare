<tr>
	<td class="etichetta">
		<label>IV.2.2) Termine per la ricezione delle manifestazioni di interesse</label>
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
					<input type="text" class="datepick" name="guue[PROCEDURE][DATE_RECEIPT_TENDERS]" value="<?= !empty($guue["PROCEDURE"]["DATE_RECEIPT_TENDERS"]) ? $guue["PROCEDURE"]["DATE_RECEIPT_TENDERS"] : null ?>" style="font-size: 1.3em;" title="Data (gg/mm/aaaa)" rel="<?= isRequired("DATE_RECEIPT_TENDERS") ?>;1;0;D">
				</td>
			</tr>
			<tr>
				<td class="etichetta">
					Ora locale: 
				</td>
				<td>
					<input type="text" class="timepick" style="font-size: 1.3em;" name="guue[PROCEDURE][TIME_RECEIPT_TENDERS]" value="<?= !empty($guue["PROCEDURE"]["TIME_RECEIPT_TENDERS"]) ? $guue["PROCEDURE"]["TIME_RECEIPT_TENDERS"] : null ?>" title="Ora locale (hh:mm)" rel="<?= isRequired("TIME_RECEIPT_TENDERS") ?>;1;0;T">
				</td>
			</tr>
		</table>
	</td>
</tr>
