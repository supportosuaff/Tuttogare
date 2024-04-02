<h3><b>VI.1) Informazioni relative alla rinnovabilit&agrave;</b></h3>
<table class="bordered">
	<tbody>
		<tr>
			<td class="etichetta">
				<label>Si tratta di un appalto rinnovabile?</label>
			</td>
			<td>
				<?
				$radio_as_select_for_recurrent_procurement = "";
				if(!empty($guue["COMPLEMENTARY_INFO"]["radio_as_select_for_recurrent_procurement"])) {
					$radio_as_select_for_recurrent_procurement = $guue["COMPLEMENTARY_INFO"]["radio_as_select_for_recurrent_procurement"];
				}
				?>
				<script type="text/javascript">
					var radio_as_select_for_recurrent_procurement_option = {
							'RECURRENT_PROCUREMENT' : [
								'enable_field',
								'',
								[],
								'estimated_timing'
							]
						};
				</script>
				<select name="guue[COMPLEMENTARY_INFO][radio_as_select_for_recurrent_procurement]" rel="<?= isRequired("radio_as_select_for_recurrent_procurement") ?>;1;0;A" title="Appalto rinnovabile" onchange="add_extra_info($(this).val(), radio_as_select_for_recurrent_procurement_option)" >
					<option value="">Seleziona..</option>
					<option <?= $radio_as_select_for_recurrent_procurement == "RECURRENT_PROCUREMENT" ? 'selected="selected"' : null ?> value="RECURRENT_PROCUREMENT">Si</option>
					<option <?= $radio_as_select_for_recurrent_procurement == "NO_RECURRENT_PROCUREMENT" ? 'selected="selected"' : null ?> value="NO_RECURRENT_PROCUREMENT">No</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="etichetta">
				Indicare il calendario previsto di pubblicazione dei prossimi avvisi:
			</td>
		</tr>
		<tr>
			<td colspan="2" class="etichetta">
				<textarea id="estimated_timing" rel="" class="ckeditor_simple" name="guue[COMPLEMENTARY_INFO][ESTIMATED_TIMING]" <?= $radio_as_select_for_recurrent_procurement != "RECURRENT_PROCUREMENT" ? 'disabled="disabled"' : null ?> >
				<?= ($radio_as_select_for_recurrent_procurement == "RECURRENT_PROCUREMENT" && !empty($guue["COMPLEMENTARY_INFO"]["ESTIMATED_TIMING"])) ? $guue["COMPLEMENTARY_INFO"]["ESTIMATED_TIMING"] : null ?>
				</textarea>
			</td>
		</tr>
	</tbody>
</table>