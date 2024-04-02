<?
include_once("../../config.php");
include_once($root."/inc/funzioni.php");

$server = new SoapServer($root.'/api/services.wsdl',array('cache_wsdl'=>WSDL_CACHE_NONE));
$server->addFunction("getGare");
$server->handle();

function getGare($params) {
	global $pdo;
	$params = json_decode(json_encode($params),TRUE);
	$bind = array();
	$bind[":token"] = $params["token"];
	$sql_ente = "SELECT codice,dominio FROM b_enti WHERE token = :token";
	$ris_ente = $pdo->bindAndExec($sql_ente,$bind);
	global $config;
	if($ris_ente->rowCount()>0){
		$ente = $ris_ente->fetch(PDO::FETCH_ASSOC);
		$dominio=$config["protocollo"].$ente["dominio"];
		$bind = array();
		$bind[":codice_ente"] = $ente["codice"];
		$strsql  = "SELECT
		b_gare.codice,
		b_gare.codice_ente,
		b_gare.codice_gestore,
		b_gare.id,
		b_gare.stato,
		b_gare.numero_atto_indizione,
		b_gare.data_atto_indizione,
		b_gare.estremi_progetto,
		b_gare.data_validazione,
		b_gare.validatore,
		b_gare.rup,
		b_gare.struttura_proponente,
		b_gare.responsabile_struttura,
		b_gare.oggetto,
		b_gare.descrizione,
		b_gare.somme_disponibili,
		b_gare.prezzoBase,
		b_gare.cig,
		b_gare.cup,
		b_gare.nuts,
		b_gare.ribasso,
		b_gare.importoAggiudicazione,
		b_gare.data_pubblicazione,
		b_gare.data_accesso,
		b_gare.data_scadenza,
		b_gare.data_apertura,
		b_gare.numero_atto_commissione,
		b_gare.numero_atto_seggio,
		b_gare.data_atto_commissione,
		b_gare.data_atto_seggio,
		b_gare.allegato_atto_commissione,
		b_gare.allegato_atto_seggio,
		b_gare.numero_atto_esito,
		b_gare.data_atto_esito,
		b_gare.allegati_esito,
		b_gare.annullata,
		b_gare.numero_annullamento,
		b_gare.data_annullamento,
		b_gare.deserta,
		b_gare.flag_gestione_autonoma,
		b_tipologie.tipologia AS tipologia,
		b_criteri.criterio AS criterio,
		b_procedure.nome AS procedura,
		b_stati_gare.titolo AS fase,
		b_modalita.modalita as modalita
		FROM b_gare
		JOIN b_modalita on b_modalita.codice = b_gare.modalita
		JOIN b_stati_gare ON b_gare.stato = b_stati_gare.fase
		JOIN b_procedure ON b_gare.procedura = b_procedure.codice
		JOIN b_criteri ON b_gare.criterio = b_criteri.codice
		JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice
		WHERE (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
		AND (pubblica > 0)";
		if(isset($params['codice'])){
			$bind[":codice"] = $params["codice"];
			$strsql .=" AND b_gare.codice = :codice";
		}
		else {
			if(isset($params["cod_minimo"])&&(!isset($params["cod_massimo"]))) {
				$bind[":cod_minimo"] = $params["cod_minimo"];
				$strsql .=" AND b_gare.codice > :cod_minimo";
			}
			else if(isset($params["cod_massimo"])&&(!isset($params["cod_minimo"]))){
				$bind[":cod_massimo"] = $params["cod_massimo"];
			  $strsql .=" AND b_gare.codice < :cod_massimo";
			}
			else if(isset($params["cod_minimo"])&&isset($params["cod_massimo"])) {
				$bind[":cod_minimo"] = $params["cod_minimo"];
				$bind[":cod_massimo"] = $params["cod_massimo"];
				$strsql .= " AND b_gare.codice BETWEEN :cod_minimo AND :cod_massimo";
			}
		}
		if(isset($params['tipologia'])) {
			$bind[":tipologia"] = $params["tipologia"];
			$strsql .=" AND b_gare.tipologia = :tipologia";
		}
		if(isset($params['procedura'])) {
			$bind[":procedura"] = $params["procedura"];
			$strsql .=" AND b_gare.procedura = :procedura";
		}
		if(isset($params['modalita'])) {
			$bind[":modalita"] = $params["modalita"];
			$strsql .=" AND b_gare.modalita = :modalita";
		}
		if(isset($params['criterio'])) {
			$bind[":criterio"] = $params["criterio"];
			$strsql .=" AND b_gare.criterio = :criterio";
		}
		if(isset($params['cig'])) {
			$bind[":cig"] = $params["cig"];
			$strsql .=" AND b_gare.cig = :cig";
		}
		if(isset($params['cup'])) {
			$bind[":cup"] = $params["cup"];
			$strsql .=" AND b_gare.cup = :cup";
		}
		if(isset($params['stato'])) {
			$bind[":stato"] = $params["stato"];
			$strsql .= " AND b_gare.stato = :stato";
		}	else  {
			if(isset($params["stato_minimo"])&&(!isset($params["stato_massimo"]))) {
				$bind[":stato_minimo"] = $params["stato_minimo"];
				$strsql .=" AND b_gare.stato > :stato_minimo";
			}
			else if(isset($params["stato_massimo"])&&(!isset($params["stato_minimo"]))) {
				$bind[":stato_massimo"] = $params["stato_massimo"];
				$strsql .=" AND b_gare.stato < :stato_massimo";
			}
			else if(isset($params["stato_minimo"])&&isset($params["stato_massimo"])) {
				$bind[":stato_minimo"] = $params["stato_minimo"];
				$bind[":stato_massimo"] = $params["stato_massimo"];
				$strsql .= " AND b_gare.stato BETWEEN :stato_minimo AND :stato_massimo";
			}
		}
		if(isset($params['data'])){
			$data = $params['data'];
			$valid_data = array();
			$valid_data[] = "data_pubblicazione";
			$valid_data[] = "data_atto_indizione";
			$valid_data[] = "data_protocollo";
			$valid_data[] = "data_validazione";
			$valid_data[] = "data_accesso";
			$valid_data[] = "data_scadenza";
			$valid_data[] = "data_apertura";
			$valid_data[] = "data_atto_esito";
			$valid_data[] = "data_annullamento";
			$entrato = $data["typeDate"];
			if(isset($data["typeDate"]) && in_array($data["typeDate"], $valid_data) !== false) {
				$entrato = true;
				if(isset($data["startDate"])&&isset($data["endDate"])) {
					$bind[":startDate"] = $data["startDate"];
					$bind[":endDate"] = $data["endDate"];
					$condizione = " BETWEEN :startDate AND :endDate";
				} else if(isset($data["startDate"])&&!isset($data["endDate"])) {
					$bind[":startDate"] = $data["startDate"];
					$condizione = " > :startDate";
				}
				else if(isset($data["endDate"])&&!isset($data["startDate"])) {
					$bind[":endDate"] = $data["endDate"];
					$condizione = " < :endDate";
				}
				$bind[":typeDate"] = $data["typeDate"];
				$strsql .= " AND " . $data["typeDate"] . " " . $condizione;
			}
		}
		$strsql .= " GROUP BY b_gare.codice ";
		$strsql .= " ORDER BY codice DESC" ;

		$risultato = $pdo->bindAndExec($strsql,$bind);
		if($risultato->rowCount()>0){
			$json=array();
			while($record = $risultato->fetch(PDO::FETCH_ASSOC)){
				$json_row = array();
				$record["url"]= "https://". $dominio.makeurl('gare',$record["codice"],'dettagli');
				// $record["modalita_lotti"]=lotsMode($record["modalita_lotti"]);
				$json_row["gara"]=$record;
				$bind = array();
				$bind[":codice"] = $record["codice"];
				$sql_importi = "SELECT b_tipologie_importi.titolo as tipologia,b_importi_gara.importo_base,b_importi_gara.importo_oneri_ribasso,b_importi_gara.importo_oneri_no_ribasso,b_importi_gara.importo_personale FROM b_importi_gara JOIN b_tipologie_importi ON b_tipologie_importi.codice = b_importi_gara.codice_tipologia WHERE codice_gara = :codice";
				$ris_importi = $pdo->bindAndExec($sql_importi,$bind);
				if($ris_importi->rowCount()>0){
					$importi = array();
					while($record_importi = $ris_importi->fetch(PDO::FETCH_ASSOC))
						$importi[]=$record_importi;
					$json_row["importi"]=$importi;
				}
				//CPV
				$sql_cpv = "SELECT b_cpv.codice, b_cpv.codice_completo, b_cpv.descrizione FROM
										b_cpv JOIN r_cpv_gare ON b_cpv.codice = r_cpv_gare.codice
										WHERE r_cpv_gare.codice_gara = :codice ORDER BY codice";
				$risultato_cpv = $pdo->bindAndExec($sql_cpv,$bind);
				if ($risultato_cpv->rowCount()>0) {
					$cpv = array();
					while($record_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC))
						$cpv[]=$record_cpv;
					$json_row["cpv"]=$cpv;
				}
				//DATA ASTA
				$sql_aste = "SELECT MAX(data_fine) AS data_asta FROM b_aste WHERE codice_gara = :codice GROUP BY codice_gara";
				$ris_aste = $pdo->bindAndExec($sql_aste,$bind);
				if ($ris_aste->rowCount()>0) {
					$asta = array();
					while($record_asta = $ris_aste->fetch(PDO::FETCH_ASSOC))
						$asta[]=$record_asta;
					$json_row["asta"]=$asta;
				}
				//ALLEGATI
				$sql_allegati = "SELECT b_allegati.codice,b_allegati.sezione,b_allegati.nome_file,b_allegati.titolo,b_allegati.descrizione,b_allegati.timestamp FROM b_allegati WHERE codice_gara = :codice AND sezione = 'gara' AND online = 'S' AND hidden = 'N' ";
				$ris_allegati = $pdo->bindAndExec($sql_allegati,$bind);
				if($ris_allegati->rowCount()>0){
					$allegati = array();
					while ($record_allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)){
						$percorso_html = "/documenti/allegati/";
						$cartella = "";
						if ($record_allegato["cartella"]!="") $cartella = $record_allegato["cartella"] . "/";
						if ($record["codice"] != 0) {
							$percorso_html .= $record["codice"] . "/" . $cartella. $record_allegato["nome_file"];
						} else {
							$percorso_html .= $cartella.$record_allegato["nome_file"];
						}
						$record_allegato["url"]=$dominio.$percorso_html;
						$allegati[]=$record_allegato;
					}
					$json_row["allegati"]=$allegati;
				}
				//LOTTI
				$sql_lotti = "SELECT b_lotti.codice,b_lotti.cig,b_lotti.oggetto,b_lotti.descrizione,b_lotti.ulteriori_informazioni,b_lotti.cpv,b_lotti.importo_base,b_lotti.importo_oneri_ribasso,b_lotti.importo_oneri_no_ribasso,b_lotti.importo_personale,b_lotti.soglia_anomalia,b_lotti.media,b_lotti.scarto_medio,b_lotti.ribasso,b_lotti.importoAggiudicazione,b_lotti.durata,b_lotti.unita_durata,b_lotti.numero_sorteggio,b_lotti.data_sorteggio,b_lotti.annullata,b_lotti.numero_annullamento,b_lotti.data_annullamento,b_lotti.deserta
											FROM b_lotti WHERE codice_gara = :codice ORDER BY codice ";
				$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
				if($ris_lotti->rowCount()>0){
					$lotto = array();
					while($record_lotti = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
						if ($record["stato"] >= 4) {
							$partecipanti = array();
							//NESSUN LOTTO
							$sql_partecipanti = "SELECT
							r_partecipanti.codice,
							r_partecipanti.partita_iva,
							r_partecipanti.ragione_sociale,
							r_partecipanti.identificativoEstero,
							r_partecipanti.pec,
							r_partecipanti.tipo,
							r_partecipanti.primo,
							r_partecipanti.anomalia,
							r_partecipanti.anomalia_facoltativa,
							r_partecipanti.verifica,
							r_partecipanti.ammesso,
							r_partecipanti.escluso,
							r_partecipanti.motivazione,
							r_partecipanti.motivazione_anomalia,
							r_partecipanti.codice_operatore,
							r_partecipanti.codice_utente,
							b_utenti.email,
							b_utenti.pec,
							b_operatori_economici.indirizzo_legale,
							b_operatori_economici.citta_legale,
							b_operatori_economici.provincia_legale,
							b_operatori_economici.regione_legale,
							b_operatori_economici.stato_legale,
							b_operatori_economici.indirizzo_operativa,
							b_operatori_economici.citta_operativa,
							b_operatori_economici.provincia_operativa,
							b_operatori_economici.regione_operativa,
							b_operatori_economici.stato_operativa
							FROM r_partecipanti
							LEFT JOIN b_operatori_economici ON r_partecipanti.codice_operatore = b_operatori_economici.codice
							LEFT JOIN b_utenti ON r_partecipanti.codice_utente = b_utenti.codice
							WHERE codice_gara = :codice AND codice_lotto = ".$record_lotti["codice"]." AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
							$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,$bind);
							if ($ris_partecipanti->rowCount()>0) {
								while($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)){
									$sql_partecipanti_gruppo = "SELECT
									r_partecipanti.codice,
									r_partecipanti.partita_iva,
									r_partecipanti.ragione_sociale,
									r_partecipanti.identificativoEstero,
									r_partecipanti.pec,
									r_partecipanti.tipo,
									r_partecipanti.codice_operatore,
									r_partecipanti.codice_utente,
									b_utenti.email,
									b_utenti.pec,
									b_operatori_economici.indirizzo_legale,
									b_operatori_economici.citta_legale,
									b_operatori_economici.provincia_legale,
									b_operatori_economici.regione_legale,
									b_operatori_economici.stato_legale,
									b_operatori_economici.indirizzo_operativa,
									b_operatori_economici.citta_operativa,
									b_operatori_economici.provincia_operativa,
									b_operatori_economici.regione_operativa,
									b_operatori_economici.stato_operativa
									FROM r_partecipanti
									LEFT JOIN b_operatori_economici ON r_partecipanti.codice_operatore = b_operatori_economici.codice
									LEFT JOIN b_utenti ON r_partecipanti.codice_utente = b_utenti.codice
									WHERE codice_gara = :codice AND codice_lotto = ".$record_lotti["codice"]." AND codice_capogruppo = " . $partecipante["codice"];
									$ris_partecipanti_gruppo = $pdo->bindAndExec($sql_partecipanti_gruppo,$bind);
									if ($ris_partecipanti_gruppo->rowCount()>0) {
										$partecipante["raggruppamento"] = array();
										while ($record_partecipante = $ris_partecipanti_gruppo->fetch(PDO::FETCH_ASSOC)) {
											$partecipante["raggruppamento"][] = $record_partecipante;
										}
									}
									$partecipanti[] = $partecipante;
								}
							}
							if(!empty($partecipanti)) $record_lotti["partecipanti"]=$partecipanti;
						}
						$lotto[]=$record_lotti;
					}
					$json_row["lotti"]=$lotto;
				}
				//AVVISI
				if($params["avvisi"]&&$params["avvisi"]===true){
					$sql_avvisi  = "SELECT b_avvisi.codice,b_avvisi.titolo,b_avvisi.testo,b_avvisi.data,b_avvisi.data_scadenza FROM b_avvisi WHERE codice_gara = :codice ORDER BY data DESC,  timestamp DESC ";
					$ris_avvisi = $pdo->bindAndExec($sql_avvisi,$bind);
					if($ris_avvisi->rowCount()>0){
						$avviso = array();
						while($record_avvisi = $ris_avvisi->fetch(PDO::FETCH_ASSOC)){
							$record_avvisi["testo"]=htmlspecialchars($record_avvisi["testo"],ENT_QUOTES);
							$record_avvisi["url"]=$dominio."/gare/avvisi/dettaglio.php?cod=".$record_avvisi["codice"];
							$avviso[]=$record_avvisi;
						}
						$json_row["avvisi"]=$avviso;
					}
				}
				//RISPOSTE QUESITI
				if($params["quesiti"]&&$params["quesiti"]===true){
					$sql_quesiti = "SELECT b_risposte.quesito, b_risposte.testo AS risposta, b_quesiti.timestamp AS data_quesito, b_risposte.timestamp AS data_risposta FROM b_quesiti LEFT JOIN b_risposte ON b_quesiti.codice = b_risposte.codice_quesito WHERE b_quesiti.attivo = 'S' AND b_quesiti.codice_gara = :codice ORDER BY b_quesiti.timestamp";
					$ris_quesiti = $pdo->bindAndExec($sql_quesiti,$bind);
					if ($ris_quesiti->rowCount()>0) {
						$quesito = array();
						while($record_quesiti = $ris_quesiti->fetch(PDO::FETCH_ASSOC))
							$quesito[]=$record_quesiti;
						$json_row["quesiti"]=$quesito;
					}
				}
				// PARTECIPANTI
				if ($record["stato"] >= 4) {
					if($ris_lotti->rowCount()==0){
						$partecipanti = array();
						//NESSUN LOTTO
						$sql_partecipanti = "SELECT
						r_partecipanti.codice,
						r_partecipanti.partita_iva,
						r_partecipanti.ragione_sociale,
						r_partecipanti.identificativoEstero,
						r_partecipanti.pec,
						r_partecipanti.tipo,
						r_partecipanti.primo,
						r_partecipanti.anomalia,
						r_partecipanti.anomalia_facoltativa,
						r_partecipanti.verifica,
						r_partecipanti.ammesso,
						r_partecipanti.escluso,
						r_partecipanti.motivazione,
						r_partecipanti.motivazione_anomalia,
						r_partecipanti.codice_operatore,
						r_partecipanti.codice_utente,
						b_utenti.email,
						b_utenti.pec,
						b_operatori_economici.indirizzo_legale,
						b_operatori_economici.citta_legale,
						b_operatori_economici.provincia_legale,
						b_operatori_economici.regione_legale,
						b_operatori_economici.stato_legale,
						b_operatori_economici.indirizzo_operativa,
						b_operatori_economici.citta_operativa,
						b_operatori_economici.provincia_operativa,
						b_operatori_economici.regione_operativa,
						b_operatori_economici.stato_operativa
						FROM r_partecipanti
						LEFT JOIN b_operatori_economici ON r_partecipanti.codice_operatore = b_operatori_economici.codice
						LEFT JOIN b_utenti ON r_partecipanti.codice_utente = b_utenti.codice
						WHERE codice_gara = :codice AND codice_lotto = 0 AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
						$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,$bind);
						if ($ris_partecipanti->rowCount()>0) {
							while($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)){
								$sql_partecipanti_gruppo = "SELECT
								r_partecipanti.codice,
								r_partecipanti.partita_iva,
								r_partecipanti.ragione_sociale,
								r_partecipanti.identificativoEstero,
								r_partecipanti.pec,
								r_partecipanti.tipo,
								r_partecipanti.codice_operatore,
								r_partecipanti.codice_utente,
								b_utenti.email,
								b_utenti.pec,
								b_operatori_economici.indirizzo_legale,
								b_operatori_economici.citta_legale,
								b_operatori_economici.provincia_legale,
								b_operatori_economici.regione_legale,
								b_operatori_economici.stato_legale,
								b_operatori_economici.indirizzo_operativa,
								b_operatori_economici.citta_operativa,
								b_operatori_economici.provincia_operativa,
								b_operatori_economici.regione_operativa,
								b_operatori_economici.stato_operativa
								FROM r_partecipanti
								LEFT JOIN b_operatori_economici ON r_partecipanti.codice_operatore = b_operatori_economici.codice
								LEFT JOIN b_utenti ON r_partecipanti.codice_utente = b_utenti.codice
								WHERE codice_gara = :codice AND codice_lotto = 0 AND codice_capogruppo = " . $partecipante["codice"];
								$ris_partecipanti_gruppo = $pdo->bindAndExec($sql_partecipanti_gruppo,$bind);
								if ($ris_partecipanti_gruppo->rowCount()>0) {
									$partecipante["raggruppamento"] = array();
									while ($record_partecipante = $ris_partecipanti_gruppo->fetch(PDO::FETCH_ASSOC)) {
										$partecipante["raggruppamento"][] = $record_partecipante;
									}
								}
								$partecipanti[] = $partecipante;
							}
						}
						if(!empty($partecipanti)) $json_row["partecipanti"]=$partecipanti;
					}
					// INVITATI
					$rec_oe_invitati = [];
					$sql_oe_invitati = "SELECT partita_iva, ragione_sociale, pec, b_operatori_economici.codice_utente, b_operatori_economici.codice AS codice_operatore FROM b_operatori_economici JOIN b_utenti ON b_utenti.codice = b_operatori_economici.codice_utente JOIN r_inviti_gare ON r_inviti_gare.codice_utente = b_utenti.codice WHERE r_inviti_gare.codice_gara = :codice_gara";
					$ris_oe_invitati = $pdo->bindAndExec($sql_oe_invitati, array(':codice_gara' => $record["codice"]));
					if($ris_oe_invitati->rowCount() > 0) {
						$rec_oe_invitati = array_merge($rec_oe_invitati,$ris_oe_invitati->fetchAll(PDO::FETCH_ASSOC));
					}
					
					$sql_manuali = "SELECT * FROM temp_inviti WHERE codice_gara = :codice_gara AND attivo = 'S'";
					$ris_manuali = $pdo->bindAndExec($sql_manuali,array(':codice_gara' => $record["codice"]));
					if($ris_manuali->rowCount() > 0) $rec_oe_invitati = array_merge($rec_oe_invitati,$ris_manuali->fetchAll(PDO::FETCH_ASSOC));
					if (count($rec_oe_invitati) > 0) {
						$json_row["invitati"]=[];
						foreach($rec_oe_invitati AS $invitato) {
							$tmp = [];
							$tmp["partita_iva"] = (!empty($invitato["partita_iva"])) ? $invitato["partita_iva"] : "";
							$tmp["ragione_sociale"] = (!empty($invitato["ragione_sociale"])) ? $invitato["ragione_sociale"] : "";
							$tmp["pec"] = (!empty($invitato["pec"])) ? $invitato["pec"] : "";
							$tmp["codice_operatore"] = (!empty($invitato["codice_operatore"])) ? $invitato["codice_operatore"] : "";
							$tmp["codice_utente"] = (!empty($invitato["codice_utente"])) ? $invitato["codice_utente"] : "";
							$json_row["invitati"][] = $tmp;
						}
					}
				}
				$json[]=$json_row;
			}
		}
	}
	return json_encode($json,JSON_HEX_TAG | JSON_UNESCAPED_SLASHES);
}

function lotsMode($value){
	$output='';
	switch($value){
		case '0':
		$output='Libera';
		break;
		case '1':
		$output='Lotto Singolo';
		break;
		case '2':
		$output='Tutti i lotti';
		break;
	}
	return $output;
}
?>
