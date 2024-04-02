<?
if (isset($_POST["id"])) {
  if (!isset($pdo)) {
    session_start();
    include("../../../config.php");
    include_once($root."/inc/funzioni.php");
;
  }
  $record_incasso = get_campi("b_incassi");
  $id_incasso = $_POST["id"];
}
$lock = false;
?>
<tr id="incasso_<?= $id_incasso ?>">
  <td width="10%"><input class="importo" type="text" id="incasso_importo_<? echo $id_incasso; ?>" name="incasso[<? echo $id_incasso; ?>][importo]" value="<? echo (float)$record_incasso["importo"] ?>" title="Importo"></td>
  <td width="60%"><input type="text" style="width: 98%;" id="incasso_descrizione_<? echo $id_incasso; ?>" name="incasso[<? echo $id_incasso; ?>][descrizione]" value="<? echo $record_incasso["descrizione"] ?>" title="Descrizione"></td>
  <td><input type="text" <?= empty($record_incasso["data_incasso"]) ? "style='border:1px solid #F00'" : ""; ?> id="incasso_data_incasso_<? echo $id_incasso; ?>" name="incasso[<? echo $id_incasso; ?>][data_incasso]" value="<? echo mysql2date($record_incasso["data_incasso"]) ?>" title="Data Incasso"></td>
  <td width="2%"><input type="image" src="/img/del.png" onClick="elimina('<? echo $id_incasso ?>','gare/rendicontazione'); return false;" name="elimina_<? echo $id_incasso; ?>" width="35px" height="auto" /></td>
</tr>
<script>
$("#incasso_data_incasso_<? echo $id_incasso; ?>").datepicker();
</script>
