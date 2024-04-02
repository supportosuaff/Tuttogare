<?php

function connessione_pdo() {
	global $connessione_db_pdo;
	global $config;
	$connessione_db_pdo = new pdo('mysql:host='.$config["db_host"].';dbname='.$config["db_name"], $config["db_user"], $config["db_pass"]);
	$connessione_db_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connessione_db_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,TRUE);
	return $connessione_db_pdo;
}

function str_limit($value,$limit,$end="...") {
	if (mb_strwidth($value, 'UTF-8') <= $limit) {
		return $value;
	}
	return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')).$end;
}

function isRequired($field)
{
	global $rel;
	if(empty($rel)) {
		@session_start();
		$rel = $_SESSION["guue"]["rel"];
	}
	return in_array($field, $rel) ? "S" : "N";
}

function loadForm($f, $i = -1, $fv = "2_0_9_S3") {
	global $pdo;
	global $root;
	global $guue;
	global $rel;
	global $v_form;
	foreach ($f as $object) {
		if(is_array($object)) {
			if(!empty($object["index"])) {
				$index = $object["index"];
				unset($object["index"]);
				loadForm($object, $index, $fv);
			} else {
				loadForm($object, null, $fv);
			}
		} else {
			if (strpos($object, ".php") !== FALSE) {
				if($i > 0) $item = $i;
				include $root . '/guue/forms/' . $fv . "/" . $object;
			} else {
				echo $object;
			}
		}
	}
}

function echo_calendario($data) {
	$giorno = substr($data, 0, 2);
	$mese   = substr($data, 3, 2);
	$anno   = substr($data, 8, 2);
	$mesi   = array("01" => "GEN", "02" => "FEB", "03" => "MAR", "04" => "APR", "05" => "MAG", "06" => "GIU", "07" => "LUG", "08" => "AGO", "09" => "SET", "10" => "OTT", "11" => "NOV", "12" => "DEC");
	$html   = "<div class=\"calendario\">
							<div class=\"giorno\">".$giorno."</div>
							<div class=\"mese\">".$mesi[$mese]." ".$anno."</div>
						</div>";
	return $html;
}

function echo_intestazione($data, $titolo, $modulo, $codice, $collegamento = TRUE, $tuttogare = FALSE) {

	$href = "";

	if ($collegamento) {
		$href = "/".$modulo."/id".$codice."-".sanitize_string($titolo);
	}

	if ($tuttogare) $href = "https://gare.comune.roma.it" . $href;

	$giorno = substr($data, 0, 2);
	$mese   = substr($data, 3, 2);
	$anno   = substr($data, 8, 2);
	$mesi   = array("01" => "GEN", "02" => "FEB", "03" => "MAR", "04" => "APR", "05" => "MAG", "06" => "GIU", "07" => "LUG", "08" => "AGO", "09" => "SET", "10" => "OTT", "11" => "NOV", "12" => "DEC");
	$html   = "<div class=\"calendario\">
                                <div class=\"giorno\">".$giorno."</div>
								 <div class=\"mese\">".$mesi[$mese]." ".$anno."</div>
                                                                </div>";

	if ($href != "") {

		$html .= "<h2 class=\"titolo_news\"><a href=\"".str_replace('"', "", $href)."\">".$titolo."</a></h2>";

	} else {

		$html .= "<h1 class=\"titolo_news\">".$titolo."</h1>";

	}
	$html .= "<div class=\"clear\"></div>";

	return $html;

}

function sanitize_string($str, $replace = array(), $delimiter = '-') {
	if (!empty($replace)) {
		$str = str_replace((array) $replace, ' ', $str);
	}

	$str = utf8_encode($str);

	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$clean = preg_replace("/[^a-zA-Z0-9\.\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

	return $clean;
}

function normal_text($string) {
	$string = strip_tags($string);
	// $string = html_entity_decode($string,ENT_COMPAT | ENT_HTML401,"UTF-8");
	return $string;
}

function makeurl($modulo, $codice, $titolo) {

	$titolo = html_entity_decode($titolo);
	$titolo = sanitize_string($titolo, "'");

	if (strlen($titolo) > 200) {$titolo = substr($titolo, 0, 200);}
	$href = "/".$modulo."/id".$codice."-".$titolo;
	$href = str_replace(' ', "-", (preg_replace('!\s+!', ' ', $href)));
	return $href;
}

function replace4URL($stringa) {
	$stringa = str_replace('"', "", $stringa);
	$stringa = str_replace(' ', "-", $stringa);
	return $stringa;
}

function fattoriale($number) {
	if ($number < 2) {
		return 1;
	} else {
		return ($number*fattoriale($number-1));
	}
}

function normalizza($punteggi,$totale = 1,$decimali = 3)
{
	$coefficienti = array();
	$max_punteggio = max($punteggi);
	foreach ($punteggi as $codice_partecipante => $punteggio)
	{
		$coefficienti[$codice_partecipante] = $punteggio / $max_punteggio * $totale;
		$coefficienti[$codice_partecipante] = (float) truncate($coefficienti[$codice_partecipante],$decimali);
	}

	return $coefficienti;

}

function riparametrazione_assoluta($punteggi,$punti_disponibili,$decimali=3)
{
	$coefficienti = array();
	$max_punteggio = 0;
	$max_codice = 0;

	foreach ($punteggi as $codice_partecipante => $punteggio)
	{
		if ($punteggio > $max_punteggio)
		{
			$max_punteggio = $punteggio;
			$max_codice = $codice_partecipante;
		}
	}

	if ($punti_disponibili != $max_punteggio && $max_punteggio > 0) {
		$incremento = $punti_disponibili / $max_punteggio;
		$coefficienti[$max_codice] = truncate($punti_disponibili,$decimali);
		foreach ($punteggi as $codice_partecipante => $punteggio)
		{
			if ($codice_partecipante != $max_codice) {
				$coefficienti[$codice_partecipante] = truncate($punteggio * $incremento,$decimali);
				$coefficienti[$codice_partecipante] = truncate($coefficienti[$codice_partecipante],$decimali);
			}
		}
		return $coefficienti;
	} else {
		return $punteggi;
	}
}

function accedi($email, $password, $force = false, $iam = false) {
	global $pdo;
	$cryptpassw  = md5($password);
	$bind = array(":email"=>$email);
	$strsql = "SELECT b_utenti.*,b_gruppi.gerarchia, b_gruppi.id as tipo_utente
						 FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice
						 WHERE  b_utenti.email = :email
						 AND b_utenti.attivo='S'";
	$log_in	= 0;
	$abilitato = false;
	$risultato = $pdo->bindAndExec($strsql,$bind);
	$codice_ente_accesso = 0;

	if (isset($_SESSION["ente"])) $codice_ente_accesso = $_SESSION["ente"]["codice"];

	if ($risultato->rowCount() === 1) {
		if ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
			$ok = false;
			if (password_verify($cryptpassw,$record["password"]) && $record["tentativi"] < 5 && $record["scaduto"] != "S") $ok = true;
			if ($iam) {
				if ($password == $_SESSION["passwordIAM"]) $ok = true;
			}
			if ($ok) {
				$codice = $record["codice"];
				$admin  = FALSE;
				if ($record["tipo_utente"] == "SAD") {
					$admin     = TRUE;
					$abilitato = TRUE;
				} else if ($record["tipo_utente"] == "OPE" || $record["tipo_utente"] == "PRO") {
					if (isset($_SESSION["ente"])) {
						$bind=array(":codice_ente"=>$_SESSION["ente"]["codice"],":codice_utente"=>$record["codice"]);
						$codice_ente_accesso = $_SESSION["ente"]["codice"];
						$sql = "SELECT r_enti_operatori.* FROM r_enti_operatori JOIN b_enti ON r_enti_operatori.cod_ente = b_enti.codice
										WHERE b_enti.attivo = 'S' AND b_enti.codice = :codice_ente
										AND r_enti_operatori.cod_utente = :codice_utente ";
						$ris  = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount() > 0) {
							$abilitato = true;
						} else {
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $record["codice"];
							$salva->nome_tabella = "r_enti_operatori";
							$salva->operazione = "INSERT";
							$salva->oggetto = array("cod_ente"=>$_SESSION["ente"]["codice"],"cod_utente"=>$record["codice"]);
							if ($salva->save() != false) {
								$abilitato = true;
								$extended = true;
							}
						}
						if ($abilitato) {
							$sql_ope = "SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente ";
							$ris_ope = $pdo->bindAndExec($sql_ope,[":codice_utente"=>$record["codice"]]);
							if ($ris_ope->rowCount() === 1) {
								$rec_ope = $ris_ope->fetch(PDO::FETCH_ASSOC);
								if (!empty($extended)) {
									if (class_exists("syncERP")) {
										$syncERP = new syncERP();
										if (method_exists($syncERP,"sendOE")) {
											$syncERP->sendOE($rec_ope["codice"]);
										}
									}
								}
								$sql_inviti = "SELECT temp_inviti.*, b_gare.codice_gestore
												FROM temp_inviti JOIN b_gare ON temp_inviti.codice_gara = b_gare.codice
												WHERE (
													temp_inviti.pec = :pec
												) AND attivo = 'S' ";
												// OR temp_inviti.partita_iva = :partita_iva
												// OR temp_inviti.partita_iva = :codice_fiscale_impresa
								$ris_inviti = $pdo->bindAndExec($sql_inviti,[":pec"=>$record["pec"]]);
												// ,":partita_iva"=>$rec_ope["partita_iva"],":codice_fiscale_impresa"=>$rec_ope["codice_fiscale_impresa"]
								if ($ris_inviti->rowCount() > 0) {
									while($invito = $ris_inviti->fetch(PDO::FETCH_ASSOC)) {
										if (!empty($invito["codice_richiesta"])) {
											$partecipante = array();
											$partecipante["codice_gara"] = $invito["codice_gara"];
											$partecipante["codice_lotto"] =  $pdo->go("SELECT codice FROM b_lotti WHERE codice_gara = :codice",[":codice"=>$invito["codice_gara"]])->fetch(PDO::FETCH_COLUMN);
											$partecipante["codice_operatore"] = $rec_ope["codice"];
											$partecipante["codice_utente"] = $record["codice"];
											$partecipante["partita_iva"] = $rec_ope["codice_fiscale_impresa"];
											$partecipante["ragione_sociale"] = (!empty($rec_ope["ragione_sociale"])) ? $rec_ope["ragione_sociale"] : $invito["ragione_sociale"];
											$partecipante["pec"] = $record["pec"];
											$partecipante["identificativoEstero"] = $rec_ope["identificativoEstero"];
											$partecipante["conferma"] = "0";
											$partecipante["ammesso"] = 'S';
											$salva = new salva();
											$salva->debug = false;
											$salva->codop = -1;
											$salva->nome_tabella = "r_partecipanti";
											$salva->operazione = "INSERT";
											$salva->oggetto = $partecipante;
											$codice_partecipante = $salva->save();
											if ($codice_partecipante > 0) {
												$r_integrazione = array();
												$r_integrazione["codice_rdo"] = $invito["codice_richiesta"];
												$r_integrazione["codice_ente"] = $invito["codice_gestore"];
												$r_integrazione["codice_partecipante"] = $codice_partecipante;
												$r_integrazione["codice_utente"] = $record["codice"];
												$salva = new salva();
												$salva->debug = false;
												$salva->codop = -1;
												$salva->nome_tabella = "r_rdo_ad";
												$salva->operazione = "INSERT";
												$salva->oggetto = $r_integrazione;
												$salva->save();
											}
										}
										$tmp = array();
										$tmp["codice_gara"] = $invito["codice_gara"];
										$tmp["codice_utente"] = $record["codice"];
										$salva = new salva();
										$salva->debug = false;
										$salva->codop = -1;
										$salva->nome_tabella = "r_inviti_gare";
										$salva->operazione = "INSERT";
										$salva->oggetto = $tmp;
										$codice_invito = $salva->save();
										$pdo->bindAndExec("UPDATE temp_inviti SET attivo = 'N' WHERE codice = :codice_invito",[":codice_invito"=>$invito["codice"]]);
									}
								}
							}
						}
					}
				} else {
					if (isset($_SESSION["ente"])) {
						$bind=array(":codice_ente"=>$_SESSION["ente"]["codice"],":codice_utente_ente"=>$record["codice_ente"]);
						$sql = "SELECT b_enti.* FROM b_enti LEFT JOIN b_enti AS b_sua ON b_enti.sua = b_sua.codice ";
						$sql .= "WHERE (b_enti.attivo = 'S' OR b_sua.attivo = 'S') AND ";
						$sql .= "(b_enti.codice = :codice_ente OR b_sua.codice = :codice_ente ) AND ";
						$sql .= "b_enti.codice = :codice_utente_ente ";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount() > 0) {
							if ($_SESSION["ente"]["ambienteTest"] == "N") {
								$sql = "SELECT * FROM b_login_hash WHERE codice_utente = :codice_utente ";
								$ris_hash = $pdo->bindAndExec($sql,array(":codice_utente"=>$record["codice"]));
								if ($ris_hash->rowCount() > 0) {
									$last_login = $ris_hash->fetch(PDO::FETCH_ASSOC);
									if ((strtotime($last_login["timestamp"]) < strtotime('-60 minutes', time())) || $force) {
									 $abilitato = true;
									 $bind = array(":codice_utente"=>$record["codice"]);
									 $pdo->bindAndExec("DELETE FROM b_login_hash WHERE codice_utente = :codice_utente",$bind);
									}
									$log_in = -1;
								} else {
									$abilitato = true;
								}
								if ($abilitato) {
									$_SESSION["loginHash"] = sha1($record["codice"].time());
									$bind = array(":codice_utente"=>$record["codice"],":hash"=>$_SESSION["loginHash"]);
									$pdo->bindAndExec("INSERT INTO b_login_hash (codice_utente, hash) VALUES (:codice_utente, :hash)",$bind);
								}
							} else {
								$abilitato = true;
							}
						}
					} else if ($record["codice_ente"] == 0) {
						$abilitato = true;
					}
				}

				if ($abilitato) {
					unset($record["password"]);
					unset($record["user_cup"]);
					unset($record["pass_cup"]);
					$log_in  = array("codice_utente" => $record["codice"], "nome_utente" => $record["nome"]." ".$record["cognome"], "admin" => $admin, "gerarchia" => $record["gerarchia"], "tipo_utente" => $record["tipo_utente"], "record_utente" => $record);

					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $record["codice"];
					$salva->nome_tabella = "b_log_accessi";
					$salva->operazione = "INSERT";
					$salva->oggetto = array("codice_ente"=>$codice_ente_accesso,"ip"=>$_SERVER["REMOTE_ADDR"]);
					$codice_log = $salva->save();

					$salva->nome_tabella = "b_check_sessions";
					$salva->operazione = "INSERT";
					$salva->oggetto = array("codice_utente"=>$record["codice"],"sessionID"=>simple_encrypt(session_id(),$config["enc_key"]),"agent"=>base64_encode($_SERVER ['HTTP_USER_AGENT']),"ip"=>$_SERVER["REMOTE_ADDR"]);
					$salva->save();

					$sql = "UPDATE b_utenti SET tentativi = 0, bot_verify = 'S', last_login = now() WHERE codice = :codice_utente ";
					$ris = $pdo->bindAndExec($sql,array(":codice_utente"=>$record["codice"]));
				}
			} else {
				$sql = "UPDATE b_utenti SET tentativi = :tentativi WHERE codice = :codice_utente ";
				$tentativi = $record["tentativi"] + 1;
				$log_in = array("tentativi"=>$tentativi,"scaduto"=>$record["scaduto"]);
				$ris = $pdo->bindAndExec($sql,array(":codice_utente"=>$record["codice"],":tentativi"=>$tentativi));
			}
		}
		$risultato->closeCursor();
	}
	if (!$abilitato) {
		$sql_injection = "/(ALTER|CREATE|DELETE|DROP|EXEC(UTE)|INSERT|MERGE|SELECT|UPDATE|UNION|HEX)+/i";
		$char_injection = "/(((\%27)|(\'))|((\%47)|(\/))|((\#)|(\%43)))/";
		if (preg_match($sql_injection,$email) && preg_match($char_injection,$email)) $email = "INJECTION";

		$salva = new salva();
		$salva->debug = false;
		$salva->codop = -1;
		$salva->nome_tabella = "b_log_tentativi";
		$salva->operazione = "INSERT";
		$salva->oggetto = array("codice_ente"=>$codice_ente_accesso,"username"=>$email,"ip"=>$_SERVER["REMOTE_ADDR"]);
		$codice_log = $salva->save();

	}
	return $log_in;
}

