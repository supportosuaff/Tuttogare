<?
	if (isset($record_iscritto)) {
	$class = "";
	if ($record_iscritto["interno"]=="S") { $class .= " interno"; } else { $class .= " esterno"; }
?>
<div id="iscritto_<? echo $id ?>" class="box iscritto_view <?= $class ?>" style="border-left:5px solid #999;">

		<table width="100%" id="tabella_<? echo $id ?>">
			<tr>
				<td class="etichetta">Codice Fiscale</td>
				<td class="etichetta">Cognome</td>
				<td class="etichetta">Nome</td>
				<td class="etichetta">Interno</td>
				<td rowspan="3" width="10" style="text-align:center"><input type="image" onClick="edit_iscritto(<?= $id ?>);return false;" src="/img/edit.png" title="Modifica"></td>
			</tr>
			<tr>
				<td width="20%">
	        <? echo $record_iscritto["codice_fiscale"] ?>
				</td>
				<td width="35%">
					<? echo $record_iscritto["cognome"] ?>
				</td>
				<td width="35%">
					<? echo $record_iscritto["nome"] ?>
				</td>
				<td width="10%" style="text-align:center">
					<?= $record_iscritto["interno"] ?>
				</td>
			</tr>
			<tr>
				<td colspan="4">
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
								<? echo $record_iscritto["telefono"] ?>
							</td>
							<td>
								<? echo $record_iscritto["email"] ?>
							</td>
							<td>
								<? echo $record_iscritto["fax"] ?>
							</td>
							<td>
								<? echo $record_iscritto["indirizzo"] ?>
							</td>
							<td>
								<? echo $record_iscritto["comune"] ?>
							</td>
							<td>
								<? echo $record_iscritto["cap"] ?>
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
