<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_gestione_guue");
		$id = $_POST["id"];
	}
	$colore = "#3C0";
	if ($record["attivo"] == "N") { $colore = "#C00"; }
	?>
	<tr id="opzione_<? echo $id ?>">
		<td class="handle" style="background:#AAA" width="20">
		<td id="flag_<?= $id ?>" style="background:<?= $colore ?>" width="1px">
		<td>
			<input type="hidden" name="opzione[<? echo $id ?>][codice]"id="codice_opzione_<? echo $record["codice"] ?>" value="<? echo $record["codice"]  ?>">
			<input type="text" class="titolo_edit" name="opzione[<? echo $id ?>][titolo]"  title="titolo" rel="S;3;255;A" id="titolo_opzione_<? echo $id ?>" value="<? echo $record["titolo"] ?>">
			<input type="hidden" name="opzione[<? echo $id ?>][ordinamento]"id="ordinamento_opzione_<? echo $record["codice"] ?>" class="ordinamento" value="<? echo $record["ordinamento"]  ?>">
			<table width="100%" class="_dettaglio">
				<tr>
					<td class="etichetta">Fase minima</td>
					<td>
						<select name="opzione[<? echo $id ?>][fase_minima]" id="fase_minima_opzione_<? echo $id ?>" rel="S;0;0;N" title="Fase minima">
							<option value="0">Nessuna</option>
							<?
								$sql = "SELECT * FROM b_stati_gare ORDER BY fase";
								$ris = $pdo->query($sql);
								if ($ris->rowCount()>0) {
									while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
										?><option value="<? echo $rec["fase"] ?>"><? echo $rec["titolo"] ?></option><?
									}
								}
							?>
		        </select>
		      </td>
		      <td class="etichetta">Fase massima</td>
		      <td>
		      	<select name="opzione[<? echo $id ?>][fase_massima]" id="fase_massima_opzione_<? echo $id ?>" rel="S;0;0;N" title="Fase massima">
		      		<option value="0">Nessuna</option>
		      		<?
			      		$sql = "SELECT * FROM b_stati_gare ORDER BY fase";
					   		$ris = $pdo->query($sql);
								if ($ris->rowCount()>0) {
									while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
										?><option value="<? echo $rec["fase"] ?>"><? echo $rec["titolo"] ?></option><?
									}
								}
							?>
						</select>
					</td>
					<td class="etichetta">Tipologie</td>
					<td colspan="3">
						<select name="opzione[<? echo $id ?>][stati_esclusi][]" multiple id="stati_esclusi_opzione_<? echo $id ?>" rel="N;0;0;ARRAY" title="Stati esclusi">
							<option value="">Nessuna</option>
							<?
								$sql = "SELECT * FROM b_tipologie WHERE attivo = 'S' ORDER BY tipologia";
					   		$ris = $pdo->query($sql);
								if ($ris->rowCount()>0) {
									while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
										?><option value="<? echo $rec["codice"] ?>"><? echo $rec["tipologia"] ?></option><?
									}
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<!-- <td class="etichetta">Modulo di riferimento</td>
					<td colspan="3">
						<select  name="opzione[<? echo $id ?>][modulo_riferimento]" title="Modulo riferimento" rel="S;0;0;A" id="modulo_riferimento_opzione_<? echo $id ?>">
						<?
							$sql = "SELECT * FROM b_moduli ORDER BY ordinamento";
				   		$ris = $pdo->query($sql);
							if ($ris->rowCount()>0) {
								while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
									?><option value="<? echo $rec["radice"] ?>"><? echo $rec["titolo"] ?></option><?
								}
							}
						?>
						</select>
					</td> -->
					<td class="etichetta">Modalita</td>
				 	<td colspan="3">
				 		<select name="opzione[<? echo $id ?>][modalita][]" multiple id="modalita_opzione_<? echo $id ?>" rel="N;0;0;A" title="Modalita">
				 			<option value="0">Tutte</option>
				 			<?
								$sql = "SELECT * FROM b_modalita ORDER BY codice";
								$ris = $pdo->query($sql);
								if ($ris->rowCount()>0) {
									while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
										?><option value="<? echo $rec["codice"] ?>"><? echo $rec["modalita"] ?></option><?
									}
								}
							?>
				 		</select>
				 	</td>
				 	<td class="etichetta">Form:</td>
				 	<td>
				 		<input type="text" name="opzione[<? echo $id ?>][form]"  title="Form" rel="S;3;255;A" value="<? echo $record["form"] ?>">
				 	</td>
				</tr>
			</table>
		</td>
		<td width="10"><button class="btn-round btn-warning" onClick="$('#opzione_<? echo $id ?> ._dettaglio').toggle(); return false"><span class="fa fa-search"></span></button></td>
		<td width="10"><button class="btn-round btn-default" onclick="disabilita('<?= $id ?>','impostazioni/gestione_guue');return false;" title="Abilita/Disabilita" placeholder="Abilita/Disabilita"><span class="fa fa-refresh"></span></button></td>
		<td width="10"><button class="btn-round btn-danger" onClick="elimina('<?= $id ?>','impostazioni/gestione_guue');return false;" title="Elimina"><span class="fa fa-remove"></span></button></td>
	</tr>
	<?
	if (!isset($_POST["id"])) {
		?>
		<script>
			$("#fase_minima_opzione_<? echo $id ?>").val('<? echo $record["fase_minima"] ?>');
			$("#fase_massima_opzione_<? echo $id ?>").val('<? echo $record["fase_massima"] ?>');
			var modalita = "<? echo $record["modalita"] ?>";
			$("#modalita_opzione_<? echo $id ?>").val(modalita.split(","));
			$("#tipo_opzione_<? echo $id ?>").val('<? echo $record["tipo"] ?>');
		</script>
		<?
	}
?>
