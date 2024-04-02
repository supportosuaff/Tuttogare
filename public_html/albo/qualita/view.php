<tr><td>
<table width="100%">
	<tr><td class="etichetta">Ente certificatore</td><td colspan="3"><? echo $qualita["ente"] ?></td></tr>
    <tr><td class="etichetta">Settore</td><td><? echo $qualita["settore"] ?></td>
    <td class="etichetta">Norma</td><td><? echo $qualita["norma"] ?></td></tr><tr>
    <td class="etichetta">Data rilascio</td><td><? echo mysql2date($qualita["data_rilascio"]) ?></td>
     <td class="etichetta">Data scadenza</td><td><? echo mysql2date($qualita["data_scadenza"]) ?></td></tr>
     <tr><td class="etichetta">Certificato</td><td colspan="3"><?
						if ($qualita["certificato"] != "") {

							?>
							<a href="/documenti/operatori/<? echo $qualita["codice_operatore"] ?>/<? echo $qualita["certificato"] ?>" title="File allegato"><img src="/img/<? echo substr($qualita["certificato"],-3)?>.png" alt="File <? echo substr($qualita["certificato"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato</a><br>
							<?
						}
					?>
    </td></tr>
</table>
</td></tr>
