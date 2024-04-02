<?
  if (isset($form)) {
    ?>
    <div style="text-align:center">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][cbc:ID][$]" value="<?= $form["sub_uuid"] ?>">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][cbc:ID][$]" value="974c8196-9d1c-419c-9ca9-45bb9f5fd59a">
      <label>Si</label>
      <input data-show="#criteria_<?= $form["codice"] ?>" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        value="true" <? if ($values["_0"][0] == "true") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
      <label>No</label>
      <input data-hide="#criteria_<?= $form["codice"] ?>" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        value="false" <? if ($values["_0"][0] == "false") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
    </div><br>
    <div id="criteria_<?= $form["codice"] ?>" <? if ($values["_0"][0] != "true") echo "style='display:none'" ?>>
        <table width="100%">
          <tr>
            <td class="etichetta">
              Descrivi tali misure
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][1][cbc:ID][$]" value="e098da8e-4717-4500-965f-f882d5b4e1ad">
              <textarea rel="N;1;0;A" title="Descrizione" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][1][ccv:Response][cbc:Description]"><?= $values["_0"][1] ?></textarea>
            </td>
          </tr>
        </table>
        <table width="100%">
          <tr>
            <td class="etichetta">
              Si &egrave; stati autorizzati dal giudice delegato ai sensi dell'articolo 110, comma 3, lett. a) del Codice?
            </td>
            <td>
              <input type="hidden" class="form_violazioni_<?= $form["uuid"] ?>" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][3][cbc:ID][$]" value="PERS_VIOLAZIONI_ELECTRONIC_3">
              <label>Si</label>
              <input type="radio" class="form_violazioni_<?= $form["uuid"] ?>"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][3][ccv:Response][ccv-cbc:Indicator]"
                value="true" <? if ($values["_0"][3] == "true") echo "checked='checked'" ?>>
              <label>No</label>
              <input type="radio" class="form_violazioni_<?= $form["uuid"] ?>"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][3][ccv:Response][ccv-cbc:Indicator]"
                value="false" <? if ($values["_0"][3] == "false") echo "checked='checked'" ?>>
              </td>
          </tr>
        </table>
        <table width="100%">
          <tr>
            <td class="etichetta">
              La partecipazione alla procedura di affidamento &egrave; subordinata ai sensi dell'art. 110, comma 5, all'avvalimento di altro operatore econimico?
            </td>
            <td>
              <input type="hidden" class="form_violazioni_<?= $form["uuid"] ?>" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][4][cbc:ID][$]" value="PERS_VIOLAZIONI_ELECTRONIC_4">
              <label>Si</label>
              <input type="radio" class="form_violazioni_<?= $form["uuid"] ?>" data-show="#avvalimento_<?= $form["codice"] ?>"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][4][ccv:Response][ccv-cbc:Indicator]"
                value="true" <? if ($values["_0"][4] == "true") echo "checked='checked'" ?>
                onclick="show_hide($(this));">
              <label>No</label>
              <input type="radio" class="form_violazioni_<?= $form["uuid"] ?>" data-hide="#avvalimento_<?= $form["codice"] ?>"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][4][ccv:Response][ccv-cbc:Indicator]"
                value="false" <? if ($values["_0"][4] == "false") echo "checked='checked'" ?>
                onclick="show_hide($(this));">
              </td>
          </tr>
          <tr id="avvalimento_<?= $form["codice"] ?>" <? if ($values["_0"][4] != "true") echo "style='display:none'" ?>>
            <td class="etichetta">
              Indicare l'impresa ausiliaria
            </td>
            <td>
              <input class="form_violazioni_<?= $form["uuid"] ?>" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][5][cbc:ID][$]" value="PERS_VIOLAZIONI_ELECTRONIC_5">
              <input class="dgue_input form_violazioni_<?= $form["uuid"] ?>" type="text" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][5][ccv:Response][cbc:Description]" value="<?= $values["_0"][5] ?>">
            </td>
          </tr>
        </table>
        <table width="100%">
          <tr>
            <td class="etichetta">
              Indicare perché l'operatore economico sar&agrave; comunque in grado di eseguire il contratto
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][2][cbc:ID][$]" value="4e3f468a-86c4-4c99-bd15-c8b221229348">
              <textarea rel="N;1;0;A" title="Descrizione" class="dgue_input" id="testo_violazioni_<?= $form["uuid"] ?>"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][2][ccv:Response][cbc:Description]"><?= $values["_0"][2] ?></textarea>
            </td>
          </tr>
        </table>
      </div>
      <table width="100%">
        <tr>
          <td colspan="2" class="etichetta" style="text-align:center">
            Queste informazioni sono disponibili elettronicamente?<br>
            <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][cbc:ID][$]" value="7458d42a-e581-4640-9283-34ceb3ad4345">
            <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][0][cbc:ID][$]" value="c1347b74-1872-4060-a6db-f4044edcd7c4">
            <label>Si</label>
            <input data-show=".electronic_reference_<?= $form["codice"] ?>" type="radio"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
              value="true" <? if ($values["_1"][0] == "true") echo "checked='checked'" ?>
              onclick="show_hide($(this));">
            <label>No</label>
            <input data-hide=".electronic_reference_<?= $form["codice"] ?>" type="radio"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
              value="false" <? if ($values["_1"][0] == "false") echo "checked='checked'" ?>
              onclick="show_hide($(this));">
          </td>
        </tr>
        <tr class="electronic_reference_<?= $form["codice"] ?>" <? if ($values["_1"][0] != "true") echo "style='display:none'" ?>>
          <td>URL</td>
          <td>Codice</td>
        </tr>
        <tr class="electronic_reference_<?= $form["codice"] ?>" <? if ($values["_1"][0] != "true") echo "style='display:none'" ?>>
          <td>
            <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][1][cbc:ID][$]" value="f4313bb6-21b6-499e-bdff-debe10e11d2c">
            <input type="text" rel="N;1;0;L" title="URL" class="dgue_input"
            name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][1][ccv:Response][cev:Evidence][cev:EvidenceDocumentReference][cac:Attachment][cac:ExternalReference][cbc:URI]"
            value="<?= $values["_1"][1]; ?>">
          </td>
          <td>
            <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][2][cbc:ID][$]" value="1f1cd18e-3e01-4ca2-af4c-e2981924ba8d">
            <input type="text" rel="N;1;0;A" title="Codice" class="dgue_input"
            name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][2][ccv:Response][ccv-cbc:Code]"
            value="<?= $values["_1"][2]; ?>">
          </td>
        </tr>
      </table>
      <script>
        $(".form_violazioni_<?= $form["uuid"] ?>").change(function(){
          data = $(".form_violazioni_<?= $form["uuid"] ?>").serialize();
          $.ajax({
            url: '/dgue/templates/violazioni_electronic/elabora.php',
            type: 'POST',
            data: data,
            dataType: 'html'
          }).done(function(response) {
            $("#testo_violazioni_<?= $form["uuid"] ?>").val(response);
          });
        });
      </script>
    <?
  }
?>