if (! function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( !array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( !array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}


function check_utente($expired=false) {
	if (isset($_SESSION["codice_utente"])) {
		global $pdo, $root;
		$abilitato = false;
		$strsql  = "SELECT b_utenti.*,b_gruppi.gerarchia, b_gruppi.id as tipo_utente ";
		$strsql .= "FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice ";
		$strsql .= "WHERE b_utenti.codice = :codice_utente ";
		$strsql .= "AND b_utenti.attivo='S'";
		$bind = array(':codice_utente' => $_SESSION["codice_utente"]);
		$risultato      = $pdo->bindAndExec($strsql,$bind);//invia la query contenuta in $strsql al database apero e connesso
		if ($risultato->rowCount() > 0) {
			if ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
				$_SESSION["nome_utente"]    = $record["nome"]." ".$record["cognome"];
				$_SESSION["amministratore"] = FALSE;
				if ($record["tipo_utente"] == "SAD") {
					$_SESSION["amministratore"] = TRUE;
					$abilitato                  = TRUE;
				} else if ($record["tipo_utente"] == "OPE" || $record["tipo_utente"] == "PRO") {
					if (isset($_SESSION["ente"])) {
						global $config;
						$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"],":codice_utente"=>$record["codice"]);
						$sql = "SELECT r_enti_operatori.* FROM r_enti_operatori JOIN b_enti ON r_enti_operatori.cod_ente = b_enti.codice ";
						$sql .= "JOIN r_moduli_ente ON r_moduli_ente.cod_ente = b_enti.codice ";
						$sql .= "JOIN b_moduli ON r_moduli_ente.cod_modulo = b_moduli.codice ";
						$sql .= "WHERE b_enti.attivo = 'S' AND b_enti.codice = :codice_ente ";
						$sql .= " AND b_moduli.registrazione = 'S' AND b_moduli.attivo = 'S' ";
						$sql .= " AND r_enti_operatori.cod_utente = :codice_utente ";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount() > 0) $abilitato = true;
						$codice_utente = $record["codice"];
					}
				} else {
					if (isset($_SESSION["ente"])) {
						if ($_SESSION["ente"]["ambienteTest"] == "N") {
							if (isset($_SESSION["loginHash"])) {
								$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"],":codice_ente_utente"=>$record["codice_ente"]);
								$sql = "SELECT b_enti.* FROM b_enti LEFT JOIN b_enti AS b_sua ON b_enti.sua = b_sua.codice ";
								$sql .= "WHERE (b_enti.attivo = 'S' OR b_sua.attivo = 'S') AND ";
								$sql .= "(b_enti.codice = :codice_ente OR b_sua.codice = :codice_ente) AND ";
								$sql .= "b_enti.codice = :codice_ente_utente ";
								$ris = $pdo->bindAndExec($sql,$bind);
								if ($ris->rowCount() > 0) {
									$bind = array(":codice_utente"=>$record["codice"],":hash"=>$_SESSION["loginHash"]);
									$sql = "SELECT * FROM b_login_hash WHERE codice_utente = :codice_utente AND hash = :hash";
									$ris_hash = $pdo->bindAndExec($sql,$bind);
									if ($ris_hash->rowCount() > 0) {
										$pdo->bindAndExec("UPDATE b_login_hash SET timestamp = NOW() WHERE codice_utente = :codice_utente AND hash = :hash",$bind);
										$abilitato = true;
									}
								}
							}
						} else {
							$abilitato = true;
						}
					} else if ($record["codice_ente"] == 0) $abilitato = true;
				}
				$_SESSION["gerarchia"]     = $record["gerarchia"];
				$_SESSION["tipo_utente"]   = $record["tipo_utente"];
				unset($record["password"]);
				unset($record["user_cup"]);
				unset($record["pass_cup"]);
				$_SESSION["record_utente"] = $record;
				if($record["force_reset"] == "S") {
					if (in_array($_SERVER["PHP_SELF"], array("/index.php","/user/change_pwd.php")) === false) {
						echo '<meta http-equiv="refresh" content="0;URL=/user/change_pwd.php">';
						die();
					}
				}
				if (!$abilitato) {
					session_destroy();
					echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
					die();
				}
				$sql = "SELECT * FROM b_password_log WHERE codice_utente = :codice_utente ORDER BY timestamp DESC LIMIT 0,1";
				$ris = $pdo->bindAndExec($sql,array(":codice_utente"=>$record["codice"]));
				if ($ris->rowCount() == 1) {
					$last_change = $ris->fetch(PDO::FETCH_ASSOC);
					$limit = strtotime("-3 month");
					$last_change = strtotime(substr($last_change["timestamp"],0,10));
					if ($last_change < $limit) {
						$_SESSION["expired_pass"] = true;
						if (!$expired) {
							echo '<meta http-equiv="refresh" content="0;URL=/user/change_pwd.php">';
							die();
						}
					}
				}
				global $disableCaptcha;

				if (!empty($record["twoFactor_token"]) && !$disableCaptcha) {
					if (!isset($_SESSION["confirmTwoFactorAuth"])) {
						if (!$expired && $_SERVER["PHP_SELF"] !== "/user/twoFactor.php") {
							echo '<meta http-equiv="refresh" content="0;URL=/user/twoFactor.php">';
							die();
						} else if (in_array($_SERVER["PHP_SELF"], array("/index.php","/user/twoFactor.php","/user/change_pwd.php")) === false) {
							session_destroy();
							echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
							die();
						}
					}
				}
				if ($record["tipo_utente"] == "OPE" || $record["tipo_utente"] == "PRO") {
					if (in_array($_SERVER["PHP_SELF"], array("/user/twoFactor.php","/user/change_pwd.php")) === false) {
						include_once "{$root}/operatori_economici/check_oe.php";
					}
				}
			}
		} else {
			session_destroy();
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	}
}

function tokenGen() {
	return bin2hex(openssl_random_pseudo_bytes(32));
}

function human_filesize($bytes, $decimals = 2) {
$size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
$factor = floor((strlen($bytes) - 1) / 3);
return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . " " . @$size[$factor];
}

function folderSize ($dir)
{
		$size = 0;
		foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
				$size += is_file($each) ? filesize($each) : folderSize($each);
		}
		return $size;
}

function is_operatore() {
	$esito = false;
	if (isset($_SESSION["codice_utente"]) && ($_SESSION["tipo_utente"] == "OPE" || $_SESSION["tipo_utente"] == "PRO")) {$esito = true;
	}

	return $esito;
}

function getFase($querystring,$request) {
	global $pdo;
	$codice = false;
	$link = str_replace("?".$querystring,"",$request);
	$bind = array();
	$bind[":link"] = $link;
	$strsql = "SELECT * FROM b_gestione_gare WHERE link = :link";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount()>0) {
		$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
		$codice = $gestione["codice"];
	}
	return $codice;
}

function getFaseConcorso($querystring,$request) {
	global $pdo;
	$codice = false;
	$link = str_replace("?".$querystring,"",$request);
	$bind = array();
	$bind[":link"] = $link;
	$strsql = "SELECT * FROM b_conf_gestione_concorsi WHERE link = :link";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount()>0) {
		$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
		$codice = $gestione["codice"];
	}
	return $codice;
}

function getFaseProgetto($querystring,$request) {
	global $pdo;
	$codice = false;
	$link = str_replace("?".$querystring,"",$request);
	$bind = array();
	$bind[":link"] = $link."%";
	$strsql = "SELECT * FROM b_conf_gestione_progetti WHERE link LIKE :link";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount()>0) {
		$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
		$codice = $gestione["codice"];
	}
	return $codice;
}

function getFaseEsecuzione($querystring,$request) {
	global $pdo;
	$codice = false;
	$link = str_replace("?".$querystring,"",$request);
	$bind = array();
	$bind[":link"] = $link;
	$strsql = "SELECT * FROM b_conf_gestione_esecuzione WHERE link = :link";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount()>0) {
		$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
		$codice = $gestione["codice"];
	}
	return $codice;
}

function getFaseReferer($link,$host) {
	global $pdo;
	$codice = false;
	$link = substr($link,strpos($link,$host)+strlen($host));
	$link = substr($link,0,strpos($link,"?"));
	$bind = array();
	$bind[":link"] = $link;
	$strsql = "SELECT * FROM b_gestione_gare WHERE link = :link";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount()>0) {
		$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
		$codice = $gestione["codice"];
	}
	return $codice;
}

function getFaseRefererConcorso($link,$host) {
	global $pdo;
	$codice = false;
	$link = substr($link,strpos($link,$host)+strlen($host));
	$link = substr($link,0,strpos($link,"?"));
	$bind = array();
	$bind[":link"] = $link;
	$strsql = "SELECT * FROM b_conf_gestione_concorsi WHERE link = :link";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount()>0) {
		$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
		$codice = $gestione["codice"];
	}
	return $codice;
}


function getFaseRefererProgetto($link,$host) {
	global $pdo;
	$codice = false;
	$link = substr($link,strpos($link,$host)+strlen($host));
	$link = substr($link,0,strpos($link,"?"));
	$bind = array();
	$bind[":link"] = $link;
	$strsql = "SELECT * FROM b_conf_gestione_progetti WHERE link = :link";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount()>0) {
		$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
		$codice = $gestione["codice"];
	}
	return $codice;
}

function getFaseRefererEsecuzione($link,$host) {
	global $pdo;
	$codice = false;
	$link = substr($link,strpos($link,$host)+strlen($host));
	$link = substr($link,0,strpos($link,"?"));
	$bind = array();
	$bind[":link"] = $link;
	$strsql = "SELECT * FROM b_conf_gestione_esecuzione WHERE link = :link";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount()>0) {
		$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
		$codice = $gestione["codice"];
	}
	return $codice;
}

function riferimentiNormativi() {
	return [
		"2023-36" => "D.Lgs. 36/2023",
		"2016-50" => "D.Lgs. 50/2016",
	];
}

function check_permessi($modulo, $utente) {
	$return         = false;
	if (!empty($modulo) && !empty($utente)) {
		global $pdo;
		$bind = array(":modulo"=>$modulo);
		$strsql = "SELECT * FROM b_moduli WHERE radice = :modulo AND attivo = 'S'";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount() > 0) {
			$record_modulo = $risultato->fetch(PDO::FETCH_ASSOC);
			if ($record_modulo["gerarchia"] >= $_SESSION["gerarchia"]) {
				if ($record_modulo["ente"] == "S" && isset($_SESSION["ente"])) {
					if ($record_modulo["cross_p"]=="S" ||
							($record_modulo["cross_p"]=="N" && $_SESSION["ente"]["permit_cross"] == 'S') ||
							($record_modulo["cross_p"]=="N" && $_SESSION["ente"]["permit_cross"] == 'N' && $_SESSION["ente"]["codice"] == $_SESSION["record_utente"]["codice_ente"]) ||
							(empty($_SESSION["record_utente"]["codice_ente"]) && $_SESSION["gerarchia"] === "0")) {
							if ($record_modulo["tutti_ente"] == "S") {
								$return = true;
							} else {
								$bind=array(":codice_modulo"=>$record_modulo["codice"],":codice_ente"=>$_SESSION["ente"]["codice"]);
								$strsql = "SELECT b_moduli.* FROM b_moduli
													 JOIN r_moduli_ente ON b_moduli.codice = r_moduli_ente.cod_modulo WHERE
													 b_moduli.codice = :codice_modulo AND b_moduli.attivo = 'S' AND r_moduli_ente.cod_ente = :codice_ente ";
								$risultato = $pdo->bindAndExec($strsql,$bind);
								if ($risultato->rowCount() > 0) {
									$return = true;
								} else if ($record_modulo["radice"] == "user" && $_SESSION["gerarchia"]==="0") $return = true;
						}
					}
				} else if ($record_modulo["admin"] == "S") {
					$return = true;
				}
				if ($return) {
					if ($record_modulo["tutti_utente"] != "S") {
						$return = false;
						$bind=array(":codice_modulo"=>$record_modulo["codice"],":codice_utente"=>$utente);
						$strsql = "SELECT b_moduli.* FROM b_moduli JOIN r_moduli_utente ON b_moduli.codice = r_moduli_utente.cod_modulo WHERE b_moduli.codice = :codice_modulo AND r_moduli_utente.cod_utente = :codice_utente ";
						$risultato = $pdo->bindAndExec($strsql,$bind);
						if ($risultato->rowCount() > 0) $return = true;
					}
				}
			}
		}
	}
	return $return;
}

function check_lock($stato, $gara) {
	if (isset($_SESSION["record_utente"])) {
		if ($_SESSION["record_utente"]["read_only"] != "S") {
			if (!empty($stato) && !empty($gara)) {
				global $pdo;
				$bind = array(":codice_gara"=>$gara);
				$strsql         = "SELECT b_stati_gare.fase AS stato FROM b_stati_gare JOIN b_gare ON b_stati_gare.fase = b_gare.stato WHERE b_gare.codice = :codice_gara";
				$risultato      = $pdo->bindAndExec($strsql,$bind);
				$bind = array(":stato"=>$stato);
				$str_gestione   = "SELECT * FROM b_gestione_gare WHERE b_gestione_gare.codice = :stato ";
				$ris_gestione      = $pdo->bindAndExec($str_gestione,$bind);
				if ($risultato->rowCount() > 0 && $ris_gestione->rowCount() > 0) {
					$rec          = $risultato->fetch(PDO::FETCH_ASSOC);
					$rec_gestione = $ris_gestione->fetch(PDO::FETCH_ASSOC);
					if (($rec["stato"] >= $rec_gestione["fase_minima"]) && (($rec["stato"] <= $rec_gestione["fase_massima"]) || ($rec_gestione["fase_massima"] == 0))) {
						return false;
					} 
				} 
			} 
		}
	} 
	return true;
}

function check_lock_concorsi($stato, $concorso) {
	if (isset($_SESSION["record_utente"])) {
		if ($_SESSION["record_utente"]["read_only"] != "S") {
			if (!empty($stato) && !empty($concorso)) {
				global $pdo;
				$bind = array(":codice_gara"=>$concorso);
				$strsql         = "SELECT b_conf_stati_concorsi.fase AS stato FROM b_conf_stati_concorsi JOIN b_concorsi ON b_conf_stati_concorsi.fase = b_concorsi.stato WHERE b_concorsi.codice = :codice_gara";
				$risultato      = $pdo->bindAndExec($strsql,$bind);
				$bind = array(":stato"=>$stato);
				$str_gestione   = "SELECT * FROM b_conf_gestione_concorsi WHERE b_conf_gestione_concorsi.codice = :stato ";
				$ris_gestione      = $pdo->bindAndExec($str_gestione,$bind);
				if ($risultato->rowCount() > 0 && $ris_gestione->rowCount() > 0) {
					$rec          = $risultato->fetch(PDO::FETCH_ASSOC);
					$rec_gestione = $ris_gestione->fetch(PDO::FETCH_ASSOC);
					if (($rec["stato"] >= $rec_gestione["fase_minima"]) && (($rec["stato"] <= $rec_gestione["fase_massima"]) || ($rec_gestione["fase_massima"] == 0))) {
						return false;
					}
				}
			}
		}
	}
	return true;
}

function check_lock_esecuzione($stato, $progetto) {
	if (isset($_SESSION["record_utente"])) {
		if ($_SESSION["record_utente"]["read_only"] != "S") {
			if (!empty($stato) && !empty($progetto)) {
				global $pdo;
				$bind = array(":codice_progetti"=>$progetto);
				$strsql         = "SELECT b_conf_stati_esecuzione.fase AS stato FROM b_conf_stati_esecuzione JOIN b_contratti ON b_conf_stati_esecuzione.fase = b_contratti.stato_esecuzione WHERE b_contratti.codice = :codice_progetti";
				$risultato      = $pdo->bindAndExec($strsql,$bind);
				$bind = array(":stato"=>$stato);
				$str_gestione   = "SELECT * FROM b_conf_gestione_esecuzione WHERE b_conf_gestione_esecuzione.codice = :stato ";
				$ris_gestione      = $pdo->bindAndExec($str_gestione,$bind);
				if ($risultato->rowCount() > 0 && $ris_gestione->rowCount() > 0) {
					$rec          = $risultato->fetch(PDO::FETCH_ASSOC);
					$rec_gestione = $ris_gestione->fetch(PDO::FETCH_ASSOC);
					if (($rec["stato"] >= $rec_gestione["fase_minima"]) && (($rec["stato"] <= $rec_gestione["fase_massima"]) || ($rec_gestione["fase_massima"] == 0))) {
						return false;
					}
				}
			}
		}
	}
	return true;
}

function check_permessi_gara($codice_fase, $gara, $utente) {
	$return = array("permesso" => false, "lock" => true);
	if (!empty($codice_fase) && !empty($gara) && !empty($utente)) {
		global $pdo;
		$sql = "SELECT * FROM b_gare WHERE codice_gestore = :codice_ente AND codice = :gara";
		$check_gara = $pdo->bindAndExec($sql,array(":gara"=>$gara,":codice_ente"=>$_SESSION["ente"]["codice"]));
		if ($check_gara->rowCount() > 0) {
			$bind = array(":codice_fase" => $codice_fase);
			$str_gestione   = "SELECT b_gestione_gare.* FROM b_gestione_gare WHERE b_gestione_gare.codice = :codice_fase";
			if ($_SESSION["gerarchia"] > 0 && $_SESSION["ente"]["codice"] != $_SESSION["record_utente"]["codice_ente"] && $_SESSION["ente"]["permit_cross"] != "S") {
				$str_gestione .= " AND b_gestione_gare.cross_p = 'S' ";
			}
			$ris_gestione   = $pdo->bindAndExec($str_gestione,$bind);
			if ($ris_gestione->rowCount() > 0) {
				$rec_gestione = $ris_gestione->fetch(PDO::FETCH_ASSOC);
				$return["permesso"] = check_permessi($rec_gestione["modulo_riferimento"], $utente);
				if ($return["permesso"]) {
					$bind = array(":codice_utente" => $utente,":codice_gara"=>$gara);
					$str_permessi = "SELECT b_utenti.codice FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice WHERE ";
					$str_permessi .= "b_utenti.codice = :codice_utente AND (b_gruppi.gerarchia < 2 OR (b_utenti.codice IN (SELECT codice_utente FROM b_permessi WHERE codice_gara = :codice_gara)))";
					$ris_permessi = $pdo->bindAndExec($str_permessi,$bind);
					if ($ris_permessi->rowCount() > 0) {
						$return["permesso"] = true;
					} else {
						$return["permesso"] = false;
					}
					$return["lock"] = check_lock($codice_fase, $gara);
				}
			}
		}
	}
	return $return;
}

