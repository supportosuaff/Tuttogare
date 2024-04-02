<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("enti",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	echo "<h1>GESTIONE ENTI</h1>";
		$bind = array();
		$strsql = "";
		$strsql  = "SELECT b_enti.*, b_sua.dominio AS dominio_sua
								FROM b_enti
								LEFT JOIN b_enti AS b_sua ON b_enti.sua = b_sua.codice ";
		if (isset($_SESSION["ente"])) {
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql .= "WHERE b_enti.codice = :codice_ente OR b_enti.sua = :codice_ente ";
		}
		$strsql .= " ORDER BY attivo DESC ";
		$risultato = $pdo->bindAndExec($strsql,$bind);
?> <hr><a href="/enti/id0-edit" title="Inserisci nuovo ente"><div class="add_new"><span class="fa fa-plus-circle fa-3x"></span><br>Aggiungi nuovo ente</div></a><hr><?

	if ($risultato->rowCount()>0) {
		?>
				<table style="text-align:center; width:100%; font-size:0.8em" id="enti" class="elenco">
        <thead>
					<tr>
						<th width="5"></th>
						<th width="5"></th>
						<th>Denominazione</th>
						<th>Test</th>
						<th>Tipo</th>
						<th>Dominio</th>
						<th width="50">Codice Fiscale</th>
						<th width="50">E-mail</th>
						<th width="50">Telefono</th>
						<th width="50">Fax</th>
						<th width="10"></th>
						<th width="10"></th>
						<th width="10"></th>
						<!-- <td width="10"></td> -->
					</tr>
				</thead>
        <tbody>
       	<?

		while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
			$codice = $record["codice"];
			$attivo	= $record["attivo"];

			$colore = "#3C0";

			if ($attivo == "N") { $colore = "#C00"; }

			?>
			<tr id="<? echo $codice ?>">
      	<td  id="flag_<? echo $codice ?>" style="background-color: <? echo $colore ?>"></td>
				<td><? echo $codice ?></td>
				<td style="text-align:left"><strong><? echo $record["denominazione"] ?></strong></td>
				<td><?= $record["ambienteTest"] ?></td>
        <td><? echo $record["tipo"] ?></td>
				<td><?
					$url = (!empty($record["dominio"])) ? $record["dominio"] : $record["dominio_sua"];
					if (!empty($url)) $url = $config["protocollo"] . $url;
					?>
					<a href="<?= $url ?>" title="Vai al portale"><?= $url ?></a>
				</td>
        <td><? echo $record["cf"] ?></td>
        <td><? echo $record["email"] ?></td>
				<td><? echo $record["telefono"] ?></td>
        <td><? echo $record["fax"] ?></td>
				<td>
					<button class="btn-round" onClick="$('#link_<?= $record["codice"] ?>').dialog({title:'Link trasparenza',width:'800',modal:true})"><span class="fa fa-link"></span></button>
					<div id="link_<?= $record["codice"] ?>" style="display:none">
						<strong><?= $record["denominazione"] ?></strong><br>
						<table width="100%">
							<tr>
								<td class="etichetta">Archivio gare</td>
								<td>
									<? $href=$url."/archivio_gare/index.php?codice_ente=" . $record["codice"]; ?>
									<a href="<?= $href ?>"><?= $href ?></a>
								</td>
							</tr>
						</table>
					</div>
				</td>
        <td><a href='/enti/id<? echo $codice ?>-edit' class="btn-round btn-warning" title="Modifica"><span class="fa fa-pencil"></span></a></td>
				<td><button class="btn-round btn-danger" onClick="disabilita('<? echo $codice ?>','enti')" title="Abilita/Disabilita"><span class="fa fa-refresh"></span></button></td>
				<? /* <td id="td-cert-<?= $record["codice"] ?>">
					<?
						$ok_cert = true;
						if ($record["requested_ssl"] == "N") {
							$ok_cert = false;
						}
						if ($ok_cert) {
							?>
							<span class="fa fa-check" style="color:#0C0"></span>
							<?
						} else {
							?>
							<button class="btn-round btn-primary" onClick="$.ajax('createSSL.php?codice=<?= $record["codice"] ?>',{dataType:'script'});"><span class="fa fa-code"></span></button>
							<?
						}
					?>
				</td> */ ?>
      </tr>
      <?
		}
		?>
      </tbody>
		</table><br>
		<div style="text-align:right">
			<a href="export.php" target="_blank">Esporta CSV</a>
		</div>
         <div class="clear"></div>
		<hr><a href="/enti/id0-edit" title="Inserisci nuovo ente"><div class="add_new"><span class="fa fa-plus-circle fa-3x"></span><br>Aggiungi nuovo ente</div></a><hr>
          <?
	}		 else {
?><h1 style="text-align:center">
<span class="fa fa-exclamation-circle fa-3x"></span><br>Nessun risultato!</h1>	<?
}

	include_once($root."/layout/bottom.php");
	?>
