<?
	ini_set('memory_limit', '-1');
	include_once("../../../config.php");
	$disable_alert_sessione = true;
	include_once($root."/layout/top.php");
	include_once($root."/inc/p7m.class.php");
	$public = true;
		if ((isset($_GET["cod"]) || isset($_POST["cod"]))&& is_operatore()) {
				if (isset($_POST["cod"])) $_GET["cod"] = $_POST["cod"];
				$codice = $_GET["cod"];
				$bind = array();
				$bind[":codice_rdo"] = $codice;
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql  = "SELECT b_rdo_ad.*, r_partecipanti.pec, r_partecipanti.codice_utente as codice_utente_mailer, r_partecipanti.partita_iva, r_partecipanti.ragione_sociale, r_rdo_ad.aperto, r_rdo_ad.timestamp_trasmissione, r_rdo_ad.codice_partecipante,r_rdo_ad.nome_file, b_gare.codice_pec, b_gare.stato, b_gare.oggetto, b_gare.public_key FROM
										r_rdo_ad JOIN b_rdo_ad ON r_rdo_ad.codice_rdo = b_rdo_ad.codice
										JOIN b_gare ON b_rdo_ad.codice_gara = b_gare.codice
										JOIN r_partecipanti ON r_rdo_ad.codice_partecipante = r_partecipanti.codice
										WHERE r_rdo_ad.codice = :codice_rdo AND r_rdo_ad.codice_utente = :codice_utente
										AND b_gare.annullata = 'N'
										AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente)
										AND (b_gare.pubblica = '2' OR b_gare.pubblica = '1') ";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$rdo = $risultato->fetch(PDO::FETCH_ASSOC);
					if ($rdo["cod_allegati"] != "" && preg_match("/^[0-9\;]+$/",$rdo["cod_allegati"])) {
						$allegati = explode(";",$rdo["cod_allegati"]);
						$str_allegati = ltrim(implode(",",$allegati),",");
						$sql = "SELECT * FROM b_allegati WHERE codice IN (" . $str_allegati . ") AND online = 'S'";
						$ris_allegati = $pdo->query($sql);
					}
				?>
