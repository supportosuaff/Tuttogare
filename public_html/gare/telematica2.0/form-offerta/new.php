<?
  if (isset($ris_buste) && ($ris_buste->rowCount() > 0) && !empty($submit) && !empty($tipo)) {
    $economica = false;
    if ($tipo == "economica") $economica = true;
    $tabella = getFormOfferta($record_gara["codice"],$codice_lotto,$economica,true);
    if (!empty($tabella)) {
      echo $tabella;
      if ($tipo == "economica") {
        ?>
        <table width="100%">
          <tr>
            <th class="etichetta"><strong><?= traduci("Oneri di sicurezza aziendale interni") ?></strong></th>
          </tr>
          <tr>
            <td style="text-align:center">
              <input type="text" name="offerta[sicurezza][0]" class="titolo_edit" style="text-align:center;max-width:300px" value="" rel="S;0;0;N" title="<?= traduci("Costi di sicurezza aziendale interni") ?>">
            </td>
          </tr>
          <tr>
            <th class="etichetta"><strong><?= traduci("Costo della manodopera") ?></strong></th>
          </tr>
          <tr>
            <td style="text-align:center">
              <input type="text" name="offerta[manodopera][0]" class="titolo_edit" style="text-align:center;max-width:300px" value="" rel="S;0;0;N" title="<?= traduci("Costo della manodopera") ?>">
            </td>
          </tr>
        </table>
        <?
      }
    } else {
      ?><h1><?= traduci("Errore di configurazione") ?></h1><?
      die();
    }
  }
?>
