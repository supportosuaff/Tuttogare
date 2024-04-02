<tr id="brevetti_<? echo $id ?>"><td>
<table width="100%"><tr>
    <td class="etichetta">Numero</td><td><? echo $brevetti["numero"] ?></td><td class="etichetta">Data</td><td><? echo mysql2date($brevetti["data"]) ?></td></tr>
        <tr><td colspan="4" class="etichetta">Breve descrizione</td></tr>
        <tr><td colspan="4"><? echo $brevetti["descrizione"] ?>
        </tr>
</table>
</td></tr>