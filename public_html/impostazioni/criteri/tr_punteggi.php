<?
	if (!isset($id)) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record_punteggio = get_campi("b_criteri_punteggi");
		$id_criterio = $_GET["id_criterio"];
		$id_punteggio = $_POST["id"];
	}
?>
<tr id="punteggio_<? echo $id_punteggio ?>"><td class='handle'></td><td><input type="text"  style="width:98%" name="punteggio[<? echo $id_criterio ?>][<? echo $id_punteggio ?>][nome]"  title="Nome" rel="S;3;255;A" id="nome_punteggio_<? echo $id_punteggio ?>" value="<? echo $record_punteggio["nome"] ?>">
<input type="hidden" class="ordinamento" name="punteggio[<? echo $id_criterio ?>][<? echo $id_punteggio ?>][ordinamento]" id="ordinamento_punteggio_<? echo $id_punteggio ?>" value="<? echo $record_punteggio["ordinamento"] ?>">
<input type="hidden" name="punteggio[<? echo $id_criterio ?>][<? echo $id_punteggio ?>][codice]" id="codice_punteggio_<? echo $id_punteggio ?>" value="<? echo $record_punteggio["codice"] ?>">
<input type="hidden" name="punteggio[<? echo $id_criterio ?>][<? echo $id_punteggio ?>][id]" id="id_punteggio_<? echo $id_punteggio ?>" value="<? echo $id_punteggio ?>"></td>
<td width="10">
<select name="punteggio[<? echo $id_criterio ?>][<? echo $id_punteggio ?>][economica]" rel="S;1;1;A"  title="Economica" id="economica_punteggio_<? echo $id_punteggio ?>">
<option value="N">No</option>
<option value="S">Si</option>
</select>
</td>
<td width="10">
<select name="punteggio[<? echo $id_criterio ?>][<? echo $id_punteggio ?>][migliorativa]" rel="S;1;1;A"  title="Migliorativa" id="migliorativa_punteggio_<? echo $id_punteggio ?>">
<option value="N">No</option>
<option value="S">Si</option>
</select>
</td>

<td width="10">
<select name="punteggio[<? echo $id_criterio ?>][<? echo $id_punteggio ?>][temporale]" rel="S;1;1;A"  title="Temporale" id="temporale_punteggio_<? echo $id_punteggio ?>">
<option value="N">No</option>
<option value="S">Si</option>
</select>
</td>

 <td width="10"><input type="image" onClick="elimina('<? echo $id_punteggio ?>','impostazioni/criteri/punteggi');return false;" src="/img/del.png" title="Elimina"></td></tr>
 <?
 if (isset($id)) {
	 ?>
     <script>
	 $("#economica_punteggio_<? echo $id_punteggio ?>").val("<? echo $record_punteggio["economica"] ?>");
	 $("#migliorativa_punteggio_<? echo $id_punteggio ?>").val("<? echo $record_punteggio["migliorativa"] ?>");
	 $("#temporale_punteggio_<? echo $id_punteggio ?>").val("<? echo $record_punteggio["temporale"] ?>");
	 </script>
     <?
 }
 ?>
