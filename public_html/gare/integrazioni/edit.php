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
	if (isset($_GET["codice"]) && isset($_GET["codice_gara"]) && isset($_GET["codice_lotto"])) {

				$codice = $_GET["codice"];
				$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
				$record_gara = $pdo->go($strsql,[":codice"=>$_GET["codice_gara"]])->fetch(PDO::FETCH_ASSOC);
				$bind = array();
				$bind[":codice"] = $codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$bind[":codice_gara"] = $_GET["codice_gara"];
				$bind[":codice_lotto"] = $_GET["codice_lotto"];

				$strsql = "SELECT * FROM b_integrazioni WHERE codice = :codice
							AND codice_ente = :codice_ente 
							AND codice_gara = :codice_gara 
							AND codice_lotto = :codice_lotto ";

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
						$record = get_campi("b_integrazioni");
						$operazione = "INSERT";
						?>
						<script>
							function completaForm() {
								titolo = "";
								val =  $("#soccorso_istruttorio").val();
								if (val != "") {
									label = $('#soccorso_istruttorio option[value="'+val+'"]').html();
									titolo += label + " - ";
								}
								titolo += $("#oggetto_gara").html();
								if ($("#data_scadenza").val() == "") $("#data_scadenza").val('<?= date("d/m/Y",strtotime("+10 day")) ?> 13:00');
								if ($("#titolo").val() == "") $("#titolo").val(titolo);
							}
						</script>
						<?
				} else {
						echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
						die();
				}
				$bind = array();
				$bind[":codice_gara"] = $_GET["codice_gara"];
				$bind[":codice_lotto"] = $_GET["codice_lotto"];
				$bind[":codice_integrazione"] = $_GET["codice"];
				$strsql = "SELECT r_partecipanti.* 
							FROM r_partecipanti
							LEFT JOIN r_integrazioni ON r_partecipanti.codice = r_integrazioni.codice_partecipante 
									  AND r_integrazioni.codice_integrazione = :codice_integrazione 
							WHERE 
								r_partecipanti.codice_gara = :codice_gara AND 
								r_partecipanti.codice_lotto = :codice_lotto AND r_partecipanti.codice_capogruppo = 0 
							AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) 
							ORDER BY r_integrazioni.codice_integrazione DESC, ragione_sociale ";
				$risultato_partecipanti = $pdo->bindAndExec($strsql,$bind);
