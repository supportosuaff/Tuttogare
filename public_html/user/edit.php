<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	include("twoFactorForm.php");
	$edit = check_permessi("user",$_SESSION["codice_utente"]);
	if ($edit || (isset($_SESSION["codice_utente"]) && $_GET["cod"] == $_SESSION["codice_utente"])) {
		if ((isset($_GET["cod"])) && (isset($_SESSION["codice_utente"]))) {
				$codice = $_GET["cod"];
				$bind = array();
				$bind[":codice"] = $codice;
				$strsql = "SELECT b_utenti.* FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice ";
				if (((!isset($_SESSION["amministratore"])) || (!$_SESSION["amministratore"])) && ($_SESSION["tipo_utente"] != "CON")) {
					$strsql .= "JOIN b_enti ON b_utenti.codice_ente = b_enti.codice ";
				}
				$strsql .= "WHERE b_utenti.codice = :codice";
				if (((!isset($_SESSION["amministratore"])) || (!$_SESSION["amministratore"])) && ($_SESSION["tipo_utente"] != "CON")) {
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$strsql.= " AND (b_enti.codice = :codice_ente";
					$strsql.= " OR b_enti.sua = :codice_ente) ";
					if ($_SESSION["codice_utente"] != $_GET["cod"]) {
						$bind[":gerarchia"] = $_SESSION["gerarchia"];
						$strsql.= " AND b_gruppi.gerarchia > :gerarchia";
					}
				}
				$strsql .= " AND (b_gruppi.gerarchia < 3 OR b_gruppi.gerarchia = 5) ";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
						$record = $risultato->fetch(PDO::FETCH_ASSOC);
						$moduli_str = "";
						$bind = array();
						$bind[":codice"] = $record["codice"];
						$sql = "SELECT * FROM r_moduli_utente WHERE cod_utente = :codice";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount()>0) {
							$moduli_str = array();
							while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
								$moduli_str[] = $rec["cod_modulo"];
							}
							$moduli_str = implode(";",$moduli_str);
						}
						$operazione = "UPDATE";

					} else if (($_GET["cod"] == 0) && $edit) {
						$record = get_campi("b_utenti");
						if (isset($_SESSION["ente"])) {
							$record["codice_ente"] = $_SESSION["ente"]["codice"];
						}
						$moduli_str = "";
						$operazione = "INSERT";
					} else {
						echo "<h1>Impossibile accedere!</h1>";
						echo '<meta http-equiv="refresh" content="0;URL=/user/">';
						die();
					}
?>


