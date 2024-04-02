<?
  if (isset($form)) {
    global $soa;
    global $pdo;
    ?>
    <div style="text-align:center">
      Modalit&agrave; di dichiarazione<br>
      <label>S.O.A.</label>
      <input type="radio" name="soa[tipo]"
        data-show="#dichiarazioni_soa"
        data-hide="#dichiarazioni_alternative"
        value="soa" <? if (!empty($soa["tipo"]) && $soa["tipo"]=="soa") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
      <label>Alternativa</label>
      <input type="radio" name="soa[tipo]"
        data-hide="#dichiarazioni_soa"
        data-show="#dichiarazioni_alternative"
        value="alternativo" <? if (!empty($soa["tipo"]) && $soa["tipo"]=="alternativo") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
      <label>Non applicabile</label>
      <input type="radio" name="soa[tipo]"
        data-hide=".soa_detail"
        value="non_applicabile" <? if (!empty($soa["tipo"]) && $soa["tipo"]=="non_applicabile") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
    </div><br>
    <div class="soa_detail" id="dichiarazioni_soa" <? if (empty($soa["tipo"]) || $soa["tipo"]!="soa") echo "style='display:none'" ?>>
      <div id="corpo_soa">
        <?
          if (!empty($soa["certificati"]) && count($soa["certificati"]) > 0) {
            $id_repeat = 0;
            foreach ($soa["certificati"] as $certificato_soa) {
              include("tr.php");
              $id_repeat++;
            }
          }
        ?>
      </div>
      <button style="font-size:14px;"
        class="aggiungi"
        onClick="aggiungi('templates/soa/tr.php','#corpo_soa');f_ready();return false;">
          <img src="/img/add.png" alt="Aggiungi certificazione"> <strong>Aggiungi certificazione</strong>
        </button>
    </div>
    <div class="soa_detail" id="dichiarazioni_alternative" <? if (empty($soa["tipo"]) || $soa["tipo"]!="alternativo") echo "style='display:none'" ?>>
      <table width="100%">
        <tr>
          <td class="etichetta" style="width:auto;">
            Anno
          </td>
          <td class="etichetta" style="width:auto;">
            Importo lavori
          </td>
          <td class="etichetta" style="width:auto;">
            Costo personale
          </td>
        </tr>
        <?
        for ($i=0;$i<5;$i++) {
        ?>
        <tr>
          <td>
            <select rel="N;1;0;A" title="Anno" name="soa[dichiarazioni][<?= $i ?>][anno]"
             class="dgue_input"
             id="soa_dichiarazioni_<?= $i ?>_anno">
              <option value="">---</option>
                <?
                  for ($year = (date("Y")-1);$year >= 2011;$year--) { ?>
                    <option><?= $year ?></option>
                  <? }
                ?>
              </select>
              <? if (!empty($soa["dichiarazioni"][$i]["anno"])) {
                ?>
                <script>
                  $("#soa_dichiarazioni_<?= $i ?>_anno").val("<?= $soa["dichiarazioni"][$i]["anno"] ?>");
                </script>
                <?
              }
              ?>
          </td>
          <td>
            <input type="text" rel="N;1;0;N" title="Importo" class="dgue_input"
            name="soa[dichiarazioni][<?= $i ?>][lavori]"
            value="<?= (!empty($soa["dichiarazioni"][$i]["lavori"])) ? $soa["dichiarazioni"][$i]["lavori"] : ""; ?>">
            <select rel="N;1;0;A" title="Anno" name="soa[dichiarazioni][<?= $i ?>][valuta_lavori]"
             class="dgue_input"
             id="soa_dichiarazioni_<?= $i ?>_valuta_lavori">
              <option value="">---</option>
              <option value="EUR">EUR (Euro)</option>
              <option value="ALL">ALL (Albanian lek)</option>
              <option value="AMD">AMD (Armenian dram)</option>
              <option value="AZN">AZN (Azerbaijani manat)</option>
              <option value="BAM">BAM (Bosnian convertible mark)</option>
              <option value="BGN">BGN (Bulgarian lev)</option>
              <option value="BYR">BYR (Belarusian ruble)</option>
              <option value="CHF">CHF (Swiss franc)</option>
              <option value="CZK">CZK (Czech koruna)</option>
              <option value="DKK">DKK (Danish krone)</option>
              <option value="GBP">GBP (pound sterling)</option>
              <option value="GEL">GEL (Georgian lari)</option>
              <option value="HRK">HRK (Croatian kuna)</option>
              <option value="HUF">HUF (Hungarian forint)</option>
              <option value="ISK">ISK (Icelandic króna)</option>
              <option value="MDL">MDL (Moldovan krone)</option>
              <option value="PLN">PLN (Polish zloty)</option>
              <option value="RON">RON (New Romanian leu)</option>
              <option value="RSD">RSD (Serbian dinar)</option>
              <option value="RUB">RUB (Russian ruble)</option>
              <option value="SEK">SEK (Swedish krona)</option>
              <option value="TRY">TRY (Turkish lira)</option>
              <option value="UAH">UAH (Ukrainian hryvnia)</option>
              <option value="USD">USD (US dollar)</option>
            </select>
            <? if (!empty($soa["dichiarazioni"][$i]["valuta_lavori"])) {
              ?>
              <script>
                $("#soa_dichiarazioni_<?= $i ?>_valuta_lavori").val("<?= $soa["dichiarazioni"][$i]["valuta_lavori"] ?>");
              </script>
              <?
            }
            ?>
          </td>
          <td>
            <input type="text" rel="N;1;0;N" title="Importo" class="dgue_input"
            name="soa[dichiarazioni][<?= $i ?>][personale]"
            value="<?= (!empty($soa["dichiarazioni"][$i]["personale"])) ? $soa["dichiarazioni"][$i]["personale"] : ""; ?>">
            <select rel="N;1;0;A" title="Anno" name="soa[dichiarazioni][<?= $i ?>][valuta_personale]"
             class="dgue_input"
             id="soa_dichiarazioni_<?= $i ?>_valuta_personale">
              <option value="">---</option>
              <option value="EUR">EUR (Euro)</option>
              <option value="ALL">ALL (Albanian lek)</option>
              <option value="AMD">AMD (Armenian dram)</option>
              <option value="AZN">AZN (Azerbaijani manat)</option>
              <option value="BAM">BAM (Bosnian convertible mark)</option>
              <option value="BGN">BGN (Bulgarian lev)</option>
              <option value="BYR">BYR (Belarusian ruble)</option>
              <option value="CHF">CHF (Swiss franc)</option>
              <option value="CZK">CZK (Czech koruna)</option>
              <option value="DKK">DKK (Danish krone)</option>
              <option value="GBP">GBP (pound sterling)</option>
              <option value="GEL">GEL (Georgian lari)</option>
              <option value="HRK">HRK (Croatian kuna)</option>
              <option value="HUF">HUF (Hungarian forint)</option>
              <option value="ISK">ISK (Icelandic króna)</option>
              <option value="MDL">MDL (Moldovan krone)</option>
              <option value="PLN">PLN (Polish zloty)</option>
              <option value="RON">RON (New Romanian leu)</option>
              <option value="RSD">RSD (Serbian dinar)</option>
              <option value="RUB">RUB (Russian ruble)</option>
              <option value="SEK">SEK (Swedish krona)</option>
              <option value="TRY">TRY (Turkish lira)</option>
              <option value="UAH">UAH (Ukrainian hryvnia)</option>
              <option value="USD">USD (US dollar)</option>
            </select>
            <? if (!empty($soa["dichiarazioni"][$i]["valuta_personale"])) {
              ?>
              <script>
                $("#soa_dichiarazioni_<?= $i ?>_valuta_personale").val("<?= $soa["dichiarazioni"][$i]["valuta_personale"] ?>");
              </script>
              <?
            }
            ?>
          </td>
        </tr>
        <? } ?>
      </table>
    </div><br><br>
    <div style="text-align:center">
      Ai fine della partecipazione alla gara i requisiti dichiarati sono:<br>
      <label>Sufficienti</label>
      <input type="radio" name="soa[requisiti]"
        value="1" <? if (isset($soa["requisiti"]) && $soa["requisiti"]=="1") echo "checked='checked'" ?>><br>
      <label>Non sono adeguati, si far&agrave; ricorso ad avvalimento o RTI o subappalto necessario laddove legittimo</label>
      <input type="radio" name="soa[requisiti]"
        value="0" <? if (isset($soa["requisiti"]) && $soa["requisiti"]=="0") echo "checked='checked'" ?>>
    </div><br>
    <?
  }
?>
