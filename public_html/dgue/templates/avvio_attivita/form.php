<?
  if (isset($form)) {
    ?>
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][cbc:ID][$]" value="e9aa7763-c167-4352-8060-1a3d7d3e2662">
        <table width="100%">
          <tr>
            <td class="etichetta">
              Specificare
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][cbc:ID][$]" value="a18b2c98-8552-45ca-9751-d4c94c05847a">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][ccv:Response][cbc:Quantity][@unitCode]" value="YEAR">
              <select rel="N;1;0;A" title="Avvio attivit&agrave;" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][ccv:Response][cbc:Quantity][$]"
               class="dgue_input"
               id="espd_ccv_Criterion_<?= $form["uuid"] ?>_ccv_RequirementGroup_ccv_Requirement_ccv_Response_cbc_Quantity">
                <option value="">---</option>
                  <?
                    for ($year = (date("Y")-1);$year >= 2011;$year--) { ?>
                      <option><?= $year ?></option>
                    <? }
                  ?>
                </select>
                <? if (!empty($values["_0"][0])) {
                  ?>
                  <script>
                    $("#espd_ccv_Criterion_<?= $form["uuid"] ?>_ccv_RequirementGroup_ccv_Requirement_ccv_Response_cbc_Quantity").val("<?= $values["_0"][0] ?>");
                  </script>
                  <?
                }
                ?>
            </td>
          </tr>
        </table>
    <?
  }
?>
