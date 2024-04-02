<?
  if (isset($form)) {

    ?>
    <div style="text-align:center">
      <div class="errore padding">
        <strong>Il completamento del requisito &egrave; subordinato alla compilazione delle altre voci della sezione D</strong>
      </div>
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][cbc:ID][$]" value="<?= $form["sub_uuid"] ?>">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][cbc:ID][$]" value="974c8196-9d1c-419c-9ca9-45bb9f5fd59a">
      <input type="hidden"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        value="<?= $values["_0"][0] ?>" id="indicator_national"><br>
      <strong id="label_national"><?
        if ($values["_0"][0] === "false") echo "No";
        if ($values["_0"][0] === "true") echo "Si";
       ?></strong>
    </div><br>
    <div id="criteria_national" <? if ($values["_0"][0] != "true") echo "style='display:none'" ?>>
        <table width="100%">
          <tr>
            <td class="etichetta">
              Descrivi tali misure
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][1][cbc:ID][$]" value="e098da8e-4717-4500-965f-f882d5b4e1ad">
              <textarea rel="N;1;0;A" title="Descrizione" style="width:100%; height:400px" id="descrizione_national"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][1][ccv:Response][cbc:Description]"><?= $values["_0"][1] ?></textarea>
              <div class="errore padding">
                <strong>La descrizione &egrave; compilata automaticamente dal sistema, si prega di verificarne il contenuto</strong>
              </div>
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
        $(".integrazioni_nazionali").change(function(){
          data = $(".integrazioni_nazionali").serialize();
          $.ajax({
            url: '/dgue/templates/violazioni_nazionali202336/elabora.php',
            type: 'POST',
            data: data,
            dataType: 'script'
          }).done(function() {
            $("#descrizione_national").load('/dgue/templates/violazioni_nazionali202336/testo_descrizione.php');
          });
        });
      </script>
    <?
  }
?>
