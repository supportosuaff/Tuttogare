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
              a) Specificare il ruolo dell'operatore economico nel raggruppamento, ovvero consorzio, GEIE, rete di impresa di cui all' art. 45, comma 2, lett. d), e), f) e g) e all'art. 46, comma 1, lett. a), b), c), d) ed e) del Codice (capofila, responsabile di compiti specifici,ecc.):
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
              d) Se pertinente, indicare la denominazione degli operatori economici facenti parte di un consorzio di cui all'art. 45, comma 2, lett. b) e c), o di una societ&agrave; di professionisti di cui all'articolo 46, comma 1, lett. f) che eseguono le prestazioni oggetto del contratto.
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_0"][4]; ?>
            </td>
          </tr>
        </table>
    <?
    }
  }
?>
