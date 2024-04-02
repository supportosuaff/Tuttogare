<?
if (isset($_POST["id"])) {
	session_start();
	include("../../../config.php");
	include_once($root."/inc/funzioni.php");
;
	$record = get_campi("b_conf_modalita_stipula");
	$id = $_POST["id"];
}
$colore = "#3C0";
if ($record["attivo"] == "N") { $colore = "#C00"; }
?>
<tr id="modalita_<? echo $id ?>">
	<td width="1" id="flag_<? echo $id ?>" style="background-color: <? echo $colore ?>"></td>
	<td>
		<input type="hidden" name="modalita[<? echo $id ?>][codice]" id="codice_modalita_<? echo $id ?>" value="<? echo $record["codice"] ?>">
		<input type="text" class="titolo_edit" name="modalita[<? echo $id ?>][etichetta]"  title="Etichetta" rel="S;3;255;A" id="etichetta_modalita_<? echo $id ?>" value="<? echo $record["etichetta"] ?>">
		<table width="100%" class="_dettaglio">
			<tr>
				<th class="etichetta">
					<span style="color:#000">Invio per firma remota</span>
				</th>
				<td>
					<select name="modalita[<? echo $id ?>][invio_remoto]" rel="S;1;0;A" title="Invio Remoto">
						<option value="">Seleziona..</option>
						<option <?= $record["invio_remoto"] == "S" ? 'selected="selected"' : null ?> value="S">Si</option>
						<option <?= $record["invio_remoto"] == "N" ? 'selected="selected"' : null ?> value="N">No</option>
					</select>
				</td>
				<th class="etichetta">
					<span style="color:#000">Ufficiale Rogante</span>
				</th>
				<td>
					<select name="modalita[<? echo $id ?>][ufficiale_rogante]" rel="S;1;0;A" title="Invio Remoto">
						<option value="">Seleziona..</option>
						<option <?= $record["ufficiale_rogante"] == "S" ? 'selected="selected"' : null ?> value="S">Si</option>
						<option <?= $record["ufficiale_rogante"] == "N" ? 'selected="selected"' : null ?> value="N">No</option>
					</select>
				</td>
			</tr>
		</table>
	</td>
	<td width="10"><button class="btn-round btn-warning" onClick="$('#modalita_<? echo $id ?> ._dettaglio').toggle(); return false"><span class="fa fa-search"></span></button></td>
	<td width="10"><button class="btn-round btn-default" onClick="disabilita('<? echo $id ?>','impostazioni/impostazioni_contratto');return false;" title="Abilita/Disabilita">
	<span class="fa fa-refresh"></span>
	</button></td>
	<td width="10"><button class="btn-round btn-danger" onClick="elimina('<? echo $id ?>','impostazioni/impostazioni_contratto');return false;" title="Elimina">
		<span class="fa fa-remove"></span>
	</button></td>
</tr>
