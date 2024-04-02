<?
  if (isset($form)) {
    ?>
    <div style="text-align:center">
      <label>Si</label>
      [ <?= ($values["_0"][0] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
      &nbsp;&nbsp;
      <label>No</label>
      [ <?= ($values["_0"][0] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
    </div><br>
    <? if ($values["_0"][0] == "true" || $show_empty) { ?>
      <?
      if ($show_empty) {
        ?>
        <strong>In caso affermativo,</strong><br><br>
        <?
      }
      ?>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              Qual &egrave; la percentuale corrispondente di lavoratori con disabilit&agrave; o svantaggiati?
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_0"][1]; ?>
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              Se richiesto, specificare a quale categoria di lavoratori con disabilit&agrave; o svantaggiati appartengono i lavoratori interessati:
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_0"][2]; ?>
            </td>
          </tr>
        </table>
    <?
    }
  }
?>
