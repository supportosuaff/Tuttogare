<?
  if (isset($form)) {
    ?>
    <div style="text-align:center">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][cbc:ID][$]" value="PERS_GROUP_<?= $form["uuid"] ?>">
      <input class="integrazioni_nazionali" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][cbc:ID][$]" value="PERS_GROUP_ART80_RESP_0">
      <label>Si</label>
      <input class="integrazioni_nazionali" data-show="#art80_<?= $form["codice"] ?>" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][cbc:Description]"
        value="true" <? if ($values["_0"][0] == "true") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
      <label>No</label>
      <input class="integrazioni_nazionali" data-hide="#art80_<?= $form["codice"] ?>" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][cbc:Description]"
        value="false" <? if ($values["_0"][0] == "false") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
        <? if ($form["uuid"] == "PERS_ART80_C5_i") { ?>
          <label>Non &egrave; tenuto alla disciplina legge 68/1999</label>
          <input class="integrazioni_nazionali" data-show="#art80_<?= $form["codice"] ?>" type="radio"
            name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][cbc:Description]"
            value="na" <? if ($values["_0"][0] == "na") echo "checked='checked'" ?>
            onclick="show_hide($(this));">
        <? } ?>
    </div><br>
    <div id="art80_<?= $form["codice"] ?>" <? if ($values["_0"][0] != "true" && $values["_0"][0] != "na") echo "style='display:none'" ?>>
        <table width="100%">
          <? if ($form["uuid"] == "PERS_ART80_C5_h") {
            ?>
            <tr>
              <td class="etichetta">
                Indicare la data dell'accertamento definitivo e l'autorit&agrave; o l'organismo di emanazione:
              </td>
              <td>
                <input class="integrazioni_nazionali" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][2][cbc:ID][$]" value="PERS_GROUP_ART80_RESP_2">
                <textarea rel="N;1;0;A" title="Descrizione" class="dgue_input integrazioni_nazionali"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][2][ccv:Response][cbc:Description]"><?= $values["_0"][2] ?></textarea>
              </td>
            </tr>
            <tr>
              <td class="etichetta">
                La violazione &egrave; stata rimossa?
              </td>
              <td>
                <input class="integrazioni_nazionali" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][3][cbc:ID][$]" value="PERS_GROUP_ART80_RESP_3">
                <label>Si</label>
                <input class="integrazioni_nazionali" type="radio"
                  name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][3][ccv:Response][ccv-cbc:Indicator]"
                  value="true" <? if ($values["_0"][3] == "true") echo "checked='checked'" ?>>
                <label>No</label>
                <input class="integrazioni_nazionali" type="radio"
                  name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][3][ccv:Response][ccv-cbc:Indicator]"
                  value="false" <? if ($values["_0"][3] == "false") echo "checked='checked'" ?>>
              </td>
            </tr>
            <?
          } ?>
          <tr>
            <td class="etichetta">
              Descrivi tali misure<? if ($form["uuid"] == "PERS_ART80_C5_i") { ?>, se del caso, le motivazioni per cui l'operatore non &egrave; tenuto alla disciplina legge 68/1999<? } ?> e se &egrave; disponibile elettronicamente la documentazione pertinente indicare (indirizzo web, autorit&agrave; o organismo di emanazione, riferimento preciso della documentazione):
            </td>
            <td>
              <input class="integrazioni_nazionali" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][1][cbc:ID][$]" value="PERS_GROUP_ART80_RESP_1">
              <textarea rel="N;1;0;A" title="Descrizione" class="dgue_input integrazioni_nazionali"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][1][ccv:Response][cbc:Description]"><?= $values["_0"][1] ?></textarea>
            </td>
          </tr>
        </table>
      </div>
    <?
  }
?>
