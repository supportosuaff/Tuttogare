<?
	if (isset($record_utente)) {
		$province = getProvinceIT();
		$regioni = getRegioniIT();
		include($root."/user/twoFactorForm.php");

?>

<h1><?= traduci("profilo") ?></h1>

<script type="text/javascript" src="/js/resumable.js"></script>
<script type="text/javascript" src="resumable-uploader.js"></script>
<?
	$obbligatorio = "S";
 	if (isset($bozza) && $bozza) {
		$obbligatorio = "N";
		if($_SESSION["record_utente"]["attivo"] == 'N') {
			?>
			<div class="ui-state-error padding">
				<h3><?= traduci('attenzione') ?>: <?= traduci("La registrazione non è ancora stata confermata") ?></h3>
			</div><br>
			<?
		}
	}
	if(! empty($_SESSION["record_utente"]["profilo_completo"]) && $_SESSION["record_utente"]["profilo_completo"] !== 'S') {
		?>
		<div class="box" style="color: #FC0107; background-color: rgba(252, 1, 7, 0.1)">
			<b><?= strtoupper(traduci('attenzione')) ?></b> <?= traduci('completa-profilo') ?>
		</div>
		<?
	}
?>
<form id="form_registrazione_oe" name="box" autocomplete="off" class="registrazione" method="post" target="_self" enctype="multipart/form-data" action="save.php" rel="validate">
					<input type="hidden" id="codice" name="codice" value="<? echo $codice ?>">
                    <input type="hidden" name="operatori[codice]" value="<? echo $record_operatore["codice"] ?>">
                    <div class="comandi">
                    <button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
                    </div>
                    <input type="hidden" name="tipo" value="<? echo $record_utente["tipo"] ?>">
                    <?
                      if($_SESSION["amministratore"]) {
												
												?>
                        <div class="box">
													<table width="100%">
														<tr>
															<td class="etichetta">Profilo</td>
															<td>
																<select id="tipologia_utente" name="utenti[gruppo]" rel="S;1;1;N" title="Tipologia OE">
																	<option value="4">Azienda</option>
																	<option value="5">Professionista</option>
																</select>
															</td>
														</tr>
													</table>


                          <div class="clearfix"></div>
                          <button type="submit" class="submit_big" style="background-color:#FF3300;" onclick="$('#form_registrazione_oe').removeAttr('rel').removeProp('rel'); return true;">Salva senza validazione</button>
                        </div>
                        <script type="text/javascript">
                          $(function() {
                            $('#tipologia_utente').val('<?= $record_utente["gruppo"] ?>').trigger('chosen:updated');
                          });
                        </script>
                        <?
                      }
                    ?>
                    <div id="tabs">
                    	<ul>
                        	<li><a href="#referente"><?= traduci("Referente") ?></a></li>
                          <? if ($record_utente["tipo"] == "OPE") { ?>
														<li><a href="#azienda"><?= traduci("Azienda") ?></a></li>
                          	<li><a href="#organizzazione"><?= traduci("Organizzazione") ?></a></li>
													<? } ?>
													<li><a href="#categorie"><?= traduci("Categorie merceologiche") ?></a></li>
													<? if (!isset($bozza) || (isset($bozza) && !$bozza)) { ?><li><a href="#committenti"><?= traduci("Committenti") ?></a></li><? } ?>
                          <li><a href="#certificazioni"><?= traduci("Certificazioni") ?></a></li>
													<li><a href="#brevetti"><?= traduci("Brevetti") ?></a></li>
                      </ul>
                     <div id="referente">
                     <h1><?= traduci("Referente") ?></h1>
					 						<div class="box">
	                    <h2><?= traduci("Credenziali") ?></h2>
                        <table width="100%" id="credenziali">
                        <tr>
                        	<td class="etichetta"><?= traduci("E-mail") ?>*</td><td colspan="3">
														<input style="width:90%" type="text" name="utenti[email]" id="email" title="<?= traduci("E-mail") ?>" value="<? echo $record_utente["email"] ?>" rel="S;2;0;E;/user/check_email.php" autocomplete="off">
                            <div id="email_check" style="display:none;"></div>
                            </td>
													</tr>
													<tr>
	                       	<td class="etichetta"><?= traduci("Password") ?>*</td>
                        <? if ($operazione == "UPDATE") {
													$rel_ripeti = "N";
													?>
	                        <td><input type="password" name="utenti[password]" id="password" title="<?= traduci("Password") ?>" rel="N;8;16;P" disabled><div id="password_strenght"></div>
														<input type="checkbox" id="edit_password" onChange="change_password()" autocomplete="off"><?= traduci("Modifica") ?> <?= traduci("Password") ?></td>
                        <? } else {
													$rel_ripeti = "S";
													?>
        	                <td><input type="password" name="utenti[password]" id="password" title="<?= traduci("Password") ?>" rel="S;8;16;P;check_password;=" autocomplete="off">
            	            <div id="password_strenght"></div></td>
                        <? } ?>
													<td class="etichetta"><?= traduci("Ripeti") ?><?= traduci("Password") ?></td>
													<td><input type="password" id="check_password" title="<?= traduci("Ripeti") ?><?= traduci("Password") ?>" rel="<?= $rel_ripeti ?>;8;16;P" <? if ($operazione == "UPDATE") echo "disabled" ?> onChange="valida($('#password'));" autocomplete="off"></td>
                        </tr>
                	        <tr><td><input type="button" value="Random password" onClick="suggest_password('#suggest')"></td><td id="suggest" colspan="3"></td></tr>
                        </table>
                    </div>
										<?
											if ($codice == $_SESSION["codice_utente"]) {
										?>
										<div class="box">
											<h2><?= traduci("Autenticazione a due fattori") ?></h2>
											<button onClick="enableTwoFactor(); return false;"><?= traduci("gestisci") . " " .  traduci("autenticazione a due fattori") ?></button>
										</div>
										<? } ?>
					<div class="box">
	                    <h2><?= traduci("Dati anagrafici") ?></h2>
                        <table width="100%" id="anagrafici">
                        <tr><td class="etichetta"><?= traduci("Nome") ?>*</td><td><input type="text" name="utenti[nome]" id="nome_referente" title="<?= traduci("Nome") ?>" value="<? echo $record_utente["nome"] ?>" rel="S;2;0;A"></td>
                        <td class="etichetta"><?= traduci("Cognome") ?>*</td><td><input type="text" name="utenti[cognome]" id="cognome_referente" title="<?= traduci("Cognome") ?>" value="<? echo $record_utente["cognome"] ?>" rel="S;2;0;A"></td></tr>
                       <tr>
                       <td class="etichetta"><?= traduci("Luogo nascita") ?>*</td><td><input type="text" name="utenti[luogo]" id="luogo_referente" title="<?= traduci("Luogo nascita") ?>" value="<? echo $record_utente["luogo"] ?>" rel="<?= $obbligatorio ?>;2;0;A"></td>
                       <td class="etichetta"><?= traduci("provincia nascita") ?>*</td><td><input type="text" name="utenti[provincia_nascita]" id="provincia_nascita_referente" title="<?= traduci("Provincia nascita") ?>" value="<? echo $record_utente["provincia_nascita"] ?>" rel="<?= $obbligatorio ?>;2;2;A" size="2" maxlength="2"></td></tr>
                        <tr><td class="etichetta"><?= traduci("Data di nascita") ?>*</td><td><input type="text" class="datepick" name="utenti[dnascita]" id="dnascita_referente" title="<?= traduci("Data nascita") ?>" value="<? echo mysql2date($record_utente["dnascita"]) ?>" rel="<?= $obbligatorio ?>;10;10;D" maxlength="10" size="10"></td>
                        	<td class="etichetta"><?= traduci("Sesso") ?>*</td><td><select name="utenti[sesso]" id="sesso_referente" title="Sesso" rel="<?= $obbligatorio ?>;1;1;A"><option value=""><?= traduci("Seleziona") ?>...</option><option value="M">M</option><option value="F">F</option></select></td></tr>

                        <tr>
                        <td class="etichetta"><?= traduci("Codice Fiscale") ?>*</td><td><input type="text" name="utenti[cf]" id="cf_referente" title="<?= traduci("Codice Fiscale") ?>" value="<? echo $record_utente["cf"] ?>" rel="<?= $obbligatorio ?>;9;0;CF" maxlength="16"><input type="button" onClick="calcola_cf($('#nome_referente').val(),$('#cognome_referente').val(),$('#luogo_referente').val(),$('#dnascita_referente').val(),$('#sesso_referente').val(),$('#provincia_nascita_referente').val(),'cf_referente');return false;" value="<?= traduci("Calcola") ?>"></td>
                        <? if ($record_utente["tipo"] != "OPE") { ?>
                        <td class="etichetta"><?= traduci("Partita IVA") ?></td><td><input type="text" name="operatori[partita_iva]" id="partita_iva" title="<?= traduci("Partita IVA") ?>" rel="N;8;0;PICF" value="<? echo $record_operatore["partita_iva"] ?>"></td></tr>
                        <tr><td class="etichetta"><?= traduci("Identificativo Fiscale Estero") ?></td><td colspan="3"><input type="text" name="operatori[identificativoEstero]" id="identificativoEstero" title="<?= traduci("Identificativo Fiscale Estero") ?>" rel="N;10;20;A" value="<? echo $record_operatore["identificativoEstero"] ?>"></td>
                        <? } ?>
</tr>

						</table>
        				<script>
							$("#sesso_referente").val('<? echo $record_utente["sesso"] ?>');
                        </script>
                    </div>
                    <? if ($record_utente["tipo"] == "OPE") { ?>
                    <div class="box">
                    <h2><?= traduci("Ruolo") ?></h2>
                    <table width="100%">
                    <tr><td class="etichetta"><?= traduci("Ruolo") ?>*</td><td colspan="5">
                    <select name="operatori[ruolo_referente]" id="ruolo_referente" title="<?= traduci("Ruolo") ?>" rel="<?= $obbligatorio ?>;0;0;A">
                    	<option value=""><?= traduci("Seleziona") ?>...</option>
											<option><?= traduci("Amministratore delegato") ?></option>
											<option><?= traduci("Amministratore unico") ?></option>
											<option><?= traduci("Consigliere delegato") ?></option>
											<option><?= traduci("Presidente del consiglio") ?></option>
											<option><?= traduci("Socio accomandatario") ?></option>
											<option><?= traduci("Legale rappresentante") ?></option>
											<option><?= traduci("Procuratore speciale") ?></option>
											<option><?= traduci("Direttore tecnico") ?></option>
                    </select>
                    </td></tr>
                    <tr><td class="etichetta"><?= traduci("Procura") ?></td>
                    	<td><select name="operatori[tipo_procura]" id="tipo_procura" title="<?= traduci("Procura") ?>" rel="N;0;0;A">
                        	<option value=""><?= traduci("Nessuna") ?></option>
                            <option><?= traduci("Normale") ?></option>
                            <option><?= traduci("Speciale") ?></option>
                        </select>
                    </td><td class="etichetta"><?= traduci("Numero") ?></td><td><input type="text" name="operatori[numero_procura]" id="numero_procura" value="<? echo $record_operatore["numero_procura"] ?>" title="<?= traduci("Numero") ?> <?= traduci('procura') ?>" rel="N;0;0;A"></td>
                    <td class="etichetta"><?= traduci("Data") ?></td><td><input type="text" name="operatori[data_procura]" size="12" value="<? echo mysql2date($record_operatore["data_procura"]) ?>" class="datepick" id="data_procura" title="<?= traduci("Data") ?> <?= traduci('procura') ?>" rel="N;0;0;D"></td></tr>
                    </table>
                    <script>
						$("#ruolo_referente").val("<? echo $record_operatore["ruolo_referente"]; ?>");
						$("#tipo_procura").val("<? echo $record_operatore["tipo_procura"]; ?>");
					</script>
                    </div>
                    <? } else { ?>
                    <div class="box">
                    	<h2><?= traduci("Dati professionali") ?></h2>
                         <table width="100%">
                         <tr><td class="etichetta"><?= traduci("Titolo di studio") ?>*</td><td colspan="3"><input style="width:98%" type="text" name="operatori[titolo_studio]" id="titolo_studio" title="<?= traduci("Titolo di studio") ?>" value="<? echo $record_operatore["titolo_studio"] ?>" rel="<?= $obbligatorio ?>;5;0;A"></td>
                        </tr>

                    <tr><td class="etichetta"><?= traduci("ordine professionale") ?>*</td><td><select name="operatori[ordine_professionale]" id="ordine_professionale" title="<?= traduci("ordine professionale") ?>" rel="<?= $obbligatorio ?>;0;0;A">
										<option><?= traduci("Archeologi") ?></option>
										<option><?= traduci("Architetti") ?></option>
                    <option><?= traduci("Commercialisti") ?></option>
										<option><?= traduci("Geometri") ?></option>
										<option><?= traduci("Geologi") ?></option>
										<option><?= traduci("Ingegneri") ?></option>
										<option><?= traduci("Periti industriali") ?></option>
										<option><?= traduci("Medici") ?></option>
										<option><?= traduci("Avvocati") ?></option>
										<option><?= traduci("Altro") ?></option>
                    </select></td>
                    <td class="etichetta"><?= traduci("Iscrizione") ?>*</td><td>

								<div id="upload_iscrizione_ordine" rel="iscrizione_ordine" class="scegli_file" style="float:left"><span class="fa fa-folder"></span></div>
								<div id="nome_file_iscrizione_ordine" style="float:left;">
								<?
					$rel = $obbligatorio.";3;0;FP";
					if ($record_operatore["iscrizione_ordine"] != "") {
						$rel = "N;3;0;FP";
						?>
													<a href="/documenti/operatori/<? echo $record_operatore["codice"] ?>/<? echo $record_operatore["iscrizione_ordine"] ?>" title="File allegato"><img src="/img/<? echo substr($record_operatore["iscrizione_ordine"],-3)?>.png" alt="File <? echo substr($record_operatore["iscrizione_ordine"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato</a><br>
													<?
					}
					?>
								</div>
								<input type="hidden" id="iscrizione_ordine" name="iscrizione_ordine" title="<?= traduci("Iscrizione") ?>" rel="<? echo $rel ?>">
								<input type="hidden" class="terminato" id="terminato_iscrizione_ordine" title="Termine upload">
								<div class="clear"></div>
								<div id="progress_bar_iscrizione_ordine" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>

							<script>
								uploader = Array();
								tmp = (function($){
									return (new ResumableUploader($("#upload_iscrizione_ordine")));
								})(jQuery);
								uploader.push(tmp);
							</script>
                    </td>
                    </tr>
                    <tr><td class="etichetta"><?= traduci("numero") ?>*><td><input type="text" name="operatori[numero_iscrizione_professionale]" id="numero_iscrizione_professionale" value="<? echo $record_operatore["numero_iscrizione_professionale"] ?>" title="<?= traduci("numero") ?> - <?= traduci('iscrizione') ?> - <?= traduci('ordine professionale') ?>" rel="<?= $obbligatorio ?>;0;0;A"></td>
                    <td class="etichetta"><?= traduci("Data") ?>*</td><td><input type="text" name="operatori[data_iscrizione_professionale]" size="12" value="<? echo mysql2date($record_operatore["data_iscrizione_professionale"]) ?>" class="datepick" id="data_iscrizione_professionale" title="<?= traduci("Data") ?> - <?= traduci('iscrizione') ?> - <?= traduci('ordine professionale') ?>" rel="<?= $obbligatorio ?>;0;0;D"></td></tr>
										<tr><td class="etichetta"><?= traduci("Curriculum") ?>*</td><td colspan="3">

														<div id="upload_curriculum" rel="curriculum" class="scegli_file" style="float:left"><span class="fa fa-folder"></span></div>
														<div id="nome_file_curriculum" style="float:left;">
															<?
										$rel = $obbligatorio.";3;0;FP";
										if ($record_operatore["curriculum"] != "") {
											$rel = "N;3;0;FP";
											?>
				                            <a href="/documenti/operatori/<? echo $record_operatore["codice"] ?>/<? echo $record_operatore["curriculum"] ?>" title="File allegato"><img src="/img/<? echo substr($record_operatore["curriculum"],-3)?>.png" alt="File <? echo substr($record_operatore["curriculum"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato</a><br>
				                            <?
										}
									?>
														</div>
														<input type="hidden" id="curriculum" name="curriculum" title="<?= traduci("Curriculum") ?>" rel="<? echo $rel ?>">
														<input type="hidden" class="terminato" id="terminato_curriculum" title="Termine upload">
														<div class="clear"></div>
														<div id="progress_bar_curriculum" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>

													<script>
														uploader = Array();
														tmp = (function($){
															return (new ResumableUploader($("#upload_curriculum")));
														})(jQuery);
														uploader.push(tmp);
													</script>

                    </td></tr>
                    </table>
                    </div>
                    <script>
						$("#ordine_professionale").val("<? echo $record_operatore["ordine_professionale"] ?>");
					</script>
					<? } ?>
					<div class="box recapiti">
                    	<h2><?= traduci("Recapiti") ?></h2>
                        <table width="100%" id="recapiti">
                        <tr><td class="etichetta"><?= traduci("Indirizzo") ?>*</td><td colspan="3"><input style="width:98%" type="text" riferimento="recapiti" name="utenti[indirizzo]" id="indirizzo_referente" title="<?= traduci("Indirizzo") ?>" value="<? echo $record_utente["indirizzo"] ?>" rel="<?= $obbligatorio ?>;5;0;A"></td>
                        </tr>
                        <tr><td class="etichetta"><?= traduci('citta') ?>*</td><td><input type="text"  name="utenti[citta]" id="citta_referente" title="<?= traduci('citta') ?>" value="<? echo $record_utente["citta"] ?>" rel="<?= $obbligatorio ?>;2;0;A"></td>
													<td class="etichetta"><?= traduci("Provincia") ?>*</td><td>
														<select name="utenti[provincia]" id="provincia" title="<?= traduci("Provincia") ?>" rel="<?= $obbligatorio ?>;2;255;A">
															<option value=""><?= traduci("Seleziona") ?></option>
															<?
																 foreach($province AS $provincia) {
																	 ?>
																	 <option value="<?= $provincia["sigla"] ?>" <?= ($provincia["sigla"] == $record_utente["provincia"]) ? "selected" : "" ?>><?= $provincia["provincia"] ?></option>
																	 <?
																 }
															?>
														</select>
													</td></tr><tr>
													<td class="etichetta"><?= traduci("Regione") ?>*</td><td>
														<select  type="text" name="utenti[regione]" id="regione" title="<?= traduci("Regione") ?>" rel="<?= $obbligatorio ?>;2;255;A" >
															<option value=""><?= traduci("Seleziona") ?></option>
															<?
																 foreach($regioni AS $regione) {
																	 ?>
																	 <option value="<?= $regione ?>" <?= ($regione == $record_utente["regione"]) ? "selected" : "" ?>><?= $regione ?></option>
																	 <?
																 }
															?>
														</select>
													</td>
													<td class="etichetta"><?= traduci("nazione") ?>*</td>
													<td>
														<select type="text" name="utenti[stato]" id="stato" title="<?= traduci("nazione") ?>" rel="<?= $obbligatorio ?>;2;0;A">
															<option value=""><?= traduci("Seleziona") ?></option>
															<?
																 foreach(getStatiUE() AS $sigla => $stato) {
																	 ?>
																	 <option value="<?= $sigla ?>" <?= ($sigla == $record_utente["stato"]) ? "selected" : "" ?>><?= $stato ?></option>
																	 <?
																 }
															?>
														</select>
													</td>
                       </tr>
                        <tr><td class="etichetta"><?= traduci("telefono") ?></td><td><input type="text" name="utenti[telefono]" id="telefono_referente" title="<?= traduci("telefono") ?>" value="<? echo $record_utente["telefono"] ?>" rel="N;0;0;A"></td>
                        <td class="etichetta"><?= traduci("Cellulare") ?></td><td><input type="text" name="utenti[cellulare]" id="cellulare_referente" title="<?= traduci("Cellulare") ?>" value="<? echo $record_utente["cellulare"] ?>" rel="N;0;0;A"></td></tr>
                        <tr><td class="etichetta"><?= traduci("PEC") ?>*</td><td colspan="3"><input style="width:400px;" type="text" name="utenti[pec]" id="pec_referente" title="<?= traduci("PEC") ?>" value="<? echo $record_utente["pec"] ?>" rel="S;2;0;E;/user/check_pec.php"></td></tr>
                        </table>
                    </div>
					<a class="precedente" style="float:left" href="#"><?= traduci("precedente") ?></a>
                    <a class="successivo" style="float:right" href="#"><?= traduci("successivo") ?></a>
                    <div class="clear"></div>
	               </div>
                   <? if ($record_utente["tipo"] == "OPE") { ?>
                   <div id="azienda">
	                   <h1><?= traduci("azienda") ?></h1>
                       <div class="box">
                       <table width="100%">
                       <tr><td class="etichetta"><?= traduci("Partita IVA") ?>*</td><td colspan="3"><input type="text" name="operatori[partita_iva]" id="partita_iva" dest="ragione_sociale" title="<?= traduci("Partita IVA") ?>" rel="<?= $obbligatorio ?>;8;0;PI" value="<? echo $record_operatore["partita_iva"] ?>"></td></tr>
                       <tr><td class="etichetta"><?= traduci("Ragione Sociale") ?>*</td><td colspan="3"><input type="text" name="operatori[ragione_sociale]" style="width:95%" id="ragione_sociale" title="<?= traduci("Ragione Sociale") ?>" rel="<?= $obbligatorio ?>;0;0;A" value="<? echo $record_operatore["ragione_sociale"] ?>"></td>
                       </tr>
                        <tr>
                          <td class="etichetta"><?= traduci("Codice Fiscale") ?>*</td><td><input type="text" name="operatori[codice_fiscale_impresa]" id="codice_fiscale_impresa" title="<?= traduci("Codice Fiscale") ?> - <?= traduci("Azienda") ?>" rel="S;8;0;PICF" value="<? echo $record_operatore["codice_fiscale_impresa"] ?>"></td>
                          <td class="etichetta"><?= traduci("Identificativo Fiscale Estero") ?></td><td>
                          	<input type="text" name="operatori[identificativoEstero]" id="identificativoEstero" title="<?= traduci("Identificativo Fiscale Estero") ?>" rel="N;2;20;A" value="<? echo $record_operatore["identificativoEstero"] ?>"></td>
                      </tr>
                        <td class="etichetta"><?= traduci("Numero dipendenti") ?></td><td><input type="text" name="operatori[n_dipendenti]" id="n_dipendenti" title="<?= traduci("Numero dipendenti") ?>" rel="N;1;0;N" value="<? echo $record_operatore["n_dipendenti"] ?>"></td>
                       <td class="etichetta"><?= traduci("codice attivita") ?></td>
                       <td>
                       	<input type="text" name="operatori[codice_attivita]" id="codice_attivita" title="<?= traduci("codice attivita") ?>" rel="N;1;0;A" value="<? echo $record_operatore["codice_attivita"] ?>">
                       </td>
                       </tr>
                       <tr>
                       <td class="etichetta"><?= traduci("Capitale Sociale") ?></td><td><input type="text" name="operatori[capitale_sociale]" id="capitale_sociale" title="<?= traduci("Capitale Sociale") ?>" rel="N;1;0;N" value="<? echo $record_operatore["capitale_sociale"] ?>"></td>
                       <td class="etichetta"><?= traduci("Capitale Versato") ?></td><td><input type="text" name="operatori[capitale_versato]" id="capitale_versato" title="<?= traduci("Capitale versato") ?>" rel="N;1;0;N" value="<? echo $record_operatore["capitale_versato"] ?>"></td>
                       </tr>
                       <tr>
												 <td class="etichetta"><?= traduci("Dimensione") ?> *</td>
												 <td><select name="operatori[pmi]" id="pmi" title="<?= traduci("Dimensione") ?>" rel="<?= $obbligatorio ?>;1;1;A">
													 <option value=""><?= traduci("Seleziona") ?>...</option>
													 <option value="C"><?= traduci("Micro") ?></option>
													 <option value="P"><?= traduci("Piccola") ?></option>
													 <option value="M"><?= traduci("Media") ?></option>
													 <option value="G"><?= traduci("Grande") ?></option>
												 </select>
												 <script>
												 	$("#pmi").val('<?= $record_operatore["pmi"] ?>');
												 </script>
											 	 </td>
												 <td class="etichetta"><?= traduci("Curriculum") ?> <?= traduci("azienda") ?></td><td>

																										<div id="upload_curriculum" rel="curriculum" class="scegli_file" style="float:left"><span class="fa fa-folder"></span></div>
																										<div id="nome_file_curriculum" style="float:left;">
																											<?
																						$rel = "N;3;0;FP";
																						if ($record_operatore["curriculum"] != "") {
																							$rel = "N;3;0;FP";
																							?>
																                            <a href="/documenti/operatori/<? echo $record_operatore["codice"] ?>/<? echo $record_operatore["curriculum"] ?>" title="File allegato"><img src="/img/<? echo substr($record_operatore["curriculum"],-3)?>.png" alt="File <? echo substr($record_operatore["curriculum"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato</a><br>
																                            <?
																						}
																					?>
																										</div>
																										<input type="hidden" id="curriculum" name="curriculum" title="<?= traduci("Curriculum") ?> <?= traduci("azienda") ?>" rel="<? echo $rel ?>">
																										<input type="hidden" class="terminato" id="terminato_curriculum" title="Termine upload">
																										<div class="clear"></div>
																										<div id="progress_bar_curriculum" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>

																									<script>
																										uploader = Array();
																										tmp = (function($){
																											return (new ResumableUploader($("#upload_curriculum")));
																										})(jQuery);
																										uploader.push(tmp);
																									</script>

                       </td></tr>
                       </table>
                       </div>
                       <div class="box sede_legale">
                    	<h2- ><?= traduci("Sede legale") ?></h2>
                        <table width="100%" id="recapiti">
                            <tr><td class="etichetta"><?= traduci("Indirizzo") ?>*</td><td colspan="3"><input style="width:99%" riferimento="sede_legale" type="text" name="operatori[indirizzo_legale]" id="indirizzo_legale" title="<?= traduci("Indirizzo") ?> - <?= traduci("Sede legale") ?>" value="<? echo $record_operatore["indirizzo_legale"] ?>" rel="<?= $obbligatorio ?>;5;0;A"></td></tr>
                        <tr><td class="etichetta"><?= traduci("Citta") ?>*</td><td><input type="text" name="operatori[citta_legale]" id="citta_legale" title="<?= traduci("Citta") ?> - <?= traduci("Sede legale") ?>" value="<? echo $record_operatore["citta_legale"] ?>" rel="<?= $obbligatorio ?>;2;0;A"></td>
                       <td class="etichetta"><?= traduci("Provincia") ?>*</td><td>
												 <select name="operatori[provincia_legale]" id="provincia_legale" title="<?= traduci("Provincia") ?> - <?= traduci("Sede legale") ?>" rel="<?= $obbligatorio ?>;2;255;A">
													 <option value=""><?= traduci("Seleziona") ?></option>
													 <?
															foreach($province AS $provincia) {
																?>
																<option value="<?= $provincia["sigla"] ?>" <?= ($provincia["sigla"] == $record_operatore["provincia_legale"]) ? "selected" : "" ?>><?= $provincia["provincia"] ?></option>
																<?
															}
													 ?>
												 </select>
											 </td></tr><tr>
                       <td class="etichetta"><?= traduci("Regione") ?>*</td><td>
												 <select  type="text" name="operatori[regione_legale]" id="regione_legale" title="<?= traduci("Regione") ?> - <?= traduci("Sede legale") ?>" rel="<?= $obbligatorio ?>;2;255;A" >
													 <option value=""><?= traduci("Seleziona") ?></option>
													 <?
															foreach($regioni AS $regione) {
																?>
																<option value="<?= $regione ?>" <?= ($regione == $record_operatore["regione_legale"]) ? "selected" : "" ?>><?= $regione ?></option>
																<?
															}
													 ?>
												 </select>
											</td>
											 <td class="etichetta"><?= traduci("nazione") ?> *</td>
											 <td>
												 <select type="text" name="operatori[stato_legale]" id="stato_legale" title="<?= traduci("nazione") ?> - <?= traduci("Sede legale") ?>" rel="<?= $obbligatorio ?>;2;0;A">
													 <option value=""><?= traduci("Seleziona") ?></option>
													 <?
															foreach(getStatiUE() AS $sigla => $stato) {
																?>
																<option value="<?= $sigla ?>" <?= ($sigla == $record_operatore["stato_legale"]) ? "selected" : "" ?>><?= $stato ?></option>
																<?
															}
													 ?>
												 </select>
											</td>
												</tr>
                        </table>
                    </div>
                     <div class="box sede_operativa">
                    	<h2><?= traduci("Sede operativa") ?></h2>
                        <table width="100%" id="recapiti">
                            <tr><td class="etichetta"><?= traduci("Indirizzo") ?>*</td><td colspan="3"><input style="width:99%" riferimento="sede_operativa" type="text" name="operatori[indirizzo_operativa]" id="indirizzo_operativa" title="<?= traduci("Indirizzo") ?> <?= traduci("Sede operativa") ?>" value="<? echo $record_operatore["indirizzo_operativa"] ?>" rel="<?= $obbligatorio ?>;5;0;A"></td></tr>
                        <tr><td class="etichetta"><?= traduci("Citta") ?>*</td><td><input type="text" name="operatori[citta_operativa]" id="citta_operativa" title="<?= traduci("Citta") ?> <?= traduci("Sede operativa") ?>" value="<? echo $record_operatore["citta_operativa"] ?>" rel="<?= $obbligatorio ?>;2;0;A"></td>
													<td class="etichetta"><?= traduci("Provincia") ?>*</td><td>
														<select name="operatori[provincia_operativa]" id="provincia_operativa" title="<?= traduci("Provincia") ?> <?= traduci("Sede operativa") ?>" rel="<?= $obbligatorio ?>;2;255;A">
															<option value=""><?= traduci("Seleziona") ?></option>
															<?
																 foreach($province AS $provincia) {
																	 ?>
																	 <option value="<?= $provincia["sigla"] ?>" <?= ($provincia["sigla"] == $record_operatore["provincia_operativa"]) ? "selected" : "" ?>><?= $provincia["provincia"] ?></option>
																	 <?
																 }
															?>
														</select>
													</td></tr><tr>
													<td class="etichetta"><?= traduci("Regione") ?>*</td><td>
														<select  type="text" name="operatori[regione_operativa]" id="regione_operativa" title="<?= traduci("Regione") ?> <?= traduci("Sede operativa") ?>" rel="<?= $obbligatorio ?>;2;255;A" >
															<option value=""><?= traduci("Seleziona") ?></option>
															<?
																 foreach($regioni AS $regione) {
																	 ?>
																	 <option value="<?= $regione ?>" <?= ($regione == $record_operatore["regione_operativa"]) ? "selected" : "" ?>><?= $regione ?></option>
																	 <?
																 }
															?>
														</select>
													</td>
													<td class="etichetta"><?= traduci("nazione") ?>*</td>
													<td>
														<select type="text" name="operatori[stato_operativa]" id="stato_operativa" title="<?= traduci("nazione") ?> - <?= traduci("Sede operativa") ?>" rel="<?= $obbligatorio ?>;2;0;A">
															<option value=""><?= traduci("Seleziona") ?></option>
															<?
																 foreach(getStatiUE() AS $sigla => $stato) {
																	 ?>
																	 <option value="<?= $sigla ?>" <?= ($sigla == $record_operatore["stato_operativa"]) ? "selected" : "" ?>><?= $stato ?></option>
																	 <?
																 }
															?>
														</select>
													</td>
											 </tr>
                        </table>
                    </div>
                    <div class="box">
                    <h2><?= traduci("Camera di commercio") ?></h2>
                    	<table width="100%">
                        	<tr><td class="etichetta"><?= traduci("Sede") ?></td><td><input type="text" name="operatori[sede_cc]" id="sede_cc" title="<?= traduci("Sede") ?> - <?= traduci("Camera di commercio") ?>" value="<? echo $record_operatore["sede_cc"] ?>" rel="N;2;0;A"></td>
                            <td class="etichetta"><?= traduci("Numero") ?> <?= traduci("iscrizione") ?></td><td><input type="text" name="operatori[numero_iscrizione_cc]" id="numero_iscrizione_cc" title="<?= traduci("Numero") ?> <?= traduci("iscrizione") ?> - <?= traduci("Camera di commercio") ?>" value="<? echo $record_operatore["numero_iscrizione_cc"] ?>" rel="N;2;0;A"></td>
                            <td class="etichetta"><?= traduci("Data") ?> <?= traduci("iscrizione") ?></td><td><input type="text" name="operatori[data_iscrizione_cc]" size="12" value="<? echo mysql2date($record_operatore["data_iscrizione_cc"]) ?>" class="datepick" id="data_iscrizione_cc" title="<?= traduci("Data") ?> <?= traduci("iscrizione") ?> - <?= traduci("Camera di commercio") ?>" rel="N;10;10;D"></td></tr>
														<tr><td class="etichetta"><?= traduci("Certificato camerale") ?></td><td colspan="3">

															<div id="upload_certificato_camerale" rel="certificato_camerale" class="scegli_file" style="float:left"><span class="fa fa-folder"></span></div>
															<div id="nome_file_certificato_camerale" style="float:left;">
																 <?
																						$rel = "N;3;0;FP";
																						if ($record_operatore["certificato_camerale"] != "") {
																							$rel = "N;3;0;FP";
																							?>
					                            <a href="/documenti/operatori/<? echo $record_operatore["codice"] ?>/<? echo $record_operatore["certificato_camerale"] ?>" title="File allegato"><img src="/img/<? echo substr($record_operatore["certificato_camerale"],-3)?>.png" alt="File <? echo substr($record_operatore["certificato_camerale"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato</a><br>
					                            <? } ?>
															</div>
															<input type="hidden" id="certificato_camerale" name="certificato_camerale" title="<?= traduci("Certificato camerale") ?>" rel="<? echo $rel ?>">
															<input type="hidden" class="terminato" id="terminato_certificato_camerale" title="Termine upload">
															<div class="clear"></div>
															<div id="progress_bar_certificato_camerale" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>

														<script>
															uploader = Array();
															tmp = (function($){
																return (new ResumableUploader($("#upload_certificato_camerale")));
															})(jQuery);
															uploader.push(tmp);
														</script>
													</td>
													<td class="etichetta"><?= traduci("Data") ?></td><td><input type="text" class="datepick" name="operatori[data_emissione_certificato]" id="data_emissione_certificato" title="<?= traduci("Data") ?> - <?= traduci("certificato camerale") ?>" rel="N;10;10;D" value="<? echo mysql2date($record_operatore["data_emissione_certificato"]) ?>"></td></tr>
                        </table>
                    </div>
                    <div class="box">
                    <h2>INPS</h2>
                    	<table width="100%">
                        	<tr><td class="etichetta"><?= traduci("Sede") ?></td><td><input type="text" name="operatori[sede_inps]" id="sede_inps" title="<?= traduci("Sede") ?> - INPS" value="<? echo $record_operatore["sede_inps"] ?>" rel="N;2;0;A"></td>
                            <td class="etichetta"><?= traduci("Matricola") ?></td><td><input type="text" name="operatori[matricola_inps]" id="matricola_inps" title="<?= traduci("Matricola") ?> - INPS" value="<? echo $record_operatore["matricola_inps"] ?>" rel="N;2;0;A"></td></tr>
                        </table>
                    </div>
                     <div class="box">
                    <h2>INAIL</h2>
                    	<table width="100%">
                        	<tr><td class="etichetta"><?= traduci("Sede") ?></td><td><input type="text" name="operatori[sede_inail]" id="sede_inail" title="><?= traduci("Sede") ?> - INAIL" value="<? echo $record_operatore["sede_inail"] ?>" rel="N;2;0;A"></td>
                            <td class="etichetta"><?= traduci("codice") ?></td><td><input type="text" name="operatori[codice_inail]" id="codice_inail" title="<?= traduci("codice") ?> - INAIL" value="<? echo $record_operatore["codice_inail"] ?>" rel="N;2;0;A"></td>
                            <td class="etichetta">PAT</td><td><input type="text" name="operatori[pat_inail]" id="pat_inail" title="PAT - INAIL" value="<? echo $record_operatore["pat_inail"] ?>" rel="N;2;0;A"></td></tr>
                        </table>
                    </div>
                    <div class="box">
                    <h2>Cassa Edile</h2>
                    	<table width="100%">
                        	<tr><td class="etichetta"><?= traduci("Sede") ?></td><td><input type="text" name="operatori[sede_cassaedile]" id="sede_cassaedile" title="><?= traduci("Sede") ?> - Cassa Edile" value="<? echo $record_operatore["sede_cassaedile"] ?>" rel="N;2;0;A"></td>
                            <td class="etichetta"><?= traduci("codice") ?></td><td><input type="text" name="operatori[codice_cassaedile]" id="codice_cassaedile" title="<?= traduci("codice") ?> - Cassa Edile" value="<? echo $record_operatore["codice_cassaedile"] ?>" rel="N;2;0;A"></td>
                            <td class="etichetta"><?= traduci("Matricola") ?></td><td><input type="text" name="operatori[matricola_cassaedile]" id="matricola_cassaedile" title="<?= traduci("Matricola") ?> - Cassa Edile" value="<? echo $record_operatore["matricola_cassaedile"] ?>" rel="N;2;0;A"></td></tr>
                        </table>
                    </div>
                    <div class="box">
                    	<h2><?= traduci("Dati Bancari") ?></h2>
                        <table width="100%">
                        	<tr><td class="etichetta"><?= traduci("Banca") ?>*</td><td><input type="text" name="operatori[banca]" style="width:90%;" id="banca" title="<?= traduci("Banca") ?>" value="<? echo $record_operatore["banca"] ?>" rel="<?= $obbligatorio ?>;2;0;A"></td></tr>
                            <tr><td class="etichetta"><?= traduci("IBAN") ?>*</td><td><input type="text" name="operatori[iban]" size="32" id="iban" title="<?= traduci("IBAN") ?>" value="<? echo $record_operatore["iban"] ?>" rel="<?= $obbligatorio ?>;2;0;IB"></td></tr>
                            <tr><td class="etichetta"><?= traduci("Intestatario") ?>*</td><td><input type="text"  style="width:90%;" name="operatori[intestatario]" id="intestatario" title="<?= traduci("Intestatario") ?>" value="<? echo $record_operatore["intestatario"] ?>" rel="<?= $obbligatorio ?>;2;0;A"></td></tr>
                        </table>
                    </div>
    	               <a class="precedente" style="float:left" href="#"><?= traduci("precedente") ?></a>
        	           <a class="successivo" style="float:right" href="#"><?= traduci("successivo") ?></a>
            	        <div class="clear"></div>
                   </div>
                   <div id="organizzazione">
	                   <h1><?= traduci("Organizzazione") ?></h1>
                       <div class="box">
	                       <h2><?= traduci("Rappresentanti legali") ?></h2>
                           <table width="100%" >
                           	<tbody id="rappresentanti">
								<? if (isset($risultato_rappresentanti) && $risultato_rappresentanti->rowCount() > 0) {
						   			while ($rappresentanti = $risultato_rappresentanti->fetch(PDO::FETCH_ASSOC)) {
										$id = $rappresentanti["codice"];
										include("rappresentanti/form.php");
									}
								} else {
									$id = "i_0";
									$rappresentanti = get_campi("b_rappresentanti");
									include("rappresentanti/form.php");
								}?>
                            </tbody>
                            <tfoot>
                            <tr><td colspan="2">
                       <button class="aggiungi" onClick="aggiungi('rappresentanti/form.php','#rappresentanti');return false;"><img src="/img/add.png" alt="<?= traduci("Aggiungi") ?>"><?= traduci("Aggiungi") ?></button></td></tr>
                            </tfoot>
                           </table>
                       </div>
                       <div class="box">
	                       <h2>CCNL</h2>
                           <table width="100%" >
                           	<tbody id="ccnl">
								<? if (isset($risultato_ccnl) && $risultato_ccnl->rowCount() > 0) {
						   			while ($ccnl = $risultato_ccnl->fetch(PDO::FETCH_ASSOC)) {
										$id = $ccnl["codice"];
										include("ccnl/form.php");
									}
								} else {
									$id = "i_0";
									$ccnl = get_campi("b_ccnl");
									include("ccnl/form.php");
								}?>
                            </tbody>
                            <tfoot>
                            <tr><td colspan="2">
                       <button class="aggiungi" onClick="aggiungi('ccnl/form.php','#ccnl');return false;"><img src="/img/add.png" alt="<?= traduci("Aggiungi") ?>"><?= traduci("Aggiungi") ?></button></td></tr>
                            </tfoot>
                           </table>
                       </div>
						<div class="clear"></div>
    	               <a class="precedente" style="float:left" href="#"><?= traduci("precedente") ?></a>
        	           <a class="successivo" style="float:right" href="#"><?= traduci("successivo") ?></a>
            	        <div class="clear"></div>
                   </div>
                   <? } ?>
                   <div id="categorie">
                   <h1><?= traduci("Categorie merceologiche") ?> *</h1>
                   		<? include("categorie/form.php"); ?>
                        <div class="clear"></div>
    	               <a class="precedente" style="float:left" href="#"><?= traduci("precedente") ?></a>
        	           <a class="successivo" style="float:right" href="#"><?= traduci("successivo") ?></a>
                       <div class="clear"></div>
                        </div>
									<? if (!isset($bozza) || (isset($bozza) && !$bozza)) { ?>
                  <div id="committenti">
	                   <h1><?= traduci("Committenti") ?></h1>
                       <div class="box">
                        <table width="100%" >
                           	<tbody id="tab_committenti">
                       <? if (isset($risultato_committenti) && $risultato_committenti->rowCount() > 0) {
						   			while ($committenti = $risultato_committenti->fetch(PDO::FETCH_ASSOC)) {
										$id = $committenti["codice"];
										include("committenti/form.php");
									}
					   		}
						?>
                        </tbody>
                            <tfoot>
                            <tr><td colspan="2">
                       <button class="aggiungi" onClick="aggiungi('committenti/form.php','#tab_committenti');return false;"><img src="/img/add.png" alt="<?= traduci("Aggiungi") ?>"><?= traduci("Aggiungi") ?></button></td></tr>
                            </tfoot>
                           </table>
                       <div class="clear"></div>
                       </div>
    	               <a class="precedente" style="float:left" href="#"><?= traduci("precedente") ?></a>
        	           <a class="successivo" style="float:right" href="#"><?= traduci("successivo") ?></a>
                       <div class="clear"></div>
                       </div>
										 <? } ?>
                        <div id="certificazioni">
                        <h1><?= traduci("CERTIFICAZIONI") ?></h1>
                       <div class="box">
                       <h2><?= traduci("Certificazioni di qualita") ?></h2>
                         <table width="100%" >
                           	<tbody id="tab_qualita">
                       <? if (isset($risultato_qualita) && $risultato_qualita->rowCount() > 0) {
						   			while ($qualita = $risultato_qualita->fetch(PDO::FETCH_ASSOC)) {
										$id = $qualita["codice"];
										include("qualita/form.php");
									}
					   		}
						?>
                        </tbody>
                            <tfoot>
                            <tr><td colspan="2">
                       <button class="aggiungi" onClick="aggiungi('qualita/form.php','#tab_qualita');return false;"><img src="/img/add.png" alt="<?= traduci("Aggiungi") ?>"><?= traduci("Aggiungi") ?></button></td></tr>
                            </tfoot>
                           </table>
                           <div class="clear"></div>
                           </div>
                            <div class="box">
                       <h2><?= traduci("Certificazioni di gestione ambientale") ?></h2>
                         <table width="100%" >
                           	<tbody id="tab_ambientali">
                       <? if (isset($risultato_ambientali) && $risultato_ambientali->rowCount() > 0) {
						   			while ($ambientali = $risultato_ambientali->fetch(PDO::FETCH_ASSOC)) {
										$id = $ambientali["codice"];
										include("ambientali/form.php");
									}
					   		}
						?>
                        </tbody>
                            <tfoot>
                            <tr><td colspan="2">
                       <button class="aggiungi" onClick="aggiungi('ambientali/form.php','#tab_ambientali');return false;"><img src="/img/add.png" alt="<?= traduci("Aggiungi") ?>"><?= traduci("Aggiungi") ?></button></td></tr>
                            </tfoot>
                           </table>
                         <div class="clear"></div>
                         </div>
												<div class="box">
                       <h2>SOA</h2>
											 <small style="color:#c00">
												 <strong><?= traduci('attenzione') ?>:</strong> <?= traduci('alert-filtro-gara') ?></strong>.
											 </small>
                         <table width="100%" >
                           	<tbody id="tab_soa">
                       <? if (isset($risultato_soa) && $risultato_soa->rowCount() > 0) {
						   			while ($soa = $risultato_soa->fetch(PDO::FETCH_ASSOC)) {
										$id = $soa["codice"];
										include("soa/form.php");
									}
					   		}
						?>
                        </tbody>
                            <tfoot>
                            <tr><td colspan="2">
                       <button class="aggiungi" onClick="aggiungi('soa/form.php','#tab_soa');return false;"><img src="/img/add.png" alt="<?= traduci("Aggiungi") ?>"><?= traduci("Aggiungi") ?></button></td></tr>
                            </tfoot>
                           </table>
                           <div class="clear"></div>
                           </div>
													 <div class="box">
	                        <h2><?= traduci("Fatturati") ?></h2>
													<small style="color:#c00">
														<?= traduci("msg-reg-fatturati") ?><br>
														<strong><?= traduci('attenzione') ?>:</strong> <?= traduci('alert-filtro-gara') ?></strong>.
													</small>
	                          <table width="100%" >
	                            	<tbody id="tab_soa_fatt">
	                        <? if (isset($risultato_soa_fatturato) && $risultato_soa_fatturato->rowCount() > 0) {
															while ($soa_fatt = $risultato_soa_fatturato->fetch(PDO::FETCH_ASSOC)) {
																$id = $soa_fatt["codice"];
																include("soa_fatturato/form.php");
															}
														} ?>
	                         </tbody>
	                             <tfoot>
	                             <tr><td colspan="2">
	                        <button class="aggiungi" onClick="aggiungi('soa_fatturato/form.php','#tab_soa_fatt');return false;"><img src="/img/add.png" alt="<?= traduci("Aggiungi") ?>"><?= traduci("Aggiungi") ?></button></td></tr>
	                             </tfoot>
	                            </table>
	                            <div class="clear"></div>
	                            </div>
											<div class="box">
												<h2><?= traduci("Progettazione") ?></h2>
												<table width="100%" >
													 <tbody id="tab_progettazione">
											<? if (isset($risultato_progettazione) && $risultato_progettazione->rowCount() > 0) {
									 while ($progettazione = $risultato_progettazione->fetch(PDO::FETCH_ASSOC)) {
									 $id = $progettazione["codice"];
									 include("progettazione/form.php");
								 }
							 }
					 ?>
											 </tbody>
													 <tfoot>
													 <tr><td colspan="2">
											<button class="aggiungi" onClick="aggiungi('progettazione/form.php','#tab_progettazione');return false;"><img src="/img/add.png" alt="<?= traduci("Aggiungi") ?>"><?= traduci("Aggiungi") ?></button></td></tr>
													 </tfoot>
													</table>
													<div class="clear"></div>
											</div>
    	              <div class="box">
                       <h2><?= traduci("Altre certificazioni") ?></h2>
                         <table width="100%" >
                           	<tbody id="tab_certificazioni">
                       <? if (isset($risultato_certificazioni) && $risultato_certificazioni->rowCount() > 0) {
						   			while ($certificazioni = $risultato_certificazioni->fetch(PDO::FETCH_ASSOC)) {
										$id = $certificazioni["codice"];
										include("certificazioni/form.php");
									}
					   		}
						?>
                        </tbody>
                            <tfoot>
                            <tr><td colspan="2">
                       <button class="aggiungi" onClick="aggiungi('certificazioni/form.php','#tab_certificazioni');return false;"><img src="/img/add.png" alt="<?= traduci("Aggiungi") ?>"><?= traduci("Aggiungi") ?></button></td></tr>
                            </tfoot>
                           </table>
                         <div class="clear"></div>
                         </div>
    	               <a class="precedente" style="float:left" href="#"><?= traduci("precedente") ?></a>
        	           <a class="successivo" style="float:right" href="#"><?= traduci("successivo") ?></a>
                       <div class="clear"></div>
                       </div>
                       <div id="brevetti">
                       <h1><?= traduci("Brevetti") ?></h1>
                       <div class="box">
                         <table width="100%" >
                           	<tbody id="tab_brevetti">
                       <? if (isset($risultato_brevetti) && $risultato_brevetti->rowCount() > 0) {
						   			while ($brevetti = $risultato_brevetti->fetch(PDO::FETCH_ASSOC)) {
										$id = $brevetti["codice"];
										include("brevetti/form.php");
									}
					   		}
						?>
                        </tbody>
                            <tfoot>
                            <tr><td colspan="2">
                       <button class="aggiungi" onClick="aggiungi('brevetti/form.php','#tab_brevetti');return false;"><img src="/img/add.png" alt="<?= traduci("Aggiungi") ?>"><?= traduci("Aggiungi") ?></button></td></tr>
                            </tfoot>
                           </table>
                         <div class="clear"></div>
                         </div>
    	               <a class="precedente" style="float:left" href="#"><?= traduci("precedente") ?></a>
        	           <a class="successivo" style="float:right" href="#"><?= traduci("successivo") ?></a>
                       <div class="clear"></div>
                       </div>
               </div>
							<? if (isset($bozza) && $bozza) {
								if($_SESSION["record_utente"]["attivo"] == 'N') {
									?>
									<div class="ui-state-error padding">
										<h3><?= traduci('attenzione') ?>: <?= traduci("La registrazione non è ancora stata confermata") ?></h3>
									</div><br>
									<?
								}
							} ?>
              <input type="submit" class="submit_big" value="Salva">
	</form>

			<div class="clear"></div>
         	<script type="text/javascript">
				$("#tabs").tabs();
				$(".precedente").each(function() {
					var id_parent = $("#tabs").children("div").index($(this).parent("div"));
					if (id_parent == 0) {
						$(this).remove();
					} else {
						var target = id_parent - 1;
						$(this).click(function() { $('#tabs').tabs('option','active',target) });
					}
				});

				$(".successivo").each(function() {
					var id_parent = $("#tabs").children("div").index($(this).parent("div"));

					if (id_parent == ($("#tabs").children("div").length - 1)) {
						$(this).remove();
					} else {
						var target = id_parent + 1;
						$(this).click(function() { $('#tabs').tabs('option','active',target) });
					}
				});
				$(".registrazione *:input").focusout(function() {
					check_error_tabs();
				});
			 	$("#anagrafici").find("input").keypress(function() {
					if ($(this).attr("id")!="cf") {
						$("#cf").val("");
						}
				});
			 </script>
<? } ?>
