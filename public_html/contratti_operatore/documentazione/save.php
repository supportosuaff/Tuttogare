<?
	ini_set('memory_limit', '-1');
	include_once "../../../config.php";
	include_once $root . "/layout/top.php";
	include_once $root . "/inc/p7m.class.php" ;
	include_once $root . "/layout/top.php";
	if (!is_operatore()) {
	    ?><h3 class="ui-state-error">Impossibile accedere al contratto. Privilegi insufficienti.</h3><?
	    die();
	} else {
	  $ris_operatore = $pdo->bindAndExec("SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente", array(':codice_utente' => $_SESSION["codice_utente"]));
	  $operatore = $ris_operatore->fetch(PDO::FETCH_ASSOC);
		if(!empty($_POST["codice_contratto"])) {
			$codice_contratto = $_POST["codice_contratto"];
	    $sql = "SELECT b_contratti.*, b_conf_modalita_stipula.invio_remoto, b_conf_modalita_stipula.etichetta as modalita_di_stipula
	            FROM b_contratti
	            JOIN b_conf_modalita_stipula ON b_conf_modalita_stipula.codice = b_contratti.modalita_stipula
	            JOIN r_contratti_contraenti ON r_contratti_contraenti.codice_contratto = b_contratti.codice
	            JOIN b_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente
	            WHERE b_contraenti.codice_utente = :codice_utente
	            AND b_contratti.codice = :codice_contratto
	            AND r_contratti_contraenti.codice_capogruppo = 0";
	    $ris = $pdo->bindAndExec($sql, array(':codice_utente' => $_SESSION["record_utente"]["codice"], ':codice_contratto' => $codice_contratto));
	    if($ris->rowCount() > 0) {
				$rec_contratto = $ris->fetch(PDO::FETCH_ASSOC);
				$html = "";
				$moduli = $_POST["modulo"];
				if(!empty($moduli)) {
					foreach ($moduli as $modulo) {
						if(!empty($modulo["filechunk"]) && !empty($modulo["md5_file"])) {
							$file = $config["chunk_folder"].'/'.$_SESSION["codice_utente"]."/".$modulo["filechunk"];
							$p7m = new P7Manager($file);
							$md5_file = $p7m->getHash('md5');
							if($modulo["md5_file"] == $md5_file) {
								$tmp_html  = '<ul class="success">';
								$tmp_html .= '<li>File integro - HASH MD5: '.$md5_file.'</li>';
								$esito = $p7m->checkSignatures();
								if ($esito == "Verification successful") {
									$tmp_html .= "<li>Firma formalmente valida ";
									$tmp_html .= '<ul class="firme">';
									$certificati = $p7m->extractSignatures();
									foreach ($certificati AS $certificato) {
										$tmp_html .= "<li>";
										if (isset($data["subject"]["commonName"])) $tmp_html .= "<h1>" . $data["subject"]["commonName"] . "</h1>";
										if (isset($data["subject"]["title"])) $tmp_html .=  $data["subject"]["title"] . "<br>";
										if (isset($data["issuer"]["organizationName"])) $tmp_html .=  "<br>Emesso da:<strong>" . $data["issuer"]["organizationName"] . "</strong>";
										$data = openssl_x509_parse($certificato,false);
										$validFrom = date('d-m-Y H:i:s', $data['validFrom_time_t']);
										$validTo = date('d-m-Y H:i:s', $data['validTo_time_t']);
										$tmp_html .= "<br>Valido da:<strong> " . $validFrom . "</strong><br>A <strong>" . $validTo . "</strong>";
										$tmp_html .= "</li>";
									}
									$tmp_html .= '</ul>';
									$tmp_html .= '</li>';

									$percorso = "{$config["arch_folder"]}/allegati_contratto/{$rec_contratto["codice"]}/";
									if (!is_dir($percorso)) mkdir($percorso,0770,true);

									$copy = copiafile_chunck($modulo["filechunk"], $percorso, str_replace($modulo["filechunk"], "", $file));

									$salva = new salva();
									$salva->debug = FALSE;
									$salva->codop = $_SESSION["codice_utente"];
									$salva->nome_tabella = "b_allegati_contratto";
									$salva->oggetto = array(
										"codice_contratto" => $rec_contratto["codice"],
										"codice_modulo" => !empty($modulo["codice_modulo"]) ? $modulo["codice_modulo"] : 0,
										"codice_operatore" => $operatore["codice"],
										"nome_file" => $copy["nome_file"],
										"riferimento" => $copy["nome_fisico"]
									);
                  $salva->operazione = "INSERT";
                  if(!empty($modulo["codice"]) && is_numeric($modulo["codice"])) {
                    $salva->operazione = "UPDATE";
                    $salva->oggetto["codice"] = $modulo["codice"];
                  }

									$codice_allegato = $salva->save();
									if(is_numeric($codice_allegato)) {
										$percorso .= $salva->oggetto["nome_file"];
										$file = $percorso;
										$tmp_html .= "<li>Salvataggio effettuato con successo</li>";
									} else {
										$tmp_html = '<h3 class="ui-state-error">'.$modulo["filechunk"].' - Non è stato possibile salvare il file. Si prega di riporvare.</h3>';
									}
								} else {
									$tmp_html = '<h3 class="ui-state-error">'.$modulo["filechunk"].' - Non è stato possibile verificare le firme sul file. Si prega di riporvare.</h3>';
								}
								$tmp_html .= '</ul>';
							} else {
								$tmp_html = '<h3 class="ui-state-error">' . $modulo["filechunk"] . ' - Errore verifica hash. Si prega di riporvare.</h3>';
							}
							$html .= $tmp_html;
						}
					}
					echo $html;
				}
			} else {
				?><h3 class="ui-state-error">Impossibile accedere al contratto. Privilegi insufficienti.</h3><?
				die();
			}
		} else {
			?><h3 class="ui-state-error">Impossibile accedere al contratto.</h3><?
			die();
		}
	}

	$_GET["codice"] = $rec_contratto["codice"];
	include_once $root . "/contratti_operatore/ritorna_pannello_contratto.php";
	include_once $root . "/layout/bottom.php";

?>
