<tr>
	<td class="etichetta">
		<label>IV.2.3) Data stimata di spedizione ai candidati prescelti degli inviti a presentare offerte o a partecipare </label>
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
					<input type="text" class="datepick" name="guue[PROCEDURE][DATE_DISPATCH_INVITATIONS]" value="<?= !empty($guue["PROCEDURE"]["DATE_DISPATCH_INVITATIONS"]) ? $guue["PROCEDURE"]["DATE_DISPATCH_INVITATIONS"] : null ?>" style="font-size: 1.3em;" title="Data (gg/mm/aaaa)" rel="<?= isRequired("DATE_DISPATCH_INVITATIONS") ?>;1;0;D">
				</td>
			</tr>
		</table>
	</td>
</tr>
