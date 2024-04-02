<?
	include_once("../../../config.php");
	$disable_alert_sessione = true;
	include_once($root."/layout/top.php");
	$public = true;
	if (isset($_GET["codice_gara"]) && isset($_GET["codice_lotto"]) && isset($_GET["codice_busta"]) && is_operatore()) {

		$codice_gara = $_GET["codice_gara"];
		$codice_lotto = $_GET["codice_lotto"];
		$codice_busta = $_GET["codice_busta"];

		$bind = array();
		$bind[":codice"] = $codice_gara;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql  = "SELECT b_gare.*, b_procedure.invito, b_procedure.fasi, b_procedure.mercato_elettronico FROM b_gare JOIN b_modalita ON b_gare.modalita = b_modalita.codice JOIN b_procedure ON b_gare.procedura = b_procedure.codice
								WHERE b_gare.codice = :codice ";
		$strsql .= "AND b_gare.annullata = 'N' AND b_modalita.online = 'S' ";
		$strsql .= "AND codice_gestore = :codice_ente ";
		$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
		$risultato = $pdo->bindAndExec($strsql,$bind);

		$accedi = false;

		if ($risultato->rowCount() > 0) {
			$bind = array();
			$bind[":codice"] = $codice_gara;
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

			$derivazione = "";
			$sql = "SELECT * FROM b_procedure WHERE codice=" . $record_gara["procedura"];
			$ris = $pdo->query($sql);
			if ($ris->rowCount()>0) {
				$rec_procedura = $ris->fetch(PDO::FETCH_ASSOC);
				$directory = $rec_procedura["directory"];
				$record["nome_procedura"] = $rec_procedura["nome"];
				$record["riferimento_procedura"] = $rec_procedura["riferimento_normativo"];
				if ($rec_procedura["mercato_elettronico"] == "S") $derivazione = "me";
				if ($rec_procedura["directory"] == "sda")  $derivazione = "sda";
				if ($rec_procedura["directory"] == "dialogo")  $dialogo = true;

			}

			$strsql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice";
			$ris_inviti = $pdo->bindAndExec($strsql,$bind);
			if ($ris_inviti->rowCount()>0) {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$strsql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice AND r_inviti_gare.codice_utente = :codice_utente";
				$ris_invitato = $pdo->bindAndExec($strsql,$bind);
				if ($ris_invitato->rowCount()>0) $accedi = true;
			} else {
				if($record_gara["invito"] == "N" || !empty($derivazione)) {
					$accedi = true;
				}
			}
			if ($derivazione != "") {
				$sql_abilitato = "SELECT * FROM r_partecipanti_".$derivazione." WHERE codice_bando = :codice_derivazione AND ammesso = 'S' AND codice_utente = :codice_utente ";
				$ris_abilitato = $pdo->bindAndExec($sql_abilitato,array(":codice_derivazione"=>$record_gara["codice_derivazione"],":codice_utente"=>$_SESSION["codice_utente"]));
				if ($ris_abilitato->rowCount() == 0) {
					$accedi = false;
				}
			}
		}
		if ($accedi) {
			$print_form = true;
			$record_gara["tipologie_gara"] = "";
			$bind = array();
			$bind[":tipologia"] = $record_gara["tipologia"];
			$sql = "SELECT tipologia FROM b_tipologie WHERE b_tipologie.codice = :tipologia";
			$ris_tipologie = $pdo->bindAndExec($sql,$bind);
			if ($ris_tipologie->rowCount()>0) {
				$rec_tipologia = $ris_tipologie->fetch(PDO::FETCH_ASSOC);
				$record_gara["tipologie_gara"] .= $rec_tipologia["tipologia"] . " ";
			}
			?>
			<h1><?= traduci("CARICA DOCUMENTAZIONE") ?> - ID <? echo $record_gara["id"] ?></h1>
			<h2><strong><? echo traduci(trim($record_gara["tipologie_gara"])) ?></strong> - <? echo $record_gara["oggetto"] ?></h2>
			<?
			$bind = array();
			$bind[":codice_gara"] = $record_gara["codice"];
			$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara ORDER BY codice";
			$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
			if ($ris_lotti->rowCount() > 0) {
				$print_form =false;
				$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto ORDER BY codice";
				$bind[":codice_lotto"] = $codice_lotto;
				$ris_check_lotti = $pdo->bindAndExec($sql_lotti,$bind);
				if ($ris_check_lotti->rowCount() > 0) {
						$lotto = $ris_check_lotti->fetch(PDO::FETCH_ASSOC);
						if ($record_gara["modalita_lotti"]==1) {
							$bind =array();
							$bind[":codice_gara"] = $record_gara["codice"];
							$bind[":codice_utente"] = $_SESSION["codice_utente"];
							$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND conferma = TRUE AND codice_utente = :codice_utente";
							$ris_partecipazioni = $pdo->bindAndExec($sql,$bind);
							if ($ris_partecipazioni->rowCount() > 0) {
								$bind = array();
								$bind[":lotto"] = $codice_lotto;
								$bind[":codice_gara"] = $record_gara["codice"];
								$bind[":codice_utente"] = $_SESSION["codice_utente"];
								$sql = "SELECT * FROM r_partecipanti WHERE codice_lotto = :lotto AND conferma = TRUE AND codice_gara = :codice_gara AND codice_utente = :codice_utente";
								$ris_partecipante_lotto = $pdo->bindAndExec($sql,$bind);
								if ($ris_partecipante_lotto->rowCount() > 0) {
									$print_form = true;
								} else {
									?>
									<h2 style="color:#C00"><?= traduci("Impossibile partecipare a più lotti") ?></h2>
									<?
								}
							} else {
								$print_form = true;
							}
					} else {
						$print_form = true;
					}
				}
			} else {
				$codice_lotto = 0;
			}

			if ($print_form) {

				$submit = false;

				if (isset($lotto)) {
					$codice_lotto = $lotto["codice"];
					echo "<div class=\"box\"><h3>" . $lotto["oggetto"] . "</h3>";
					echo $lotto["descrizione"]."</div>";
				}

				if (strtotime($record_gara["data_scadenza"]) > time()) {
						$submit = true;
				} else if ($record_gara["fasi"] == 'S') {
					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$bind[":codice_utente"] = $_SESSION["codice_utente"];
					$bind[":lotto"] = $codice_lotto;
					$sql_fase = "SELECT r_partecipanti.* FROM r_partecipanti JOIN b_2fase ON r_partecipanti.codice_gara = b_2fase.codice_gara AND r_partecipanti.codice_lotto = b_2fase.codice_lotto
											WHERE r_partecipanti.codice_utente = :codice_utente AND r_partecipanti.codice_lotto = :lotto AND r_partecipanti.codice_gara = :codice_gara
											AND ammesso = 'S' AND escluso = 'N' AND b_2fase.data_inizio <= now() AND b_2fase.data_fine > now()";
					$ris_fase = $pdo->bindAndExec($sql_fase,$bind);
					if ($ris_fase->rowCount() > 0) $submit = true;
				}

				$filtro_mercato = "";
				if ($record_gara["mercato_elettronico"]=="S") $filtro_mercato = " AND mercato_elettronico = 'S' ";
				$filtro_fase = "";
				if ($record_gara["fasi"]=="S") {
					if (strtotime($record_gara["data_scadenza"]) > time()) {
						$filtro_fase = " AND 2fase = 'N' ";
					}
				}
				if (isset($dialogo) && $dialogo = true) $filtro_fase = " AND 2fase = 'S' ";

				$bind = array();
				$bind[":codice_criterio"] = $record_gara["criterio"];
				$bind[":codice_busta"] = $codice_busta;

				$strsql = "SELECT b_criteri_buste.* FROM b_criteri_buste
									 WHERE codice_criterio = :codice_criterio " . $filtro_fase . $filtro_mercato ." AND codice = :codice_busta AND eliminato = 'N' LIMIT 0,1";
				$ris_buste = $pdo->bindAndExec($strsql,$bind);
				if ($ris_buste->rowCount() > 0 && $submit) {
					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$bind[":codice_lotto"] = $codice_lotto;
					$bind[":codice_utente"] = $_SESSION["codice_utente"];
					$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_utente = :codice_utente AND codice_capogruppo = 0 ";
					$ris = $pdo->bindAndExec($sql,$bind);
					if ($ris->rowCount() > 0) {
						$partecipante = $ris->fetch(PDO::FETCH_ASSOC);
					}
					$busta = $ris_buste->fetch(PDO::FETCH_ASSOC);
					$check_file = true;
					$check_hash = false;
					if ($busta["economica"] == "S") {
						if ($record_gara["nuovaOfferta"] == "N") {
							$check_hash = true;
						} else {
							$bind = array();
							$bind[":codice_gara"] = $record_gara["codice"];
							$sql_valutazione = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica
																	JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
																	WHERE b_valutazione_tecnica.codice_gara = :codice_gara
																	AND b_valutazione_tecnica.tipo = 'N'
																	AND b_valutazione_tecnica.valutazione <> ''
																	AND (b_criteri_punteggi.economica = 'S' OR b_criteri_punteggi.temporale = 'S') ";
							if ($record_gara["nuovaOfferta"] == "S") {
								$bind[":codice_lotto"] = $codice_lotto;
								$sql_valutazione .= " AND (b_valutazione_tecnica.codice_lotto = 0 OR b_valutazione_tecnica.codice_lotto = :codice_lotto) ";
							}
							$ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);
							if ($ris_valutazione->rowCount() > 0) $check_hash = true;
						}
						if ($check_hash) {
							$bind = array();
							if (!empty($partecipante)) {
								$bind[":codice_gara"] = $record_gara["codice"];
								$bind[":codice_lotto"] = $codice_lotto;
								$bind[":codice_partecipante"] = $partecipante["codice"];
								$bind[":tipo"] = "economica";
								$strsql = "SELECT * FROM b_offerte_economiche
													 WHERE codice_gara = :codice_gara
													 AND codice_lotto = :codice_lotto
													 AND codice_partecipante = :codice_partecipante
													 AND tipo = :tipo ORDER BY timestamp DESC";
								$ris_checkFile = $pdo->bindAndExec($strsql,$bind);
							}
							if (!isset($ris_checkFile) || (isset($ris_checkFile) && $ris_checkFile->rowCount()==0)) {
								 $check_file = false;
								 $check_economica = false;
							}
						}
					}
					if ($busta["tecnica"] == "S") {
						$bind = array();
						$bind[":codice_gara"] = $record_gara["codice"];
						$sql_valutazione = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica
																JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
																WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND b_valutazione_tecnica.tipo = 'N' AND b_valutazione_tecnica.valutazione <> ''
																AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N'";
																if ($record_gara["nuovaOfferta"] == "S") {
																	$bind[":codice_lotto"] = $codice_lotto;
																	$sql_valutazione .= " AND (b_valutazione_tecnica.codice_lotto = 0 OR b_valutazione_tecnica.codice_lotto = :codice_lotto) ";
																}
						$ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);
						if ($ris_valutazione->rowCount() > 0) {
							$check_hash = true;
							if (!empty($partecipante)) {
								$bind[":codice_lotto"] = $codice_lotto;
								$bind[":codice_partecipante"] = $partecipante["codice"];
								$bind[":tipo"] = "tecnica";
								$strsql = "SELECT * FROM b_offerte_economiche
													 WHERE codice_gara = :codice_gara
													 AND codice_lotto = :codice_lotto
													 AND codice_partecipante = :codice_partecipante
													 AND tipo = :tipo ORDER BY timestamp DESC";
								$ris_checkFile = $pdo->bindAndExec($strsql,$bind);
							}
							if (!isset($ris_checkFile) || (isset($ris_checkFile) && $ris_checkFile->rowCount()==0)) {
								$check_file = false;
								$check_tecnica = false;
							}
						}
					}

					if ($check_file) {
						?>
						<h2 style="text-transform:uppercase"><strong><?= traduci($busta["nome"]) ?></strong></h2>
						<script type="text/javascript" src="/js/spark-md5.min.js"></script>
						<script type="text/javascript" src="/js/resumable.js"></script>
						<script type="text/javascript" src="resumable-uploader.js"></script>

			      <div id="modulo_partecipazione">
							<form action="upload.php" id="upload_busta" method="post" target="_self" rel="validate">
								 <input type="hidden" name="codice_gara" value="<? echo $record_gara["codice"] ?>">
								 <input type="hidden" name="codice_lotto" value="<? echo $codice_lotto ?>">
								 <input type="hidden" name="codice_busta" value="<? echo $busta["codice"] ?>">
								 <br>
									<div style="text-align:center" class="box">
										<h2 style="text-align:center"><strong><?= traduci("Guida all'invio della documentazione di gara") ?></strong></h2><br>
										<?
										$perc = "25%";
										$step = 0;
										?>
										<div style="float:left; width:<?= $perc ?>">
											<div><strong>STEP <? $step++; echo $step ?></strong></div>
											<div><img src="img/step1.png" alt="Step 1" style="max-width:200px" width="100%"></div>
											<div><?= traduci('partecipazione-step-1') ?><?= ($check_hash) ? ", <strong>".traduci('incluso il file PDF generato dal sistema')."</strong>" : "."; ?></div>
										</div>
										<div style="float:left; width:<?= $perc ?>">
											<div><strong>STEP <? $step++; echo $step ?></strong></div>
											<div><img src="img/step2.png" alt="Step 2" style="max-width:200px" width="100%"></div>
											<div><?= traduci('partecipazione-step-2') ?><?= ($check_hash) ? ", <strong>" . traduci("inclusa la copia firmata digitalmente del file generato dal sistema") . "</strong>." : "."; ?></div>
										</div>
										<div style="float:left; width:<?= $perc ?>">
											<div><strong>STEP <? $step++; echo $step ?></strong></div>
											<div><img src="img/step3.png" alt="Step 3" style="max-width:200px" width="100%"></div>
											<div><?= traduci('partecipazione-step-3') ?></div>
										</div>
										<div style="float:left; width:<?= $perc ?>">
											<div><strong>STEP <? $step++; echo $step ?></strong></div>
											<div><img src="img/step4.png" alt="Step 4" style="max-width:200px" width="100%"></div>
											<div><?= traduci('partecipazione-step-4') ?></div>
										</div>
										<div class="clear"></div>
									</div><br>
									<h1><strong><?= strtoupper(traduci("SELEZIONA IL FILE")) ?></strong></h1><BR>
									<input type="hidden" name="md5_file" id="md5_file" title="File" rel="S;0;0;A">
										<input type="hidden" id="filechunk" name="filechunk">
										<div class="scegli_file"><img src="/img/folder.png" style="vertical-align:middle"><br><?= traduci("Seleziona il file") ?> - <? echo traduci($busta["nome"]) ?></div>
										<script>
											var uploader = (function($){
											return (new ResumableUploader($('.scegli_file')));
											})(jQuery);
										</script>
										<div id="progress_bar" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
										<div class="modulo_partecipazione">
											<?= traduci("Chiave personalizzata") ?>*<br>
											<input class="titolo_edit" style="width:25%" type="password" name="salt" id="salt" title="<?= traduci("Chiave personalizzata") ?>" rel="S;12;0;P"><br>
											<?= traduci('Minimo 12 caratteri') ?><br><br>
											<?= traduci("Ripeti") ?> <?= traduci("Chiave personalizzata") ?>*<br>
											<input class="titolo_edit" style="width:25%" type="password" id="repeat-salt" title="<?= traduci("Chiave personalizzata") ?>" rel="S;12;0;P;salt;="><br><br>
											<span style="font-weight:normal"><?= traduci('memo-chiave') ?></span>
										</div>
										<input type="submit" class="submit_big" value="<?= traduci("Carica busta") ?>" id="invio" onClick="if (confirm('<?= traduci("msg-conferma-revoca") ?>')) { $('#wait').show(); uploader.resumable.upload(); } return false;">
									</form>
								</div>
						<?
					} else {
						echo "<h3 class='ui-state-error'>" . traduci('impossibile accedere') . ": ".traduci('Prima di proseguire è necessario generare e firmare digitalmente i seguenti file di offerta').": <ul>";
						if (isset($check_economica)) echo "<li>" . traduci("Offerta economica") . "</li>";
						if (isset($check_tecnica)) echo "<li>".traduci("Offerta tecnica")."</li>";
						echo "</ul></h3>";
					}
					?>
					<a class="submit_big" style="background-color:#444" href="/gare/telematica2.0/modulo.php?cod=<?= $codice_gara ?><?= ($codice_lotto > 0) ? "&codice_lotto=".$codice_lotto : "" ?>"><?= traduci("Ritorna al pannello") ?></a>
					<?
				} else {
					if (!$submit) {
						echo "<h1>" . traduci("Impossibile accedere") . ": " . traduci("Termini scaduti") . "</h1>";
					} else {
						echo "<h1>" . traduci("Impossibile accedere") . ": 3</h1>";
					}
				}
			} else {
				echo "<h1>". traduci('impossibile accedere') . " - ERROR 2</h1>";
			}
		} else {
			echo "<h1>". traduci('impossibile accedere') . " - ERROR 1</h1>";
		}
	} else {
		echo "<h1>". traduci('impossibile accedere') . " - ERROR 0</h1>";
	}
	include_once($root."/layout/bottom.php");
	?>
