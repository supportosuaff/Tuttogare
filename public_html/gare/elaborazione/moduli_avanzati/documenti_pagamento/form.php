<?
	$checked_N = "checked";
	$checked_S = "";
	$style = "style=\"display:none;\"";
	$rel_costo = "N;0;0;N";
	$record_costo = get_campi("b_costo_documenti");
	$record_costo["intestazione"] = $_SESSION["ente"]["denominazione"];
	$bind = array();
	$bind[":codice"] = $record_gara["codice"];
	$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
	$sql_costo = "SELECT * FROM b_costo_documenti WHERE codice_gara = :codice AND codice_ente = :codice_ente";
	$ris_costo = $pdo->bindAndExec($sql_costo,$bind);
	if ($ris_costo->rowCount() > 0) {
		$style = "";
		$record_costo = $ris_costo->fetch(PDO::FETCH_ASSOC);
		$checked_N = "";
		$checked_S = "checked";
		$rel_costo = "S;0;0;N";
	} else {
		$sql_costo = "SELECT * FROM b_costo_documenti WHERE codice_ente = " . $_SESSION["ente"]["codice"] . " ORDER BY codice DESC LIMIT 0,1";
		$ris_costo = $pdo->bindAndExec($sql_costo,$bind);
		if ($ris_costo->rowCount() > 0) {
			$record_costo = $ris_costo->fetch(PDO::FETCH_ASSOC);
		}
	}
?>
<table width="100%">
	<tr>
		<tr><td class="etichetta" style="background-color: #CCC; text-align:left;">
			<strong>Documenti a pagamento</strong>
		</td>
	</tr>
<tr>
	<td style="text-align:center; font-size:12px">
    Si <input <?= $checked_S ?> class="costo_documenti" type="radio" name="costo" value="S" id="costo_S">
    No <input <?= $checked_N ?> class="costo_documenti" type="radio" name="costo" value="N" id="costo_N">
    </td>
</tr>
</table>
<table width="100%" <? echo $style ?> id="form_costo">
                        <tr><td class="etichetta" width="10%">Costo</td><td><input size="10" type="text" name="costo_documenti[costo]" id="costo_documenti_costo" title="Costo documenti" value="<? echo $record_costo["costo"] ?>" rel="<? echo $rel_costo ?>"></td>
                        </tr>
                        <tr><td class="etichetta" width="10%">C/C Postale</td><td><input size="20" type="text" name="costo_documenti[cc_posta]" id="costo_documenti_cc_posta" title="C/C Postale" value="<? echo $record_costo["cc_posta"] ?>" rel="N;0;0;A"></td>
                        </tr>
                        <tr><td class="etichetta" width="10%">IBAN</td><td><input style="width:95%" type="text" name="costo_documenti[iban]" id="costo_documenti_iban" title="IBAN" value="<? echo $record_costo["iban"] ?>" rel="N;0;34;A"></td></tr>
                        <tr><td class="etichetta" width="10%">Intestazione C/C Bancario</td><td><input style="width:95%" type="text" name="costo_documenti[intestazione]" id="costo_documenti_intestazione" title="Intestazione Bancaria" value="<? echo $record_costo["intestazione"] ?>" rel="N;0;250;A"></td></tr>
                        <tr><td class="etichetta" width="10%">Banca</td><td><input style="width:95%" type="text" name="costo_documenti[banca]" id="costo_documenti_banca" title="Banca" value="<? echo $record_costo["banca"] ?>" rel="N;0;150;A"></td></tr>
                         </table>
<script>
	$(".costo_documenti").change(function() {
		if ($(this).val() == "S") {
			$("#form_costo").slideDown();
			$("#costo_documenti_costo").attr("rel","S;0;0;N");
			if ($("#costo_documenti_iban").val() != "") {
				$("#costo_documenti_intestazione").attr("rel","S;3;250;A");
				$("#costo_documenti_banca").attr("rel","S;3;150;A");
			}
		} else {
			$("#form_costo").slideUp();
			$("#costo_documenti_costo").attr("rel","N;0;0;N");
			$("#costo_documenti_intestazione").attr("rel","N;3;250;A");
			$("#costo_documenti_banca").attr("rel","N;3;150;A");
		}
	});

	$("#costo_documenti_iban").change(function() {
		if ($(this).val() == "") {
			$("#costo_documenti_intestazione").attr("rel","N;3;250;A");
			$("#costo_documenti_banca").attr("rel","N;3;150;A");
		} else {
			$("#costo_documenti_intestazione").attr("rel","S;3;250;A");
			$("#costo_documenti_banca").attr("rel","S;3;150;A");
		}
	});
</script>
