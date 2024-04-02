<?
	include_once("../../../config.php");
	$disable_alert_sessione = true;
	include_once($root."/layout/top.php");
	$public = true;
	if (!empty($_GET["codice_gara"]) && isset($_GET["codice_lotto"]) && !empty($_GET["codice_busta"]) && !empty($_GET["busta_originale"]) && is_operatore()) {

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

		if ($risultato->rowCount() > 0) {
			$bind = array();
			$bind[":codice"] = $codice_gara;
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
			?>
			<h1><?= traduci("CARICA DOCUMENTAZIONE") ?> - ID <? echo $record_gara["id"] ?></h1>
			<h2><? echo $record_gara["oggetto"] ?></h2>
			<?
			$bind = array();
			$bind[":codice_gara"] = $record_gara["codice"];
			$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara ORDER BY codice";
			$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
			if ($ris_lotti->rowCount() > 0) {
				$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto ORDER BY codice";
				$bind[":codice_lotto"] = $codice_lotto;
				$ris_check_lotti = $pdo->bindAndExec($sql_lotti,$bind);
				if ($ris_check_lotti->rowCount() > 0) {
					$lotto = $ris_check_lotti->fetch(PDO::FETCH_ASSOC);
				}
			} else {
				$codice_lotto = 0;
			}
			$submit = false;

			if (isset($lotto)) {
				$codice_lotto = $lotto["codice"];
				echo "<div class=\"box\"><h3>" . $lotto["oggetto"] . "</h3>";
				echo $lotto["descrizione"]."</div>";
			}
			
			$bind = array();
			$bind[":codice_gara"] = $record_gara["codice"];
			$bind[":codice_lotto"] = $codice_lotto;
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
			$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_utente = :codice_utente AND codice_capogruppo = 0 ";
			$ris = $pdo->bindAndExec($sql,$bind);
			if ($ris->rowCount() > 0) {
				$partecipante = $ris->fetch(PDO::FETCH_ASSOC);
				$sql_in = "SELECT b_buste.*, b_criteri_buste.nome AS nome_busta FROM b_buste 
						   JOIN b_criteri_buste ON b_buste.codice_busta = b_criteri_buste.codice 
						   WHERE b_buste.codice = :codice_originale AND b_buste.codice_gara = :codice_gara AND b_buste.codice_lotto = :codice_lotto AND b_buste.codice_busta = :codice_busta AND b_buste.codice_partecipante = :codice_partecipante ";
				$ris_in = $pdo->bindAndExec($sql_in,array(":codice_originale"=>$_GET["busta_originale"],":codice_busta"=>$_GET["codice_busta"],":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto,":codice_partecipante"=>$partecipante["codice"]));
				if ($ris_in->rowCount()>0) {
					$rec_busta = $ris_in->fetch(PDO::FETCH_ASSOC);
					$emendabile = checkBustaEmendabile($rec_busta);
					if ($emendabile) {
					?>
					<h2 style="text-transform:uppercase"><strong><?= traduci($rec_busta["nome_busta"]) ?></strong></h2>
					<script type="text/javascript" src="/js/spark-md5.min.js"></script>
					<script type="text/javascript" src="/js/resumable.js"></script>
					<script type="text/javascript" src="resumable-uploader.js"></script>

					<div id="modulo_partecipazione">
						<form action="upload-emendamento.php" id="upload_busta" method="post" target="_self" rel="validate">
								<input type="hidden" name="codice_gara" value="<? echo $record_gara["codice"] ?>">
								<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto ?>">
								<input type="hidden" name="codice_busta" value="<? echo $rec_busta["codice_busta"] ?>">
								<input type="hidden" name="busta_originale" value="<? echo $rec_busta["codice"] ?>">
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
										<div><?= traduci('partecipazione-step-1') ?></div>
									</div>
									<div style="float:left; width:<?= $perc ?>">
										<div><strong>STEP <? $step++; echo $step ?></strong></div>
										<div><img src="img/step2.png" alt="Step 2" style="max-width:200px" width="100%"></div>
										<div><?= traduci('partecipazione-step-2') ?></div>
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
									<div class="scegli_file"><img src="/img/folder.png" style="vertical-align:middle"><br><?= traduci("Seleziona il file") ?> - <? echo traduci($rec_busta["nome_busta"]) ?></div>
									<script>
										var uploader = (function($){
										return (new ResumableUploader($('.scegli_file')));
										})(jQuery);
									</script>
									<div id="progress_bar" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
									<div class="modulo_partecipazione">
										<?= traduci("Descrizione del correttivo") ?>*<br>
										<textarea class="titolo_edit" id="descrizione" name="descrizione" rows="5" rel="S;0;0;A" title="Descrizione"></textarea>
										<small><?= traduci("Fornire una descrizione delle modifiche apportate") ?></small>
									</div>
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
						<a class="submit_big" style="background-color:#444" href="/gare/telematica2.0/modulo.php?cod=<?= $codice_gara ?><?= ($codice_lotto > 0) ? "&codice_lotto=".$codice_lotto : "" ?>"><?= traduci("Ritorna al pannello") ?></a>
						<?
					} else {
						echo "<h1>" . traduci("Busta non emenedabile") . "</h1>";
					}
				} else {
					echo "<h1>" . traduci("Impossibile accedere") . ": 3</h1>";
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
