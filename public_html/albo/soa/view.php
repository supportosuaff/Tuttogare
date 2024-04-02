<tr><td>
<table width="100%">
	<tr><td class="etichetta">Ente certificatore</td><td colspan="3"><? echo $soa["ente"] ?></td></tr>
    <tr><td class="etichetta">Categoria</td><td><? echo $soa["id"] . " " . $soa["descrizione"] ?></td>
			 <td class="etichetta">Classifica</td><td><? echo $soa["id_classifica"] ?></td></tr>
		<tr>
    <td class="etichetta">Data rilascio</td><td><? echo mysql2date($soa["data_rilascio"]) ?></td>
     <td class="etichetta">Data scadenza</td><td><? echo mysql2date($soa["data_scadenza"]) ?></td></tr>
     <tr><td class="etichetta">Certificato</td><td colspan="3"><?
						if ($soa["certificato"] != "") {

							?>
                            <a href="/documenti/operatori/<? echo $soa["codice_operatore"] ?>/<? echo $soa["certificato"] ?>" title="File allegato"><img src="/img/<? echo substr($soa["certificato"],-3)?>.png" alt="File <? echo substr($soa["certificato"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato</a><br>
                            <?
						}
					?>
    </td></tr>
</table>
</td></tr>
