<?
if (isset($_POST["id"])) {
	session_start();
	include("../../../config.php");
	include_once($root."/inc/funzioni.php");
;
	$qualificazione = get_campi("b_qualificazione_progettazione");
	$qualificazione["importo"] = 0;
	$id = $_POST["id"];
}
?>
<tr>
	<td>
		<div style="max-width:700px !important">
			<select rel="S;0;0;N" title="Categoria" name="qualificazione[<? echo $id ?>][codice_categoria]" id="codice_categoria_qualificazione_<? echo $id ?>">
				<option value="">Seleziona...</option>
				<?
				$sql = "SELECT * FROM b_categorie_progettazione WHERE attivo = 'S' ORDER BY codice";
				$ris_elenco = $pdo->query($sql);
				if ($ris_elenco->rowCount()>0) {
					while($oggetto = $ris_elenco->fetch(PDO::FETCH_ASSOC)) {
						?>
						<option value="<? echo $oggetto["codice"] ?>"><strong><? echo $oggetto["id"] . "</strong> - " . $oggetto["descrizione"] ?></option>
						<?
					}
				}
				?>
			</select>
		</div>
	</td>
	<td width="10">
		<input size="12" class="importo" onchange="check_importi();" title="Importo" rel="S;1;0;N" name="qualificazione[<? echo $id ?>][importo]" id="importo_base_qualificazione_<? echo $id ?>" value="<? echo $qualificazione["importo"] ?>">
	</td>
	<td width="10">
		<input type="image" onClick="$(this).parents('tr').first().remove();" src="/img/del.png" title="Elimina">
	</td>
</tr>

<? if (!isset($_POST["id"])) { ?>
<script>
	$("#codice_categoria_qualificazione_<? echo $id ?>").val('<? echo $qualificazione["codice_categoria"] ?>');
</script>
<? } ?>
