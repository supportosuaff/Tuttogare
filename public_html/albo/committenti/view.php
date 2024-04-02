<tr><td>
<table width="100%">
	<tr><td class="etichetta">Denominazione</td><td colspan="3"><? echo $committenti["denominazione"] ?></td></tr>
    <td class="etichetta">Atto</td><td><? echo $committenti["atto"] ?></td>
    <td class="etichetta">Importo</td><td><? echo $committenti["importo"] ?></td></tr><tr>
    <td class="etichetta">Data inizio</td><td><? echo mysql2date($committenti["dal"]) ?></td>
        <td class="etichetta">Data fine</td><td><? echo mysql2date($committenti["al"]) ?></td>
        <tr><td colspan="4" class="etichetta">Oggetto</td></tr>
        <tr><td colspan="4"><? echo $committenti["oggetto"] ?></tr>
</table></td></tr>