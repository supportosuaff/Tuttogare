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

			$codice = $_GET["codice"];
			$bind = array();
			$bind[":codice"] = $codice;
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$bind[":codice_gara"] = $_GET["codice_gara"];

			$strsql = "SELECT * FROM b_rdo_ad WHERE codice = :codice";
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
					$record = get_campi("b_rdo_ad");
					$operazione = "INSERT";
			} else {
					echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
					die();
			}
			$bind = array();
			$bind[":codice_gara"] = $_GET["codice_gara"];
			$bind[":codice_rdo"] = $_GET["codice"];
			$strsql = "SELECT r_partecipanti.* FROM r_partecipanti JOIN r_rdo_ad ON r_partecipanti.codice = r_rdo_ad.codice_partecipante WHERE r_rdo_ad.codice_rdo = :codice_rdo AND codice_gara = :codice_gara AND codice_capogruppo = 0 GROUP BY r_partecipanti.codice ORDER BY ragione_sociale ";
			$risultato_partecipanti = $pdo->bindAndExec($strsql,$bind);
			$sql = "SELECT * FROM temp_inviti WHERE codice_gara = :codice_gara AND codice_richiesta = :codice_rdo AND attivo = 'S'";
			$ris_inviti = $pdo->bindAndExec($sql,$bind);
?>
<h1>Richiesta Offerta</h1>
<? if (pecConfigurata()) { ?>
			<div class="clear"></div>
				<div id="schede">
					<ul>
						<li><a href="#form">Richiesta</a></li>
						<? if (strtotime($record["data_scadenza"]) > time() || empty($record["data_scadenza"])) { ?>
							<li><a href="#invito">Operatori Economici</a></li>
							<li><a href="#manuale">Inserimento Manuale</a></li>
						<? } ?>
						<? if ($risultato_partecipanti->rowCount() > 0 || ($ris_inviti->rowCount() > 0)) { ?><li><a href="#offerte">Offerte</a></li><? } ?>
					</ul>
					<? if (!$lock) { ?>
						<form name="box" method="post" action="save.php" rel="validate">
											<input type="hidden" name="codice" value="<? echo $codice; ?>">
											<input type="hidden" name="operazione" value="<? echo $operazione ?>">
											<input type="hidden" name="codice_gara" value="<? echo $_GET["codice_gara"] ?>">
						<? } ?>

					<div id="form">
					<table width="100%">
						<tr>
							<td class="etichetta">Oggetto *</td>
							<td colspan="5">
								<input type="text" name="titolo" value="<? echo $record["titolo"] ?>" class="titolo_edit" title="Titolo" rel="S;2;255;A">
							</td>
						</tr>
						<tr>
							<td class="etichetta">Termine chiarimenti</td>
							<td>
								<input type="text" id="data_chiarimenti" name="data_chiarimenti" value="<? echo mysql2datetime($record["data_chiarimenti"]) ?>"
								class="datetimepick" size="16" title="Termine chiarimenti"
								rel="S;16;16;DT">
							</td>
							<td class="etichetta">Data scadenza*</td>
							<td>
								<input type="text" id="data_scadenza" name="data_scadenza" value="<? echo mysql2datetime($record["data_scadenza"]) ?>" class="datetimepick" size="16" title="Data Scadenza" rel="S;16;16;DT;data_chiarimenti;>">
							</td>
							<td class="etichetta">Data apertura</td>
							<td>
								<input type="text" id="data_apertura" name="data_apertura" value="<? echo mysql2datetime($record["data_apertura"]) ?>"
								class="datetimepick" size="16" title="Data Apertura"
								rel="<?= ($operazione=="INSERT" || ($operazione=="UPDATE" && $record["data_apertura"]==0)) ? "N" : "S" ?>;16;16;DT;data_scadenza;>"><br>
								<small>Se impostata sar&agrave; necessaria la chiave privata per accedere i files inviati dagli operatori</small>
							</td>
						</tr>
						<tr>
							<td class="etichetta">Invia richiesta anche ad indirizzo e-mail</td>
							<td>
								<input type="checkbox" name="sendMail">
							</td>
						</tr>
						<tr><td colspan="6">
              <textarea rows='10' class="ckeditor_simple" name="richiesta" cols='80' id="richiesta" title="Richiesta" rel="S;3;0;A"><? echo $record["richiesta"]; ?></textarea>
						</td></tr></table>
						<div id="anteprima-invitati" style="display:none">
							<h2>Invitati</h2>
							<table width="100%">
								<thead>
									<tr>
										<td width="150">Codice fiscale azienda</td>
										<td>Ragione Sociale</td>
										<td width="10"></td>
									</tr>
								</thead>
								<tbody id="table-invitati">
								</tbody>
							</table>
						</div>
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
<? } ?>
<a class="submit_big btn-warning" href="/gare/id<?= $record_gara["codice"] ?>-dettaglio" target="_blank"><span class="fa fa-search"></span> Anteprima</a>
<input type="submit" class="submit_big" value="Salva">
<input type="hidden" id="indirizzi" name="indirizzi" title="Destinatari">

