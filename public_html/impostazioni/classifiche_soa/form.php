<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_classifiche_soa");
		$id = $_POST["id"];
	}
	
?>
  
<tr id="classifica_<? echo $id ?>"><td width="10%"><input type="hidden" name="classifica[<? echo $id ?>][codice]"id="codice_<? echo $id ?>" value="<? echo $record["codice"] ?>">
<input type="text" class="titolo_edit" name="classifica[<? echo $id ?>][id]"  title="ID" rel="S;1;10;A" id="id_<? echo $id ?>" value="<? echo $record["id"] ?>">
</td>                	            <td><input type="text" value="<? echo $record["minimo"] ?>" name="classifica[<? echo $id ?>][minimo]" id="minimo_<? echo $id ?>" rel="S;0;0;N" title="Importo minimo"></td>
                        	    <td><input type="text" value="<? echo $record["massimo"] ?>" name="classifica[<? echo $id ?>][massimo]" id="massimo_<? echo $id ?>" rel="S;0;0;N" title="Importo massimo"></td>
<td width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/classifiche_soa');return false" src="/img/del.png" title="Elimina"></td></tr>
