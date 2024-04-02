<? if (!empty($scelta_anomalia)) {  ?>
        <div class="box">
        <h2>Scelta procedura di calcolo anomalia</h2>
        <?

            if (!empty($lotto["algoritmo_anomalia"])) {
                if (empty($lotto["messaggio_anomalia"])) {
                    echo "<br>Algoritmo lettera: <strong>" . strtoupper($lotto["algoritmo_anomalia"]) . "</strong>" . ((!empty($lotto["coef_e"])) ? " - Coefficiente: <strong>" . $lotto["coef_e"] . "</strong>" : "");
                }
            } else if (!empty($record["algoritmo_anomalia"])) {
                if (empty($record["messaggio_anomalia"])) {
                    echo "<br>Algoritmo lettera: <strong>" . strtoupper($record["algoritmo_anomalia"]) . "</strong>" . ((!empty($record["coef_e"])) ? " - Coefficiente: <strong>" . $record["coef_e"] . "</strong>" : "");
                }
            } else {
        ?>
        <table width="100%">
            <tr>
                <td class="etichetta">Metodo</td>
                <td width="50%">
                    <select name="scelta_anomalia" class="parametri_anomalia" id="scelta_anomalia" rel="N;1;1;A" title="Procedura calcolo anomalia">
                        <option value="">Seleziona...</option>
                        <option value="z">Sorteggia automaticamente</option>
                        <option value="a">Art. 97 c.2 lett. a</option>
                        <option value="b">Art. 97 c.2 lett. b</option>
                        <option value="c">Art. 97 c.2 lett. c</option>
                        <option value="d">Art. 97 c.2 lett. d</option>
                        <option value="e">Art. 97 c.2 lett. e</option>
                    </select>
                </td>
                <td class="etichetta">Coefficente lett. e</td>
                <td>
                    <select name="coef_e" id="coef_e" class="parametri_anomalia" rel="N;1;3;A" title="Coefficente lett. e" disabled="disabled">
                        <option value="">Seleziona...</option>
                        <option value="z">Sorteggia automaticamente</option>
                        <?  if (strtotime($record["data_pubblicazione"]) < strtotime('2017-05-20')) { ?>
                            <option>0.6</option>
                            <option>0.8</option>
                            <option>1</option>
                            <option>1.2</option>
                            <option>1.4</option>
                        <? } else { ?>
                            <option>0.6</option>
                            <option>0.7</option>
                            <option>0.8</option>
                            <option>0.9</option>
                        <? } ?>
                    </select>
                </td>
            </tr>
        </table>
        <? } ?>
    </div>
    <script>
        $("#scelta_anomalia").change(function() {
            $("#coef_e").val('');
            if ($(this).val() == "e" || $(this).val() == "z") {
                $("#coef_e").removeAttr("disabled");
            } else {
                $("#coef_e").attr("disabled","disabled");
            }
            $("#coef_e").trigger('chosen:updated');
        });
    </script>
    <? 
    }
?>
