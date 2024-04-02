<?
  if (isset($form)) {
        ?>
    <div style="text-align:center">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][cbc:ID][$]" value="<?= $form["sub_uuid"] ?>">
      <input class="bancarotta_<?= $form["codice"] ?>" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][cbc:ID][$]" value="974c8196-9d1c-419c-9ca9-45bb9f5fd59a">
      <label>Si</label>
      <input data-show="#criteria_<?= $form["codice"] ?>" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        class="bancarotta_<?= $form["codice"] ?>" value="true" <? if ($values["_0"][0] == "true") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
      <label>No</label>
      <input data-hide="#criteria_<?= $form["codice"] ?>" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        class="bancarotta_<?= $form["codice"] ?>" value="false" <? if ($values["_0"][0] == "false") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
    </div><br>
    <div id="criteria_<?= $form["codice"] ?>" <? if ($values["_0"][0] != "true") echo "style='display:none'" ?>>
      <table width="100%">
        <tr>
          <td class="etichetta">
            <label>si applica quanto previsto dell’art.95 del Codice della Crisi di Impresa (D.lgs 14/2019)?</label>
          </td>
        </tr>
        <tr>
          <td style="text-align:center">
            <input class="bancarotta_<?= $form["codice"] ?>" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][2][cbc:ID][$]" value="PERS_ID_BANKRUPTCY_1">
            <label>Si</label>
            <input data-show="#criteria_<?= $form["codice"] ?>_crisi_impresa" class="bancarotta_<?= $form["codice"] ?>" type="radio"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][2][ccv:Response][ccv-cbc:Indicator]"
              value="true" <? if ($values["_0"][2] == "true") echo "checked='checked'" ?>
              onclick="show_hide($(this));">
            <label>No</label>
            <input data-hide="#criteria_<?= $form["codice"] ?>_crisi_impresa" class="bancarotta_<?= $form["codice"] ?>" type="radio"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][2][ccv:Response][ccv-cbc:Indicator]"
              value="false" <? if ($values["_0"][2] == "false") echo "checked='checked'" ?>
              onclick="show_hide($(this));">
          </td>
        </tr>
      </table>
      <table width="100%" id="criteria_<?= $form["codice"] ?>_crisi_impresa" style="<?= ($values["_0"][2] == "true") ? "":"display:none"; ?>">
        <tr>
          <td class="etichetta">
            <label>Indicare il provvedimento autorizzativo</label>
          </td>
        </tr>
        <tr>
          <td>
            <input class="bancarotta_<?= $form["codice"] ?>" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][3][cbc:ID][$]" value="PERS_ID_BANKRUPTCY_2">
            <textarea rel="N;1;0;A" title="Descrizione" class="bancarotta_<?= $form["codice"] ?> dgue_input"
            name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][3][ccv:Response][cbc:Description]"><?= $values["_0"][3] ?></textarea>
          </td>
        </tr>
      </table>
      <table width="100%">
        <tr>
          <td class="etichetta">
            <label>Riepilogo generato automaticamente dal sistema</label>
          </td>
        </tr>
        <tr>
          <td>
            <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][1][cbc:ID][$]" value="e098da8e-4717-4500-965f-f882d5b4e1ad">
            <textarea rel="N;1;0;A" title="Descrizione" id="elenco_bancarotta_<?= $form["codice"] ?>" readonly class="dgue_input"
            name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][1][ccv:Response][cbc:Description]"><?= $values["_0"][1] ?></textarea>
          </td>
        </tr>
      </table>
    </div>
    <script>
      $(".bancarotta_<?= $form["codice"] ?>").change(function(){
        data = $(".bancarotta_<?= $form["codice"] ?>").serialize();
        $.ajax({
          url: '/dgue/templates/bancarotta/elabora.php',
          type: 'POST',
          data: data,
          dataType: 'html'
        }).done(function(response) {
          $("#elenco_bancarotta_<?= $form["codice"] ?>").val(response);
        });
      });
    </script>
    <?
  }
?>
