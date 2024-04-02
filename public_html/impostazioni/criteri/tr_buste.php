<?
	if (!isset($id)) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record_busta = get_campi("b_criteri_buste");
		$id_criterio = $_GET["id_criterio"];
		$id_busta = $_POST["id"];
	}
?>
<tr id="busta_<? echo $id_busta ?>">
<td class="handle"></td><td><input type="text"  style="width:98%" name="busta[<? echo $id_criterio ?>][<? echo $id_busta ?>][nome]"  title="Nome" rel="S;3;255;A" id="nome_busta_<? echo $id_busta ?>" value="<? echo $record_busta["nome"] ?>">
<input type="hidden" class="ordinamento" name="busta[<? echo $id_criterio ?>][<? echo $id_busta ?>][ordinamento]" id="ordinamento_busta_<? echo $id_busta ?>" value="<? echo $record_busta["ordinamento"] ?>">
<input type="hidden" name="busta[<? echo $id_criterio ?>][<? echo $id_busta ?>][codice]" id="codice_busta_<? echo $id_busta ?>" value="<? echo $record_busta["codice"] ?>">
<input type="hidden" name="busta[<? echo $id_criterio ?>][<? echo $id_busta ?>][id]" id="id_busta_<? echo $id_busta ?>" value="<? echo $id_busta ?>"></td>
<td width="10">
<select name="busta[<? echo $id_criterio ?>][<? echo $id_busta ?>][tecnica]" rel="S;1;1;A"  title="Tecnica" id="tecnica_busta_<? echo $id_busta ?>">
<option value="N">No</option>
<option value="S">Si</option>
</select>
</td>
<td width="10">
<select name="busta[<? echo $id_criterio ?>][<? echo $id_busta ?>][economica]" rel="S;1;1;A"  title="Economica" id="economica_busta_<? echo $id_busta ?>">
<option value="N">No</option>
<option value="S">Si</option>
</select>
</td>
<td width="10">
<select name="busta[<? echo $id_criterio ?>][<? echo $id_busta ?>][mercato_elettronico]" rel="S;1;1;A"  title="Mercato Elettronico" id="mercato_elettronico_busta_<? echo $id_busta ?>">
<option value="N">No</option>
<option value="S">Si</option>
</select>
<td width="10">
<select name="busta[<? echo $id_criterio ?>][<? echo $id_busta ?>][2fase]" rel="S;1;1;A"  title="2 Fase" id="2fase_busta_<? echo $id_busta ?>">
<option value="N">No</option>
<option value="S">Si</option>
</select>
</td>

 <td width="10"><input type="image" onClick="elimina('<? echo $id_busta ?>','impostazioni/criteri/buste');return false;" src="/img/del.png" title="Elimina"></td></tr>
  <?
 if (isset($id)) {
	 ?>
     <script>
		 $("#tecnica_busta_<? echo $id_busta ?>").val("<? echo $record_busta["tecnica"] ?>");
	$("#economica_busta_<? echo $id_busta ?>").val("<? echo $record_busta["economica"] ?>");
	$("#mercato_elettronico_busta_<? echo $id_busta ?>").val("<? echo $record_busta["mercato_elettronico"] ?>");
	$("#2fase_busta_<? echo $id_busta ?>").val("<? echo $record_busta["2fase"] ?>");
	 </script>
     <?
 }
 ?>