function check_permessi_concorso($codice_fase, $gara, $utente) {
	$return = array("permesso" => false, "lock" => true);
	if (!empty($codice_fase) && !empty($gara) && !empty($utente)) {
		global $pdo;
		$sql = "SELECT * FROM b_concorsi WHERE codice_gestore = :codice_ente AND codice = :gara";
		$check_gara = $pdo->bindAndExec($sql,array(":gara"=>$gara,":codice_ente"=>$_SESSION["ente"]["codice"]));
		if ($check_gara->rowCount() > 0) {
			$bind = array(":codice_fase" => $codice_fase);
			$str_gestione   = "SELECT b_conf_gestione_concorsi.* FROM b_conf_gestione_concorsi WHERE b_conf_gestione_concorsi.codice = :codice_fase";
			if ($_SESSION["gerarchia"] > 0 && $_SESSION["ente"]["codice"] != $_SESSION["record_utente"]["codice_ente"] && $_SESSION["ente"]["permit_cross"] != "S") {
				$str_gestione .= " AND b_conf_gestione_concorsi.cross_p = 'S' ";
			}
			$ris_gestione   = $pdo->bindAndExec($str_gestione,$bind);
			if ($ris_gestione->rowCount() > 0) {
				$rec_gestione = $ris_gestione->fetch(PDO::FETCH_ASSOC);
				$return["permesso"] = check_permessi($rec_gestione["modulo_riferimento"], $utente);
				if ($return["permesso"]) {
					$bind = array(":codice_utente" => $utente,":codice_gara"=>$gara);
					$str_permessi = "SELECT b_utenti.codice FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice WHERE ";
					$str_permessi .= "b_utenti.codice = :codice_utente AND (b_gruppi.gerarchia < 2 OR (b_utenti.codice IN (SELECT codice_utente FROM b_permessi_concorsi WHERE codice_gara = :codice_gara)))";
					$ris_permessi = $pdo->bindAndExec($str_permessi,$bind);
					if ($ris_permessi->rowCount() > 0) {
						$return["permesso"] = true;
					} else {
						$return["permesso"] = false;
					}
					$return["lock"] = check_lock_concorsi($codice_fase, $gara);
				}
			}
		}
	}
	return $return;
}


function check_permessi_progetto($codice_fase, $progetto, $utente) {
	$return = array("permesso" => false, "lock" => true);
	if (!empty($codice_fase) && !empty($progetto) && !empty($utente)) {
		global $pdo;
		$sql = "SELECT * FROM b_progetti_investimento WHERE codice_gestore = :codice_ente AND codice = :progetto";
		$check_progetto = $pdo->bindAndExec($sql,array(":progetto"=>$progetto,":codice_ente"=>$_SESSION["ente"]["codice"]));
		if ($check_progetto->rowCount() > 0) {
			$bind = array(":codice_fase" => $codice_fase);
			$str_gestione   = "SELECT b_conf_gestione_progetti.* FROM b_conf_gestione_progetti WHERE b_conf_gestione_progetti.codice = :codice_fase";
			$ris_gestione   = $pdo->bindAndExec($str_gestione,$bind);
			if ($ris_gestione->rowCount() > 0) {
				$rec_gestione = $ris_gestione->fetch(PDO::FETCH_ASSOC);
				$return["permesso"] = check_permessi($rec_gestione["modulo_riferimento"], $utente);
				if ($return["permesso"]) {
					$bind = array(":codice_utente" => $utente,":codice_progetto"=>$progetto);
					$str_permessi = "SELECT b_utenti.codice FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice WHERE ";
					$str_permessi .= "b_utenti.codice = :codice_utente AND (b_gruppi.gerarchia < 2 OR (b_utenti.codice IN (SELECT codice_utente FROM b_permessi_progetti WHERE codice_progetto = :codice_progetto)))";
					$ris_permessi = $pdo->bindAndExec($str_permessi,$bind);
					if ($ris_permessi->rowCount() > 0) {
						$return["permesso"] = true;
					} else {
						$return["permesso"] = false;
					}
					$return["lock"] = false;
					$sql = "SELECT * FROM b_progetti_investimento WHERE stato_cup >= 100 AND codice = :codice_progetto";
					$ris_check = $pdo->bindAndExec($sql,array(":codice_progetto"=>$progetto));
					if ($ris_check->rowCount() > 0) $return["lock"] = true;
				}
			}
		}
	}
	return $return;
}

function check_permessi_esecuzione($codice_fase, $contratto, $utente) {
	$return = array("permesso" => false, "lock" => true);
	if (!empty($codice_fase) && !empty($contratto) && !empty($utente)) {
		global $pdo;
		$sql = "SELECT * FROM b_contratti WHERE codice_gestore = :codice_ente AND codice = :contratto";
		$check_contratto = $pdo->bindAndExec($sql,array(":contratto"=>$contratto,":codice_ente"=>$_SESSION["ente"]["codice"]));
		if ($check_contratto->rowCount() > 0) {
			$bind = array(":codice_fase" => $codice_fase);
			$str_gestione   = "SELECT b_conf_gestione_esecuzione.* FROM b_conf_gestione_esecuzione WHERE b_conf_gestione_esecuzione.codice = :codice_fase";
			$ris_gestione   = $pdo->bindAndExec($str_gestione,$bind);
			if ($ris_gestione->rowCount() > 0) {
				$rec_gestione = $ris_gestione->fetch(PDO::FETCH_ASSOC);
				$return["permesso"] = check_permessi($rec_gestione["modulo_riferimento"], $utente);
				if ($return["permesso"]) {
					$bind = array(":codice_utente" => $utente,":codice_contratto"=>$contratto);
					$str_permessi = "SELECT b_utenti.codice FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice WHERE ";
					$str_permessi .= "b_utenti.codice = :codice_utente AND (b_gruppi.gerarchia < 2 OR (b_utenti.codice IN (SELECT codice_utente FROM b_permessi_esecuzione_contratti WHERE codice_contratto = :codice_contratto)))";
					$ris_permessi = $pdo->bindAndExec($str_permessi,$bind);
					if ($ris_permessi->rowCount() > 0) {
						$return["permesso"] = true;
					} else {
						$return["permesso"] = false;
					}
					$return["lock"] = check_lock_esecuzione($codice_fase, $contratto);
				}
			}
		}
	}
	return $return;
}

function check_permessi_lottoanac($lotto, $utente) {
	$return = array("permesso" => false, "lock" => false);
	if (!empty($lotto) && !empty($utente) && isset($_SESSION["ente"]["codice"])) {
		global $pdo;
		if (check_permessi("anac", $utente)) {
			$bind = array(":codice_utente" => $utente,":codice_lotto"=>$lotto);
			$str_permessi = "SELECT b_utenti.codice_ente, b_gruppi.gerarchia
											 FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice
											 WHERE
											 	b_utenti.codice = :codice_utente AND
												(b_gruppi.gerarchia < 2 OR (
													b_utenti.codice IN (
														SELECT b_permessi_simog.codice_utente
														FROM b_permessi_simog
														JOIN b_lotti_simog ON b_permessi_simog.codice_simog = b_lotti_simog.codice_simog
														WHERE b_lotti_simog.codice = :codice_lotto)
													)
												)";
			$ris_permessi = $pdo->bindAndExec($str_permessi,$bind);
			if ($ris_permessi->rowCount() > 0) {
				$chk_utente = $ris_permessi->fetch(PDO::FETCH_ASSOC);
				if ($chk_utente["gerarchia"] > 0) {
					$bind = array(':codice' => $lotto, ':codice_ente' => $chk_utente["codice_ente"]);
		      $sql = "SELECT b_lotti_simog.codice FROM b_lotti_simog JOIN b_simog ON b_lotti_simog.codice_simog = b_simog.codice
									WHERE b_lotti_simog.codice = :codice
									AND (b_simog.codice_ente = :codice_ente OR b_simog.codice_gestore = :codice_ente) ";
					$ris_permessi = $pdo->bindAndExec($sql,$bind);
					if ($ris_permessi->rowCount() > 0) $return["permesso"] = true;
				} else {
					$return["permesso"] = true;
				}
			}
		}
	}
	return $return;
}

function registrazione_abilitata() {
	$return = false;
	global $pdo;
	if (isset($_SESSION["ente"]) && !isset($_SESSION["codice_utente"])) {
		$bind = array(":codice_ente" => $_SESSION["ente"]["codice"]);
		$strsql = "SELECT * FROM b_moduli JOIN r_moduli_ente ON b_moduli.codice = r_moduli_ente.cod_modulo WHERE b_moduli.registrazione = 'S' AND r_moduli_ente.cod_ente = :codice_ente";
		$ris    = $pdo->bindAndExec($strsql,$bind);
		if ($ris->rowCount() > 0) $return = true;
	}
	return $return;

}

function simple_encrypt($text, $salt) {
	return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
}

function simple_decrypt($text, $salt) {
	return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
}

function purify($text) {
	$config = HTMLPurifier_Config::createDefault();
	// $config->set('Core', 'Encoding', 'ISO-8859-1'); // replace with your encoding
	// $config->set('HTML', 'Doctype', 'HTML 4.01 Transitional'); // replace with your doctype
	$purifier = new HTMLPurifier($config);
	return $purifier->purify($text);
}

function purifyInput(&$array) {
	if (!empty($array)) {
		foreach($array AS $key => $value) {
			if (!is_array($value)) {
				$array[$key] = purify($value);
			} else {
				$array[$key] = purifyInput($value);
			}
		}
	}
	return $array;
}


function log_gare($codice_ente, $codice_gara, $operazione, $oggetto, $log = true) {

	global $pdo;
	$codice_log     = 0;
	if ($log) {
		$sql_log = "SELECT * FROM b_log ORDER BY codice DESC LIMIT 0,1";
		$ris_log = $pdo->query($sql_log);
		if ($ris_log->rowCount()) {
			$log = $ris_log->fetch(PDO::FETCH_ASSOC);
			$codice_log = $log["codice"];
		}
	}
	$bind = array();
	$bind[":codice_ente"] = $codice_ente;
	$bind[":codice_log"] = $codice_log;
	$bind[":codice_gara"] = $codice_gara;
	$bind[":operazione"] = purify($operazione);
	$bind[":oggetto"] = purify($oggetto);
	$bind[":codice_utente"] = $_SESSION["codice_utente"];
	$operazione = htmlspecialchars($operazione, ENT_QUOTES, 'UTF-8');
	$operazione = str_replace("&amp;", "&", $operazione);
	$strsql     = "INSERT INTO b_log_gare (codice_ente,codice_log,codice_gara,operazione,oggetto,utente_modifica) VALUES (";
	$strsql .= ":codice_ente, ";
	$strsql .= ":codice_log, ";
	$strsql .= ":codice_gara, ";
	$strsql .= ":operazione, ";
	$strsql .= ":oggetto, ";
	$strsql .= ":codice_utente)";

	$risultato = $pdo->bindAndExec($strsql,$bind);//invia la query contenuta in $strsql al database apero e connesso
}

function getIndirizzoConferma($codice_pec) {
	global $pdo;
	$return = false;
	if (!empty($_SESSION["ente"]["codice"])) {
		if ($codice_pec > 0) {
			$risultato = $pdo->go("SELECT pec FROM b_pec WHERE codice = :codice_pec AND codice_ente = :codice_ente ",[":codice_pec"=>$codice_pec,":codice_ente"=>$_SESSION["ente"]["codice"]]);
			if ($risultato->rowCount() > 0) $return = $risultato->fetch(PDO::FETCH_ASSOC)["pec"];
		}
		if (empty($return)) $return = array($_SESSION["ente"]["pec"]);
	}
	return $return;
}

function log_concorso($codice_ente, $codice_gara, $operazione, $oggetto, $log = true) {

	global $pdo;
	$codice_log     = 0;
	if ($log) {
		$sql_log = "SELECT * FROM b_log ORDER BY codice DESC LIMIT 0,1";
		$ris_log = $pdo->query($sql_log);
		if ($ris_log->rowCount()) {
			$log = $ris_log->fetch(PDO::FETCH_ASSOC);
			$codice_log = $log["codice"];
		}
	}
	$bind = array();
	$bind[":codice_ente"] = $codice_ente;
	$bind[":codice_log"] = $codice_log;
	$bind[":codice_gara"] = $codice_gara;
	$bind[":operazione"] = purify($operazione);
	$bind[":oggetto"] = purify($oggetto);
	$bind[":codice_utente"] = $_SESSION["codice_utente"];
	$operazione = htmlspecialchars($operazione, ENT_QUOTES, 'UTF-8');
	$operazione = str_replace("&amp;", "&", $operazione);
	$strsql     = "INSERT INTO b_log_concorsi (codice_ente,codice_log,codice_gara,operazione,oggetto,utente_modifica) VALUES (";
	$strsql .= ":codice_ente, ";
	$strsql .= ":codice_log, ";
	$strsql .= ":codice_gara, ";
	$strsql .= ":operazione, ";
	$strsql .= ":oggetto, ";
	$strsql .= ":codice_utente)";

	$risultato = $pdo->bindAndExec($strsql,$bind);//invia la query contenuta in $strsql al database apero e connesso
}

function log_esecuzione($codice_ente, $codice_contratto, $operazione, $oggetto, $log = true) {

	global $pdo;
	$codice_log     = 0;
	if ($log) {
		$sql_log = "SELECT * FROM b_log ORDER BY codice DESC LIMIT 0,1";
		$ris_log = $pdo->query($sql_log);
		if ($ris_log->rowCount()) {
			$log = $ris_log->fetch(PDO::FETCH_ASSOC);
			$codice_log = $log["codice"];
		}
	}
	$bind = array();
	$bind[":codice_ente"] = $codice_ente;
	$bind[":codice_log"] = $codice_log;
	$bind[":codice_contratto"] = $codice_contratto;
	$bind[":operazione"] = purify($operazione);
	$bind[":oggetto"] = purify($oggetto);
	$bind[":codice_utente"] = $_SESSION["codice_utente"];
	$operazione = htmlspecialchars($operazione, ENT_QUOTES, 'UTF-8');
	$operazione = str_replace("&amp;", "&", $operazione);
	$strsql     = "INSERT INTO b_log_esecuzione (codice_ente,codice_log,codice_contratto,operazione,oggetto,utente_modifica) VALUES (";
	$strsql .= ":codice_ente, ";
	$strsql .= ":codice_log, ";
	$strsql .= ":codice_contratto, ";
	$strsql .= ":operazione, ";
	$strsql .= ":oggetto, ";
	$strsql .= ":codice_utente)";

	$risultato = $pdo->bindAndExec($strsql,$bind);//invia la query contenuta in $strsql al database apero e connesso
}


function getName($nomefile,$destinazione) {
	$numero   = 0;
	$percorso = $destinazione;
	$nome_documento_doc=  sanitize_string($nomefile);
	$percorso_nomefile  = $percorso.$nome_documento_doc;
	while (file_exists($percorso_nomefile)) {
		$numero++;
		$percorso_nomefile = $percorso.$numero."-".$nome_documento_doc;
	}
	if ($numero > 0) {
		$nome_documento_doc = $numero."-".$nome_documento_doc;
	}
	return $nome_documento_doc;
}

function getRealName($file_path) {
	$nome_attuale = explode("/",$file_path);
	$nome_attuale = end($nome_attuale);
	$percorso = str_replace("/".$nome_attuale, "/",$file_path);
	$numero   = 0;
	$nomeFisico = hash("md5",file_get_contents($file_path));
	$nomeFisico .= substr(str_shuffle($nomeFisico),0,5);
	$percorso_nomefile  = $percorso.$nomeFisico;
	while (file_exists($percorso_nomefile)) {
		$numero++;
		$percorso_nomefile = $percorso.$numero."-".$nomeFisico;
	}
	if ($numero > 0) {
		$nomeFisico = $numero."-".$nomeFisico;
	}
	return $nomeFisico;
}

function getRealNameFromData($data) {
	$nomeFisico = hash("md5",$data);
	$nomeFisico .= substr(str_shuffle($nomeFisico),0,5);
	return $nomeFisico;
}

function copiafile_chunck($nomefile, $destinazione, $chunk_folder, $nome_destinazione = "", $elimina = true) {
	$return = false;
	if (strpos($nomefile,"..")===false) {
		$numero   = 0;
		$percorso = $destinazione;
		if ($nome_destinazione == "") {
			$nome_documento_doc= sanitize_string($nomefile); //SELEZIONIAMO DALL'ARRAY IL NOME EFFETTIVO DEL FILE
			$nomeFisico = md5_file($chunk_folder."/".$nomefile);
			$salt = substr(str_shuffle($nomeFisico),0,5);
			$nomeFisico .= $salt;
			$salt = substr(str_shuffle($nomeFisico),0,5);
			$nome_documento_doc = explode(".", $nome_documento_doc);
			$ins = array(time().$salt);
			array_splice( $nome_documento_doc, 1, 0, $ins );
			$nome_documento_doc = implode(".", $nome_documento_doc);
			$percorso_nomefile  = $percorso.$nomeFisico;
			while (file_exists($percorso_nomefile)) {
				$numero++;
				$percorso_nomefile = $percorso.$numero."-".$nomeFisico;
			}
			if ($numero > 0) {
				$nomeFisico = $numero."-".$nomeFisico;
			}
		} else {
			$percorso_nomefile  = $percorso.$nome_destinazione;
			$nome_documento_doc = $nomeFisico = $nome_destinazione;
		}
		if (copy($chunk_folder."/".$nomefile, $percorso_nomefile)) {
			if ($elimina) unlink($chunk_folder."/".$nomefile);
			$return = array("nome_file"=>$nome_documento_doc,"nome_fisico"=>$nomeFisico);
		}
	}
	return $return;
}

