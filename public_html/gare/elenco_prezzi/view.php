<?
    if (isset($prezzo)) {
?>
  <tr id="prezzo_<?= $id ?>">
      <td width="10">
        <?= $prezzo["tipo"] ?>
      </td>
      <td>
        <strong><? echo $prezzo["descrizione"] ?></strong>
      </td>
      <td width="50">
        <? echo $prezzo["unita"] ?>
      </td>

      <td width="50">
        <? echo $prezzo["quantita"] ?>
      </td>
      <td width="10">
        <input type="image" onClick="edit_prezzo(<?= $id ?>);return false;" src="/img/edit.png" title="Modifica">
      </td>
  </tr>
<? } ?>
