<?
 if (isset($record)) {
		$_SESSION["gara"] = $record;
		$bind = array();
		$bind[":codice"] = $record["codice"];
		$sql = "SELECT opzione FROM b_opzioni_selezionate WHERE codice_gara = :codice AND opzione IN (SELECT codice FROM b_opzioni WHERE codice_gruppo = 40)";
		$ris = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount() > 0) {
			$opzione = $ris->fetch(PDO::FETCH_ASSOC);
			if ($opzione["opzione"] == "58") {
	?>
	<h1>ELENCO PREZZI</h1>

	<? if (!$lock) { ?>
		<div class="box">
			<table width="100%">
				<tbody>
					<tr>
						<td style="text-align: center;vertical-align: middle;"><strong>Caricamento massivo dell'elenco dei prezzi</strong></td>
					</tr>
				</tbody>
			</table>
			<form action="edit.php?codice=<? echo $codice ?>" rel="validate" method="post" enctype="multipart/form-data">
			<input type="hidden" name="codice_gara" value="<? echo $codice; ?>">
			<table class="dettaglio" width="100%">
				<tbody>
				<tr>
					<td width="25%">
						<img src="/img/xls.png" alt="Modello elenco prezzi"/><a href="/gare/elenco_prezzi/modello.php" style="vertical-align:super">Modello CSV</a>
					</td>
								<?
								$bind = array();
								$bind[":codice"] = $record["codice"];
								$strsql = "SELECT * FROM b_lotti WHERE codice_gara = :codice";
								$ris_lotti = $pdo->bindAndExec($strsql,$bind);
								$lotti_prezzi = array();
								if ($ris_lotti->rowCount() > 0) {
									?>
									<td width="25%">
											<select rel="S;0;0;N" title="Lotto" name="codice_lotto">
												<?
									while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
										echo "<option name='lotto' value='".$lotto["codice"]."'>".$lotto["oggetto"]."</option>";
									}
									?>
										</select>
									</td>
									<?
								}
								?>
						<td width="50%">
									<input type="file" rel="S;0;0;A" title="File" name="elenco_prezzi" id="file"></td>
						<td>
						<input type="submit" name="submit" value="Upload">
					</td>
				</tr>
				</tbody>
			</table>
		</form>
		</div>
					<?
					if (isset($_POST["submit"])) {
						$codice_lotto = 0;
						if (isset($_POST["codice_lotto"])) $codice_lotto = $_POST["codice_lotto"];
							$upload_path = $config["chunk_folder"]."/";
							$allowed_filetypes = array('.csv');
							$filename = $_FILES["elenco_prezzi"]["name"];
							$ext = substr($filename, strpos($filename, '.'), strlen($filename) - 1);
							$msg='';
							if (!in_array($ext, $allowed_filetypes)) $msg = ('Il file non è valido');
							if (move_uploaded_file($_FILES["elenco_prezzi"]["tmp_name"], $upload_path . $filename)){

								ini_set('auto_detect_line_endings',TRUE);
								$file = fopen($upload_path . $filename, "r");
								while(! feof($file))
									$partecipanti[]=fgetcsv($file,0,';');
								fclose($file);
								$bind = array();
								$bind[":codice"] = $_SESSION["gara"]["codice"];
								$sql_opzione = "SELECT opzione FROM b_opzioni_selezionate WHERE codice_gara = :codice AND opzione IN (SELECT codice FROM b_opzioni WHERE codice_gruppo = 23)";
								$ris_opzione = $pdo->bindAndExec($sql_opzione,$bind);
								if ($ris_opzione->rowCount()>0) {
									$opzioni_sel = $ris_opzione->fetch(PDO::FETCH_ASSOC);
									$tipo = $opzioni_sel["opzione"];
								}

								foreach($partecipanti as $key=>$value) {
									if($key!=0 &&$value!=null){
										if(($tipo==29)&&strcasecmp($value[0],"corpo")!=0){
											$msg .= "Attenzione, presenti errori all'interno del file riguardo il lotto ".$value[1]."\\n";
											break;
										}else if(($tipo==30)&&strcasecmp($value[0],"misura")!=0){
											$msg .= "Attenzione, presenti errori all'interno del file riguardo il lotto ".$value[1]."\\n";
											break;
										}else if((strcasecmp($value[0],"corpo")!=0)&&(strcasecmp($value[0],"misura")!=0)){
											$msg .= "Attenzione, presenti errori all'interno del file riguardo il lotto ".$value[1]."\\n";
											break;
										}
										$bind = array();
										$bind[":codice"] = $codice;
										$bind[":codice_lotto"] = $codice_lotto;
										$bind[":tipo"] = $value[0];
										$bind[":descrizione"] = htmlspecialchars($value[1],ENT_QUOTES);
										$bind[":unita"] = $value[2];
										$bind[":quantita"] = str_replace(",",".",$value[3]);
							
										$sql_insert = "INSERT INTO b_elenco_prezzi (codice_gara, codice_lotto, tipo, descrizione, unita, quantita)
																	 VALUES(:codice,:codice_lotto,:tipo,:descrizione,:unita,:quantita)";
										$risultato_insert = $pdo->bindAndExec($sql_insert,$bind);
									}
								}
							}
							if(strcmp($msg,'')!=0) echo '<script type="text/javascript">alert("' . $msg . '"); </script>';
					}
					?>
		<script>
			function aggiungi_prezzo(id) {
				if ($(".prezzo").length < 50) {
					aggiungi('record.php','#elenco_prezzi_'+id);
				} else {
					alert("Troppi prezzi in modifica, procedere al salvataggio e riprovare");
				}
				return false;
			}
			function edit_prezzo(id) {
				if ($(".prezzo").length < 50) {
					data = "id=" + id;
					$.ajax({
						type: "POST",
						url: "record.php",
						dataType: "html",
						data: data,
						async:false,
						success: function(script) {
							$("#prezzo_"+id).replaceWith(script);
						}
					});
					f_ready();
					etichette_testo();
				} else {
					alert("Troppi prezzi in modifica, procedere al salvataggio e riprovare");
				}
				return false;
			}
		</script>
		<form name="box" method="post" action="/gare/elenco_prezzi/save.php" rel="validate">
			<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
			<div class="comandi">
				<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
			</div>
    <? } ?>
				<?
				$bind = array();
				$bind[":codice"] = $record["codice"];
				$strsql = "SELECT * FROM b_lotti WHERE codice_gara = :codice";
				$ris_lotti = $pdo->bindAndExec($strsql,$bind);
				$lotti_prezzi = array();
				if ($ris_lotti->rowCount() > 0) {
					while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
						$lotti_prezzi[] = array("codice"=>$lotto["codice"],"oggetto"=>$lotto["oggetto"]);
					}
				} else {
					$lotti_prezzi[] = array("codice"=>0,"oggetto"=>"");
				}
				foreach($lotti_prezzi as $lotto) {
					?>
					<div>
						<?
						if ($lotto["oggetto"]!="") echo "<h2>" . $lotto["oggetto"] . "</h2>";
            ?>
            <table width="100%">
              <thead>
                <tr>
                  <th width="10">Tipo</th>
                  <th>Descrizione</th>
                  <th width="50">U.d.m.</th>
                  <th width="50">Quantit&agrave;</th>
                  <th width="10"></th>
                </tr>
              </thead>
              <tbody id="elenco_prezzi_<? echo $lotto["codice"] ?>">
                <?
    						$bind = array();
    						$bind[":codice"] = $record["codice"];
    						$bind[":codice_lotto"] = $lotto["codice"];
    						$strsql = "SELECT * FROM b_elenco_prezzi WHERE codice_gara = :codice AND codice_lotto = :codice_lotto ORDER BY tipo, codice";
    						$ris_prezzi = $pdo->bindAndExec($strsql,$bind);
    						if ($ris_prezzi->rowCount() > 0) {
    							while($prezzo = $ris_prezzi->fetch(PDO::FETCH_ASSOC)) {
    								$id = $prezzo["codice"];
    								include("view.php");
    							}
    						}
    						?>
              </tbody>
            </table>
            <button class="aggiungi" onClick="aggiungi('record.php','#elenco_prezzi_<? echo $lotto["codice"] ?>');return false;"><img src="/img/add.png" alt="Aggiungi voce">Aggiungi voce</button>
					</div>
					<?
				}
				?>
		<? if (!$lock) { ?>
			<input type="submit" class="submit_big" value="Salva">
		</form>
		<? } ?>
		<? include($root."/gare/ritorna.php"); ?>
		<script>
			<? if ($lock) { ?>
				$("#contenuto_top :input").not('.espandi').prop("disabled", true);
			<? } ?>
		</script>
		<?
		} else {
			echo "<h1>Elenco prezzi non necessario</h1>";
		}
	} else {
		echo "<h1>Elenco prezzi non necessario</h1>";
	}
} else {
	echo "<h1>Gara non trovata</h1>";
}
?>
