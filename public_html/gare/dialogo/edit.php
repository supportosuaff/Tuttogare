<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
		if ($codice_fase !== false) {
			$esito = check_permessi_gara($codice_fase,$_GET["codice_gara"],$_SESSION["codice_utente"]);
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
	if (isset($_GET["codice"]) && isset($_GET["codice_gara"])) {
		$bind = array();
		$bind[":codice"]=$_GET["codice_gara"];
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
		$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
		if ($_SESSION["gerarchia"] > 0) {
			$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
			$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
		}
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount() > 0) {
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
			if ($record_gara["dialogo_chiuso"]=="S") $lock = true;

				$codice = $_GET["codice"];
				$bind = array();
				$bind[":codice"] = $codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$bind[":codice_gara"] = $_GET["codice_gara"];

				$strsql = "SELECT * FROM b_dialogo WHERE codice = :codice";
				$strsql .= " AND codice_ente = :codice_ente ";
				$strsql .= " AND codice_gara = :codice_gara ";

				//echo $strsql;
				$risultato = $pdo->bindAndExec($strsql,$bind);

				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$operazione = "UPDATE";
					if ($record["cod_allegati"] != "" && preg_match("/^[0-9\;]+$/",$record["cod_allegati"])) {
								$allegati = explode(";",$record["cod_allegati"]);
								$str_allegati = ltrim(implode(",",$allegati),",");
								$sql = "SELECT * FROM b_allegati WHERE codice IN (" . $str_allegati . ")";
								$ris_allegati = $pdo->query($sql);
					}
				} else if ($codice == 0) {
						$record = get_campi("b_dialogo");
						$operazione = "INSERT";
				} else {
						echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
						die();
				}
				$bind = array();
				$bind[":codice_gara"] = $_GET["codice_gara"];
				$strsql = "SELECT b_utenti.codice, b_operatori_economici.partita_iva, b_operatori_economici.ragione_sociale, b_utenti.pec FROM b_utenti
									JOIN b_operatori_economici ON b_utenti.codice = b_operatori_economici.codice_utente
									JOIN r_inviti_gare ON b_utenti.codice = r_inviti_gare.codice_utente
									 WHERE r_inviti_gare.codice_gara = :codice_gara ORDER BY ragione_sociale ";
				$risultato_partecipanti = $pdo->bindAndExec($strsql,$bind);
?>
<h1>Richiesta dialogo</h1>
<div class="clear"></div>
				<div id="schede">
					<ul>
						<li><a href="#form">Richiesta</a></li>
						<li><a href="#partecipanti">Operatori Economici</a></li>
					</ul>
					<div id="form">
						<? if (!$lock) { ?>
						<form name="box" method="post" action="save.php" rel="validate">
											<input type="hidden" name="codice" value="<? echo $codice; ?>">
											<input type="hidden" name="operazione" value="<? echo $operazione ?>">
											<input type="hidden" name="codice_gara" value="<? echo $_GET["codice_gara"] ?>">
						<? } ?>
					<table width="100%">
						<tr>
							<td class="etichetta">Oggetto</td>
							<td colspan="3">
								<input type="text" name="titolo" value="<? echo $record["titolo"] ?>" class="titolo_edit" title="Titolo" rel="S;2;255;A">
							</td>
						</tr>
						<tr>
							<td class="etichetta">Data scadenza*</td>
							<td>
								<input type="text" id="data_scadenza" name="data_scadenza" value="<? echo mysql2datetime($record["data_scadenza"]) ?>" class="datetimepick" size="16" title="Data Scadenza" rel="S;16;16;DT">
							</td>
							<td class="etichetta">Data apertura</td>
							<td>
								<input type="text" id="data_apertura" name="data_apertura" value="<? echo mysql2datetime($record["data_apertura"]) ?>"
								class="datetimepick" size="16" title="Data Apertura"
								rel="<?= ($operazione=="INSERT" || ($operazione=="UPDATE" && $record["data_apertura"]==0)) ? "N" : "S" ?>;16;16;DT;data_scadenza;>"><br>
								<small>Se impostata sar&agrave; necessaria la chiave privata per accedere i files inviati dagli operatori</small>
							</td>
						</tr>
						<tr><td colspan="4">
              <textarea rows='10' class="ckeditor_simple" name="richiesta" cols='80' id="richiesta" title="Richiesta" rel="S;3;0;A"><? echo $record["richiesta"]; ?></textarea>
						</td></tr></table>
						<? if (!$lock) { ?>
<div id="allegati">
	<?	$cod_allegati = $record["cod_allegati"]; ?>
		<input type="hidden" value="<? echo $cod_allegati ?>" name="cod_allegati" title="Allegati" id="cod_allegati" rel="N;0;0;A">
        <button onClick="open_allegati();return false;" style="width:100%; padding:10px; background-color:#F60" class="submit">
		           <img src="/allegati/icon.png" alt="Allega" width="15" style="vertical-align:middle"> Allega file
		</button>
       	<table width="100%" id="tab_allegati">
        	<? if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
            	while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
					include($root."/allegati/tr_allegati.php");
				}
			} ?>
        </table>

