<?
	include_once("../../../config.php");
	if (isset($_GET["cod"]) && is_operatore()) {
		$codice = $_GET["cod"];
		$bind = array();
		$bind[":codice"] = $codice;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql  = "SELECT b_gare.*, b_procedure.invito, b_procedure.directory, b_procedure.fasi, b_procedure.mercato_elettronico FROM b_gare JOIN b_modalita ON b_gare.modalita = b_modalita.codice JOIN b_procedure ON b_gare.procedura = b_procedure.codice
								WHERE b_gare.codice = :codice ";
		$strsql .= "AND b_gare.annullata = 'N' AND b_modalita.online = 'S' ";
		$strsql .= "AND codice_gestore = :codice_ente ";
		$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
		$risultato = $pdo->bindAndExec($strsql,$bind);

		$accedi = false;
		$seconda_fase = false;

		if ($risultato->rowCount() > 0) {

			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
			if ($record_gara["procedura"] == 11) {
				$bind = array();
				$bind[":codice_gara"] = $codice;
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql  = "SELECT b_rdo_ad.*, r_rdo_ad.timestamp_trasmissione,r_rdo_ad.codice AS codice_rdo, r_rdo_ad.nome_file FROM
										r_rdo_ad JOIN b_rdo_ad ON r_rdo_ad.codice_rdo = b_rdo_ad.codice
										JOIN b_gare ON b_rdo_ad.codice_gara = b_gare.codice
										WHERE b_rdo_ad.codice_gara = :codice_gara AND r_rdo_ad.codice_utente = :codice_utente
										AND b_gare.annullata = 'N'
										AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente)
										AND (b_gare.pubblica = '2' OR b_gare.pubblica = '1') ORDER BY b_rdo_ad.timestamp DESC";

				$ris_rdo = $pdo->bindAndExec($strsql,$bind);
				if ($ris_rdo->rowCount() > 0) {
					$accedi = false;
					header("Location: /gare/rdo_ad/view.php?cod={$record_gara["codice"]}");
					die();
				}
				echo "<h1>".traduci('Impossibile accedere')." - La stazione appaltante non ha ancora impostato richieste di offerta</h1>";
				die();
			}

			$bind = array();
			$bind[":codice_gara"] = $codice;
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql  = "SELECT b_dialogo.*, r_dialogo.timestamp_trasmissione,r_dialogo.codice AS codice_dialogo, r_dialogo.nome_file FROM
									r_dialogo JOIN b_dialogo ON r_dialogo.codice_dialogo = b_dialogo.codice
									JOIN b_gare ON b_dialogo.codice_gara = b_gare.codice
									WHERE b_dialogo.codice_gara = :codice_gara AND r_dialogo.codice_utente = :codice_utente
									AND b_gare.annullata = 'N' AND b_gare.dialogo_chiuso = 'N'
									AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente)
									AND (b_gare.pubblica = '2' OR b_gare.pubblica = '1') ORDER BY b_dialogo.timestamp DESC";

			$ris_dialogo = $pdo->bindAndExec($strsql,$bind);
			if ($ris_dialogo->rowCount() > 0 || ($record_gara["directory"]=="dialogo" && $record_gara["dialogo_chiuso"] == "N")) {
				$accedi = false;
				header("Location: /gare/dialogo/view.php?cod={$record_gara["codice"]}");
				die();
			}

		}
		include_once($root."/layout/top.php");
		$public = true;
		$codice = $_GET["cod"];
		$bind = array();
		$bind[":codice"] = $codice;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql  = "SELECT b_gare.*, b_procedure.invito, b_procedure.directory, b_procedure.fasi, b_procedure.mercato_elettronico FROM b_gare JOIN b_modalita ON b_gare.modalita = b_modalita.codice JOIN b_procedure ON b_gare.procedura = b_procedure.codice
								WHERE b_gare.codice = :codice ";
		$strsql .= "AND b_gare.annullata = 'N' AND b_modalita.online = 'S' ";
		$strsql .= "AND codice_gestore = :codice_ente ";
		$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
		$risultato = $pdo->bindAndExec($strsql,$bind);

		$accedi = false;
		$seconda_fase = false;

		if ($risultato->rowCount() > 0) {

			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

			$bind = array();
			$bind[":codice_gara"] = $codice;
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql  = "SELECT b_rdo_ad.*, r_rdo_ad.timestamp_trasmissione,r_rdo_ad.codice AS codice_rdo, r_rdo_ad.nome_file FROM
									r_rdo_ad JOIN b_rdo_ad ON r_rdo_ad.codice_rdo = b_rdo_ad.codice
									JOIN b_gare ON b_rdo_ad.codice_gara = b_gare.codice
									WHERE b_rdo_ad.codice_gara = :codice_gara AND r_rdo_ad.codice_utente = :codice_utente
									AND b_gare.annullata = 'N'
									AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente)
									AND (b_gare.pubblica = '2' OR b_gare.pubblica = '1') ORDER BY b_rdo_ad.timestamp DESC";

			$ris_rdo = $pdo->bindAndExec($strsql,$bind);
			if ($ris_rdo->rowCount() > 0) {
				$accedi = false;
				echo '<meta http-equiv="refresh" content="0;URL=/gare/rdo_ad/view.php?cod='.$record_gara["codice"].'">';
				die();
			}

			$bind = array();
			$bind[":codice_gara"] = $codice;
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql  = "SELECT b_dialogo.*, r_dialogo.timestamp_trasmissione,r_dialogo.codice AS codice_dialogo, r_dialogo.nome_file FROM
									r_dialogo JOIN b_dialogo ON r_dialogo.codice_dialogo = b_dialogo.codice
									JOIN b_gare ON b_dialogo.codice_gara = b_gare.codice
									WHERE b_dialogo.codice_gara = :codice_gara AND r_dialogo.codice_utente = :codice_utente
									AND b_gare.annullata = 'N' AND b_gare.dialogo_chiuso = 'N'
									AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente)
									AND (b_gare.pubblica = '2' OR b_gare.pubblica = '1') ORDER BY b_dialogo.timestamp DESC";

			$ris_dialogo = $pdo->bindAndExec($strsql,$bind);
			if ($ris_dialogo->rowCount() > 0 || ($record_gara["directory"]=="dialogo" && $record_gara["dialogo_chiuso"] == "N")) {
				$accedi = false;
				echo '<meta http-equiv="refresh" content="0;URL=/gare/dialogo/view.php?cod='.$record_gara["codice"].'">';
				die();
			}

			$bind = array();
			$bind[":codice"] = $codice;
			$derivazione = "";
			$sql = "SELECT * FROM b_procedure WHERE codice = :codice_procedura";
			$ris = $pdo->bindAndExec($sql,array(":codice_procedura"=>$record_gara["procedura"]));
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
			<h1><?= traduci("PANNELLO DI GARA") ?> - ID <? echo $record_gara["id"] ?></h1>
			<h2><strong><? echo traduci(trim($record_gara["tipologie_gara"])) ?></strong> - <? echo $record_gara["oggetto"] ?></h2>
			<?
			$bind = array();
			$bind[":codice_gara"] = $record_gara["codice"];
			$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara ORDER BY codice";
			$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
			if ($ris_lotti->rowCount() > 0) {
				$print_form =false;
				if (isset($_GET["codice_lotto"]) || isset($codice_lotto)) {
					if (isset($_GET["codice_lotto"]) && !isset($codice_lotto)) $codice_lotto = $_GET["codice_lotto"];
					$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto ORDER BY codice";
					$bind[":codice_lotto"] = $codice_lotto;
					$ris_check_lotti = $pdo->bindAndExec($sql_lotti,$bind);
					if ($ris_check_lotti->rowCount() > 0) {
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
					?>
					<div class="box">
					<h3><?= traduci("Lotti") ?></h3>
					<?
						if (strtotime($record_gara["data_scadenza"]) > time()) {
							if ($record_gara["modalita_lotti"]==2) {
								?>
									<strong style="color:#C00"><?= traduci("attenzione") ?>: <?= traduci("obbligatorio-tutti-lotti") ?></strong><br>
								<?
							} else if ($record_gara["modalita_lotti"]==1) {
								?>
								<strong style="color:#C00"><?= traduci("attenzione") ?>: <?= traduci("Impossibile partecipare a più lotti") ?></strong><br>
								<?
								$bind =array();
								$bind[":codice_gara"] = $record_gara["codice"];
								$bind[":codice_utente"] = $_SESSION["codice_utente"];
								$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND conferma = TRUE AND codice_utente = :codice_utente";
								$ris_partecipazioni = $pdo->bindAndExec($sql,$bind);
								$partecipato = false;
								if ($ris_partecipazioni->rowCount() > 0) $partecipato = true;
							}
						} 
					?>
					<table width="100%">
					<?
					$scaduto = false;
					if (strtotime($record_gara["data_scadenza"]) <= time()) $scaduto = true;
					while ($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
							$partecipato_lotto = false;
							$bind = array();
							$bind[":lotto"] = $lotto["codice"];
							$bind[":codice_gara"] = $record_gara["codice"];
							$bind[":codice_utente"] = $_SESSION["codice_utente"];
							$sql = "SELECT * FROM r_partecipanti WHERE codice_lotto = :lotto AND conferma = TRUE AND codice_gara = :codice_gara AND codice_utente = :codice_utente";
							$ris_partecipante_lotto = $pdo->bindAndExec($sql,$bind);
							$sql = "SELECT * FROM r_partecipanti WHERE codice_lotto = :lotto AND codice_gara = :codice_gara AND codice_utente = :codice_utente";
							$ris_partecipante_lotto_fasi = $pdo->bindAndExec($sql,$bind);
							if ($ris_partecipante_lotto->rowCount() > 0) $partecipato_lotto = true;
						?>
						<? if (!$scaduto || ($scaduto && $partecipato_lotto) || ($record_gara["fasi"] == 'S' && $ris_partecipante_lotto_fasi->rowCount() > 0)) { ?>
							<tr>
								<td><strong><? echo $lotto["oggetto"] ?></strong><br>
									<? echo $lotto["descrizione"] ?>
								</td>
								<td width="20%">
									<? if (!isset($partecipato) || (($partecipato && $partecipato_lotto) || !$partecipato)) { ?>
									<a href="/gare/telematica2.0/modulo.php?cod=<? echo $record_gara["codice"] ?>&codice_lotto=<? echo $lotto["codice"] ?>" class="submit_big" <? if ($partecipato_lotto) echo "style=\"background-color:#0C0\"" ?> title="<?= traduci("Partecipa") ?>"><?= (strtotime($record_gara["data_scadenza"]) > time()) ? traduci("Partecipa") : traduci("Visualizza") ?></a>
										<? if ($partecipato_lotto && (strtotime($record_gara["data_scadenza"]) > time())) {
											$partecipante_lotto = $ris_partecipante_lotto->fetch(PDO::FETCH_ASSOC);
											?><a id="button_revoca_<? echo $partecipante_lotto["codice"] ?>" href="#" onClick="elimina('<? echo $partecipante_lotto["codice"] ?>','gare/telematica2.0');" class="submit_big" style="background-color:#C30" title="<?= traduci("Revoca partecipazione") ?>"><?= traduci("Revoca partecipazione") ?></a><?
										}
									} else {
										?>
										<h3><?= traduci("Impossibile partecipare a più lotti") ?></h3>
										<?
									}
									?>
								</td>
							</tr>
					<?
					}
				}
				?>
				</table>
				</div>
			<?
			}

				} else {
					$codice_lotto = 0;
				}

			if ($print_form) {

				$seconda_fase = false;
				$submit = false;

				$bind = array();
				$bind[":lotto"] = $codice_lotto;
				$bind[":codice_gara"] = $record_gara["codice"];
				$sql_lotto = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :lotto";
				$ris_lotto = $pdo->bindAndExec($sql_lotto,$bind);
				if ($ris_lotto->rowCount() > 0) {
					$lotto = $ris_lotto->fetch(PDO::FETCH_ASSOC);
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
					if ($ris_fase->rowCount() > 0) $seconda_fase = true;
				}

				$filtro_mercato = "";
				if ($record_gara["mercato_elettronico"]=="S") $filtro_mercato = " AND mercato_elettronico = 'S' ";
				$filtro_fase = "";
				if ($record_gara["fasi"]=="S") {
					if (strtotime($record_gara["data_scadenza"]) > time()) {
						$filtro_fase = " AND 2fase = 'N' ";
					}
				}
				if (isset($dialogo) && $dialogo == true) $filtro_fase = " AND 2fase = 'S' ";

				$bind = array();
				$bind[":codice_criterio"] = $record_gara["criterio"];
				$strsql = "SELECT b_criteri_buste.* FROM b_criteri_buste
									 WHERE codice_criterio = :codice_criterio " . $filtro_fase . $filtro_mercato ." AND eliminato = 'N' ORDER BY ordinamento";
				$ris_buste = $pdo->bindAndExec($strsql,$bind);

				if ($ris_buste->rowCount() > 0) {
					$sql_partecipante = "SELECT r_partecipanti.* FROM r_partecipanti WHERE r_partecipanti.codice_utente = :codice_utente AND r_partecipanti.codice_lotto = :lotto AND r_partecipanti.codice_gara = :codice_gara AND codice_capogruppo = 0";
					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$bind[":codice_utente"] = $_SESSION["codice_utente"];
					$bind[":lotto"] = $codice_lotto;
					$ris_partecipante = $pdo->bindAndExec($sql_partecipante,$bind);
					if($ris_partecipante->rowCount()==1) $partecipante = $ris_partecipante->fetch(PDO::FETCH_ASSOC);
					?><div id="conferma_partecipazione_bis"></div><?
					if ($submit) {
						if (empty($partecipante)) {
							if ($ris_inviti->rowCount() == 0) {
								?>
								<form action="/gare/telematica2.0/save_bozza.php" method="post" rel="validate" id="dati_bozza">
									<input type="hidden" name="codice_gara" value="<?= $record_gara["codice"] ?>">
									<input type="hidden" name="codice_lotto" value="<?= $codice_lotto ?>">
									<div style="text-align:right">
										<button type="submit" style="padding:10px" class="submit"><span class="fa fa-pencil"></span> <?= traduci("Salva in bozza per ricevere comunicazioni") ?></button>
									</div>
								</form>
								<?
							}
						}
						if (!isset($dialogo)) {
							// if (!isset($partecipante) || (isset($partecipante) && $partecipante["conferma"] == false))
							?>
							<form action="/gare/telematica2.0/save_raggruppamento.php" method="post" rel="validate" target="_self" id="dati_raggruppamento">
								<input type="hidden" name="codice_gara" value="<?= $record_gara["codice"] ?>">
								<input type="hidden" name="codice_lotto" value="<?= $codice_lotto ?>">
								<div class="box">
								<h2><?= traduci("raggruppamento") ?></h2>
								<?
									$path = $config["path_vocabolario"]."/{$_SESSION["language"]}/partecipazione-nota-raggruppamento.html";
									if (file_exists($path)) include($path);
								?>
								<br><br>
									<table width="100%">
										<tbody id="table_raggruppamento">
												<?
													if (isset($partecipante)) {
														$sql_group = "SELECT * FROM r_partecipanti WHERE codice_capogruppo = :codice_capogruppo";
														$ris_group = $pdo->bindAndExec($sql_group,array(":codice_capogruppo"=>$partecipante["codice"]));
														if ($ris_group->rowCount()) {
															while($record_membro=$ris_group->fetch(PDO::FETCH_ASSOC)) {
																$id_membro = $record_membro["codice"];
																include("tr_membro.php");
															}
														}
													}
												?>
												</tbody>
										<tfoot>
											<button style="font-size:14px; <?=(isset($partecipante) && $partecipante["conferma"]) ? "display:none;" : "" ?>"
												class="aggiungi"
												onClick="aggiungi('tr_membro.php','#table_raggruppamento');return false;">
													<span class="fa fa-plus-circle"></span> <strong><?= traduci("Aggiungi partecipante al raggruppamento") ?></strong>
												</button>
											</tfoot>
									</table>
									<input <?= (isset($partecipante) && $partecipante["conferma"]) ? "style='display:none'" : "" ?> type="submit" class="submit_big" onclick="return (confirm('<?= traduci("msg-conferma-revoca") ?>'))"value="<?= traduci("Salva raggruppamento") ?>">
									<?
										if (isset($partecipante) && $partecipante["conferma"]) {
											?>
											<script>
												function edit_raggruppamento() {
													if (confirm('Modificare il raggruppamento comporterà la revoca di eventuali istanze precedenti. Sei sicuro di voler continuare?')) {
														$("#dati_raggruppamento").find(":input").removeAttr("disabled");
														$("#dati_raggruppamento").find(".aggiungi").show();
														$("#dati_raggruppamento").find(".submit_big").show();
													}
													return false;
												}
											</script>
											<button class="submit_big" style="background-color:#FC0" onClick="edit_raggruppamento(); return false"><?= traduci("Modifica Raggruppamento") ?></button>
											<?
										}
									?><br>
									<strong><span class="fa fa-alert-sign"></span> <?= strtoupper(traduci("ATTENZIONE")) ?>: <?= traduci('msg-modifica-raggruppamento') ?></strong>
								</div>
						</form>
							<?
						}
					}

					$buste = array();
					$boxWidth= 98 / $ris_buste->rowCount();
					while($busta = $ris_buste->fetch(PDO::FETCH_ASSOC)) {
						$buste[$busta["codice"]] = false;
						unset($memorizzata);
						$stato = "#F00";
						?>
						<div style="width:<?= $boxWidth ?>%; margin-right:4px; float:left;">
						<div class="box">
						<h3 style="text-align:center">
							<span class="fa fa-folder-open fa-5x"></span>
							<br><?= traduci($busta["nome"]) ?></h3>
								<?
								if (isset($partecipante)) {
									$sql_in = "SELECT * FROM b_buste WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_busta = :codice_busta AND codice_partecipante = :codice_partecipante ";
									if ($seconda_fase) $sql_in .= " AND nome_file LIKE '%seconda_fase'";
									$ris_in = $pdo->bindAndExec($sql_in,array(":codice_busta"=>$busta["codice"],":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto,":codice_partecipante"=>$partecipante["codice"]));
									if ($ris_in->rowCount()>0) {
										$rec_busta = $ris_in->fetch(PDO::FETCH_ASSOC);
										$emendabile = checkBustaEmendabile($rec_busta);
										$buste[$busta["codice"]] = true;
										?>
										<a class="pannello" href="#" onclick="$('#view_<?= $busta["codice"] ?>').toggle()"><?= traduci("Visualizza Invio") ?></a>
										<form class="box" id="view_<?= $busta["codice"] ?>" action="/gare/telematica2.0/open.php" target="_blank" rel="validate" method="post" style="display:none">
											<input type="hidden" name="codice_gara" value="<?= $record_gara["codice"] ?>">
											<input type="hidden" name="codice_lotto" value="<?= $codice_lotto ?>">
											<input type="hidden" name="codice_busta" value="<?= $busta["codice"] ?>">
											<? if ($rec_busta["aperto"]=="N") { ?>
												<input type="password" class="titolo_edit" name="salt" title="<?= traduci("Chiave Personalizzata") ?>" rel="S;12;0;P">
											<? } else { ?>
												<input type="hidden" name="salt" value="aperta">
											<? } ?>
											<input type="submit" class="submit_big" class="pannello" value="<?= traduci("Scarica") ?>">
										</form>
										<?
										$sql_in = "SELECT * FROM b_emendamenti WHERE busta_originale = :originale AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_partecipante = :codice_partecipante ";
										$ris_emendamento = $pdo->bindAndExec($sql_in,array(":originale"=>$rec_busta["codice"],":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto,":codice_partecipante"=>$partecipante["codice"]));
										$label_button_emendamento = "Richiedi emendamento";
										if ($ris_emendamento->rowCount()>0) {
											$label_button_emendamento = "Sostituisci emendamento";
											$rec_emendamento = $ris_emendamento->fetch(PDO::FETCH_ASSOC);
											$stato_emendamento = "#FC0";
											$label_emendamento = "Richiesto";
											if ($rec_emendamento["accettato"] == "S") {
												$stato_emendamento = "#0C0";
												$label_emendamento = "Accettato";
											} else if ($rec_emendamento["accettato"] == "N") {
												$stato_emendamento = "#C00";
												$label_emendamento = "Rifiutato";
											}
											?>
											<a class="pannello" href="#" onclick="$('#view_emenedamento_<?= $busta["codice"] ?>').toggle()">
												<?= traduci("Visualizza emendamento trasmesso") ?>
												<small style="float:right; padding:2px; color:#000; background-color: <?= $stato_emendamento ?>"><?= $label_emendamento ?></small>
												<div class="clear"></div>
											</a>
											<? if (!empty($rec_emendamento["motivazione"])) { ?>
												<div class="box errore">
													<small><?= traduci("Motivazione rifiuto") ?></small><br>
													<?= $rec_emendamento["motivazione"] ?>
												</div>
											<? } ?>
											<form class="box" id="view_emenedamento_<?= $busta["codice"] ?>" action="/gare/telematica2.0/open-emendamento.php" target="_blank" rel="validate" method="post" style="display:none">
												<input type="hidden" name="codice_gara" value="<?= $record_gara["codice"] ?>">
												<input type="hidden" name="codice_lotto" value="<?= $codice_lotto ?>">
												<input type="hidden" name="busta_originale" value="<?= $rec_emendamento["busta_originale"] ?>">
												<input type="hidden" name="codice" value="<?= $rec_emendamento["codice"] ?>">
												<? if ($rec_busta["aperto"]=="N") { ?>
													<input type="password" class="titolo_edit" name="salt" title="<?= traduci("Chiave Personalizzata") ?>" rel="S;12;0;P">
												<? } else { ?>
													<input type="hidden" name="salt" value="aperta">
												<? } ?>
												<input type="submit" class="submit_big" class="pannello" value="<?= traduci("Scarica") ?>">
											</form>
											<?
										}
										if ($emendabile) { 
											?>
											<a class="pannello" style="background-color:#09F" href="/gare/telematica2.0/submit-emendamento.php?busta_originale=<?= $rec_busta["codice"] ?>&codice_busta=<?= $busta["codice"]?>&codice_gara=<?= $record_gara["codice"] ?>&codice_lotto=<?= $codice_lotto ?>">
												<?= traduci($label_button_emendamento) ?>
											</a>
											<?
										}
										if ($record_gara["oe_open"] == "S" && $partecipante["ammesso"] == "S" && $partecipante["escluso"] == "N") {
											$bind = array();
											$bind[":codice_gara"] = $record_gara["codice"];
											$bind[":codice_lotto"] = $codice_lotto;
											$bind[":codice_busta"] = $busta["codice"];
											$strsql  = "SELECT * FROM b_date_apertura
																	WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_busta = :codice_busta
																	ORDER BY codice DESC LIMIT 0,1";
											$check_apertura = $pdo->bindAndExec($strsql,$bind);
											if ($check_apertura->rowCount()>0) {
												$record_data = $check_apertura->fetch(PDO::FETCH_ASSOC);
												$time = strtotime($record_data["data_apertura"]);
												if ($time <= time()) {
													if ($rec_busta["aperto"]=="N") {
														?>
														<a class="pannello" style='background-color:#09F' href="#" onclick="$('#unlock_<?= $busta["codice"] ?>').toggle()"><?= traduci("Apri busta") ?></a>
			 										 	 <form class="box" id="unlock_<?= $busta["codice"] ?>" action="/gare/telematica2.0/unlock.php" rel="validate" method="post" style="display:none">
			 												 <input type="hidden" name="codice_gara" value="<?= $record_gara["codice"] ?>">
			 												 <input type="hidden" name="codice_lotto" value="<?= $codice_lotto ?>">
			 												 <input type="hidden" name="codice_busta" value="<?= $busta["codice"] ?>">
															 <input type="password" class="titolo_edit" name="salt" title="<?= traduci("Chiave Personalizzata") ?>" rel="S;12;0;P">
			 												 <input type="submit" class="submit_big" class="pannello" value="<?= traduci("Apri busta") ?>">
			 											 </form>
														<?
													} else {
														?>
														<div style="padding:3px; background-color:#0C0; text-align:center; color:#fff">
															<strong><?= traduci("Aperta") ?></strong>
														</div>
														<?
													}
												}
											}
										}
									}
								}
								?>
								<?
									if ($submit || $seconda_fase) {
										$bind = array();
										$bind[":codice_gara"] = $record_gara["codice"];
										$genera_offerta = false;
										$check = false;
										if ($busta["tecnica"] == "S" || $busta["economica"] == "S") {
											$tipo = "economica";
											$sql_valutazione = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica
																					JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
																					WHERE b_valutazione_tecnica.codice_gara = :codice_gara
																					AND b_valutazione_tecnica.valutazione <> '' AND b_valutazione_tecnica.tipo = 'N' ";
											if ($record_gara["nuovaOfferta"] == "S") {
												$bind[":codice_lotto"] = $codice_lotto;
												$sql_valutazione .= "AND (b_valutazione_tecnica.codice_lotto = 0 OR b_valutazione_tecnica.codice_lotto = :codice_lotto) ";
											}
											if ($busta["tecnica"]=="S" && $busta["economica"] == "S") {
												$label = "Offerta";
												if ($record_gara["nuovaOfferta"] == "N") {
													unset($sql_valutazione);
												}
											} else if ($busta["tecnica"] == "S") {
												$label = "Offerta tecnica";
												$tipo = "tecnica";
												$sql_valutazione .= "AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N'";
											} else if ($busta["economica"] == "S") {
												$label = "Offerta economica";
												if ($record_gara["nuovaOfferta"] == "S") {
													$sql_valutazione .= "AND (b_criteri_punteggi.economica = 'S' OR b_criteri_punteggi.temporale = 'S')";
												} else {
													unset($sql_valutazione);
												}
											}
											if (isset($sql_valutazione)) {
												$ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);
												if ($ris_valutazione->rowCount() > 0) $genera_offerta = true;
											} else {
												$genera_offerta = true;
											}
											if (isset($partecipante)) {
												$bind[":codice_lotto"] = $codice_lotto;
												$bind[":codice_partecipante"] = $partecipante["codice"];
												$bind[":tipo"] = $tipo;
												$strsql = "SELECT * FROM b_offerte_economiche
																	 WHERE codice_gara = :codice_gara
																	 AND codice_lotto = :codice_lotto
																	 AND codice_partecipante = :codice_partecipante
																	 AND tipo = :tipo ORDER BY timestamp DESC";
												$ris_checkFile = $pdo->bindAndExec($strsql,$bind);
												if ($ris_checkFile->rowCount()==1) {
													 $check = true;
													 $stato = "#F50";
												}
											}
											if ($genera_offerta) {
												?>
												<a <?= ($check) ? "" : "style='background-color:#09F'" ?> class="pannello" href="/gare/telematica2.0/genera_offerta.php?<?= $tipo ?>=1&codice_gara=<?= $record_gara["codice"] ?>&codice_lotto=<?= $codice_lotto ?>">
													<?= ($check) ? traduci("Rigenera") : traduci("Genera") ?> <?= traduci($label) ?>
												</a>
												<?
												if ($check) {
													if (isset($_SESSION["offerFile"][$partecipante["codice"]][$tipo])) {
													?>
													<a href="download_offerta.php?codice_partecipante=<?=$partecipante["codice"] ?>&tipo=<?= $tipo ?>" target="_blank" class="pannello">
														<?= traduci("Scarica") ?> <?= traduci($label) ?>
													</a>
													<?
													} else {
														?>
														<a class="pannello" href="#" onclick="$('#download_<?= $tipo ?>').toggle()"><?= traduci("Scarica") ?> <?= traduci($label) ?></a>
			 										 	 <form class="box" id="download_<?= $tipo ?>" action="/gare/telematica2.0/download_offerta.php" target="_blank" rel="validate" method="get" style="display:none">
			 												 <input type="hidden" name="codice_partecipante" value="<?= $partecipante["codice"] ?>">
			 												 <input type="hidden" name="tipo" value="<?= $tipo ?>">
			 												 <input type="password" class="titolo_edit" name="salt" title="<?= traduci("Chiave Personalizzata") ?>" rel="S;12;0;P">
			 												 <input type="submit" class="submit_big" class="pannello" value="<?= traduci("Scarica") ?>">
			 											 </form>
														<?
													}
												}
											}
										}
										if (!$genera_offerta || ($genera_offerta && $check)) {
											if ($buste[$busta["codice"]]) {
											 $stato = "#0C0";
											 $label = "Sostituisci Documentazione";
											 $style = "";
											} else {
											 $style = "style='background-color:#09F'";
											 $label = "Carica documentazione";
										 	} ?>
											<a <?= $style ?> class="pannello" href="/gare/telematica2.0/submit.php?codice_busta=<?= $busta["codice"]?>&codice_gara=<?= $record_gara["codice"] ?>&codice_lotto=<?= $codice_lotto ?>">
												<?= traduci("$label") ?>
											</a>
										<? } ?>
										<div style="padding:3px; background-color:<?= $stato ?>; text-align:center; color:#fff"><strong>
											<?
												switch($stato) {
													case "#0C0":
														echo traduci("Caricata");
														break;
													case "#F50":
														echo traduci("In preparazione");
														break;
													default:
														echo traduci("Non inviata");
												}
											?></strong>
										</div>
								<? } ?>
							</div>
						</div>
						<?
					}
					?>
					<div class="clear"></div>
					<?
					if ($submit || $seconda_fase) {
						if (in_array(false,$buste,true)===false) {
							if ($partecipante["conferma"]==0) {
								?>
								<div id="conferma_partecipazione">
									<div class="box">
										<h3 class="ui-state-error" style="text-align:center">
											<?
												$path = $config["path_vocabolario"]."/{$_SESSION["language"]}/partecipazione-msg-invia.html";
												if (file_exists($path)) include($path);
											?>
										</h3><br>
										<form target="_self" action="/gare/telematica2.0/invia.php" method="post">
											<input type="hidden" name="codice_gara" value="<?= $record_gara["codice"] ?>">
											<input type="hidden" name="codice_lotto" value="<?= $codice_lotto ?>">
										<input type="submit" style="font-size:18px" value="<?= strtoupper(traduci("INVIA LA PARTECIPAZIONE")) ?>" class="submit_big" onclick="$('#wait').show(); return true;"/>
										</form>
									</div>
								</div>
								<script>
									$("#conferma_partecipazione_bis").html($("#conferma_partecipazione").html());
									f_ready();
								</script>
								<?
							} else {
								?>
								<ul class="success">
									<li><h3><strong><?= traduci("Partecipazione inviata") ?></strong></h3></li>
								</ul>
								<? if (!$seconda_fase) { ?>
									<a id="button_revoca_<? echo $partecipante["codice"] ?>" href="#" onClick="elimina('<? echo $partecipante["codice"] ?>','gare/telematica2.0');" class="submit_big" style="background-color:#C30" title="<?= traduci("revoca partecipazione") ?>"><?= traduci("revoca partecipazione") ?></a>
								<?
								}
							}
						} else {
							?>
							<h3 class="ui-state-error"><strong><?= traduci("msg-check-buste") ?></strong></h3>
							<?
						}
					} else {
						?><strong style="background-color:#F30; cursor:default" class="submit_big"><?= strtoupper(traduci("GARA SCADUTA")) ?></strong><?
						$bind = array(':codice' => $record_gara["codice"], ":codice_utente" => $_SESSION["codice_utente"],":codice_lotto"=>$codice_lotto);
						$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_utente = :codice_utente AND (conferma = TRUE OR conferma IS NULL)";
						$ris_partecipazione_confermata = $pdo->bindAndExec($sql,$bind);
						if ($ris_partecipazione_confermata->rowCount() > 0) {
							include_once($root."/inc/zoomMtg.class.php");
							$zoom = new zoomMtg;
							$meeting = $zoom->getMeetingFromDB("gare",$record_gara["codice"],$codice_lotto,"seduta pubblica");
							if (!empty($meeting)) {
								$meeting = json_decode($meeting["response"],true);
								$status = $zoom->getMeetingDetails($meeting["id"]);
								if (!empty($status["status"]) && $status["status"] != "finished") {
									?>
									<a target="_blank" href="conference.php?cod=<?= $record_gara["codice"] ?>&codice_lotto=<?= $codice_lotto ?>" class="submit_big">
										<span class="fa fa-video-camera"></span> Avvia Conference Room
									</a> 
									<?
								}
							}
							?>
							<div id="monitor_seduta">
							<?
								include("monitor.php");
							?>
							</div>
							<script>
							$.ajaxSetup ({
							// Disable caching of AJAX responses */
							cache: false
							});
							var ajaxDelay = 300000;
							setInterval(function(){
								$.ajax({
									url: 'monitor.php',
									dataType: 'html',
									method: 'get',
									async: "true",
									data: "cod=<?= $record_gara["codice"] ?>&codice_lotto=<?= $codice_lotto ?>",
									success: function(script) {
										$("#monitor_seduta").html(script);
									}
								});
							}, ajaxDelay);
							</script>
							<?
						} else {
							echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
						}
					}
					if ($codice_lotto > 0) {
						?>
						<a class="submit_big" style="background-color:#444" href="/gare/telematica2.0/modulo.php?cod=<?= $record_gara["codice"] ?>"><?= traduci("Ritorna alla scelta lotto") ?></a>
						<?
					}
				} else {
					echo "<h1>".traduci('impossibile accedere')." - 1</h1>";
				}
			}
		} else {
			echo "<h1>".traduci('impossibile accedere')." - 2</h1>";
		}
		include_once($root."/layout/bottom.php");
	} else {
		echo "<h1>".traduci('impossibile accedere')." - 3</h1>";
	}
	?>
