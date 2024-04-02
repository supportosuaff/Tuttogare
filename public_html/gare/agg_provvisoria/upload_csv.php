<?
session_start();
include_once("../../../config.php");
include_once($root."/inc/funzioni.php");
if ((isset($_POST["codice_gara"])&&isset($_POST["codice_lotto"]))){
	$codice = $_POST["codice_gara"];
	$codice_lotto = $_POST["codice_lotto"];
	$upload_path = sys_get_temp_dir().DIRECTORY_SEPARATOR;
	$allowed_filetypes = array('.csv');
	$filename = $_FILES["punteggi"]["name"];
	$ext = substr($filename, strpos($filename, '.'), strlen($filename) - 1);
	$msg='';
	if (!in_array($ext, $allowed_filetypes)) $msg = ('File non compatibile.');
	if (move_uploaded_file($_FILES["punteggi"]["tmp_name"], $upload_path . $filename)){
		ini_set('auto_detect_line_endings',TRUE);
		$file = fopen($upload_path . $filename, "r");
		while(! feof($file))
			$punteggi_csv[]=fgetcsv($file,0,';');
		fclose($file);
	}
	$bind = array();
	$bind[":codice"] = $codice;
	$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
	$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
	$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
	if ($_SESSION["gerarchia"] > 0) {
		$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
		$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
	}
	$strsql .= " AND data_apertura <= now() ";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	$record = $risultato->fetch(PDO::FETCH_ASSOC);
	$msg='';
	foreach ($punteggi_csv as $key => $value) {
		if($key==0) continue;
		if($value!=null){
			$aggiorna_ammesso = false;
			$aggiorna_anomalia = false;
			$ammesso = $value[2];
			$motivazione = $value[3];
			$anomalia = $value[4];
			$motivazione_anomalia = $value[5];

			$bind = array();
			$bind[":codice_criterio"] = $record["criterio"];
			$sql = "SELECT * FROM b_criteri_punteggi WHERE codice_criterio = :codice_criterio ORDER BY ordinamento ";
			$ris_punteggi = $pdo->bindAndExec($sql,$bind);
			$ris_punteggi = $ris_punteggi->fetchAll(PDO::FETCH_ASSOC);

			if (is_numeric($value[0])) {

				$bind = array();
				$bind[":codice"] = $value[0];
				$bind[":codice_gara"] = $codice;
				$bind[":codice_lotto"] = $codice_lotto;
				$sql = "SELECT * FROM r_partecipanti WHERE codice = :codice AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) ";

				$ris_r_partecipanti = $pdo->bindAndExec($sql,$bind);
				if ($ris_r_partecipanti->rowCount()>0){
					while($record_partecipante = $ris_r_partecipanti->fetch(PDO::FETCH_ASSOC)){
						//AMMESSO
						if(strcasecmp($ammesso,"S")==0){
							$aggiorna_ammesso=true;
							if(strcasecmp($motivazione,'')!=0){
								$msg .= "<li>&egrave; stata inserita una motivazione di esclusione per il partecipante ammesso <strong>".$record_partecipante["ragione_sociale"]."</strong></li>";
							}
						}else if(strcasecmp($ammesso,"N")==0){
							$aggiorna_ammesso=true;
							if(strcasecmp($motivazione,'')==0){
								$aggiorna_ammesso=false;
								$msg .= "<li>non &egrave; stata inserita nessuna motivazione di esclusione per il partecipante escluso <strong>".$record_partecipante["ragione_sociale"]."</strong></li>";
							}
						}else{
							$msg .= "<li>errore nella colonna \"Ammesso\" per il partecipante <strong>".$record_partecipante["ragione_sociale"]."</strong></li>";
						}

						if($aggiorna_ammesso){
							$bind = array();
							$bind[":ammesso"] = strtoupper($ammesso);
							$bind[":motivazione"] = $motivazione;
							$bind[":codice"] = $record_partecipante["codice"];
							$bind[":utente_modifica"] = $_SESSION["codice_utente"];
							$sql_ammesso = "UPDATE r_partecipanti SET ammesso = :ammesso, motivazione = :motivazione, utente_modifica = :utente_modifica WHERE codice = :codice";
							$ris_ammesso = $pdo->bindAndExec($sql_ammesso,$bind);
						}

						//ANOMALIA
						if(strcasecmp($anomalia,"N")==0){
							$aggiorna_anomalia=true;
							if(strcasecmp($motivazione_anomalia,'')!=0){
								$msg .= "<li>&egrave; stata inserita una motivazione di anomalia per il partecipante <strong>".$record_partecipante["ragione_sociale"]."</strong></li>";
							}
						}else if(strcasecmp($anomalia,"S")==0){
							$aggiorna_anomalia=true;
							if(strcasecmp($motivazione_anomalia,'')==0){
								$aggiorna_anomalia=false;
								$msg .= "<li>non &egrave; stata inserita una motivazione per l'anomalia del partecipante <strong>".$record_partecipante["ragione_sociale"]."</strong></li>";
							}
						}else{
							$msg .= "<li>errore nella colonna \"Anomalia\" per il partecipante <strong>".$record_partecipante["ragione_sociale"]."</strong></li>";
						}

						if($aggiorna_anomalia){
							$sql_anomalia = "UPDATE r_partecipanti SET anomalia = '".strtoupper($anomalia)."', motivazione_anomalia = '".$motivazione_anomalia."' where codice = ".$record_partecipante["codice"];
							$ris_ammesso = $pdo->bindAndExec($sql_anomalia,$bind);
						}

						if (count($ris_punteggi)>0) {
							$contatore=6;
							foreach ($ris_punteggi as $punteggio) {

								$punti = $value[$contatore];
								$punti = str_replace(",",".",$punti);
								$punti = round($punti,3);
								$punti = number_format($punti,3,".","");
								$max_punti = 100;

								$bind = array();
								$bind[":codice_gara"] = $record_partecipante["codice_gara"];
								$bind[":codice_punteggio"] = $punteggio["codice"];

								$sql_punteggi = "SELECT SUM(punteggio) AS massimo FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara ";
								$sql_punteggi .= " AND punteggio_riferimento = :codice_punteggio GROUP BY punteggio_riferimento";
								$ris_punteggio = $pdo->bindAndExec($sql_punteggi,$bind);
								if ($ris_punteggio->rowCount()>0) {
									$arr_punti = $ris_punteggio->fetch(PDO::FETCH_ASSOC);
									$max_punti = $arr_punti["massimo"];
								} else {

									$bind = array();
									$bind[":codice_gara"] = $record["codice"];

									$sql_punteggi = "SELECT * FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara ";
									$ris_punteggio = $pdo->bindAndExec($sql_punteggi,$bind);
									if ($ris_punteggio->rowCount() > 0) $max_punti = 0;
								}

								//PUNTEGGI
								if(is_numeric($punti)&&($punti<=$max_punti)){
									$bind = array();
									$bind[":codice"] = $record_partecipante["codice"];
									$bind[":codice_gara"] = $record_partecipante["codice_gara"];
									$bind[":codice_lotto"] = $record_partecipante["codice_lotto"];
									$bind[":punteggio"] = $punteggio["codice"];

									$sql_punteggi  = "SELECT * FROM r_punteggi_gare WHERE codice_partecipante = :codice";
									$sql_punteggi .= " AND codice_gara = :codice_gara";
									$sql_punteggi .= " AND codice_lotto = :codice_lotto";
									$sql_punteggi .= " AND codice_punteggio = :punteggio";
									$ris_punteggio = $pdo->bindAndExec($sql_punteggi,$bind);
									if ($ris_punteggio->rowCount()>0) {
										$record_punteggi=$ris_punteggio->fetch(PDO::FETCH_ASSOC);
										$bind = array();
										$bind[":codice"] = $record_punteggi["codice"];
										$sql_delete = "DELETE FROM r_punteggi_gare WHERE codice = :codice";
										$ris_delete=$pdo->bindAndExec($sql_delete,$bind);
									}
									$bind = array();
									$bind[":codice_partecipante"] = $record_partecipante["codice"];
									$bind[":codice_punteggio"] = $punteggio["codice"];
									$bind[":codice_gara"] = $codice;
									$bind[":codice_lotto"] = $codice_lotto;
									$bind[":puntegggio"] = $punti;
									$bind[":utente_modifica"] =$_SESSION["codice_utente"];

									$sql_insert = "INSERT INTO `r_punteggi_gare`(`codice_partecipante`, `codice_punteggio`, `codice_gara`, `codice_lotto`, `punteggio`, `utente_modifica`)
																 VALUES ( :codice_partecipante, :codice_punteggio, :codice_gara, :codice_lotto, :puntegggio, :utente_modifica)";
									$ris_insert = $pdo->bindAndExec($sql_insert,$bind);
									$contatore++;
								}else{
									//PUNTI NON NUMERICO O MAX PUNTI SUPERATO
									$msg .= '<li>Punteggio: <strong>' . $punteggio["nome"] . '</strong> non numerico o superiore al massimo per <strong>'.$record_partecipante["ragione_sociale"].'</strong></li>';
									break;
								}
							}
						}
					}
				}else{
				//PARTECIPANTE INESISTENTE
					$msg .= '<li>partecipante '.$value[1].' inesistente</li>';
					break;
				}
			}
			else{
			//CODICE PARTECIPANTE NON NUMERICO
				$msg .= '<li>codice partecipante non numerico</li>';
				break;
			}
		}
	}
	if($codice_lotto==0) $url ='/gare/agg_provvisoria/edit.php?codice='.$codice;
	else $url ='/gare/agg_provvisoria/edit.php?codice='.$codice.'&codice_lotto='.$codice_lotto;

	if($msg!=''){
		include_once($root."/layout/top.php");
		echo '<h1>ERRORE IMPORTAZIONE PUNTEGGI</h1><div><ul>';
		echo $msg;
		echo '</ul></div><input type="button" class="ritorna_button submit_big" style="background-color:#999;" value="Ritorna al pannello" onClick="location.href=\''.$url.'\'"/>';

		include_once($root."/layout/bottom.php");
	}else{
		echo '<meta http-equiv="refresh" content="0;URL='.$url.'">';
	}
}
?>
