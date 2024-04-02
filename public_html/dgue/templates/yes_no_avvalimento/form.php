<?
  if (isset($form)) {
    if (!empty($form["sub_uuid"])) {

      ?>
      <div style="text-align:center">
        <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][cbc:ID][$]" value="<?= $form["sub_uuid"] ?>">
        <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][cbc:ID][$]" value="7f18c64e-ae09-4646-9400-f3666d50af51">
        <label>Si</label>
        <input data-show="#criteria_<?= $form["codice"] ?>" type="radio" data-show="#criteria_<?= $form["codice"] ?>"
          name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
          value="true" <? if ($values["_0"][0] == "true") echo "checked='checked'" ?>
          onclick="show_hide($(this));">
        <label>No</label>
        <input data-hide="#criteria_<?= $form["codice"] ?>" type="radio" data-hide="#criteria_<?= $form["codice"] ?>"
          name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
          value="false" <? if ($values["_0"][0] == "false") echo "checked='checked'" ?>
          onclick="show_hide($(this));">
      </div><br>
      <div id="criteria_<?= $form["codice"] ?>" <? if ($values["_0"][0] != "true") echo "style='display:none'" ?>>
            <table width="100%">
              <tr>
                <td class="etichetta">
                  Indicare la denominazione degli operatori economici di cui si intende avvalersi:
                </td>
                <td>
                  <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][1][cbc:ID][$]" value="PERS_ID_AVVALIMENTO_1">
                  <textarea rel="N;1;0;A" title="Descrizione" class="dgue_input"
                  name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][1][ccv:Response][cbc:Description]"><?= $values["_0"][1] ?></textarea>
                </td>
              </tr>
              <tr>
                <td class="etichetta">
                  Indicare i requisiti oggetto di avvalimento:
                </td>
                <td>
                  <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][2][cbc:ID][$]" value="PERS_ID_AVVALIMENTO_2">
                  <textarea rel="N;1;0;A" title="Descrizione" class="dgue_input"
                  name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][2][ccv:Response][cbc:Description]"><?= $values["_0"][2] ?></textarea>
                </td>
              </tr>
            </table>
        <div class="errore padding" style="text-align:center"><strong>Presentare per ciascuna impresa ausiliaria un DGUE distinto, debitamente compilato e firmato dai soggetti interessati, con le informazioni richieste dalle sezioni A e B della presente parte, dalla parte III, dalla parte IV ove pertinente e dalla parte VI.<br><br>
Si noti che dovrebbero essere indicati anche i tecnici o gli organismi tecnici che non facciano parte integrante dell'operatore economico, in particolare quelli responsabili del controllo della qualit&agrave; e, per gli appalti pubblici di lavori, quelli di cui l'operatore economico disporr&agrave; per l'esecuzione dell'opera.</strong></div>
      </div>
      <?
    } else {
      ?>
      <div class="errore padding" style="text-align:center">
        <strong>ERRORE DI CONFIGURAZIONE.</strong>
      </div>
      <?
    }
  }
?>
