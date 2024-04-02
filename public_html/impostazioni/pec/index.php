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

	echo "<h1>PEC</h1>";
	$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
	$strsql = "SELECT b_enti.* FROM b_enti WHERE codice = :codice_ente";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount() > 0) {
		$record_ente = $risultato->fetch(PDO::FETCH_ASSOC);
		?>
        <form name="box" autocomplete="off" method="post" action="save.php" rel="validate" autocomplete="off" >
        <div class="comandi">
                    <button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
                    </div>
                    <input type="hidden" id="codice" name="codice" value="<? echo $record_ente["codice"]; ?>">
                    <div id="impostazioni_pec">
                    	<div class="box" id="predefinito">
                        <table width="100%">
							<tr>
								<td class="etichetta">PEC*</td>
								<td><input autocomplete="off" class="titolo_edit" autocomplete="off" type="text" name="ente[pec]" id="pec" title="pec" value="<? echo $record_ente["pec"] ?>" rel="S;0;0;E"></td>
								<td class="etichetta">Password*</td>
								<td><input autocomplete="off" class="titolo_edit" autocomplete="off" type="password" name="ente[password]" id="password" title="password" value="<? echo simple_decrypt($record_ente["password"],$_SESSION["ente"]["cf"]) ?>" rel="S;0;0;A;"></td>
							</tr>
							<tr>
								<td class="etichetta">SMTP*</td>
								<td><input type="text" name="ente[smtp]" id="smtp" title="smtp" value="<? echo $record_ente["smtp"] ?>" rel="S;0;0;A"></td>
								<td class="etichetta">Porta</td><td><input type="text" name="ente[smtp_port]" id="port" title="Porta" size="4" value="<? echo $record_ente["smtp_port"] ?>" rel="S;0;0;N;"></td></tr>
							<tr>
							<tr>
								<td class="etichetta">IMAP</td>
								<td><input type="text" name="ente[imap]" id="imap" title="imap" value="<? echo $record_ente["imap"] ?>" rel="N;0;0;A"></td>
								<td class="etichetta">Porta</td><td><input type="text" name="ente[imap_port]" id="port" title="Porta" size="4" value="<? echo $record_ente["imap_port"] ?>" rel="N;0;0;N;"></td></tr>
							<tr>
								<td class="etichetta">SSL</td>
								<td><input type="checkbox" name="ente[usa_ssl]" id="usa_ssl" <? if ($record_ente["usa_ssl"]) echo "checked" ?>></td>
								<td colspan="3"><input style="width:100%;background:#F60;" type="button" class="submit" value="Prova" onClick="prova_configurazione('predefinito');return false;"></td>
							</tr>
                        </table>
                       </div>
                       <?
													$sql = "SELECT * FROM b_pec WHERE codice_ente = :codice_ente AND eliminato = 'N'";
													$ris = $pdo->bindAndExec($sql,$bind);
													if ($ris->rowCount()) {
														while($record_pec = $ris->fetch(PDO::FETCH_ASSOC)) {
															$id = $record_pec["codice"];
															include("tr_pec.php");
														}
													}
												?>
                        </div>
                         <button class="aggiungi" onClick="aggiungi('tr_pec.php','#impostazioni_pec');return false;"><img src="/img/add.png" alt="Aggiungi indirizzo">Aggiungi indirizzo</button>
               <input type="submit" class="submit_big" value="Salva">
	</form>
        <?
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	include_once($root."/layout/bottom.php");
	?>
