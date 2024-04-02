<?
  if (isset($criterio)) {
    if (empty($criterio["codice_padre"])) $peso_totale += $criterio["punteggio"];
    $ris_sub->bindValue(":codice_criterio",$criterio["codice"]);
    $ris_sub->execute();
    $sub_criteri = [];
    $totale_criterio = 0;
    $color = "";
    if ($ris_sub->rowCount() > 0) {
      while($sub = $ris_sub->fetch(PDO::FETCH_ASSOC)) {
        $sub_criteri[] = $sub;
        $totale_criterio += $sub["punteggio"];
      }
      if ($totale_criterio != $criterio["punteggio"]) {
        $color = "color: #C00";
      }
    }
    if (!empty($criterio["valutazione"]) && $criterio["tipo"] == "N") {
      if ($criterio["economica"] == "S" || $criterio["temporale"] == "S") {
        $preview_economica = true;
      } else {
        $preview_tecnica = true;
      }
    }
?>
  <? if (empty($criterio["codice_padre"])) { ?>
    <tr id="padre-<?= $criterio["codice"] ?>" style="font-weight:bold; <?= $color ?>">
      <td><?= ($criterio["tipo"] == "N") ? "Quantitativo" : "Qualitativo"; ?></td>
      <td style="text-align:center">
        <?= $criterio["nome"] ?>
        <? if (!empty($codice_lotto) && $criterio["codice_lotto"]==0) { ?>
          <br>
          <small><b>Tutti i lotti</b></small>
        <? } ?>
      </td>
    <? } else { ?>
      <tr><td colspan="2"></td>
    <? } ?>
    <td>
      <?= $criterio["descrizione"] ?>
    </td>
    <td style="text-align:center">
      <?= $criterio["punteggio"] ?>
      <? if (!empty($color)) {
        $diff = $totale_criterio - $criterio["punteggio"];
        $label = ($diff < 0) ? "mancanti" : "in eccesso";
        echo "<br><small>(" . $diff . " " . $label . ")</small>";
      } ?>
    </td>
    <td style="text-align:center">
      <? if (count($sub_criteri) == 0 ) { ?><?= (!empty($formule[$criterio["valutazione"]]["titolo"])) ? $formule[$criterio["valutazione"]]["titolo"] : ""; ?><? } ?>
    </td>
    <?
      if ($codice_lotto == $criterio["codice_lotto"]) {
    ?>
      <td style="text-align:center">
        <a title="modifica" class="btn-round btn-warning" href="edit.php?codice=<?= $criterio["codice"] ?>&codice_gara=<?= $criterio["codice_gara"] ?>&codice_lotto=<?= $criterio["codice_lotto"] ?>">
          <span class="fa fa-pencil"></span>
        </a>
      </td>
      <? if (!$lock) { ?>
        <td>
          <? if (empty($criterio["codice_padre"])) { ?>
            <a title="Aggiungi sub" class="btn-round btn-success" href="edit.php?codice=0&codice_padre=<?= $criterio["codice"] ?>&codice_gara=<?= $criterio["codice_gara"] ?>&codice_lotto=<?= $criterio["codice_lotto"] ?>">
              <span class="fa fa-plus"></span>
            </a>
          <? } ?>
        </td>
        <td>
          <button class="btn-round btn-danger" onclick="elimina('<?= $criterio["codice"] ?>','gare/configura_offerta'); return false" title="Elimina" placeholder="Elimina">
            <span class="fa fa-remove"></span>
          </button>
        </td>
      <? } ?>
    <? } else { ?>
      <td style="text-align:center" <?= (!$lock) ? "colspan='3'" : "" ?>>
        <a href="/gare/configura_offerta/index.php?codice=<?= $criterio["codice_gara"] ?>&lotto=<?= $criterio["codice_lotto"] ?>">
          Tutti lotti
        </a>
      </td>
    <? } ?>
  </tr>
  <?
    if (count($sub_criteri) > 0) {
      foreach($sub_criteri AS $criterio) include("tr_criterio.php");
    }
  }
?>
