<h2 style="text-align: center;">
	<b>
		Allegato D1 – Appalti generici<br>
		Motivazione della decisione di aggiudicare l&#39;appalto senza precedente pubblicazione di
		un avviso di indizione di gara nella Gazzetta ufficiale dell&#39;Unione europea<br>
	</b>
	<i>
		Direttiva 2014/24/UE<br>
		<small>(selezionare l&#39;opzione pertinente e fornire una spiegazione)</small>
	</i>
</h2>
<table class="bordered">
	<tr>
		<td class="etichetta"><label><b>2. Altre motivazioni per l&#39;aggiudicazione dell&#39;appalto senza previa pubblicazione di un avviso di indizione di gara nella Gazzetta ufficiale dell&#39;Unione europea</b></label></td>
	</tr>
	<tr>
		<td>
			<label>
				<?
				$d_outside_scope = !empty($guue["DIRECTIVE_2014_24_EU"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_OUTSIDE_SCOPE"]) ? TRUE : FALSE;
				?>
				<input type="checkbox" <?= $d_outside_scope ? 'checked="checked"' : null ?> name="guue[DIRECTIVE_2014_23_EU][PT_AWARD_CONTRACT_WITHOUT_CALL][D_OUTSIDE_SCOPE]">
				L&#39;appalto non rientra nel campo di applicazione della direttiva
			</label>
		</td>
	</tr>
	<tr>
		<td class="etichetta">
			<label><b>3. Spiegazione:</b></label>
		</td>
	</tr>
	<tr>
		<td>
			<textarea name="guue[DIRECTIVE_2014_24_EU][PT_AWARD_CONTRACT_WITHOUT_CALL][D_JUSTIFICATION]" rel="S;3;2500;A" title="Spiegazione" class="ckeditor_simple">
				<?= !empty($guue["DIRECTIVE_2014_24_EU"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_JUSTIFICATION"]) ? $guue["DIRECTIVE_2014_23_EU"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_JUSTIFICATION"] : null?>
			</textarea>
		</td>
	</tr>
</table>