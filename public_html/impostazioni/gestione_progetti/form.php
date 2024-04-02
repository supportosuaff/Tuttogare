<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_conf_gestione_progetti");
		$id = $_POST["id"];
	}

?><tr id="opzione_<? echo $id ?>">
	<td class="handle" style="background:#AAA" width="20">
		<td>
			<input type="hidden" name="opzione[<? echo $id ?>][codice]"id="codice_opzione_<? echo $record["codice"] ?>" value="<? echo $record["codice"]  ?>">
			<input type="text" class="titolo_edit" name="opzione[<? echo $id ?>][titolo]"  title="titolo" rel="S;3;255;A" id="titolo_opzione_<? echo $id ?>" value="<? echo $record["titolo"] ?>">
 			<input type="hidden" name="opzione[<? echo $id ?>][ordinamento]"id="ordinamento_opzione_<? echo $record["codice"] ?>" class="ordinamento" value="<? echo $record["ordinamento"]  ?>">
			<table width="100%" class="_dettaglio">
			<tr><td class="etichetta">Tipo</td><td>
			<select  name="opzione[<? echo $id ?>][tipo]" title="Tipo" rel="S;0;20;A" id="tipo_opzione_<? echo $id ?>">
				<option value="gestione">Gestione</option>
				<option value="monitoraggio">Monitoraggio</option>
				<option value="integrazioni">Integrazioni</option>
			</select>
			</td>

<td class="etichetta">Modulo di riferimento</td><td colspan="3">
<select  name="opzione[<? echo $id ?>][modulo_riferimento]" title="Modulo riferimento" rel="S;0;0;A" id="modulo_riferimento_opzione_<? echo $id ?>">
<?
	$sql = "SELECT * FROM b_moduli ORDER BY ordinamento";
					   		$ris = $pdo->query($sql);
									if ($ris->rowCount()>0) {
										while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
											?><option value="<? echo $rec["radice"] ?>"><? echo $rec["titolo"] ?></option><?
										}
									}
?>
</select></td>
</tr>
<tr>
<td class="etichetta">Link</td>
                       <td colspan="3">
                    <input type="text" style="width:98%" name="opzione[<? echo $id ?>][link]"  title="Link" rel="S;3;255;A" id="link_opzione_<? echo $id ?>" value="<? echo $record["link"] ?>">
                       </td>

<td class="etichetta">Badge</td>
                       <td colspan="3">
                    <input type="text" style="width:98%" name="opzione[<? echo $id ?>][badge]"  title="Badge" rel="N;3;255;A" id="badge_opzione_<? echo $id ?>" value="<? echo $record["badge"] ?>">
                       </td>
</tr></table></td>
<td width="10"><button class="btn-round btn-warning" onClick="$('#opzione_<? echo $id ?> ._dettaglio').toggle(); return false"><span class="fa fa-search"></span></button></td>
 <td width="10"><button class="btn-round btn-danger" onClick="elimina('<? echo $id ?>','impostazioni/gestione_progetti');return false;" title="Elimina"><span class="fa fa-remove"></span></button></td>
</tr>
<?

	if (!isset($_POST["id"])) {
		?><script>
			$("#modulo_riferimento_opzione_<? echo $id ?>").val("<? echo $record["modulo_riferimento"] ?>");
			$("#tipo_opzione_<? echo $id ?>").val('<? echo $record["tipo"] ?>');
		</script>
        <?
	}
?>
