<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_gestione_gare");
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
				<option value="elaborazione">Elaborazione</option>
			    <option value="documentale">Documentale</option>
			    <option value="comunicazione">Comunicazione</option>
			</select>
			</td>
			<td class="etichetta">Mercato Elettronico</td><td><select name="opzione[<? echo $id ?>][mercato_elettronico]" title="Mercato Elettronico" rel="S;1;1;A" id="mercato_elettronico_opzione_<? echo $id ?>" >
			<option value="S">Si</option>
			<option value="E">Esclusiva</option>
			<option value="N">No</option>
			</select></td>
			<td class="etichetta">Scaduta</td><td><select name="opzione[<? echo $id ?>][scaduta]" title="Scaduta" rel="S;1;1;A" id="scaduta_opzione_<? echo $id ?>" >
			<option value="S">Si</option>
			<option value="N">No</option>
			</select></td>
			<td class="etichetta">Apertura</td><td><select name="opzione[<? echo $id ?>][apertura]" title="Apertura" rel="S;1;1;A" id="apertura_opzione_<? echo $id ?>" >
			<option value="S">Si</option>
			<option value="N">No</option>
			</select></td>
			</tr>
			<tr>
			<td class="etichetta">Fase minima</td>
       <td>
       <select name="opzione[<? echo $id ?>][fase_minima]" id="fase_minima_opzione_<? echo $id ?>" rel="S;0;0;N" title="Fase minima">
       <option value="0">Nessuna</option>
       <? $sql = "SELECT * FROM b_stati_gare ORDER BY fase";
	$ris = $pdo->query($sql);
	if ($ris->rowCount()>0) {
		while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
			?><option value="<? echo $rec["fase"] ?>"><? echo $rec["titolo"] ?></option><?
		}
	}
?>
       </select>
       </td>
<td class="etichetta">Fase massima</td>
       <td>
       <select name="opzione[<? echo $id ?>][fase_massima]" id="fase_massima_opzione_<? echo $id ?>" rel="S;0;0;N" title="Fase massima">
       <option value="0">Nessuna</option>
       <? $sql = "SELECT * FROM b_stati_gare ORDER BY fase";
	$ris = $pdo->query($sql);
	if ($ris->rowCount()>0) {
		while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
			?><option value="<? echo $rec["fase"] ?>"><? echo $rec["titolo"] ?></option><?
		}
	}
?>
       </select>
       </td>

<td class="etichetta">Fasi escluse</td>
                       <td colspan="3">
                       <select name="opzione[<? echo $id ?>][stati_esclusi][]" multiple id="stati_esclusi_opzione_<? echo $id ?>" rel="N;0;0;ARRAY" title="Stati esclusi">
                       <option value="">Nessuna</option>
                       <? $sql = "SELECT * FROM b_stati_gare ORDER BY fase";
					   		$ris = $pdo->query($sql);
									if ($ris->rowCount()>0) {
										while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
											?><option value="<? echo $rec["fase"] ?>"><? echo $rec["titolo"] ?></option><?
										}
									}
								?>
                       </select>
                       </td>
                       </tr>

</tr>
<tr>
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

<td class="etichetta">Modalita</td>
                       <td colspan="3">
                       <select name="opzione[<? echo $id ?>][modalita][]" multiple id="modalita_opzione_<? echo $id ?>" rel="N;0;0;A" title="Modalita">
												<option value="0">Tutte</option>
                       <? $sql = "SELECT * FROM b_modalita ORDER BY codice";
					   						$ris = $pdo->query($sql);
													if ($ris->rowCount()>0) {
														while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
															?><option value="<? echo $rec["codice"] ?>"><? echo $rec["modalita"] ?></option><?
														}
													}
												?>
                       </select>
                       </td>
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
</tr>
<tr>
	<td class="etichetta">Cross platform</td><td><select name="opzione[<? echo $id ?>][cross_p]" title="Cross" rel="S;1;1;A" id="cross_p_opzione_<? echo $id ?>" >
	<option value="S">Si</option>
	<option value="N">No</option>
	</select></td>
	<td class="etichetta">Procedure escluse</td>
	<td colspan="5">
		<select name="opzione[<? echo $id ?>][procedure_escluse][]" multiple id="procedure_escluse_opzione_<? echo $id ?>" rel="N;0;0;A" title="Procedure escluse">
		 <option value="0">Nessuna</option>
		<? $sql = "SELECT * FROM b_procedure ORDER BY codice";
		 $ris = $pdo->query($sql);
			 if ($ris->rowCount()>0) {
				 while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
					 ?><option value="<? echo $rec["codice"] ?>"><? echo $rec["nome"] ?></option><?
				 }
			 }
		 ?>
		</select>
	</td>
</tr>
</table></td>
<td width="10"><button class="btn-round btn-warning" onClick="$('#opzione_<? echo $id ?> ._dettaglio').toggle(); return false"><span class="fa fa-search"></span></td>
 <td width="10"><button class="btn-round btn-danger" onClick="elimina('<? echo $id ?>','impostazioni/gestione');return false;" title="Elimina"><span class="fa fa-remove"></span></button></td>
</tr>
<?

	if (!isset($_POST["id"])) {
		?><script>
			$("#modulo_riferimento_opzione_<? echo $id ?>").val("<? echo $record["modulo_riferimento"] ?>");
			var values = '<? echo $record["stati_esclusi"] ?>';
			$("#stati_esclusi_opzione_<? echo $id ?>").val(values.split(','));
			$("#fase_minima_opzione_<? echo $id ?>").val('<? echo $record["fase_minima"] ?>');
			$("#fase_massima_opzione_<? echo $id ?>").val('<? echo $record["fase_massima"] ?>');
			var modalita = "<? echo $record["modalita"] ?>";
			$("#modalita_opzione_<? echo $id ?>").val(modalita.split(","));
			var procedure_escluse = "<? echo $record["procedure_escluse"] ?>";
			$("#procedure_escluse_opzione_<? echo $id ?>").val(procedure_escluse.split(","));
			$("#tipo_opzione_<? echo $id ?>").val('<? echo $record["tipo"] ?>');
			$("#mercato_elettronico_opzione_<? echo $id ?>").val('<? echo $record["mercato_elettronico"] ?>');
			$("#scaduta_opzione_<? echo $id ?>").val('<? echo $record["scaduta"] ?>');
			$("#apertura_opzione_<? echo $id ?>").val('<? echo $record["apertura"] ?>');
			$("#cross_p_opzione_<? echo $id ?>").val('<? echo $record["cross_p"] ?>');
		</script>
        <?
	}
?>