// ********************* fine copia file

function replace_accents($string)
{
  return str_replace( array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $string);
}
function get_campi($tabella) {
	$campi = "";
	global $pdo;
	$strsql = "DESCRIBE ".$tabella.";";
	$risultato  = $pdo->query($strsql);

	if ($risultato->rowCount() > 0) {
		$campi = array();
		while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
			$campi[$record["Field"]] = "";
		}
	}
	return $campi;
}

function oggi() {
	$today = getdate();
	switch ($today['mday']) {
		case "1":$day = "01";
			break;
		case "2":$day = "02";
			break;
		case "3":$day = "03";
			break;
		case "4":$day = "04";
			break;
		case "5":$day = "05";
			break;
		case "6":$day = "06";
			break;
		case "7":$day = "07";
			break;
		case "8":$day = "08";
			break;
		case "9":$day = "09";
			break;
		default:$day = $today['mday'];
	}
	switch ($today['month']) {
		case "January":$mese = "01";
			break;
		case "February":$mese = "02";
			break;
		case "March":$mese = "03";
			break;
		case "April":$mese = "04";
			break;
		case "May":$mese = "05";
			break;
		case "June":$mese = "06";
			break;
		case "July":$mese = "07";
			break;
		case "August":$mese = "08";
			break;
		case "September":$mese = "09";
			break;
		case "October":$mese = "10";
			break;
		case "November":$mese = "11";
			break;
		case "December":$mese = "12";
			break;
	}
	$oggi = $day."/".$mese."/".$today['year'];
	return $oggi;
}

function mysql2time($mysql_date) {
	return substr($mysql_date, -5);
}

function mysql2date($mysql_date) {
	$anno          = substr($mysql_date, 0, 4);
	$mese          = substr($mysql_date, 5, 2);
	$giorno        = substr($mysql_date, 8, 2);
	$reversed_data = "$giorno/$mese/$anno";

	if (($reversed_data == "00/00/0000")|($reversed_data == "//")) {
		$reversed_data = "";
	}

	return $reversed_data;
}

function date2mysql($normal_date) {
	$anno          = substr($normal_date, 6, 4);
	$mese          = substr($normal_date, 3, 2);
	$giorno        = substr($normal_date, 0, 2);
	$reversed_data = "$anno-$mese-$giorno";

	if ($reversed_data == "--") {
		$reversed_data = "";
	}

	return $reversed_data;
}
function mysql2completedate($mysql_date) {
	$mesi = array(1=> 'Gennaio', 'Febbraio', 'Marzo', 'Aprile',
		'Maggio', 'Giugno', 'Luglio', 'Agosto',
		'Settembre', 'Ottobre', 'Novembre', 'Dicembre');

	$giorni = array('Domenica', 'Lunedi', 'Martedi', 'Mercoledi',
		'Giovedi', 'Venerdi', 'Sabato');
	list($sett, $giorno, $mese, $anno, $ora) = explode('-', date('w-d-n-Y-H:i', strtotime($mysql_date)));
	return $giorni[$sett]." - ".$giorno." ".$mesi[$mese]." ".$anno." - ".$ora;
}
function mysql2datetime($mysql_date) {
	$anno          = substr($mysql_date, 0, 4);
	$mese          = substr($mysql_date, 5, 2);
	$giorno        = substr($mysql_date, 8, 2);
	$ora           = substr($mysql_date, 11, 2);
	$minuti        = substr($mysql_date, 14, 2);
	$reversed_data = "$giorno/$mese/$anno $ora:$minuti";

	if (($reversed_data == "00/00/0000 00:00")||($reversed_data == "// :")) {
			$reversed_data = "";
	}

	return $reversed_data;
}

function datetime2mysql($normal_date) {
	$anno          = substr($normal_date, 6, 4);
	$mese          = substr($normal_date, 3, 2);
	$giorno        = substr($normal_date, 0, 2);
	$ora           = substr($normal_date, 11, 2);
	$minuti        = substr($normal_date, 14, 2);
	$reversed_data = "$anno-$mese-$giorno $ora:$minuti";

	if ($reversed_data == "--") {
		$reversed_data = "";
	}

	return $reversed_data;
}

function dateFromCF($cf,$format="mysql") {
	$anno = substr($cf,6,2);
	$anno_maggiorenne = date('Y') - 18;
	$anno = ((2000 + $anno) > $anno_maggiorenne) ? "19".$anno : "20".$anno;
	$mese = substr($cf,8,1);
	switch ($mese) {                                                    // attenzione le lettere non sono in successione alfabetica
		case'A': $mese = "01"; break;
		case'B': $mese = "02"; break;
		case'C': $mese = "03"; break;
		case'D': $mese = "04"; break;
		case'E': $mese = "05"; break;
		case'H': $mese = "06"; break;
		case'L': $mese = "07"; break;
		case'M': $mese = "08"; break;
		case'P': $mese = "09"; break;
		case 'R': $mese = "10"; break;
		case 'S': $mese = "11"; break;
		case 'T': $mese = "12"; break;
	}

	$giorno = substr($cf,9,2);
	if ($giorno > 40) {
		$giorno -= 40;
		if ($giorno < 10) $giorno = "0".$giorno;
	}

	$data = "";
	switch ($format) {
		case 'it':
			$data = $giorno . "/" . $mese . "/" . $anno;
		break;
		default:
			$data = $anno."-".$mese."-".$giorno;
		break;
	}
	return $data;
}

function cityFromCF($cf) {
	global $pdo;
	$comune = strtoupper(substr($cf,11,4));
  $bind = array(":comune"=>$comune);
	$sql = "SELECT * FROM b_comuni WHERE cf = :comune";
	$ris = $pdo->bindAndExec($sql,$bind);
	$citta = "";
	if ($ris->rowCount()>0) {
		$rec = $ris->fetch(PDO::FETCH_ASSOC);
		$citta = $rec["descr"];
	}
  return $citta;
}

//************* funzione log

function scrivilog($nometab, $operazione, $istruzione, $codoperatore) {
	global $pdo;
	$istruzione = str_replace("'", chr(34), $istruzione);
	$strsql = "INSERT INTO b_log (nometab,operazione,istruzione,ip,codoperatore) VALUES (:nome_tab,:operazione,:istruzione,:id,:codoperatore)";
	$bind = array();
	$bind[":nome_tab"] = $nometab;
	$bind[":operazione"] = $operazione;
	$bind[":istruzione"] = gzcompress(base64_encode($istruzione));
	$bind[":id"] = getenv("REMOTE_ADDR");
	$bind[":codoperatore"] = $codoperatore;
	$pdo->bindAndExec($strsql,$bind);
	return "SELECT 1";

}

function suddivisione($richiesta, $nomecampo) {

	$cond = "";
	if (is_array($richiesta)) {
		foreach ($richiesta as $parola) {
			if (strlen($parola) > 0) {
				$cond .= $nomecampo." like '%".$parola."%' OR ";
			}
		}
		$cond = substr($cond, 0, strlen($cond)-3);//per eliminare l'ultimo OR
	} else {

		if (strlen($richiesta) > 0) {
			$cond .= $nomecampo." like '%".$richiesta."%' ";
		}
	}

	return $cond;
}


function suddivisione_pdo($richiesta, $nomecampo) {

	$cond = "";
	$bind=array();
	$i=0;
	if (is_array($richiesta)) {
		foreach ($richiesta as $parola) {
			$i++;
			if (strlen($parola) > 0) {
				$bind[":parola_".$i] = "%".$parola."%";
				$cond .= $nomecampo." like :parola_".$i." OR ";
			}
		}
		$cond = substr($cond, 0, strlen($cond)-3);//per eliminare l'ultimo OR
	} else {
		if (strlen($richiesta) > 0) {
			$i++;
			$bind[":parola_".$i] = "%".$richiesta."%";
			$cond .= $nomecampo." like :parola_".$i." ";
		}
	}
	return array("sql"=>$cond,"bind"=>$bind);
}

function genpwd($cnt) {
	$pwd = str_shuffle('abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
	$pwd = substr($pwd, 0, $cnt-1);
	$chr = str_shuffle('!?*-%');
	$pwd .= substr($chr, 0, 1);
	$pwd = str_shuffle($pwd);
	return $pwd;
}

function randomPassword($len = 8) {
	/* Programmed by Christian Haensel
	 ** christian@chftp.com
	 ** http://www.chftp.com
	 **
	 ** Exclusively published on weberdev.com.
	 ** If you like my scripts, please let me know or link to me.
	 ** You may copy, redistribute, change and alter my scripts as
	 ** long as this information remains intact.
	 **
	 ** Modified by Josh Hartman on 12/30/2010.
	 */
	if (($len%2) !== 0) {// Length paramenter must be a multiple of 2
		$len = 8;
	}
	$length   = $len-2;// Makes room for the two-digit number on the end
	$conso    = array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z');
	$vocal    = array('a', 'e', 'i', 'o', 'u');
	$password = '';
	srand((double) microtime()*1000000);
	$max = $length/2;
	for ($i = 1; $i <= $max; $i++) {
		$password .= $conso[rand(0, 19)];
		$password .= $vocal[rand(0, 4)];
	}
	$password .= rand(10, 99);
	$newpass = $password;
	return $newpass;
}

function &prepare_mailer($subject, $body, $codice_pec = 0) {
	global $root;
	$errore         = "";
	$configurazione = array();
	if ($codice_pec != -1) {
		global $pdo;
		$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
		$strsql         = "SELECT * FROM b_enti WHERE codice = :codice_ente ";
		$risultato      = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount() > 0) {
			$ente                      = $risultato->fetch(PDO::FETCH_ASSOC);
			$configurazione["host"]          = $ente["smtp"];
			$configurazione["smtp_port"]     = $ente["smtp_port"];
			$configurazione["smtp_ssl"]      = $ente["usa_ssl"];
			$configurazione["mittente_mail"] = $ente["pec"];
			$configurazione["smtp_password"] = $ente["password"];
		}
		if ($codice_pec != 0) {
			$bind = array(":codice_pec"=>$codice_pec,":codice_ente"=>$_SESSION["ente"]["codice"]);
			$strsql         = "SELECT * FROM b_pec WHERE codice = :codice_pec AND codice_ente = :codice_ente ";
			$risultato      = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$record_pec                      = $risultato->fetch(PDO::FETCH_ASSOC);
				$configurazione["host"]          = $record_pec["smtp"];
				$configurazione["smtp_port"]     = $record_pec["smtp_port"];
				$configurazione["smtp_ssl"]      = $record_pec["usa_ssl"];
				$configurazione["mittente_mail"] = $record_pec["pec"];
				$configurazione["smtp_password"] = $record_pec["password"];
			}
		}
	} else {
		global $config;
		$configurazione["host"]          = $config["smtp_server"];
		$configurazione["smtp_port"]     = $config["smtp_port"];
		$configurazione["smtp_ssl"]      = $config["smtp_ssl"];
		$configurazione["mittente_mail"] = $config["mittente_mail"];
		$configurazione["smtp_password"] = $config["smtp_password"];
	}
	$mail = new PHPMailer();
	$mail->setLanguage('it');
	$mail->IsSMTP();
	$mail->Timeout = 30;
	$mail->Host                                                 = $configurazione["host"];
	$mail->Port                                                 = $configurazione["smtp_port"];
	if ($configurazione["smtp_ssl"] == true) {$mail->SMTPSecure = 'ssl';
	}

	$mail->SMTPAuth                                                     = true;
	$mail->Username                                                     = $configurazione["mittente_mail"];
	$mail->Password                                                     = $configurazione["smtp_password"];
	if (isset($_SESSION["ente"]) && $codice_pec != -1) {$mail->Password = simple_decrypt($configurazione["smtp_password"], $_SESSION["ente"]["cf"]);
	}

	$mail->SetFrom($configurazione["mittente_mail"], $_SESSION["config"]["nome_sito"]);
	$mail->Subject = $subject;
	$mail->MsgHTML($body);
	return $mail;
}

