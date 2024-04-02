<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$public = true;
	if (isset($_GET["cod"]) && is_operatore()) {
		$codice = $_GET["cod"];

		$bind = array();
		$bind[":codice"] = $codice;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql  = "SELECT b_concorsi.* FROM b_concorsi
								WHERE b_concorsi.codice = :codice ";
		$strsql .= "AND b_concorsi.annullata = 'N' ";
		$strsql .= "AND codice_gestore = :codice_ente ";
		$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
		$risultato = $pdo->bindAndExec($strsql,$bind);

		$accedi = false;

		if ($risultato->rowCount() > 0) {

			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

			$i = 0;
			$open = false;
			$last = array();
			$fase_attiva = array();

			$sql_fasi = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara  ";
			$ris_fasi = $pdo->bindAndExec($sql_fasi,array(":codice_gara"=>$record_gara["codice"]));
			if ($ris_fasi->rowCount() > 0) {
				$open = true;
				while($fase = $ris_fasi->fetch(PDO::FETCH_ASSOC)) {
					if ($fase["attiva"]=="S") {
						if ($i > 0) $open = false;
						$last = $fase_attiva;
						$fase_attiva = $fase;
					}
					$i++;
				}
			}

			if ($open) {
				$accedi = true;
			} else if (!empty($last["codice"])) {
				$sql_check = "SELECT * FROM r_partecipanti_concorsi JOIN r_partecipanti_utenti_concorsi ON r_partecipanti_concorsi.codice = r_partecipanti_utenti_concorsi.codice_partecipante
								WHERE r_partecipanti_concorsi.codice_gara = :codice_gara AND r_partecipanti_concorsi.codice_fase = :codice_fase AND r_partecipanti_concorsi.conferma = 1 AND r_partecipanti_concorsi.ammesso = 'S'
								AND r_partecipanti_concorsi.escluso = 'N' AND r_partecipanti_utenti_concorsi.codice_utente = :codice_utente ";
				$ris_check = $pdo->bindAndExec($sql_check,array(":codice_gara"=>$record_gara["codice"],":codice_fase"=>$last["codice"],":codice_utente"=>$_SESSION["codice_utente"]));
				if ($ris_check->rowCount() > 0) $accedi = true;
			}

		if ($accedi) {
			$print_form = true;
			?>
			<h1>PANNELLO DI CONCORSO - ID <? echo $record_gara["id"] ?></h1>
			<h2><strong><? echo $record_gara["oggetto"] ?></strong> - Fase: <?= $fase_attiva["oggetto"] ?></h2>
			<?
				if (empty($_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]]["salt"])) {
					$print_form = false;
					?>
					<div class="ui-state-error padding" style="text-align:center">
						<h2 style="text-align:center"><strong>ATTENZIONE</strong></h2>
						Per tutelare la segretezza della partecipazione, tutte le informazioni relative all'utente saranno memorizzate in forma crittografata.<br><br>
						Per proseguire scegli una chiave personalizzata di almeno 12 caratteri.<br><br>
						Tale chiave, insieme al codice identificativo rilasciato dalla piattaforma, ti permetter&agrave; di riaccedere al pannelo di partecipazione al concorso.<br>
						Inoltre, la Stazione Appaltante potrebbe richiederti la chiave personalizzata in fase di apertura buste.<br><br>
						In caso di smarrimento della chiave personalizzata non sar&agrave; possibile recuperare la stessa.
					</div>
					<div style="width:49%; float:left">
						<form action="/concorsi/partecipa/generaUID.php" method="post" rel="validate" target="_self">
							<input type="hidden" name="codice_gara" value="<?= $record_gara["codice"] ?>">
							<div class="modulo_partecipazione">
								<h2>Crea chiave</h2>
								Chiave personalizzata*<br><input class="titolo_edit" style="width:50%" type="password" name="salt" id="salt" title="Chiave" rel="S;12;0;P"><br>
								Minimo 12 caratteri<br><br>
								Ripeti Chiave personalizzata*<br><input class="titolo_edit" style="width:50%" type="password" name="salt_repeat" id="salt_repeat" title="Ripeti chiave" rel="S;12;0;P;salt;="><br>
							</div>
							<input type="submit" class="submit_big" value="Invia" id="invio">
						</form>
					</div>
					<div style="width:49%; float:right">
						<form action="/concorsi/partecipa/recupera.php" method="post" rel="validate">
							<input type="hidden" name="codice_gara" value="<?= $record_gara["codice"] ?>">
							<div class="modulo_partecipazione">
								<h2>Recupera partecipazione</h2><br>
								Codice Identificativo Univoco<br><input class="titolo_edit" style="width:50%" type="text" name="identificativo" id="identificativo" title="Identificativo" rel="S;0;0;A"><br>
								<br>
								Chiave personalizzata*<br><input class="titolo_edit" style="width:50%" type="password" name="key" id="key" title="Chiave personalizzata" rel="S;12;0;P"><br>
							</div>
							<input type="submit" class="submit_big" value="Recupera" id="invio">
						</form>
					</div>
					<div class="clear"></div>
					<?
				}
			if ($print_form) {
				$sql = "SELECT * FROM r_partecipanti_concorsi WHERE codice = :codice";
				$partecipante = $_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]];
				$ris_partecipante = $pdo->bindAndExec($sql,array(":codice"=>$partecipante["codice"]));
				$ris_partecipante = $ris_partecipante->fetch(PDO::FETCH_ASSOC);
				$partecipante["conferma"] = $ris_partecipante["conferma"];
				$partecipante["timestamp"] = $ris_partecipante["timestamp"];
				$_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]] = $partecipante;
				$submit = false;

				if (strtotime($fase_attiva["scadenza"]) > time()) $submit = true;


				$bind = array();
				$strsql = "SELECT b_fasi_concorsi_buste.* FROM b_fasi_concorsi_buste ORDER BY codice";
				$ris_buste = $pdo->bindAndExec($strsql,$bind);

				if ($ris_buste->rowCount() > 0) {
					?>
					<a target="_blank" class="submit_big" href="/concorsi/partecipa/downloadUID.php?codice_concorso=<?= $record_gara["codice"] ?>&codice_fase=<?= $fase_attiva["codice"] ?>">Scarica PDF Identificativo</a><br>
					<div id="conferma_partecipazione_bis"></div>
					<?
					$buste = array();
					$boxWidth= 98 / $ris_buste->rowCount();
					$first = true;
					while($busta = $ris_buste->fetch(PDO::FETCH_ASSOC)) {
						$buste[$busta["codice"]] = false;
						$checks = array();
						unset($memorizzata);
						$stato = "#F00";
						?>
						<div style="width:<?= $boxWidth ?>%; margin-right:4px; float:left;">
						<div class="box">
						<h3 style="text-align:center">
							<img src="/img/folder.png" width="75" alt="Cartella"><br><?= $busta["nome"] ?></h3>
								<?
								if (isset($partecipante)) {
									$sql_in = "SELECT * FROM b_buste_concorsi WHERE codice_gara = :codice_gara AND codice_busta = :codice_busta AND codice_partecipante = :codice_partecipante ";
									$ris_in = $pdo->bindAndExec($sql_in,array(":codice_busta"=>$busta["codice"],":codice_gara"=>$record_gara["codice"],":codice_partecipante"=>$partecipante["codice"]));
									if ($ris_in->rowCount()>0) {
										$rec_busta = $ris_in->fetch(PDO::FETCH_ASSOC);
										 $buste[$busta["codice"]] = true;
										 ?>
										<a class="pannello" href="#" onclick="$('#view_<?= $busta["codice"] ?>').toggle()">Visualizza Invio</a>
											<form class="box" id="view_<?= $busta["codice"] ?>" action="/concorsi/partecipa/open.php" target="_blank" rel="validate" method="post" style="display:none">
												 <input type="hidden" name="codice_gara" value="<?= $record_gara["codice"] ?>">
												 <input type="hidden" name="codice_busta" value="<?= $busta["codice"] ?>">
												 <? if ($rec_busta["aperto"]=="N") { ?>
													 <input type="password" class="titolo_edit" name="salt" title="Chiave Personalizzata" rel="S;12;0;P">
													<? } else { ?>
														<input type="hidden" name="salt" value="aperta">
												 <? } ?>
												 <input type="submit" class="submit_big" class="pannello" value="Scarica">
											 </form>
										 <?
									}
								}
								?>
								<? if ($submit) {
									if ($first || (isset($caricata))) {
										if (count($checks)==0 || in_array(false,$checks,true)===false) {
											if ($buste[$busta["codice"]]) {
												 $stato = "#0C0";
												 $label = "Sostituisci Documentazione";
												 $style = "";
												 if ($first) $caricata = true;
											 } else {
												 $style = "style='background-color:#09F'";
												 $label = "Carica la documentazione";
											 } ?>
											<a <?= $style ?> class="pannello" href="/concorsi/partecipa/submit.php?codice_busta=<?= $busta["codice"]?>&codice_gara=<?= $record_gara["codice"] ?>">
												<?= $label ?>
											</a>
										<? } ?>
										<div style="padding:3px; background-color:<?= $stato ?>; text-align:center; color:#fff"><strong>
											<?
												switch($stato) {
													case "#0C0":
														echo "Caricata";
														break;
													case "#F50":
														echo "In preparazione...";
														break;
													default:
														echo "Non inviata";
												}
											?></strong>
										</div>
									<? } else {
									?>
									<div style="text-align:center"><br>
										<strong>Carica busta precedente</strong>
									</div>
									<?
								} ?>
						<?
						$first = false;
						}
						?></div>
					</div><?
					}
					?>
					<div class="clear"></div>
					<?
					if ($submit) {
						if (in_array(false,$buste,true)===false) {
							if ($partecipante["conferma"]==0) {
								?>
								<div id="conferma_partecipazione">
									<div class="box">
										<h3 class="ui-state-error" style="text-align:center"><strong>ATTENZIONE INVIO DELLA PARTECIPAZIONE NON EFFETTUATO</strong><br>
											Per terminare la procedura di partecipazione &egrave; necessario cliccare sul pulsante <strong>INVIA LA PARTECIPAZIONE</strong>.<br>
											In assenza di errori restituiti dal sistema, l'Operatore Economico potr&agrave; scaricare un PDF contenente la conferma di avvenuta partecipazione al concorso.
										</h3><br>
										<form target="_self" action="/concorsi/partecipa/invia.php" method="post">
											<input type="hidden" name="codice_gara" value="<?= $record_gara["codice"] ?>">
										<input type="submit" style="font-size:18px" value="INVIA LA PARTECIPAZIONE" class="submit_big" onclick="$('#wait').show(); return true;"/>
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
									<li><h3><strong>Partecipazione inviata</strong></h3></li>
								</ul>
								<a class="submit_big" href="/concorsi/partecipa/downloadRicevuta.php?codice_concorso=<?= $record_gara["codice"] ?>&codice_fase=<?= $fase_attiva["codice"] ?>">Scarica PDF Ricevuta</a><br>
								<form action="/concorsi/partecipa/delete.php" rel="validate" method="post">
									<input type="hidden" name="codice_partecipante" value="<?= $partecipante["codice"] ?>">
									<input type="hidden" name="codice_gara" value="<?= $record_gara["codice"] ?>">
									<input type="hidden" name="codice_fase" value="<?= $fase_attiva["codice"] ?>">
									<button id="button_revoca_<? echo $partecipante["codice"] ?>" class="submit_big" style="background-color:#C30" title="Revoca partecipazione">Revoca partecipazione</button>
								</form>
								<?

							}
						} else {
							?>
							<h3 class="ui-state-error"><strong >E' necessario caricare tutte le buste prima di inviare la partecipazione</strong></h3>
							<?
						}
					} else {
						?><strong style="background-color:#F30; cursor:default" class="submit_big">CONCORSO SCADUTO</strong><?
						$bind = array(':codice' => $record_gara["codice"], ":codice_utente" => $partecipante["codice"]);
						$sql = "SELECT * FROM r_partecipanti_concorsi WHERE codice_gara = :codice AND codice = :codice_utente AND (conferma = TRUE OR conferma IS NULL)";
						$ris_partecipazione_confermata = $pdo->bindAndExec($sql,$bind);
						if ($ris_partecipazione_confermata->rowCount() == 0) {
							echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
						}
					}
				} else {
					echo "<h1>Impossibile continuare: Procedura Errata</h1>";
				}
			}
			} else {

				echo "<h1>Concorso inesistente o privilegi insufficienti</h1>";

			}
		} else {
			echo "<h1>Concorso inesistente o privilegi insufficienti</h1>";
		}
	} else {
		echo "<h1>Concorso inesistente o privilegi insufficienti</h1>";
	}
	include_once($root."/layout/bottom.php");
	?>
