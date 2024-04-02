<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	include_once($root."/inc/p7m.class.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("dialogo_competitivo",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

		if (isset($_GET["codice"])&&isset($_GET["codice_bando"])) {
					$codice = $_GET["codice"];
					$codice_bando = $_GET["codice_bando"];
					$bind =	array(':codice_bando' => $codice_bando);
					$strsql= "SELECT * FROM b_bandi_dialogo WHERE codice = :codice_bando ";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount() > 0) {
						$record_bando = $risultato->fetch(PDO::FETCH_ASSOC);
						if (strtotime($record_bando["data_scadenza"]) <= time()) {
							$cpv_bando = array();
							$cpv = array();
							$bind = array(':codice' => $record_bando["codice"]);
							$strsql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_bandi_dialogo ON b_cpv.codice = r_cpv_bandi_dialogo.codice WHERE r_cpv_bandi_dialogo.codice_bando = :codice ORDER BY codice";
							$risultato_cpv = $pdo->bindAndExec($strsql,$bind);

							if ($risultato_cpv->rowCount()>0) while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) $cpv_bando[] = $rec_cpv["codice"];

							$bind =	array(':codice_bando' => $record_bando["codice"], ':codice_operatore'=>$codice);
							$strsql = "UPDATE r_partecipanti_dialogo SET visto = 'S' WHERE codice_bando = :codice_bando AND codice_operatore = :codice_operatore ";
							$risultato = $pdo->bindAndExec($strsql,$bind);
							$bind =	array(':codice_operatore'=>$codice);
							$strsql = "SELECT * FROM b_operatori_economici WHERE codice = :codice_operatore ";
							$risultato = $pdo->bindAndExec($strsql,$bind);
								if ($risultato->rowCount() > 0) {
									$record_operatore = $risultato->fetch(PDO::FETCH_ASSOC);
									$bind =	array(':codice_bando' => $record_bando["codice"], ':codice_operatore'=>$record_operatore["codice"]);
									/* $bind[":codice_utente"] = $record_operatore["codice_utente"];
									$bind[":codice_operatore"] = $record_operatore["codice"]; */
									$strsql= "SELECT * FROM r_partecipanti_dialogo WHERE codice_bando = :codice_bando AND codice_operatore = :codice_operatore ";
									$risultato = $pdo->bindAndExec($strsql,$bind);
									if ($risultato->rowCount() > 0) {
										$bind = array();
										$bind[":codice_utente"] = $record_operatore["codice_utente"];
										$record_partecipante = $risultato->fetch(PDO::FETCH_ASSOC);
										$strsql = "SELECT b_utenti.*, b_gruppi.id as tipo FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice WHERE b_utenti.codice = :codice_utente ";
										$risultato = $pdo->bindAndExec($strsql,$bind);
										if ($risultato->rowCount() > 0) {
											$bind = array();
											$bind[":codice_operatore"] = $record_operatore["codice"];
											$record_utente = $risultato->fetch(PDO::FETCH_ASSOC);
											$strsql = "SELECT * FROM b_ccnl WHERE codice_operatore = :codice_operatore ";
											$risultato_ccnl = $pdo->bindAndExec($strsql,$bind);
											$strsql = "SELECT * FROM b_rappresentanti WHERE codice_operatore = :codice_operatore ";
											$risultato_rappresentanti = $pdo->bindAndExec($strsql,$bind);
											$string_cpv = "";
											$cpv = array();
											$strsql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_operatori ON b_cpv.codice = r_cpv_operatori.codice WHERE r_cpv_operatori.codice_operatore = :codice_operatore ORDER BY codice";
											$risultato_cpv = $pdo->bindAndExec($strsql,$bind);
											$strsql = "SELECT * FROM b_committenti WHERE codice_operatore = :codice_operatore";
											$risultato_committenti = $pdo->bindAndExec($strsql,$bind);
											$strsql = "SELECT * FROM b_certificazioni_qualita WHERE codice_operatore = :codice_operatore";
											$risultato_qualita = $pdo->bindAndExec($strsql,$bind);
											$strsql = "SELECT b_certificazioni_soa.*, b_classifiche_soa.id AS id_classifica, b_categorie_soa.id, b_categorie_soa.descrizione FROM b_certificazioni_soa JOIN b_categorie_soa
																	ON b_certificazioni_soa.codice_categoria = b_categorie_soa.codice
																	JOIN b_classifiche_soa ON b_certificazioni_soa.codice_classifica = b_classifiche_soa.codice
																	WHERE b_certificazioni_soa.codice_operatore = :codice_operatore";
											$risultato_soa = $pdo->bindAndExec($strsql,$bind);
											$strsql = "SELECT b_esperienze_progettazione.*, b_categorie_progettazione.id, b_categorie_progettazione.destinazione, b_categorie_progettazione.descrizione AS descrizione_categoria FROM b_esperienze_progettazione
																	JOIN b_categorie_progettazione ON b_esperienze_progettazione.codice_categoria = b_categorie_progettazione.codice
																	WHERE b_esperienze_progettazione.codice_operatore = :codice_operatore";
											$risultato_progettazione = $pdo->bindAndExec($strsql,$bind);
											$strsql = "SELECT * FROM b_certificazioni_ambientali WHERE codice_operatore = :codice_operatore";
											$risultato_ambientali = $pdo->bindAndExec($strsql,$bind);
											$strsql = "SELECT * FROM b_altre_certificazioni WHERE codice_operatore = :codice_operatore";
											$risultato_certificazioni = $pdo->bindAndExec($strsql,$bind);
											$strsql = "SELECT * FROM b_brevetti WHERE codice_operatore = :codice_operatore";
											$risultato_brevetti = $pdo->bindAndExec($strsql,$bind);
											?>
											<h1><? echo $record_operatore["ragione_sociale"] ?></h1>
											<h3><?= $record_bando["oggetto"] ?></h3><br>

		                      <div id="tabs">
		                      	<ul>
		                          <li><a href="#referente">Referente</a></li>
		                          <? if ($record_utente["tipo"] == "OPE") { ?><li><a href="#azienda">Azienda</a></li>
		                          <li><a href="#organizzazione">Organizzazione</a></li><? } ?>
		  							          <li><a href="#categorie">Categorie</a></li>
		  							          <li><a href="#committenti">Committenti</a></li>
		                          <li><a href="#certificazioni">Certificazioni</a></li>
		  							          <li><a href="#brevetti">Brevetti</a></li>
		                          <li><a href="#allegati">Allegati</a></li>
															<li><a href="#comunicazioni">Comunicazioni</a></li>
		                        </ul>
		                       <div id="referente">
		                       <h1>Referente</h1>
		  					<div class="box">
		  	                    <h2>Dati anagrafici</h2>
		                          <table width="100%" id="anagrafici">
		                          <tr><td class="etichetta">Nome</td><td><? echo $record_utente["nome"] ?></td>
		                          <td class="etichetta">Cognome</td><td><? echo $record_utente["cognome"] ?></td></tr>
		                         <tr>
		                         <td class="etichetta">Luogo nascita</td><td><? echo $record_utente["luogo"] ?></td>
		                         <td class="etichetta">Provincia nascita</td><td><? echo $record_utente["provincia_nascita"] ?></td></tr>
		                          <tr><td class="etichetta">Data di nascita</td><td><? echo mysql2date($record_utente["dnascita"]) ?></td>
		                          	<td class="etichetta">Sesso</td><td><? echo $record_utente["sesso"] ?></td></tr>
		                          <tr>
		                          <td class="etichetta">Codice Fiscale</td><td><? echo $record_utente["cf"] ?></td>
		                          <? if ($record_utente["tipo"] != "OPE") { ?>
		                          <td class="etichetta">Partita IVA</td><td><? echo $record_operatore["partita_iva"] ?></td>
		                          <? } ?>
		  </tr>

		  						</table>
		                      </div>
		                      <? if ($record_utente["tipo"] == "OPE") { ?>
		                      <div class="box">
		                      <h2>Ruolo</h2>
		                      <table width="100%">
		                      <tr><td class="etichetta">Ruolo</td><td colspan="5"><? echo $record_operatore["ruolo_referente"] ?>
		                      </td></tr>
		                      <tr><td class="etichetta">Procura</td>
		                      	<td><? echo $record_operatore["tipo_procura"] ?></td><td class="etichetta">Numero</td><td><? echo $record_operatore["numero_procura"] ?></td>
		  	                    <td class="etichetta">Data</td><td><? echo mysql2date($record_operatore["data_procura"]) ?></td></tr>
		                      </table>
		                         </div>
		                      <? } else { ?>
		                      <div class="box">
		                      	<h2>Dati professionali</h2>
		                           <table width="100%">
		                           <tr><td class="etichetta">Titolo di studio</td><td colspan="3"><? echo $record_operatore["titolo_studio"] ?></td>
		                          </tr>
		                      <tr><td class="etichetta">Ordine</td><td><? echo $record_operatore["ordine_professionale"] ?></td>
		                      <td class="etichetta">Iscrizione</td><td>
		                      <?
		  						if ($record_operatore["iscrizione_ordine"] != "") {
		  						?>
		                              <a href="/documenti/operatori/<? echo $record_operatore["codice"] ?>/<? echo $record_operatore["iscrizione_ordine"] ?>" title="File allegato"><img src="/img/<? echo substr($record_operatore["iscrizione_ordine"],-3)?>.png" alt="File <? echo substr($record_operatore["iscrizione_ordine"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato</a><br>
		                              <?
		  						}
		  					?>
		                      </td>
		                      </tr>
		                      <tr><td class="etichetta">Numero</td><td><? echo $record_operatore["numero_iscrizione_professionale"] ?></td>
		                      <td class="etichetta">Data</td><td><? echo mysql2date($record_operatore["data_iscrizione_professionale"]) ?></td></tr>
		                      <tr><td class="etichetta">Curriculum</td><td colspan="3">
		                        <?
		  						if ($record_operatore["curriculum"] != "") {
		  							?>
		                              <a href="/documenti/operatori/<? echo $record_operatore["codice"] ?>/<? echo $record_operatore["curriculum"] ?>" title="File allegato"><img src="/img/<? echo substr($record_operatore["curriculum"],-3)?>.png" alt="File <? echo substr($record_operatore["curriculum"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato</a><br>
		                              <?
		  						}
		  					?>
		                      </td></tr>
		                      </table>
		                      </div>
		  					<? } ?>
		  					<div class="box">
		                      	<h2>Recapiti</h2>
		                          <table width="100%" id="recapiti">
		                          <tr><td class="etichetta">Indirizzo</td><td colspan="3"><? echo $record_utente["indirizzo"] ?></td></tr>
		                          <tr>
		                          <td class="etichetta">Citta</td><td><? echo $record_utente["citta"] ?></td>
		                         <td class="etichetta">Provincia</td><td><? echo $record_utente["provincia"] ?></td></tr>
		                         <tr>
		                         <td class="etichetta">Regione</td><td><? echo $record_utente["regione"] ?></td>
		                         	<td class="etichetta">Stato</td><td><? echo $record_utente["stato"] ?></td></tr>
		                          <tr><td class="etichetta">Telefono</td><td><? echo $record_utente["telefono"] ?></td>
		                          <td class="etichetta">Cellulare</td><td><? echo $record_utente["cellulare"] ?></td></tr>
		                          <tr>
		                          	<td class="etichetta">E-mail</td><td width="300">
		  							<? echo $record_utente["email"] ?>
		                              </td><td class="etichetta">PEC</td><td><? echo $record_utente["pec"] ?></td></tr>
		                          </table>
		                      </div>
		  	               </div>
		                     <? if ($record_utente["tipo"] == "OPE") { ?>
		                     <div id="azienda">
		  	                   <h1>Dati aziendali</h1>
		                         <div class="box">
		                         <table width="100%">
		                         <tr><td class="etichetta">Ragione Sociale</td><td colspan="3"><? echo $record_operatore["ragione_sociale"] ?></td>
		                         </tr>
		                         <tr><td class="etichetta">Partita IVA</td><td><? echo $record_operatore["partita_iva"] ?></td>
		                         <td class="etichetta">Codice Fiscale</td><td><? echo $record_operatore["codice_fiscale_impresa"] ?></td>
		                        </tr>
		                          <td class="etichetta">Numero dipendenti</td><td><? echo $record_operatore["n_dipendenti"] ?></td>
		                         <td class="etichetta">Codice attivit&agrave;</td>
		                         <td><? echo $record_operatore["codice_attivita"] ?>
		                         </td>
		                         </tr>
		                         <tr>
		                         <td class="etichetta">Capitale Sociale</td><td><? echo $record_operatore["capitale_sociale"] ?></td>
		                         <td class="etichetta">Capitale Versato</td><td><? echo $record_operatore["capitale_versato"] ?></td>
		                         </tr>
		                         <tr><td class="etichetta">Dimensione</td><td><? switch ($record_operatore["pmi"]) {
											         case "C": echo "Micro"; break;
											         case "P": echo "Piccola"; break;
											         case "M": echo "Media"; break;
											         case "G": echo "Grande"; break;
											       } ?></td>
											       <td class="etichetta">Curriculum aziendale</td><td>
											       <?
											       if ($record_operatore["curriculum"] != "") {
											        ?>
											        <a href="/documenti/operatori/<? echo $record_operatore["codice"] ?>/<? echo $record_operatore["curriculum"] ?>" title="File allegato"><img src="/img/<? echo substr($record_operatore["curriculum"],-3)?>.png" alt="File <? echo substr($record_operatore["curriculum"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato</a><br>
											        <?
											      }
											      ?>
											    </td></tr>
		                         </table>
		                         </div>
		                         <div class="box">
		                      	<h2>Sede legale</h2>
		                          <table width="100%" id="recapiti">
		                          <tr><td class="etichetta">Indirizzo</td><td colspan="3"><? echo $record_operatore["indirizzo_legale"] ?></td></tr>
		                          <tr><td class="etichetta">Citta</td><td><? echo $record_operatore["citta_legale"] ?></td>
		                         <td class="etichetta">Provincia</td><td><? echo $record_operatore["provincia_legale"] ?></td></tr>
		                         <tr>
		                         <td class="etichetta">Regione</td><td><? echo $record_operatore["regione_legale"] ?></td>
		                         	<td class="etichetta">Stato</td><td><? echo $record_operatore["stato_legale"] ?></td></tr>
		                          </table>
		                      </div>
		                      <div class="box">
		                      	<h2>Sede operativa</h2>
		                          <table width="100%" id="recapiti">
		                          <tr><td class="etichetta">Indirizzo</td><td colspan="3"><? echo $record_operatore["indirizzo_operativa"] ?></td></tr>
		                          <tr><td class="etichetta">Citta</td><td><? echo $record_operatore["citta_operativa"] ?></td>
		                         <td class="etichetta">Provincia</td><td><? echo $record_operatore["provincia_operativa"] ?></td></tr>
		                         <tr>
		                         <td class="etichetta">Regione</td><td><? echo $record_operatore["regione_operativa"] ?></td>
		                         	<td class="etichetta">Stato</td><td><? echo $record_operatore["stato_operativa"] ?></td></tr>
		                          </table>
		                      </div>
		                      <div class="box">
		                      <h2>Camera di commercio</h2>
		                      	<table width="100%">
		                          	<tr><td class="etichetta">Sede</td><td><? echo $record_operatore["sede_cc"] ?></td>
		                              <td class="etichetta">Numero iscrizione</td><td><? echo $record_operatore["numero_iscrizione_cc"] ?></td>
		                              <td class="etichetta">Data iscrizione</td><td><? echo mysql2date($record_operatore["data_iscrizione_cc"]) ?></td></tr>
		                              <tr><td class="etichetta">Certificato camerale</td><td colspan="3">
		   <?
		  						if ($record_operatore["certificato_camerale"] != "") {
		  							?>
		                              <a href="/documenti/operatori/<? echo $record_operatore["codice"] ?>/<? echo $record_operatore["certificato_camerale"] ?>" title="File allegato">
		                              <img src="/img/<? echo substr($record_operatore["certificato_camerale"],-3)?>.png" alt="File <? echo substr($record_operatore["certificato_camerale"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato
		                              </a><br>
		                              <?
		  						}
		  					?>
		  </td>
		  <td class="etichetta">Data emissione</td><td><? echo mysql2date($record_operatore["data_emissione_certificato"]) ?></td></tr>
		                          </table>
		                      </div>
		                      <div class="box">
		                      <h2>INPS</h2>
		                      	<table width="100%">
		                          	<tr><td class="etichetta">Sede</td><td><? echo $record_operatore["sede_inps"] ?></td>
		                              <td class="etichetta">Matricola</td><td><? echo $record_operatore["matricola_inps"] ?></td></tr>

		                          </table>
		                      </div>
		                       <div class="box">
		                      <h2>INAIL</h2>
		                      	<table width="100%">
		                          	<tr><td class="etichetta">Sede</td><td><? echo $record_operatore["sede_inail"] ?></td>
		                              <td class="etichetta">Codice</td><td><? echo $record_operatore["codice_inail"] ?></td>
		                              <td class="etichetta">PAT</td><td><? echo $record_operatore["pat_inail"] ?></td></tr>
		                          </table>
		                      </div>
		                      <div class="box">
		                      <h2>Cassa Edile</h2>
		                      	<table width="100%">
		                          	<tr><td class="etichetta">Sede</td><td><? echo $record_operatore["sede_cassaedile"] ?></td>
		                              <td class="etichetta">Codice</td><td><? echo $record_operatore["codice_cassaedile"] ?></td>
		                              <td class="etichetta">Matricola</td><td><? echo $record_operatore["matricola_cassaedile"] ?></td></tr>
		                          </table>
		                      </div>
		                      <div class="box">
		                      	<h2>Dati Bancari</h2>
		                          <table width="100%">
		                          	<tr><td class="etichetta">Banca</td><td><? echo $record_operatore["banca"] ?></td></tr>
		                              <tr><td class="etichetta">IBAN</td><td><? echo $record_operatore["iban"] ?></td></tr>
		                              <tr><td class="etichetta">Intestatario</td><td><? echo $record_operatore["intestatario"] ?></td></tr>
		                          </table>
		                      </div>
		                     </div>
		                     <div id="organizzazione">
		  	                   <h1>Organizzazione</h1>
		                         <div class="box">
		  	                       <h2>Rappresentanti legali</h2>
		                             <table width="100%" >
		                             	<tbody id="rappresentanti">
		  								<? if (isset($risultato_rappresentanti) && $risultato_rappresentanti->rowCount() > 0) {
		  						   			while ($rappresentanti = $risultato_rappresentanti->fetch(PDO::FETCH_ASSOC)) {
		  										include($root."/albo/rappresentanti/view.php");
		  									}
		  								} ?>
		                              </tbody>
		                             </table>
		                         </div>
		                         <div class="box">
		  	                       <h2>CCNL applicati</h2>
		                             <table width="100%" >
		                             	<tbody id="ccnl">
		  								<? if (isset($risultato_ccnl) && $risultato_ccnl->rowCount() > 0) {
		  						   			while ($ccnl = $risultato_ccnl->fetch(PDO::FETCH_ASSOC)) {
		  										?><tr><td><? echo $ccnl["nome"] ?></td></tr><?
		  									}
		  								}
		  								?>
		                              </tbody>
		                             </table>
		                         </div>
		                     </div>
		                     <? } ?>
		                     <div id="categorie">
		                     <h1>Categorie merceologiche</h1>
		                        <?
		                            $strsql  = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_operatori ON b_cpv.codice = r_cpv_operatori.codice WHERE codice_operatore = :codice_operatore";
		                            $ris = $pdo->bindAndExec($strsql,$bind);
		                            if ($ris->rowCount()>0) {
		                            ?>
		                              <table id="categorie" class="elenco" width="100%" title="Categorie CPV">
		                              <thead>
		                                <tr>
		                                  <th>CPV</th><th>Descrizione</th>
		                                </tr>
		                              </thead>
		                              <tbody>
		                            <?
		                            while ($categoria = $ris->fetch(PDO::FETCH_ASSOC)) {
																	$style="";
																	if (!empty($cpv_bando)) {
																		foreach ($cpv_bando as $available_cpv) {
																			if (strpos($categoria["codice"],$available_cpv)===0) {
																				$style="style='background-color:#a3d696 !important;'";
																			}
																		}
																	}
		                             ?>
		                                  <tr <?= $style ?>>
		                                    <td width="5%"><strong><?= str_pad($categoria["codice"],9,"0") ?></strong></td>
		                                    <td width="95%"><?= $categoria["descrizione"] ?></td>
		                                  </tr>
		                                  <?
		                                }

		                            ?>
		                              </tbody>
		                            </table>
		                            <div class="clear"></div>
		                            <?
		                          }
		                        ?>
		                      </div>
		                     <div id="committenti">
		  	                   <h1>Committenti</h1>
		                         <div class="box">
		                          <table width="100%" >
		                             	<tbody id="tab_committenti">
		                         <? if (isset($risultato_committenti) && $risultato_committenti->rowCount() > 0) {
		  						   			while ($committenti = $risultato_committenti->fetch(PDO::FETCH_ASSOC)) {
		  										include($root."/albo/committenti/view.php");
		  									}
		  					   		}
		  						?>
		                          </tbody>
		                             </table>
		                         </div>
		                         </div>
		                          <div id="certificazioni">

		                         <div class="box">
		                         <h2>Certificazioni di qualit&agrave;</h2>
		                           <table width="100%" >
		                             	<tbody id="tab_qualita">
		                         <? if (isset($risultato_qualita) && $risultato_qualita->rowCount() > 0) {
		  						   			while ($qualita = $risultato_qualita->fetch(PDO::FETCH_ASSOC)) {
		  										include($root."/albo/qualita/view.php");
		  									}
		  					   		}
		  						?>
		                          </tbody>
		                             </table>
		                             <div class="clear"></div>
		                             </div>
		                              <div class="box">
		                         <h2>Certificazioni di gestione ambientale</h2>
		                           <table width="100%" >
		                             	<tbody id="tab_ambientali">
		                         <? if (isset($risultato_ambientali) && $risultato_ambientali->rowCount() > 0) {
		  						   			while ($ambientali = $risultato_ambientali->fetch(PDO::FETCH_ASSOC)) {
		  										include($root."/albo/ambientali/view.php");
		  									}
		  					   		}
		  						?>
		                          </tbody>
		                             </table>
		                           <div class="clear"></div>
		                           </div>
															<div class="box">
		                         <h2>Attestazioni SOA</h2>
		                           <table width="100%" >
		                             	<tbody id="tab_soa">
		                         <? if (isset($risultato_soa) && $risultato_soa->rowCount() > 0) {
		  						   			while ($soa = $risultato_soa->fetch(PDO::FETCH_ASSOC)) {
		  										include($root."/albo/soa/view.php");
		  									}
		  					   		}
		  						?>
		                          </tbody>
		                             </table>
		                             <div class="clear"></div>
		                             </div>
														 <div class="box">
														<h2>Progettazione</h2>
															<table width="100%" >
																 <tbody id="tab_progettazione">
														<? if (isset($risultato_progettazione) && $risultato_progettazione->rowCount() > 0) {
												 while ($progettazione = $risultato_progettazione->fetch(PDO::FETCH_ASSOC)) {
												 include($root."/albo/progettazione/view.php");
											 }
										 }
								 ?>
														 </tbody>
																</table>
																<div class="clear"></div>
																</div>
		      	              <div class="box">
		                         <h2>Altre certificazioni</h2>
		                           <table width="100%" >
		                           	<thead>
		                              <tr>
		                              	<th>Tipo</th>
		                                  <th>Ente certificatore</th>
		                                  <th>Certificazione</th>
		                              </tr>
		                             	<tbody id="tab_certificazioni">
		                         <? if (isset($risultato_certificazioni) && $risultato_certificazioni->rowCount() > 0) {
		  						   			while ($certificazioni = $risultato_certificazioni->fetch(PDO::FETCH_ASSOC)) {
		  										?><tr><td><? echo $certificazioni["tipo"] ?></td><td><? echo $certificazioni["denominazione"] ?></td><td><? echo $certificazioni["certificazione"] ?></td>
		      </tr><?
		  									}
		  					   		}
		  						?>
		                          </tbody>
		                             </table>
		                           <div class="clear"></div>
		                           </div>
		                         </div>
		                         <div id="brevetti">
		                         <h2>Brevetti</h2>
		                         <div class="box">
		                           <table width="100%" >
		                             	<tbody id="tab_brevetti">
		                         <? if (isset($risultato_brevetti) && $risultato_brevetti->rowCount() > 0) {
		  						   			while ($brevetti = $risultato_brevetti->fetch(PDO::FETCH_ASSOC)) {
		  										include($root."/albo/brevetti/view.php");
		  									}
		  					   		}
		  						?>
		                          </tbody>
		                             </table>
		                           <div class="clear"></div>
		                           </div>
		                         </div>
		                         <div id="allegati">
		                         <h1>Allegati</h1>
		                         <table width="100%">
		                      <?
														$bind = array();
														$bind[":codice_bando"] = $record_bando["codice"];
		                        $sql = "SELECT * FROM b_modulistica_dialogo WHERE codice_bando = :codice_bando ORDER BY attivo DESC, codice";
		                        $risultato = $pdo->bindAndExec($sql,$bind);
		                        if ($risultato->rowCount()>0) {
		                          while ($record_modulo = $risultato->fetch(PDO::FETCH_ASSOC)) {
																$bind = array();
																$bind[":codice_operatore"] = $record_operatore["codice"];
																$bind[":codice_modulo"] = $record_modulo["codice"];
		                            $strsql  = "SELECT * FROM b_allegati_dialogo WHERE ";
		                            $strsql .= "codice_modulo = :codice_modulo AND codice_operatore = :codice_operatore";
		                            $ris_allegato = $pdo->bindAndExec($strsql,$bind);
		                            $nome_file = "";
		                            if($ris_allegato->rowCount()>0) {
		                              $allegato = $ris_allegato->fetch(PDO::FETCH_ASSOC);
		                              $nome_file = "<strong>" . $allegato["nome_file"] . "</strong> <input type=\"image\" src=\"/img/info.png\" style=\"vertical-align:middle; cursor:pointer;\" onClick=\"$('#note_" . $allegato["codice"] ."').dialog({title:'Informazioni di Firma',modal:'true'}); return false;\"><br>";
																	$p7m = new P7Manager($config["arch_folder"] . "/allegati_dialogo/" . $allegato["codice_operatore"] . "/" . $allegato["riferimento"]);
		                              $certificati = $p7m->extractSignatures();
		                              foreach ($certificati AS $esito) {
		                                $data = openssl_x509_parse($esito,false);
		                                $validFrom = date('d-m-Y H:i:s', $data['validFrom_time_t']);
		                                $validTo = date('d-m-Y H:i:s', $data['validTo_time_t']);
		                                $note =  "<ul><li>";
		                                if (isset($data["subject"]["commonName"])) $note .= "<h1>" . $data["subject"]["commonName"] . "</h1>";
		                                if (isset($data["subject"]["organizationName"]) && $data["subject"]["organizationName"] != "NON PRESENTE") $note .= "<strong>" . $data["subject"]["organizationName"] . "</strong><br>";
		                                if (isset($data["subject"]["title"])) $note .=  $data["subject"]["title"] . "<br>";
		                                if (isset($data["issuer"]["organizationName"])) $note .=  "<br>Emesso da:<strong> " . $data["issuer"]["organizationName"] . "</strong>";
		                                $note .=  "<br><br>Valido da:<strong> " . $validFrom . "</strong><br>A <strong>" . $validTo . "</strong>";
		                                $note .=  "</li></ul>";
		                              }
																	$file_info = new finfo(FILEINFO_MIME_TYPE);
															    $mime_type = $file_info->buffer(file_get_contents($config["arch_folder"] . "/allegati_dialogo/" . $allegato["codice_operatore"] . "/" . $allegato["riferimento"]));
																	$nome_file.= "<a href=\"../download_allegato.php?codice=" . $allegato["codice"] . "\" title=\"Scarica Allegato\"><img src=\"/img/p7m.png\" alt=\"Scarica Allegato\" width=\"25\"></a>";
																	if (strpos($mime_type,"pdf") === false) {
																		$nome_file.= "<a href=\"../open_p7m.php?codice=" . $allegato["codice"] . "\" title=\"Estrai Contenuto\"><img src=\"/img/download.png\" alt=\"Estrai Contenuto\" width=\"25\"></a>";
																	}
		                              $nome_file.= "<div style=\"display:none;\" id=\"note_" . $allegato["codice"] . "\">" . $note . "</div>";
		                            }
																if ($record_modulo["attivo"] == "S" || $ris_allegato->rowCount() > 0) {
																?>
																	<tr <?= ($record_modulo["attivo"] == "N") ? "style='background-color:#999'" : "" ?>>
																		<td width="70%">
																			<? echo $record_modulo["titolo"]; ?>
																			<?= ($record_modulo["attivo"] == "N") ? "<br><strong>DISATTIVO</strong>" : "" ?>
																		</td>
																		<td width="30%" style="text-align: center"><? echo $nome_file ?></td>
																	</tr>
																<? }
																}
		                          } ?>
		                    </table>
		                         </div>
														<div id="comunicazioni">
															<?
																$strsql = "SELECT b_comunicazioni.*, r_comunicazioni_utenti.protocollo,  r_comunicazioni_utenti.sync, r_comunicazioni_utenti.codice as codice_relazione, r_comunicazioni_utenti.data_protocollo FROM b_comunicazioni JOIN r_comunicazioni_utenti ON b_comunicazioni.codice =
																r_comunicazioni_utenti.codice_comunicazione
																					 WHERE  b_comunicazioni.sezione = 'dialogo' AND b_comunicazioni.codice_gara = :codice_bando AND
																					 codice_utente = :codice_utente ORDER BY b_comunicazioni.timestamp DESC ";
			 													$bind = array();
			 													$bind[":codice_bando"] = $record_bando["codice"];
																$bind[":codice_utente"] = $record_operatore["codice_utente"];
																$risultato = $pdo->bindAndExec($strsql,$bind);
																$user = $pdo->prepare("SELECT CONCAT(cognome,' ',nome) AS user FROM b_utenti WHERE codice = :codice");
																if ($risultato->rowCount()>0){
																?>
																	<table style="font-size:12px" width="100%" class="elenco">
																	<thead><tr><td>Data</td><td>Oggetto</td><td>Ricevute</td></thead>
																	<tbody>
																		<? while ($comunicazione = $risultato->fetch(PDO::FETCH_ASSOC)) { ?>
																		<tr>
																			<td width="120"><? echo mysql2datetime($comunicazione["timestamp"]) ?></td>
																			<td><a href="#" onclick='$("#comunicazione<? echo $comunicazione["codice"] ?>").dialog({title:"Comunicazione del <? echo mysql2datetime($comunicazione["timestamp"]) ?>",modal:"true",width:"700px"})'><? echo substr($comunicazione["oggetto"],0,180) . "..." ?></a><? if (!empty($comunicazione["protocollo"])) { ?>
																				<br><small>Prot. n. <?= $comunicazione["protocollo"] ?> del <?= mysql2date($comunicazione["data_protocollo"]) ?></small>
																			<? } ?>
																				<div id="comunicazione<? echo $comunicazione["codice"] ?>" style="display:none">
																						<? if (!empty($comunicazione["protocollo"])) { ?>
																							<div class="box">
													                    	<strong>Prot. n. <?= $comunicazione["protocollo"] ?> del <?= mysql2date($comunicazione["data_protocollo"]) ?></strong>
																							</div>
													                  <? } ?>
																						<? echo $comunicazione["corpo"] ?>

																				</div>
																				<div style="text-align:right">
																					<small>Utente: <? $user->bindValue(":codice",$comunicazione["utente_modifica"]); $user->execute(); echo $user->fetch(PDO::FETCH_ASSOC)["user"]; ?></small>
																				</div>
																			</td>
																			<td>
																				<?
																				if($comunicazione["sync"] == "S") {
																					$hash = simple_encrypt($comunicazione["codice_relazione"], "ricevute-pec");
																					$hash = base64_encode($hash);
																					?><a href="/comunicazioni/download-ricevute.php?ricevuta=<?= $hash ?>" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;<?
																				}
																				?>
																			</td>
																		</tr>
																	<? } ?>
																	</tbody>
																	</table>
																	<div class="clear"></div>
																	<?
																	}
															?>
														</div>
		                 </div>
											<? if ($record_partecipante["ammesso"] != 'S') { ?>
														<form action="/dialogo_competitivo/abilitazione/abilita.php" method="post">
		                              <input type="hidden" name="codice_operatore" value="<?= $codice ?>">
		                              <input type="hidden" name="codice_bando" value="<?= $record_bando["codice"] ?>">
		                              <input type="submit" class="submit_big" style="background-color:#0C0" value="Abilita">
		                        </form>
											<? } ?>
											<? if (($record_partecipante["ammesso"] != 'N' && $record_partecipante["valutato"] == "S") || $record_partecipante["valutato"] == "N") { ?>
															<input type="button" class="submit_big" style="background-color:#c00" onclick="$('#form_dialog').dialog({title:'Respingi la richiesta',modal:'true',width:'700px'});$('#corpo').ckeditor(config_simple);" value="Respingi">

															<div id="form_dialog" style="display:none">
																	<form action="/dialogo_competitivo/abilitazione/respingi.php" rel="validate" method="post">
																				<input type="hidden" name="codice_operatore" value="<?= $codice ?>">
																				<input type="hidden" name="codice_bando" value="<?= $record_bando["codice"] ?>">
																				<textarea id="corpo" rel="S;10;0;A" title="Corpo comunicazione" name="corpo">
																				</textarea>
																				<input type="submit" class="submit_big" value="Invia">
																	</form>
															</div>
											<? } ?>
															<script>
																function ritorna() {
																	window.location.href = "/dialogo_competitivo/partecipanti/index.php?codice=<? echo $record_bando["codice"] ?>";
																}

															</script>
															<input type="button" class="submit_big" style="background-color:#999;" value="Ritorna all'elenco" onClick="ritorna()">

		  			<div class="clear"></div>
		  			<script type="text/javascript">
		  				$("#tabs").tabs();
		  			</script>
		  					<?
						}  else {
							?>
							<h1>Impossibile accedere: Utente non trovato</h1>
							<?
						}
					} else {
						?>
						<h1>Impossibile accedere: Richiesta non trovata</h1>
						<?
					}
				} else {
					?>
					<h1>Impossibile accedere: Operatore non trovato</h1>
					<?
				}
			} else {
				?>
				<h1>Impossibile accedere: Termine apertura non raggiunto</h1>
				<?
			}
      } else {
				?>
				<h1>Errore nella richiesta</h1>
				<?
			}
    } else {
			?>
			<h1>Errore nella richiesta</h1>
			<?
		}

	include_once($root."/layout/bottom.php");
	?>