<h1><?= traduci("RICHIESTA OFFERTA") ?></h1>
<div class="box"><h2><? echo $rdo["oggetto"] ?></h2></div>
<?
	$msg = "";
	if (isset($_POST["filechunk"]) && (strpos($_POST["filechunk"], "../")===false) && strtotime($rdo["data_scadenza"]) > time() && $rdo["stato"]=="3") {
		$data_busta = file_get_contents($config["chunk_folder"]."/".$_SESSION["codice_utente"]."/".$_POST["filechunk"]);
		$p7m = new P7Manager($config["chunk_folder"]."/".$_SESSION["codice_utente"]."/".$_POST["filechunk"]);
		$md5_file = $p7m->getHash('md5');
		if ($md5_file == $_POST["md5_file"]) {
			$msg .= "<li>File integro - HASH MD5: " . $md5_file . "</li>";
			$esito = $p7m->checkSignatures();
			if ($esito == "Verification successful") {
				$msg .= "<li>Firma formalmente valida";
				$certificati = $p7m->extractSignatures();
				$msg .= "<ul class=\"firme\">";
					foreach ($certificati AS $esito) {
						$data = openssl_x509_parse($esito,false);
						$validFrom = date('d-m-Y H:i:s', $data['validFrom_time_t']);
						$validTo = date('d-m-Y H:i:s', $data['validTo_time_t']);
						$msg .=  "<li>";
						if (isset($data["subject"]["commonName"])) $msg .= "<h1>" . $data["subject"]["commonName"] . "</h1>";
						if (isset($data["subject"]["organizationName"])) $msg .= "<strong>" . $data["subject"]["organizationName"] . "</strong><br>";
						if (isset($data["subject"]["title"])) $msg .=  $data["subject"]["title"] . "<br>";
						if (isset($data["issuer"]["organizationName"])) $msg .=  "<br>Emesso da:<strong>" . $data["issuer"]["organizationName"] . "</strong>";
						$msg .=  "<br>Valido da:<strong> " . $validFrom . "</strong><br>A <strong>" . $validTo . "</strong>";
						$msg .=  "</li>";
					}
					$msg .= "</ul>";
					$msg .= "</li>";
					$path = "/" . $rdo["codice_gara"] . "/rdo/".$rdo["codice"]."/";
					$upload = array();
					$upload["codice"] = $codice;
					$upload["md5"] = $md5_file;
					$upload["aperto"] = "N";
					$upload["codice_allegato"] = 0;
					$upload["timestamp_trasmissione"] = date('Y-m-d H:i:s');
					$errore = false;

					if ($rdo["data_apertura"] > 0) {
						$upload["nome_file"] = $rdo["codice_partecipante"] . "_" . $codice;
						$dest = $config["doc_folder"];
						$enc_busta = openssl_encrypt($data_busta,$config["crypt_alg"],$_POST["salt"],OPENSSL_RAW_DATA,$config["enc_salt"]);
						if ($enc_busta !== FALSE) {
							$put_file = $enc_busta;
							$public = openssl_pkey_get_public(trim($rdo["public_key"]));
							if (openssl_public_encrypt($_POST["salt"],$upload["salt"],$public)) {
								$msg .= "<li>Criptazione effettuata con successo</li>";
							} else {
								$errore = true;
								$error_msg = '<h3 class="ui-state-error">' . traduci("errore nella criptazione del file") .' - 1</h3>';
							}
						} else {
							$errore = true;
							$error_msg = '<h3 class="ui-state-error">' . traduci("errore nella criptazione del file") .' - 2</h3>';
						}
					} else {
						$dest = $config["arch_folder"];

						$file_info = new finfo(FILEINFO_MIME_TYPE);
						$mime_type = $file_info->buffer($data_busta);
						$estensione = "p7m";
						if (strpos($mime_type, "pdf")!==false) $estensione = "pdf";

						$allegato = array();

						$put_file = $data_busta;

						$upload["nome_file"] = getRealNameFromData($put_file);
						$upload["salt"] = "";
						$upload["aperto"] = "S";

						$allegato["codice_gara"] = $rdo["codice_gara"];
						$allegato["codice_ente"] = $_SESSION["ente"]["codice"];
						$allegato["cartella"] = "rdo/".$rdo["codice"];
						$allegato["nome_file"] = $rdo["codice_partecipante"] . "_" . $codice.".".$estensione;
						$allegato["riferimento"] = $upload["nome_file"];
						$allegato["titolo"] = "Offerta " . $rdo["partita_iva"] . " - " . date('Y-m-d h-i-s');
						$allegato["online"] = "N";

						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "b_allegati";
						$salva->operazione = "INSERT";
						$salva->oggetto = $allegato;
						$upload["codice_allegato"] = $salva->save();
					}
					if (!$errore) {
						if (!is_dir($dest . $path)) {
							mkdir($dest . $path,0770,true);
						}
						$path_file = $dest.$path.$upload["nome_file"];
						file_put_contents($path_file,$put_file);
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "r_rdo_ad";
						$salva->operazione = "UPDATE";
						$salva->oggetto = $upload;
						$codice_upload = $salva->save();
						if ($codice_upload != false) {
							$msg .= "<li>" . traduci("salvataggio riuscito con successo")  . "</li>";

								$oggetto = "Conferma di ricezione offerta: " . $rdo["oggetto"];
								$corpo = "L'operatore economico ". $rdo["ragione_sociale"] . ",  ha trasmesso l'offerta:<br>";
								$corpo.= "<br><strong>" . $rdo["titolo"] . "</strong><br><br>";
								$corpo.= "<br>" . $rdo["richiesta"] . "<br><br>";
								$corpo.= "Hash MD5 della trasmissione: <ul>";
								$corpo.="<li><strong>" . $upload["md5"] . "</strong></li></ul>";
								$corpo.= "Distinti Saluti<br><br>";

								$mailer = new Communicator();
								$mailer->oggetto = $oggetto;
								$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
								$mailer->codice_pec = $rdo["codice_pec"];
								$mailer->comunicazione = true;
								$mailer->coda = false;
								$mailer->sezione = "gara";
								$mailer->codice_gara = $rdo["codice_gara"];
								$mailer->destinatari = $rdo["codice_utente_mailer"];
								$esito = $mailer->send();

								$pec_conferma = getIndirizzoConferma($rdo["codice_pec"]);

								$mailer = new Communicator();
								$mailer->oggetto = $oggetto;
								$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
								$mailer->codice_pec = -1;
								$mailer->comunicazione = false;
								$mailer->coda = false;
								$mailer->sezione = "gara";
								$mailer->codice_gara = $rdo["codice_gara"];
								$mailer->destinatari = $pec_conferma;
								$mailer->type = 'comunicazione-gara';
								$esito = $mailer->send();

								$sql = "UPDATE r_partecipanti SET conferma = TRUE WHERE codice_utente = :codice_utente AND codice_gara = :codice_gara ";
								$ris_update = $pdo->bindAndExec($sql,array(":codice_utente"=>$_SESSION["codice_utente"],":codice_gara"=>$rdo["codice_gara"]));
						?>
							<ul class="success">
									<? echo $msg ?>
									<li><?= traduci("salvataggio riuscito con successo") ?></li>
										<br><?= traduci("partecipazione-conferma-pec") ?>
									</li>
							</ul>
							<?
						} else {
							?>
								<h3 class="ui-state-error"><?= traduci('errore-salvataggio') ?></h3>
							<?
						}
					} else {
						echo $error_msg;
					}
				} else {
					?>
						<h3 class="ui-state-error"><?= traduci("Firma del file non valida") ?></h3>
					<?
				}
			} else {
				?>
				<h3 class="ui-state-error"><?= traduci("Errore nella procedura di upload") ?></h3>
				<?
			}
			unlink($config["chunk_folder"]."/".$_SESSION["codice_utente"]."/".$_POST["filechunk"]);
		} else {
		?>
				<script type="text/javascript" src="/js/spark-md5.min.js"></script>
        <script type="text/javascript" src="/js/resumable.js"></script>
				<script type="text/javascript" src="resumable-uploader.js"></script>
	<div class="box">
				<table width="100%">
					<tr>
						<td colspan="4">
								<strong><?= $rdo["titolo"] ?></strong>
						</td>
					</tr>

						<tr>
							<td class="etichetta"><?=traduci("Scadenza")?>:</td>
							<td>
								<strong><?= mysql2datetime($rdo["data_scadenza"]) ?></strong>
							</td>
							<?
								if ($rdo["data_apertura"] > 0) {
									?>
									<td class="etichetta"><?=traduci("Apertura")?>:</td>
									<td>
										<strong><?= mysql2datetime($rdo["data_apertura"]) ?></strong>
									</td>
									<?
								}
								?>
						</tr>
						<tr>
							<td colspan="4">
									<?= $rdo["richiesta"] ?>
							</td>
						</tr>
					</table>
				</div>

					<?
					if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
							$public = true;
							?>
                            <div class="box"><h2><?= traduci("Allegati") ?></h2>
                            <table width="100%" id="tab_allegati">
                            <?
                       			while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
									include($root."/allegati/tr_allegati.php");
								}
							?>
                        </table>
                        </div>
                        <?
						}
						$titolo = "Invia offerta";
						if ($rdo["timestamp_trasmissione"] > 0) {
							$titolo = "Sostituisci offerta";
							?>
							<div class="box">
								<h2 style="color:#0C0"><?= traduci("Trasmissione effettuata") ?>: <?= mysql2completedate($rdo["timestamp_trasmissione"]) ?></h2>
							</div>
							<?
						}
						if (strtotime($rdo["data_scadenza"]) > time() && $rdo["stato"]=="3") {
						?>
						<br>
						<br>
						<h1><?= $titolo ?></h1>
				<form action="modulo.php" id="upload_busta" method="post" target="_self" rel="validate">
					<div style="text-align:center">
						<input type="hidden" id="cod" name="cod" value="<?= $codice ?>">
	        	<div style="float:left; width:25%">
							<div><strong>STEP 1</strong></div>
							<div><img src="/gare/telematica2.0/img/step1.png" alt="Step 1" style="max-width:200px" width="100%"></div>
							<div><?= traduci('partecipazione-step-1') ?></div>
						</div>
						<div style="float:left; width:25%">
							<div><strong>STEP 2</strong></div>
							<div><img src="/gare/telematica2.0/img/step2.png" alt="Step 2" style="max-width:200px" width="100%"></div>
							<div><?= traduci('partecipazione-step-2') ?></div>
						</div>
						<div style="float:left; width:25%">
							<div><strong>STEP 3</strong></div>
							<div><img src="/gare/telematica2.0/img/step3.png" alt="Step 3" style="max-width:200px" width="100%"></div>
							<div><?= traduci('partecipazione-step-3') ?></div>
						</div>
						<div style="float:left; width:25%">
							<div><strong>STEP 4</strong></div>
							<div><img src="/gare/telematica2.0/img/step4.png" alt="Step 4" style="max-width:200px" width="100%"></div>
							<div><?= traduci("Seleziona il file") ?>, <? if ($rdo["data_apertura"]>0) { ?> - <?= traduci("inserire una chiave personalizzata") ?> <? } ?> - <?= traduci("cliccare su INVIA") ?></div>
						</div>
						<div class="clear"></div>
				</div>
				<div class="box">
					<strong><?= traduci('nota-firmatari') ?></strong>
				</div><br>
      	<input type="hidden" name="md5_file" id="md5_file" title="File" rel="S;0;0;A">
        <input type="hidden" id="filechunk" name="filechunk">
          <div class="scegli_file"><span class="fa fa-folder-open"></span><br><?= traduci("Scegli il file") ?></div>
          <script>
  					var uploader = (function($){
	  					return (new ResumableUploader($('.scegli_file')));
						})(jQuery);
					</script>
        <div id="progress_bar" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
				<?
					if ($rdo["data_apertura"]>0) { ?>
						<div class="modulo_partecipazione">
							<?= traduci("Chiave personalizzata") ?>*<br>
							<input class="titolo_edit" style="width:25%" type="password" name="salt" id="salt" title="<?= traduci("Chiave personalizzata") ?>" rel="S;12;0;P"><br>
							<?= traduci('Minimo 12 caratteri') ?><br><br>
							<?= traduci("Ripeti") ?> <?= traduci("Chiave personalizzata") ?>*<br>
							<input class="titolo_edit" style="width:25%" type="password" id="repeat-salt" title="<?= traduci("Chiave personalizzata") ?>" rel="S;12;0;P;salt;="><br><br>
							<span style="font-weight:normal"><?= traduci('memo-chiave') ?></span>
						</div>
				<? } ?>
				<input type="submit" style="display: none;" class="submit_big" value="<?= traduci("Invia") ?>" id="invio" onClick="if (confirm('<?= traduci("msg-conferma-revoca") ?>')) { $('#wait').show(); uploader.resumable.upload(); } return false;">
				</form>
				<?
			} else {
				if ($rdo["stato"]=="3") {
					?>
					<div class="box">
						<h2 style="color:#F00"><?= traduci("Scaduta") ?></h2>
					</div>
					<? } else {
				?>
				<div class="box">
					<h2 style="color:#F00"><?= traduci("Aggiudicazione definitiva") ?></h2>
				</div>
				<?
				}
			}
			}
			?>
			<a href="/gare/rdo_ad/view.php?cod=<?= $rdo["codice_gara"] ?>" class="ritorna_button submit_big" style="background-color:#999;"><?= traduci("Ritorna al pannello") ?></a>
			<?
		} else {
			echo "<h1>". traduci('impossibile accedere') . " - ERROR 0</h1>";
		}
	} else {
		echo "<h1>". traduci('impossibile accedere') . " - ERROR 1</h1>";
	}
	include_once($root."/layout/bottom.php");
?>
