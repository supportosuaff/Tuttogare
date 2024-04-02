<?
  if (!isset($range)) {
    $range["minimo"] = "";
    $range["massimo"] = "";
    $range["punti"] = "";
    $id = $_POST["id"];
  }
?>
  <tr id="range-<?= $id ?>">
    <td>
      <input id="option-S-<?= $id ?>-range-minimo" name="option-S[range][<?= $id ?>][minimo]" value="<?= $range["minimo"] ?>" rel="S;0;0;N;option-S-<?= $id ?>-range-massimo;<=" title="Minimo">
    </td>
    <td>
      <input id="option-S-<?= $id ?>-range-massimo" name="option-S[range][<?= $id ?>][massimo]" value="<?= $range["massimo"] ?>" rel="S;0;0;N;option-S-<?= $id ?>-range-minimo;>=" title="Massimo">
    </td>
    <td>
      <input id="option-S-<?= $id ?>-range-punti" name="option-S[range][<?= $id ?>][punti]" value="<?= $range["punti"] ?>" rel="S;0;0;N;punteggio;<=" title="Punti">
    </td>
    <td>
      <button class="btn-round btn-micro btn-danger" onClick="$('#range-<?= $id ?>').slideUp('fast',function() { $(this).remove() }); return false;">
        <span class="fa fa-remove"></span>
      </button>
    </td>
  </tr>