function invia_email($destinatari, $oggetto, $corpo, $codice_pec = 0) {

	$head = "<html><head>
					<style>
						body { font-family: Tahoma, Geneva, sans-serif; margin:0px; padding:0px }
						.padding { padding:20px; }
						tr.odd { background-color:#F6F6F6;}
						tr.even { background-color:#ECECEC; }
						#bottom { padding:20px; background-color: #DDD; text-align:right }
					</style>
					</head>
					<body>";
	$head .= "<div class=\"padding\"><table>";
	if (isset($_SESSION["ente"])) {
		$head .= "<tr><td><img src=\"https://gare.comune.roma.it/documenti/enti/".$_SESSION["ente"]["logo"]."\" width=\"150\"></td>";
		$head .= "<td><div class=\"padding\">";
		$head .= "<h1>".$_SESSION["ente"]["denominazione"]."</h1>";
		$head .= "<strong>".$_SESSION["ente"]["indirizzo"]." - ".$_SESSION["ente"]["citta"]." (".$_SESSION["ente"]["provincia"].")</strong><br>";
		if ($_SESSION["ente"]["telefono"] != "") {$head .= "Tel. ".$_SESSION["ente"]["telefono"]."<br>";
		}

		if ($_SESSION["ente"]["fax"] != "") {$head .= "Fax. ".$_SESSION["ente"]["fax"]."<br>";
		}

		if ($_SESSION["ente"]["email"] != "") {$head .= "Email: <a href=\"mailto:".$_SESSION["ente"]["email"]."\">".$_SESSION["ente"]["email"]."</a><br>";
		}

		if ($_SESSION["ente"]["pec"] != "") {$head .= "PEC: <a href=\"mailto:".$_SESSION["ente"]["pec"]."\">".$_SESSION["ente"]["pec"]."</a><br>";
		}

		$head .= "</div></td></tr>";
	} else {
		$head .= "<tr><td><img src=\"https://gare.comune.roma.it/img/tuttogarepa-logo-software-sx-small.png\" alt=\"Stazione Appalti\"></td>";
		$head .= "<td>";
		$head .= "<h1>Stazione Appalti.it</h1>";
		$head .= "</td></tr>";
	}
	$head .= "</table></div>";
	$head .= "<hr><div class=\"padding\">";

	$bottom = "</div>";
	$bottom .= "<div id=\"bottom\">";
	$bottom .= "<img src=\"https://gare.comune.roma.it/img/tuttogarepa-logo-software-sx-small.png\" alt=\"Stazione Appalti\">";
	$bottom .= "</div>";
	$bottom .= "</body></html>";
	$errore = "";
	if (!is_array($destinatari)) $destinatari = array($destinatari);
	foreach ($destinatari as $destinatario) {
		$mail = null;
		$mail = &prepare_mailer($_SESSION["config"]["nome_sito"]." - ".$oggetto, $head.$corpo.$bottom, $codice_pec);
		$mail->AddAddress($destinatario);
		if (!$mail->Send()) {
			$errore .= "Problema durante l'invio.\n";
			$errore .= "Errore classe: ".$mail->ErrorInfo;
		}
	}
	unset($mail);
	return $errore;
}

function differenza_ore($ora1, $ora2, $sep) {
	$part      = explode($sep, $ora1);
	$arr       = explode($sep, $ora2);
	$diff      = mktime($arr[0], $arr[1])-mktime($part[0], $part[1]);
	$ore       = floor($diff/(60*60));
	$minuti    = ($diff/60)%60;
	$ore       = str_pad($ore, 2, 0, STR_PAD_LEFT);
	$minuti    = str_pad($minuti, 2, 0, STR_PAD_LEFT);
	$risultato = $ore.":".$minuti;
	return $risultato;
}
function somma_ore($ora1, $ora2, $sep) {
	$ora1   = explode($sep, $ora1);
	$ora2   = explode($sep, $ora2);
	$ore    = $ora1[0]+$ora2[0];
	$minuti = $ora1[1]+$ora2[1];
	if ($minuti > 59) {
		$minuti = $minuti-60;
		$ore += 1;
	}
	$ore       = str_pad($ore, 2, 0, STR_PAD_LEFT);
	$minuti    = str_pad($minuti, 2, 0, STR_PAD_LEFT);
	$risultato = $ore.":".$minuti;
	return $risultato;
}

function sizeofvar($var) {
	$start_memory = memory_get_usage();
	$tmp          = unserialize(serialize($var));
	return memory_get_usage()-$start_memory;
}

function get_client_ip() {
	$ipaddress = '';
	if (isset($_SERVER['HTTP_CLIENT_IP'])) {
		$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
		$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	} else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
		$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	} else if (isset($_SERVER['HTTP_FORWARDED'])) {
		$ipaddress = $_SERVER['HTTP_FORWARDED'];
	} else if (isset($_SERVER['REMOTE_ADDR'])) {
		$ipaddress = $_SERVER['REMOTE_ADDR'];
	} else {

		$ipaddress = 'UNKNOWN';
	}

	return $ipaddress;
}

function is_dir_empty($dir) {
	if (!is_readable($dir)) {return NULL;
	}

	return (count(scandir($dir)) == 2);
}

function getBrowser() {
	$u_agent  = $_SERVER['HTTP_USER_AGENT'];
	$bname    = 'Unknown';
	$platform = 'Unknown';
	$version  = "";

	//First get the platform?
	if (preg_match('/linux/i', $u_agent)) {
		$platform = 'linux';
	} elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
		$platform = 'mac';
	} elseif (preg_match('/windows|win32/i', $u_agent)) {
		$platform = 'windows';
	}

	// Next get the name of the useragent yes seperately and for good reason
	if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
		$bname = 'Internet Explorer';
		$ub    = "MSIE";
	} elseif (preg_match('/Firefox/i', $u_agent)) {
		$bname = 'Mozilla Firefox';
		$ub    = "Firefox";
	} elseif (preg_match('/Chrome/i', $u_agent)) {
		$bname = 'Google Chrome';
		$ub    = "Chrome";
	} elseif (preg_match('/Safari/i', $u_agent)) {
		$bname = 'Apple Safari';
		$ub    = "Safari";
	} elseif (preg_match('/Opera/i', $u_agent)) {
		$bname = 'Opera';
		$ub    = "Opera";
	} elseif (preg_match('/Netscape/i', $u_agent)) {
		$bname = 'Netscape';
		$ub    = "Netscape";
	}

	// finally get the correct version number
	$known   = array('Version', $ub, 'other');
	$pattern = '#(?<browser>'.join('|', $known).
	')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	if (!preg_match_all($pattern, $u_agent, $matches)) {
		// we have no matching number just continue
	}

	// see how many we have
	$i = count($matches['browser']);
	if ($i != 1) {
		//we will have two since we are not using 'other' argument yet
		//see if version is before or after the name
		if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
			$version = $matches['version'][0];
		} else {
			$version = $matches['version'][1];
		}
	} else {
		$version = $matches['version'][0];
	}

	// check if we have a number
	if ($version == null || $version == "") {$version = "?";}

	return array(
		'userAgent' => $u_agent,
		'name'      => $bname,
		'version'   => $version,
		'platform'  => $platform,
		'pattern'   => $pattern,
	);
}

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath deve essere una directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}


	function array2XML($dgue,$last_key="",$no_empty=false) {
		$xml = "";
		foreach($dgue AS $key => $element) {
			if (!empty($element) || (empty($element) && is_numeric($element))) {
				$open_tag = $key;
				$close_tag = $key;
				$ignore_tag = false;
				if ($key === '$') {
					$open_tag = "";
					$close_tag = "";
				} else if (is_numeric($key)) {
					$open_tag = $last_key;
					$close_tag = $last_key;
				}

				$attribute = "";
				if (is_array($element)) {
					foreach ($element AS $sub_key => $sub_element) {
						if (strpos($sub_key,"@") === 0) {
							$sub_element = trim($sub_element);
							if(!$no_empty || ($no_empty && strlen($sub_element) > 0)) {
								$attribute.= " " . ltrim($sub_key,"@") . "=\"".$sub_element."\"";
							}
							unset($element[$sub_key]);
						}
						if (is_numeric($sub_key)) $ignore_tag = true;
					}
					if (!empty($open_tag) && !$ignore_tag) {
						$xml .= "<".$open_tag;
						if (!empty($attribute)) $xml.= " " . $attribute;
						$xml .= ">";
					}
					$xml.= array2XML($element,$open_tag,$no_empty);
					if ($close_tag && !$ignore_tag) $xml .= "</" . $close_tag .">\n";
				} else {
					if (!empty($open_tag)) $xml .= "<".$open_tag.">";
						$xml.= $element;
					if ($close_tag) $xml .= "</" . $close_tag .">\n";
				}
			}
		}
		return $xml;
	}

	function cscpv_fabbisogno($codice_iniziativa,$codice_ente,$cpv) {
		global $pdo;
		$stato = "disabled";

		if (!empty($codice_iniziativa) && !empty($codice_ente) && !empty($cpv)) {
			$bind = array(":codice"=>$codice_iniziativa,":codice_ente"=>$codice_ente);
			$strsql = "SELECT b_fabbisogno.* FROM b_fabbisogno
								 JOIN r_enti_fabbisogno ON b_fabbisogno.codice = r_enti_fabbisogno.codice_fabbisogno
								 WHERE b_fabbisogno.codice = :codice AND r_enti_fabbisogno.codice_ente = :codice_ente ";
		  $risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$iniziativa = $risultato->fetch(PDO::FETCH_ASSOC);
				$sql = "SELECT * FROM b_schema_fabbisogno WHERE codice_fabbisogno = :codice AND cpv = :cpv AND obbligatorio = 'S' AND attivo = 'S'";
				$ris_schema = $pdo->bindAndExec($sql,array(":codice"=>$iniziativa["codice"],":cpv"=>$cpv));
				if ($ris_schema->rowCount() == 0) {
					$sql = "SELECT * FROM b_schema_fabbisogno WHERE codice_fabbisogno = :codice AND cpv = 0 AND obbligatorio = 'S' AND attivo = 'S'";
					$ris_schema = $pdo->bindAndExec($sql,array(":codice"=>$iniziativa["codice"]));
				}
				if ($ris_schema->rowCount() > 0) {
					$empty = 0;
					while($voce = $ris_schema->fetch(PDO::FETCH_ASSOC)) {
						$sql = "SELECT * FROM b_risposte_fabbisogno WHERE codice_schema = :voce AND cpv = :cpv AND codice_ente = :codice_ente AND valore <> ''";
						$bind = array(":voce"=>$voce["codice"],":cpv"=>$cpv,":codice_ente"=>$codice_ente);
						$ris_check = $pdo->bindAndExec($sql,$bind);
						if ($ris_check->rowCount()==0) $empty++;
					}
					if ($empty==0) {
						$stato = "V";
					} else if ($ris_schema->rowCount() == $empty) {
						$stato = "I";
					} else {
						$stato = "C";
					}
				}
			}
		}
		return $stato;
	}

	function getTypeAndExtension($file) {
		$return = array();
		global $config;
		if (file_exists($file)) {
			$unlink = false;
			$content = file_get_contents($file);
			$test64 = base64_decode($content,true);
			if ($test64 !== false) {
				$name = "gtae64_" . time();
				$path = sys_get_temp_dir() ."/". $name;
				if (file_put_contents($path,$test64)) {
					$unlink = true;
					$file = $path;
				}
			}
			// $finfo = finfo_open(FILEINFO_MIME_TYPE);
			// $type = finfo_buffer($finfo, $content);
			$type = mime_content_type($file);
			if ($unlink) unlink($file);
			$return["type"] = $type;
			if ($type=="application/octet-stream") {
				$return["ext"] = ".p7m";
				if (!is_dir( $config["chunk_folder"] . '/ext_test')) mkdir( $config["chunk_folder"] . '/ext_test',0777,true);
				$tmp_file = (!empty($_SESSION["codice_utente"])) ? $_SESSION["codice_utente"] : rand();
				$tmp_file .= "_" . rand();
				$tmp_file = $config["chunk_folder"] . '/ext_test/'.$tmp_file;
				$comando = $config["bash_folder"].'/estrai.bash \'' . $file . '\' \'' . $tmp_file .'\'';
				$esito = shell_exec("sh " . $comando . " 2>&1");
				if (trim($esito)=="Verification successful") {
					$ext = getTypeAndExtension($tmp_file);
					if (count($ext) > 0 && !empty($ext["ext"])) {
						$return["ext"] = $ext["ext"] . $return["ext"];
					}
					unlink($tmp_file);
				}
			} else {
				$extension = explode("/",$type);
				$extension = $extension[1];
				if (strpos($extension,"rar")!==FALSE) $extension = "rar";
				if (strpos($extension,"7z")!==FALSE) $extension = "7z";
				if (strpos($extension,"plain")!==FALSE) $extension = "xml";
				switch($return["type"]) {
					case "application/msword": $extension = "doc"; break;
					case "application/msword": $extension = "dot"; break;
					case "application/vnd.openxmlformats-officedocument.wordprocessingml.document": $extension = "docx"; break;
					case "application/vnd.openxmlformats-officedocument.wordprocessingml.template": $extension = "dotx"; break;
					case "application/vnd.ms-word.document.macroEnabled.12": $extension = "docm"; break;
					case "application/vnd.ms-word.template.macroEnabled.12": $extension = "dotm"; break;
					case "application/vnd.ms-excel": $extension = "xls"; break;
					case "application/vnd.ms-excel": $extension = "xlt"; break;
					case "application/vnd.ms-excel": $extension = "xla"; break;
					case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet": $extension = "xlsx"; break;
					case "application/vnd.openxmlformats-officedocument.spreadsheetml.template": $extension = "xltx"; break;
					case "application/vnd.ms-excel.sheet.macroEnabled.12": $extension = "xlsm"; break;
					case "application/vnd.ms-excel.template.macroEnabled.12": $extension = "xltm"; break;
					case "application/vnd.ms-excel.addin.macroEnabled.12": $extension = "xlam"; break;
					case "application/vnd.ms-excel.sheet.binary.macroEnabled.12": $extension = "xlsb"; break;
					case "application/vnd.ms-powerpoint": $extension = "ppt"; break;
					case "application/vnd.ms-powerpoint": $extension = "pot"; break;
					case "application/vnd.ms-powerpoint": $extension = "pps"; break;
					case "application/vnd.ms-powerpoint": $extension = "ppa"; break;
					case "application/vnd.openxmlformats-officedocument.presentationml.presentation": $extension = "pptx"; break;
					case "application/vnd.openxmlformats-officedocument.presentationml.template": $extension = "potx"; break;
					case "application/vnd.openxmlformats-officedocument.presentationml.slideshow": $extension = "ppsx"; break;
					case "application/vnd.ms-powerpoint.addin.macroEnabled.12": $extension = "ppam"; break;
					case "application/vnd.ms-powerpoint.presentation.macroEnabled.12": $extension = "pptm"; break;
					case "application/vnd.ms-powerpoint.template.macroEnabled.12": $extension = "potm"; break;
					case "application/vnd.ms-powerpoint.slideshow.macroEnabled.12": $extension = "ppsm"; break;
					case "application/vnd.ms-access": $extension = "mdb"; break;
				}
				$return["ext"] = ".".$extension;
			}
		}
		return (count($return) > 0) ? $return : false;
	}

	function delete_directory($dir) {
    if ($handle = opendir($dir)) {
      while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
          if(is_dir($dir.$file)) {
            if(!@rmdir($dir.$file)) {
              delete_directory($dir.$file.'/');
            }
          } else {
            @unlink($dir.$file);
          }
        }
      }
      closedir($handle);
      @rmdir($dir);
    }
  }

	function getListeSIMOG() {
		global $root;
		$return = false;
		if (file_exists($root."/inc/liste-simog.xml")) {
			require_once($root."/inc/xml2json.php");
			$schema = simplexml_load_file($root.'/inc/liste-simog.xml');
			$schema = xmlToArray($schema);
			if (!empty($schema["schema"]["xsd:simpleType"])) {
				$selects = array();
				foreach($schema["schema"]["xsd:simpleType"] AS $select) {
					$selects[$select["@name"]] = array();
					foreach ($select["xsd:restriction"]["xsd:enumeration"] as $value) {
						$selects[$select["@name"]][$value["@value"]] = (!is_array($value["xsd:annotation"]["xsd:documentation"])) ? $value["xsd:annotation"]["xsd:documentation"] : implode(" - " , $value["xsd:annotation"]["xsd:documentation"]);
						$alert = strpos($selects[$select["@name"]][$value["@value"]],"***");
						if ($alert !== false) {
							$selects[$select["@name"]][$value["@value"]] = substr($selects[$select["@name"]][$value["@value"]],0,$alert);
						}
					}
				}
				if (count($selects) > 0) {
					$return = $selects;
				}
			}
		}
		ksort($return);
		return $return;
	}

	function convertDate_SIMOG($array) {
		foreach($array AS $key => $value) {
			if (is_array($value)) {
				$array[$key] = convertDate_SIMOG($value);
			} else {
				if ((stripos($key, "DATA_") !== false) || (stripos($key, "TERMINE_") !== false) && $key != "TERMINE_RIDOTTO") $array[$key] = date2mysql($value);
			}
		}
		return $array;
	}
	function convertDate_FROMSIMOG($array) {
		foreach($array AS $key => $value) {
			if (is_array($value)) {
				$array[$key] = convertDate_SIMOG($value);
			} else {
				if ((stripos($key, "DATA_") !== false) || (stripos($key, "TERMINE_") !== false) && $key != "TERMINE_RIDOTTO") $array[$key] = mysql2date($value);
			}
		}
		return $array;
	}

	function convertDate_DbDateSimog($array) {
		foreach($array AS $key => $value) {
			if (is_array($value)) {
				$array[$key] = convertDate_DbDateSimog($value);
			} else if (!empty($value)) {
				if ((stripos($key, "DATA_") !== false) || (stripos($key, "TERMINE_") !== false) && $key != "TERMINE_RIDOTTO") {
					$date = new DateTime(($value));
					$array[$key] = $date->format(DateTime::ATOM);
				}
			}
		}
		return $array;
	}

	function removeEmpty($array,$ignore=TRUE) {
		$list = array();
		if ($ignore) $list = array("CF_AMM_AGENTE","DEN_AMM_AGENTE","CUI","CODICE_STATO","FLAG_AVVALIMENTO","CF_AMM","DEN_AMM","CF_SA","DEN_SA","CODICE_CC","DENOM_CC","NUM_IMPRESE_INVITATE","NUM_MANIF_INTERESSE","NUM_OFFERTE_FUORI_SOGLIA","NUM_IMP_ESCL_INSUF_GIUST","CUP","NUM_OFFERTE_ESCLUSE","DURATA_SOSP","NUM_INFORTUNI","NUM_INF_PERM","NUM_INF_MORT","NUM_IMPRESE_OFFERENTI","NUM_OFFERTE_AMMESSE","FAX","TELEFONO","EMAIL","INDIRIZZO","COMUNE","CAP");
		if (is_array($array)) {
			foreach($array as $key => $value) {
				if (is_numeric($key) || in_array($key, $list)===false) {
					if (is_array($value)) {
						$array[$key] = removeEmpty($value);
					} else {
						if (empty($value)) unset($array[$key]);
					}
				}
			}
		}
		return $array;
	}

	function getProvinceIT() {
		global $pdo;
		$sql = "SELECT sigla_provincia AS sigla, provincia FROM b_conf_cup_localizzazioni WHERE sigla_provincia IS NOT NULL GROUP BY sigla_provincia ORDER BY provincia";
		$ris = $pdo->query($sql);
		if ($ris->rowCount() > 0) {
			$province = $ris->fetchAll(PDO::FETCH_ASSOC);
			$province[] = array("sigla"=>"EE","provincia"=>"Estero");
			return $province;
		} else {
			return false;
		}
	}

	function getRegioniIT() {
		global $pdo;
		$sql = "SELECT regione FROM b_conf_cup_localizzazioni WHERE regione IS NOT NULL GROUP BY regione ORDER BY regione";
		$ris = $pdo->query($sql);
		if ($ris->rowCount() > 0) {
			$regioni = $ris->fetchAll(PDO::FETCH_ASSOC);
			// $delimiters = "'- \t\r\n\f\v";
			foreach($regioni as $index => $regione) {
				$regioni[$index] = ucwords(strtolower($regione["regione"]));
			}
			$regioni[] = "Estero";
			return $regioni;
		} else {
			return false;
		}
	}

	function getStatiUE() {
		$paesi = array();
		$paesi["AT"] = "Austria";
		$paesi["BE"] = "Belgio";
		$paesi["BG"] = "Bulgaria";
		$paesi["CY"] = "Cipro";
		$paesi["HR"] = "Croazia";
		$paesi["DK"] = "Danimarca";
		$paesi["EE"] = "Estonia";
		$paesi["FI"] = "Finlandia";
		$paesi["FR"] = "Francia";
		$paesi["DE"] = "Germania";
		$paesi["GR"] = "Grecia";
		$paesi["IE"] = "Irlanda";
		$paesi["IT"] = "Italia";
		$paesi["LV"] = "Lettonia";
		$paesi["LT"] = "Lituania";
		$paesi["LU"] = "Lussemburgo";
		$paesi["MT"] = "Malta";
		$paesi["NL"] = "Paesi Bassi";
		$paesi["PL"] = "Polonia";
		$paesi["PT"] = "Portogallo";
		$paesi["GB"] = "Regno Unito";
		$paesi["CZ"] = "Repubblica ceca";
		$paesi["RO"] = "Romania";
		$paesi["SK"] = "Slovacchia";
		$paesi["SI"] = "Slovenia";
		$paesi["ES"] = "Spagna";
		$paesi["SE"] = "Svezia";
		$paesi["HU"] = "Ungheria";
		$paesi["NO"] = "Norvegia";
		$paesi["CH"] = "Svizzera";
		$paesi["EE"] = "Extra-comunitario";
		return $paesi;
	}

	function check_session_id() {
		global $pdo;
		if (isset($_SESSION["codice_utente"]) && $_SESSION["codice_utente"] > 0) {
			$continua = false;
			$sql_check_sess = "SELECT * FROM b_check_sessions WHERE codice_utente = :codice_utente ";
			$ris_check = $pdo->bindAndExec($sql_check_sess,array(":codice_utente"=>$_SESSION["codice_utente"]));
			if ($ris_check->rowCount() > 0) {
				while ($sess = $ris_check->fetch(PDO::FETCH_ASSOC)) {
					$sess["sessionID"] = simple_decrypt($sess["sessionID"],$config["enc_key"]);
					$check = true;
					$values = array("sessionID"=>session_id(),"agent"=>base64_encode($_SERVER ['HTTP_USER_AGENT']),"ip"=>$_SERVER["REMOTE_ADDR"]);
					foreach($values AS $key => $value) {
						if ($sess[$key] != $value) {
							if ($key != "agent" ) {
								$check = false;
							} else {
								$value = purify(base64_decode($value));
								if ($sess[$key] != $value) $check = false;
							}
						}
					}
					if ($check) {
						$continua = true;
						break;
					}
				}
			}
			if (!$continua) {
				echo "<H1>ERRORE RICONOSCIMENTO SESSIONE</H1>";
				session_unset();
				session_destroy();
				die();
			}
		}
	}
	function array_orderby()
	{
		$args = func_get_args();
		$data = array_shift($args);
		foreach ($args as $n => $field) {
			if (is_string($field)) {
				$tmp = array();
				foreach ($data as $key => $row)
					$tmp[$key] = $row[$field];
				$args[$n] = $tmp;
			}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		return array_pop($args);
	}

	function jsonToArray($path) {
		$return = false;
		if (file_exists($path) && (strpos($path,"../")===false)) {
			$return = json_decode(file_get_contents($path),true);
		}
		return $return;
	}


	function gen_uuid() {
	    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
	        // 32 bits for "time_low"
	        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
	        // 16 bits for "time_mid"
	        mt_rand( 0, 0xffff ),
	        // 16 bits for "time_hi_and_version",
	        // four most significant bits holds version number 4
	        mt_rand( 0, 0x0fff ) | 0x4000,
	        // 16 bits, 8 bits for "clk_seq_hi_res",
	        // 8 bits for "clk_seq_low",
	        // two most significant bits holds zero and one for variant DCE1.1
	        mt_rand( 0, 0x3fff ) | 0x8000,
	        // 48 bits for "node"
	        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
	    );
	}

	function check_configurazione_offerta($codice_gara,$codice_lotto = 0) {
			global $pdo;
			$status = "danger";
			$sql = "SELECT * FROM b_valutazione_tecnica
							WHERE codice_gara = :codice_gara AND (codice_lotto = :codice_lotto OR codice_lotto = 0)
							AND codice_padre = 0 ";
			$ris_generali = $pdo->prepare($sql);
			$ris_generali->bindValue(":codice_gara",$codice_gara);
			if (empty($codice_lotto)) {
				$sql_lotti = "SELECT codice FROM b_lotti WHERE codice_gara = :codice_gara ";
				$ris_lotti = $pdo->bindAndExec($sql_lotti,[":codice_gara"=>$codice_gara]);
				$lotti = [["codice"=>0]];
				if ($ris_lotti->rowCount() > 0) $lotti = $ris_lotti->fetchAll(PDO::FETCH_ASSOC);
			} else {
				$lotti = [["codice"=>$codice_lotto]];
			}
			$success = true;
			$sql = "SELECT punteggio FROM b_valutazione_tecnica
							WHERE codice_padre = :codice_criterio ";
			$ris_sub = $pdo->prepare($sql);
			foreach($lotti AS $lotto) {
				$totale = 0;
				$ris_generali->bindValue(":codice_lotto",$lotto["codice"]);
				$ris_generali->execute();
				if ($ris_generali->rowCount() > 0) {
					$status = "warning";
					while($criterio = $ris_generali->fetch(PDO::FETCH_ASSOC)) {
						$ris_sub->bindValue(":codice_criterio",$criterio["codice"]);
						$totale += $criterio["punteggio"];
						$ris_sub->execute();
						if ($ris_sub->rowCount() > 0) {
							$totale_criterio = 0;
							while($sub = $ris_sub->fetch(PDO::FETCH_ASSOC)) $totale_criterio += $sub["punteggio"];
							if ($totale_criterio != $criterio["punteggio"]) $success = false;
						}
					}
					if ($totale != 100) $success = false;
				} else { $success = false; }
			}
			if (!isset($criterio)) $success = false;
			if ($success) $status = "ok";
			return ["status"=>$status,"totale"=>$totale];
	}

	function truncate($number,$decimal=2) {
	  if (strpos($number, ".") !== false) {
	    if (!is_numeric($decimal)) $decimal = 0;
	    $decimal = floor($decimal);
	    list($intero,$decimali) = explode(".",$number);
	    if ($decimal > 0) {
	      return $intero . "." . substr($decimali,0,$decimal);
	    } else {
	      return $intero;
	    }
	  } else {
	    return $number;
	  }
	}

	function getFormOfferta($codice_gara,$codice_lotto,$economica=true,$form=false,$inputs=false,$convertInputs=false) {
		global $pdo;
		global $root;
		$sql = "SELECT b_valutazione_tecnica.*,
									 b_criteri_punteggi.economica,
									 b_criteri_punteggi.temporale,
									 b_criteri_punteggi.migliorativa
						FROM b_valutazione_tecnica
						JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
						WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND
									(b_valutazione_tecnica.codice_lotto = :codice_lotto OR b_valutazione_tecnica.codice_lotto = 0)
						AND b_valutazione_tecnica.valutazione <> '' AND b_valutazione_tecnica.tipo = 'N'
						AND b_valutazione_tecnica.codice NOT IN
						(SELECT codice_padre FROM b_valutazione_tecnica WHERE codice_padre <> 0 AND codice_gara = :codice_gara)";
		$bind = array();
		$bind[":codice_gara"] = $codice_gara;
		$bind[":codice_lotto"] = $codice_lotto;
		$formule = json_decode(file_get_contents($root."/gare/configura_offerta/formule.json"),TRUE);
		if ($economica !== null) {
			if ($economica) {
			 $sql .= "AND (b_criteri_punteggi.economica = 'S' OR b_criteri_punteggi.temporale = 'S') ";
			} else {
				$sql .= "AND (b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N') ";
			}
		}
		$criteri = $pdo->bindAndExec($sql,$bind);
		if ($criteri->rowCount() > 0) {
			$criteri = $criteri->fetchAll(PDO::FETCH_ASSOC);
			ob_start();
			?>
			<table width="100%">
				<?
				foreach($criteri AS $criterio) {
					$tipo_input = "tecnica";
					if ($criterio["migliorativa"] == "S") $tipo_input = "migliorativa";
					if ($criterio["temporale"] == "S") $tipo_input = "temporale";
					if ($criterio["economica"] == "S" && $criterio["migliorativa"] == "N") $tipo_input = "economica";
					$input_name = "offerta[{$tipo_input}][{$criterio["codice"]}]";
					$valore = ($form) ? "" : 0;
					?>
					<tr>
						<th class="etichetta" colspan="6">
							<strong><?= $criterio["descrizione"] ?></strong>
							<?
							if ($form && !empty($formule[$criterio["valutazione"]]["formula"])) {
								?>
								<div style="float:right; padding:10px">
									<small>Formula applicata per il calcolo del punteggio</small><br><strong>
									<?= $formule[$criterio["valutazione"]]["formula"] ?></strong></small>
								</div><div class="clear"></div>
								<?
							}
							?>
						</th>
					</tr>
					<?
					if ($criterio["valutazione"] != "E") {
						if (!empty($inputs)) {
							$valore = (!empty($inputs[$tipo_input][$criterio["codice"]])) ? $inputs[$tipo_input][$criterio["codice"]] : 0;
							$valore = truncate($valore,$criterio["decimali"]);
							if ($criterio["valutazione"] == "O") $valore = (int)floor($valore);
							$inputs[$tipo_input][$criterio["codice"]] = $valore;
						}
						?>
						<tr>
							<td colspan="6" style="text-align:center">
								<?
									if ($criterio["valutazione"] == "O") {
										if ($form) {
											?>
											<div class="dataTables_wrapper">
												<select name="<?= $input_name ?>" value="<?= $valore ?>" rel="S;0;0;N" title="<?= $criterio["descrizione"] ?>">
													<option value="">Seleziona</option>
													<option <?= ($valore === 1) ? "selected" : "" ?> value="1">Si</option>
													<option <?= ($valore === 0) ? "selected" : "" ?> value="0">No</option>
												</select>
											</div>
											<?
										} else {
											echo ($valore === 1) ? "Si" : "No";
										}
									} else {
										if ($form) {
											?>
											<input type="text" name="<?= $input_name ?>" class="titolo_edit" style="max-width:300px; text-align:center" value="<?= $valore ?>" rel="S;0;0;N" title="<?= $criterio["descrizione"] ?>">
											<?
										} else {
											echo number_format($valore,$criterio["decimali"],",",".");
										}
									}
								?>
							</td>
						</tr>
						<?
					} else {
						$tipo_input = "elenco_prezzi";
						?>
						<tr>
							<th class="etichetta">Tipo</th>
							<th class="etichetta">Voce</th>
							<th class="etichetta">U.d.m.</th>
							<th class="etichetta">Quantit&agrave;</th>
							<th class="etichetta">Prezzo unitario offerto</th>
							<th class="etichetta">Totale</th>
						</tr>
						<?
						$sql_elenco = "SELECT * FROM b_elenco_prezzi WHERE codice_criterio = :codice_criterio";
						$ris_elenco = $pdo->bindAndExec($sql_elenco,array(":codice_criterio"=>$criterio["codice"]));
						if ($ris_elenco->rowCount() > 0) {
							$valore_totale = 0;
							while($voce = $ris_elenco->fetch(PDO::FETCH_ASSOC)) {
								$input_name = "offerta[elenco_prezzi][{$voce["codice"]}]";
								$valore = ($form) ? "" : 0;
								$totale_voce = 0;
								if (!empty($inputs)) {
									$valore = (!empty($inputs[$tipo_input][$voce["codice"]])) ? $inputs[$tipo_input][$voce["codice"]] : 0;
									$valore = truncate($valore,$criterio["decimali"]);
									$inputs[$tipo_input][$voce["codice"]] = $valore;
									$totale_voce = $valore * $voce["quantita"];
									$valore_totale += $totale_voce;
									$totale_voce = truncate($totale_voce,$criterio["decimali"]);
									$valore_totale = truncate($valore_totale,$criterio["decimali"]);
								}
							?>
							<tr>
								<td style="text-align:center"><?= ucfirst($voce["tipo"]) ?></td>
								<td width="30%"><?= $voce["descrizione"] ?></td>
								<td style="text-align:center"><?= $voce["unita"] ?></td>
								<td style="text-align:right"><?= number_format($voce["quantita"],2,",",".") ?></td>
								<td style="text-align:center">
								<?
									if ($form) {
										?>
										<input type="text" name="<?= $input_name ?>" class="titolo_edit prezzo_<?= $criterio["codice"] ?>" data-quantita="<?= $voce["quantita"] ?>" data-voce="<?= $voce["codice"] ?>" style="text-align:center;max-width:300px" value="<?= $valore ?>" rel="S;0;0;N" title="<?= $criterio["descrizione"] ?>">
										<?
									} else {
										echo number_format($valore,$criterio["decimali"],",",".");
									}
								?>
								</td>
								<td style="text-align:right" id='totale_voce_<?= $voce["codice"] ?>'>
									<?= number_format($totale_voce,$criterio["decimali"],",",".") ?>
								</td>
							</tr>
							<?
							}
							?>
							<tr>
								<th class="etichetta" colspan="5"><strong>Totale <?= $criterio["descrizione"] ?></strong></th>
								<td style="text-align:right" id="totale_<?= $criterio["codice"] ?>">
									<? if ($form) { ?>
										<script>
											$(".prezzo_<?= $criterio["codice"] ?>").change(function() {
												totale_offerta = 0;
												$(".prezzo_<?= $criterio["codice"] ?>").each(function() {
													if ($(this).val() != "") {
														if (valida($(this)) == "") {
															totale_voce = +(parseFloat($(this).val())) * $(this).data("quantita");
															totale_offerta = totale_offerta + totale_voce;
															totale_voce = number_format(totale_voce,<?= $criterio["decimali"] ?>,",",".");
															$("#totale_voce_"+$(this).data("voce")).html("&euro; " + totale_voce);
														}
													}
												});
												totale_offerta = number_format(totale_offerta,<?= $criterio["decimali"] ?>,",",".");
												$("#totale_<?= $criterio["codice"] ?>").html("&euro; " + totale_offerta);
											});
										</script>
									<? } ?>
									<?= number_format($valore_totale,$criterio["decimali"],",",".") ?>
								</td>
							</tr>
							<?
						} else {
							?>
							<tr>
								<td colspan="6"><strong>ERRORE CONFIGURAZIONE ELENCO PREZZI</strong></td>
							</tr>
							<?
						}
					}
				}
				?>
			</table>
			<?
			$html = ob_get_clean();
			if ($convertInputs) {
				return array("html"=>$html,"inputs"=>$inputs);
			} else {
				return $html;
			}
		} else {
			return false;
		}
	}

	function getPunteggiCriterio($codice_gara,$codice_lotto,$filtro=null,$forceOfferte=null) {
		global $pdo;
		$return = false;
		$bind=array();
		$bind[":codice_gara"] = $codice_gara;
		$bind[":codice_lotto"] = $codice_lotto;
		$strsql = "SELECT r_partecipanti.codice FROM r_partecipanti
							 WHERE r_partecipanti.codice_gara = :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto
							 AND r_partecipanti.codice_capogruppo = 0
							 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)
							 AND r_partecipanti.ammesso = 'S'";
		$partecipanti = $pdo->bindAndExec($strsql,$bind);
		$partecipanti = $partecipanti->fetchAll(PDO::FETCH_ASSOC);
		if (empty($codice_lotto)) {
			$bindImporti = array();
			$bindImporti[":codice_gara"] = $codice_gara;
			$sql = "SELECT sum(b_importi_gara.importo_base) AS importo_base,
						sum(b_importi_gara.importo_oneri_ribasso) AS importo_oneri_ribasso,
						sum(b_importi_gara.importo_oneri_no_ribasso) AS importo_oneri_no_ribasso,
						sum(b_importi_gara.importo_personale) AS importo_personale
						FROM b_importi_gara WHERE codice_gara = :codice_gara";
			$ris_importi = $pdo->bindAndExec($sql,$bindImporti);
			if ($ris_importi->rowCount()>0) {
				$importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
			}
		} else {
			$bindImporti = array();
			$bindImporti[":codice_gara"] = $codice_gara;
			$bindImporti[":codice_lotto"] = $codice_lotto;
			$sql = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto";
			$ris_importi = $pdo->bindAndExec($sql,$bindImporti);
			if ($ris_importi->rowCount()>0) {
				$importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
			}
		}
		$numero_partecipanti = count($partecipanti);
		if ($numero_partecipanti>0) {
			$strsql = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica
			JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
			WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND b_valutazione_tecnica.valutazione <> ''
			AND (b_valutazione_tecnica.codice_lotto = :codice_lotto OR b_valutazione_tecnica.codice_lotto = 0)";
			if (!empty($filtro)) {
				if (is_numeric($filtro)) {
					$bind[":codice_criterio"] = $filtro;
					$strsql .= " AND b_valutazione_tecnica.codice = :codice_criterio ";
					$simple_return = true;
				} else if ($filtro == "economica") {
					$strsql .= " AND (b_criteri_punteggi.economica = 'S' OR  b_criteri_punteggi.temporale = 'S')";
				} else if ($filtro == "tecnica") {
					$strsql .= " AND b_criteri_punteggi.economica = 'N' AND  b_criteri_punteggi.temporale = 'N'";
				} else {
					return false;
				}
			}
			$criteri = $pdo->bindAndExec($strsql,$bind);
			if ($criteri->rowCount()>0) {
				$return = [];
				while($criterio = $criteri->fetch(PDO::FETCH_ASSOC)) {
					$punteggio_max = $criterio["punteggio"];
					$offerte = array();
					if ($forceOfferte === null) {
						$bind=array();
						$bind[":codice_dettaglio"] = $criterio["codice"];
						if ($criterio["valutazione"] == "E") {
							$strsql = "SELECT b_offerte_decriptate.codice_partecipante, SUM(b_offerte_decriptate.offerta * b_elenco_prezzi.quantita) AS offerta
												 FROM b_offerte_decriptate
												 JOIN b_elenco_prezzi ON b_offerte_decriptate.codice_dettaglio = b_elenco_prezzi.codice
												 WHERE b_offerte_decriptate.codice_partecipante = :codice
												 AND b_elenco_prezzi.codice_criterio = :codice_dettaglio
												 AND b_offerte_decriptate.tipo = 'elenco_prezzi'
												 GROUP BY b_offerte_decriptate.codice_partecipante ";
							$criterio["valutazione"] = "I";
							if (!empty($criterio["options"])) {
								$criterio["valutazione"] = "Q";
								$bilineare_prezzo = true;
								if ($codice_lotto==0) {
									$bind_prezzo = array();
									$bind_prezzo[":codice_gara"] = $codice_gara;
									$sql = "SELECT sum(b_importi_gara.importo_base) AS importo_base,
														sum(b_importi_gara.importo_oneri_ribasso) AS importo_oneri_ribasso,
														sum(b_importi_gara.importo_oneri_no_ribasso) AS importo_oneri_no_ribasso,
														sum(b_importi_gara.importo_personale) AS importo_personale
														FROM b_importi_gara WHERE codice_gara = :codice_gara";
									$ris_importi = $pdo->bindAndExec($sql,$bind_prezzo);
									if ($ris_importi->rowCount()>0) $importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
								} else {
									$bind_prezzo = array();
									$bind_prezzo[":codice_gara"] = $codice_gara;
									$bind_prezzo[":codice_lotto"] = $codice_lotto;
									$sql = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto";
									$ris_importi = $pdo->bindAndExec($sql,$bind_prezzo);
									if ($ris_importi->rowCount()>0) $importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
								}
								$importo = $importi["importo_base"];
							}
						} else {
							$strsql = "SELECT *
												 FROM b_offerte_decriptate
												 WHERE codice_partecipante = :codice
												 AND codice_dettaglio = :codice_dettaglio
												 AND tipo <> 'elenco_prezzi'
												 ORDER BY timestamp DESC";
						}
						foreach ($partecipanti as $partecipante) {
							$bind[":codice"] = $partecipante["codice"];
							$ris_offerte = $pdo->bindAndExec($strsql,$bind);
							if ($ris_offerte->rowCount()>0) {
								$offerta = $ris_offerte->fetch(PDO::FETCH_ASSOC);
								if (isset($bilineare_prezzo)) {
									$perc = $offerta["offerta"] * 100 / $importo;
									$perc = 100 - $perc;
									if ($perc < 0) $perc = 0;
									$offerte[$partecipante["codice"]] = truncate($perc,$criterio["decimali"]);
								} else {
									$offerte[$partecipante["codice"]] = truncate($offerta["offerta"],$criterio["decimali"]);
								}
							}
						}
					} else if (is_array($forceOfferte)) $offerte = $forceOfferte;
					if (count($offerte) > 0) {
						$punteggi = [];
						$settings = json_decode($criterio["options"],true);
						switch($criterio["valutazione"]) {
							case "P":
								$max = max($offerte);
								$chiavi = array_keys($offerte);
								foreach ($chiavi as $chiave) {
									$punteggio_ottenuto = 0;
									if ($max>0) $punteggio_ottenuto = $offerte[$chiave] * $punteggio_max / $max;
									$punteggi[$chiave] = truncate($punteggio_ottenuto,$criterio["decimali"]);
								}
							break;
							case "B":
								$max = max($offerte);
								$media = array_sum($offerte)/count($offerte);
								$chiavi = array_keys($offerte);
								foreach ($chiavi as $chiave) {
									$punteggio_ottenuto = 0;
									$offerta = $offerte[$chiave];
									if ($max>0) {
										if ($offerta > $media) {
											$punteggio_ottenuto = ($settings + (1 - $settings) * (($offerta - $media) / ($max - $media))) * $punteggio_max;
										} else {
											$punteggio_ottenuto = ($settings * ($offerta / $media)) * $punteggio_max;
										}
									}
									$punteggi[$chiave] = truncate($punteggio_ottenuto,$criterio["decimali"]);
								}
							break;
							case "Q":
								$max = max($offerte);
								$chiavi = array_keys($offerte);
								foreach ($chiavi as $chiave) {
									$punteggio_ottenuto = 0;
									if ($max>0) $punteggio_ottenuto = pow(($offerte[$chiave] / $max),$settings) * $punteggio_max;
									$punteggi[$chiave] = truncate($punteggio_ottenuto,$criterio["decimali"]);
								}
							break;
							case "K":
								$chiavi = array_keys($offerte);
								foreach ($chiavi as $chiave) {
									$punteggio_ottenuto = $punteggio_max * (1-(pow(1-(1-($offerte[$chiave]/$importi["importo_base"])),$settings)));
									$punteggi[$chiave] = truncate($punteggio_ottenuto,$criterio["decimali"]);
								}
							break;
							case "W":
								$chiavi = array_keys($offerte);
								foreach ($chiavi as $chiave) {
									$punteggio_ottenuto = $punteggio_max * (1-(pow(1-($offerte[$chiave]/100),$settings)));
									$punteggi[$chiave] = truncate($punteggio_ottenuto,$criterio["decimali"]);
								}
							break;
							case "I":
								$min = min($offerte);
								$chiavi = array_keys($offerte);
								foreach ($chiavi as $chiave) {
									$punteggio_ottenuto = $min * $punteggio_max / $offerte[$chiave];
									$punteggi[$chiave] = truncate($punteggio_ottenuto,$criterio["decimali"]);
								}
							break;
							case "O":
								$chiavi = array_keys($offerte);
								foreach ($chiavi as $chiave) {
									$punteggi[$chiave] = 0;
									if ($offerte[$chiave] == 1) $punteggi[$chiave] = $punteggio_max;
								}
							break;
							case "S":
								$chiavi = array_keys($offerte);
								$ranges = $settings["range"];
								if (count($ranges) > 0) {
									foreach ($chiavi as $chiave) {
										$offerta = $offerte[$chiave];
										$offerte[$chiave] = 0;
										foreach($ranges AS $range) {
											if ($offerta >= $range["minimo"] && $offerta <= $range["massimo"])
												$offerte[$chiave] = $range["punti"];
										}
									}
									reset($chiavi);
									$max = max($offerte);
									foreach ($chiavi as $chiave) {
										if (isset($settings["riparametra"])) {
											$punteggio_ottenuto = 0;
											if ($max>0) $punteggio_ottenuto = $offerte[$chiave] * $punteggio_max / $max;
										} else {
											$punteggio_ottenuto = $offerte[$chiave];
										}
										$punteggi[$chiave] = truncate($punteggio_ottenuto,$criterio["decimali"]);
									}
								}
							break;
						}
					}
					if (isset($punteggi)) {
						if (isset($simple_return)) {
							$return = $punteggi;
						} else {
							$return[$criterio["codice"]] = $punteggi;
						}
					}
				}
			}
		}
		return $return;
	}

	function getImportoAggiudicazione($codice_gara,$codice_lotto = 0) {
		global $pdo;
		$return = false;
		$rialzo = false;
			$elenco_prezzi = false;
		$sql = "SELECT codice FROM b_gare WHERE codice = :codice_gara AND nuovaOfferta ='S'";
		$sqlNorma = "SELECT codice FROM b_gare WHERE codice = :codice_gara AND norma ='2023-36'";
		$bind = array();
		$bind[":codice_gara"] = $codice_gara;
		$sql_tipo = "SELECT opzione FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione IN (SELECT codice FROM b_opzioni WHERE codice_gruppo = 40)";
		$ris_tipo = $pdo->bindAndExec($sql_tipo,$bind);
		$rialzo = false;
		if ($ris_tipo->rowCount() > 0) {
		  $opzione = $ris_tipo->fetch(PDO::FETCH_ASSOC);
		  if ($opzione["opzione"] == "270") $rialzo = true;
				if ($opzione["opzione"] == "58") $elenco_prezzi =true;
		}
		$nuovaOfferta = false;
		$manodopera202336 = false;
		if ($pdo->bindAndExec($sql,[":codice_gara"=>$codice_gara])->rowCount() > 0) $nuovaOfferta = true;
		if ($pdo->bindAndExec($sqlNorma,[":codice_gara"=>$codice_gara])->rowCount() > 0) $manodopera202336 = true;
		if ($codice_lotto==0) {
		  $bind = array();
		  $bind[":codice_gara"] = $codice_gara;
		  $sql = "SELECT sum(b_importi_gara.importo_base) AS importo_base,
					sum(b_importi_gara.importo_oneri_ribasso) AS importo_oneri_ribasso,
					sum(b_importi_gara.importo_oneri_no_ribasso) AS importo_oneri_no_ribasso,
					sum(b_importi_gara.importo_personale) AS importo_personale
					FROM b_importi_gara WHERE codice_gara = :codice_gara";
		  $ris_importi = $pdo->bindAndExec($sql,$bind);
		  if ($ris_importi->rowCount()>0) $importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
		} else {
		  $bind = array();
		  $bind[":codice_gara"] = $codice_gara;
		  $bind[":codice_lotto"] = $codice_lotto;
		  $sql = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto";
		  $ris_importi = $pdo->bindAndExec($sql,$bind);
		  if ($ris_importi->rowCount()>0) $importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
		}
		if (isset($importi)) {
		  $getPunteggioEconomico = false;
		  if ($nuovaOfferta) {
			$sql = "SELECT b_valutazione_tecnica.codice, b_valutazione_tecnica.valutazione,
					b_criteri_punteggi.economica,
					b_criteri_punteggi.temporale,
					b_criteri_punteggi.migliorativa
					FROM b_valutazione_tecnica
					JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
					WHERE b_valutazione_tecnica.codice_gara = :codice_gara
					AND (b_valutazione_tecnica.codice_lotto = :codice_lotto OR b_valutazione_tecnica.codice_lotto = 0)
					";
			$ris_criteri = $pdo->bindAndExec($sql,[":codice_gara"=>$codice_gara,":codice_lotto"=>$codice_lotto]);
			if ($ris_criteri->rowCount() == 1) {
			  $criterio = $ris_criteri->fetch(PDO::FETCH_ASSOC);
			  if (($criterio["valutazione"] == "P" || $criterio["valutazione"] == "E")
				 && $criterio["economica"] == 'S'
				 && $criterio["temporale"] = 'N'
				 && $criterio["migliorativa"] = 'N') $getPunteggioEconomico = true;
			} else {
						$criteri_economici = [];
						while($criterio = $ris_criteri->fetch(PDO::FETCH_ASSOC)) {
							if (($criterio["valutazione"] == "P" || $criterio["valutazione"] == "E")
				 && $criterio["economica"] == 'S'
				 && $criterio["temporale"] = 'N'
				 && $criterio["migliorativa"] = 'N') $criteri_economici[] = $criterio;
						}
					}
		  } else {
			$sql = "SELECT b_valutazione_tecnica.*
					FROM b_valutazione_tecnica
					WHERE b_valutazione_tecnica.codice_gara = :codice_gara";
			$ris_criteri = $pdo->bindAndExec($sql,[":codice_gara"=>$codice_gara]);
			if ($ris_criteri->rowCount() == 0) $getPunteggioEconomico = true;
		  }
		  if (!$getPunteggioEconomico) {
					if (!$nuovaOfferta) {
						$strsql = "SELECT b_offerte_decriptate.*
											 FROM b_offerte_decriptate
											 JOIN r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
											 WHERE
											 r_partecipanti.codice_gara = :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto
											 AND r_partecipanti.primo = 'S'
											 AND b_offerte_decriptate.tipo = 'economica' ";
					$ris_offerte = $pdo->bindAndExec($strsql,[":codice_gara"=>$codice_gara,":codice_lotto"=>$codice_lotto]);
					if ($ris_offerte->rowCount()>0) {
					  if ($ris_offerte->rowCount()>1 && $elenco_prezzi && isset($importi)) {
						$totale_offerta = 0;
						while($offerta = $ris_offerte->fetch(PDO::FETCH_ASSOC)) $totale_offerta += $offerta["offerta"];
								if ($totale_offerta < 0) $totale_offerta = 0;
								$base_gara = $importi["importo_base"]; // + $importi["importo_oneri_ribasso"] + $importi["importo_personale"];
					  $percentuale = ($base_gara - $totale_offerta)/$base_gara * 100;
							} else if ($ris_offerte->rowCount() === 1) {
								$offerta = $ris_offerte->fetch(PDO::FETCH_ASSOC);
						$percentuale = $offerta["offerta"];
							}
						}
					} else {
						if (isset($criteri_economici) && count($criteri_economici) === 1) {
							$criterio = $criteri_economici[0];
							if ($criterio["valutazione"] == "P") {
								$strsql = "SELECT b_offerte_decriptate.*
													 FROM b_offerte_decriptate
													 JOIN r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
													 WHERE r_partecipanti.codice_gara = :codice_gara
													 AND r_partecipanti.codice_lotto = :codice_lotto
													 AND b_offerte_decriptate.codice_dettaglio = :codice_criterio
													 AND r_partecipanti.primo = 'S'
													 AND b_offerte_decriptate.tipo = 'economica' ";
								$ris_offerte = $pdo->bindAndExec($strsql,[":codice_gara"=>$codice_gara,":codice_lotto"=>$codice_lotto,":codice_criterio"=>$criterio["codice"]]);
								if ($ris_offerte->rowCount() === 1) {
									$percentuale = $ris_offerte->fetch(PDO::FETCH_ASSOC)["offerta"];
								}
							} else {
								$strsql = "SELECT SUM(b_offerte_decriptate.offerta * b_elenco_prezzi.quantita) AS offerta
													 FROM b_offerte_decriptate
													 JOIN b_elenco_prezzi ON b_offerte_decriptate.codice_dettaglio = b_elenco_prezzi.codice
													 JOIN r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
													 WHERE r_partecipanti.primo = 'S'
													 AND r_partecipanti.codice_gara = :codice_gara
													 AND r_partecipanti.codice_lotto = :codice_lotto
													 AND b_elenco_prezzi.codice_criterio = :codice_criterio
													 AND b_offerte_decriptate.tipo = 'elenco_prezzi'
													 GROUP BY b_offerte_decriptate.codice_partecipante ";
								$ris_offerte = $pdo->bindAndExec($strsql,[":codice_gara"=>$codice_gara,":codice_lotto"=>$codice_lotto,":codice_criterio"=>$criterio["codice"]]);
								if ($ris_offerte->rowCount() === 1) {
									$totale_offerta = $ris_offerte->fetch(PDO::FETCH_ASSOC)["offerta"];
									if ($totale_offerta < 0) $totale_offerta = 0;
									$base_gara = $importi["importo_base"]; // + $importi["importo_oneri_ribasso"] + $importi["importo_personale"];
						  $percentuale = ($base_gara - $totale_offerta)/$base_gara * 100;
								}
							}
						}
					}
				} else {
			$sql = "SELECT SUM(r_punteggi_gare.punteggio) as ribasso
					FROM r_partecipanti
					  JOIN r_punteggi_gare ON r_partecipanti.codice = r_punteggi_gare.codice_partecipante
					  JOIN b_criteri_punteggi ON r_punteggi_gare.codice_punteggio = b_criteri_punteggi.codice
					WHERE r_partecipanti.primo = 'S'
					  AND b_criteri_punteggi.economica = 'S'
					  AND b_criteri_punteggi.temporale = 'N'
					  AND b_criteri_punteggi.migliorativa = 'N'
					  AND r_partecipanti.codice_gara = :codice
					  AND r_partecipanti.codice_lotto = :codice_lotto
					GROUP BY r_partecipanti.codice ";
			$ris = $pdo->bindAndExec($sql,[":codice"=>$codice_gara,":codice_lotto"=>$codice_lotto]);
			if ($ris->rowCount() > 0) {
			  $percentuale = $ris->fetch(PDO::FETCH_ASSOC)["ribasso"];
			}
		  }
		  if (isset($percentuale) && $percentuale > 0 && isset($importi)) {
					$decimal =3;
					if (isset($criterio["decimali"])) $decimal = $criterio["decimali"];
					$importo_base = $importi["importo_base"]; // + $importi["importo_personale"] + $importi["importo_oneri_ribasso"];
					$costi = $importi["importo_oneri_no_ribasso"];
					if ($manodopera202336) {
						$importo_base += $importi["importo_personale"];
					}
					if ($rialzo) {
						$importo_aggiudicazione = ($importo_base * ((100 + $percentuale)/100)) + $costi;
					} else {
						$importo_aggiudicazione = ($importo_base * ((100 - $percentuale)/100)) + $costi;
					}
					$importo_aggiudicazione = truncate($importo_aggiudicazione,$decimal);
					$percentuale = truncate($percentuale,$decimal);
					$return = ["percentuale"=>$percentuale,"importo"=>$importo_aggiudicazione];
		  }
		}
		return $return;
	}
	function checkCommissario($codice_gara) {
		global $pdo;
		$return = false;
		if (!empty($_SESSION["codice_commissario"]) && !empty($_SESSION["token_commissario"]) && !empty($codice_gara)) {
			$bind = array();
			$bind[":codice_gara"] = $codice_gara;
			$bind[":token"] = $_SESSION["token_commissario"];
			$bind[":codice"] = $_SESSION["codice_commissario"];

			$sql_login = "SELECT b_commissioni.*
										FROM `b_commissioni`
										JOIN b_gare ON b_commissioni.codice_gara = b_gare.codice
										WHERE `b_commissioni`.`token` = :token
										AND b_gare.stato = 3
										AND `b_commissioni`.`codice` = :codice
										AND `b_commissioni`.`codice_gara` = :codice_gara
										AND `b_commissioni`.`valutatore` = 'S'";
			$ris_login = $pdo->bindAndExec($sql_login,$bind);
			if ($ris_login->rowCount() === 1) $return = true;
		}
		return $return;
	}

	function traduci($key,$lang="") {
		global $config;
		if (empty($lang)) $lang = (!empty($_SESSION["language"])) ? $_SESSION["language"] : "IT";
		$return = $key;
		$key = html_entity_decode($key);
		$key = convertAccent($key);
		$key = strtolower($key);
		if (file_exists($config["path_vocabolario"]."/vocabolario.json")) {
			$dictionary = json_decode(file_get_contents($config["path_vocabolario"]."/vocabolario.json"),TRUE);
			if (!empty($dictionary[$key][$lang])) $return = ucfirst($dictionary[$key][$lang]);
		}
		return $return;
	}
	function convertAccent($string) {
	  $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
	                              'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
	                              'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
	                              'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
	                              'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
	  return strtr( $string, $unwanted_array );
	}

	function pecConfigurata() {
		global $root;
		global $pdo;
		if (isset($_SESSION["ente"]) && $_SESSION["ente"]["ambienteTest"] == "N") {
			if (empty($_SESSION["check_pec"])) {
				if (!file_exists("{$root}/inc/integrazioni/{$_SESSION["ente"]["codice"]}/communicator.bridge.class.php")) {
					return false;
				}
			}
			$sql = "SELECT codice FROM b_coda WHERE codice_ente = :codice_ente AND `timestamp_creazione` < '" . date('Y-m-d h:i:s', strtotime('-1 day')) . "'";
			if ($pdo->go($sql,[":codice_ente"=>$_SESSION["ente"]["codice"]])->rowCount() > 0) {
				return false;
			}
		}
		return true;
	}

	function array_remove_empty($haystack) {
		foreach ($haystack as $key => $value) {
				if (is_array($value)) {
						$haystack[$key] = array_remove_empty($haystack[$key]);
				}

				if (empty($haystack[$key])) {
						unset($haystack[$key]);
				}
		}
		return $haystack;
	}

	function getArt80Provider() {
		global $config;
		if (isset($_SESSION["ente"])) {
			if (isset($config["url-art-80"][$_SESSION["ente"]["providerArt80"]])) {
				return $config["url-art-80"][$_SESSION["ente"]["providerArt80"]];
			}
		}
		return false;
	}

	function getArt80States() {
		$provider = getArt80Provider();
		$states = !empty($_SESSION["verifica-art-80"]["states"]) ? $_SESSION["verifica-art-80"]["states"] : array();
		if(empty($states)) {
			$request = curl_init();
			curl_setopt_array($request, array(
				CURLOPT_URL => "{$provider["url"]}/request/states",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"claimant-id: {$_SESSION["ente"]["codice"]}",
					"token: {$provider["application_token"]}",
					"Content-Type: application/json"
				),
			));
			$request = addCurlAuth($request);
			$response = curl_exec($request);
			curl_close($request);			
			if(! empty($response)) {
				$tmp = json_decode($response, TRUE);
				if (empty($tmp["error"])) {
					$_SESSION["verifica-art-80"]["states"] = $states = $tmp;
				}
			}
		}
		return $states;
	}

	function getArt80Request($id) {
		$provider = getArt80Provider();
		$request = curl_init();
		curl_setopt_array($request, array(
			CURLOPT_URL => "{$provider["url"]}/request/{$id}/show",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"claimant-id: {$_SESSION["ente"]["codice"]}",
				"token: {$provider["application_token"]}",
				"Content-Type: application/json"
			),
		));
		$request = addCurlAuth($request);
		$response = curl_exec($request);
		curl_close($request);
		
		if(! empty($response)) {
				$tmp = json_decode($response, TRUE);
				if (empty($tmp["error"])) $request = $tmp;
		}
		return $request;
	}

	function getArt80Requests($ids) {

		$provider = getArt80Provider();
		$requests = [];
		try {
			$request = curl_init();
			curl_setopt_array($request, array(
				CURLOPT_URL => "{$provider["url"]}/request/massive",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_POSTFIELDS => json_encode(["id" => $ids]),
				CURLOPT_HTTPHEADER => array(
					"claimant-id: {$_SESSION["ente"]["codice"]}",
					"token: {$provider["application_token"]}",
					"Content-Type: application/json"
				),
			));
			$request = addCurlAuth($request);
			$response = curl_exec($request);
			curl_close($request);
			if(! empty($response)) {
				$tmp = json_decode($response, TRUE);
				if (! isset($tmp["error"])) {
					foreach($tmp AS $req) {
						$requests[$req["id"]] = $req;
					}
				}
				return $requests;
			}
		} catch (\Exception $e) {
			unset($e);
		}
		return false;
	}

	function checkStatoArt80($codice) {
		$return = false;
		$provider = getArt80Provider();
		if (!empty($provider)) {
			$array = true;
			if (!is_array($codice)) {
				$codice = [$codice];
				$array = false;
			}
			global $pdo;
			$checkRequests = $pdo->prepare("SELECT id_richiesta FROM b_verifiche_art80 WHERE codice_gestore = :codice_gestore AND codice_fiscale = :codice_fiscale AND id_richiesta > 0 ORDER BY codice LIMIT 0,1");
			$ids = [];
			$join = [];
			$checkRequests->bindValue(":codice_gestore",$_SESSION["ente"]["codice"]);
			foreach($codice AS $cod) {
				$checkRequests->bindValue(":codice_fiscale",$cod);
				$checkRequests->execute();
				if ($checkRequests->rowCount() === 1) {
					$req = $checkRequests->fetch(PDO::FETCH_ASSOC)["id_richiesta"];
					$ids[] = $req;
					$join[$req] = $cod;
				}
			}

			if (!empty($ids)) {
				$requests = getArt80Requests($ids);
				if (!empty($requests)) {
					$states = getArt80States();
					if (!$array) {
						reset($requests);
						$key = key($requests);
						$return["code"] = $requests[$key]["status"]["code"];
						$return["color"] = $states[$return["code"]]["color"];
						$return["status"]  = $states[$return["code"]]["title"];
					} else {
						foreach($join AS $req => $cod) {
							if (!empty($requests[$req])) {
								$return[$cod] = [];
								$return[$cod]["code"] = $requests[$req]["status"]["code"];
								$return[$cod]["color"] = $states[$return[$cod]["code"]]["color"];
								$return[$cod]["status"]  = $states[$return[$cod]["code"]]["title"];
							}
						}
					}
				}
			}
		} 
		return $return;
	}

	function base64url_encode($data)
	{
		// First of all you should encode $data to Base64 string
		$b64 = base64_encode($data);

		// Make sure you get a valid result, otherwise, return FALSE, as the base64_encode() function do
		if ($b64 === false) {
			return false;
		}

		// Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
		$url = strtr($b64, '+/', '-_');

		// Remove padding character from the end of line and return the Base64URL result
		return rtrim($url, '=');
	}
	function base64url_decode($data, $strict = false)
	{
		// Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
		$b64 = strtr($data, '-_', '+/');

		// Decode Base64 string and return the original data
		return base64_decode($b64, $strict);
	}
	return false;

	function getCriteriFeedBack() {
		if (!empty($_SESSION["ente"]["codice"])) {
			global $pdo;
			if (!empty($_SESSION["ente"]["codice"])) {
				$strsql = "SELECT * FROM b_set_feedback WHERE codice_ente = :codice AND eliminato = 'N'";
				$risultato = $pdo->bindAndExec($strsql,array(":codice"=>$_SESSION["ente"]["codice"]));
				if ($risultato->rowCount() > 0) {
					return $risultato->fetchAll(PDO::FETCH_ASSOC);
				} else {
					$risultato = $pdo->bindAndExec($strsql,array(":codice"=>0));
					if ($risultato->rowCount() > 0) {
						return $risultato->fetchAll(PDO::FETCH_ASSOC);
					}
				}
			}
		}
		return false;
	}

	function fromCSVtoArray($path) {
		$return = false;
		if (file_exists($path) && (strpos($path,"../")===false)) {
			ini_set('auto_detect_line_endings',TRUE);
			$handle = fopen($path, "r");
			$array = $fields = array();
			$i = 0;
			if ($handle) {
				while (($row = fgetcsv($handle, 0, ",")) !== false) {
					if (empty($fields)) {
							$fields = $row;
							continue;
					}
					foreach ($row as $k=>$value) {
							$array[$i][$fields[$k]] = $value;
					}
					$i++;
				}
				if (feof($handle)) {
					$return = $array;
				}
			}
		}
		return $return;
	}

	function getRicevuteNonConservate($sezione,$id) {
		global $pdo;
		return $pdo->go("SELECT r_comunicazioni_utenti.*
						FROM r_comunicazioni_utenti
						JOIN b_comunicazioni ON r_comunicazioni_utenti.codice_comunicazione = b_comunicazioni.codice
						WHERE b_comunicazioni.sezione = :sezione AND b_comunicazioni.codice_gara = :id
					 	AND r_comunicazioni_utenti.identificativo_messaggio IS NOT NULL
						AND r_comunicazioni_utenti.sync = 'S' AND r_comunicazioni_utenti.codice NOT IN (SELECT codice_file FROM r_conservazione_file WHERE tabella = 'comunicazioni')",
						[":sezione"=>$sezione,":id"=>$id])->fetchAll(PDO::FETCH_ASSOC);
	}


	function insertRicevuteinPacchetto($sezione,$id,$codice_pacchetto_conservazione,$esclusioni = []) {
		global $pdo;
		global $config;
		$msgs = getRicevuteNonConservate($sezione,$id);
		$sth_save = $pdo->prepare("INSERT INTO `r_conservazione_file`(`codice_ente`,`codice_pacchetto`,`codice_file`,`tabella`,`file_path`,`nome_file`,`hash_md5`,`hash_sha1`,`hash_sha256`,`utente_modifica`) VALUES (:codice_ente,:codice_pacchetto,:codice_file,:tabella,:file_path,:nome_file,:hash_md5,:hash_sha1,:hash_sha256,:utente_modifica)");
		if (!empty($msgs)) {
			foreach($msgs AS $msg) {
				if (in_array($msg["codice"],$esclusioni) === false) {
					$percorso_fisico = "{$config["arch_folder"]}/ricevutepec/{$_SESSION["ente"]["codice"]}/{$msg["codice"]}.zip";
					if (file_exists($percorso_fisico)) {
						$file_content = file_get_contents($percorso_fisico);
						$sth_save->bindValue(":codice_ente", !empty($_SESSION["record_utente"]["codice_ente"]) ? $_SESSION["record_utente"]["codice_ente"] : $_SESSION["ente"]["codice"]);
						$sth_save->bindValue(":codice_pacchetto", $codice_pacchetto_conservazione);
						$sth_save->bindValue(":codice_file", $msg["codice"]);
						$sth_save->bindValue(":tabella", "comunicazioni");
						$sth_save->bindValue(":file_path", $percorso_fisico);
						$sth_save->bindValue(":nome_file", "Ricevute-comunicazione-{$msg["codice"]}.zip");
						$sth_save->bindValue(":hash_md5", hash("md5",$file_content));
						$sth_save->bindValue(":hash_sha1", hash("sha1",$file_content));
						$sth_save->bindValue(":hash_sha256", hash("sha256",$file_content));
						$sth_save->bindValue(":utente_modifica", $_SESSION["codice_utente"]);
						$sth_save->execute();
						$pdo->lastInsertId();
					}
				}
			}
		}
	}
	function extractRealFiles($file_path, $file, $realFiles, $tmp_paths = []) {
		global $config;

		$fileName = basename($file_path);
		$rand = md5(rand());
		if (!file_exists(sys_get_temp_dir() .'/'. $rand)) mkdir(sys_get_temp_dir() .'/'. $rand);
		$tmp_paths[] = sys_get_temp_dir() .'/'. $rand;
		$extractP7M = sys_get_temp_dir() .'/'. $rand . '/' . $fileName . "-extracted";

		$comando = $config["bash_folder"].'/estrai.bash \'' . $file_path . '\' \'' . $extractP7M . '\'';
		$esito = shell_exec("sh " . $comando . " 2>&1");
		if (trim($esito)=="Verification successful") $file_path = $extractP7M;

		$file_info = new finfo(FILEINFO_MIME_TYPE);
		$mime_type = $file_info->buffer(file_get_contents($file_path));

		if ((strpos($mime_type,"zip") !== false)||(strpos($mime_type,"rar") !== false)||(strpos($mime_type,"7z") !== false)) {

			$zip_folder = sys_get_temp_dir() .'/'. $rand . "-" . $fileName . "-dir" . '/zip/';
			if(is_dir($zip_folder)) rmdir($zip_folder);
			$unzipped = false;

			if (strpos($mime_type,"zip")) {

				$zipOpen = new ZipArchive;
				$res_zip = $zipOpen->open($file_path);
				if ($res_zip === true) {

					$unzipped = true;
					$zipOpen->extractTo($zip_folder);
					$zipOpen->close();

				}

			} else if (strpos($mime_type,"rar")) {

				$res_zip = RarArchive::open($file_path);
				$entries = $res_zip->getEntries();

				if ($entries !== false) {
					$unzipped = true;
					foreach ($entries as $e) {
						$e->extract($zip_folder);
					}
					$res_zip->close();
				}

			} else if (strpos($mime_type,"7z")) {

				$comando = $config["bash_folder"].'/estrai_7z.bash \''.$file_path.'\' \''.$zip_folder.'\'';
				$esito_extract = shell_exec("sh " . $comando . " 2>&1");
				if (strpos($esito_extract,"Everything is Ok") !== false) $unzipped = true;

			}

			if ($unzipped) {

				$it = new RecursiveDirectoryIterator($zip_folder, RecursiveDirectoryIterator::SKIP_DOTS);
				$filesZip = new RecursiveIteratorIterator($it,RecursiveIteratorIterator::CHILD_FIRST);
				$finfo = finfo_open(FILEINFO_MIME_TYPE);

				foreach($filesZip as $fileZip) {

					$tmp = [];
					$tmp["tabella"] = $file["tabella"];
					$tmp["file_path"] = $fileZip->getRealPath();
					$tmp["nome_file"] = basename($tmp["file_path"]);
					$tmp["mime_type"] = finfo_file($finfo, $tmp["file_path"]);
					$tmp["hash_sha256"] = hash("sha256", file_get_contents($tmp["file_path"]));

					if ($fileZip->isDir()) {

						$realExtractedFiles = extractRealFiles($fileZip->getRealPath(), $tmp, $realFiles, $tmp_paths);
						$realFiles = array_merge($realFiles, $realExtractedFiles[0]);
						$tmp_paths = array_unique(array_filter(array_merge($tmp_paths, $realExtractedFiles[1])));

					} else {

						$hashes = array_map(function ($each) { return $each['hash_sha256']; }, $realFiles);
						if(! in_array($tmp["hash_sha256"], $hashes)) $realFiles[] = $tmp;

					}
				}
			}
		} else {

			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$file["mime_type"] = finfo_file($finfo, $file["file_path"]);
			$file["hash_sha256"] = hash("sha256", file_get_contents($file["file_path"]));

			$hashes = array_map(function ($each) { return $each['hash_sha256']; }, $realFiles);
			if(! in_array($file["hash_sha256"], $hashes)) $realFiles[] = $file;

		}

		return [$realFiles, $tmp_paths];
	}

	function is_base64($str)
	{
		return (bool)preg_match('`^[a-zA-Z0-9+/]+={0,2}$`', $str);
	}

	function transferToAlboCommissari($action,$oe,$codice_albo) {

		$match = [];

		if (!empty($match[$codice_albo])) {
			$elenco = $match[$codice_albo];
			global $pdo;
			$oe = $pdo->go("SELECT codice_utente, codice_fiscale_impresa, ragione_sociale, indirizzo_legale, citta_legale FROM b_operatori_economici WHERE codice = :codice",[":codice"=>$oe])->fetch(PDO::FETCH_ASSOC);
			if (!empty($oe)) {
				$utente = $pdo->go("SELECT cognome, nome, email, telefono FROM b_utenti WHERE codice = :codice",[":codice"=>$oe["codice_utente"]])->fetch(PDO::FETCH_ASSOC);
				if (!empty($utente)) {
					$check = $pdo->go("SELECT codice FROM b_commissari_albo WHERE codice_fiscale = :cf AND codice_albo = :albo",[":cf"=>$oe["codice_fiscale_impresa"],":albo"=>$elenco])->fetch(PDO::FETCH_COLUMN);
					if ($action == "INSERT") {
						if (empty($check)) {
							$insert = [];
							$insert["codice_albo"] = $elenco;
							$insert["codice_fiscale"] = $oe["codice_fiscale_impresa"];
							$insert["cognome"] = $utente["cognome"];
							$insert["nome"] = $utente["nome"];
							$insert["email"] = $utente["email"];
							$insert["telefono"] = $utente["telefono"];
							$insert["comune"] = $oe["citta_legale"];
							$insert["indirizzo"] = $oe["indirizzo_legale"];
							$insert["attivo"] = "S";
							$insert["interno"] = "N";
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_commissari_albo";
							$salva->operazione = "INSERT";
							$salva->oggetto = $insert;
							return $salva->save();
						}
					} else if ($action == "DELETE") {
						if (!empty($check)) {
							$pdo->go("DELETE FROM b_commissari_albo WHERE codice = :codice",[":codice"=>$check]);
							return true;
						}
					}
				}
			}
		}
		return false;
	}

	function addCurlAuth($curl) {
		global $config;
		if (!empty($config["curl_proxy"])) {
			if (!empty($config["curl_user"]) && !empty($config["curl_pass"])) {
				curl_setopt($curl, CURLOPT_PROXY, $config["curl_proxy"]);
				curl_setopt($curl, CURLOPT_PROXYUSERPWD,"{$config["curl_user"]}:{$config["curl_pass"]}");
				curl_setopt($curl, CURLOPT_PROXYAUTH, CURLAUTH_NTLM);
				curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, 1);
			}
		}
		return $curl;
	}

	function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds') {
		$sets = [];
		if(strpos($available_sets, 'l') !== false) $sets[] = 'abcdefghjkmnpqrstuvwxyz';
		if(strpos($available_sets, 'u') !== false) $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
		if(strpos($available_sets, 'd') !== false) $sets[] = '23456789';
		if(strpos($available_sets, 's') !== false) $sets[] = '!@#$%&*?';
		$all = $password = $dash_str = '';
		foreach($sets as $set) {
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}
		$all = str_split($all);
		for($i = 0; $i < $length - count($sets); $i++) {
			$password .= $all[array_rand($all)];
		}
		$password = str_shuffle($password);
		if(!$add_dashes) return $password;
		$dash_len = floor(sqrt($length));
		while(strlen($password) > $dash_len) {
			$dash_str .= substr($password, 0, $dash_len) . '-';
			$password = substr($password, $dash_len);
		}
		$dash_str .= $password;
		return $dash_str;
	}

	function checkBustaEmendabile($busta) {
		global $pdo;
		if (is_operatore() && $busta["utente_modifica"] == $_SESSION["codice_utente"]) {
			$checkBusta = $pdo->go("SELECT codice FROM b_criteri_buste WHERE (tecnica = 'S' OR economica = 'S') AND codice = :codice",[":codice"=>$busta["codice_busta"]]);
			if ($checkBusta->rowCount() > 0) {
				$checkGara = $pdo->go("SELECT codice FROM b_gare WHERE emendamenti = 'S' AND norma = '2023-36' AND codice = :gara AND data_scadenza < now() AND data_apertura > now()",[":gara"=>$busta["codice_gara"]]);
				if ($checkGara->rowCount() > 0) {
					return true;
				}
			}
		}
		return false;
	}

?>