</div>
<?
if (strtotime($record["data_scadenza"]) > time() || empty($record["data_scadenza"])) {
?>
	<div id="invito">
		<?
		include_once($root."/inc/oeManager.class.php");
    $bind = array();
    $bind[":codice_gara"] = $record_gara["codice"];
    $sql_cpv = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_gare ON b_cpv.codice = r_cpv_gare.codice WHERE r_cpv_gare.codice_gara = :codice_gara ORDER BY codice";
    $risultato_cpv = $pdo->bindAndExec($sql_cpv,$bind);
    if ($risultato_cpv->rowCount()>0) {
      $cpv = array();
      while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
        $cpv[] = $rec_cpv["codice"];
      }
      $_POST["oeManager"]["cpv"] = implode(";",$cpv);
    }

    if (!empty($record_gara["tipo_elenco"]) && !empty($record_gara["codice_elenco"])) {
      $_POST["oeManager"]["elenco"] = $record_gara["tipo_elenco"] . "-" . $record_gara["codice_elenco"];
    }

    ?>
    <button onClick="$('#filtro').slideToggle('fast'); return false" style="width:100%; padding:10px; background-color:#F60" class="submit">
    	<span class="fa fa-filter"></span> Filtri OE
    </button>
    <div class="box" id="filtro">
      <? oeManager::printFilterForm() ?>
      <button class="submit_big" onClick="oeManagerFilters = $('.oeManagerInput').serializeArray(); $('.export-filter').val($('.oeManagerInput').serialize()); elenco.draw(); return false;"><span class="fa fa-filter"></span> Applica filtri</button>
      <br>
    </div>
    <table style="text-align:center; width:100%; font-size:0.8em" id="oe">
      <thead>
        <tr>
					<th width="10">ID</th>
          <th>Ragione Sociale</th>
          <th width="10">Tipo</th>
          <th width="100">Codice Fiscale Azienda</th>
          <th width="150">Inviti</th>
          <th width="150">Affidamenti</th>
          <th width="10"></th>
          <th width="10"><button id="invia_all" class='btn-primary' onClick="triggerClick = true; elenco.page.len(-1).draw(); return false">Invita tutti</button></th>
        </tr>
      </thead>
      <tbody>
      </tbody>
		</table>
		<button title="Esporta PDF" onClick="$('#exportOEPDF').submit(); return false;"><img style="vertical-align:middle" src="/img/pdf.png" alt="Esporta">Esporta PDF</button>
		<button title="Esporta CSV" onClick="$('#exportOECSV').submit(); return false;"><img style="vertical-align:middle" src="/img/xls.png" alt="Esporta">Esporta CSV</button>
    <script>
      var oeManagerFilters = $(".oeManagerInput").serializeArray();
      var triggerClick = false
      var elenco = $("#oe").DataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "/js/dataTables.Italian.json"
        },
        "order": [[ 5, "ASC" ]],
        "ajax": {
          "url": "/gare/rdo_ad/directDataSource.php",
          "method": "POST",
          "data": function (d) {
            d.oeManager = oeManagerFilters;
						d.codice_gara = "<?= $record_gara["codice"] ?>";
            d.codice_rdo = "<?= $record["codice"] ?>";
            return d;
          }
        },
        "drawCallback": function( settings ) {
          if (triggerClick) {
           $('.invita').trigger('click');
           triggerClick = false;
          }
					check_invitati();
        },
        "pageLength": 50,
        "lengthMenu": [
            [5, 10, 25, 50, 100, 200, -1],
            [5, 10, 25, 50, 100, 200, "Tutti"]
        ]
      });

  		function invitato(codice) {
  			invitati = $("#indirizzi").val().split(";");
  			if ($.inArray(codice,invitati)==-1) {
  				invitati.push(codice);
  				$("#indirizzi").val(invitati.join(";"));
  				$("#invia_"+codice).removeClass("btn-warning").addClass("btn-primary").html('<span class="fa fa-check"></span> Selezionato');
					infoOE = $("#invia_"+codice).parent().parent().parent().children();
					row = "<tr id='invitato-"+codice+"'><td>" + infoOE[3].innerHTML + "</td><td>" + infoOE[1].innerHTML + "</td>";
					row += "<td><button class='btn-danger btn-round' onClick='invitato(\""+codice+"\"); return false;'><span class='fa fa-remove'></span></button></td></tr>";
					$("#table-invitati").append(row)
					$("#anteprima-invitati").slideDown();
  			} else {
  				index = $.inArray(codice,invitati);
  				invitati.splice(index,1);
  				$("#indirizzi").val(invitati.join(";"));
  				$("#invia_"+codice).removeClass("btn-primary").addClass("btn-warning").html('<span class="fa fa-plus"></span> Invita');
					$("#invitato-"+codice).remove();
  			}
				return false;
  		}


	      function check_invitati() {
					invitati = $("#indirizzi").val().split(";");
					invitati.forEach(function(codice) {
						$("#invia_"+codice).removeClass("btn-warning").addClass("btn-primary").html('<span class="fa fa-check"></span> Selezionato');
	        });
					return false;
	      }
			</script>
		</div>
		<div id="manuale">
			<div class="box">
				Inserendo manualmente i riferimenti, il sistema trasmetterà la richiesta, con un invito di registrazione, agli operatori economici non iscritti
			</div>
			<table width="100%">
				<thead>
					<tr>
						<th width="120">Codice Fiscale Azienda</th>
						<th width="120">identificativo Estero</th>
						<th>Ragione Sociale</th>
						<th>PEC</th>
						<th width="10"></th>
					</tr>
				</thead>
				<tbody id="partecipanti">
				</tbody>
				<tfoot>
					<tr>
						<td colspan="5">
							<button type="button" class="submit_big" onClick="aggiungi('tr_partecipante.php','#partecipanti')">Aggiungi partecipante</button>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
		<? } ?>
		<? if (!$lock) { ?>
			</form>
		<? } ?>
		<? if ($risultato_partecipanti->rowCount()>0 || ($ris_inviti->rowCount() > 0)){ ?>
			<div id="offerte">
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
							<td>Codice fiscale azienda</td>
							<td>Ragione Sociale</td>
							<td>PEC</td>
							<td></td>
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
									$bind[":codice_partecipante"] = $record_partecipante["codice"];
									$bind[":codice"] = $codice;
									$sql_check = "SELECT * FROM r_rdo_ad WHERE codice_partecipante = :codice_partecipante AND codice_rdo = :codice";
									$ris_check = $pdo->bindAndExec($sql_check,$bind);
									if ($ris_check->rowCount()==0) {
										$ok=true;
									} else {
										$record_r_rdo = $ris_check->fetch(PDO::FETCH_ASSOC);
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
											<td width="10" id="cella_<?= $record_partecipante["codice"] ?>">
												<?
													if (isset($record_r_rdo) && $record_r_rdo["nome_file"] != "") {
														if ($record_r_rdo["aperto"] == "N") {
															if ($record_r_rdo["salt"] != "") {
																if (strtotime($record["data_apertura"]) <= strtotime("now")) { ?>
																<form action="open.php" rel="validate" method="post">
																	<input type="hidden" name="codice_gara" value="<? echo $record["codice_gara"] ?>">
																	<input type="hidden" name="codice" value="<? echo $record_r_rdo["codice"] ?>">
																	<input type="hidden" name="private_key" class="private" rel="S;0;0;A" title="Chiave privata">
																	<input type="submit" value="Apri busta">
																</form>
																<? } else { ?>
																	Impossibile aprire
																<? }
															} else {
															 ?>
															 <a href="/allegati/download_allegato.php?codice=<? echo $record_r_rdo["codice_allegato"] ?>" title="Scarica P7M">
				 												<img src="/img/download.png" alt="Scarica Allegato" width="25"></a>
				 												<a href="/allegati/open_p7m.php?codice=<? echo $record_r_rdo["codice_allegato"] ?>" title="Estrai Contenuto">
				 													<img src="/img/p7m.png" alt="Estrai Allegato" width="25">
				 											</a>
															 <?
															}
														} else { ?>
															<a href="/allegati/download_allegato.php?codice=<? echo $record_r_rdo["codice_allegato"] ?>" title="Scarica P7M">
																<img src="/img/download.png" alt="Scarica Allegato" width="25"></a>
																<a href="/allegati/open_p7m.php?codice=<? echo $record_r_rdo["codice_allegato"] ?>" title="Estrai Contenuto">
																	<img src="/img/p7m.png" alt="Estrai Allegato" width="25">
																</a>

																<? }
															} else { ?>
																Non presentata
															<? } ?>
														</td>
											</td>
									</tr>
								<?
								}
							}
						?>
					</tbody>
				</table>
				<?
					if ($ris_inviti->rowCount() > 0) {
						?>
						<div class="box">
							<h3>Invitati non iscritti</h3>
							<table width="100%">
								<thead>
									<tr>
										<th width="120">Codice Fiscale azienda</th>
										<th width="120">identificativo Estero</th>
										<th>Ragione Sociale</th>
										<th>PEC</th>
									</tr>
								</thead>
								<tbody>
									<?
										while($partecipante = $ris_inviti->fetch(PDO::FETCH_ASSOC)) {
											?>
											<tr>
												<td><?= $partecipante["partita_iva"] ?></td>
												<td><?= $partecipante["identificativoEstero"] ?></td>
												<td><?= $partecipante["ragione_sociale"] ?></td>
												<td><?= $partecipante["pec"] ?></td>
											</tr>
											<?
										}
									?>
								</tbody>
							</table>
						</div>
						<?
					}
				?>
			</div>
			<?
		}
		?>
		</div>
		<div style="text-align:right">
					<form target="_blank" id="exportOEPDF" action="/albo/pdf.php" method="POST">
						<input type="hidden" id="filters-pdf" class="export-filter" name="filters">
					</form>
					<form target="_blank" id="exportOECSV" action="/albo/excel.php" method="POST">
						<input type="hidden" id="filters-excel" class="export-filter" name="filters">
					</form>
					<script>
						$(".export-filter").val($(".oeManagerInput").serialize());
					</script>
      	</div>
			<script>
			$("#schede").tabs(<? if ($operazione=="UPDATE") echo "{active:3}" ?>);
			<? if ($lock) { ?>

					$("#schede :input").not('.espandi').prop("disabled", true);

			<? } ?>
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
			?>
			<h2>Impossibile procedere. Configurare PEC</h2>
			<?
		}
			} else {
				echo "<h1>Richesta non trovata</h1>";
			}
		} else {
			echo "<h1>Procedura non trovata</h1>";
		}
	?>


<?
	include_once($root."/layout/bottom.php");
	?>
