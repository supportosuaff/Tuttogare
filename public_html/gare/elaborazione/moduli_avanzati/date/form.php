<?
	$limite = date("d/m/Y H:i") . ";>";
?>
<table width="100%">
	<tr>
		<tr><td class="etichetta" colspan="6" style="background-color: #CCC; text-align:left;">
			<strong>Scadenze</strong>
		</td>
	</tr>
	<tr>
    <td class="etichetta">Termine richieste chiarimenti</td>    <td><input type="text" inline="true" class="datetimepick_today" title="Termine richieste chiarimenti"  name="date[data_accesso]" id="data_accesso" value="<? echo mysql2datetime($record_gara["data_accesso"]); ?>" rel="S;16;16;DT;<? echo $limite ?>">
    </td>
    <td class="etichetta">Termine ricevimento offerte</td>    <td>
    	<input type="text" class="datetimepick_today" title="Termine ricevimento offerte"  name="date[data_scadenza]" id="data_scadenza" value="<? echo mysql2datetime($record_gara["data_scadenza"]) ?>" rel="S;16;16;DT;data_accesso;>">
    </td>
     <td class="etichetta">Apertura offerte</td>
    <td>
    	<input type="text" class="datetimepick_today" title="Apertura offerte"  name="date[data_apertura]" id="data_apertura" value="<? echo mysql2datetime($record_gara["data_apertura"]) ?>" rel="S;16;16;DT;data_scadenza;>">
    </td>
     </tr>
</table>
