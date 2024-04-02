<?
if (isset($_POST["id"])) {
  if (!isset($pdo)) {
    session_start();
    include("../../../config.php");
    include_once($root."/inc/funzioni.php");
;
  }
  $record_pubblicita = get_campi("b_pubblicita_legale_concorsi");
  $id = $_POST["id"];
}
$lock = false;
?>
<tr id="pubblicita_<?=$id?>">
  <td width="5%"><input type="text" id="pubblicita_data_protocollo_<? echo $id; ?>" name="pubblicita[<? echo $id; ?>][data_protocollo]" value="<? echo mysql2date($record_pubblicita["data_protocollo"]) ?>" class="datepick" title="Data protocollo" rel="S;10;10;D;<? echo date("d/m/Y H:i") ?>;<="></td>
  <td width="5%"><input type="text" id="pubblicita_numero_protocollo_<? echo $id; ?>" name="pubblicita[<? echo $id; ?>][numero_protocollo]" value="<? echo $record_pubblicita["numero_protocollo"] ?>" title="Numero Protocollo" rel="S;1;100;A"></td>
  <td width="78%"><input type="text" style="width: 98%;" id="pubblicita_descrizione_<? echo $id; ?>" name="pubblicita[<? echo $id; ?>][descrizione]" value="<? echo $record_pubblicita["descrizione"] ?>" title="Descrizione" rel="S;1;255;A"></td>
  <td width="2%"><input type="image" src="/img/del.png" onClick="elimina('<? echo $id ?>','concorsi/pubblicita_legale'); return false;" name="elimina_<? echo $id; ?>" width="35px" height="auto" /></td>
</tr>
