<?
	include_once("../../config.php");
	$disable_alert_sessione = true;
	include_once($root."/layout/top.php");
	include_once($root."/inc/p7m.class.php");
	$public = true;
		if ((isset($_GET["cod"]) || isset($_POST["cod"]))&& is_operatore()) {
				if(! empty($_SESSION["record_utente"]["profilo_completo"]) && $_SESSION["record_utente"]["profilo_completo"] !== 'S') {
					echo '<meta http-equiv="refresh" content="0;URL=/operatori_economici/id'.$_SESSION["record_utente"]["codice"].'-edit">'; die();
				}
				if (isset($_POST["cod"])) $_GET["cod"] = $_POST["cod"];
				$codice = $_GET["cod"];
				$bind = array();
				$bind[":codice"] = $codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql  = "SELECT * FROM b_bandi_sda WHERE codice = :codice ";
				$strsql .= "AND annullata = 'N' AND data_scadenza > now() ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$record_bando = $risultato->fetch(PDO::FETCH_ASSOC);

					?>
					<h1>MODULO DI ABILITAZIONE - ID <? echo $record_bando["id"] ?></h1>
					<h2><? echo $record_bando["oggetto"] ?></h2>
					<?
						if (is_operatore()) {
								$bind = array();
								$bind[":codice_utente"] = $_SESSION["codice_utente"];
								$strsql = "SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente";
								$ris = $pdo->bindAndExec($strsql,$bind);
								$operatore = $ris->fetch(PDO::FETCH_ASSOC);
								$bind = array();
								$bind[":codice_bando"] = $record_bando["codice"];
								$bind[":codice_utente"] = $_SESSION["codice_utente"];
								$sql = "SELECT * FROM r_partecipanti_sda WHERE codice_bando = :codice_bando AND codice_utente = :codice_utente ";
								$ris = $pdo->bindAndExec($sql,$bind);
								$partecipato = false;
								if ($ris->rowCount() > 0) {
									$partecipante = $ris->fetch(PDO::FETCH_ASSOC);
									$partecipato = true;
								}
								?>
								<script type="text/javascript" src="/js/resumable.js"></script>
								<script type="text/javascript" src="resumable-uploader.js"></script>
								<script type="text/javascript" src="/js/spark-md5.min.js"></script>
								<form id="modulo" rel="validate" method="post" target="_self" action="save_modulo.php">
									<input type="hidden" id="codice_bando" name="codice_bando" value="<? echo $record_bando["codice"] ?>">
									<? if (!$partecipato) { ?>
										<input type="hidden" id="invia" value="N" name="invia">
									<? } ?>
									<div id="tabs">
										<ul>
											<li><a href="#cpv">Categorie CPV</a></li>
											<li><a href="#allegati">Allegati</a></li>
										</ul>
										<div id="cpv">
											<script>
												function gestisci_cpv(cpv) {
													array_cpv = $("#input_cpv").val().split(",");
													if ($.inArray(cpv,array_cpv)==-1) {
														array_cpv.push(cpv);
													} else {
														var index = array_cpv.indexOf(cpv);
														array_cpv.splice(index, 1);
													}
													$("#input_cpv").val(array_cpv.join(","));
												}
											</script>
											<?
												$bind = array();
												$bind[":codice_bando"] = $record_bando["codice"];
												$sql = "SELECT * FROM r_cpv_bandi_sda WHERE codice_bando = :codice_bando";
												$ris_categorie = $pdo->bindAndExec($sql,$bind);
												if ($ris_categorie->rowCount()>0) {
													$generale = true;
													$bind[":codice_operatore"] = $operatore["codice"];
													$strsql  = "SELECT * FROM r_cpv_operatori_sda WHERE codice_bando = :codice_bando";
													$strsql .= " AND codice_operatore = :codice_operatore ";
													$ris = $pdo->bindAndExec($strsql,$bind);
													$array_cpv = array();
													if ($ris->rowCount()>0) $generale = false;
													?>
														<table id="categorie" class="elenco" width="100%" title="Categorie CPV" class="valida">
														<thead>
															<tr>
																<th></th><th>CPV</th><th>Descrizione</th>
															</tr>
														</thead>
														<tbody>
													<?
													while ($categoria = $ris_categorie->fetch(PDO::FETCH_ASSOC)) {
														$sql_cpv = "SELECT * FROM b_cpv WHERE codice LIKE '" . $categoria["codice"] . "%'";
														$ris_cpv = $pdo->query($sql_cpv);
														if ($ris_cpv->rowCount()>0) {
															while ($cpv = $ris_cpv->fetch(PDO::FETCH_ASSOC)) {
																	$checked = "";
																	if (!$generale) {
																		$bind = array();
																		$bind[":codice_bando"] = $record_bando["codice"];
																		$bind[":codice_operatore"] = $operatore["codice"];
																		$bind[":codice"] = $cpv["codice"];
																		$strsql  = "SELECT * FROM r_cpv_operatori_sda WHERE codice_bando = :codice_bando ";
																		$strsql .= " AND codice_operatore = :codice_operatore AND codice = :codice ";
																		$ris = $pdo->bindAndExec($strsql,$bind);
																		if ($ris->rowCount()>0) {
																			$checked = "checked='checked'";
																			$array_cpv[] = $cpv["codice"];
																		}
																	} else {
																		$bind = array();
																		$bind[":codice_utente"] = $_SESSION["codice_utente"] ;

																		$strsql  = "SELECT * FROM r_cpv_operatori WHERE codice_utente = :codice_utente AND (";
																		$cpv_attuale = $cpv["codice"];
																		while(strlen($cpv_attuale)>=2) {
																			$strsql .= "codice	LIKE '" . $cpv_attuale . "%' OR ";
																			$cpv_attuale = substr($cpv_attuale,0,-1);
																		}
																		$strsql = substr($strsql,0,-4).")";
																		$ris = $pdo->bindAndExec($strsql,$bind);
																		if ($ris->rowCount()>0) {
																			$checked = "checked='checked'";
																			$array_cpv[] = $cpv["codice"];
																		}
																	}
																?>
																<tr>
																	<td width="1%"><input <? echo $checked ?> type="checkbox" id="cpv_" onclick="gestisci_cpv('<? echo $cpv["codice"] ?>')">
																	<td width="5%"><strong><?= str_pad($cpv["codice"],9,"0") ?></strong></td>
																	<td width="94%"><?= $cpv["descrizione"] ?></td>
																</tr>
																<?
															}
														}
													}
													?>
														</tbody>
													</table>
													<div class="clear"></div>
													<?
												}
											?>
											<input type="hidden" id="input_cpv" name="cpv" value="<?= implode(",",$array_cpv); ?>">
										</div>
										<div id="allegati">
											<table width="100%">
												<thead>
													<tr><td>Modulo</td><td>Obbligatorio</td><td>Allegato</td></tr></thead>
												<?

													$bind = array();
													$bind[":codice_bando"] = $record_bando["codice"];
													$sql = "SELECT * FROM b_modulistica_sda WHERE codice_bando = :codice_bando AND attivo = 'S' ORDER BY codice";
													$risultato = $pdo->bindAndExec($sql,$bind);
													if ($risultato->rowCount()>0) {
														?>
															<script>
																var uploader = new Array();
															</script>
														<?
														while ($record_modulo = $risultato->fetch(PDO::FETCH_ASSOC)) {

															$bind = array();
															$bind[":codice_modulo"] = $record_modulo["codice"];
															$bind[":codice_operatore"] = $operatore["codice"];
															$strsql  = "SELECT * FROM b_allegati_sda WHERE ";
															$strsql .= "codice_modulo = :codice_modulo AND codice_operatore = :codice_operatore";
															$ris_allegato = $pdo->bindAndExec($strsql,$bind);
															$nome_file = "";
															$note = "";
															if($ris_allegato->rowCount()>0) {
																$allegato = $ris_allegato->fetch(PDO::FETCH_ASSOC);
																$nome_file = "<strong>" . $allegato["nome_file"] . "</strong> <input type=\"image\" src=\"/img/info.png\" style=\"vertical-align:middle; cursor:pointer;\" onClick=\"$('#note_" . $allegato["codice"] ."').dialog({title:'Informazioni di Firma',modal:'true'}); return false;\"><br>";
																$p7m = new P7Manager($config["arch_folder"] . "/allegati_sda/" . $allegato["codice_operatore"] . "/" . $allegato["riferimento"]);
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
														    $mime_type = $file_info->buffer(file_get_contents($config["arch_folder"] . "/allegati_sda/" . $allegato["codice_operatore"] . "/" . $allegato["riferimento"]));
																$nome_file.= "<a href=\"download_allegato.php?codice=" . $allegato["codice"] . "\" title=\"Scarica Allegato\"><img src=\"/img/p7m.png\" alt=\"Scarica Allegato\" width=\"25\"></a>";
																if (strpos($mime_type,"pdf") === false) {
																	$nome_file.= "<a href=\"open_p7m.php?codice=" . $allegato["codice"] . "\" title=\"Estrai Contenuto\"><img src=\"/img/download.png\" alt=\"Estrai Contenuto\" width=\"25\"></a>";
																}
																$nome_file.= "<div style=\"display:none;\" id=\"note_" . $allegato["codice"] . "\">" . $note . "</div>";
															}
															?>
															<tr>
																<td width="70%"><? echo $record_modulo["titolo"]; ?></td>
																<td width="5%"><? echo $record_modulo["obbligatorio"]; ?></td>
																<td width="25%">
																	<input type="hidden" class="md5" name="md5_file_<? echo $record_modulo["codice"] ?>" id="md5_file_<? echo $record_modulo["codice"] ?>" title="File">
	                  												<input type="hidden" class="filechunk <? if ($record_modulo["obbligatorio"] == "S") echo "obbligatorio" ?>" id="filechunk_<? echo $record_modulo["codice"] ?>" name="filechunk_<? echo $record_modulo["codice"] ?>" title="Allegato">
	                  												<input type="hidden" class="terminato" id="terminato_<? echo $record_modulo["codice"] ?>" title="Termine upload">
																	<div id="nome_file_<? echo $record_modulo["codice"] ?>" style="float:left;"><? echo $nome_file ?></div>
																	<div id="modulistica_<? echo $record_modulo["codice"] ?>" rel="<? echo $record_modulo["codice"] ?>" class="scegli_file" style="float:right"><img src="/img/folder.png" height="30" style="vertical-align:middle"></div>
																	<div class="clear"></div>
										                    		<div id="progress_bar_<? echo $record_modulo["codice"] ?>" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
																</td>
															</tr>
																 <script>
																	tmp = (function($){
																		return (new ResumableUploader($("#modulistica_<? echo $record_modulo["codice"] ?>")));
																	})(jQuery);
																	uploader.push(tmp);
																</script>
														<? }
													} ?>
											</table>
										</div>
									</div>
									<script>
										$("#tabs").tabs();
										function save() {
											$("#categorie").attr("rel","N;0;0;checked;group_validate");
											$(".filechunk").attr("rel","N;0;0;A");
											if ($("#invia").length>0) $("#invia").val('N');
											return true;
										}
										function save_invia() {
											$("#categorie").attr("rel","S;0;0;checked;group_validate");
											$("#invia").val('S');
											$(".obbligatorio").each(function() {
												id = $(this).attr("id").split("_");
												id = id[1];
												if ($("#nome_file_"+id).html()=="") {
													$(this).attr("rel","S;0;0;A");
												} else {
													$(this).attr("rel","N;0;0;A");
												}
											});
										return true;
									}
								</script>
										<input class="submit_big" type="submit" value="Salva" onclick="save()">
									<?
										if (!$partecipato) {
											?>
												<input class="submit_big" type="submit" value="Salva ed invia" style="background-color: #0C0" onclick="save_invia();">
											<?
										} else {
											?>
											<a id="button_revoca_<? echo $partecipante["codice"] ?>" href="#" onClick="elimina('<? echo $partecipante["codice"] ?>','sda/abilitazione');" class="submit_big" style="background-color:#C30" title="Revoca partecipazione">Revoca partecipazione</a>
											<?
										}
									?>
								</form>

							<?
							} else if (!isset($_SESSION["codice_utente"])) {
								?><div class="box">
						        <h3><a href="/operatori_economici/registrazione.php" title="Registrazione operatori economici">Registrati</a> o <a href="/accesso.php" title="Accedi all'area riservata">Accedi</a> per partecipare</h3></div>
						        <?
							}
				} else {
					echo "<h1>Bando inesistente o privilegi insufficienti</h1>";
				}
			} else {
				echo "<h1>Bando inesistente</h1>";
			}
	include_once($root."/layout/bottom.php");
	?>
