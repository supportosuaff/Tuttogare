<?
use Dompdf\Dompdf;
use Dompdf\Options;
include_once("../../../../config.php");


$edit = false;
$lock = true;
if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
	$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
	if ($codice_fase !== false) {
		$esito = check_permessi_gara($codice_fase,$_POST["estrazione"]["codice_gara"],$_SESSION["codice_utente"]);
		$edit = $esito["permesso"];
		$lock = $esito["lock"];
	}
	if (!$edit) {
		die();
	}
} else {
	die();
}
if ($edit)
{
	ini_set('max_execution_time', 600);
	ini_set('memory_limit', '-1');
	$estrazione = $_POST["estrazione"];
	$codice_gara = $estrazione["codice_gara"];
	$bind = array();
	$bind[":codice"] = $codice_gara;
	$strsql = "SELECT * FROM b_estrazioni WHERE codice_gara = :codice";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	$errore = false;
	if ($risultato->rowCount()>0) {
		?>
			<script>
				alert('Impossibile proseguire. Sorteggio già effettuato.');
			</script>
		<?
	} else {
		if (isset($estrazione["codice_bando"]) && $estrazione["codice_bando"] != 0) {
			$bind = array();
			$bind[":codice"] = $estrazione["codice_bando"];
			$sql = "SELECT b_operatori_economici.*
							FROM b_operatori_economici JOIN r_partecipanti_albo ON b_operatori_economici.codice = r_partecipanti_albo.codice_operatore
							JOIN b_utenti ON b_operatori_economici.codice_utente = b_utenti.codice
							WHERE r_partecipanti_albo.ammesso = 'S' AND b_utenti.attivo = 'S'
							AND r_partecipanti_albo.codice_bando = :codice GROUP BY b_operatori_economici.codice";
		} else {
			$estrazione["codice_bando"] = 0;
			$bind = array();
			$bind[":codice"] = $_SESSION["ente"]["codice"];
			$sql = "SELECT b_operatori_economici.*
							FROM b_operatori_economici JOIN b_utenti ON b_operatori_economici.codice_utente = b_utenti.codice
							JOIN r_enti_operatori ON b_utenti.codice = r_enti_operatori.cod_utente
							WHERE b_utenti.attivo = 'S' AND r_enti_operatori.cod_ente = :codice GROUP BY b_operatori_economici.codice";
		}
		$ris_operatori = $pdo->bindAndExec($sql,$bind);
		if ($ris_operatori->rowCount() > 0) {
				$totale_operatori = $ris_operatori->rowCount();
				$join = "";
				$where = "";
				$bind_general = array();

				$bind = array();
				$bind[":codice_gara"] = $codice_gara;
				$sql_cpv = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_gare ON b_cpv.codice = r_cpv_gare.codice WHERE r_cpv_gare.codice_gara = :codice_gara ORDER BY codice";
				$risultato_cpv = $pdo->bindAndExec($sql_cpv,$bind);
				if ($risultato_cpv->rowCount()>0) {
					$cpv = array();
					while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
						$cpv[] = $rec_cpv["codice"];
					}
					$string_cpv = implode(";",$cpv);
				}

				if (isset($estrazione["filtro_cpv"]) && $estrazione["filtro_cpv"] == "S") {
					if (isset($string_cpv) && $string_cpv != "") {
						$join .= " JOIN r_cpv_operatori ON b_operatori_economici.codice = r_cpv_operatori.codice_operatore ";
						$where .= " AND (";
						$categorie = explode(";",$string_cpv);
						foreach($categorie as $codice) {
							if ($codice != "") {
								$where .= "(r_cpv_operatori.codice = '" . $codice . "' ";
								if (strlen($codice)>2) {
									$diff = strlen($codice) - 2;
									for($i=1;$i<=$diff;$i++) {
										$where .= "OR r_cpv_operatori.codice = '".substr($codice,0,$i*-1)."' ";
									}
								}
							$where.=") OR ";
							}
						}
						$where = substr($where,0,-4);
						$where .= ")";
					}
				}

				$bind = array();
				$bind[":codice_gara"] = $codice_gara;
				$sql_soa = "SELECT * FROM b_qualificazione_lavori WHERE codice_gara = :codice_gara AND tipo = 'P'";
				$risultato_soa = $pdo->bindAndExec($sql_soa,$bind);
				if ($risultato_soa->rowCount() > 0) {
					$risultato_soa = $risultato_soa->fetchAll(PDO::FETCH_ASSOC);
				} else {
					$risultato_soa = array();
				}
				$bind = array();
				$bind[":codice_gara"] = $codice_gara;
				$sql_progettazione = "SELECT b_qualificazione_progettazione.importo, b_categorie_progettazione.group, b_categorie_progettazione.complessita, b_qualificazione_progettazione.codice_categoria
															FROM b_qualificazione_progettazione JOIN b_categorie_progettazione ON b_qualificazione_progettazione.codice_categoria = b_categorie_progettazione.codice
															WHERE b_qualificazione_progettazione.codice_gara = :codice_gara ORDER BY b_qualificazione_progettazione.importo, b_qualificazione_progettazione.codice DESC LIMIT 0,1";
				$risultato_progettazione = $pdo->bindAndExec($sql_progettazione,$bind);
				if ($risultato_progettazione->rowCount() > 0) {
					$risultato_progettazione = $risultato_progettazione->fetchAll(PDO::FETCH_ASSOC);
				} else {
					$risultato_progettazione = array();
				}
				if (isset($estrazione["filtro_soa"]) && ($estrazione["filtro_soa"] == "S" || $estrazione["filtro_soa"] == "C" || $estrazione["filtro_soa"] == "F")) {
					if (count($risultato_soa)>0) {
						$join .= " JOIN b_certificazioni_soa ON b_operatori_economici.codice = b_certificazioni_soa.codice_operatore ";
						$where .= " AND (";
						foreach($risultato_soa AS $rec_soa) {
							$importo_prevalente = $rec_soa["importo_base"];
							$where .= "(b_certificazioni_soa.codice_categoria = '".$rec_soa["codice_categoria"]."' ";
							if ($estrazione["filtro_soa"] == "C" && $importo_prevalente >= 150000) {
								$estrazione["anni"] = "";
								$sql_classifiche = "SELECT * FROM b_classifiche_soa WHERE ATTIVO = 'S'";
								$ris_classifiche = $pdo->query($sql_classifiche);
								if ($ris_classifiche->rowCount()>0) {
									$codici_classifica = array("-1");
									while($rec_classifica = $ris_classifiche->fetch(PDO::FETCH_ASSOC)) {
										if (($rec_classifica["massimo"] == 0)||($rec_classifica["massimo"]*1.20) >= $rec_soa["importo_base"]) $codici_classifica[] = $rec_classifica["codice"];
									}
									if (count($codici_classifica) > 0) $where.= " AND b_certificazioni_soa.codice_classifica IN (" . trim(implode(",",$codici_classifica),",") . ") ";
								}
							} else if ($estrazione["filtro_soa"] == "F" && $importo_prevalente < 150000 && !empty($estrazione["anni"])) {
								$sql_fatturati = "SELECT b_certificazioni_soa.codice
																	FROM b_fatturato_soa JOIN b_certificazioni_soa ON b_fatturato_soa.codice_attestazione = b_certificazioni_soa.codice
																	WHERE b_certificazioni_soa.codice_categoria = '".$rec_soa["codice_categoria"]."' AND b_fatturato_soa.anno >= '".(date("Y")-$estrazione["anni"])."'
																	GROUP BY b_fatturato_soa.codice_attestazione
																	HAVING SUM(b_fatturato_soa.fatturato) >= ".$importo_prevalente;
								$ris_fatturati = $pdo->query($sql_fatturati);
								$codice_attestazioni = array("-1");
								if ($ris_fatturati->rowCount() > 0) while ($codice_attestazioni[] = $ris_fatturati->fetch(PDO::FETCH_ASSOC)["codice"]);
								$where .= " AND (b_certificazioni_soa.codice_classifica > 0 OR b_certificazioni_soa.codice IN (" . trim(implode(",",$codice_attestazioni),",") . ")) ";
							} else {
								$where .= " AND b_certificazioni_soa.codice_classifica > 0 ";
							}

							$where .= ") OR ";
						}
						$where = substr($where,0,-4);
						$where .= ") ";
					}
				} else if (isset($estrazione["filtro_progettazione"]) && $estrazione["filtro_progettazione"] == "S" && !empty($estrazione["anni"])) {
					if (count($risultato_progettazione)>0) {
						$join .= " JOIN b_esperienze_progettazione ON b_operatori_economici.codice = b_esperienze_progettazione.codice_operatore ";
						$where .= " AND (";
						foreach($risultato_progettazione AS $rec_progettazione) {
							$importo_prevalente = $rec_progettazione["importo"];
							$where .= "(";
							$sql_esperienze = "SELECT b_esperienze_progettazione.codice_operatore
																FROM b_esperienze_progettazione JOIN b_categorie_progettazione ON b_esperienze_progettazione.codice_categoria = b_categorie_progettazione.codice
																WHERE b_categorie_progettazione.group = '".$rec_progettazione["group"]."'
																AND b_categorie_progettazione.complessita >= '".$rec_progettazione["complessita"]."'
																AND YEAR(b_esperienze_progettazione.data_inizio) >= '".(date("Y")-$estrazione["anni"])."'
																GROUP BY b_esperienze_progettazione.codice_operatore
																HAVING SUM((b_esperienze_progettazione.importo * b_esperienze_progettazione.percentuale)/100) >= ".$importo_prevalente;
							$ris_esperienze = $pdo->query($sql_esperienze);
							$codice_esperienze = array("-1");
							if ($ris_esperienze->rowCount() > 0) while ($codice_esperienze[] = $ris_esperienze->fetch(PDO::FETCH_ASSOC)["codice_operatore"]);
							$where .= " b_operatori_economici.codice IN (" . trim(implode(",",$codice_esperienze),",") . ") ";
							$where .= ") OR ";
						}
						$where = substr($where,0,-4);
						$where .= ") ";
					}
				}
				if ($estrazione["codice_bando"] === 0) {
					$bind_general[":codice_ente"] = $_SESSION["ente"]["codice"];
					$sql = "SELECT b_operatori_economici.*
									FROM b_operatori_economici JOIN b_utenti ON b_operatori_economici.codice_utente = b_utenti.codice
									JOIN r_enti_operatori ON b_utenti.codice = r_enti_operatori.cod_utente	";
					$sql.= $join;
					$sql.= "WHERE b_utenti.attivo = 'S'
									AND r_enti_operatori.cod_ente = :codice_ente ";
					$sql.= $where;
					$sql.= " GROUP BY b_operatori_economici.codice ORDER BY b_operatori_economici.codice ";
				} else {
					$bind_general[":codice_bando"] = $estrazione["codice_bando"];
					$sql = "SELECT b_operatori_economici.*
									FROM b_operatori_economici JOIN r_partecipanti_albo ON b_operatori_economici.codice = r_partecipanti_albo.codice_operatore
									JOIN b_utenti ON b_operatori_economici.codice_utente = b_utenti.codice ";
					$sql.= $join;
					$sql.= "WHERE r_partecipanti_albo.ammesso = 'S' AND b_utenti.attivo = 'S'
									AND r_partecipanti_albo.codice_bando = :codice_bando ";
					$sql.= $where;
					$sql.= " GROUP BY b_operatori_economici.codice ORDER BY b_operatori_economici.codice ";
				}

				$ris_operatori = $pdo->bindAndExec($sql,$bind_general);

				if ($ris_operatori->rowCount()>0) {
					$tmpOperatori = $ris_operatori->fetchAll(PDO::FETCH_ASSOC);
					$allOperatori = [];
					$esclusiManuali = [];
					if (!empty($_POST["esclusioni"])) {
						foreach($tmpOperatori AS $operatore) {
							if (in_array($operatore["codice_fiscale_impresa"],$_POST["esclusioni"]) === false) {
								$allOperatori[] = $operatore;
							} else {
								$esclusiManuali[] = $operatore;
							}
						}
					} else {
						$allOperatori = $tmpOperatori;
					}
					$operatori_filtrati = count($allOperatori);
					$sorteggio = true;
					if ($operatori_filtrati <= $estrazione["numero_partecipanti"]) {
						$sorteggio = false;
						?>
							<script>
								alert('Sorteggio non necessario. Numero degli operatori disponibili è inferiore o uguale al numero di partecipanti richiesti');
							</script>
						<?
					}

					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_estrazioni";
					$salva->operazione = "INSERT";
					$salva->oggetto = $estrazione;
					$codice_estrazione = $salva->save();
					if ($codice_estrazione !== false) {
						$operatori = array();
						if (!empty($esclusiManuali)) {
							foreach($esclusiManuali AS $operatore) {
								$relazione = array();
								$relazione["codice_estrazione"] = $codice_estrazione;
								$relazione["codice_operatore"] = $operatore["codice"];
								$relazione["codice_utente"] = $operatore["codice_utente"];
								$relazione["escluso"] = "S";
								$relazione["selezionato"] = "N";

								$salva = new salva();
								$salva->debug = false;
								$salva->codop = $_SESSION["codice_utente"];
								$salva->nome_tabella = "r_estrazioni";
								$salva->operazione = "INSERT";
								$salva->oggetto = $relazione;
								$codice_relazione = $salva->save();
							}
						}
						foreach($allOperatori AS $operatore) {

							$relazione = array();
							$relazione["codice_estrazione"] = $codice_estrazione;
							$relazione["codice_operatore"] = $operatore["codice"];
							$relazione["codice_utente"] = $operatore["codice_utente"];
							$relazione["escluso"] = "N";
							$relazione["selezionato"] = "N";
							if (!$sorteggio) $relazione["selezionato"] = "S";

							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "r_estrazioni";
							$salva->operazione = "INSERT";
							$salva->oggetto = $relazione;
							$codice_relazione = $salva->save();

							$count_filtro = 0;
							if (isset($estrazione["esclusioni"]) && $estrazione["esclusioni"] != "N") {
								$prosegui_filtro = true;
								if ($estrazione["codice_bando"] > 0) {
									$bind=array();
									$bind[":codice_bando"] = $estrazione["codice_bando"];
									$bind[":codice_gara"] = $estrazione["codice_gara"];
									$sql_filtro = "SELECT b_gare.codice FROM b_gare WHERE b_gare.codice_elenco = :codice_bando AND b_gare.tipo_elenco = 'albo' AND b_gare.codice <> :codice_gara AND b_gare.annullata = 'N' ";
									$ris_filtro = $pdo->bindAndExec($sql_filtro,$bind);
									if ($ris_filtro->rowCount() == 0) {
										$prosegui_filtro = false;
									}
								}
								if ($prosegui_filtro) {
									if ($estrazione["esclusioni"] == "A") {
										$bind = array();
										$bind[":codice"] = $operatore["codice"];
										$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
										$sql_filtro = "SELECT r_partecipanti.* ";
										$from_filtro = " FROM r_partecipanti JOIN b_gare ON r_partecipanti.codice_gara = b_gare.codice ";
										$where_filtro = " WHERE r_partecipanti.codice_operatore = :codice AND r_partecipanti.primo = 'S' AND b_gare.codice_gestore = :codice_ente AND b_gare.annullata = 'N' ";
										if ($estrazione["codice_bando"] != 0) {
											$bind[":codice_bando"] = $estrazione["codice_bando"];
											$where_filtro .= " AND r_partecipanti.codice_gara IN (SELECT b_gare.codice FROM b_gare WHERE b_gare.codice_elenco = :codice_bando AND b_gare.tipo_elenco = 'albo')";
										}
									} else {
										$bind = array();
										$bind[":codice"] = $operatore["codice_utente"];
										$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
										$sql_filtro = "SELECT r_inviti_gare.* ";
										$from_filtro = " FROM r_inviti_gare JOIN b_gare ON r_inviti_gare.codice_gara = b_gare.codice ";
										$where_filtro = " WHERE r_inviti_gare.codice_utente = :codice AND b_gare.codice_gestore = :codice_ente AND b_gare.annullata = 'N' ";
										if ($estrazione["codice_bando"] != 0) {
											$bind[":codice_bando"] = $estrazione["codice_bando"];
											$where_filtro .= " AND b_gare.codice_elenco = :codice_bando AND b_gare.tipo_elenco = 'albo' ";
											$where_filtro .= " AND r_inviti_gare.codice_gara IN (SELECT b_gare.codice FROM b_gare WHERE b_gare.codice_elenco = :codice_bando AND b_gare.tipo_elenco = 'albo')";
										}
									}

									if (!empty($estrazione["conteggio_rotazione"])) {
										if (strpos($estrazione["conteggio_rotazione"],"soa") !== false) {
											if (count($risultato_soa) > 0);
											$soa = $risultato_soa[0];
											$bind[":soa"] = $soa["codice_categoria"];
											$from_filtro .= " JOIN b_qualificazione_lavori ON b_gare.codice = b_qualificazione_lavori.codice_gara ";
											$where_filtro .= " AND b_qualificazione_lavori.codice_categoria = :soa AND b_qualificazione_lavori.tipo = 'P' ";
											if ($estrazione["conteggio_rotazione"] == "soa_classifica") {
												$sql_classifiche = "SELECT * FROM b_classifiche_soa WHERE ATTIVO = 'S' AND minimo <= :importo AND (massimo >= :importo OR massimo = 0)";
												$somma = $soa["importo_base"] + $soa["importo_oneri"];
												$ris_classifiche = $pdo->bindAndExec($sql_classifiche,array(":importo"=>$somma));
												if ($ris_classifiche->rowCount() > 0) {
													$class = $ris_classifiche->fetch(PDO::FETCH_ASSOC);
													$bind[":limite_minimo"] = $class["minimo"];
													$where_filtro .= " AND (b_qualificazione_lavori.importo_base + b_qualificazione_lavori.importo_oneri) >= :limite_minimo ";
													if ($class["massimo"] > 0) {
														$bind[":limite_massimo"] = $class["massimo"];
														$where_filtro .= " AND (b_qualificazione_lavori.importo_base + b_qualificazione_lavori.importo_oneri) <= :limite_massimo ";
													}
												}
											}
										} else if (!empty($string_cpv) && strpos($estrazione["conteggio_rotazione"],"cpv") !== false) {
											$cpv_length = substr($estrazione["conteggio_rotazione"], -1);
											$from_filtro .= " JOIN r_cpv_gare ON b_gare.codice = r_cpv_gare.codice_gara ";
											if (is_numeric($cpv_length)) {
												$where_filtro .= " AND (";
												$categorie = explode(";",$string_cpv);
												$n_cat = 0;
												foreach($categorie as $codice) {
													$n_cat++;
													if ($codice != "") {
														$codice = substr($codice,0,$cpv_length);
														$bind[":cat_".$n_cat] = $codice . '%';
														$where_filtro .= "(r_cpv_gare.codice LIKE :cat_".$n_cat ." ";
														$where_filtro.=") OR ";
													}
												}
												$where_filtro = substr($where_filtro,0,-4);
												$where_filtro .= ")";
											}
										} else if (!empty($risultato_progettazione) && strpos($estrazione["conteggio_rotazione"],"progettazione") !== false) {
											if (count($risultato_progettazione) > 0) {
												$bind[":progettazione"] = $risultato_progettazione[0]["codice_categoria"];
												$from_filtro .= " JOIN b_qualificazione_progettazione ON b_gare.codice = b_qualificazione_progettazione.codice_gara ";
												$where_filtro .= " AND b_qualificazione_progettazione.codice_categoria = :progettazione ";
											}
										}
									}
									$risultato_filtro = $pdo->bindAndExec($sql_filtro.$from_filtro.$where_filtro,$bind);
									$count_filtro = $risultato_filtro->rowCount();
								}
							}
							if ($codice_relazione != false) {
								$operatori[] = array("codice"=>$codice_relazione,"count"=>$count_filtro);
								$sql_update = "UPDATE r_estrazioni SET conteggio = :count WHERE codice = :codice";
								$pdo->bindAndExec($sql_update,array(":codice"=>$codice_relazione,":count"=>$count_filtro));
							}
						}
						$valore_massimo = 0;
						$operatori_valore = array();
						$operatori_inclusi = array();

						if ($sorteggio && count($operatori) > 0) {
							foreach($operatori AS $operatore) {
								if (!isset($valore_minimo)) $valore_minimo = $operatore["count"];
								if ($operatore["count"] < $valore_minimo) $valore_minimo = $operatore["count"];
								if ($operatore["count"] > $valore_massimo) $valore_massimo = $operatore["count"];
								if (!isset($operatori_valore[$operatore["count"]])) $operatori_valore[$operatore["count"]] = array();
								$operatori_valore[$operatore["count"]][]=$operatore;
							}
							$operatori_selezionati = array();
							if ($valore_minimo != $valore_massimo) {
								$valori = array_keys($operatori_valore);
								sort($valori,SORT_NUMERIC);
								$round = 0;
								foreach($valori as $valore) {
									if ($round > 0) $operatori_selezionati = array_merge($operatori_selezionati,$operatori_inclusi);
									$operatori_inclusi = $operatori_valore[$valore];
									if ((count($operatori_inclusi)+count($operatori_selezionati))>$estrazione["numero_partecipanti"]) {
										break; // Chiedere se numero partecipanti o sufficiente > 3
									} else if ((count($operatori_inclusi)+count($operatori_selezionati))==$estrazione["numero_partecipanti"]) {
										$operatori_selezionati = array_merge($operatori_selezionati,$operatori_inclusi);
										$operatori_inclusi = array();
										break;
									}
									$round++;
								}
							} else {
								$operatori_inclusi = $operatori;
							}
							if (count($operatori_inclusi)>0) {
								$codici_inclusi = array_map(function ($ar) {return $ar['codice'];}, $operatori_inclusi);
								$bind=array();
								$bind[":codice_estrazione"] = $codice_estrazione;
								$sql_escludi = "UPDATE r_estrazioni SET escluso = 'S' WHERE codice_estrazione = :codice_estrazione AND codice NOT IN (" . implode(",",$codici_inclusi) .")";
								$ris_escludi = $pdo->bindAndExec($sql_escludi,$bind);
								if (count($operatori_selezionati)>0) {
									?>
										<script>
											alert('Alcuni operatori sono stati selezionati automaticamente per il principio di rotazione.');
										</script>
									<?
									$codici_selezionati = array_map(function ($ar) {return $ar['codice'];}, $operatori_selezionati);
									$sql_seleziona = "UPDATE r_estrazioni SET escluso = 'N', selezionato = 'S' WHERE codice_estrazione = :codice_estrazione AND codice IN (" . implode(",",$codici_selezionati) .")";
									$ris_seleziona = $pdo->bindAndExec($sql_seleziona,$bind);
								}
								$i=0;
								shuffle($operatori_inclusi);
								$sequenza = range(1,count($operatori_inclusi));
								shuffle($sequenza);
								$sequenza = array_slice($sequenza,0,$estrazione["numero_partecipanti"]-count($operatori_selezionati));
								foreach($operatori_inclusi AS $operatore_selezionato) {
									$i++;
									$selezionato = "N";
									if (in_array($i,$sequenza,true)) $selezionato = "S";
									$bind = array();
									$bind[":selezionato"] = $selezionato;
									$bind[":identificativo"] = $i;
									$bind[":codice_estrazione"] = $codice_estrazione;
									$bind[":codice"] = $operatore_selezionato["codice"];
									$sql_includi = "UPDATE r_estrazioni SET selezionato = :selezionato, identificativo = :identificativo WHERE codice_estrazione = :codice_estrazione AND codice = :codice";
									$ris_includi = $pdo->bindAndExec($sql_includi,$bind);
								}
								$bind=array();
								$bind[":sequenza"] = implode(" - ",$sequenza);
								$bind[":codice_estrazione"] = $codice_estrazione;
								$sql_sequenza = "UPDATE b_estrazioni SET sequenza = :sequenza WHERE codice = :codice_estrazione";
								$ris_sequenza = $pdo->bindAndExec($sql_sequenza,$bind);
							} else if (count($operatori_selezionati) >0){
								$codici_selezionati = array_map(function ($ar) {return $ar['codice'];}, $operatori_selezionati);
								$bind=array();
								$bind[":codice_estrazione"] = $codice_estrazione;
								$sql_escludi = "UPDATE r_estrazioni SET escluso = 'S' WHERE codice_estrazione = :codice_estrazione AND codice NOT IN (" . implode(",",$codici_selezionati) .")";
								$ris_escludi = $pdo->bindAndExec($sql_escludi,$bind);
								$sql_seleziona = "UPDATE r_estrazioni SET selezionato = 'S' WHERE codice_estrazione = :codice_estrazione AND codice IN (" . implode(",",$codici_selezionati) .")";
								$ris_seleziona = $pdo->bindAndExec($sql_seleziona,$bind);
								?>
									<script>
										alert('Sorteggio non necessario. Il numero degli operatori estraibili è uguale al numero di partecipanti richiesti');
									</script>
								<?
							} else {
								$_POST["estrazione"]["salva"] = "N";
								?>
								<script>
									alert("Si è verificato un errore. Si prega di contattare l'Help Desk tecnico");
								</script>
								<?
							}
						}
					} else {
						$errore = true;
						?>
						<script>
							alert('Errore nel salvataggio dell\'estrazione');
						</script>
						<?
					}
				} else {
					$errore = true;
					?>
						<script>
							alert('Impossibile proseguire. Nessun operatore economico soddisfa i requisiti.');
						</script>
					<?
				}
			} else {
				$errore = true;
				?>
					<script>
						alert('Impossibile proseguire. Nessun operatore economico soddisfa i requisiti.');
					</script>
				<?
			}

		}
		if (!$errore) {

			$bind = array();
			$bind[":codice"] = $codice_gara;
			$bind[":codice_elenco"] = 0;
			$bind[":tipo_elenco"] = "";
			if (isset($estrazione["codice_bando"]) && $estrazione["codice_bando"] != 0) {
				$bind[":codice_elenco"] = $estrazione["codice_bando"];
				$bind[":tipo_elenco"] = "albo";
			}
			$sql = "UPDATE b_gare SET inviato_avviso = 'N', codice_elenco = :codice_elenco, tipo_elenco = :tipo_elenco WHERE codice = :codice";
			$update_inviato = $pdo->bindAndExec($sql,$bind);

			log_gare($_SESSION["ente"]["codice"],$codice_gara,"INSERT","Estrazione operatori economici da invitare");
			$html ="<html>";
			$html.= "<style>";
			$html.= "body { font-size:10px } table { width:100%; } ";
			$html.= "table td { padding:2px; border:1px solid #CCC } ";
			$html.= "table.no_border td { padding:2px; border:none; vertical-align:top;} ";
			$html.= "</style>";
			$html.= "<body>";
			ob_start();
			include("report.php");
			$report = ob_get_clean();
			echo $report;
			$html.=$report;
			$html.= "</body></html>";

			$percorso = $config["arch_folder"];

			$allegato["online"] = 'N';
			$allegato["codice_gara"] = $codice_gara;
			$allegato["codice_ente"] = $_SESSION["ente"]["codice"];

			$percorso .= "/".$allegato["codice_gara"];

			if (!is_dir($percorso)) mkdir($percorso,0777,true);
			$allegato["nome_file"] = $allegato["codice_gara"] . " - Verbale estrazione.".time().".pdf";
			$allegato["titolo"] = "Verbale Estrazione";

			$options = new Options();
			$options->set('defaultFont', 'Helvetica');
			$options->setIsRemoteEnabled(true);
			$dompdf = new Dompdf($options);
			$dompdf->loadHtml($html);
			$dompdf->setPaper('A4', 'portrait');
			$dompdf->set_option('defaultFont', 'Helvetica');
			$dompdf->render();
			$content = $dompdf->output();
			file_put_contents($percorso."/".$allegato["nome_file"],$content);

			if (file_exists($percorso."/".$allegato["nome_file"])) {
				$allegato["riferimento"] = getRealName($percorso."/".$allegato["nome_file"]);
				rename($percorso."/".$allegato["nome_file"],$percorso."/".$allegato["riferimento"]);
				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_allegati";
				$salva->operazione = "INSERT";
				$salva->oggetto = $allegato;
				$codice_allegato = $salva->save();

			}
			?>
			<script>
				$("#inviti").remove();
				$("#partecipanti-manuali").remove();
				$(".label_invitati").remove();
				<? if ($_POST["estrazione"]["salva"] == "S") echo '$("#pubblica").submit();'; ?>
			</script>
			<?
		}
		}
		?>
