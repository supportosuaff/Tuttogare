<? $function_i = $i; ?>
<tr>
	<td class="etichetta" colspan="4">
		<label>II.2.5) Criteri di aggiudicazione </label>
	</td>
</tr>
<tr>
	<td colspan="4" id="award_criteria_item_to_ignore_<?= $item ?>"><button class="aggiungi" type="button" onClick="aggiungi('forms/2_0_9_S3/common/criteri_di_aggiudicazione_2_2_5.php','#award_criteria_item_to_ignore_<?= $item ?>', {item:'<?= $item ?>'});return false;"><img src="/img/add.png" alt="Aggiungi committente">Aggiungi criterio</button><?
		if(empty($v_form)) $v_form = $_SESSION["guue"]["v_form"];
		include $root . '/guue/forms/2_0_9_S3/common/criteri_di_aggiudicazione_2_2_5.php';
	?></td>
</tr>
<tr>
	<td colspan="4">
		<table class="bordered">
			<tr>
				<td class="etichetta">
					<label>
						<i>I criteri possono essere:
							<ul>
						    <li>Criterio di qualit&agrave; e criterio di costo;</li>
						    <li>Criterio di qualit&agrave; e criterio di prezzo (&Egrave; necessario specificare la ponderazione);</li>
						    <li>Criterio di costo;</li>
						    <li>Criterio di Prezzo (Non si deve indicare la ponderazione);</li>
							</ul>
						</i>
					</label>
				</td>
			</tr>
		</table>
	</td>
</tr>
<? $i = $function_i; ?>
