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
      <? if ($show_empty) {
        ?>
        <strong>In caso affermativo,</strong><br>
        <strong>Accertarsi che gli altri operatori interessati forniscano un DGUE distinto.</strong><br><br>
        <?
      }
      ?>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              a) Specificare il ruolo dell'operatore economico nel raggruppamento, ovvero consorzio, GEIE, rete di impresa di cui all’art.65 comma 2 lettere e) f) g) h) e art.66 comma 1 lettere a) b) c) d) f) Codice (capofila, responsabile di compiti specifici,ecc.):
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_0"][1]; ?>
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              b) Indicare gli altri operatori economici che compartecipano alla procedura di appalto:
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_0"][2]; ?>
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              c) Se pertinente, indicare il nome del raggruppamento partecipante:
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_0"][3]; ?>
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              d) Se pertinente, indicare la denominazione degli operatori economici facenti parte di un consorzio di cui all’art.65 comma 2 lettere b) d) o di una società di professionisti di cui all’art.66 comma 1 lettera g) che eseguono le prestazioni oggetto del contratto.
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_0"][4]; ?>
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              e) Se pertinente, specificare le categorie di lavori o le parti del servizio o della fornitura che saranno eseguite dai singoli operatori riuniti o consorziati (art.68 comma 2 del Codice)
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_0"][5]; ?>
            </td>
          </tr>
        </table>
    <?
    }
  }
?>
