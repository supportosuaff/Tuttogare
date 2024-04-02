<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_categorie_progettazione");
		$id = $_POST["id"];
	}

?>

<tr id="categoria_<? echo $id ?>">
	<td width="10%"><input type="hidden" name="categoria[<? echo $id ?>][codice]"id="codice_<? echo $id ?>" value="<? echo $record["codice"] ?>">
		<input type="text" class="titolo_edit" name="categoria[<? echo $id ?>][id]"  title="ID" rel="S;1;10;A" id="id_<? echo $id ?>" value="<? echo $record["id"] ?>">
	</td>
	<td width="2%"><input size="10" type="text" name="categoria[<? echo $id ?>][group]"  title="Gruppo" rel="S;1;10;N" id="group_<? echo $id ?>" value="<? echo $record["group"] ?>">
	<td width="60%">
		<input type="text" style="width:95%"  name="categoria[<? echo $id ?>][destinazione]"  title="Destinazione Funzionale" rel="N;1;255;A" id="destinazione_<? echo $id ?>" value="<? echo $record["destinazione"] ?>">
		<textarea rows="5" style="width:95%" name="categoria[<? echo $id ?>][descrizione]"  title="Descrizione" rel="S;3;0;A" id="descrizione_<? echo $id ?>"><? echo $record["descrizione"] ?></textarea>
	</td>
	<td width="5%"><input type="text" style="width:95%"  name="categoria[<? echo $id ?>][corrispondenze_143]"  title="L 143/49 Classi e categorie" rel="N;1;100;A" id="corrispondenze_143_<? echo $id ?>" value="<? echo $record["corrispondenze_143"] ?>">
	</td>
	<td width="5%"><input type="text" style="width:95%"  name="categoria[<? echo $id ?>][corrispondenze_71]"  title="DM 18/11/1971" rel="N;1;100;A" id="corrispondenze_71_<? echo $id ?>" value="<? echo $record["corrispondenze_71"] ?>">
	</td>
	<td width="5%"><input type="text" style="width:95%"  name="categoria[<? echo $id ?>][corrispondenze_91]"  title="DM 232/1991" rel="N;1;100;A" id="corrispondenze_91_<? echo $id ?>" value="<? echo $record["corrispondenze_91"] ?>">
	</td>
	<td width="5%"><input size="10" type="text" name="categoria[<? echo $id ?>][complessita]"  title="Complessita" rel="S;1;10;N" id="complessita_<? echo $id ?>" value="<? echo $record["complessita"] ?>">
	<td width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/categorie_progettazione');return false" src="/img/del.png" title="Elimina"></td>
</tr>