?>
<h1>Richiesta integrazioni</h1>
<small id="oggetto_gara"><?= $record_gara["oggetto"] ?></small><br><br>
<div class="clear"></div>
				<div id="schede">
					<ul>
						<li><a href="#form">Richiesta</a></li>
						<li><a href="#partecipanti">Operatori Economici</a></li>
					</ul>
					<div id="form">
						<form name="box" method="post" action="save.php" rel="validate">
											<input type="hidden" name="codice" value="<? echo $codice; ?>">
											<input type="hidden" name="operazione" value="<? echo $operazione ?>">
											<input type="hidden" name="codice_gara" value="<? echo $_GET["codice_gara"] ?>">
											<input type="hidden" name="codice_lotto" value="<? echo $_GET["codice_lotto"] ?>">

					<table width="100%">
						<tr>
							<td class="etichetta">Tipo di richiesta*</td>
								<td>
									<select <? if ($codice == 0) { ?>onChange="completaForm();"<? } ?> name="soccorso_istruttorio" id="soccorso_istruttorio" title="Tipo di richiesta" rel="S;1;1;A">
										<option value="">Seleziona...</option>
										<option value="S">Soccorso Istruttorio</option>
										<option value="N">Integrazione</option>
										<option value="A">Verifica Anomalie</option>
									</select>
									<script>
										$('#soccorso_istruttorio').val('<?= $record["soccorso_istruttorio"] ?>');
									</script>
								</td>
							<td class="etichetta">Data scadenza*</td>
							<td>
								<input type="text" id="data_scadenza" name="data_scadenza" value="<? echo mysql2datetime($record["data_scadenza"]) ?>" class="datetimepick" size="16" title="Data Scadenza" rel="S;16;16;DT">
							</td>
							<td class="etichetta">Data apertura</td>
							<td>
								<input type="text" id="data_apertura" name="data_apertura" value="<? echo mysql2datetime($record["data_apertura"]) ?>"
								class="datetimepick" size="16" title="Data Apertura"
								rel="<?= ($operazione=="INSERT" || ($operazione=="UPDATE" && $record["data_apertura"]==0)) ? "N" : "S" ?>;16;16;DT;data_scadenza;>"><br>
								<small>Se impostata sar&agrave; necessaria la chiave privata per accedere ai files inviati dagli operatori</small>
							</td>
						</tr>
						<tr>
							<td class="etichetta">Oggetto</td>
							<td colspan="5">
								<input type="text" id="titolo" name="titolo" value="<? echo $record["titolo"] ?>" class="titolo_edit" title="Titolo" rel="S;2;255;A">
							</td>
						</tr>
						<tr><td colspan="6">
              <textarea rows='10' class="ckeditor_simple" name="richiesta" cols='80' id="richiesta" title="Richiesta" rel="S;3;0;A"><? echo $record["richiesta"]; ?></textarea>
						</td></tr></table>
						<div id="anteprima-selezionati" style="display:none">
							<h2>Selezionati</h2>
							<table width="100%">
								<thead>
									<tr>
										<td width="150">Partita IVA</td>
										<td>Ragione Sociale</td>
										<td width="10"></td>
									</tr>
								</thead>
								<tbody id="table-selezionati">
								</tbody>
							</table>
						</div>
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
					<td width="150" style="text-align:center">
					</td>
				</tr>
			</thead>
			<tbody>
				<?
					$codici_partecipante = array();
					while($record_partecipante=$risultato_partecipanti->fetch(PDO::FETCH_ASSOC)) {
						$ok = false;
						unset($record_integrazione);
						if ($codice != 0)
						{
							$bind = array();
							$bind[":codice_partecipante"] = $record_partecipante["codice"];
							$bind[":codice"] = $codice;
							$sql_check = "SELECT * FROM r_integrazioni WHERE codice_partecipante = :codice_partecipante AND codice_integrazione = :codice";
							$ris_check = $pdo->bindAndExec($sql_check,$bind);
							if ($ris_check->rowCount()==0) {
								$ok=true;
							} else {
								$record_integrazione = $ris_check->fetch(PDO::FETCH_ASSOC);
							}
						} else {
							$ok = true;
						}
						if ($ok) $codici_partecipante[] = $record_partecipante["codice"];
						if ((strtotime($record["data_scadenza"]) <= time() && $record["data_scadenza"] > 0 && !$ok) || ($record["data_scadenza"]==0) || (strtotime($record["data_scadenza"]) > time() && $record["data_scadenza"] > 0)) {
						?>
						<tr id="partecipante_<? echo $record_partecipante["codice"] ?>">
									<td width="10"><strong><? echo $record_partecipante["partita_iva"] ?></strong></td>
									<td><? if ($record_partecipante["tipo"] != "") echo "<strong>RAGGRUPPAMENTO</strong> - " ?><? echo $record_partecipante["ragione_sociale"] ?></td>
									<td><?= $record_partecipante["pec"] ?></td>
									<td width="10" style="text-align:center" id="cella_<?= $record_partecipante["codice"] ?>">
										<? if ($record_partecipante["codice_utente"] != 0 && $ok && ((strtotime($record["data_scadenza"]) > time() || $record["data_scadenza"] == ""))) { ?>
											<button id="add_<? echo $record_partecipante["codice"] ?>" class="btn-warning add_destinatario" onClick='add_destinatario("<? echo $record_partecipante["codice"] ?>");return false;' title="Aggiungi destinatario">
												<span class="fa fa-plus"></span> Seleziona
											</button>
										<? } else {
											if (isset($record_integrazione)) {
												if ($record_integrazione["nome_file"] != "") {
													if ($record_integrazione["aperto"] == "N") {
														if ($record_integrazione["salt"] != "") {
															if (strtotime($record["data_apertura"]) <= strtotime("now")) { ?>
															<form action="open.php" rel="validate" method="post">
																<input type="hidden" name="codice_gara" value="<? echo $record["codice_gara"] ?>">
																<input type="hidden" name="codice" value="<? echo $record_integrazione["codice"] ?>">
																<input type="hidden" name="private_key" class="private" rel="S;0;0;A" title="Chiave privata">
																<input type="submit" value="Apri busta">
															</form>
															<? } else { ?>
																Impossibile aprire
															<? }
														} else {
														?>
														<a href="/allegati/download_allegato.php?codice=<? echo $record_integrazione["codice_allegato"] ?>" title="Scarica P7M">
															<img src="/img/download.png" alt="Scarica Allegato" width="25"></a>
															<a href="/allegati/open_p7m.php?codice=<? echo $record_integrazione["codice_allegato"] ?>" title="Estrai Contenuto">
																<img src="/img/p7m.png" alt="Estrai Allegato" width="25">
														</a>
														<?
														}
													} else { ?>
														<a href="/allegati/download_allegato.php?codice=<? echo $record_integrazione["codice_allegato"] ?>" title="Scarica P7M">
															<img src="/img/download.png" alt="Scarica Allegato" width="25"></a>
														<a href="/allegati/open_p7m.php?codice=<? echo $record_integrazione["codice_allegato"] ?>" title="Estrai Contenuto">
															<img src="/img/p7m.png" alt="Estrai Allegato" width="25">
														</a>
													<? }
													echo "<br>" . mysql2datetime($record_integrazione["timestamp_trasmissione"]);
													} else { ?>
													Non presentata
												<? }
											} ?>
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
		<script>
			$("#elenco_partecipanti").DataTable({
				"paginate": false,
				"sorting": false,
				"info": false
			});
		</script>
	</div>
	<script>
	<?
		if (count($codici_partecipante) == 0) {
	?>
		$("#invia_all").remove();
	<?
		}
	?>
		function add_destinatario(codice) {
			if ($("#codici_partecipante").val() != "") {
				codici = $("#codici_partecipante").val().split(",");
			} else {
				codici = Array();
			}
			pos = $.inArray(codice, codici);
			if (pos == -1) {
				codici.push(codice);
				$("#add_"+codice).removeClass("btn-warning").addClass("btn-primary").html('<span class="fa fa-check"></span> Selezionato');
				infoOE = $("#add_"+codice).parent().parent().children();
				row = "<tr id='invitato-"+codice+"'><td>" + infoOE[0].innerHTML + "</td><td>" + infoOE[1].innerHTML + "</td>";
				row += "<td><button class='btn-danger btn-round' onClick='add_destinatario(\""+codice+"\"); return false;'><span class='fa fa-remove'></span></button></td></tr>";
				$("#table-selezionati").append(row)
				$("#anteprima-selezionati").slideDown();
				$("#partecipante_"+codice).addClass("selezionato");
			} else {
				codici.splice(pos, 1);
				$("#add_"+codice).removeClass("btn-primary").addClass("btn-warning").html('<span class="fa fa-plus"></span> Seleziona');
				$("#invitato-"+codice).remove();
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
			</script>

     <?
	 $form_upload["codice_gara"] = $_GET["codice_gara"];
	 $form_upload["online"] = 'S;S';
	 include($root."/allegati/form_allegati.php");


	 $_GET["codice"] = $_GET["codice_gara"];
	 include($root."/gare/ritorna.php");

	 ?>

    <div class="clear"></div>
    <?

			} else {

				echo "<h1>Integrazione non trovata</h1>";

				}

	?>


<?
	include_once($root."/layout/bottom.php");
	?>
