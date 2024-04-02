<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	if (!is_operatore()) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
	} else {
?>
<h1>DGUE - DOCUMENTO DI GARA UNICO EUROPEO</h1>
<?
		$bind = array();
		$bind[":codice_utente"] = $_SESSION["codice_utente"];
		$sql = "SELECT * FROM b_dgue_compilati WHERE codice_utente = :codice_utente ORDER BY timestamp DESC";
		$ris_model = $pdo->bindAndExec($sql,$bind);
		if ($ris_model->rowCount() > 0) {
	?>

		<table width="100%" class="elenco">
			<thead>
				<tr>
					<td width="10%">Identificativo</td>
					<td width="50%">Oggetto</td>
					<td width="20%">Ente</td>
					<td width="15%">Data</td>
					<td width="10">PDF</td>
					<td width="10">XML</td>
					<td width="10">Elimina</td>
				</tr>
			</thead>
			<tbody>
    <?
		while($modello = $ris_model->fetch(PDO::FETCH_ASSOC)) {
			$model = json_decode($modello["json"],true);
			?>
			<tr>
				<td><?= $model["cbc:ContractFolderID"]['$'] ?></td>
				<td><?= $model["cac:AdditionalDocumentReference"][1]["cac:Attachment"]["cac:ExternalReference"]["cbc:FileName"] ?></td>
				<td><?= $model["cac:ContractingParty"]["cac:Party"]["cac:PartyName"]["cbc:Name"] ?></td>
				<td><?= mysql2datetime($modello["timestamp"]) ?></td>
				<td><a href="getPDF.php?codice_dgue=<?= $modello["codice"] ?>" title="Anteprima PDF"><span class='fa fa-file-pdf-o fa-2x'></span></a></td>
				<td><a href="getXML.php?codice_dgue=<?= $modello["codice"] ?>" title="Download XML"><span style="color:#066" class='fa fa-code fa-2x'></span></a></td>
				<td style="text-align:center"><input type="image" onClick="elimina('<? echo $modello["codice"] ?>','dgue');" src="/img/del.png" title="Elimina"></td>
			</tr>
			<?
		}
	?>
    	</tbody>
    </table>
    <div class="clear"></div>
<?php
		} else { ?>
			<h1 style="text-align:center">Nessun documento in archivio</h1>
  <? }
	}
	include_once($root."/layout/bottom.php");
	?>
