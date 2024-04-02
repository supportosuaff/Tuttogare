<?
	if (isset($record_partecipante)) {
		$showArt80 = true;
		
?>
<div id="partecipante_<? echo $id_capogruppo ?>" class="box" style="border-left:5px solid #999;">
	<table width="100%">
	<tr>
		<td width="10"><strong><?= $cont_partecipante ?></strong></td>
		<td>
	    <table align="right">
				<tr>
					<td class="etichetta">Protocollo</td><td>
	     <? echo $record_partecipante["numero_protocollo"] ?> del <? echo mysql2date($record_partecipante["data_protocollo"]) ?></td></tr></table>
		<table width="100%" id="tabella_<? echo $id_capogruppo ?>">
	    	<thead>
					<tr>
						<td style="width:15%">Codice Fiscale Impresa</td>
						<td style="width:40%">Ragione sociale</td>
						<td style="width:15%">Pec</td>
						<td style="width:15%">Identificativo Estero</td>
						<td style="width:10%">Ruolo</td>
						<td style="width:5%" colspan="2"></td>
					</tr>
				</thead>
			<tr style="font-weight:bold">
				<td width="10"><? echo $record_partecipante["partita_iva"] ?></td>
				<td>
					<?
						if (!isset($art80)) $art80 = (check_permessi("verifica-art-80", $_SESSION["codice_utente"])) ? true : false;
						$check80 = checkStatoArt80($record_partecipante["partita_iva"]);
						if ($check80 != false) {
							echo '<div class="status_indicator" style="float:left; margin-right:5px; background-color: ' .$check80["color"]  .'"></div>';	
						}
						echo $record_partecipante["ragione_sociale"];
						if (!empty($art80) && !empty($record_partecipante["codice_operatore"]) && $showArt80) { ?><div style="text-align:right"><a href="#" onClick="sendArt80Request('<?= $record_partecipante["codice_operatore"] ?>')" title="Richiedi verifica art.80">Verifica Articolo 80</a></div><? } ?>
				</td>
				<td><? echo $record_partecipante["pec"] ?></td>
	    	<td width="150"><? echo $record_partecipante["identificativoEstero"] ?></td>
				<td width="150"><?= $record_partecipante["tipo"] ?></td>
				<td width="10">
					<? if (!$lock) { ?>
					<input type="image" onClick="edit_partecipante(<?= $id_capogruppo ?>);return false;" src="/img/edit.png" title="Modifica">
					<? } ?>
				</td>
	    </tr>
			<? if ($record_partecipante["tipo"] == "04-CAPOGRUPPO") {
				$bind = array();
				$bind[":codice"] = $record["codice"];
				$bind[":codice_capogruppo"] = $record_partecipante["codice"];
				$sql = "SELECT * FROM {$table} WHERE codice_gara = :codice AND codice_capogruppo = :codice_capogruppo";
				$ris_membri = $pdo->bindAndExec($sql,$bind);
				if ($ris_membri->rowCount()>0) {
					while ($record_membro = $ris_membri->fetch(PDO::FETCH_ASSOC)) {
						?>
						<tr>
							<td width="10"><? echo $record_membro["partita_iva"] ?></td>
								<td>
									<?
										$check80 = checkStatoArt80($record_membro["partita_iva"]);
										if ($check80 != false) {
											echo '<div class="status_indicator" style="float:left; margin-right:5px; background-color: ' .$check80["color"]  .'"></div>';	
										}
										echo $record_membro["ragione_sociale"];
										if (!empty($art80) && !empty($record_membro["codice_operatore"])) { ?><div style="text-align:right"><a href="#" onClick="sendArt80Request('<?= $record_membro["codice_operatore"] ?>')" title="Richiedi verifica art.80">Verifica Articolo 80</a></div><? } ?>		
								</td>
								<td width="10"><? echo $record_membro["identificativoEstero"] ?></td>
								<td width="150" colspan="2"><?= $record_membro["tipo"] ?></td>
							</tr>
						<?
					}
				}
			}
			?>
		</table>
	</td>
</tr>
</table>
  </div>
<?
}
?>
