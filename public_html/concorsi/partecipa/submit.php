<?
	include_once("../../../config.php");
	$disable_alert_sessione = true;
	include_once($root."/layout/top.php");
	$public = true;
	if (isset($_GET["codice_gara"]) && isset($_GET["codice_busta"]) && is_operatore()) {

		$codice_gara = $_GET["codice_gara"];

		$bind = array();
		$bind[":codice"] = $codice_gara;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql  = "SELECT b_concorsi.* FROM b_concorsi
								WHERE b_concorsi.codice = :codice ";
		$strsql .= "AND b_concorsi.annullata = 'N' ";
		$strsql .= "AND codice_gestore = :codice_ente ";
		$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
		$risultato = $pdo->bindAndExec($strsql,$bind);

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

		if ($accedi && !empty($_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]]["salt"])) {
			$partecipante = $_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]];
			$print_form = true;
			?>
			<h1>CARICA DOCUMENTAZIONE - ID <? echo $record_gara["id"] ?></h1>
			<h2><? echo $record_gara["oggetto"] ?> - Fase: <?= $fase_attiva["oggetto"] ?></h2>
			<?

			if (strtotime($fase_attiva["scadenza"]) > time()) {
				$strsql = "SELECT b_fasi_concorsi_buste.* FROM b_fasi_concorsi_buste
									 WHERE codice = :codice_busta LIMIT 0,1";
				$ris_buste = $pdo->bindAndExec($strsql,array(":codice_busta"=>$_GET["codice_busta"]));
				if ($ris_buste->rowCount() > 0) {
					$busta = $ris_buste->fetch(PDO::FETCH_ASSOC);
					$check_file = true;
					$check_hash = false;
					if ($busta["tecnica"] == "N") {
						$bind = array();
						$bind[":codice_gara"] = $record_gara["codice"];
						$bind[":codice_partecipante"] = $partecipante["codice"];
						$strsql = "SELECT b_buste_concorsi.* FROM b_buste_concorsi JOIN b_fasi_concorsi_buste ON b_buste_concorsi.codice_busta = b_fasi_concorsi_buste.codice
											 WHERE b_buste_concorsi.codice_gara = :codice_gara
											 AND b_buste_concorsi.codice_partecipante = :codice_partecipante
											 AND b_fasi_concorsi_buste.tecnica = 'S' ";
						$ris_checkFile = $pdo->bindAndExec($strsql,$bind);
						if (!isset($ris_checkFile) || (isset($ris_checkFile) && $ris_checkFile->rowCount()==0)) {
							$check_hash = true;
							$check_file = false;
							$check_tecnica = false;
						}

					}

					if ($check_file) {
						?>
						<h2 style="text-transform:uppercase"><strong><?= $busta["nome"] ?></strong></h2>
						<script type="text/javascript" src="/js/spark-md5.min.js"></script>
						<script type="text/javascript" src="/js/resumable.js"></script>
						<script type="text/javascript" src="resumable-uploader.js"></script>

			      <div id="modulo_partecipazione">
							<form action="upload.php" id="upload_busta" method="post" target="_self" rel="validate">
								 <input type="hidden" name="codice_gara" value="<? echo $record_gara["codice"] ?>">
								 <input type="hidden" name="codice_busta" value="<? echo $busta["codice"] ?>">
								 <br>
									<div style="text-align:center" class="box">
										<h2 style="text-align:center"><strong>Guida all'invio della documentazione di partecipazione</strong></h2><br>
										<?
										$perc = "25%";
										$step = 0;
										if ($busta["tecnica"]=="N") { ?>
										<div style="float:left; width:<?= $perc ?>">
											<div><strong>STEP <? $step++; echo $step ?></strong></div>
											<div><img src="img/step1.png" alt="Step 1" style="max-width:200px" width="100%"></div>
											<div>
												Firmare digitalmente tutti i files costituenti la documentazione amministrativa e la stessa offerta Tecnica caricata nella fase precedente.<br><br>
												<strong>N.B. Qualora l&rsquo;Offerta Tecnica sia costituita da pi&ugrave; files, non si dovranno firmare digitalmente i singoli files ma esclusivamente la cartella compressa costituente l&rsquo;Offerta Tecnica.</strong>
											</div>
										</div>
										<div style="float:left; width:<?= $perc ?>">
											<div><strong>STEP <? $step++; echo $step ?></strong></div>
											<div><img src="img/step2.png" alt="Step 2" style="max-width:200px" width="100%"></div>
											<div>Creare un archivio compresso (di tipo .zip, .rar oppure 7z) contenente la documentazione amministrativa e la stessa offerta Tecnica caricata nella fase precedente</div>
										</div>
										<div style="float:left; width:<?= $perc ?>">
											<div><strong>STEP <? $step++; echo $step ?></strong></div>
											<div><img src="img/step3.png" alt="Step 3" style="max-width:200px" width="100%"></div>
											<div>Firmare digitalmente in formato <strong>P7M (CAdES)</strong> l'archivio ZIP creato.</div>
										</div>
										<div style="float:left; width:<?= $perc ?>">
											<div><strong>STEP <? $step++; echo $step ?></strong></div>
											<div><img src="img/step4.png" alt="Step 4" style="max-width:200px" width="100%"></div>
											<div>Selezionare l&rsquo;archivio compresso firmato digitalmente, inserire una Chiave personalizzata di almeno 12 caratteri e cliccare su Carica Busta.</div>
										</div>
									<? } else { ?>
										<div class="ui-state-error padding">
											<h1 style="text-align:center"><strong>ATTENZIONE</strong></h1>
											<h3 style="text-align:center">Al fine di tutelare la segretezza della partecipazione non caricare files firmati digitalmente o altre informazioni che potrebbero portare all'indentificazione del/i soggetto/i partecipante/i.<br>
											<br>Durante il caricamento della <strong>Busta Amministrativa</strong> sar&agrave; richiesto di inserire, unitamente al resto della documentazione richiesta, una copia firmata digitalmente della busta tecnica.</h3>
										</div>
									<? } ?>
										<div class="clear"></div>
									</div><br>
									<h1><strong>SELEZIONA IL FILE</strong></h1><BR>
									<input type="hidden" name="md5_file" id="md5_file" title="File" rel="S;0;0;A">
										<input type="hidden" id="filechunk" name="filechunk">
										<div class="scegli_file"><img src="/img/folder.png" style="vertical-align:middle"><br>Seleziona il file - <? echo $busta["nome"] ?></div>
										<script>
											var uploader = (function($){
											return (new ResumableUploader($('.scegli_file'),<?= ($busta["tecnica"]=="S") ? "false" : "true"; ?>));
											})(jQuery);
										</script>
										<div id="progress_bar" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
										<div class="modulo_partecipazione">
											Chiave personalizzata*<br>
											<input class="titolo_edit" style="width:25%" type="password" name="salt" id="salt" title="Chiave" rel="S;12;0;P"><br>
											Minimo 12 caratteri<br><br>
											Ripeti Chiave*<br>
											<input class="titolo_edit" style="width:25%" type="password" id="repeat-salt" title="Chiave" rel="S;12;0;P;salt;="><br><br>
											<span style="font-weight:normal">Si prega di appuntare e conservare la chiave personalizzata, eccezionalemente potrebbe essere richiesta dalla Stazione Appaltante per accedere ai files inviati</span>
										</div>
										<input type="submit" class="submit_big" value="Carica busta" id="invio" onClick="if (confirm('Questa operazione revocher&agrave; eventuali istanze precedenti. Vuoi continuare?')) { $('#wait').show(); uploader.resumable.upload(); } return false;">
									</form>
								</div>
						<?
					} else {
						echo "<h3 class='ui-state-error'>Impossibile continuare: Prima di proseguire è necessario generare e firmare digitalmente i seguenti file di offerta: <ul>";
						if (isset($check_tecnica)) echo "<li>Busta Tecnica</li>";
						echo "</ul></h3>";
					}
					?>
					<a class="submit_big" style="background-color:#444" href="/concorsi/partecipa/modulo.php?cod=<?= $codice_gara ?>">Ritorna al pannello</a>
					<?
				} else {
					echo "<h1>Impossibile continuare: Errore nella procedura</h1>";
				}
			} else {
				echo "<h1>Impossibile continuare: Termini scaduti</h1>";
			}
			} else {
				echo "<h1>Impossibile continuare: Privilegi insufficienti</h1>";
			}
		} else {
			echo "<h1>Gara inesistente o privilegi insufficienti</h1>";
		}
	} else {
		echo "<h1>Gara inesistente o privilegi insufficienti</h1>";
	}
	include_once($root."/layout/bottom.php");
	?>
