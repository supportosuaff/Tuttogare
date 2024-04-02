<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	if ($edit) {
	echo "<h1>DATI MINIMI</h1>";
		if (!empty($_SESSION["ente"]["codice"])) {
			$strsql = "SELECT * FROM b_impostazioni_dati_minimi WHERE codice_gestore = :codice AND eliminato = 'N'";

			$risultato = $pdo->bindAndExec($strsql,array(":codice"=>$_SESSION["ente"]["codice"]));
	?>
        <form name="box" method="post" action="save.php" rel="validate" >
        <div class="comandi">
                    <button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
		</div>
                    <input type="hidden" id="codice" name="codice" value="<? echo $record_ente["codice"]; ?>">
        			<div class="box">
                <table width="100%" >
									<thead>
										<tr>
											<th></th>
											<th>Nome</th>
											<th>Tipo</th>
											<th>Tipologie</th>
											<th>Tag</th>
											<th>Obbligatorio</th>
											<th></th>
											<th></th>
										</tr>
									</thead>
									<tbody id="rows">
										<? if (isset($risultato) && $risultato->rowCount() > 0) {
												while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
													$id = $record["codice"];
													include("form.php");
												}
											} else {
												$id = "i_0";
												$record = get_campi("b_impostazioni_dati_minimi");
												include("form.php");
											}
										?>
                  </tbody>
                  <tfoot>
                  	<tr><td colspan="8">
											<button class="aggiungi" onClick="aggiungi('form.php','#rows');return false;"><img src="/img/add.png" alt="Aggiungi campo">Aggiungi campo</button></td>
										</tr>
									</tfoot>
								</table>
							</div>
							<input type="submit" class="submit_big" value="Salva">
	</form>
        <?
		} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	include_once($root."/layout/bottom.php");
	?>
