<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	echo "<h1>AREE ORGANIZZATIVE</h1>";
	$bind = array(":codice"=>$_SESSION["ente"]["codice"]);
	$strsql = "SELECT b_aree_organizzative.*, b_enti.denominazione FROM b_aree_organizzative JOIN b_enti ON b_aree_organizzative.codice_ente = b_enti.codice WHERE b_aree_organizzative.codice_gestore = :codice AND b_aree_organizzative.attivo = 'S' ORDER BY b_enti.denominazione, b_aree_organizzative.nome";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount() > 0) {
		?>
    <form name="box" method="post" action="save.php" rel="validate" >
      <div class="comandi">
        <button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
      </div>
			<table width="100%" class="elenco">
				<thead>
					<tr>
						<th>Ente</th>
						<th>Codice Unit&agrave; CUP</th>
						<th>Unit&agrave; Organizzativa</th>
						<th>Elimina</th>
					</tr>
				</thead>
				<tbody>
		    	<?
						while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
							?>
							<tr id="area_<?= $record["codice"] ?>">
								<td><?= $record["denominazione"] ?></td>
								<td width="20%">
									<input type="text" class="titolo_edit" name="area[<?= $record["codice"] ?>][codice_unita]" value="<?= $record["codice_unita"] ?>" title="Codice Unita" rel="S;1;20;A">
								</td>

								<td width="50%">
									<input type="text" class="titolo_edit" name="area[<?= $record["codice"] ?>][nome]" value="<?= $record["nome"] ?>" title="Nome area" rel="S;1;255;A">
								</td>
								<td width="10">
									<input type="image" onClick="elimina('<? echo $record["codice"] ?>','impostazioni/aree_organizzative');return false;" src="/img/del.png" title="Elimina">
								</td>
							</tr>
							<?
						}
					?>
				</tbody>
			</table>
		</form>
	<?
	} else {
		?>
		<h2>Nessun risultato</h2>
		<?
	}

	include_once($root."/layout/bottom.php");
	?>
