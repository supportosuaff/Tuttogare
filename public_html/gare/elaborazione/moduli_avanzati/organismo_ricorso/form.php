<?
	$record_organismo = get_campi("b_organismi_ricorso");
	$bind=array();
	$bind[":codice_ente"] = $record_gara["codice_ente"];
	$sql_organismo ="SELECT * FROM b_organismi_ricorso WHERE codice_ente = :codice_ente";
	$ris_organismo = $pdo->bindAndExec($sql_organismo,$bind);
	if ($ris_organismo->rowCount() > 0) $record_organismo = $ris_organismo->fetch(PDO::FETCH_ASSOC);
?>
	<table width="100%">
    <tr>
        <td class="etichetta" colspan="4" style="background-color: #CCC; text-align:left;">
            <strong>Organismo responsabile delle procedure di ricorso</strong>
        </td>
    </tr>
    <tr>
        <td class="etichetta" width="10%"> <label for="organismo[denominazione]">Denominazione</label></td>
        <td colspan="3">
            <input style="width:95%" type="text" name="organismo[denominazione]" id="organismo_denominazione" title="Denominazione" value="<? echo $record_organismo["denominazione"] ?>" rel="S;2;0;A">
        </td>
    </tr>
    <tr>
        <td class="etichetta"><label for="organismo[url]">Sito istituzionale</label></td>
        <td colspan="3">
            <input style="width:95%" type="text" name="organismo[url]" id="organismo_url" title="Sito istituzionale" value="<? echo $record_organismo["url"] ?>" rel="N;5;0;L">
        </td>
    </tr>
    <tr>
        <td class="etichetta"><label for="organismo[indirizzo]">Indirizzo</label></td>
        <td>
            <input type="text" name="organismo[indirizzo]" id="organismo_indirizzo" title="Indirizzo" value="<? echo $record_organismo["indirizzo"] ?>" rel="N;5;0;A">
        </td>
        <td class="etichetta"><label for="organismo[citta]">Citta</label></td>
        <td>
            <input type="text" name="organismo[citta]" id="organismo_citta" title="Citta" value="<? echo $record_organismo["citta"] ?>" rel="N;2;0;A">
        </td>
    </tr>
    <tr>
        <td class="etichetta"><label for="organismo[cap]">CAP</label></td>
        <td>
            <input type="text" name="organismo[cap]" id="organismo_cap" title="C.A.P." value="<? echo $record_organismo["cap"] ?>" rel="N;5;5;A" size="5" maxlength="5">
        </td>
        <td class="etichetta"><label for="organismo[provincia]">Provincia</label></td>
        <td>
            <input type="text" name="organismo[provincia]" id="organismo_provincia" title="Provincia" value="<? echo $record_organismo["provincia"] ?>" rel="N;2;2;A" size="2" maxlength="2">
        </td>
    </tr>
    <tr>
        <td class="etichetta"><label for="organismo[stato]">Stato</label></td>
        <td>
            <input type="text" name="organismo[stato]" id="organismo_stato" title="Stato" value="<? echo $record_organismo["stato"] ?>" rel="N;2;0;A">
        </td>
        <td class="etichetta"><label for="organismo[telefono]">Telefono</label></td>
        <td>
            <input type="text" name="organismo[telefono]" id="organismo_telefono" title="Telefono" value="<? echo $record_organismo["telefono"] ?>" rel="N;0;0;A">
        </td>
    </tr>
    <tr>
        <td class="etichetta"><label for="organismo[fax]">Fax</label></td>
        <td>
            <input type="text" name="organismo[fax]" id="organismo_fax" title="fax" value="<? echo $record_organismo["fax"] ?>" rel="N;0;0;A">
        </td>
        <td class="etichetta"><label for="organismo[email]">E-mail</label></td>
        <td>
            <input type="text" name="organismo[email]" id="organismo_email" title="email" value="<? echo $record_organismo["email"] ?>" rel="N;0;0;E">
        </td>
    </tr>
    <tr>
        <td class="etichetta"><label for="organismo[pec]">PEC</label></td>
        <td colspan="3">
            <input type="text" name="organismo[pec]" id="organismo_pec" title="pec" value="<? echo $record_organismo["pec"] ?>" rel="N;0;0;E">
        </td>
    </tr>
</table>