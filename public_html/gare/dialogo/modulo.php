<?
	ini_set('memory_limit', '-1');
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	include_once($root."/inc/p7m.class.php");
	$public = true;
		if ((isset($_GET["cod"]) || isset($_POST["cod"]))&& is_operatore()) {
				if (isset($_POST["cod"])) $_GET["cod"] = $_POST["cod"];
				$codice = $_GET["cod"];
				$bind = array();
				$bind[":codice_dialogo"] = $codice;
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql  = "SELECT b_dialogo.*, r_partecipanti.pec, r_partecipanti.codice_utente as codice_utente_mailer, r_partecipanti.partita_iva, r_partecipanti.ragione_sociale, r_dialogo.aperto, r_dialogo.timestamp_trasmissione, r_dialogo.codice_partecipante,r_dialogo.nome_file, b_gare.codice_pec, b_gare.oggetto, b_gare.public_key FROM
										r_dialogo JOIN b_dialogo ON r_dialogo.codice_dialogo = b_dialogo.codice
										JOIN b_gare ON b_dialogo.codice_gara = b_gare.codice
										JOIN r_partecipanti ON r_dialogo.codice_partecipante = r_partecipanti.codice

										WHERE r_dialogo.codice = :codice_dialogo AND r_dialogo.codice_utente = :codice_utente
										AND b_gare.annullata = 'N'
										AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente)
										AND (b_gare.pubblica = '2' OR b_gare.pubblica = '1') ";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$dialogo = $risultato->fetch(PDO::FETCH_ASSOC);
					if ($dialogo["cod_allegati"] != "" && preg_match("/^[0-9\;]+$/",$dialogo["cod_allegati"])) {
								$allegati = explode(";",$dialogo["cod_allegati"]);
								$str_allegati = ltrim(implode(",",$allegati),",");
								$sql = "SELECT * FROM b_allegati WHERE codice IN (" . $str_allegati . ") AND online = 'S'";
								$ris_allegati = $pdo->query($sql);
					}
				?>
