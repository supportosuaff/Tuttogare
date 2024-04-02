<?
	if (isset($record_incarico)) {
		$ruoli = getListeSIMOG()["RuoloResponsabileType"];
?>
<div id="incarico_<? echo $id ?>" class="box" style="border-left:5px solid #999;">

		<table width="100%" id="tabella_<? echo $id ?>">
			<tr>
				<td class="etichetta">Ruolo</td>
				<td colspan="4">
					<?= $ruoli[$record_incarico["ruolo"]] ?>
				</td>
				<? if (!$lock) { ?>
					<td rowspan="4" width="10" style="text-align:center"><input type="image" onClick="edit_incarico(<?= $id ?>);return false;" src="/img/edit.png" title="Modifica"></td>
				<? } ?>
			</tr>
			<tr>
				<td class="etichetta">Codice Fiscale</td>
				<td class="etichetta">Cognome</td>
				<td class="etichetta">Nome</td>
				<td class="etichetta">Atto</td>
				<td class="etichetta">Data</td>
			</tr>
			<tr>
				<td>
	        <? echo $record_incarico["codice_fiscale"] ?>
				</td>
				<td>
					<? echo $record_incarico["cognome"] ?>
				</td>
				<td>
					<? echo $record_incarico["nome"] ?>
				</td>
				<td>
					<?= $record_incarico["numero_atto"] ?>
				</td>
				<td>
					<?= mysql2date($record_incarico["data_atto"]) ?>
				</td>
			</tr>
			<tr>
				<td colspan="5">
					<table width="100%">
						<tr>
							<td class="etichetta">Telefono</td>
							<td class="etichetta">E-mail</td>
							<td class="etichetta">Fax</td>
							<td class="etichetta">Indirizzo</td>
							<td class="etichetta">Comune</td>
							<td class="etichetta">CAP</td>
						</tr>
						<tr>
							<td>
								<? echo $record_incarico["telefono"] ?>
							</td>
							<td>
								<? echo $record_incarico["email"] ?>
							</td>
							<td>
								<? echo $record_incarico["fax"] ?>
							</td>
							<td>
								<? echo $record_incarico["indirizzo"] ?>
							</td>
							<td>
								<? echo $record_incarico["comune"] ?>
							</td>
							<td>
								<? echo $record_incarico["cap"] ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
</table>
  </div>
<?
}
?>
