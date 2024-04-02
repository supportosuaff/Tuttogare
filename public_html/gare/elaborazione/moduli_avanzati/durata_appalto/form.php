<table width="100%">
	<tr>
		<tr><td class="etichetta" colspan="2" style="background-color: #CCC; text-align:left;">
			<strong>Durata appalto</strong>
		</td>
	</tr>
	<tr>
    <td width="10"><input size="3" type="text" title="Durata"  name="durata[durata]" id="durata" value="<? echo $record_gara["durata"]; ?>" rel="S;1;3;N;0;>"></td><td>
    <div style="width:100px">
	    <select style="width:100px !important;" name="durata[unita_durata]" id="unita_durata" title="Unità durata" rel="S;2;2;A">
        	<option value="">Seleziona...</option>
    		<option value="gg">Giorni</option>
        	<option value="mm">Mesi</option>
	    </select>
        </div>
    </td>
     </tr>
</table>
<script>
	$("#unita_durata").val("<? echo $record_gara["unita_durata"] ?>");
</script>
