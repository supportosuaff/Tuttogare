<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_moduli");
		$id = $_POST["id"];

	}

	$colore = "#3C0";
	if ($record["attivo"] == "N") { $colore = "#C00"; }

?><div id="moduli_<? echo $id ?>" class="box">
	<table width="100%">
		<tr>
			<td rowspan="4" width="1%" id="flag_<? echo $id ?>" style="background-color: <? echo $colore ?>"></td>
			<th colspan="5"  width="50%">Descrizione</th>
			<th width="10%">Radice</th>
			<th width="10%">Glyph</th>
			<th width="10%">Gerarchia</th>
			<th width="2%">Attiva / Disattiva</th>
			<th width="2%">Elimina</th>
		</tr>
		<tr>
			<td colspan="5" width="50%"><input type="hidden" name="moduli[<? echo $id ?>][codice]" id="codice_moduli_<? echo $id ?>" value="<? echo $record["codice"]  ?>">
				<input type="text" class="titolo_edit" name="moduli[<? echo $id ?>][titolo]"  title="Titolo" rel="S;3;255;A" id="titolo_moduli_<? echo $id ?>" value="<? echo $record["titolo"] ?>"><br>
				<input type="hidden" name="moduli[<? echo $id ?>][ordinamento]"id="ordinamento_moduli_<? echo $record["codice"] ?>" class="ordinamento" value="<? echo $record["ordinamento"]  ?>">
				<input type="text" style="width:98%" name="moduli[<? echo $id ?>][descrizione]"  title="Descrizione" rel="S;3;255;A" id="descrizione_moduli_<? echo $id ?>" value="<? echo $record["descrizione"] ?>">
			</td>
			<td><input type="text" size="15" name="moduli[<? echo $id ?>][radice]"  title="Radice" rel="S;3;255;A" id="radice_moduli_<? echo $id ?>" value="<? echo $record["radice"] ?>"></td>
			<td><input type="text" size="15" name="moduli[<? echo $id ?>][glyph]"  title="Glyph" rel="N;3;255;A" id="glyph_moduli_<? echo $id ?>" value="<? echo $record["glyph"] ?>"></td>
			<td>
					<select name="moduli[<? echo $id ?>][gerarchia]" title="Gerarchia" rel="S;0;0;N" id="gerarchia_moduli_<? echo $id ?>">
						<option value="">Seleziona...</option>
						<?
							$sql = "SELECT b_gruppi.* FROM b_gruppi GROUP BY gerarchia ORDER BY gerarchia";
							$ris = $pdo->query($sql);
							if ($ris->rowCount()>0) {
								while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
									?>
									<option value="<? echo $rec["gerarchia"] ?>"><? echo $rec["gruppo"] ?></option>
									<?
								}
							}
						?>
					</select>
			</td>
			<td style="text-align:center" rowspan="3" width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/moduli');return false;" src="/img/switch.png" title="Abilita/Disabilita"></td>
			<td style="text-align:center" rowspan="3" width="10"><input type="image" onClick="elimina('<? echo $id ?>','impostazioni/moduli');return false;" src="/img/del.png" title="Elimina"></td>
		</tr>
		<tr>
			<th>Ente</th>
			<th>Amministrazione</th>
			<th>Menu</th>
			<th>Registrazione</th>
			<th>Nascoto</th>
			<th>Tutti Enti</th>
			<th>Tutti Utenti</th>
			<th>Cross-platform</th>
		</tr>
		<tr>
			<td><select name="moduli[<? echo $id ?>][ente]" title="Ente" rel="S;1;1;A" id="ente_moduli_<? echo $id ?>">
			<option value="S">Si</option>
			<option value="N">No</option>
			</select></td>
			<td><select name="moduli[<? echo $id ?>][admin]" title="Amministrazione" rel="S;1;1;A" id="admin_moduli_<? echo $id ?>" >
			<option value="S">Si</option>
			<option value="N">No</option>
			</select></td>
			<td><select name="moduli[<? echo $id ?>][menu]" title="menu" rel="S;1;1;A" id="menu_moduli_<? echo $id ?>" >
			<option value="S">Si</option>
			<option value="N">No</option>
			</select></td>
			<td><select name="moduli[<? echo $id ?>][registrazione]" title="registrazione" rel="S;1;1;A" id="registrazione_moduli_<? echo $id ?>" >
			<option value="S">Si</option>
			<option value="N">No</option>
			</select></td>
			<td><select name="moduli[<? echo $id ?>][nascosto]" title="nascosto" rel="S;1;1;A" id="nascosto_moduli_<? echo $id ?>" >
			<option value="S">Si</option>
			<option value="N">No</option>
			</select></td>
			<td><select name="moduli[<? echo $id ?>][tutti_ente]" title="tutti_ente" rel="S;1;1;A" id="tutti_ente_moduli_<? echo $id ?>" >
			<option value="S">Si</option>
			<option value="N">No</option>
			</select></td>
			<td><select name="moduli[<? echo $id ?>][tutti_utente]" title="tutti_utente" rel="S;1;1;A" id="tutti_utente_moduli_<? echo $id ?>" >
			<option value="S">Si</option>
			<option value="N">No</option>
			</select></td>
			<td><select name="moduli[<? echo $id ?>][cross_p]" title="cross_p" rel="S;1;1;A" id="cross_p_moduli_<? echo $id ?>" >
			<option value="S">Si</option>
			<option value="N">No</option>
			</select></td>
		 </tr>
	 </table>
</div>
<?

	if (!isset($_POST["id"])) {
		?><script>
			$("#gerarchia_moduli_<? echo $id ?>").val('<? echo $record["gerarchia"] ?>');
			$("#ente_moduli_<? echo $id ?>").val('<? echo $record["ente"] ?>');
			$("#admin_moduli_<? echo $id ?>").val('<? echo $record["admin"] ?>');
			$("#menu_moduli_<? echo $id ?>").val('<? echo $record["menu"] ?>');
			$("#registrazione_moduli_<? echo $id ?>").val('<? echo $record["registrazione"] ?>');
			$("#nascosto_moduli_<? echo $id ?>").val('<? echo $record["nascosto"] ?>');
			$("#tutti_ente_moduli_<? echo $id ?>").val('<? echo $record["tutti_ente"] ?>');
			$("#tutti_utente_moduli_<? echo $id ?>").val('<? echo $record["tutti_utente"] ?>');
			$("#cross_p_moduli_<? echo $id ?>").val('<? echo $record["cross_p"] ?>');
		</script>
        <?
	}
?>
