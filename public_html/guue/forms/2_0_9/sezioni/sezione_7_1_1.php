<tr>
	<td class="etichetta" colspan="4">
		<label><b>VII.1.1) Codice CPV principale:</b></label>
	</td>
</tr>
<?
$sql = "SELECT * FROM b_cpv";
$ris = $pdo->bindAndExec($sql);
if($ris->rowCount() > 0) {
	$rec_cpv = $ris->fetchAll(PDO::FETCH_ASSOC);
}
?>
<tr>
	<td colspan="4">
		<select name="guue[MODIFICATIONS_CONTRACT][DESCRIPTION_PROCUREMENT][CPV_MAIN][CPV_CODE][ATTRIBUTE][CODE]" title="Codice CPV" rel="S;1;0;A">
			<option value="">Seleziona..</option>
			<?
				foreach ($rec_cpv as $cpv) {
					?>
					<option <?= !empty($guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["CPV_MAIN"]["CPV_CODE"]["ATTRIBUTE"]["CODE"]) ? 'selected="selected"' : null ?> value="<?= $cpv["codice"] ?>"><?= $cpv["descrizione"] ?></option>
					<?
				}
			?>
		</select>
	</td>
</tr>