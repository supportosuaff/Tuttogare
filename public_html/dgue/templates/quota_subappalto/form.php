<?
  if (isset($form)) {
    ?>

      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][cbc:ID][$]" value="575f7550-8a2d-4bad-b9d8-be07ab570076">

        <table width="100%">
          <tr>
            <td class="etichetta">
             Specificare
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][cbc:ID][$]" value="3aaca389-4a7b-406b-a4b9-080845d127e7">
              <textarea rel="N;1;0;A" title="Descrizione" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][ccv:Response][cbc:Description]"><?= $values["_0"][0] ?></textarea>
            </td>
          </tr>
        </table>
    <?
  }
?>
