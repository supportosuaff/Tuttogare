<?
	if(empty($address_item)) $address_item = null;
	if(empty($prefix)) $prefix = "ADDRS1-";
	if(!isset($required)) $required = TRUE;
	if(empty($excluded_input)) $excluded_input = array();
	if(empty($added_input)) $added_input = array();
	if(empty($keys)) $keys = "";
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && empty($root)) {
		include_once '../../../../../config.php';
		include_once $root . '/inc/funzioni.php';
	}
	if(!empty($_POST["param"]["chiavi"])) {
		$_POST["chiavi"] = $_POST["param"]["chiavi"];
		$address_item = $_POST["param"]["item"];
	}
	if(!empty($_POST["chiavi"]))
	{
		foreach ($_POST["chiavi"] as $chiave) {
			$keys .= '['.$chiave.']';
		}
	}
	$replaced_key = str_replace(array('[',']'), array("", "_"), $keys);
?>
<table id="<?= !empty($address_item) ? 'address_item_'.$address_item : null ?>" class="bordered">
	<tbody>
		<tr>
			<td class="etichetta">
				Denominazione ufficiale:
			</td>
			<td colspan="<?= !in_array("NATIONALID", $excluded_input) ? '3' : '7' ?>">
				<input type="text" style="font-size: 1.1em" title="Denominazione ufficiale" name="guue<?= $keys ?>[OFFICIALNAME]" <?= !empty($guue[$replaced_key]["OFFICIALNAME"]) ? 'value="'.$guue[$replaced_key]["OFFICIALNAME"].'"' : null  ?> class="espandi" rel="<?= isRequired($prefix."OFFICIALNAME") ?>;2;300;A">
			</td>
			<?
			if(!in_array("NATIONALID", $excluded_input)) {
				?>
				<td class="etichetta" colspan="2">
					Numero di identificazione nazionale:
				</td>
				<td colspan="2">
					<input type="text" style="font-size: 1.1em" title="Numero di identificazione nazionale" name="guue<?= $keys ?>[NATIONALID]" <?= !empty($guue[$replaced_key]["NATIONALID"]) ? 'value="'.$guue[$replaced_key]["NATIONALID"].'"' : null  ?> class="espandi" rel="<?= isRequired($prefix."NATIONALID") ?>;0:100;A">
				</td>
				<?
			}
			?>
		</tr>
		<tr>
			<td class="etichetta">
				Indirizzo postale:
			</td>
			<td colspan="8">
				<input type="text" style="font-size: 1.1em" title="Indirizzo postale" class="espandi" name="guue<?= $keys ?>[ADDRESS]" <?= !empty($guue[$replaced_key]["ADDRESS"]) ? 'value="'.$guue[$replaced_key]["ADDRESS"].'"' : null  ?> rel="<?= isRequired($prefix."ADDRESS") ?>;0:400;A">
			</td>
		</tr>
		<tr>
			<td class="etichetta">
				Citt&agrave;:
			</td>
			<td colspan="3">
				<input type="text" style="font-size: 1.1em" title="Citt&agrave;" name="guue<?= $keys ?>[TOWN]" <?= !empty($guue[$replaced_key]["TOWN"]) ? 'value="'.$guue[$replaced_key]["TOWN"].'"' : null  ?> class="espandi" rel="<?= isRequired($prefix."TOWN") ?>;2;100;A">
			</td>
			<td class="etichetta">
				Codice postale:
			</td>
			<td>
				<input type="text" style="font-size: 1.1em" title="Codice postale" class="espandi" name="guue<?= $keys ?>[POSTAL_CODE]" <?= !empty($guue[$replaced_key]["POSTAL_CODE"]) ? 'value="'.$guue[$replaced_key]["POSTAL_CODE"].'"' : null  ?> rel="<?= isRequired($prefix."POSTAL_CODE") ?>;0;20;N">
			</td>
			<td class="etichetta">
				Paese:
			</td>
			<td>
				<select title="Paese" name="guue<?= $keys ?>[COUNTRY][ATTRIBUTE][VALUE]" rel="<?= isRequired($prefix."COUNTRY") ?>;1;2;A">
					<option value="">Seleziona...</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "1A" ) ? 'selected="selected"' : null  ?> value="1A">1A</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "AD" ) ? 'selected="selected"' : null  ?> value="AD">AD</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "AE" ) ? 'selected="selected"' : null  ?> value="AE">AE</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "AF" ) ? 'selected="selected"' : null  ?> value="AF">AF</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "AG" ) ? 'selected="selected"' : null  ?> value="AG">AG</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "AI" ) ? 'selected="selected"' : null  ?> value="AI">AI</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "AL" ) ? 'selected="selected"' : null  ?> value="AL">AL</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "AM" ) ? 'selected="selected"' : null  ?> value="AM">AM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "AO" ) ? 'selected="selected"' : null  ?> value="AO">AO</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "AQ" ) ? 'selected="selected"' : null  ?> value="AQ">AQ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "AR" ) ? 'selected="selected"' : null  ?> value="AR">AR</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "AS" ) ? 'selected="selected"' : null  ?> value="AS">AS</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "AT" ) ? 'selected="selected"' : null  ?> value="AT">AT</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "AU" ) ? 'selected="selected"' : null  ?> value="AU">AU</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "AW" ) ? 'selected="selected"' : null  ?> value="AW">AW</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "AX" ) ? 'selected="selected"' : null  ?> value="AX">AX</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "AZ" ) ? 'selected="selected"' : null  ?> value="AZ">AZ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BA" ) ? 'selected="selected"' : null  ?> value="BA">BA</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BB" ) ? 'selected="selected"' : null  ?> value="BB">BB</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BD" ) ? 'selected="selected"' : null  ?> value="BD">BD</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BE" ) ? 'selected="selected"' : null  ?> value="BE">BE</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BF" ) ? 'selected="selected"' : null  ?> value="BF">BF</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BG" ) ? 'selected="selected"' : null  ?> value="BG">BG</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BH" ) ? 'selected="selected"' : null  ?> value="BH">BH</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BI" ) ? 'selected="selected"' : null  ?> value="BI">BI</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BJ" ) ? 'selected="selected"' : null  ?> value="BJ">BJ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BL" ) ? 'selected="selected"' : null  ?> value="BL">BL</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BM" ) ? 'selected="selected"' : null  ?> value="BM">BM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BN" ) ? 'selected="selected"' : null  ?> value="BN">BN</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BO" ) ? 'selected="selected"' : null  ?> value="BO">BO</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BQ" ) ? 'selected="selected"' : null  ?> value="BQ">BQ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BR" ) ? 'selected="selected"' : null  ?> value="BR">BR</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BS" ) ? 'selected="selected"' : null  ?> value="BS">BS</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BT" ) ? 'selected="selected"' : null  ?> value="BT">BT</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BV" ) ? 'selected="selected"' : null  ?> value="BV">BV</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BW" ) ? 'selected="selected"' : null  ?> value="BW">BW</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BY" ) ? 'selected="selected"' : null  ?> value="BY">BY</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "BZ" ) ? 'selected="selected"' : null  ?> value="BZ">BZ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CA" ) ? 'selected="selected"' : null  ?> value="CA">CA</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CC" ) ? 'selected="selected"' : null  ?> value="CC">CC</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CD" ) ? 'selected="selected"' : null  ?> value="CD">CD</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CF" ) ? 'selected="selected"' : null  ?> value="CF">CF</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CG" ) ? 'selected="selected"' : null  ?> value="CG">CG</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CH" ) ? 'selected="selected"' : null  ?> value="CH">CH</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CI" ) ? 'selected="selected"' : null  ?> value="CI">CI</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CK" ) ? 'selected="selected"' : null  ?> value="CK">CK</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CL" ) ? 'selected="selected"' : null  ?> value="CL">CL</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CM" ) ? 'selected="selected"' : null  ?> value="CM">CM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CN" ) ? 'selected="selected"' : null  ?> value="CN">CN</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CO" ) ? 'selected="selected"' : null  ?> value="CO">CO</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CP" ) ? 'selected="selected"' : null  ?> value="CP">CP</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CR" ) ? 'selected="selected"' : null  ?> value="CR">CR</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CU" ) ? 'selected="selected"' : null  ?> value="CU">CU</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CV" ) ? 'selected="selected"' : null  ?> value="CV">CV</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CW" ) ? 'selected="selected"' : null  ?> value="CW">CW</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CX" ) ? 'selected="selected"' : null  ?> value="CX">CX</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CY" ) ? 'selected="selected"' : null  ?> value="CY">CY</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "CZ" ) ? 'selected="selected"' : null  ?> value="CZ">CZ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "DE" ) ? 'selected="selected"' : null  ?> value="DE">DE</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "DJ" ) ? 'selected="selected"' : null  ?> value="DJ">DJ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "DK" ) ? 'selected="selected"' : null  ?> value="DK">DK</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "DM" ) ? 'selected="selected"' : null  ?> value="DM">DM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "DO" ) ? 'selected="selected"' : null  ?> value="DO">DO</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "DZ" ) ? 'selected="selected"' : null  ?> value="DZ">DZ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "EC" ) ? 'selected="selected"' : null  ?> value="EC">EC</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "EE" ) ? 'selected="selected"' : null  ?> value="EE">EE</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "EG" ) ? 'selected="selected"' : null  ?> value="EG">EG</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "EH" ) ? 'selected="selected"' : null  ?> value="EH">EH</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "ER" ) ? 'selected="selected"' : null  ?> value="ER">ER</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "ES" ) ? 'selected="selected"' : null  ?> value="ES">ES</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "ET" ) ? 'selected="selected"' : null  ?> value="ET">ET</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "FI" ) ? 'selected="selected"' : null  ?> value="FI">FI</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "FJ" ) ? 'selected="selected"' : null  ?> value="FJ">FJ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "FK" ) ? 'selected="selected"' : null  ?> value="FK">FK</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "FM" ) ? 'selected="selected"' : null  ?> value="FM">FM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "FO" ) ? 'selected="selected"' : null  ?> value="FO">FO</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "FR" ) ? 'selected="selected"' : null  ?> value="FR">FR</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GA" ) ? 'selected="selected"' : null  ?> value="GA">GA</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GD" ) ? 'selected="selected"' : null  ?> value="GD">GD</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GE" ) ? 'selected="selected"' : null  ?> value="GE">GE</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GF" ) ? 'selected="selected"' : null  ?> value="GF">GF</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GG" ) ? 'selected="selected"' : null  ?> value="GG">GG</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GH" ) ? 'selected="selected"' : null  ?> value="GH">GH</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GI" ) ? 'selected="selected"' : null  ?> value="GI">GI</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GL" ) ? 'selected="selected"' : null  ?> value="GL">GL</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GM" ) ? 'selected="selected"' : null  ?> value="GM">GM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GN" ) ? 'selected="selected"' : null  ?> value="GN">GN</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GP" ) ? 'selected="selected"' : null  ?> value="GP">GP</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GQ" ) ? 'selected="selected"' : null  ?> value="GQ">GQ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GR" ) ? 'selected="selected"' : null  ?> value="GR">GR</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GS" ) ? 'selected="selected"' : null  ?> value="GS">GS</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GT" ) ? 'selected="selected"' : null  ?> value="GT">GT</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GU" ) ? 'selected="selected"' : null  ?> value="GU">GU</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GW" ) ? 'selected="selected"' : null  ?> value="GW">GW</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "GY" ) ? 'selected="selected"' : null  ?> value="GY">GY</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "HK" ) ? 'selected="selected"' : null  ?> value="HK">HK</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "HM" ) ? 'selected="selected"' : null  ?> value="HM">HM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "HN" ) ? 'selected="selected"' : null  ?> value="HN">HN</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "HR" ) ? 'selected="selected"' : null  ?> value="HR">HR</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "HT" ) ? 'selected="selected"' : null  ?> value="HT">HT</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "HU" ) ? 'selected="selected"' : null  ?> value="HU">HU</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "ID" ) ? 'selected="selected"' : null  ?> value="ID">ID</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "IE" ) ? 'selected="selected"' : null  ?> value="IE">IE</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "IL" ) ? 'selected="selected"' : null  ?> value="IL">IL</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "IM" ) ? 'selected="selected"' : null  ?> value="IM">IM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "IN" ) ? 'selected="selected"' : null  ?> value="IN">IN</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "IO" ) ? 'selected="selected"' : null  ?> value="IO">IO</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "IQ" ) ? 'selected="selected"' : null  ?> value="IQ">IQ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "IR" ) ? 'selected="selected"' : null  ?> value="IR">IR</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "IS" ) ? 'selected="selected"' : null  ?> value="IS">IS</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "IT" ) ? 'selected="selected"' : null  ?> value="IT" <?= empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) ? 'selected="selected"' : null ?>>IT</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "JE" ) ? 'selected="selected"' : null  ?> value="JE">JE</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "JM" ) ? 'selected="selected"' : null  ?> value="JM">JM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "JO" ) ? 'selected="selected"' : null  ?> value="JO">JO</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "JP" ) ? 'selected="selected"' : null  ?> value="JP">JP</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "KE" ) ? 'selected="selected"' : null  ?> value="KE">KE</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "KG" ) ? 'selected="selected"' : null  ?> value="KG">KG</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "KH" ) ? 'selected="selected"' : null  ?> value="KH">KH</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "KI" ) ? 'selected="selected"' : null  ?> value="KI">KI</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "KM" ) ? 'selected="selected"' : null  ?> value="KM">KM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "KN" ) ? 'selected="selected"' : null  ?> value="KN">KN</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "KP" ) ? 'selected="selected"' : null  ?> value="KP">KP</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "KR" ) ? 'selected="selected"' : null  ?> value="KR">KR</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "KW" ) ? 'selected="selected"' : null  ?> value="KW">KW</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "KY" ) ? 'selected="selected"' : null  ?> value="KY">KY</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "KZ" ) ? 'selected="selected"' : null  ?> value="KZ">KZ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "LA" ) ? 'selected="selected"' : null  ?> value="LA">LA</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "LB" ) ? 'selected="selected"' : null  ?> value="LB">LB</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "LC" ) ? 'selected="selected"' : null  ?> value="LC">LC</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "LI" ) ? 'selected="selected"' : null  ?> value="LI">LI</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "LK" ) ? 'selected="selected"' : null  ?> value="LK">LK</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "LR" ) ? 'selected="selected"' : null  ?> value="LR">LR</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "LS" ) ? 'selected="selected"' : null  ?> value="LS">LS</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "LT" ) ? 'selected="selected"' : null  ?> value="LT">LT</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "LU" ) ? 'selected="selected"' : null  ?> value="LU">LU</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "LV" ) ? 'selected="selected"' : null  ?> value="LV">LV</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "LY" ) ? 'selected="selected"' : null  ?> value="LY">LY</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MA" ) ? 'selected="selected"' : null  ?> value="MA">MA</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MC" ) ? 'selected="selected"' : null  ?> value="MC">MC</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MD" ) ? 'selected="selected"' : null  ?> value="MD">MD</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "ME" ) ? 'selected="selected"' : null  ?> value="ME">ME</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MF" ) ? 'selected="selected"' : null  ?> value="MF">MF</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MG" ) ? 'selected="selected"' : null  ?> value="MG">MG</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MH" ) ? 'selected="selected"' : null  ?> value="MH">MH</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MK" ) ? 'selected="selected"' : null  ?> value="MK">MK</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "ML" ) ? 'selected="selected"' : null  ?> value="ML">ML</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MM" ) ? 'selected="selected"' : null  ?> value="MM">MM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MN" ) ? 'selected="selected"' : null  ?> value="MN">MN</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MO" ) ? 'selected="selected"' : null  ?> value="MO">MO</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MP" ) ? 'selected="selected"' : null  ?> value="MP">MP</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MQ" ) ? 'selected="selected"' : null  ?> value="MQ">MQ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MR" ) ? 'selected="selected"' : null  ?> value="MR">MR</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MS" ) ? 'selected="selected"' : null  ?> value="MS">MS</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MT" ) ? 'selected="selected"' : null  ?> value="MT">MT</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MU" ) ? 'selected="selected"' : null  ?> value="MU">MU</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MV" ) ? 'selected="selected"' : null  ?> value="MV">MV</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MW" ) ? 'selected="selected"' : null  ?> value="MW">MW</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MX" ) ? 'selected="selected"' : null  ?> value="MX">MX</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MY" ) ? 'selected="selected"' : null  ?> value="MY">MY</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "MZ" ) ? 'selected="selected"' : null  ?> value="MZ">MZ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "NA" ) ? 'selected="selected"' : null  ?> value="NA">NA</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "NC" ) ? 'selected="selected"' : null  ?> value="NC">NC</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "NE" ) ? 'selected="selected"' : null  ?> value="NE">NE</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "NF" ) ? 'selected="selected"' : null  ?> value="NF">NF</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "NG" ) ? 'selected="selected"' : null  ?> value="NG">NG</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "NI" ) ? 'selected="selected"' : null  ?> value="NI">NI</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "NL" ) ? 'selected="selected"' : null  ?> value="NL">NL</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "NO" ) ? 'selected="selected"' : null  ?> value="NO">NO</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "NP" ) ? 'selected="selected"' : null  ?> value="NP">NP</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "NR" ) ? 'selected="selected"' : null  ?> value="NR">NR</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "NU" ) ? 'selected="selected"' : null  ?> value="NU">NU</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "NZ" ) ? 'selected="selected"' : null  ?> value="NZ">NZ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "OM" ) ? 'selected="selected"' : null  ?> value="OM">OM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "PA" ) ? 'selected="selected"' : null  ?> value="PA">PA</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "PE" ) ? 'selected="selected"' : null  ?> value="PE">PE</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "PF" ) ? 'selected="selected"' : null  ?> value="PF">PF</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "PG" ) ? 'selected="selected"' : null  ?> value="PG">PG</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "PH" ) ? 'selected="selected"' : null  ?> value="PH">PH</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "PK" ) ? 'selected="selected"' : null  ?> value="PK">PK</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "PL" ) ? 'selected="selected"' : null  ?> value="PL">PL</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "PM" ) ? 'selected="selected"' : null  ?> value="PM">PM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "PN" ) ? 'selected="selected"' : null  ?> value="PN">PN</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "PR" ) ? 'selected="selected"' : null  ?> value="PR">PR</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "PS" ) ? 'selected="selected"' : null  ?> value="PS">PS</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "PT" ) ? 'selected="selected"' : null  ?> value="PT">PT</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "PW" ) ? 'selected="selected"' : null  ?> value="PW">PW</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "PY" ) ? 'selected="selected"' : null  ?> value="PY">PY</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "QA" ) ? 'selected="selected"' : null  ?> value="QA">QA</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "RE" ) ? 'selected="selected"' : null  ?> value="RE">RE</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "RO" ) ? 'selected="selected"' : null  ?> value="RO">RO</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "RS" ) ? 'selected="selected"' : null  ?> value="RS">RS</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "RU" ) ? 'selected="selected"' : null  ?> value="RU">RU</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "RW" ) ? 'selected="selected"' : null  ?> value="RW">RW</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SA" ) ? 'selected="selected"' : null  ?> value="SA">SA</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SB" ) ? 'selected="selected"' : null  ?> value="SB">SB</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SC" ) ? 'selected="selected"' : null  ?> value="SC">SC</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SD" ) ? 'selected="selected"' : null  ?> value="SD">SD</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SE" ) ? 'selected="selected"' : null  ?> value="SE">SE</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SG" ) ? 'selected="selected"' : null  ?> value="SG">SG</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SH" ) ? 'selected="selected"' : null  ?> value="SH">SH</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SI" ) ? 'selected="selected"' : null  ?> value="SI">SI</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SJ" ) ? 'selected="selected"' : null  ?> value="SJ">SJ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SK" ) ? 'selected="selected"' : null  ?> value="SK">SK</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SL" ) ? 'selected="selected"' : null  ?> value="SL">SL</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SM" ) ? 'selected="selected"' : null  ?> value="SM">SM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SN" ) ? 'selected="selected"' : null  ?> value="SN">SN</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SO" ) ? 'selected="selected"' : null  ?> value="SO">SO</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SR" ) ? 'selected="selected"' : null  ?> value="SR">SR</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SS" ) ? 'selected="selected"' : null  ?> value="SS">SS</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "ST" ) ? 'selected="selected"' : null  ?> value="ST">ST</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SV" ) ? 'selected="selected"' : null  ?> value="SV">SV</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SX" ) ? 'selected="selected"' : null  ?> value="SX">SX</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SY" ) ? 'selected="selected"' : null  ?> value="SY">SY</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "SZ" ) ? 'selected="selected"' : null  ?> value="SZ">SZ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "TC" ) ? 'selected="selected"' : null  ?> value="TC">TC</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "TD" ) ? 'selected="selected"' : null  ?> value="TD">TD</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "TF" ) ? 'selected="selected"' : null  ?> value="TF">TF</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "TG" ) ? 'selected="selected"' : null  ?> value="TG">TG</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "TH" ) ? 'selected="selected"' : null  ?> value="TH">TH</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "TJ" ) ? 'selected="selected"' : null  ?> value="TJ">TJ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "TK" ) ? 'selected="selected"' : null  ?> value="TK">TK</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "TL" ) ? 'selected="selected"' : null  ?> value="TL">TL</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "TM" ) ? 'selected="selected"' : null  ?> value="TM">TM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "TN" ) ? 'selected="selected"' : null  ?> value="TN">TN</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "TO" ) ? 'selected="selected"' : null  ?> value="TO">TO</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "TR" ) ? 'selected="selected"' : null  ?> value="TR">TR</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "TT" ) ? 'selected="selected"' : null  ?> value="TT">TT</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "TV" ) ? 'selected="selected"' : null  ?> value="TV">TV</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "TW" ) ? 'selected="selected"' : null  ?> value="TW">TW</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "TZ" ) ? 'selected="selected"' : null  ?> value="TZ">TZ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "UA" ) ? 'selected="selected"' : null  ?> value="UA">UA</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "UG" ) ? 'selected="selected"' : null  ?> value="UG">UG</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "UK" ) ? 'selected="selected"' : null  ?> value="UK">UK</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "UM" ) ? 'selected="selected"' : null  ?> value="UM">UM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "US" ) ? 'selected="selected"' : null  ?> value="US">US</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "UY" ) ? 'selected="selected"' : null  ?> value="UY">UY</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "UZ" ) ? 'selected="selected"' : null  ?> value="UZ">UZ</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "VA" ) ? 'selected="selected"' : null  ?> value="VA">VA</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "VC" ) ? 'selected="selected"' : null  ?> value="VC">VC</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "VE" ) ? 'selected="selected"' : null  ?> value="VE">VE</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "VG" ) ? 'selected="selected"' : null  ?> value="VG">VG</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "VI" ) ? 'selected="selected"' : null  ?> value="VI">VI</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "VN" ) ? 'selected="selected"' : null  ?> value="VN">VN</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "VU" ) ? 'selected="selected"' : null  ?> value="VU">VU</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "WF" ) ? 'selected="selected"' : null  ?> value="WF">WF</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "WS" ) ? 'selected="selected"' : null  ?> value="WS">WS</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "YE" ) ? 'selected="selected"' : null  ?> value="YE">YE</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "YT" ) ? 'selected="selected"' : null  ?> value="YT">YT</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "ZA" ) ? 'selected="selected"' : null  ?> value="ZA">ZA</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "ZM" ) ? 'selected="selected"' : null  ?> value="ZM">ZM</option>
					<option <?= (!empty($guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"]) && $guue[$replaced_key]["COUNTRY"]["ATTRIBUTE"]["VALUE"] == "ZW" ) ? 'selected="selected"' : null  ?> value="ZW">ZW</option>
				</select>
			</td>
		</tr>
		<tr>
			<?
			if(!in_array("CONTACT_POINT", $excluded_input)) {
				?>
				<td class="etichetta">
					Persona di contatto:
				</td>
				<td colspan="5">
					<input type="text" style="font-size: 1.1em" title="Persona di contatto" class="espandi" name="guue<?= $keys ?>[CONTACT_POINT]" <?= !empty($guue[$replaced_key]["CONTACT_POINT"]) ? 'value="'.$guue[$replaced_key]["CONTACT_POINT"].'"' : null  ?> rel="<?= isRequired($prefix."CONTACT_POINT") ?>;0;300;A">
				</td>
				<?
			}
			if (in_array("E_MAIL", $added_input)) {
				?>
				<td class="etichetta">
					E-mail:
				</td>
				<td colspan="5">
					<input type="text" style="font-size: 1.1em" title="Persona di contatto" class="espandi" name="guue<?= $keys ?>[E_MAIL]" <?= !empty($guue[$replaced_key]["E_MAIL"]) ? 'value="'.$guue[$replaced_key]["E_MAIL"].'"' : null  ?> rel="<?= isRequired($prefix."E_MAIL") ?>;0;300;E">
				</td>
				<?
			}
			?>
			<td class="etichetta">
				Tel.:
			</td>
			<td <? if (!in_array("E_MAIL_1", $excluded_input)) echo 'colspan="7"' ?>>
				<input type="text" style="font-size: 1.1em" title="+39 12345678" class="espandi" name="guue<?= $keys ?>[PHONE]" <?= !empty($guue[$replaced_key]["PHONE"]) ? 'value="'.$guue[$replaced_key]["PHONE"].'"' : null  ?> rel="<?= isRequired($prefix."PHONE") ?>;0;100;A;check_phone.php">
			</td>
		</tr>
		<tr>
			<?
			if(!in_array("E_MAIL_1", $excluded_input)) {
				?>
				<td class="etichetta">
					E-mail:
				</td>
				<td colspan="7">
					<input type="text" style="font-size: 1.1em" title="E-mail" name="guue<?= $keys ?>[E_MAIL]" <?= !empty($guue[$replaced_key]["E_MAIL"]) ? 'value="'.$guue[$replaced_key]["E_MAIL"].'"' : null  ?> rel="<?= isRequired($prefix."E_MAIL") ?>;6;200;E" class="espandi">
				</td>
				</tr>
				<tr>
				<?
			}
			if ($prefix == "ADDRS6-" || $prefix == "ADDRS7-") {
				?>
				<td class="etichetta">
					Fax:
				</td>
				<td <? if(!in_array("URL", $added_input)) echo 'colspan="7"'; ?>>
					<input type="text" style="font-size: 1.1em" title="Fax" class="espandi" name="guue<?= $keys ?>[FAX]" <?= !empty($guue[$replaced_key]["FAX"]) ? 'value="'.$guue[$replaced_key]["FAX"].'"' : null  ?> rel="<?= isRequired($prefix."FAX") ?>;0;100;A;check_phone.php">
				</td>
				<?
				if(in_array("URL", $added_input)) {
					?>
					<td class="etichetta">
							Indirizzo principale <i>(URL)</i>
						</td>
						<td colspan="5">
							<input type="text" style="font-size: 1.1em" title="URL indirizzo principale" class="espandi" name="guue<?= $keys ?>[URL]" <?= !empty($guue[$replaced_key]["URL"]) ? 'value="'.$guue[$replaced_key]["URL"].'"' : null  ?> rel="<?= isRequired($prefix."URL") ?>;1;200;L">
						</td>
					<?
				}
			} else {
					if(in_array("URL", $added_input)) {
					?>
					<td class="etichetta">
							Indirizzo principale <i>(URL)</i>
						</td>
						<td colspan="5">
							<input type="text" style="font-size: 1.1em" title="URL indirizzo principale" class="espandi" name="guue<?= $keys ?>[URL]" <?= !empty($guue[$replaced_key]["URL"]) ? 'value="'.$guue[$replaced_key]["URL"].'"' : null  ?> rel="<?= isRequired($prefix."URL") ?>;1;200;L">
						</td>
					<?
				}
				?>
				<td class="etichetta">
					Fax:
				</td>
				<td>
					<input type="text" style="font-size: 1.1em" title="Fax" class="espandi" name="guue<?= $keys ?>[FAX]" <?= !empty($guue[$replaced_key]["FAX"]) ? 'value="'.$guue[$replaced_key]["FAX"].'"' : null  ?> rel="<?= isRequired($prefix."FAX") ?>;0;100;A;check_phone.php">
				</td>
				<?
			}
			?>
		</tr>
		<?
			if(!in_array("NUTS", $excluded_input)) {
				?>
				<tr>
					<td class="etichetta">
						Codice NUTS:
					</td>
					<td colspan="7">
						<select name="guue<?= $keys ?>[NUTS][ATTRIBUTE][CODE]" rel="<?= isRequired($prefix."NUTS") ?>;1;0;A" title="Codice Nuts">
							<option value="">Seleziona...</option>
							<?
							$sql_nuts = "SELECT * FROM b_nuts ORDER BY descrizione";
							$ris_nuts = $pdo->query($sql_nuts);
							if ($ris_nuts->rowCount() > 0)
							{
								while ($nuts = $ris_nuts->fetch(PDO::FETCH_ASSOC)) {
									?>
									<option value="<?= $nuts["nuts"] ?>" <?= (!empty($guue[$replaced_key]["NUTS"]["ATTRIBUTE"]["CODE"]) && $guue[$replaced_key]["NUTS"]["ATTRIBUTE"]["CODE"] == $nuts["nuts"]) ? 'selected="selected"' : null  ?> ><?= $nuts["nuts"] ?> - <?= $nuts["descrizione"] ?> <? if (!empty($nuts["data_fine_validita"])) { ?> - scadenza: <?= mysql2date($nuts["data_fine_validita"]) ?><? } ?></option>
									<?
								}
							}
							?>
						</select>
					</td>
				</tr>
				<?
				if(in_array("URL_AFTER_NUTS", $added_input)) {
					?>
						<tr>
							<td class="etichetta">
								Indirizzo principale <i>(URL)</i>
							</td>
							<td colspan="7">
								<input type="text" style="font-size: 1.1em" title="URL indirizzo principale" class="espandi" name="guue<?= $keys ?>[URL]" <?= !empty($guue[$replaced_key]["URL"]) ? 'value="'.$guue[$replaced_key]["URL"].'"' : null  ?> rel="<?= isRequired($prefix."URL") ?>;1;200;L">
							</td>
						</tr>
					<?
				}
			}

			if (!in_array("URL_GENERAL", $excluded_input)) {
				?>
				<tr>
					<td colspan="8"><b>Indirizzi Internet</b></td>
				</tr>
				<tr>
					<td class="etichetta">
						Indirizzo principale <i>(URL)</i>
					</td>
					<td colspan="7">
						<input type="text" style="font-size: 1.1em" title="URL indirizzo principale" class="espandi" name="guue<?= $keys ?>[URL_GENERAL]" <?= !empty($guue[$replaced_key]["URL_GENERAL"]) ? 'value="'.$guue[$replaced_key]["URL_GENERAL"].'"' : null  ?> rel="<?= isRequired($prefix."URL_GENERAL") ?>;1;200;L">
					</td>
				</tr>
				<?
			}

			if (!in_array("URL_BUYER", $excluded_input)) {
				?>
				<tr>
					<td class="etichetta">
						Indirizzo del profilo di committente: <i>(URL)</i>
					</td>
					<td colspan="7">
						<input type="text" style="font-size: 1.1em" title="URL Indirizzo del profilo di committente" class="espandi" name="guue<?= $keys ?>[URL_BUYER]" <?= !empty($guue[$replaced_key]["URL_BUYER"]) ? 'value="'.$guue[$replaced_key]["URL_BUYER"].'"' : null  ?> rel="<?= isRequired($prefix."URL_BUYER") ?>;0;200;L">
					</td>
				</tr>
				<?
			}
		?>
	</tbody>
	<?
	if(!empty($address_item)) {
		?>
		<tfoot>
			<tr>
				<td colspan="8">
					<button type="button" onclick="$('#address_item_<?= $address_item ?>').remove();" class="submit_big" style="background-color: #CC0000; color: #FFF;">ELIMINA INFORMAZIONI ADDIZIONALI</button>
				</td>
			</tr>
		</tfoot>
		<?
	}
	if(empty($do_not_close_table)) { ?></table><? }
	?>
<input type="hidden" name="keys_to_replace[]" id="input" class="form-control" value="<?= str_replace(array('[',']'), array('',';'), $keys) ?>">
