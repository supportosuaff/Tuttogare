<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$rappresentanti = get_campi("b_rappresentanti");
		$id = $_POST["id"];
	}
?>
<tr id="rappresentanti_<? echo $id ?>"><td>
<input type="hidden" name="rappresentanti[<? echo $id ?>][codice]"id="codice_rappresentanti_<? echo $id ?>" value="<? echo $rappresentanti["codice"] ?>">
<table width="100%">
	<tr>
    	<tr><td class="etichetta"><?= traduci("Ruolo") ?>*</td><td colspan="5"> <select name="rappresentanti[<? echo $id ?>][qualita]" id="qualita_rappresentanti_<? echo $id ?>" title="<?= traduci("ruolo") ?>" rel="<?= (isset($obbligatorio)) ? $obbligatorio : "S" ?>;0;0;A">
            <option value=""><?= traduci("Seleziona") ?>...</option>
						<option><?= traduci("Amministratore delegato") ?></option>
						<option><?= traduci("Amministratore unico") ?></option>
						<option><?= traduci("Consigliere") ?></option>
						<option><?= traduci("Presidente del consiglio") ?></option>
						<option><?= traduci("Socio accomandatario") ?></option>
						<option><?= traduci("Legale rappresentante") ?></option>
                    </select></td></tr>
    	<td class="etichetta"><?= traduci("Nome") ?>*</td><td><input type="text" name="rappresentanti[<? echo $id ?>][nome]"  title="<?= traduci("Nome") ?>" rel="<?= (isset($obbligatorio)) ? $obbligatorio : "S" ?>;3;255;A" id="nome_rappresentanti_<? echo $id ?>" value="<? echo $rappresentanti["nome"] ?>"></td>
        <td class="etichetta"><?= traduci("Cognome") ?>*</td><td><input type="text" name="rappresentanti[<? echo $id ?>][cognome]"  title="<?= traduci("Cognome") ?>" rel="<?= (isset($obbligatorio)) ? $obbligatorio : "S" ?>;2;255;A" id="cognome_rappresentanti_<? echo $id ?>" value="<? echo $rappresentanti["cognome"] ?>"></td>
        <td class="etichetta"><?= traduci("Codice Fiscale") ?>*</td><td><input type="text" name="rappresentanti[<? echo $id ?>][codice_fiscale]"  title="<?= traduci("Codice Fiscale") ?>" rel="<?= (isset($obbligatorio)) ? $obbligatorio : "S" ?>;9;0;CF" id="codice_fiscale_rappresentanti_<? echo $id ?>" value="<? echo $rappresentanti["codice_fiscale"] ?>"></td>
        </tr>
        <tr>
        <td class="etichetta"><?= traduci("Indirizzo") ?>*</td><td><input type="text" name="rappresentanti[<? echo $id ?>][indirizzo]"  title="<?= traduci("Indirizzo") ?>" rel="<?= (isset($obbligatorio)) ? $obbligatorio : "S" ?>;3;255;A" id="indirizzo_rappresentanti_<? echo $id ?>" value="<? echo $rappresentanti["indirizzo"] ?>"></td>
        <td class="etichetta"><?= traduci("Citta") ?>*</td><td><input type="text" name="rappresentanti[<? echo $id ?>][citta]"  title="<?= traduci("Citta") ?>" rel="<?= (isset($obbligatorio)) ? $obbligatorio : "S" ?>;0;255;A" id="citta_rappresentanti_<? echo $id ?>" value="<? echo $rappresentanti["citta"] ?>"></td>
        <td class="etichetta"><?= traduci("CAP") ?>*</td><td><input type="text" name="rappresentanti[<? echo $id ?>][cap]" maxlength="5" title="<?= traduci("CAP") ?>" rel="<?= (isset($obbligatorio)) ? $obbligatorio : "S" ?>;5;0;A" id="cap_rappresentanti_<? echo $id ?>" value="<? echo $rappresentanti["cap"] ?>"></td></tr><tr>
         <td class="etichetta"><?= traduci("Provincia") ?>*</td><td><input type="text" name="rappresentanti[<? echo $id ?>][provincia]" maxlength="2"  title="<?= traduci("Provincia") ?>" rel="<?= (isset($obbligatorio)) ? $obbligatorio : "S" ?>;2;2;A" id="provincia_rappresentanti_<? echo $id ?>" value="<? echo $rappresentanti["provincia"] ?>"></td>
        <td class="etichetta"><?= traduci("nazione") ?>*</td><td colspan="3"><input type="text" name="rappresentanti[<? echo $id ?>][stato]"  title="<?= traduci("nazione") ?>" rel="<?= (isset($obbligatorio)) ? $obbligatorio : "S" ?>;0;255;A" id="stato_rappresentanti_<? echo $id ?>" value="<? echo $rappresentanti["stato"] ?>"></td>
        </tr>
</table>
</td><td width="10"><? if ($id!="i_0") { ?><input type="image" src="/img/del.png" onClick="elimina('<? echo $id ?>','operatori_economici/rappresentanti');return false;"><? } ?></td></tr>
	<? if (!isset($_POST["id"])) { ?>
    <script>
		$("#qualita_rappresentanti_<? echo $id ?>").val('<? echo $rappresentanti["qualita"] ?>');
	</script>
		<? } ?>
