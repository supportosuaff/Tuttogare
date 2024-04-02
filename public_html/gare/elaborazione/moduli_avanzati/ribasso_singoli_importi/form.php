<?
	$bind_check_ribasso = array(":codice_gara"=>$record_gara["codice"]);
	$sql_check_ribasso = "SELECT * FROM b_importi_gara WHERE codice_gara = :codice_gara";
	$ris_check_ribasso = $pdo->bindAndExec($sql_check_ribasso,$bind_check_ribasso);
	if ($ris_check_ribasso->rowCount() > 1) { ?>
		<table width="100%">
			<tr>
				<tr><td class="etichetta" colspan="2" style="background-color: #CCC; text-align:left;">
					<strong>Ribasso su singoli importi di gara</strong>
				</td>
			</tr>
			<tr>
				<td style="text-align:center; font-size:12px">
					Si <input <?= ($record_gara["ribassoSingoliImporti"]) ? "checked" : ""; ?> type="radio" name="ribassoSingoliImporti" value="1" id="ribasso_S">
			    No <input <?= (!$record_gara["ribassoSingoliImporti"]) ? "checked" : ""; ?> type="radio" name="ribassoSingoliImporti" value="0" id="ribasso_N">
			    </td>
			</tr>
		</table>
	<? } ?>
