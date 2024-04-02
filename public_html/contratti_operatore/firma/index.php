<?
	include_once "../../../config.php";
	$disable_alert_sessione = true;
	include_once $root . "/layout/top.php";
  if (!is_operatore()) {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  } else {
    $ris_operatore = $pdo->bindAndExec("SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente", array(':codice_utente' => $_SESSION["codice_utente"]));
    $operatore = $ris_operatore->fetch(PDO::FETCH_ASSOC);
    if (!empty($_SESSION["codice_utente"]) && !empty($_SESSION["ente"]) && !empty($_GET["codice"])) {
			$codice_contratto = $_GET["codice"];
	    $sql = "SELECT b_contratti.*, b_conf_modalita_stipula.invio_remoto, b_conf_modalita_stipula.etichetta as modalita_di_stipula
	            FROM b_contratti
	            JOIN b_conf_modalita_stipula ON b_conf_modalita_stipula.codice = b_contratti.modalita_stipula
	            JOIN r_contratti_contraenti ON r_contratti_contraenti.codice_contratto = b_contratti.codice
	            JOIN b_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente
	            WHERE b_contraenti.codice_utente = :codice_utente
	            AND b_contratti.codice = :codice_contratto
	            AND r_contratti_contraenti.codice_capogruppo = 0";
	    $ris = $pdo->bindAndExec($sql, array(':codice_utente' => $_SESSION["record_utente"]["codice"], ':codice_contratto' => $codice_contratto));
			if($ris->rowCount() > 0) {
	      $rec_contratto = $ris->fetch(PDO::FETCH_ASSOC);
				if($rec_contratto["invio_remoto"] == "S") {
					$href_contratto = "?codice={$rec_contratto["codice"]}";
					?>
					<h1>UPLOAD DEL CONTRATTO FIRMATO DIGITALMENTE</h1><br>
					<script type="text/javascript" src="/js/spark-md5.min.js"></script>
					<script type="text/javascript" src="/js/resumable.js"></script>
					<script type="text/javascript" src="resumable-uploader.js"></script>
					<div class="box">
            <?
            $bind = array(':codice_contratto' => $rec_contratto["codice"], ':codice_ente' => $_SESSION["ente"]["codice"]);
            $ris_file = $pdo->bindAndExec("SELECT b_allegati.* FROM b_allegati WHERE sezione = 'contratti' AND codice_gara = :codice_contratto AND cartella = 'contratti_da_firmare' AND codice_ente = :codice_ente", $bind);
            if($ris_file->rowCount() > 0) {
              $rec_file = $ris_file->fetch(PDO::FETCH_ASSOC);
              $file = "{$config["arch_folder"]}/allegati_contratto/{$rec_contratto["codice"]}/{$rec_file["riferimento"]}";
              if(file_exists($file)) {
                ?><a class="pannello" href="contratto.php<?= $href_contratto ?>" target="_blank" title="Contratto da firmare">Scarica il contratto da firmare</a><?
              }
            }
            ?>
						<form action="upload.php" id="upload_contratto" method="post" target="_self" rel="validate">
							<input type="hidden" name="codice_contratto" value="<?= $rec_contratto["codice"] ?>">
							<input type="hidden" name="md5_file" id="md5_file" title="File" rel="S;0;0;A">
							<input type="hidden" id="filechunk" name="filechunk">
									<table style="width:100%;">
										<thead>
											<tr>
												<td colspan="2" style="color: #000;">
													<div class="scegli_file"><i class="fa fa-folder-open fa-5x"></i><br>Clicca per selezionare il file del contratto firmato digitalmente</div>
													<script>
														var uploader = (function($){
															return (new ResumableUploader($('.scegli_file')));
														})(jQuery);
													</script>
												</td>
											</tr>
											<tr>
												<td colspan="2">
													<div id="progress_bar" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
												</td>
											</tr>
											<tr>
												<th colspan="2">
													<input type="submit" class="submit_big" style="cursor:pointer" onClick="$('#wait').show(); uploader.resumable.upload();return false;" value="Carica">
												</th>
											</tr>
										</thead>
									</table>
						</form>
					</div>
					<?
				} else {
					echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			    die();
				}
			} else {
				?><h3 class="ui-state-error">Permessi insufficienti per la firma del contratto. Si prega di contattare l&#39;amministrazione.</h3><?
			}
		} else {
			?><h3 class="ui-state-error">Permessi insufficienti per la firma del contratto. Si prega di contattare l&#39;amministrazione.</h3><?
		}
  }
	include_once($root . "/contratti_operatore/ritorna_pannello_contratto.php");
	include_once($root."/layout/bottom.php");
	die();
?>
