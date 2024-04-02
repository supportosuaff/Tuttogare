<?
  if (isset($form)) {
    ?>
    <div style="text-align:center">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][cbc:ID][$]" value="64162276-7014-408f-a9af-080426bfe1fd">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][cbc:ID][$]" value="7f18c64e-ae09-4646-9400-f3666d50af51">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][1][cbc:ID][$]" value="67fd1dde-2a0a-486e-9469-79c78796fc22">
      <label>Si</label>
      <input data-show=".criteria_<?= $form["codice"] ?>_true" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        value="true" <? if ($values["_0"][0] == "true") echo "checked='checked'" ?>
        onclick="$('.notApp_<?= $form["codice"] ?>').prop('checked',false); show_hide($(this));" class="radio_<?= $form["codice"] ?>">
      <label>No</label>
      <input data-hide=".criteria_<?= $form["codice"] ?>_true" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        value="false" <? if ($values["_0"][0] == "false") echo "checked='checked'" ?>
        onclick="$('.notApp_<?= $form["codice"] ?>').prop('checked',false); show_hide($(this));" class="radio_<?= $form["codice"] ?>">
      <label>Non applicabile</label>
      <input data-hide=".criteria_<?= $form["codice"] ?>" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][1][ccv:Response][ccv-cbc:Indicator]"
        value="true" <? if ($values["_0"][1] == "true") echo "checked='checked'" ?>
        onclick="$('.radio_<?= $form["codice"] ?>').prop('checked',false); show_hide($(this));" class="notApp_<?= $form["codice"] ?>">
    </div><br>
    <div class="criteria_<?= $form["codice"] ?> criteria_<?= $form["codice"] ?>_true" <? if ($values["_0"][0] != "true") echo "style='display:none'" ?>>
      <div class="errore padding">
        <strong>Rispondere compilando le altre parti di questa sezione, la sezione B e, ove pertinente, la sezione C della presente parte, la parte III, la parte V se applicabile, e in ogni caso compilare e firmare la parte VI.</strong>
      </div>
        <table width="100%">
          <tr>
            <td class="etichetta">
              a) Indicare la denominazione dell'elenco o del certificato e, se pertinente, il pertinente numero di iscrizione o della certificazione:
            </td>
          </tr>
          <tr>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][2][cbc:ID][$]" value="30064ad3-fc11-4579-8528-fdd0b9a5ba75">
              <input type="text" rel="N;1;0;A" title="Numero di iscrizione o certificazione" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][2][ccv:Response][cbc:Description]"
              value="<?= $values["_0"][2]; ?>">
            </td>
          </tr>
          <tr>
            <td class="etichetta">
              b) Se il certificato di iscrizione o la certificazione &egrave; disponibile elettronicamente, indicare:
            </td>
          </tr>
          <tr>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][3][cbc:ID][$]" value="b3403349-cbc0-4d84-879e-fc0f2d90ecbd">
              <input type="text" rel="N;1;0;A" title="Certificato disponibile elettronicamente" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][3][ccv:Response][cbc:Description]"
              value="<?= $values["_0"][3]; ?>">
            </td>
          </tr>
          <tr>
            <td class="etichetta">
              c) Indicare i riferimenti in base ai quali &egrave; stata ottenuta l'iscrizione o la certificazione e, se pertinente, la classificazione ricevuta nell'elenco ufficiale:
            </td>
          </tr>
          <tr>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][4][cbc:ID][$]" value="792ff522-6f3f-4a62-ab6e-a8b272bc290e">
              <input type="text" rel="N;1;0;A" title="Riferimenti iscrizione" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][4][ccv:Response][cbc:Description]"
              value="<?= $values["_0"][4]; ?>">
            </td>
          </tr>
          <tr>
            <td class="etichetta">
              d) L'iscrizione o la certificazione comprende tutti i criteri di selezione richiesti?
            </td>
          </tr>
          <tr>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][cbc:ID][$]" value="92e44d3b-af8e-4a29-91a8-24d27aa27fee">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][cbc:ID][$]" value="d9996ef5-49f9-4cf8-a2f5-31c9f4efd894">
              <label>Si</label>
              <input type="radio" data-hide=".criteria_<?= $form["codice"] ?>_alert"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][ccv:Response][ccv-cbc:Indicator]"
                value="true" <? if ($values["_01"][0] == "true") echo "checked='checked'" ?> onclick="show_hide($(this));">
              <label>No</label>
              <input type="radio" data-show=".criteria_<?= $form["codice"] ?>_alert"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][ccv:Response][ccv-cbc:Indicator]"
                value="false" <? if ($values["_01"][0] == "false") echo "checked='checked'" ?> onclick="show_hide($(this));">
                <div class="errore padding criteria_<?= $form["codice"] ?>_alert" <? if ($values["_01"][0] != "false") echo "style='display:none'" ?>>
                  <strong>Inserire inoltre tutte le informazioni mancanti nella parte IV, sezione A, B, C, o D secondo il caso</strong>
                </div>
            </td>
          </tr>
          <tr>
            <td class="etichetta">
              e) L'operatore economico potr&agrave; fornire un certificato per quanto riguarda il pagamento dei contributi previdenziali e delle imposte, o fornire informazioni che permettano all'amministrazione aggiudicatrice o all'ente aggiudicatore di acquisire tale documento direttamente accedendo a una banca dati nazionale che sia disponibile gratuitamente in un qualunque Stato membro?
            </td>
          </tr>
          <tr>
            <td>
              <div class="errore padding">
                <strong>SOLO se richiesto dal pertinente avviso o bando o dai documenti di gara:</strong>
              </div>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][cbc:ID][$]" value="59e6f3ef-15cd-4e21-82ac-ea497ccd44e2">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][0][cbc:ID][$]" value="0e71abd3-198e-49c5-8128-5708617bb191">
              <label>Si</label>
              <input type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                value="true" <? if ($values["_1"][0] == "true") echo "checked='checked'" ?>>
              <label>No</label>
              <input type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                value="false" <? if ($values["_1"][0] == "false") echo "checked='checked'" ?>>
            </td>
          </tr>
        </table>
        <table width="100%">
          <tr>
            <td class="etichetta">
              Se la documentazione pertinente &egrave; disponibile elettronicamente, indicare:
            </td>
          </tr>
          <tr>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][1][cbc:ID][$]" value="caa72cea-5443-49fb-84ba-ab6c64427f77">
              <input type="text" rel="N;1;0;A" title="Documentazione disponibile elettronicamente" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][1][ccv:Response][cbc:Description]"
              value="<?= $values["_1"][1]; ?>">
            </td>
          </tr>
        </table>
      </div>
    <?
  }
?>