<div class="clear"></div>
<? if ($_SESSION["codice_utente"] == $record["codice"]) { ?>
	<form id="eliminaSPID" name="box" method="post" action="/user/deleteSpid.php" rel="validate" autocomplete="off">
		<input type="hidden" name="eliminaAutenticazione">
	</form>
<? } ?>
<form name="box" method="post" action="save.php" rel="validate" autocomplete="off">
                    <input type="hidden" name="modulo" value="<? echo "user"; ?>">
                    <input type="hidden" id="codice" name="codice" value="<? echo $record["codice"]; ?>">
                    <input type="hidden" id="operazione" name="operazione" value="<? echo $operazione ?>">
					<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
					 <div class="box">
	                    <h2>Credenziali</h2>
                        <table width="100%" id="credenziali">
                        <tr>
                        	<td class="etichetta" width="20">E-mail*</td><td width="300">
                            <input style="width:90%" type="text" name="email" id="email" title="E-mail" value="<? echo $record["email"] ?>" rel="S;2;0;E;/user/check_email.php" autocomplete="new-password">
                            </td>
	                       	<td class="etichetta" width="20">Attivo</td><td><select name="attivo" id="attivo" title="Attivo" rel="S;1;1;A"><option value="">Seleziona...</option><option value="S">Si</option><option value="N">No</option></select></td></tr>
                        <tr>
													<td class="etichetta">Password*</td>
													<? 
														if ($operazione == "UPDATE") { 
															$rel = "N";
														} else {
															$rel = "S";
														}
													?>
													<td>
														<input type="password" name="password" id="password" title="Password" rel="<?= $rel ?>;8;16;P;check_password;=" autocomplete="new-password" <? if ($operazione == "UPDATE") echo "disabled" ?>>
														<div id="password_strenght"></div>
														<input type="checkbox" id="edit_password" onChange="change_password()" autocomplete="off"><?= traduci("Modifica") ?> <?= traduci("Password") ?></td>
													</td>
													<td class="etichetta"><?= traduci("Ripeti") ?> <?= traduci("Password") ?></td>
													<td>
														<input type="password" id="check_password" title="<?= traduci("Ripeti") ?> <?= traduci("Password") ?>" rel="<?= $rel ?>;8;16;P" <? if ($operazione == "UPDATE") echo "disabled" ?> onChange="valida($('#password'));" autocomplete="off">
													</td>
												</tr>
												<tr>
													<td><input type="button" value="Random password" onClick="suggest_password('#suggest')"></td><td id="suggest"></td>
												</tr>
												<tr>
													<td class="etichetta">Temporaneo</td>
													<td>
														<select name="temporaneo" id="temporaneo" rel="S;1;1;A" onChange="checkTipo()">
															<option value="N">No</option>
															<option value="S">SI</option>
															<option value="C">Commissario</option>
														</select>
														<script type="text/javascript">
															$('#temporaneo').val('<?= $record["temporaneo"] ?>');
															function checkTipo() {
																if ($('#temporaneo').val() == "C") {
																	$("#moduli").slideUp();
																} else {
																	$("#moduli").slideDown();
																}
															}
															checkTipo();
														</script>
													</td>
													<? if ($_SESSION["gerarchia"] <= 1 && check_permessi("user",$_SESSION["codice_utente"]) && $_SESSION["record_utente"]["read_only"] != "S") { ?>
														<td class="etichetta">Sola lettura moduli applicativi</td>
														<td>
															<select name="read_only" id="read_only" rel="S;1;1;A">
																<option value="N">No</option>
																<option value="S">SI</option>
															</select>
															<script type="text/javascript">
																$('#read_only').val('<?= $record["read_only"] ?>');
															</script>
														</td>
													<? } ?>
												</tr>
												<? if ($_SESSION["gerarchia"] <= 1 && check_permessi("user",$_SESSION["codice_utente"]) && $_SESSION["record_utente"]["read_only"] != "S") { ?>
                        <tr>
                        	<?
													$bind = array(':gerarchia' => $_SESSION["gerarchia"]);
								$sql = "SELECT * FROM b_gruppi WHERE disponibile = 'S' AND gerarchia >= :gerarchia";
								if (isset($_SESSION["ente"])) {
									$sql .= " AND gerarchia > 0 ";
								}
								$ris = $pdo->bindAndExec($sql,$bind);
								if ($ris->rowCount()>0) { ?>
	                        <td class="etichetta" width="20">Ruolo</td>
                            <td>
	                            <select name="gruppo" id="gruppo" title="Ruolo" rel="S;0;0;N">
                                	<option value="">Seleziona...</option>
                                	<?
										while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
											?><option value="<? echo $rec["codice"] ?>"><? echo $rec["gruppo"] ?></option><?
										}
									?>
    	                        </select>
                            </td>
                            	<script>
									$("#gruppo").val("<? echo $record["gruppo"] ?>");
								</script>
                            <? } ?>
							<?
								$sql = "SELECT * FROM b_enti ";
								if (isset($_SESSION["ente"])) {
									if ($_SESSION["gerarchia"] < 1) {
										$bind = array(':codice_ente' => $_SESSION["ente"]["codice"]);
									} else {
										$bind = array(':codice_ente' => $_SESSION["record_utente"]["codice_ente"]);
									}
									$sql .= " WHERE codice = :codice_ente OR sua = :codice_ente ";
								}
								$sql .= " ORDER BY denominazione";
								$ris = $pdo->bindAndExec($sql,$bind);
								if ($ris->rowCount()>0) { ?>
	                        <td class="etichetta" width="20">Ente</td>
                            <td>
	                            <select name="codice_ente" id="codice_ente" title="Ente" rel="S;0;0;N">
                                	<?
										if (isset($_SESSION["amministratore"]) && $_SESSION["amministratore"]) {
											?>
                                            <option value="0">SUPER AMMINISTRATORI</option>
                                            <?
										}
										while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
											?><option value="<? echo $rec["codice"] ?>"><? echo $rec["denominazione"] ?></option><?
										}
									?>
    	                        </select>
                            </td>
                                <script>
									$("#codice_ente").val("<? echo $record["codice_ente"] ?>");
								</script>
                            <? } ?>
                        </tr>
												<tr>
													<td class="etichetta">Ufficio / Dipartimento:</td>
													<td colspan="3">
														<input type="text" name="note_ufficio_dipartimento" style="width: 100%" id="note_ufficio_dipartimento" title="Ufficio / dipartimento" value="<? echo $record["note_ufficio_dipartimento"] ?>" rel="N;0;0;A;">
													</td>
												</tr>
												<tr>
													<td class="etichetta" width="20">Procedure Attive</td>
                          <td>
															<select name="procedureAttive[]" multiple id="procedureAttive" rel="N;0;0;A" title="Procedure attive">
															 <option value="0">Tutte</option>
															<? $sql = "SELECT * FROM b_procedure ORDER BY codice";
															 $ris = $pdo->query($sql);
																 if ($ris->rowCount()>0) {
																	 while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
																		 ?><option value="<? echo $rec["codice"] ?>"><? echo $rec["nome"] ?></option><?
																	 }
																 }
															 ?>
															</select>
															<script>
																var procedureAttive = "<? echo $record["procedureAttive"] ?>";
																$("#procedureAttive").val(procedureAttive.split(","));
															</script>
                          </td>
													<td class="etichetta" width="20">Limite importo</td>
                          <td>
                          	<input type="text" name="limiteMassimo" id="limiteMassimo" title="Limite" value="<? echo $record["limiteMassimo"] ?>" rel="N;0;0;N;0;>=">
                          </td>
												</tr>
												<? } ?>
                        </table>
                        <script>
													$("#attivo").val('<? echo $record["attivo"] ?>');
                        </script>
                    </div>
										<?
											if ($codice == $_SESSION["codice_utente"]) {
										?>
										<div class="box">
											<h2>Autenticazione a due fattori</h2>
											<button onClick="enableTwoFactor(); return false;">Gestisci autenticazione a due fattori</button>
										</div>
										<? } ?>
										<div class="box">
	                    <h2>Dati anagrafici</h2>
                        <table width="100%" id="anagrafici">
                        <tr><td class="etichetta">Nome*</td><td><input type="text" name="nome" id="nome" title="Nome" value="<? echo $record["nome"] ?>" rel="S;2;0;A"></td><td class="etichetta">Cognome*</td><td><input type="text" name="cognome" id="cognome" title="Cognome" value="<? echo $record["cognome"] ?>" rel="S;2;0;A"></td></tr>
                       <tr>
                       <td class="etichetta">Luogo nascita</td><td><input type="text" name="luogo" id="luogo" title="Luogo di nascita" value="<? echo $record["luogo"] ?>" rel="N;2;0;A"></td>
                       <td class="etichetta">Provincia nascita</td><td><input type="text" name="provincia_nascita" id="provincia_nascita" title="Provincia di nascita" value="<? echo $record["provincia_nascita"] ?>" rel="N;2;2;A" size="2" maxlength="2"></td></tr>
                        <tr><td class="etichetta">Data di nascita</td><td><input type="text" class="datepick" name="dnascita" id="dnascita" title="Data di nascita" value="<? echo mysql2date($record["dnascita"]) ?>" rel="N;10;10;D" maxlength="10" size="10"></td>
                        	<td class="etichetta">Sesso</td><td><select name="sesso" id="sesso" title="Sesso" rel="N;1;1;A"><option value="">Seleziona...</option><option value="M">M</option><option value="F">F</option></select></td></tr>

                        <tr>
                        <td class="etichetta">Codice Fiscale</td><td><input type="text" name="cf" id="cf" title="Codice Fiscale" value="<? echo $record["cf"] ?>" rel="S;16;16;A" maxlength="16"><input type="button" onClick="calcola_cf($('#nome').val(),$('#cognome').val(),$('#luogo').val(),$('#dnascita').val(),$('#sesso').val(),$('#provincia_nascita').val(),'cf');return false;" value="Calcola"></td>
                        <td></td><td></td>
						</table>
        				<script>
							$("#sesso").val('<? echo $record["sesso"] ?>');
                        </script>
                    </div>
					<div class="box recapiti">
                    	<h2>Recapiti</h2>
                        <table width="100%" id="recapiti">
                        <tr><td class="etichetta">Indirizzo</td><td colspan="3"><input style="width:98%" riferimento="recapiti" type="text" name="indirizzo" id="indirizzo" title="Indirizzo" value="<? echo $record["indirizzo"] ?>" rel="N;5;0;A"></td></tr>
                        <tr><td class="etichetta">Citta</td><td><input data-geo="locality" type="text" name="citta" id="citta" title="Citta" value="<? echo $record["citta"] ?>" rel="N;2;0;A"></td>
                       <td class="etichetta">Provincia</td><td><input type="text" data-geo="administrative_area_level_2"  name="provincia" id="provincia" title="Provincia" value="<? echo $record["provincia"] ?>" rel="N;2;0;A"></td></tr><tr>
                        <td class="etichetta">Regione</td><td><input type="text" name="regione" data-geo="administrative_area_level_1" id="regione" title="Regione" value="<? echo $record["regione"] ?>" rel="N;2;255;A" ></td>
                       	<td class="etichetta">Stato</td><td><input data-geo="country" type="text" name="stato" id="stato" title="Stato" value="<? echo $record["stato"] ?>" rel="N;2;0;A"></td></tr>
                        <tr><td class="etichetta">Telefono</td><td><input type="text" name="telefono" id="telefono" title="Telefono" value="<? echo $record["telefono"] ?>" rel="N;0;0;A"></td>
                        <td class="etichetta">Cellulare</td><td><input type="text" name="cellulare" id="cellulare" title="Cellulare" value="<? echo $record["cellulare"] ?>" rel="N;0;0;A"></td></tr>
                        <tr><td class="etichetta" width="20">PEC</td><td width="300"><input style="width:90%" type="text" name="pec" id="pec" title="PEC" value="<? echo $record["pec"] ?>" rel="N;2;0;E;/user/check_pec.php"></td></td>
                        </table>
                    </div>

	    <input type="hidden" name="cod_moduli" id="cod_moduli" value="<? echo $moduli_str; ?>">
			<input type="submit" class="submit_big" value="Salva">
			<? if ($_SESSION["gerarchia"] <= 1 && check_permessi("user",$_SESSION["codice_utente"])) { ?>
				<input type="submit" class="submit_big" onClick="$('#codice').val(''); $('#operazione').val('INSERT'); return true" value="Duplica">
			<? } ?>
