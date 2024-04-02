<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("gare",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
		if (isset($_GET["codice"])) {

				$codice = $_GET["codice"];
				$bind = array();
				$bind[":codice"] = $codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql  = "SELECT b_gare.*, b_gare.tipologia AS codice_tipologia, b_tipologie.tipologia AS tipologia, b_gare.criterio AS codice_criterio, b_criteri.criterio AS criterio, b_gare.procedura AS codice_procedura, b_procedure.nome AS procedura, b_procedure.fasi, b_stati_gare.titolo AS fase, b_stati_gare.colore ";
				$strsql .= "FROM b_gare JOIN b_stati_gare ON b_gare.stato = b_stati_gare.fase ";
				$strsql .= "JOIN b_procedure ON b_gare.procedura = b_procedure.codice ";
				$strsql .= "JOIN b_criteri ON b_gare.criterio = b_criteri.codice ";
				$strsql .= "JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice ";
				if ($_SESSION["gerarchia"] == 2) {
					$strsql .= "JOIN b_permessi ON b_gare.codice = b_permessi.codice_gara ";
				}
				$strsql .= "WHERE b_gare.codice = :codice ";
				$strsql .= "AND b_gare.codice_gestore = :codice_ente ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (b_gare.codice_ente = :codice_ente_utente OR b_gare.codice_gestore = :codice_ente_utente) ";
				}
				if ($_SESSION["gerarchia"] > 1) {
					$bind[":codice_utente"] = $_SESSION["codice_utente"];
					$strsql .= " AND (b_permessi.codice_utente = :codice_utente)";
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					if (($record["stato"]==3) && (strtotime($record["data_scadenza"])<time())) {
						$record["colore"] = $config["colore_scaduta"];
						 $record["fase"] = "Scaduta";
					} ?>
					<?
						$gare_demo = array("1155","1545","1337","3656","18556","3685","4575","4712","10690");
						if (in_array($record["codice"], $gare_demo) !== false) {
							?>
							<form action="reset_demo.php?codice=<?= $record["codice"] ?>">
								<button class="submit_big"><span class="fa fa-refresh"> Resetta stato</span></button>
							</form>
							<?
						}
					?>
          <h1>PANNELLO DI GESTIONE - GARA #<? echo $record["id"] ?> - ID SUAFF #<?= $record["id_suaff"] ?> <input type="image" onclick="$('#info').toggle()" src="/img/info.png" title="Ulteriori informazioni"></h1>
          <div id="info" class="ui-state-error padding" style="display:none">
          	<h2>Ulteriori informazioni</h2>
          	<table width="100%">
          		<tr><td class="etichetta">Codice Gara Univoco</td><td><strong><? echo $record["codice"]; ?></strong></td></tr>
          		<tr><td class="etichetta">Codice Gara Relativo</td><td><strong><? echo $record["id"]; ?></strong></td></tr>
          		<tr><td class="etichetta">Codice Ente</td><td><strong><? echo $record["codice_ente"]; ?></strong></td></tr>
							<? if ($record["stato"] >= 3 && $pdo->go("SELECT codice FROM r_dgue_gare WHERE codice_gara = :codice_gara AND sezione = 'gare'",[":codice_gara"=>$record["codice"]])->rowCount()) { ?>
								<tr><td class="etichetta">URL DGUE</td><td>https://<?= $_SESSION["ente"]["dominio"] ?>/dgue/edit.php?sezione=gare&codice_riferimento=<?= $record["codice"] ?></td></tr>
							<? } ?>
          	</table>
          	<?
						$bind = array();
						$bind[":codice"] = $record["codice"];
						$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice ORDER BY codice";
						$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
						if ($ris_lotti->rowCount()>0) {
							?>
							<h2>Informazioni Lotti</h2>
							<table width="100%">
								<tr><td style="width:1%" class="etichetta">#</td><td style="width:5%" class="etichetta">Codice Lotto Univoco</td><td class="etichetta">Oggetto</td></tr>
								<?
									$i=0;
									while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
										$i++;
										?>
										<tr>
											<td><strong><?= $i ?></strong></td>
											<td style="text-align: center"><strong><? echo $lotto["codice"] ?></strong></td>
											<td><? echo $lotto["oggetto"] ?></td>
										</tr>
										<?
									}
								?>
							</table>
							<?
						}
						if ($_SESSION["gerarchia"] == 0) {
							?>
							<div class="box">
								<? if (!empty($record["public_key"])) { ?>
								<div style="float:left; width:49%">
									<form id="telematicaForm" action="/gare/sad-management.php" method="POST" rel="validate">
										<input type="hidden" name="operazione" value="telematica">
										<input type="hidden" name="codice_gara" value="<?= $record["codice"] ?>">
										<button type="button" id="telematicaButton" class="submit_big" style="background-color:blue"><i class="fa fa-paper-plane" aria-hidden="true"></i> IMPOSTA GARA TELEMATICA</button>
									</form>
									<script>
										$('#telematicaButton').on('click', function(event) {
											event.preventDefault();
											jconfirm('<p>Sei sicuro di voler impostare questa gara come telematica?</p>', function(event) {
												$('#telematicaForm').submit();
											});
										})
									</script>
								</div>
								<div style="float:right; width:49%">
									<form id="extrapiattaformaForm" action="/gare/sad-management.php" method="POST" rel="validate">
											<input type="hidden" name="operazione" value="extrapiattaforma">
											<input type="hidden" name="codice_gara" value="<?= $record["codice"] ?>">
											<button type="button" id="extrapiattaformaButton" class="submit_big" style="background-color:green"><i class="fa fa-file-o" aria-hidden="true"></i> IMPOSTA GARA EXTRA-PIATTAFORMA</button>
										</form>
										<script>
											$('#extrapiattaformaButton').on('click', function(event) {
												event.preventDefault();
												jconfirm('<p>Sei sicuro di voler impostare questa gara come extrapiattaforma?</p>', function(event) {
													$('#extrapiattaformaForm').submit();
												});
											})
										</script>
								</div>
								<div class="clear"></div>
								<? } ?>
								<?
								$operatori = $pdo->bindAndExec('SELECT COUNT(codice) FROM r_partecipanti WHERE codice_gara = :codice_gara', array(':codice_gara' => $record["codice"]))->fetch(PDO::FETCH_COLUMN, 0);
								if($operatori == 0) {
								?>
									<form id="backward" action="/gare/sad-management.php" method="POST" rel="validate">
										<input type="hidden" name="operazione" value="backward-to-elaborazione">
										<input type="hidden" name="codice_gara" value="<?= $record["codice"] ?>">
										<button type="button" id="backward_button" class="submit_big" style="background-color:darkorange"><i class="fa fa-step-backward" aria-hidden="true"></i> RIPORTA ALLO STATO DI ELABORAZIONE</button>
									</form>
									<script>
										$('#backward_button').on('click', function(event) {
											event.preventDefault();
											jconfirm('<p>Sei sicuro di voler riportare questa gara allo stato di elaborazione?</p>', function(event) {
												$('#backward').submit();
											});
										})
									</script>
								<?
								}
								if($operatori == 0 && !empty($record["public_key"])) {
									?>
									<form id="remove_key" action="/gare/sad-management.php" method="POST" rel="validate">
										<input type="hidden" name="operazione" value="reset_key">
										<input type="hidden" name="codice_gara" value="<?= $record["codice"] ?>">
										<button type="button" id="remove_key_button" class="submit_big" style="background-color:crimson"><i class="fa fa-key" aria-hidden="true"></i> RIMOZIONE CHIAVE PUBBLICA</button>
										<span style="color: #3C3C3C !important; font-size: 90%">N.B. Questa procedura rimuove la chiave pubblica della gara e l'operazione della S.A. appaltante potrà risalvare la scheda Elaborzione per ottenere una nuova coppia di chiavi per la cifratura delle buste. <strong>L'operazione di rimozione è irreversibile.</strong></span>
									</form>
									<script>
										$('#remove_key_button').on('click', function(event) {
											event.preventDefault();
											jconfirm('<p>Sei sicuro di voler rimuovere la chiave pubblica di questa gara?<br><strong>L&#39operazione di rimozione &egrave; irreversibile!!</strong></p>', function(event) {
												$('#remove_key').submit();
											});
										})
									</script>
									<?
								}
								?>
							</div>
							<?
						}
						?>
					</div>
					<h2><? echo $record["oggetto"] ?></h2>
					<h3 style="text-align:right">
						Tipologia: <strong><? echo $record["tipologia"] ?></strong> |
						Criterio: <strong><? echo $record["criterio"] ?></strong> |
						Procedura: <strong><? echo $record["procedura"] ?></strong> |
						Stato: <strong><? echo $record["fase"] ?></strong>
						<? if ($record["contributo_sua"] > 0) echo " | Contributo: &euro; <strong>" . number_format($record["contributo_sua"],2,",",".") . "</strong>"; ?>
					</h3>
					<div style="background-color:#<? echo $record["colore"] ?>; padding:5px;"></div><br>
            <?
						$scaduta = "N";
						$apertura = "N";
						if (strtotime($record["data_scadenza"])<=time()) $scaduta = "S";
						if (strtotime($record["data_apertura"])<=time()) $apertura = "S";
						$tipi = [];
						$tipi[] = "elaborazione";
						$tipi[] = "documentale";
						$tipi[] = "comunicazione";
						foreach($tipi AS $tipo) {
							$bind = array();
							$bind[":tipo"] = $tipo;
							$bind[":stato_a"] = $record["stato"].",%";
							$bind[":stato_b"] = "%,".$record["stato"].",%";
							$bind[":stato_c"] = "%,".$record["stato"];
							$bind[":stato_d"] = $record["stato"];

							$bind[":procedura_a"] = $record["codice_procedura"].",%";
							$bind[":procedura_b"] = "%,".$record["codice_procedura"].",%";
							$bind[":procedura_c"] = "%,".$record["codice_procedura"];
							$bind[":procedura_d"] = $record["codice_procedura"];

							$sql = "SELECT b_gestione_gare.* FROM b_gestione_gare ";
							$sql .= " WHERE b_gestione_gare.tipo = :tipo AND b_gestione_gare.fase_minima <= :stato_d ";
							$sql .= " AND ((b_gestione_gare.stati_esclusi NOT LIKE :stato_b ";
							$sql .= " AND b_gestione_gare.stati_esclusi NOT LIKE :stato_c ";
							$sql .= " AND b_gestione_gare.stati_esclusi NOT LIKE :stato_a) OR b_gestione_gare.stati_esclusi IS NULL) ";
							$sql .= " AND b_gestione_gare.procedure_escluse NOT LIKE :procedura_b ";
							$sql .= " AND b_gestione_gare.procedure_escluse NOT LIKE :procedura_c ";
							$sql .= " AND b_gestione_gare.procedure_escluse NOT LIKE :procedura_a ";
							$sql .= " AND b_gestione_gare.procedure_escluse NOT LIKE :procedura_d ";
							$sql .= " AND (b_gestione_gare.modalita = '0' OR b_gestione_gare.modalita REGEXP '[[:<:]]". $record["modalita"] . "[[:>:]]')";
							if ($scaduta == "N") $sql .= " AND b_gestione_gare.scaduta = 'N'";
							if ($apertura == "N") $sql .= " AND b_gestione_gare.apertura = 'N'";
							$sql .= " ORDER BY b_gestione_gare.ordinamento";
							$ris_comandi = $pdo->bindAndExec($sql,$bind);

							if ($ris_comandi->rowCount() > 0) {

								$percentuale = number_format((100/count($tipi)) - 1,2);
								?>
								<div style="float:left; width:<?= $percentuale ?>%; margin:1px;">
									<h3><? echo ucfirst($tipo) ?></h3>
									<?
									$st_index = json_decode(file_get_contents($root."/inc/status_standard_color.json"),TRUE);
									while($rec = $ris_comandi->fetch(PDO::FETCH_ASSOC)) {
										if ($rec["cross_p"] == "S" || $_SESSION["gerarchia"]==="0" || ($_SESSION["record_utente"]["codice_ente"] == $_SESSION["ente"]["codice"]) || $_SESSION["ente"]["permit_cross"]=="S") {
											$continue = check_permessi($rec["modulo_riferimento"],$_SESSION["codice_utente"]);
											if ($continue) {
												$show = true;
												$folder = explode("/",$rec["link"]);
												$temp = array_pop($folder);
												$folder = implode("/",$folder);
												$st_color = "";
												if (file_exists($root.$folder."/check.php")) include($root.$folder."/check.php");
												if (file_exists($root.$folder."/_status.php")) include($root.$folder."/_status.php");
												if ($show) {
													$lock = check_lock($rec["codice"],$record["codice"]);
													?>
													<a <? if (!empty($st_color)) { ?>style="border-left:10px solid <?= $st_color ?>" <? } ?> class="pannello <? if ($lock==true) echo "locked"?>" href="<? echo $rec["link"] ?><?= (strpos($rec["link"], "?") === false) ? "?" : "&" ?>codice=<? echo $record["codice"]?>" title="<? echo $rec["titolo"] ?>">
													<? echo $rec["titolo"] ?>
													<? if ($rec["badge"] != "") {
														if (file_exists($root.$rec["badge"])) include($root.$rec["badge"]);
													} ?>
													</a>
													<?
												}
											}
										}
									}
									?>
									<div class="clear"></div>
								</div>
								<?
							}
						}
					?><div class="clear"></div><?
			} else {
				echo "<h1>Non autorizzato - Contattare l'amministratore</h1>";
			}
		} else {
			echo "<h1>Gara non trovata</h1>";
		}
	include_once($root."/layout/bottom.php");
	?>
