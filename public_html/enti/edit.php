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
			$codice = $_GET["cod"];
			$bind = array(":codice"=>$codice);
			$strsql = "SELECT b_enti.* FROM b_enti WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($edit) {
				if ($risultato->rowCount() > 0) {
						$record = $risultato->fetch(PDO::FETCH_ASSOC);
						$moduli_str = "";
						$sql = "SELECT * FROM r_moduli_ente WHERE cod_ente = :codice";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount() >0) {
							$moduli_str = array();
							while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
								$moduli_str[] = $rec["cod_modulo"];
							}
							$moduli_str = implode(";",$moduli_str);
						}
						$operazione = "UPDATE";
				} else {
						$record = get_campi("b_enti");
						$record["codice"] = 0;
						$moduli_str = "";
						$operazione = "INSERT";
				}
?>

<div class="clear"></div>
<script type="text/javascript" src="/js/resumable.js"></script>
<script type="text/javascript" src="resumable-uploader.js"></script>
<? if (isset($_SESSION["amministratore"])) { ?>

	<script>
		function cercaIPA() {
	    $.ajax({
	      url: '/enti/searchIpa.php',
	      type: 'POST',
	      dataType: 'script',
	      data: {codice: $('#cerca_ipa').val()},
	      beforeSend: function(e) {
	        $('#wait').fadeIn();
	      }
	    })
	    .done(function(script) {
	      script;
	    })
	    .always(function() {
	      $('#wait').fadeOut();
	    });
		}
	</script>
<? } ?>
<form name="box" method="post" action="save.php" rel="validate" >
    <input type="hidden" name="modulo" value="<? echo "enti"; ?>">
    <input type="hidden" id="codice" name="codice" value="<? echo $record["codice"]; ?>">
    <input type="hidden" id="operazione" name="operazione" value="<? echo $operazione ?>">
    <input type="hidden" name="token" id="token" value="<?echo $record["token"]; ?>">

		<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
					 <div class="box">
	                    <h2>Dati anagrafici</h2>
												<table width="100%" id="anagrafici" style="table-layout: fixed;">
												<tr>
													<td class="etichetta">Codice iPA</td>
													<td>
														<input title="Codice IPA" type="text" name="codice_suaff" id="cerca_ipa" size="5" value="<?= $record["codice_ipa"] ?>"> <button class="submit" onClick="cercaIPA(); return false;">Cerca</button>
													</td>
													<td class="etichetta">Codice SUAFF</td>
													<td>
														<input title="Codice SUAFF" type="text" name="codice_suaff" size="5" value="<?= $record["codice_suaff"] ?>">
													</td>
                        <tr><td class="etichetta" width="10%">Denominazione</td><td colspan="3"><input class="titolo_edit" type="text" name="denominazione" id="denominazione" title="Denominazione" value="<? echo $record["denominazione"] ?>" rel="S;2;0;A"></td></tr>
												<tr>
                        <td class="etichetta">Tipologia Ente</td>
                        	<td colspan="3"> <select name="tipologia_ente" id="tipologia_ente" title="Tipologia ente" rel="S;0;0;N">
							<?
								$sql = "SELECT * FROM b_tipologie_ente WHERE attivo = 'S' AND eliminato = 'N'";
								$ris = $pdo->query($sql);
								if ($ris->rowCount() > 0) {
									while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
										echo "<option value=\"" . $rec["codice"] . "\">" . $rec["titolo"] . "</option>";
									}
								}
							?>
                        </select></td>
                        </tr>
												<tr>
													<td class="etichetta">Tipologia Stazione Appaltante</td>
														<td colspan="3"> <select name="tipologia_sa" id="tipologia_sa" title="Tipo SA" rel="S;0;0;A">
															<option value="">Seleziona...</option>
														<?
															foreach(getListeSIMOG()["TipologiaSAType"] AS $valore => $tipologia) {
																?>
																<option value="<?= $valore ?>"><?= $tipologia ?></option>
																<?
															}
														?>
													</select></td>
												</tr>
                        <tr><td class="etichetta">Tipo</td><td>
                        	<select name="tipo" id="tipo" rel="S;1;20;A" title="Tipo">
                            	<option value="">Seleziona</option>
                                <option>SUA</option>
                                <option>Ente</option>
                            </select>
                        </td>
                        <td class="etichetta">Stazione Unica Appaltante</td>
                        <td>
                        <select name="sua" id="sua" title="Stazione Unica Appaltante" rel="N;0;0;N">
	                        <option value="0">Nessuna</option>
							<?
								$sql = "SELECT * FROM b_enti WHERE attivo = 'S' AND tipo = 'SUA' AND codice <> :codice";
								$ris = $pdo->bindAndExec($sql,array(":codice"=>$record["codice"]));
								if ($ris->rowCount() > 0) {
									while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
										echo "<option value=\"" . $rec["codice"] . "\">" . $rec["denominazione"] . "</option>";
									}
								}
							?>
                        </select>
                        </td>
                        </tr>
                        <script>

							$("#tipologia_sa").val('<? echo $record["tipologia_sa"] ?>');
							$("#tipologia_ente").val('<? echo $record["tipologia_ente"] ?>');
							$("#tipo").val('<? echo $record["tipo"] ?>');
							$("#sua").val('<? echo $record["sua"] ?>');
						</script>
						<tr><td class="etichetta">Dominio</td><td>
						<input style="width:95%" type="text" name="dominio" id="dominio" title="Dominio" value="<? echo $record["dominio"] ?>" rel="N;0;255;A">
						</td>
						<td class="etichetta">Codice Fiscale</td><td><input type="text" name="cf" id="cf" title="Codice Fiscale" value="<? echo $record["cf"] ?>" rel="S;10;16;A" maxlength="16"></td>
						</tr>
						<tr>
							<td class="etichetta">Numerazione</td><td>
								<select name="numerazione" id="numerazione" rel="S;6;11;A" title="Numerazione">
										<option value="">Seleziona</option>
											<option value="solare">Solare</option>
											<option value="progressiva">Progressiva</option>
									</select>
							</td>
							<td class="etichetta">Ambiente di Test</td><td>
								<select name="ambienteTest" id="ambienteTest" rel="S;1;1;A" title="Ambiente di test">
										<option value="">Seleziona</option>
											<option value="S">Si</option>
											<option value="N">No</option>
									</select>
							</td>
						</tr>
						<tr>
							<td class="etichetta">Indirizzo ricezione chiavi</td>
							<td colspan="3">
								<input type="text" name="email_chiavi" id="email_chiavi" title="E-mail ricezione chiavi" value="<? echo $record["email_chiavi"] ?>" rel="N;0;0;E" style="width:98%"><br>
								<strong style="font-size:10px">Se non valorizzato le chiavi per decriptare le buste di partecipazione saranno inviate all'indirizzo e-mail dell'utente che crea la gara</strong>
							</td>
						</tr>
						<tr>
							<td class="etichetta">Indirizzo ricezione chiavi backup</td>
							<td colspan="3">
								<input type="text" name="email_chavi_bkInterno" id="email_chavi_bkInterno" title="E-mail ricezione chiavi" value="<? echo $record["email_chavi_bkInterno"] ?>" rel="N;0;0;E" style="width:98%"><br>
								<strong style="font-size:10px">Se valorizzato il sistema invierà una copia di backup delle chiavi per decriptare le buste di partecipazione anche all'indirizzo e-mail indicato</strong>
							</td>
						</tr>
						<tr>
							<td class="etichetta">Ufficio</td>
							<td>
								<select name="ufficio" id="ufficio" rel="S;1;1;A" title="Ufficio / Ente">
										<option value="">Seleziona</option>
											<option value="S">Si</option>
											<option value="N">No</option>
									</select>
							</td>
							<td class="etichetta">Permetti uso moduli cross platform</td><td>
								<select name="permit_cross" id="permit_cross" rel="S;1;1;A" title="Moduli cross">
									<option value="">Seleziona</option>
									<option value="S">Si</option>
									<option value="N">No</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="etichetta">Numero Verde per OE</td><td>
								<select name="numero_verde" id="numero_verde" rel="S;1;1;A" title="Numero verde">
										<option value="">Seleziona</option>
											<option value="S">Si</option>
											<option value="N">No</option>
									</select>
							</td>
						</tr>
						<tr>
							<td class="etichetta">Numero assistenza OE</td><td>
								<input type="text" name="numero_assistenza_oe" id="numero_assistenza_oe" title="Numero assistenza OE" value="<? echo $record["numero_assistenza_oe"] ?>" rel="N;0;255;A">
							</td>
							<td class="etichetta">Numero assistenza SA</td><td>
								<input type="text" name="numero_assistenza_sa" id="numero_assistenza_sa" title="Numero assistenza SA" value="<? echo $record["numero_assistenza_sa"] ?>" rel="N;0;255;A">
							</td>
						</tr>
						<tr>
							<td class="etichetta">Email assistenza</td>
							<td>
								<input type="text" name="email_assistenza_oe" id="email_assistenza_oe" title="E-mail assistenza" value="<? echo $record["email_assistenza_oe"] ?>" rel="N;0;250;E">
							</td>
							<td class="etichetta">Contatto DPO</td><td>
								<input type="text" name="dpo" id="dpo" title="Contatto DPO" value="<? echo $record["dpo"] ?>" rel="N;0;255;A">
							</td>
						</tr>
						<tr>
							<td class="etichetta">Profilo completo OE</td>
							<td>
								<select name="profilo_completo_oe" id="profilo_completo_oe" rel="S;1;1;A" title="Profilo completo OE">
									<option value="N">No</option>
									<option value="S">Si</option>
								</select>
							</td>
						</tr>
						</table>
						<script>
							$("#numerazione").val('<? echo $record["numerazione"] ?>');
							$("#ambienteTest").val('<? echo $record["ambienteTest"] ?>');
							$('#ufficio').val('<? echo $record["ufficio"] ?>');
							$("#profilo_completo_oe").val('<? echo $record["profilo_completo_oe"] ?>');
							$("#permit_cross").val('<? echo $record["permit_cross"] ?>');
							$("#numero_verde").val('<? echo $record["numero_verde"] ?>');
						</script>
					</div>
								<div id="cpn" class="box">
									<h2>Configurazione Servizio Contratti Pubblici Nazionali</h2>
									<table width="100%" style="table-layout: fixed;">
										<tr>
											<td class="etichetta">ID Cliente: </td>
											<td>
												<input type="text" name="cpnClientId" id="clientid" title="ClientId" value="<? echo $record["cpnClientId"] ?>" rel="N;0;0;A" style="width: 100%; box-sizing : border-box;">
											</td>
											<td class="etichetta">Chiave Cliente: </td>
											<td>
												<input type="text" name="cpnClientKey" id="clientkey" title="ClientKey" value="<? echo $record["cpnClientKey"] ?>" rel="N;0;0;A" style="width: 100%; box-sizing : border-box;">
											</td>
										</tr>
									</table>
								</div>
					<div class="box">
          	<h2>Recapiti</h2>
           <table width="100%" id="recapiti">
             <tr><td class="etichetta">Sito istituzionale</td><td colspan="3"><input style="width:95%" type="text" name="url" id="url" title="Sito istituzionale" value="<? echo $record["url"] ?>" rel="S;5;0;L"></td></tr>
            	<tr><td class="etichetta">Indirizzo</td><td><input type="text" name="indirizzo" id="indirizzo" title="Indirizzo" value="<? echo $record["indirizzo"] ?>" rel="N;2;0;A"></td>
              <td class="etichetta">Citta</td><td><input type="text" name="citta" id="citta" title="Citta" value="<? echo $record["citta"] ?>" rel="N;2;0;A"></td></tr>
             <tr>
             <td class="etichetta">CAP</td><td><input type="text" name="cap" id="cap" title="C.A.P." value="<? echo $record["cap"] ?>" rel="N;5;5;A" size="5" maxlength="5"></td>
             <td class="etichetta">Provincia</td><td><input type="text" name="provincia" id="provincia" title="Provincia" value="<? echo $record["provincia"] ?>" rel="N;2;2;A" size="2" maxlength="2"></td>
             	</tr><tr><td class="etichetta">Stato</td><td><input type="text" name="stato" id="stato" title="Stato" value="<? echo $record["stato"] ?>" rel="N;2;0;A"></td>
              <td class="etichetta">Telefono</td><td><input type="text" name="telefono" id="telefono" title="Telefono" value="<? echo $record["telefono"] ?>" rel="N;0;0;A"></td></tr>
              <tr><td class="etichetta">Fax</td><td><input type="text" name="fax" id="fax" title="fax" value="<? echo $record["fax"] ?>" rel="N;0;0;A"></td>
               <td class="etichetta">E-mail</td><td><input type="text" name="email" id="email" title="email" value="<? echo $record["email"] ?>" rel="N;0;0;E"></td></tr>
							<?
							$rel = "S";
							if ($_SESSION["gerarchia"] === "0") $rel = "N";
							?>
               <tr><td class="etichetta" colspan="4">Orari</td></tr>
               <tr><td class="etichetta">Apertura</td><td><input type="text" class="timepick" name="ora_apertura" id="ora_apertura" title="ora_apertura" value="<? echo substr($record["ora_apertura"],0,5) ?>" rel="<?=$rel?>;5;5;T"></td>
               <td class="etichetta">Chiusura</td><td><input type="text" class="timepick" name="ora_chiusura" id="ora_chiusura" title="ora_chiusura" value="<? echo substr($record["ora_chiusura"],0,5); ?>" rel="<?=$rel?>;5;5;T"></td></tr>
               </table>
               </div>
							 <? if ($_SESSION["amministratore"]) { ?>
								 <div class="box">
									 <h2>Integrazioni</h2>
									 <table width="100%">
										<tr>
											<td class="etichetta">Identificativo GUUE</td><td>
													<input style="width:99%" type="text" name="id_guue" id="id_guue" title="Identificativo GUUE" value="<? echo $record["id_guue"] ?>" rel="N;0;255;A">
												</td>
										 </tr>
										 <tr>
											<td class="etichetta">Codice Soggetto CUP</td>
												<td>
													<input style="width:99%" type="text" name="codice_soggetto_cup" id="codice_soggetto_cup" title="Soggetto CUP" value="<? echo $record["codice_soggetto_cup"] ?>" rel="N;0;0;A">
												</td>
										</tr>
										<tr>
											<td class="etichetta">Analytics GTAG ID</td><td>
												<input style="width:99%" type="text" name="gtag_id" id="gtag_id" title="Analytics GTAG ID" value="<? echo $record["gtag_id"] ?>" rel="N;0;255;A">
											</td>
										</tr>
										<tr>
											<td class="etichetta">Codice NSO</td>
												<td>
													<input style="width:99%" type="text" name="codice_nso" id="codice_nso" title="Codice NSO" value="<? echo $record["codice_nso"] ?>" rel="N;0;0;A">
												</td>
										</tr>
									 </table><br>
										<?
										if (!empty($config["url-art-80"])) {
											if (is_array($config["url-art-80"])) {
											?>
											<table width="100%">
												<tr>
													<td colspan="5" class="etichetta">Provider servizi verifica Art.80</td>
													<td>
														<select name="providerArt80" id="providerArt80" title="Provider Art. 80" rel="N;0;0;A">
															<option value="">Nessuno</option>
															<?
																foreach($config["url-art-80"] AS $id_provider => $provider) {
																	?>
																	<option value="<?= $id_provider ?>"><?= $provider["titolo"] ?></option>
																	<?
																}
															?>
														</select>
                        		<script>
															$("#providerArt80").val('<? echo $record["providerArt80"] ?>');
														</script>
													</td>
												</tr>
											</table>
											<?
										}
									}
									?>
								</div>
							 <? } ?>
					<div class="box">
						<h2>Informativa e norme tecniche</h2>
						<table width="100%">
							<tr><td class="etichetta">Sezione 2 - Norme tecniche</td></tr>
							<tr><td><textarea name="norme_sezione_2" id="norme_sezione_2" rel="N;0;0;A" class="ckeditor_full" title="Norme tecniche - Sezione 2"><?=$record["norme_sezione_2"]?></textarea></td></tr>
							<tr><td class="etichetta">Informativa privacy personalizzata</td></tr>
							<tr><td><textarea name="informativa_privacy" id="informativa_privacy" rel="N;0;0;A" class="ckeditor_full" title="Informativa privacy personalizzata"><?=$record["informativa_privacy"]?></textarea></td></tr>
							<tr><td class="etichetta">Informativa registrazione OE personalizzata</td></tr>
							<tr><td><textarea name="informativa_reg_oe" id="informativa_reg_oe" rel="N;0;0;A" class="ckeditor_simple" title="Informativa registrazione OE personalizzata"><?=$record["informativa_reg_oe"]?></textarea></td></tr>
							<tr><td class="etichetta">Messaggio personalizzato </td></tr>
							<tr><td><textarea name="messaggio_comunicazioni" id="messaggio_comunicazioni" rel="N;0;0;A" class="ckeditor_simple" title="Messaggio footer comunicazioni"><?=$record["messaggio_comunicazioni"]?></textarea></td></tr>
							<tr><td class="etichetta">Footer personalizzato </td></tr>
							<tr><td><textarea name="custom_footer" id="custom_footer" rel="N;0;0;A" class="ckeditor_full" title="Footer personalizzato"><?=$record["custom_footer"]?></textarea></td></tr>

						</table>
					</div>
					<div class="box" id="impostazioni_pec">
					<h2>PEC</h2>
					<table width="100%">
						<tr>
							<td class="etichetta">PEC*</td>
							<td><input type="text" autocomplete="off" name="pec" id="pec" style="width:98%" title="pec" value="<? echo $record["pec"] ?>" rel="S;0;0;E"></td>
						</tr>
						<tr>
							<td class="etichetta">PEC (barra inferiore)</td>
							<td><input type="text" autocomplete="off" name="pec_footer" id="pec_footer" style="width:98%" title="PEC barra inferiore" value="<? echo $record["pec_footer"] ?>" rel="N;0;0;E"></td>
						</tr>
					</table>
					</div>
					<div class="box">
					<h2>Logo</h2>
					<div style="text-align:center;">
						<?
						$src = "/img/photo.png";
						if ($record["riferimento"] != "") $src = "/documenti/enti/".$record["logo"];
						?>
						<input type="hidden" name="logo" id="logo" value="<? echo $record["logo"] ?>">
						<input type="hidden" name="riferimento" id="riferimento" value="<? echo $record["riferimento"] ?>">
						<img class="arrotondato" id="img_foto" width="128" src="<?= $src ?>" alt="Foto allegata"><br>
						<input type="hidden" id="filechunk_foto" name="filechunk">
						<a href="#" class="submit_big btn-warning" id="uploadFoto">Carica logo</a>
						<div id="progress_bar_img" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
						<button class="submit_big btn-danger" onClick="$('#img_foto').attr('src','/img/photo.png');$('#logo').val('');$('#riferimento').val(''); return false;">Rimuovi</button>
						<script>
						var imgLoader = (function($){
						return (new ResumableUploader($('#uploadFoto'),"img"));
					})(jQuery);
						</script>
					</div>

					</div>
					<input type="hidden" name="logo" id="logo" value="<? echo $record["logo"] ?>">
					<input type="hidden" name="cod_moduli" id="cod_moduli" value="<? echo $moduli_str; ?>">
					<input type="submit" class="submit_big" value="Salva">
					<input type="submit" class="submit_big" onClick="$('#codice').val(''); $('#operazione').val('INSERT'); return true" value="Duplica">
					</form>
    <div class="clear"></div>

    <div id="moduli">
    <h2 style="text-align:center">Moduli</h2>
	    <?
    					$s = "SELECT * FROM b_moduli WHERE b_moduli.ente = 'S' AND b_moduli.tutti_ente = 'N' ORDER BY codice";
							$r = $pdo->query($s);

							if ($r->rowCount()>0) {
								$count = 0;
								while($re = $r->fetch(PDO::FETCH_ASSOC)) {
									$count++;
										?>
										<div style="float:left; margin-left:1%; width:10%; text-align:center">
											<button class="bt_relazione" rel="#cod_moduli" name="<? echo $re["codice"] ?>">
												<?
													if ($re["glyph"] != "") {
														echo "<span class='".$re["glyph"]." fa-4x'></span>";
													} else if (file_exists($root."/".$re["radice"]."/icon.png")) {
														echo "<img src='/". $re["radice"] . "/icon.png'>";
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
						?>
						<div class="clear"></div>
    </div>
			<div class="clear"></div>
         	<script type="text/javascript">
						fill_relazioni("#cod_moduli","#moduli");
					</script>
    <?
			} else {
						echo "<h1>Impossibile accedere!</h1>";
						echo '<meta http-equiv="refresh" content="0;URL=/enti/">';
						die();
				}
	?>


<?
	include_once($root."/layout/bottom.php");
	?>