</div>
<input type="submit" class="submit_big" value="Salva">
<input type="hidden" id="codici_partecipante" name="codici_partecipante" value="" title="Destinatari" <? if ($operazione=="INSERT") { ?>rel="S;0;0;A"<?} ?>>
</form>
<? }?>
</div>
<?
if ($risultato_partecipanti->rowCount()>0){ ?>
	<div id="partecipanti">
		<? if ($operazione == "UPDATE" && strtotime($record["data_apertura"]) <= time() && $record["data_apertura"] > 0) { ?>
			<div style="text-align:center">
				<strong>Caricare la chiave privata</strong><br>
				<input type="file" id="chiave" title="Chiave privata" rel="N;0;0;F"/>
			</div>
			<script>
			if (window.File && window.FileReader && window.FileList && window.Blob) {
			 function handleFileSelect(evt) {
				$("#file").parent().addClass('working');
				var file = evt.target.files[0];
				var r = new FileReader();
				r.onload = function(e) {
					var contents = e.target.result;
					$(".private").val(contents);
					$("#chiave").parent().removeClass('working');
				}
				r.readAsBinaryString(file);
			}
			document.getElementById("chiave").addEventListener('change', handleFileSelect, false);
		} else {
			corpo_alert = '<div style="text-align:center; font-weight:bold">Il tuo browser non supporta la procedura di invio.<br>';
			corpo_alert += 'Si consiglia di aggiornare il browser in uso o di utilizzare uno dei seguenti';
			corpo_alert += '<table width="100%"><tr>';
			corpo_alert += '<td style="text-align:center; width:50%;"><a target="_blank" title="Sito esterno" href="http://www.google.it/intl/it/chrome/browser/">';
			corpo_alert += '<img src="/img/chrome.png" alt="Google Chrome"><br>Google Chrome';
			corpo_alert += '</a></td>';
			corpo_alert += '<td style="text-align:center; width:50%;"><a target="_blank" title="Sito esterno" href="http://www.mozilla.org/it/firefox/new/">';
			corpo_alert += '<img src="/img/firefox.png" alt="Firefox"><br>Firefox';
			corpo_alert += '</a></td>';
			corpo_alert += '</tr>';
			corpo_alert += '</table></div>';
			jalert(corpo_alert);
			$('#buste').after(corpo_alert).remove();
		}
		</script>
		<? } ?>
		<table width="100%" id="elenco_partecipanti">
			<thead>
				<tr>
					<td>Codice Fiscale</td>
					<td>Ragione Sociale</td>
					<td>PEC</td>
					<td><? if (strtotime($record["data_scadenza"]) > time() || $record["data_scadenza"] == "") { ?><input id="invia_all" type="image" src="/img/add.png" onClick="add_all(); return false;" width="24" title="Aggiungi tutti"><? } ?></td>
				</tr>
			</thead>
			<tbody>
				<?
					$codici_partecipante = array();
					while($record_partecipante=$risultato_partecipanti->fetch(PDO::FETCH_ASSOC)) {
						$ok = false;
						if ($codice != 0)
						{
							$bind = array();
							$bind[":codice_utente"] = $record_partecipante["codice"];
							$bind[":codice"] = $codice;
							$sql_check = "SELECT * FROM r_dialogo WHERE codice_utente = :codice_utente AND codice_dialogo = :codice";
							$ris_check = $pdo->bindAndExec($sql_check,$bind);
							if ($ris_check->rowCount()==0) {
								$ok=true;
							} else {
								$record_dialogo = $ris_check->fetch(PDO::FETCH_ASSOC);
							}
						} else {
							$ok = true;
						}
						if ($ok) $codici_partecipante[] = $record_partecipante["codice"];
						if ((strtotime($record["data_scadenza"]) <= time() && $record["data_scadenza"] > 0 && !$ok) || ($record["data_scadenza"]==0) || (strtotime($record["data_scadenza"]) > time() && $record["data_scadenza"] > 0)) {
						?>
						<tr id="partecipante_<? echo $record_partecipante["codice"] ?>">
									<td width="10"><strong><? echo $record_partecipante["partita_iva"] ?></strong></td>
									<td><? echo $record_partecipante["ragione_sociale"] ?></td>
									<td><?= $record_partecipante["pec"] ?></td>
									<td width="10" id="cella_<?= $record_partecipante["codice"] ?>">
										<? if ($record_partecipante["codice"] != 0 && $ok && ((strtotime($record["data_scadenza"]) > time() || $record["data_scadenza"] == ""))) { ?>
											<input id="add_<? echo $record_partecipante["codice"] ?>" class="add_destinatario" type="image" src="/img/add.png" onClick='add_destinatario("<? echo $record_partecipante["codice"] ?>");return false;' width="24" title="Aggiungi destinatario">
										<? } else {
											if (isset($record_dialogo) && $record_dialogo["nome_file"] != "") {
												if ($record_dialogo["aperto"] == "N") {
													if ($record_dialogo["salt"] != "") {
														if (strtotime($record["data_apertura"]) <= strtotime("now")) { ?>
														<form action="open.php" rel="validate" method="post">
															<input type="hidden" name="codice_gara" value="<? echo $record["codice_gara"] ?>">
															<input type="hidden" name="codice" value="<? echo $record_dialogo["codice"] ?>">
															<input type="hidden" name="private_key" class="private" rel="S;0;0;A" title="Chiave privata">
															<input type="submit" value="Apri busta">
														</form>
														<? } else { ?>
															Impossibile aprire
														<? }
													} else {
													 ?>
													 <a href="/allegati/download_allegato.php?codice=<? echo $record_dialogo["codice_allegato"] ?>" title="Scarica P7M">
		 												<img src="/img/download.png" alt="Scarica Allegato" width="25"></a>
		 												<a href="/allegati/open_p7m.php?codice=<? echo $record_dialogo["codice_allegato"] ?>" title="Estrai Contenuto">
		 													<img src="/img/p7m.png" alt="Estrai Allegato" width="25">
		 											</a>
													 <?
													}
												} else { ?>
													<a href="/allegati/download_allegato.php?codice=<? echo $record_dialogo["codice_allegato"] ?>" title="Scarica P7M">
														<img src="/img/download.png" alt="Scarica Allegato" width="25"></a>
														<a href="/allegati/open_p7m.php?codice=<? echo $record_dialogo["codice_allegato"] ?>" title="Estrai Contenuto">
															<img src="/img/p7m.png" alt="Estrai Allegato" width="25">
														</a>

														<? }
													} else { ?>
														Non presentata
													<? } ?>
												</td>
										<?	} ?>
									</td>
							</tr>
						<?
						}
					}
				?>
			</tbody>
		</table>
	</div>
	<script>
	<?
		if (count($codici_partecipante) == 0) {
	?>
		$("#invia_all").remove();
	<?
		}
	?>
		function add_all() {
			if ($("#codici_partecipante").val().split(',').length == <?= count($codici_partecipante) ?>) {
				$("#codici_partecipante").val('');
				$(".add_destinatario").attr("src","/img/add.png");
				$(".add_destinatario").parent().parent().removeClass("selezionato");
				$("#invia_all").attr("src","/img/add.png");
			} else {
				$("#codici_partecipante").val('<?= implode(",",$codici_partecipante) ?>');
				$(".add_destinatario").attr("src","/img/del.png");
				$("#invia_all").attr("src","/img/del.png");
				$(".add_destinatario").parent().parent().addClass("selezionato");
			}
		}
		function add_destinatario(codice) {
			if ($("#codici_partecipante").val() != "") {
				codici = $("#codici_partecipante").val().split(",");
			} else {
				codici = Array();
			}
			pos = $.inArray(codice, codici);
			if (pos == -1) {
				codici.push(codice);
				$("#add_"+codice).attr("src","/img/del.png");
				$("#partecipante_"+codice).addClass("selezionato");
			} else {
				codici.splice(pos, 1);
				$("#add_"+codice).attr("src","/img/add.png");
				$("#partecipante_"+codice).removeClass("selezionato");
			}
			$("#codici_partecipante").val(codici.join(","));
			if (codici.length == <?= count($codici_partecipante) ?>) {
				$("#invia_all").attr("src","/img/del.png");
			} else {
				$("#invia_all").attr("src","/img/add.png");

			}
		}
	</script>
	<?
}
?>
</div>
			<script>
			$("#schede").tabs(<? if ($operazione=="UPDATE") echo "{active:1}" ?>);
			<? if ($lock){ ?>
				 $("#tabs :input").not('.espandi').prop("disabled", true);
			<? } ?>

			</script>

     <?
	 $form_upload["codice_gara"] = $_GET["codice_gara"];
	 $form_upload["online"] = 'S;S';
	 include($root."/allegati/form_allegati.php"); ?>

    <div class="clear"></div>
    <?
		$_GET["codice"] = $_GET["codice_gara"];
		include($root."/gare/ritorna.php");

			} else {

				echo "<h1>Richiesta non trovata</h1>";

				}
			} else {

				echo "<h1>Richiesta non trovata</h1>";

				}

	?>


<?
	include_once($root."/layout/bottom.php");
	?>
