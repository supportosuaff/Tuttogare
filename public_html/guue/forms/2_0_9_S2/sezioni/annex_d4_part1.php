<h2 style="text-align: center;">
	<b>
		Allegato D4 – Concessione<br>
		Motivazione della decisione di aggiudicare la concessione senza previa pubblicazione di un bando di concessione nella Gazzetta ufficiale dell’Unione europea
	</b>
	<i>
		Direttiva 2014/23/UE<br>
		(selezionare l&#39;opzione pertinente e fornire una spiegazione)
	</i>
</h2>
<table class="bordered">
	<tr>
		<td class="etichetta"><label><b>1. Motivazione dell&#39;aggiudicazione della concessione senza precedente pubblicazione di un bando di concessione, conformemente all&#329;articolo 31, paragrafi 4 e 5, della direttiva 2014/23/UE</b></label></td>
	</tr>
	<tr>
		<td>
			<label>
				<?
				$d_no_tenders_request = !empty($guue["DIRECTIVE_2014_23_EU"]["PT_AWARD_CONTRACT_WITHOUT_PUBLICATION"]["D_ACCORDANCE_ARTICLE"]["D_NO_TENDERS_REQUESTS"]) ? TRUE : FALSE;
				?>
				<input type="checkbox" <?= $d_no_tenders_request ? 'checked="checked"' : null ?> name="guue[DIRECTIVE_2014_23_EU][PT_AWARD_CONTRACT_WITHOUT_PUBLICATION][D_ACCORDANCE_ARTICLE][D_NO_TENDERS_REQUESTS]">
				Non &egrave; stata presentata alcuna offerta o alcuna offerta appropriata o non &egrave; stata depositata alcuna candidatura o alcuna candidatura appropriata in risposta a una precedente procedura di concessione.
			</label>
		</td>
	</tr>
	<tr>
		<td>
			<label>
				<?
				$particular_economic_operator = !empty($guue["DIRECTIVE_2014_23_EU"]["PT_AWARD_CONTRACT_WITHOUT_PUBLICATION"]["D_ACCORDANCE_ARTICLE"]["radio_as_select_for_reason"]) ? $guue["DIRECTIVE_2014_23_EU"]["PT_AWARD_CONTRACT_WITHOUT_PUBLICATION"]["D_ACCORDANCE_ARTICLE"]["radio_as_select_for_reason"] : null;
				?>
				<input type="checkbox" <?= !empty($particular_economic_operator) ? 'checked="checked"' : null ?> onchange="toggle_field($(this), '#reason_supplies_from_one')">
				I lavori o i servizi possono essere forniti unicamente da un determinato operatore economico per una delle seguenti ragioni:
			</label>
		</td>
	</tr>
	<tr>
		<td>
			<select id="reason_supplies_from_one" rel="S;1;0;A" title="Ragione per cui i lavori o i servizi possono essere forniti unicamente da un determinato operatore economico" name="guue[DIRECTIVE_2014_23_EU][PT_AWARD_CONTRACT_WITHOUT_PUBLICATION][D_ACCORDANCE_ARTICLE][radio_as_select_for_reason]" <?= empty($particular_economic_operator) ? 'disabled="disabled"' : null ?>>
				<option value="">Seleziona..</option>
				<option <?= $particular_economic_operator ==  "D_ARTISTIC" ? 'selected="selected"' : null ?> value="D_ARTISTIC">l&#39;oggetto della concessione &egrave; la creazione o l&#39;acquisizione di un&#39;opera d&#39;arte o di una rappresentazione artistica unica</option>
				<option <?= $particular_economic_operator ==  "D_TECHNICAL" ? 'selected="selected"' : null ?> value="D_TECHNICAL">la concorrenza &egrave; assente per motivi tecnici</option>
				<option <?= $particular_economic_operator ==  "D_EXCLUSIVE_RIGHT" ? 'selected="selected"' : null ?> value="D_EXCLUSIVE_RIGHT">esistenza di un diritto esclusivo</option>
				<option <?= $particular_economic_operator ==  "D_PROTECT_RIGHTS" ? 'selected="selected"' : null ?> value="D_PROTECT_RIGHTS">tutela dei diritti di propriet&agrave; intellettuale e diritti esclusivi diversi da quelli definiti all&#39;articolo 5, punto 10, della direttiva</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="etichetta">
			<label><b>3. Spiegazione:</b></label>
		</td>
	</tr>
	<tr>
		<td>
			<textarea name="guue[DIRECTIVE_2014_23_EU][PT_AWARD_CONTRACT_WITHOUT_PUBLICATION][D_ACCORDANCE_ARTICLE][D_JUSTIFICATION]" rel="S;3;2500;A" title="Spiegazione" class="ckeditor_simple">
				<?= !empty($guue["DIRECTIVE_2014_23_EU"]["PT_AWARD_CONTRACT_WITHOUT_PUBLICATION"]["D_ACCORDANCE_ARTICLE"]["D_JUSTIFICATION"]) ? $guue["DIRECTIVE_2014_23_EU"]["PT_AWARD_CONTRACT_WITHOUT_PUBLICATION"]["D_ACCORDANCE_ARTICLE"]["D_JUSTIFICATION"] : null?>
			</textarea>
		</td>
	</tr>
</table>