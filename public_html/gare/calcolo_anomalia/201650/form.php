<?
    if (isset($record)) {
        ?>
        <form name="box" method="post" action="save.php" rel="validate">
            <input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
            <input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
            <div class="comandi">
                <button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
            </div>
            <table width="100%">
                <tr>
                    <td class="etichetta">Metodo</td>
                    <td>
                        <select name="scelta_anomalia" id="scelta_anomalia" rel="S;1;1;A" title="Procedura calcolo anomalia">
                            <option value="">Seleziona...</option>
                            <option value="z">Sorteggia automaticamente</option>
                            <option value="a">Art. 97 c.2 lett. a</option>
                            <option value="b">Art. 97 c.2 lett. b</option>
                            <option value="c">Art. 97 c.2 lett. c</option>
                            <option value="d">Art. 97 c.2 lett. d</option>
                            <option value="e">Art. 97 c.2 lett. e</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="etichetta">Coefficente lett. e</td>
                    <td>
                        <select name="coef_e" id="coef_e" rel="S;1;3;A" title="Coefficente lett. e" <? if (empty($coef_e)) echo "disabled=\"disabled\""; ?>>
                            <option value="">Seleziona...</option>
                            <option value="z">Sorteggia automaticamente</option>
                            <? if (strtotime($record["data_pubblicazione"]) < strtotime('2017-05-20')) { ?>
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
            <script>
                $("#scelta_anomalia").change(function() {
                    $("#coef_e").val('');
                    if ($(this).val() == "e" || $(this).val() == "z") {
                        $("#coef_e").removeAttr("disabled");
                    } else {
                        $("#coef_e").attr("disabled", "disabled");
                    }
                    $("#coef_e").trigger('chosen:updated');
                });
                $("#scelta_anomalia").val('<?= $algoritmo_anomalia ?>');
                $("#coef_e").val('<?= $coef_e ?>');
            </script>
            <? if (!$lock) { ?>
                <input type="submit" class="submit_big" value="Salva">
        </form>
        <?  } else { ?>
            <script>
                $(":input").not('.espandi').prop("disabled", true);
            </script>
        <?
        }
    }
?>