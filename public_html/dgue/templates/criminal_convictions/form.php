<?
  $exist = false;
  $to = 1;
  unset($valori);
  if (isset($_POST["id"]) && isset($_POST["target"])) {
    session_start();
		include("../../../../config.php");
		include_once($root."/inc/funzioni.php");
    include($root."/dgue/config.php");
    $json = json_decode(file_get_contents($root."/dgue/templates/criminal_convictions/definition.json"),true);
    $id_repeat = $_SESSION["id_repeat"] + 1;
    $sql_form = "SELECT * FROM b_dgue_settings WHERE uuid = :uuid AND version = '2016-50'";
    $ris_form = $pdo->bindAndExec($sql_form,array(":uuid"=>substr($_POST["target"],6)));
    if ($ris_form->rowCount() > 0) {
      $form = $ris_form->fetchAll(PDO::FETCH_ASSOC)[0];
    }
  } else {
    $exist = true;
  }

  if (isset($form)) {
    $valori[0] = findValues(@$dgue["ccv:Criterion"][$form["uuid"]],$json);
    if (!empty($dgue["ccv:Criterion"][$form["uuid"]][0])) {
      $i_element = 0;
      foreach($dgue["ccv:Criterion"][$form["uuid"]] AS $elemento) {
        $valori[$i_element] = findValues($elemento,$json);
        $i_element++;
      }
      $to = $i_element;
    }
    for ($count_element = 0;$count_element < $to;$count_element++) {
      if (!empty($valori[$count_element])) $values = $valori[$count_element];
      if ($exist) $id_repeat = $count_element;
      ?>
      <div id="element_<?= $form["uuid"] ?>_<?= $id_repeat ?>">
        <? if ($id_repeat > 0) echo "<hr style='border-bottom:3px solid #333'>"; ?>
        <div style="text-align:center">
          <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][cbc:ID][$]" value="<?= $form["sub_uuid"] ?>">
          <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:Requirement][0][cbc:ID][$]" value="974c8196-9d1c-419c-9ca9-45bb9f5fd59a">
          <label>Si</label>
          <input data-show="#criteria_<?= $form["codice"] ?>_<?= $id_repeat ?>" type="radio"
            name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
            value="true" <? if ($values["_0"][0] == "true") echo "checked='checked'" ?>
            onclick="show_hide($(this));">
          <label>No</label>
          <input data-hide="#criteria_<?= $form["codice"] ?>_<?= $id_repeat ?>" type="radio"
            name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
            value="false" <? if ($values["_0"][0] == "false") echo "checked='checked'" ?>
            onclick="show_hide($(this));">
        </div><br>
        <div id="criteria_<?= $form["codice"] ?>_<?= $id_repeat ?>" <? if ($values["_0"][0] != "true") echo "style='display:none'" ?>>
            <table width="100%">
              <tr>
                <td class="etichetta">
                  Data della condanna
                </td>
                <td>
                  <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:Requirement][1][cbc:ID][$]" value="ecf40999-7b64-4e10-b960-7f8ff8674cf6">
                  <input type="text" class="datepick" rel="N;10;10;D" title="Data della condanna" class="dgue_input"
                  name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:Requirement][1][ccv:Response][cbc:Date]"
                  value="<?= mysql2date($values["_0"][1]); ?>">
                </td>
              </tr>
              <tr>
                <td class="etichetta">
                  Motivo
                </td>
                <td>
                  <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:Requirement][2][cbc:ID][$]" value="7d35fb7c-da5b-4830-b598-4f347a04dceb">
                  <textarea rel="N;1;0;A" title="Motivo condanna" class="dgue_input "
                  name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:Requirement][2][ccv:Response][cbc:Description]"><?= $values["_0"][2] ?></textarea>
                </td>
              </tr>
              <tr>
                <td class="etichetta">
                  Chi &egrave; stato condannato
                </td>
                <td>
                  <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:Requirement][3][cbc:ID][$]" value="c5012430-14da-454c-9d01-34cedc6a7ded">
                  <textarea rel="N;1;0;A" title="Condannato" class="dgue_input "
                  name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:Requirement][3][ccv:Response][cbc:Description]"><?= $values["_0"][3] ?></textarea>
                </td>
              </tr>
              <tr>
                <td class="etichetta">
                  Durata della condanna
                </td>
                <td>
                  <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:Requirement][4][cbc:ID][$]" value="9ca9096f-edd2-4f19-b6b1-b55c83a2d5c8">
                  <input type="text" rel="N;1;0;A" title="Durata della condanna" class="dgue_input"
                  name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:Requirement][4][ccv:Response][cac:Period][cbc:Description]"
                  value="<?= $values["_0"][4]; ?>">
                </td>
              </tr>
            </table>
            <table width="100%">
              <tr>
                <td colspan="2" class="etichetta" style="text-align:center">
                  L'operatore economico ha preso misure per dimostrare la propria affidabilit&agrave; ("autodisciplina")? <br>
                  <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][cbc:ID][$]" value="5f9f09f7-f701-432c-9fdc-c22c124a74c9">
                  <input class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][0][cbc:ID][$]" value="20c5361b-7599-4ee6-b030-7f8323174d1e">
                  <label>Si</label>
                  <input data-show=".autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>" type="radio"
                    name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                    value="true" <? if ($values["_01"][0] == "true") echo "checked='checked'" ?>
                    class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>" onclick="show_hide($(this));">
                  <label>No</label>
                  <input data-hide=".autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>" type="radio"
                    name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                    value="false" <? if ($values["_01"][0] == "false") echo "checked='checked'" ?>
                    class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>" onclick="show_hide($(this));">
                </td>
              </tr>
              <tr class="autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>" <? if ($values["_01"][0] != "true") echo "style='display:none'" ?>>
                <td>
                  <table width="100%">
                    <tr>
                      <td class="etichetta">
                        <strong>
                          1) La sentenza di condanna definitiva ha riconosciuto l'attenuante della collaborazione come definita dalle singole fattispecie di reato?
                        </strong>
                      </td>
                    </tr>
                    <tr>
                      <td style="text-align:center">
                        <label>Si</label>
                        <input class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][2][cbc:ID][$]" value="PERS_ID_SELFCLEAN_2">
                        <input type="radio"
                          name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][2][ccv:Response][ccv-cbc:Indicator]"
                          value="true"
                          class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>"
                           <? if ($values["_01"][2] == "true") echo "checked='checked'" ?>>
                        <label>No</label>
                        <input type="radio"
                          name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][2][ccv:Response][ccv-cbc:Indicator]"
                          value="false"
                          class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>"
                           <? if ($values["_01"][2] == "false") echo "checked='checked'" ?>>

                      </td>
                    </tr>
                    <tr>
                      <td class="etichetta">
                        <strong>
                          2) La sentenza definitiva di condanna prevede una pena detentiva non superiore a 18 mesi?
                        </strong>
                      </td>
                    </tr>
                    <tr>
                      <td style="text-align:center">
                        <label>Si</label>
                        <input class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][3][cbc:ID][$]" value="PERS_ID_SELFCLEAN_3">
                        <input type="radio"
                          name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][3][ccv:Response][ccv-cbc:Indicator]"
                          value="true"
                          class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>"
                           <? if ($values["_01"][3] == "true") echo "checked='checked'" ?>>
                        <label>No</label>
                        <input type="radio"
                          name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][3][ccv:Response][ccv-cbc:Indicator]"
                          value="false"
                          class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>"
                           <? if ($values["_01"][3] == "false") echo "checked='checked'" ?>>
                      </td>
                    </tr>
                    <tr>
                      <td class="etichetta">
                        <strong>
                          3) in caso di risposta affermativa per le ipotesi 1) e/o 2):
                        </strong>
                      </td>
                    </tr>
                    <tr>
                      <td style="text-align:center">
                      E' stato risarcito interamente il danno?<br>
                        <label>Si</label>
                        <input class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][4][cbc:ID][$]" value="PERS_ID_SELFCLEAN_4">
                        <input type="radio"
                          name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][4][ccv:Response][ccv-cbc:Indicator]"
                          value="true"
                          class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>"
                           <? if ($values["_01"][4] == "true") echo "checked='checked'" ?>>
                        <label>No</label>
                        <input type="radio"
                          name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][4][ccv:Response][ccv-cbc:Indicator]"
                          value="false"
                          class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>"
                           <? if ($values["_01"][4] == "false") echo "checked='checked'" ?>>
                           <br>
                         <br>Si &egrave; impegnato formalmente a risarcire il danno?<br>
                         <label>Si</label>
                         <input class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][5][cbc:ID][$]" value="PERS_ID_SELFCLEAN_5">
                         <input type="radio"
                           name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][5][ccv:Response][ccv-cbc:Indicator]"
                           value="true"
                           class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>"
                            <? if ($values["_01"][5] == "true") echo "checked='checked'" ?>>
                         <label>No</label>
                         <input type="radio"
                           name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][5][ccv:Response][ccv-cbc:Indicator]"
                           value="false"
                           class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>"
                            <? if ($values["_01"][5] == "false") echo "checked='checked'" ?>>
                            <br>
                      </td>
                    </tr>
                    <tr>
                      <td class="etichetta">
                        <strong>
                          4) per le ipotesi 1) e 2 l'operatore economico ha adottato misure di carattere tecnico o organizzativo e relativi al personale idonei a prevenire ulteriori illeciti o reati?
                        </strong>
                      </td>
                    </tr>
                    <tr>
                      <td style="text-align:center">
                        <label>Si</label>
                        <input class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][6][cbc:ID][$]" value="PERS_ID_SELFCLEAN_6">
                        <input data-show="#elenco_autodisciplina_<?= $form["uuid"] ?>_<?= $id_repeat ?>" onclick="show_hide($(this));" type="radio"
                          name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][6][ccv:Response][ccv-cbc:Indicator]"
                          value="true"
                          class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>"
                           <? if ($values["_01"][6] == "true") echo "checked='checked'" ?>>
                        <label>No</label>
                        <input type="radio" data-hide="#elenco_autodisciplina_<?= $form["uuid"] ?>_<?= $id_repeat ?>" onclick="show_hide($(this));"
                          name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][6][ccv:Response][ccv-cbc:Indicator]"
                          value="false"
                          class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>"
                           <? if ($values["_01"][6] == "false") echo "checked='checked'" ?>>
                           <br>
                          <input class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][7][cbc:ID][$]" value="PERS_ID_SELFCLEAN_7">
                           <textarea rel="N;1;0;A" title="Misure adottate" class="dgue_input form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>" id="elenco_autodisciplina_<?= $form["uuid"] ?>_<?= $id_repeat ?>"  <? if ($values["_01"][6] != "true") echo "style='display:none'" ?>
                          name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][7][ccv:Response][cbc:Description]"><?= $values["_01"][7] ?></textarea>
                      </td>
                    </tr>
                    <tr>
                      <td class="etichetta">
                        <strong>
                          5) se le sentenze di condanne sono state emesse nei confronti dei soggetti cessati di cui all'art. 80 comma 3, indicare le misure che dimostrano la completa ed effettiva dissociazione dalla condotta penalmente sanzionata:
                        </strong>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <input class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][8][cbc:ID][$]" value="PERS_ID_SELFCLEAN_8">
                        <textarea rel="N;1;0;A" title="Misure adottate soggetti cessati" class="dgue_input form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>"
                        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][8][ccv:Response][cbc:Description]"><?= $values["_01"][8] ?></textarea>
                      </td>
                    </tr>
                    <tr>
                      <td class="etichetta">
                        <strong>Riepilogo generato automaticamente dal sistema</strong>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:RequirementGroup][cbc:ID][$]" value="74e6c7b4-757b-4b40-ada6-fad6a997c310">
                        <input class="form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>" type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:RequirementGroup][ccv:Requirement][0][cbc:ID][$]" value="7b07904f-e080-401a-a3a1-9a3efeeda54b">
                         <textarea rel="N;1;0;A" title="Misure adottate" class="dgue_input" id="elenco_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>"
                        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][cbc:Description]"><?= $values["_01"][1] ?></textarea>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </div>
            <table width="100%">
              <tr>
                <td colspan="2" class="etichetta" style="text-align:center">
                  Queste informazioni sono disponibili elettronicamente?<br>
                  <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][1][cbc:ID][$]" value="7458d42a-e581-4640-9283-34ceb3ad4345">
                  <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][1][ccv:Requirement][0][cbc:ID][$]" value="c1347b74-1872-4060-a6db-f4044edcd7c4">
                  <label>Si</label>
                  <input data-show=".electronic_reference_<?= $form["codice"] ?>_<?= $id_repeat ?>" type="radio"
                    name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][1][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                    value="true" <? if ($values["_1"][0] == "true") echo "checked='checked'" ?>
                    onclick="show_hide($(this));">
                  <label>No</label>
                  <input data-hide=".electronic_reference_<?= $form["codice"] ?>_<?= $id_repeat ?>" type="radio"
                    name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][1][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                    value="false" <? if ($values["_1"][0] == "false") echo "checked='checked'" ?>
                    onclick="show_hide($(this));">
                </td>
              </tr>
              <tr class="electronic_reference_<?= $form["codice"] ?>_<?= $id_repeat ?>" <? if ($values["_1"][0] != "true") echo "style='display:none'" ?>>
                <td>URL</td>
                <td>Codice</td>
              </tr>
              <tr class="electronic_reference_<?= $form["codice"] ?>_<?= $id_repeat ?>" <? if ($values["_1"][0] != "true") echo "style='display:none'" ?>>
                <td>
                  <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][1][ccv:Requirement][1][cbc:ID][$]" value="f4313bb6-21b6-499e-bdff-debe10e11d2c">
                  <input type="text" rel="N;1;0;L" title="URL" class="dgue_input"
                  name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][1][ccv:Requirement][1][ccv:Response][cev:Evidence][cev:EvidenceDocumentReference][cac:Attachment][cac:ExternalReference][cbc:URI]"
                  value="<?= $values["_1"][1]; ?>">
                </td>
                <td>
                  <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][1][ccv:Requirement][2][cbc:ID][$]" value="1f1cd18e-3e01-4ca2-af4c-e2981924ba8d">
                  <input type="text" rel="N;1;0;A" title="Codice" class="dgue_input"
                  name="espd[ccv:Criterion][<?= $form["uuid"] ?>][<?= $id_repeat ?>][ccv:RequirementGroup][1][ccv:Requirement][2][ccv:Response][ccv-cbc:Code]"
                  value="<?= $values["_1"][2]; ?>">
                </td>
              </tr>
            </table>
            <? if ($id_repeat > 0) { ?>
              <button onClick="$('#element_<?= $form["uuid"] ?>_<?= $id_repeat ?>').remove(); return false;" class="submit_big" style="background-color:#C30" title="Elimina elemento">Elimina elemento</button>
            <? } ?><br>
          </div>
      <script>
        $(".form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>").change(function(){
          data = $(".form_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>").serialize();
          $.ajax({
            url: '/dgue/templates/criminal_convictions/elabora.php',
            type: 'POST',
            data: data,
            dataType: 'html'
          }).done(function(response) {
            $("#elenco_autodisciplina_<?= $form["codice"] ?>_<?= $id_repeat ?>").val(response);
          });
        });
      </script>
      <?
    }
  }
  $_SESSION["id_repeat"] = $id_repeat;
?>
