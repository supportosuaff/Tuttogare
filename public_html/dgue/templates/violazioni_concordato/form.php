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
              &Egrave; stato emesso il decreto di ammissione al concordato preventivo di cui all'art. 47 D.Lgs 14/2019, come previsto dall’art. 110 comma 5?
            </td>
            <td>
              <input type="hidden" class="form_violazioni_<?= $form["uuid"] ?>" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][1][cbc:ID][$]" value="PERS_VIOLAZIONI_CONCORDATO_1">
              <label>Si</label>
              <input type="radio" class="form_violazioni_<?= $form["uuid"] ?>"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][1][ccv:Response][ccv-cbc:Indicator]"
                value="true" <? if ($values["_0"][1] == "true") echo "checked='checked'" ?>>
              <label>No</label>
              <input type="radio" class="form_violazioni_<?= $form["uuid"] ?>"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][1][ccv:Response][ccv-cbc:Indicator]"
                value="false" <? if ($values["_0"][1] == "false") echo "checked='checked'" ?>>
              </td>
          </tr>
        </table>
        <table width="100%">
        <tr>
            <td class="etichetta">
            La partecipazione alla procedura di affidamento &egrave; subordinata ai requisiti di cui all'art. 110, comma 6, nonostante sia stato emesso il decreto di ammissione al concordato preventivo di cui all'art. 47 D.Lgs 14/2019?
            </td>
            <td>
              <input type="hidden" class="form_violazioni_<?= $form["uuid"] ?>" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][2][cbc:ID][$]" value="PERS_VIOLAZIONI_CONCORDATO_2">
              <label>Si</label>
              <input type="radio" class="form_violazioni_<?= $form["uuid"] ?>"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][2][ccv:Response][ccv-cbc:Indicator]"
                value="true" <? if ($values["_0"][2] == "true") echo "checked='checked'" ?>>
              <label>No</label>
              <input type="radio" class="form_violazioni_<?= $form["uuid"] ?>"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][2][ccv:Response][ccv-cbc:Indicator]"
                value="false" <? if ($values["_0"][2] == "false") echo "checked='checked'" ?>>
              </td>
          </tr>
        </table>
        <table width="100%">
        <tr>
            <td class="etichetta">
            La partecipazione alla procedura di affidamento &egrave; subordinata all'avvalimento dei requisiti di un altro soggetto in quanto non ancora depositato il decreto di ammissione al concordato preventivo cui all’art. 47 D.Lgs 14/2019, come previsto dall'art. 110 comma 4?
            </td>
            <td>
              <input type="hidden" class="form_violazioni_<?= $form["uuid"] ?>" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][3][cbc:ID][$]" value="PERS_VIOLAZIONI_CONCORDATO_3">
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
