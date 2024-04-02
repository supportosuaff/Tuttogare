<?
  if (isset($form)) {
    global $subappalto;
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
              In caso affermativo, elencare i subappaltatori proposti e le parti che si intende subappaltare:
            </td>
          </tr>
          <?
            for ($c_s=1;$c_s<=3;$c_s++) {
              ?>
              <tr>
                <td class="etichetta">Subappaltatore <?= $c_s ?></td>
              </tr>
              <tr>
                <td>
                  <table width="100%">
                    <tr>
                      <td>Ragione Sociale</td>
                      <td>
                        <input type="text" rel="N;0;0;A" name="subappalto[operatori][<?= $c_s ?>][ragione_sociale]" value="<?= (!empty($subappalto["operatori"][$c_s]["ragione_sociale"])) ? $subappalto["operatori"][$c_s]["ragione_sociale"] : "" ?>" style="width:95%" class="subappaltatori dgue_input">
                      </td>
                    </tr>
                    <tr>
                      <td>Partita IVA</td>
                      <td>
                        <input type="text" rel="N;11;0;CFPI" name="subappalto[operatori][<?= $c_s ?>][partita_iva]" value="<?= (!empty($subappalto["operatori"][$c_s]["partita_iva"])) ? $subappalto["operatori"][$c_s]["partita_iva"] : "" ?>" style="width:95%" class="subappaltatori dgue_input">
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <?
            }
          ?>
          <tr>
            <td class="etichetta">
              Elencare le prestazioni o lavorazioni che si intende subappaltare e la relativa quota (espressa in percentuale) sull'mporto contrattuale:
            </td>
          </tr>
          <tr>
            <td>
              <textarea rel="N;1;0;A" rel="N;0;0;A" title="Parti da subappaltare" class="dgue_input subappaltatori"
              name="subappalto[descrizione]"
              ><?= (!empty($subappalto["descrizione"])) ? $subappalto["descrizione"] : "" ?></textarea>
            </td>
          </tr>
          <tr>
            <td class="etichetta">
              Riepilogo:
              <div class="errore padding">
                <strong>Il completamento del campo &egrave; automatizzato in base compilazione delle altre voci della Subappalto</strong>
              </div>
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
            dataType: 'script'
          }).done(function() {
            $("#resume_subappalto").load('/dgue/templates/subappalto/testo_descrizione.php');
          });
        });
      </script>
    <?
  }
?>
