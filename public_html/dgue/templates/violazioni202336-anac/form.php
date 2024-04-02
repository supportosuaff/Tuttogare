<?
  if (isset($form)) {
        ?>
    <div style="text-align:center">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][cbc:ID][$]" value="<?= $form["sub_uuid"] ?>">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][cbc:ID][$]" value="974c8196-9d1c-419c-9ca9-45bb9f5fd59a">
      <label>Si</label>
      <input data-show="#criteria_<?= $form["codice"] ?>" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        value="true" <? if ($values["_0"][0] == "true") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
      <label>No</label>
      <input data-hide="#criteria_<?= $form["codice"] ?>" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        value="false" <? if ($values["_0"][0] == "false") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
    </div><br>
    <div id="criteria_<?= $form["codice"] ?>" <? if ($values["_0"][0] != "true") echo "style='display:none'" ?>>
      <table width="100%">
        <tr>
          <td class="etichetta">
            <label>Descrivi tali misure, nei casi di cui all’art 94 comma 5 lettere e) ed f) indicare fino a quando perdura l’iscrizione nel casellario ANAC</label>
          </td>
        </tr>
        <tr>
          <td style="text-align:center">
            <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][1][cbc:ID][$]" value="e098da8e-4717-4500-965f-f882d5b4e1ad">
            <textarea rel="N;1;0;A" title="Descrizione" class="dgue_input"
            name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][1][ccv:Response][cbc:Description]"><?= $values["_0"][1] ?></textarea>
          </td>
        </tr>
      </table>
      <table width="100%">
        <tr>
          <td class="etichetta">
            <label>L'operatore economico ha preso misure per dimostrare la propria affidabilità ("autodisciplina")?</label><br>
          </td>
        </tr>
        <tr>
          <td style="text-align:center">
            <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:RequirementGroup][cbc:ID][$]" value="41dd2e9b-1bfd-44c7-93ee-56bd74a4334b">
            <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:RequirementGroup][ccv:Requirement][0][cbc:ID][$]" value="20c5361b-7599-4ee6-b030-7f8323174d1e">
            <label>Si</label>
            <input data-show=".autodisciplina_<?= $form["codice"] ?>" type="radio"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
              value="true" <? if ($values["_01"][0] == "true") echo "checked='checked'" ?>
              onclick="show_hide($(this));">
            <label>No</label>
            <input data-hide=".autodisciplina_<?= $form["codice"] ?>" type="radio"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
              value="false" <? if ($values["_01"][0] == "false") echo "checked='checked'" ?>
              onclick="show_hide($(this));">
          </td>
        </tr>
      </table>
      <div class="autodisciplina_<?= $form["codice"] ?>" <? if ($values["_01"][0] != "true") echo "style='display:none'" ?>>
        <table width="100%">
          <tr>
            <td class="etichetta">
              <label>E' stato risarcito interamente il danno?</label><br>
            </td>
          </tr>
          <tr>
            <td style="text-align:center">
              <label>Si</label>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:RequirementGroup][ccv:Requirement][2][cbc:ID][$]" value="PERS_ID_SELFCLEAN_2">
              <input type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:RequirementGroup][ccv:Requirement][2][ccv:Response][ccv-cbc:Indicator]"
                value="true"
                class="form_autodisciplina_<?= $form["uuid"] ?>_<?= $id_repeat ?>"
                  <? if ($values["_01"][2] == "true") echo "checked='checked'" ?>>
              <label>No</label>
              <input type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:RequirementGroup][ccv:Requirement][2][ccv:Response][ccv-cbc:Indicator]"
                value="false"
                class="form_autodisciplina_<?= $form["uuid"] ?>_<?= $id_repeat ?>"
                  <? if ($values["_01"][2] == "false") echo "checked='checked'" ?>>
            </td>
          </tr>
        </table>
        <table width="100%">
          <tr>
            <td class="etichetta">
              <label>Si è impegnato formalmente a risarcire il danno?</label><br>
            </td>
          </tr>
          <tr>
            <td style="text-align:center">
              <label>Si</label>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:RequirementGroup][ccv:Requirement][3][cbc:ID][$]" value="PERS_ID_SELFCLEAN_3">
              <input type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:RequirementGroup][ccv:Requirement][3][ccv:Response][ccv-cbc:Indicator]"
                value="true"
                class="form_autodisciplina_<?= $form["uuid"] ?>_<?= $id_repeat ?>"
                <? if ($values["_01"][3] == "true") echo "checked='checked'" ?>>
              <label>No</label>
              <input type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:RequirementGroup][ccv:Requirement][3][ccv:Response][ccv-cbc:Indicator]"
                value="false"
                class="form_autodisciplina_<?= $form["uuid"] ?>_<?= $id_repeat ?>"
                <? if ($values["_01"][3] == "false") echo "checked='checked'" ?>>
            </td>
          </tr>
        </table>
        <table width="100%">
          <tr>
            <td class="etichetta">
              <label>Ha dichiarato i fatti e le circostanze in modo globale collaborando attivamente con le autorità investigative?</label><br>
            </td>
          </tr>
          <tr>
            <td style="text-align:center">
              <label>Si</label>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:RequirementGroup][ccv:Requirement][4][cbc:ID][$]" value="PERS_ID_SELFCLEAN_4">
              <input type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:RequirementGroup][ccv:Requirement][4][ccv:Response][ccv-cbc:Indicator]"
                value="true"
                class="form_autodisciplina_<?= $form["uuid"] ?>_<?= $id_repeat ?>"
                <? if ($values["_01"][4] == "true") echo "checked='checked'" ?>>
              <label>No</label>
              <input type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:RequirementGroup][ccv:Requirement][4][ccv:Response][ccv-cbc:Indicator]"
                value="false"
                class="form_autodisciplina_<?= $form["uuid"] ?>_<?= $id_repeat ?>"
                <? if ($values["_01"][4] == "false") echo "checked='checked'" ?>>
            </td>
          </tr>
        </table>
        <div class="border autodisciplina_<?= $form["codice"] ?>" <? if ($values["_01"][0] != "true") echo "style='display:none'" ?>>
          <table width="100%">
            <tr>
              <td class="etichetta">
                <label>Indicare le misure di carattere tecnico o organizzativo e relative al personale, eventualmente adottate, idonee a prevenire ulteriori illeciti o reati, la relativa documentazione e se disponibile elettronicamente indicare (indirizzo web, autorit&agrave; o organismo di emanazione, riferimento preciso della documentazione):</label><br>
              </td>
            </tr>
            <tr>
              <td style="text-align:center">
                <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:RequirementGroup][ccv:Requirement][1][cbc:ID][$]" value="7b07904f-e080-401a-a3a1-9a3efeeda54b">
                <textarea rel="N;1;0;A" title="Autodisciplina" class="dgue_input "
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:RequirementGroup][ccv:Requirement][1][ccv:Response][cbc:Description]"><?= $values["_01"][1] ?></textarea>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <?
  }
?>
