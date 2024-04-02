<tr><td>
<table width="100%">
	<tr><td class="etichetta">Ente certificatore</td><td colspan="3"><? echo $ambientali["ente"] ?></td></tr>
    <tr><td class="etichetta">Settore</td><td><? echo $ambientali["settore"] ?></td>
    <td class="etichetta">Norma</td><td><? echo $ambientali["norma"] ?></td></tr><tr>
    <td class="etichetta">Data rilascio</td><td><? echo mysql2date($ambientali["data_rilascio"]) ?></td>
     <td class="etichetta">Data scadenza</td><td><? echo mysql2date($ambientali["data_scadenza"]) ?></td></tr>
     <tr><td class="etichetta">Certificato</td><td colspan="3"><?
						if ($ambientali["certificato"] != "") {

							?>
                            <a href="/documenti/operatori/<? echo $ambientali["codice_operatore"] ?>/<? echo $ambientali["certificato"] ?>" title="File allegato"><img src="/img/<? echo substr($ambientali["certificato"],-3)?>.png" alt="File <? echo substr($ambientali["certificato"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato</a><br>
                            <?
						}
					?>
    </td></tr>
</table>
</td></tr>
