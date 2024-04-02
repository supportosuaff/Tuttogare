<h3><b>VI.2) Informazioni relative ai flussi di lavoro elettronici</b></h3>
<table class="bordered">
	<tbody>
		<tr>
			<td>
				<label><input type="checkbox" name="guue[COMPLEMENTARY_INFO][EORDERING]" <?= !empty($guue["COMPLEMENTARY_INFO"]["EORDERING"]) ? 'checked="checked"' : null ?>> Si far&agrave; ricorso all&#39;ordinazione elettronica</label><br>
				<label><input type="checkbox" name="guue[COMPLEMENTARY_INFO][EINVOICING]" <?= !empty($guue["COMPLEMENTARY_INFO"]["EINVOICING"]) ? 'checked="checked"' : null ?>> Sar&agrave; accettata la fatturazione elettronica</label><br>
				<label><input type="checkbox" name="guue[COMPLEMENTARY_INFO][EPAYMENT]" <?= !empty($guue["COMPLEMENTARY_INFO"]["EPAYMENT"]) ? 'checked="checked"' : null ?>> Sar&agrave; utilizzato il pagamento elettronico</label><br>
			</td>
		</tr>
	</tbody>
</table>