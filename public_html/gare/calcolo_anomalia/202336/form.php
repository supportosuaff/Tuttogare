<?
    if (isset($record)) {
        
        $print_form = true;
        $data_pubblicazione = mysql2date($record["data_pubblicazione"]);

        if (!empty($data_pubblicazione) && $record["pubblica"] > 0) {
            $print_form = false;
            if ($algoritmo_anomalia == "S" && strtotime($record["data_scadenza"]) < time()) {
                $print_form = true;
            }
            
        } 
        if (!empty($algoritmo_anomalia)) {
            switch($algoritmo_anomalia) {
                case "23A5": $labelMetodo = "Metodo A"; break;
                case "23A15": $labelMetodo = "Metodo A"; break;
                case "23B": $labelMetodo = "Metodo B"; break;
                case "23C": $labelMetodo = "Metodo C"; break;
                default: $labelMetodo = $algoritmo_anomalia;
            }
            ?>
            <div class="box edit-form" style="text-align:center">
                <strong>Metodo scelto:</strong><br>
                <?= ($algoritmo_anomalia == "S") ? "Sorteggio" : $labelMetodo ?>
                <? if (!empty($coef_e)) { ?>
                    <br><strong>Ribasso di riferimento</strong><br>
                    <?= $coef_e ?>
                <? } ?>
            <?
                if ($print_form) {
                    ?>
                        <button type="button" class="submit_big" style="background-color:#Fc0" value="Modifica" onclick="$('.edit-form').slideToggle();">
                        Modifica
                        </button>

                    <?
                }
            ?>
            </div>
            <?
        }
        if ($print_form) {
            ?>
            <form <?= (!empty($algoritmo_anomalia)) ? "style='display:none'" : "" ?> name="box" method="post" action="save.php" rel="validate" class="edit-form">
                <input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
                <input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
                <div class="comandi">
                    <button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
                </div>
                <table width="100%">
                    <tr>
                        <td class="etichetta">Vuoi applicare l'esclusione automatica delle offerte anomale?</td>
                        <td>
                            <select name="scelta_anomalia" id="scelta_anomalia" rel="S;1;1;A" title="Procedura calcolo anomalia">
                                <option value="">Seleziona...</option>
                                <option value="N">NO</option>
                                <option value="A">SI, Metodo A dell'allegato II.2 dell'D.lgs. 36/2023</option>
                                <option value="B">SI, Metodo B dell'allegato II.2 dell'D.lgs. 36/2023</option>
                                <option value="C">SI, Metodo C dell'allegato II.2 dell'D.lgs. 36/2023</option>
                                <option value="S">SI, sorteggerò il metodo dopo la scadenza</option>
                            </select>
                            <br><small>
                            Ai sensi dell'art. 54 del D.Lgs. 36/2023, l'esclusione automatica delle offerte deve essere prevista negli atti di gara e il metodo di individuazione della soglia di anomalia deve essere scelto tra uno dei tre algoritmi previsti dall'allegato. II.2 del codice
                            </small>
                        </td>
                    </tr>
                </table>
                <? 
                    $percentili = json_decode(file_get_contents(__DIR__."/percentiliMetodoC.json"),true); 
                ?>
                <div id="table-percentile" <?= ($algoritmo_anomalia === "C") ? "" : "style='display:none;'" ?>>
                    <table width="100%">
                        <thead>
                            <tr>
                                <th>
                                    Categoria / Valore
                                </th>
                                <th>50 &deg;</th>
                                <th>60 &deg;</th>
                                <th>70 &deg;</th>
                                <th>80 &deg;</th>
                                <th>90 &deg;</th>
                                <th>95 &deg;</th>
                                <th>99 &deg;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                                foreach($percentili AS $categoria => $importi) {
                                    ?>
                                    <tr>
                                        <td class="etichetta" colspan="8"><?= $categoria ?></td>
                                    </tr>
                                    <?
                                    foreach($importi AS $fascia => $p) {
                                        ?>
                                        <tr>
                                            <td class="etichetta"><?= $fascia ?></td>
                                            <? foreach($p AS $r) { ?>
                                                <td style="text-align:center">
                                                    <?= $r ?><br>
                                                    <input type="radio" name="percentile" <?= ($coef_e == $r) ? "checked" : "" ?> value="<?= $r ?>">
                                                </td>
                                            <? } ?>
                                        </tr>
                                        <?
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <script>
                    $("#scelta_anomalia").change(function() {
                        if ($(this).val() == "C") {
                            $(".percentile").removeAttr("disabled");
                            $("#table-percentile").slideDown();
                        } else {
                            $(".percentile").attr("disabled", "disabled");
                            $("#table-percentile").slideUp();
                        }
                    });
                    $("#scelta_anomalia").val('<?= $algoritmo_anomalia ?>');
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
    }
?>