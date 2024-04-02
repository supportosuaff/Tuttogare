<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFase($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
		if ($codice_fase !== false) {
			$esito = check_permessi_gara($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
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
			?>
			<div id="conversazioni">
			<h1>Conversazioni interne</h1>
			<form name="box" method="post" action="/gare/conversazioni/save.php" rel="validate">
				<input type="hidden" name="codice_gara" value="<?= $codice ?>">
			<?
				$bind = array();
				$bind[":codice_gara"] = $codice;
				$strsql  = "SELECT b_messaggi.*, b_utenti.nome, b_utenti.cognome FROM b_messaggi
										JOIN b_utenti ON b_messaggi.utente_modifica = b_utenti.codice
										JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice
										WHERE b_messaggi.codice_gara = :codice_gara
										ORDER BY b_messaggi.timestamp";
				$ris_msg = $pdo->bindAndExec($strsql,$bind);
				$numero_msg = $ris_msg->rowCount();
				$i = 0;

					$first = false;
				if ($numero_msg > 0) {
					while ($msg = $ris_msg->fetch(PDO::FETCH_ASSOC)) {
						$style = "";
						$class = "";
						if ($msg["utente_modifica"] == $_SESSION["codice_utente"]) {
							$style = "float:right; background-color:#FFF;";
						} else {
							$bind = array(":codice_utente" => $_SESSION["codice_utente"],":codice_messaggio"=> $msg["codice"]);
							$sql_read = "SELECT * FROM r_read WHERE utente_modifica = :codice_utente AND codice_messaggio = :codice_messaggio";
							$ris_read = $pdo->bindAndExec($sql_read,$bind);
							if ($ris_read->rowCount() == 0) {
								if (!$first) {
									$first = true;
									$class = "first";
								}
								$style="background-color:#9fedb7; font-weight:bold";
								$read = array();
								$read["codice_messaggio"] = $msg["codice"];

								$salva = new salva();
								$salva->debug = false;
								$salva->codop = $_SESSION["codice_utente"];
								$salva->nome_tabella = "r_read";
								$salva->operazione = "INSERT";
								$salva->oggetto = $read;
								$salva->save();

							}
						}
							$i++;
							?>
							<div class="box <?= $class ?>" style="width:80%; <?  echo $style; ?>">
								<div style="text-align:right; font-size:10px;">
									<?= "<strong>" . $msg["cognome"] . " " . $msg["nome"] . "</strong> - " . mysql2datetime($msg["timestamp"]); ?>
								</div>
								<?= $msg["testo"] ?>
								<?
								if (!empty($msg["cod_allegati"]) && preg_match("/^[0-9\;]+$/",$msg["cod_allegati"])) {
									$allegati = explode(";",$msg["cod_allegati"]);
									$str_allegati = ltrim(implode(",",$allegati),",");
									$sql = "SELECT * FROM b_allegati WHERE codice IN (" . $str_allegati . ") AND online = 'N'";
									$ris_allegati = $pdo->query($sql);
									if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
											?>
											<table width="100%" id="tab_allegati">
												<?
												$edit = false;
												while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
													include($root."/allegati/tr_allegati.php");
												}
												$edit = true;
												?>
											</table>
											<?
										}
									}
								?>
							</div>
							<div class="clear"></div>
							<?
						}
				}
				?>

				<div class="box">
					<textarea rows='10' class="ckeditor" name="testo" cols='80' id="testo_msg" title="testo" rel="S;3;0;A"></textarea>
				</div>
				<div id="allegati">
						<input type="hidden" name="cod_allegati" title="Allegati" id="cod_allegati" rel="N;0;0;A">
				        <button onClick="open_allegati();return false;" style="width:100%; padding:10px; background-color:#F60" class="submit">
						           <img src="/allegati/icon.png" alt="Allega" width="15" style="vertical-align:middle"> Allega file
						</button>
						<table width="100%" id="tab_riservati">
						</table>
				</div>
				<input type="submit" class="submit_big" value="Invia">
		    </form>
				<? $scrollTo = "#testo_msg"; ?>
				<? if ($first) $scrollTo = ".first" ?>
					<script>
						$('html,body').animate({
							scrollTop: $('<?= $scrollTo ?>').offset().top
						},'slow');
					</script>
				 </div>
		    <div class="clear"></div>
				<?
				$form_upload["codice_gara"] = $codice;
				$form_upload["online"] = 'N';
				include($root."/allegati/form_allegati.php");
				include($root."/gare/ritorna.php");
				include_once($root."/layout/bottom.php");
			}
?>