<h1>DIALOGO COMPETITIVO</h1>
<div class="box"><h2><? echo $dialogo["oggetto"] ?></h2></div>
<?
	$msg = "";
	if (isset($_POST["filechunk"])) {
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
					$path = "/" . $dialogo["codice_gara"] . "/dialogo/".$dialogo["codice"]."/";
					$upload = array();
					$upload["codice"] = $codice;
					$upload["md5"] = $md5_file;
					$upload["aperto"] = "N";
					$upload["codice_allegato"] = 0;
					$upload["timestamp_trasmissione"] = date('Y-m-d H:i:s');
					$errore = false;

					if ($dialogo["data_apertura"] > 0) {
						$upload["nome_file"] = $dialogo["codice_partecipante"] . "_" . $codice;
						$dest = $config["doc_folder"];
						$enc_busta = openssl_encrypt($data_busta,$config["crypt_alg"],$_POST["salt"],OPENSSL_RAW_DATA,$config["enc_salt"]);
						if ($enc_busta !== FALSE) {
							$put_file = $enc_busta;
							$public = openssl_pkey_get_public(trim($dialogo["public_key"]));
							if (openssl_public_encrypt($_POST["salt"],$upload["salt"],$public)) {
								$msg .= "<li>Criptazione effettuata con successo</li>";
							} else {
								$errore = true;
								$error_msg = '<h3 class="ui-state-error">Errore nella crittazione della chiave</h3>';
							}
						} else {
							$errore = true;
							$error_msg = '<h3 class="ui-state-error">Errore nella crittazione del file</h3>';
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

						$allegato["codice_gara"] = $dialogo["codice_gara"];
						$allegato["codice_ente"] = $_SESSION["ente"]["codice"];
						$allegato["cartella"] = "dialogo/".$dialogo["codice"];
						$allegato["nome_file"] = $dialogo["codice_partecipante"] . "_" . $codice.".".$estensione;
						$allegato["riferimento"] = $upload["nome_file"];
						$allegato["titolo"] = "Risposta " . $dialogo["partita_iva"] . " - " . date('Y-m-d h-i-s');
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
						$salva->nome_tabella = "r_dialogo";
						$salva->operazione = "UPDATE";
						$salva->oggetto = $upload;
						$codice_upload = $salva->save();
						if ($codice_upload != false) {
							$msg .= "<li>Salvataggio effettuato con successo</li>";

								$oggetto = "Conferma di invo documentazione dialogo competitivo: " . $dialogo["oggetto"];
								$corpo = "L'operatore economico ". $dialogo["ragione_sociale"] . ",  ha trasmesso i files relativi al dialogo:<br>";
								$corpo.= "<br><strong>" . $dialogo["titolo"] . "</strong><br><br>";
								$corpo.= "<br>" . $dialogo["richiesta"] . "<br><br>";
								$corpo.= "Hash MD5 della trasmissione: <ul>";
								$corpo.="<li><strong>" . $upload["md5"] . "</strong></li></ul>";
								$corpo.= "Distinti Saluti<br><br>";

								$mailer = new Communicator();
								$mailer->oggetto = $oggetto;
								$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
								$mailer->codice_pec = $dialogo["codice_pec"];
								$mailer->comunicazione = true;
								$mailer->coda = false;
								$mailer->sezione = "gara";
								$mailer->codice_gara = $dialogo["codice_gara"];
								$mailer->destinatari = $dialogo["codice_utente_mailer"];
								$esito = $mailer->send();

								$pec_conferma = getIndirizzoConferma($dialogo["codice_pec"]);

								$mailer = new Communicator();
								$mailer->oggetto = $oggetto;
								$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
								$mailer->codice_pec = -1;
								$mailer->comunicazione = false;
								$mailer->coda = false;
								$mailer->sezione = "gara";
								$mailer->codice_gara = $dialogo["codice_gara"];
								$mailer->destinatari = $pec_conferma;
								$mailer->type = 'comunicazione-dialogo';
								$esito = $mailer->send();

						?>
							<ul class="success">
									<? echo $msg ?>
									<li>La trasmissione &egrave; stata ricevuta con successo.
										<br>Un messaggio di posta elettronica certificata &egrave; stato inviato per confermare l'operazione
									</li>
							</ul>
							<?
						} else {
							?>
								<h3 class="ui-state-error">Errore nel salvataggio dei dati</h3>
							<?
						}
					} else {
						echo $error_msg;
					}
				} else {
					?>
						<h3 class="ui-state-error">Firma non valida</h3>
					<?
				}
			} else {
				?>
				<h3 class="ui-state-error">Errore nell'upload - Il file non è integro</h3>
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
								<strong><?= $dialogo["titolo"] ?></strong>
						</td>
					</tr>

						<tr>
							<td class="etichetta">Scadenza:</td>
							<td>
								<strong><?= mysql2datetime($dialogo["data_scadenza"]) ?></strong>
							</td>
							<?
								if ($dialogo["data_apertura"] > 0) {
									?>
									<td class="etichetta">Apertura:</td>
									<td>
										<strong><?= mysql2datetime($dialogo["data_apertura"]) ?></strong>
									</td>
									<?
								}
								?>
						</tr>
						<tr>
							<td colspan="4">
									<?= $dialogo["richiesta"] ?>
							</td>
						</tr>
					</table>
				</div>

					<?
					if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
							$public = true;
							?>
                            <div class="box"><h2>Allegati</h2>
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
						$titolo = "Invia dialogo";
						if ($dialogo["timestamp_trasmissione"] > 0) {
							$titolo = "Sostituisci dialogo";
							?>
							<div class="box">
								<h2 style="color:#0C0">Trasmissione effettuata: <?= mysql2completedate($dialogo["timestamp_trasmissione"]) ?></h2>
							</div>
							<?
						}
						if (strtotime($dialogo["data_scadenza"]) > time()) {
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
							<div>Firmare digitalmente tutta la documentazione da trasmettere</div>
						</div>
						<div style="float:left; width:25%">
							<div><strong>STEP 2</strong></div>
							<div><img src="/gare/telematica2.0/img/step2.png" alt="Step 2" style="max-width:200px" width="100%"></div>
							<div>Inserire i files firmati in un archivio compresso di tipo ZIP</div>
						</div>
						<div style="float:left; width:25%">
							<div><strong>STEP 3</strong></div>
							<div><img src="/gare/telematica2.0/img/step3.png" alt="Step 3" style="max-width:200px" width="100%"></div>
							<div>Firmare digitalmente l'archivio ZIP</div>
						</div>
						<div style="float:left; width:25%">
							<div><strong>STEP 4</strong></div>
							<div><img src="/gare/telematica2.0/img/step4.png" alt="Step 4" style="max-width:200px" width="100%"></div>
							<div>Selezionare il file,  <? if ($dialogo["data_apertura"]>0) { ?>inserire una chiave personalizzata <? } ?>e cliccare su INVIA</div>
						</div>
						<div class="clear"></div>
				</div>
				<div class="box">
					<strong>N.B.: Le indicazioni riguardo i firmatari della documentazione sono analoghe a quanto previsto dalle tradizionali gare cartacee.</strong>
				</div><br>
      	<input type="hidden" name="md5_file" id="md5_file" title="File" rel="S;0;0;A">
        <input type="hidden" id="filechunk" name="filechunk">
          <div class="scegli_file"><img src="/img/folder.png" style="vertical-align:middle"><br>Scegli il file</div>
          <script>
  					var uploader = (function($){
	  					return (new ResumableUploader($('.scegli_file')));
						})(jQuery);
					</script>
        <div id="progress_bar" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
				<?
					if ($dialogo["data_apertura"]>0) { ?>
					<div class="modulo_partecipazione">
						Chiave personalizzata*<br>
						<input class="titolo_edit" style="width:25%" type="password" name="salt" id="salt" title="Chiave" rel="S;12;0;P"><br>
						Minimo 12 caratteri<br><br>
						Ripeti Chiave*<br>
						<input class="titolo_edit" style="width:25%" type="password" id="repeat-salt" title="Chiave" rel="S;12;0;P;salt;="><br><br>
						<span style="font-weight:normal">Si prega di appuntare e conservare la chiave personalizzata, eccezionalemente potrebbe essere richiesta dalla Stazione Appaltante per accedere ai files inviati</span>
					</div>
				<? } ?>
				<input type="submit" style="display: none;" class="submit_big" value="Invia" id="invio" onClick="if (confirm('Questa operazione canceller&agrave; eventuali istanze precedenti. Vuoi continuare?')) { $('#wait').show(); uploader.resumable.upload(); } return false;">
				</form>
				<?
			} else {
				?>
				<div class="box">
					<h2 style="color:#F00">Scaduta</h2>
				</div>
				<?
			}
			}
			?>
			<a href="/gare/dialogo/view.php?cod=<?= $dialogo["codice_gara"] ?>" class="ritorna_button submit_big" style="background-color:#999;">Ritorna</a>
			<?
		} else {
			echo "<h1>dialogo inesistente o privilegi insufficienti</h1>";
		}
	} else {
		echo "<h1>dialogo inesistente</h1>";
	}
	include_once($root."/layout/bottom.php");
?>
