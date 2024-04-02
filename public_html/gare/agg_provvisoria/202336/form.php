<? 
    $riferimento = $record;
    if (!empty($lotto["algoritmo_anomalia"])) {
        $riferimento = $lotto;
    }
    if (!empty($scelta_anomalia) && $riferimento["algoritmo_anomalia"] != "N" && $record["tipologia"] != 3) {  ?>
    <div class="box">
        <h2>Scelta procedura di calcolo anomalia</h2>
        <?        
            if (!empty($riferimento["algoritmo_anomalia"])) {
                switch($riferimento["algoritmo_anomalia"]) {
                    case "23A5": $labelMetodo = "Metodo A"; break;
                    case "23A15": $labelMetodo = "Metodo A"; break;
                    case "23B": $labelMetodo = "Metodo B"; break;
                    case "23C": $labelMetodo = "Metodo C"; break;
                    default: $labelMetodo = $riferimento["algoritmo_anomalia"];
                }
                
                if (empty($riferimento["messaggio_anomalia"])) {
                    echo "<br>Metodo: <strong>" . strtoupper($riferimento["algoritmo_anomalia"]) . "</strong>" . ((!empty($riferimento["coef_e"])) ? " - Ribasso riferimento: <strong>" . $riferimento["coef_e"] . "</strong>" : "");
                }
            } else {
        ?>
        <table width="100%">
            <tr>
                <td class="etichetta">Metodo</td>
                <td width="50%">
                    <select name="scelta_anomalia" class="parametri_anomalia" id="scelta_anomalia" rel="N;1;1;A" title="Procedura calcolo anomalia">
                        <option value="">Seleziona...</option>
                        <option value="S">Sorteggia automaticamente</option>
                    </select>
                </td>
            </tr>
        </table>
        <? } ?>
    </div>
    <? 
    }
?>
