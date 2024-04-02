<?
if (isset($_POST["id"])) {
  if (!isset($pdo)) {
    session_start();
    include("../../config.php");
    include_once($root."/inc/funzioni.php");
;
  }
  $record_limite = get_campi("b_limitazioni");
  $id_limite = $_POST["id"];
}
?>
<tr id="limite_<?= $id_limite ?>">
  <td width="250">
    <select name="limite[<?= $id_limite ?>][tipologia]" rel="S;0;0;N" title="Tipologia">
      <option value="0">Tutte</option>
    <?
      $sql = "SELECT * FROM b_tipologie WHERE eliminato = 'N' ORDER BY codice";
      $ris = $pdo->query($sql);
      if ($ris->rowCount()>0) {
        while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
          ?><option <?= (!empty($record_limite["tipologia"]) && $record_limite["tipologia"] == $rec["codice"]) ? "selected" : "" ?> value="<? echo $rec["codice"] ?>"><? echo $rec["tipologia"] ?></option><?
        }
      }
    ?>
    </select>
  </td>
  <td><input type="text" name="limite[<?= $id_limite; ?>][importo_max]" value="<? echo $record_limite["importo_max"] ?>" title="Importo massimo"></td>
  <td><input type="image" src="/img/del.png" onClick="$(this).closest('tr').remove()" name="elimina_<?= $id_limite; ?>" width="35px" height="auto" /></td>
</tr>
