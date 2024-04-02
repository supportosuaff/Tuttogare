<?
  if (isset($form)) {
    ?>
    <div style="text-align:center">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][cbc:ID][$]" value="d5fe5a71-7fd3-4910-b6f4-5cd2a4d23524">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][cbc:ID][$]" value="7f18c64e-ae09-4646-9400-f3666d50af51">
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
              In caso affermativo, indicare i lavori o le parti di opere ovvero i servizi e le forniture o parti di servizi e forniture che si intende subappaltare
            </td>
          </tr>
          <tr>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][1][cbc:ID][$]" value="999c7fe2-61cd-4e86-b76f-e280304dc8c9">
              <textarea rel="N;1;0;A" title="Proposte subappalto" class="dgue_input" id="resume_subappalto"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][1][ccv:Response][cbc:Description]"
              ><?= $values["_0"][1]; ?></textarea>
            </td>
          </tr>
        </table>
      </div>
      <script>
        $(".subappaltatori").change(function(){
          data = $(".subappaltatori").serialize();
          $.ajax({
            url: '/dgue/templates/subappalto/elabora.php',
            type: 'POST',
            data: data,
            dataType: 'html'
          }).done(function(response) {
            $("#resume_subappalto").val(response);
          });
        });
      </script>
    <?
  }
?>
