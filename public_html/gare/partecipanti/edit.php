<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
				if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
				if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
					$codice_fase = getFase($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
					if ($codice_fase!==false) {
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
				$codice = $_GET["codice"];
				$bind = array();
				$bind[":codice"]=$codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}
			//	$strsql .= " AND data_apertura <= now() ";
				$risultato = $pdo->bindAndExec($strsql,$bind);

				?>
				<h1>PARTECIPANTI</h1>

				<?
				if ($risultato->rowCount() > 0) {
					$ifase = false;
					if (!empty($_GET["ifase"])) {
						$ifase = true;
						$lock = true;
					}
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					if (isset($_POST["submit"]) && !$lock) {
						//echo "sono dentro";
						$codice_lotto = $_POST["codice_lotto"];
						$upload_path = $config["chunk_folder"] . "/";
							$allowed_filetypes = array('.csv');
							$filename = $_FILES["partecipanti"]["name"];
							$ext = substr($filename, strpos($filename, '.'), strlen($filename) - 1);
							$msg='';
							if (!in_array($ext, $allowed_filetypes))
								$msg = ('Tipologia di file non permesso');
							if (move_uploaded_file($_FILES["partecipanti"]["tmp_name"], $upload_path . $filename)){
								//$msg =  time() . ' Your file upload was successful, view the file <a href="' . $upload_path . $filename . '" title="Your File">here</a>';
								ini_set('auto_detect_line_endings',TRUE);
								$file = fopen($upload_path . $filename, "r");
								while(! feof($file))
									$partecipanti[]=fgetcsv($file,0,';');
								fclose($file);

								//print_r($partecipanti);
								foreach($partecipanti as $key=>$value){
									if($value!=null){
									if(!isset($value[3])) $value[3]="";
									if(!isset($value[4])) $value[4]="";
									if(!isset($value[5])) $value[5]="";
									if($value[0]!="NUM. PROTOCOLLO" && $value[1]!="DATA PROTOCOLLO" && $value[2]!="IMPRESA" && $value[3]!="IDENTIFICATIVO ESTERO" && $value[4]!="P. IVA" && $value[5]!="PEC"){
										//print_r($value);
										//CONTROLLO DATA
										if(!preg_match("/(0[1-9]|[12][0-9]|3[01])[\/\-\.](0[1-9]|1[012])[\/\-\.](19|20)\d\d/", $value[1])){
											$msg .= "Attenzione, errore nell'inserimento <br/> del partecipante <strong>".$value[2]."</strong>, <br/>verificare la data di protocollo (formato: <strong>dd/mm/yyyy</strong>)\\n";
											break;
										}
										$codice_operatore = 0;
										$codice_utente = 0;
										$bind=array();
										$bind[":codice"] = $value[5];
										$strsql = "SELECT b_operatori_economici.*, b_utenti.pec FROM b_operatori_economici JOIN b_utenti ON b_operatori_economici.codice_utente = b_utenti.codice WHERE b_utenti.pec = :codice ";
										$risultato_operatori_economici = $pdo->bindAndExec($strsql,$bind);
										if ($risultato_operatori_economici->rowCount()==1) {
											$record_operatore = $risultato_operatori_economici->fetch(PDO::FETCH_ASSOC);
											$codice_operatore = $record_operatore["codice"];
											$codice_utente = $record_operatore["codice_utente"];
										}
										$bind=array();
										$bind[":var_1"] = $codice;
										$bind[":var_2"] = $codice_lotto;
										$bind[":var_3"] = $codice_operatore;
										$bind[":var_4"] = $codice_utente;
										$bind[":var_5"] = date2mysql($value[1]);
										$bind[":var_6"] = $value[0];
										$bind[":var_7"] = $value[4];
										$bind[":var_8"] = htmlspecialchars($value[2],ENT_QUOTES);
										$bind[":var_9"] = $value[3];
										$bind[":var_10"] = $value[5];
										$sql_insert = "INSERT INTO r_partecipanti (codice_gara, codice_lotto,codice_operatore, codice_utente, data_protocollo, numero_protocollo, partita_iva, ragione_sociale, identificativoEstero, pec) VALUES
										(		:var_1,
												:var_2,
												:var_3,
												:var_4,
												:var_5,
												:var_6,
												:var_7,
												:var_8,
												:var_9,
												:var_10);";
										$risultato_insert = $pdo->bindAndExec($sql_insert,$bind);
										if ($risultato_insert->rowCount()===0) { ?>
										<br>Errore nell'inserimento del partecipante <?= $value[2] ?>
										<?
										}
										}
									}
								}
							}
							if(strcmp($msg,'')!=0) echo '<script type="text/javascript">jalert("' . $msg . '"); </script>';
					}
					//$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$operazione = "UPDATE";
					$bind=array();
					$bind[":codice"] = $record["codice"];
					$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice ORDER BY codice";
					$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
					$print_form = false;
					if ($ris_lotti->rowCount()>0) {
						$deserti = 0;
						$nonAggiudicati = 0;
						if (isset($_GET["lotto"])) {
							$codice_lotto = $_GET["lotto"];
							$bind=array();
							$bind[":codice"] = $codice_lotto;
							$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice ORDER BY codice";
							$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
							if ($ris_lotti->rowCount()>0) {
								$print_form = true;
								$lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC);
								echo "<h2>" . $lotto["oggetto"] . "</h2>";
								if ($lotto["deserta"] == "S") echo "<strong>Lotto deserto</strong>";
								if ($lotto["deserta"] == "Y") echo "<strong>Lotto non aggiudicato</strong>";
							}
						} else {
							?>
							<table width="100%">
								<tr><th>Lotto</th><th width="10">Partecipanti</th></tr>
							<?
							while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
								$deserto = false;
								$nonAggiudicato = false;
								if ($lotto["deserta"]=="S") {
									$deserti++;
									$deserto = true;
								} else if ($lotto["deserta"] == "Y") {
									$nonAggiudicato = true;
									$nonAggiudicati++;
								}
								$bind=array();
								$bind[":codice"] = $record["codice"];
								$bind[":lotto"] = $lotto["codice"];
								$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_lotto = :lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
								$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
								?>
								<tr>
								<td>
									<a <?= ($deserto) ? 'style="background-color:#999"' : "" ?> class="submit_big" href ="edit.php?codice=<? echo $record["codice"] ?>&lotto=<? echo $lotto["codice"] ?>">
										<? echo $lotto["oggetto"] ?>
										<?= ($deserto) ? '<br><small>Deserto</small>' : "" ?>
									</a>
								</td>
								<td style="text-align:center">
									<strong style="font-size:24px"><? echo $ris_partecipanti->rowCount() ?></strong>
								</td></tr>
								<?
							}
							?>
							</table>
							<?
							if ($deserti == $ris_lotti->rowCount() && !$lock) {
								?>
								<form method="post" action="save_deserta.php" id="deserta">
									<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
									<input type="submit"  class="submit_big" style="background-color:#FF3300" value="Gara deserta">
								</form>

								<?
							} else if ($nonAggiudicati == $ris_lotti->rowCount() && !$lock) {
								?>
								<form method="post" action="save_deserta.php" id="deserta">
									<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
	                <input type="hidden" name="nonAggiudicata" value="TRUE">
									<input type="submit"  class="submit_big" style="background-color:#FFAA00" value="Gara non aggiudicata">
								</form>

								<?
							}
						}
					} else {
						$print_form = true;
						$codice_lotto = 0;
					}

					if ($print_form) {
						$bind=array();
						$bind[":codice"] = $record["codice"];
						$bind[":lotto"] = $codice_lotto;
						$sql_fasi = "SELECT * FROM b_2fase WHERE codice_gara = :codice AND codice_lotto = :lotto AND data_inizio <= now() AND data_fine > now() ";
						$ris_fasi = $pdo->bindAndExec($sql_fasi,$bind);
						if ($ris_fasi->rowCount()>0) {
							$print_form = false;
						}
						$sql_fasi = "SELECT * FROM b_aste WHERE codice_gara = :codice AND codice_lotto = :lotto AND data_inizio <= now() AND data_fine > now() ";
						$ris_fasi = $pdo->bindAndExec($sql_fasi,$bind);
						if ($ris_fasi->rowCount()>0) {
							$print_form = false;
						}
						if ($print_form) {
							if ($pdo->go("SELECT codice FROM r_partecipanti_Ifase WHERE codice_gara = :codice_gara",[":codice_gara"=>$record["codice"]])->rowCount() > 0) {
								$link = "edit.php?codice={$record["codice"]}&lotto={$codice_lotto}";
								$label = "Visualizza partecipanti fase attiva";
								if (!$ifase) {
									$link = "edit.php?codice={$record["codice"]}&lotto={$codice_lotto}&ifase=1";
									$label = "Visualizza partecipanti I Fase";
								}
								?>
								<a class="submit_big" href ="<?= $link ?>">
										<?= $label ?>
								</a>
								<?
							}
							if (!$lock) {
						?>

					<div class="box">
						<table width="100%">
							<tbody>
								<tr>
									<td style="text-align: center;vertical-align: middle;"><strong>Caricamento massivo dei partecipanti</strong></td>
								</tr>
							</tbody>
						</table>
						<form action="edit.php?codice=<? echo $codice ?>&lotto=<? echo $codice_lotto ?>" method="post" enctype="multipart/form-data">
						<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
						<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
						<table class="dettaglio" width="100%">
							<tbody>
							<tr>
								<td width="25%">
									<img src="/img/xls.png" alt="Modello partecipanti"/><a href="partecipanti.php" name="partecipanti_csv" download style="vertical-align:super">Modello CSV</a>
								</td>
								<td width="70%">
										<input type="file" name="partecipanti" id="file">
								</td>
								<td width="5%">
										<input type="submit" name="submit" value="Upload">
								</td>
							</tr>
							</tbody>
						</table>
									</form>
					</div>
					<? } ?>
  	    <form name="box" method="post" action="save.php" rel="validate">
        	<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
        	<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">

<script>
function aggiungi_partecipante() {
	if ($(".edit-box").length < 50) {
		aggiungi('tr_capogruppo.php?codice_gara=<? echo $record["codice"] ?>&codice_criterio=<? echo $record["criterio"] ?>','#partecipanti');
	} else {
		alert("Troppi partecipanti in modifica, procedere al salvataggio e riprovare");
	}
	return false;
}


function edit_partecipante(id) {
	if ($(".edit").length < 50) {
		data = "codice=" + id;
		$.ajax({
			type: "GET",
			url: "tr_capogruppo.php",
			dataType: "html",
			data: data,
			async:false,
			success: function(script) {
				$("#partecipante_"+id).replaceWith(script);
			}
		});
		f_ready();
		etichette_testo();
	} else {
		alert("Troppi partecipanti in modifica, procedere al salvataggio e riprovare");
	}
	return false;
}
</script>
                <div id="partecipanti">
                <?
								$bind=array();
								$bind[":codice"] = $record["codice"];
								$bind[":lotto"] = $codice_lotto;
								$table = "r_partecipanti";
								if ($ifase) $table = "r_partecipanti_Ifase";
						$sql = "SELECT * FROM {$table} WHERE codice_gara = :codice AND codice_lotto = :lotto AND codice_capogruppo = 0 AND ({$table}.conferma = TRUE OR {$table}.conferma IS NULL)";
						$ris_partecipanti = $pdo->bindAndExec($sql,$bind);

						if ($ris_partecipanti->rowCount()>0) {
							$cont_partecipante = 0;
							while ($record_partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
								$cont_partecipante++;
								$id_capogruppo = $record_partecipante["codice"];
								include("view_partecipante.php");
							}
						} else {
							$record_partecipante = get_campi("r_partecipanti");
							$id_capogruppo = "i_".rand();
							$new_line = true;
							include("tr_capogruppo.php");
						}
?>
					<div style="text-align:right">
						<a href="export.php?codice_gara=<?= $record["codice"] ?>&codice_lotto=<?= $codice_lotto ?><?= $ifase ? "&ifase=1" : "" ?>" target="_blank"><img src="/img/xls.png" style="vertical-align:middle" alt="Modello partecipanti"/> Esporta CSV</a>
					</div>
        </div>
				<? if (!$lock) { ?>

					<button class="aggiungi" onClick="aggiungi_partecipante();return false;"><img src="/img/add.png" alt="Aggiungi partecipante">Aggiungi partecipante</button>
                <input type="submit" class="submit_big" value="Salva">
                </form>
                <form method="post" action="save_deserta.php" id="deserta">
	                <input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
	                <input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
    	            <input type="submit"  class="submit_big" style="background-color:#FF3300" value="Gara deserta" onClick="return confirm('Confermi l\'operazione?');">
								</form>
								<form method="post" action="save_deserta.php" id="non-aggiudicata">
	                <input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
	                <input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
	                <input type="hidden" name="nonAggiudicata" value="TRUE">
    	            <input type="submit"  class="submit_big" style="background-color:#FFAA00" value="Gara non aggiudicata" onClick="return confirm('Confermi l\'operazione?');">
                </form>

                <?
            	   } else {
					 ?>
						<script>
	                        $("#contenuto_top :input").not('.espandi').prop("disabled", true);
                    	</script>
											<div>

											</div>
                     <?
				 }
				} else {
					echo "<h1>IMPOSSIBILE ACCEDERE</h1>";
					echo "<h3>Procedure di negoziazione aperte</h3>";
				}
			}
				 include($root."/gare/ritorna.php");
			} else {

				echo "<h1>Gara non trovata</h1>";

				}
			} else {

				echo "<h1>Gara non trovata</h1>";

				}

	?>


<?
	include_once($root."/layout/bottom.php");
	?>
