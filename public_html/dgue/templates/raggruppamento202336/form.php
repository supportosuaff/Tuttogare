<?
  if (isset($form)) {
    ?>
    <div style="text-align:center">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][cbc:ID][$]" value="d939f2c6-ba25-4dc4-889c-11d1853add19">
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
      <div class="errore padding">
        <strong>Accertarsi che gli altri operatori interessati forniscano un DGUE distinto.</strong>
      </div>
      <table width="100%">
        <tr>
          <td class="etichetta">
            <label>a) Specificare il ruolo dell'operatore economico nel raggruppamento, ovvero consorzio, GEIE, rete di impresa di cui all’art.65 comma 2 lettere e) f) g) h) e art.66 comma 1 lettere a) b) c) d) f) Codice (capofila, responsabile di compiti specifici,ecc.):</label>      
          </td>
        </tr>
        <tr>
          <td>
            <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][1][cbc:ID][$]" value="907fd62b-02f1-452c-81a8-785bedb0c536">
            <input type="text" rel="N;1;0;A" title="Ruolo operatore" class="dgue_input"
            name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][1][ccv:Response][cbc:Description]"
            value="<?= $values["_0"][1]; ?>">
          </td>
        </tr>
      </table>
      <table width="100%">
        <tr>
          <td class="etichetta">
            <label>b) Indicare gli altri operatori economici che compartecipano alla procedura di appalto:</label>
          </td>
        </tr>
        <tr>
          <td>
            <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][2][cbc:ID][$]" value="7c267f95-a3a7-49ef-abd9-e121dcd641a9">
            <input type="text" rel="N;1;0;A" title="Altri componenti" class="dgue_input"
            name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][2][ccv:Response][cbc:Description]"
            value="<?= $values["_0"][2]; ?>">
          </td>
        </tr>
      </table>
      <table width="100%">
        <tr>
          <td class="etichetta">
            <label>c) Se pertinente, indicare il nome del raggruppamento partecipante:</label>
          </td>
        </tr>
        <tr>
          <td>
            <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][3][cbc:ID][$]" value="96f38793-4469-4153-aba6-c613282cdbdc">
            <input type="text" rel="N;1;0;A" title="Nome Raggruppamento" class="dgue_input"
            name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][3][ccv:Response][cbc:Description]"
            value="<?= $values["_0"][3]; ?>">
          </td>
        </tr>
      </table>
      <table width="100%">
        <tr>
          <td class="etichetta">
            <label>d) Se pertinente, indicare la denominazione degli operatori economici facenti parte di un consorzio di cui all’art.65 comma 2 lettere b) d) o di una società di professionisti di cui all’art.66 comma 1 lettera g) che eseguono le prestazioni oggetto del contratto.</label>
          </td>
        </tr>
        <tr>
          <td>
            <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][4][cbc:ID][$]" value="PERS_ID_RAGGRUPPAMENTO_1">
            <input type="text" rel="N;1;0;A" title="Denominazione operatori facenti parte di un consorzio" class="dgue_input"
            name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][4][ccv:Response][cbc:Description]"
            value="<?= $values["_0"][4]; ?>">
          </td>
        </tr>
      </table>
      <table width="100%">
        <tr>
          <td class="etichetta">
            <label>e) Se pertinente, specificare le categorie di lavori o le parti del servizio o della fornitura che saranno eseguite dai singoli operatori riuniti o consorziati (art.68 comma 2 del Codice)</label>
          </td>
        </tr>
        <tr>
          <td>
            <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][5][cbc:ID][$]" value="PERS_ID_RAGGRUPPAMENTO_2">
            <input type="text" rel="N;1;0;A" title="Specifica categorie di lavori" class="dgue_input"
            name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][5][ccv:Response][cbc:Description]"
            value="<?= $values["_0"][5]; ?>">
          </td>
        </tr>
      </table>
    </div>
    <?
  }
?>
