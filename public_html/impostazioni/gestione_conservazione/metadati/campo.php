<?
	if(!empty($_POST["id"]) && empty($record_campo)) {
		session_start();
		include("../../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record_campo = get_campi("b_schema_metadati");
		$record_campo["tipo"] = "";
    $record_campo["codice"] = 0;
    $record_campo["codice_ente"] = "abc";
		$id_campo = $_POST["id"];
	} else {
		$id_campo = $record_campo["codice"];
	}
  $tipologie = array(
    'A' => 'Alfanumerico',
    'N' => 'Numerico',
    'D' => 'Data',
    'T' => 'Ora',
    'DT' => 'Data e ora',
  );
	if(!empty($id_campo)) {
		?>
		<div id="campo_<?= $id_campo ?>" class="box edit-box" style="border-left:5px solid <?= is_numeric($record_campo["codice_ente"]) && $record_campo["codice_ente"] == 0 && isset($_SESSION["ente"]) ? '#f44336' : '#999' ?>;">
			<table width="100%" id="tabella_campo_<?= $id_campo ?>">
				<tbody>
					<tr>
						<td <? if($record_campo["codice_ente"] == 0 && $_SESSION["gerarchia"] > 0) echo 'colspan="2"'; ?>>
              <input type="hidden" name="campo[<?= $id_campo ?>][id]" id="id_campo_<?= $id_campo ?>" value="<?= $id_campo ?>">
	            <input type="hidden" name="campo[<?= $id_campo ?>][codice]" id="codice_campo_<?= $id_campo ?>" value="<?= $record_campo["codice"] ?>">
              <? if(($record_campo["codice_ente"] != 0 && $_SESSION["gerarchia"] <= 1) || ($record_campo["codice_ente"] == 0 && $_SESSION["gerarchia"] === "0") || $record_campo["codice_ente"] == $_SESSION["record_utente"]["codice_ente"] || !is_numeric($record_campo["codice_ente"])) {?>
                <?= is_numeric($record_campo["codice_ente"]) && $record_campo["codice_ente"] == 0 && isset($_SESSION["ente"]) ? '<b>CAMPO CONFIG GENERALE</b><br>' : null ?>
                <input type="text" style="font-weight:bold;" name="campo[<?= $id_campo ?>][etichetta]"  title="Etichetta Campo" rel="S;3;255;A" id="etichetta_campo_<?= $id_campo ?>" value="<?= $record_campo["etichetta"] ?>">
                <input type="text" name="campo[<?= $id_campo ?>][descrizione]"  title="Descrizione" rel="N;1;0;A" id="descrizione_campo_<?= $id_campo ?>" value="<?= $record_campo["descrizione"] ?>">
              <? } else { ?>
                <p><span style="font-weight:bold; font-size: 1em"><?= $record_campo["etichetta"] ?></span><br><span><?= $record_campo["descrizione"] ?></span></p>
              <? } ?>
              <table width="100%" class="_dettaglio" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td class="etichetta" style="width:10%">Obbligatorio: </td>
                  <td style="width:40%">
                    <? if(($record_campo["codice_ente"] != 0 && $_SESSION["gerarchia"] <= 1) || ($record_campo["codice_ente"] == 0 && $_SESSION["gerarchia"] === "0") || $record_campo["codice_ente"] == $_SESSION["record_utente"]["codice_ente"] || !is_numeric($record_campo["codice_ente"])) {?>
          					<select name="campo[<?= $id_campo ?>][obbligatorio]" title="Obbligatorio" rel="S;1;1;A" id="obbligatorio_campo_<?= $id_campo ?>" >
                      <option <?= $record_campo["obbligatorio"] == "N" ? 'selected="selected"' : null; ?> value="N">No</option>
          						<option <?= $record_campo["obbligatorio"] == "S" ? 'selected="selected"' : null; ?> value="S">Si</option>
          					</select>
                    <? } else { echo $record_campo["descrizione"] == "N" ? 'No' : 'Si'; } ?>
          				</td>
          				<td class="etichetta" style="width:10%">Tipologia: </td>
          				<td style="width:40%">
                    <? if(($record_campo["codice_ente"] == 0 && $_SESSION["gerarchia"] === "0") || $record_campo["codice_ente"] == $codice_ente || !is_numeric($record_campo["codice_ente"])) { ?>
          					<select name="campo[<?= $id_campo ?>][tipologia]" title="Tipologia" rel="S;1;1;A" id="tipologia_bando_<?= $id_campo ?>" >
          						<option>Seleziona..</option>
                      <?
                      foreach ($tipologie as $val => $label) {
                        ?><option <?= $record_campo["tipologia"] == $val ? 'selected="selected"' : null ?> value="<?= $val ?>"><?= $label ?></option><?
                      }
                      ?>
          					</select>
                    <? } else { echo $tipologie[$record_campo["tipologia"]]; } ?>
          				</td>
                </tr>
              </table>
						</td>
            <td width="10"><button class="btn-round btn-warning" onClick="$('#tabella_campo_<?= $id_campo ?> ._dettaglio').toggle(); return false"><span class="fa fa-search"></span></button></td>
            <? if(($record_campo["codice_ente"] != 0 && $_SESSION["gerarchia"] <= 1) || ($record_campo["codice_ente"] == 0 && $_SESSION["gerarchia"] === "0") || $record_campo["codice_ente"] == $_SESSION["record_utente"]["codice_ente"] || !is_numeric($record_campo["codice_ente"])) {?>
						<td width="10"><button class="btn-round btn-danger" onClick="elimina('<?= $id_campo ?>','impostazioni/gestione_conservazione/metadati');return false;" title="Elimina">
							<span class="fa fa-remove"></span>
						</button></td>
						<?} ?>
					</tr>
				</tbody>
			</table>
		</div>
		<?
	}
?>
