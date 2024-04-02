<?
  if (isset($form)) {
    ?>
    <div style="text-align:center">
      <label>Si</label>
      [ <?= ($values["_0"][0] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
      &nbsp;&nbsp;
      <label>No</label>
      [ <?= ($values["_0"][0] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
      &nbsp;&nbsp;
      <label>Non Applicabile</label>
      [ <?= ($values["_0"][1] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
    </div><br>
    <? if ($values["_0"][0] == "true" || $show_empty) { ?>
      <? if ($show_empty) {
        ?>
        <strong>In caso affermativo:</strong><br>
        <strong>Rispondere compilando le altre parti di questa sezione, la sezione B e, ove pertinente, la sezione C della presente parte, la parte III, la parte V se applicabile, e in ogni caso compilare e firmare la parte VI.</strong><br><br>
        <?
      }
      ?>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              a) Indicare la denominazione dell'elenco o del certificato e, se pertinente, il pertinente numero di iscrizione o della certificazione:
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_0"][2]; ?>
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              b) Se il certificato di iscrizione o la certificazione &egrave; disponibile elettronicamente, indicare:
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_0"][3]; ?>
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              c) Indicare i riferimenti in base ai quali &egrave; stata ottenuta l'iscrizione o la certificazione e, se pertinente, la classificazione ricevuta nell'elenco ufficiale:
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_0"][4]; ?>
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              d) L'iscrizione o la certificazione comprende tutti i criteri di selezione richiesti?
            </th>
          </tr>
          <tr>
            <td>
              <label>Si</label>
              [ <?= ($values["_01"][0] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              &nbsp;&nbsp;
              <label>No</label>
              [ <?= ($values["_01"][0] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              <? if ($show_empty) {
                ?><br><br>
                <strong>In caso di risposta negativa alla lettera d):<br>
                Inserire inoltre tutte le informazioni mancanti nella parte IV, sezione A, B, C, o D secondo il caso</strong><br>
                <?
              }
              ?>
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              e) L'operatore economico potr&agrave; fornire un certificato per quanto riguarda il pagamento dei contributi previdenziali e delle imposte, o fornire informazioni che permettano all'amministrazione aggiudicatrice o all'ente aggiudicatore di acquisire tale documento direttamente accedendo a una banca dati nazionale che sia disponibile gratuitamente in un qualunque Stato membro?
            </th>
          </tr>
          <tr>
            <td>
              <? if ($show_empty) {
                ?><br>

              <strong>SOLO se richiesto dal pertinente avviso o bando o dai documenti di gara:</strong><br><br>
              <? } ?>
              <label>Si</label>
              [ <?= ($values["_1"][0] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              &nbsp;&nbsp;
              <label>No</label>
              [ <?= ($values["_1"][0] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              Se la documentazione pertinente &egrave; disponibile elettronicamente, indicare:
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_1"][1]; ?>
            </td>
          </tr>
        </table>
      <? }
  }
?>