<br>
	</form>
		<? if ($_SESSION["gerarchia"] <= 1 && check_permessi("user",$_SESSION["codice_utente"]) && $_SESSION["record_utente"]["read_only"] != "S") { ?>
    <div id="moduli">
    <h2 style="text-align:center">Moduli</h2>
	    <?
							$bind = array();
							$s = "SELECT b_moduli.* FROM b_moduli WHERE b_moduli.tutti_utente = 'N' AND
											gerarchia >= '". $_SESSION["gerarchia"] . "'
											GROUP BY b_moduli.codice
											ORDER BY b_moduli.codice";
							$r = $pdo->bindAndExec($s,$bind);
							if ($r->rowCount()>0) {
								$count = 0;
								while($re = $r->fetch(PDO::FETCH_ASSOC)) {
									$show = true;
									if ($re["ente"] == "S" && $re["tutti_ente"] == "N" && isset($_SESSION["ente"])) {
										$sql = "SELECT * FROM r_moduli_ente WHERE cod_modulo = :cod_modulo AND cod_ente = :cod_ente ";
										$check_ente = $pdo->bindAndExec($sql,array(":cod_modulo"=>$re["codice"],":cod_ente"=>$_SESSION["ente"]["codice"]));
										if ($check_ente->rowCount() == 0) $show=false;
									}
									if ($show) {
									$count++;
									?>
									<div style="float:left; margin-left:1%; width:10%; text-align:center">
										<button class="bt_relazione" rel="#cod_moduli" name="<? echo $re["codice"] ?>">
                    	<?
											if ($re["glyph"] != "") {
											 echo "<span class='".$re["glyph"]." fa-4x'></span>";
										 } else if (file_exists($root."/".$re["radice"]."/icon.png")) {
												echo "<img src='/". $re["radice"] . "/icon.png' alt='". $titolo . "'>";
											}
											?><br>
                    	<strong><? echo str_replace(" ","<br>",$re["titolo"]) ?></strong>
										</button><br>
										<small><?= $re["descrizione"] ?></small>
									</div>
									<?
									if ($count%9==0) echo "<div class=\"clear\"></div>";
									}
								}
							}
						?>
						<div class="clear"></div>
    </div><?
	} ?>
			<div class="clear"></div>
         	<script type="text/javascript">
			fill_relazioni("#cod_moduli","#moduli");
			 	$("#anagrafici").find("input").keypress(function() {
					if ($(this).attr("id")!="cf") {
						$("#cf").val("");
						}
				});
			 </script>
    <?
				} else {
						echo "<h1>Impossibile accedere!</h1>";
						echo '<meta http-equiv="refresh" content="0;URL=/user/">';
						die();
				}
			} else {
				echo "<h1>Impossibile accedere!</h1>";
				echo '<meta http-equiv="refresh" content="0;URL=/user/">';
				die();
			}
	include_once($root."/layout/bottom.php");
	?>
