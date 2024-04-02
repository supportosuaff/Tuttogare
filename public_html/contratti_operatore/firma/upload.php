<?
	ini_set('max_execution_time', 600);
	ini_set('memory_limit', '-1');

	include_once("../../../config.php");
	include_once($root."/inc/p7m.class.php");
	include_once($root."/layout/top.php");
	include($root."/inc/pdftotext.phpclass");

	function fatal_handler() {
	  $error = error_get_last();
	  if( $error !== NULL && $error['type'] === E_ERROR || $error['type'] === E_USER_ERROR)
	  {
			global $codice_gara;
			global $codice_lotto;
			?>
	  	<h3 class="ui-state-error">Si è verificato un errore nella procedura, si prega di riprovare se il problema persiste contattare l'Help Desk tecnico al numero <?= $_SESSION["numero_assistenza"] ?></h3>
			<? if (!empty($codice_gara)) { ?>
				<a class="submit_big" style="background-color:#444"  href="/gare/telematica2.0/modulo.php?cod=<?= $codice_gara ?><?= ($codice_lotto > 0) ? "&codice_lotto=".$codice_lotto : "" ?>">Ritorna al pannello</a>
			<? }
	  }
	}
	register_shutdown_function( "fatal_handler" );

	if(is_operatore()) {
		$ris_operatore = $pdo->bindAndExec("SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente", array(':codice_utente' => $_SESSION["codice_utente"]));
    $operatore = $ris_operatore->fetch(PDO::FETCH_ASSOC);
		if(!empty($_POST["codice_contratto"]) && isset($_POST["filechunk"])) {
			$codice = $codice_contratto = $_POST["codice_contratto"];
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
				?>
				<h1>PANNELLO DI GESTIONE - CONTRATTO #<?= $rec_contratto["codice"] ?></h1>
	      <h2>Oggetto: <small><?= $rec_contratto["oggetto"] ?></small></h2>
				<h2 style="text-align:right; border-bottom:10px solid #999999; margin-bottom:20px;">
					Tipologia: <small><strong><?= $rec_contratto["modalita_di_stipula"] ?></strong></small>
					&nbsp;|&nbsp;Importo: <small><strong>&euro; <?= $rec_contratto["importo_totale"] ?></strong></small>
					<? if(!empty($rec_contratto["cig"])) echo "&nbsp;|&nbsp;CIG: <small><strong>{$rec_contratto["cig"]}</strong></small>" ?>
					<? if(!empty($rec_contratto["cup"])) echo "&nbsp;|&nbsp;CUP: <small><strong>{$rec_contratto["cup"]}</strong></small>" ?>
				</h2>
				<?
				if($rec_contratto["invio_remoto"] == "S" && (strpos($_POST["filechunk"], "../")===false)) {
					$file = "{$config["chunk_folder"]}/{$_SESSION["codice_utente"]}/{$_POST["filechunk"]}";
					$p7m = new P7Manager($file);
					$md5_file = $p7m->getHash('md5');
					if($_POST["md5_file"] == $md5_file) {
						// TODO: VERIFICARE CHE IL FILE COINCIDE CON IL CONTRATTO GENERATO DALLA PIATTAFORMA
            $bind = array(':codice' => $codice_contratto, ':tipo' => 'contratto', ':sezione' => 'contratti');
            $ris_documento = $pdo->bindAndExec("SELECT b_documentale.codice, b_allegati.nome_file, b_allegati.riferimento FROM b_documentale JOIN b_allegati ON b_allegati.codice = b_documentale.codice_allegato WHERE b_documentale.tipo = :tipo AND b_documentale.sezione = :sezione AND b_documentale.codice_gara = :codice AND codice_allegato > 0 AND attivo = 'S'", $bind);
            if($ris_documento->rowCount() > 0) {
              $rec_documento = $ris_documento->fetch(PDO::FETCH_ASSOC);
              $original_file_path = "{$config["arch_folder"]}/allegati_contratto/{$codice}/{$rec_documento["riferimento"]}";
            } else {
              $ris_documento = $pdo->bindAndExec("SELECT riferimento FROM `b_allegati` WHERE `sezione` = 'contratti' AND `codice_gara` = :codice_contratto AND `cartella` = 'contratti_da_firmare'", array(':codice_contratto' => $codice_contratto));
              if($ris_documento->rowCount() > 0) {
                $rec_documento = $ris_documento->fetch(PDO::FETCH_ASSOC);
                $original_file_path = "{$config["arch_folder"]}/allegati_contratto/{$codice_contratto}/{$rec_documento["riferimento"]}";
              }
            }
            if(file_exists($original_file_path)) {
							$hash_error = FALSE;
							$original = new P7Manager($original_file_path);
							$original_file = file_get_contents($original_file_path);
							$file_info = new finfo(FILEINFO_MIME_TYPE);
							$mime_type = $file_info->buffer($original_file);
							$pdfOriginal = false;
							if (strpos($mime_type,"pdf") !== false) {
								$pdfOriginal = true;
							}
							$hash = array();
							$signedOriginal = false;
							$signed = $original->checkSignatures();
							if (!empty($signed)) {
								$signedOriginal = true;
								$uploadFile = file_get_contents($file);
								$original_signatures = json_encode($original->extractSignatures());
								$upload_signatures = json_encode($p7m->extractSignatures());
								$checkMD5 = hash("md5", $original_signatures);
								$checkMD5Upload = hash("md5", $upload_signatures);
								if ($checkMD5 == $checkMD5Upload) {
									unset($original_file);
									$sameUpload = true;
								} else {
									if (strpos($mime_type,"pdf") === false) {
										$original_file = $original->extractContent();
									} 
								}
							} 
							if (!empty($original_file)) {
								$hash["md5"] = hash("md5", $original_file);
								$hash["sha1"] = hash("sha1", $original_file);
								$hash["sha256"] = hash("sha256", $original_file);
								foreach ($hash as $method => $hash_string) {
									if(!empty($hash_string)) {
										if($p7m->find($hash_string, $method) === FALSE) $hash_error = TRUE;
									}
								}
							} else {
								$hash_error = true;
							}
							if ($hash_error && $pdfOriginal && !isset($sameUpload)) {
								try {
									$dataContent = @new PdfToText($original_file_path);
                  $dataContent =  $dataContent->Text;
                  $dataContent = preg_replace("/[^a-zA-Z0-9]/", '', $dataContent);
                } catch (Exception $e) {
									$dataContent = "";
                }
                $hash_string = hash("sha256",$dataContent);
								if ($p7m->find($hash_string,"sha256",true) !== FALSE) $hash_error = false;
							}
              if(!$hash_error) {
                $html = '<ul class="success">';
                $html .= '<li>File integro - HASH MD5: '.$md5_file.'</li>';
                $esito = $p7m->checkSignatures();
                if ($esito == "Verification successful") {
                  $html .= "<li>Firma formalmente valida ";
                  $html .= '<ul class="firme">';
                  $certificati = $p7m->extractSignatures();
                  foreach ($certificati AS $certificato) {
                    $html .= "<li>";
                    if (isset($data["subject"]["commonName"])) $html .= "<h1>" . $data["subject"]["commonName"] . "</h1>";
                    if (isset($data["subject"]["title"])) $html .=  $data["subject"]["title"] . "<br>";
                    if (isset($data["issuer"]["organizationName"])) $html .=  "<br>Emesso da:<strong>" . $data["issuer"]["organizationName"] . "</strong>";
                    $data = openssl_x509_parse($certificato,false);
                    $validFrom = date('d-m-Y H:i:s', $data['validFrom_time_t']);
                    $validTo = date('d-m-Y H:i:s', $data['validTo_time_t']);
                    $html .= "<br>Valido da:<strong> " . $validFrom . "</strong><br>A <strong>" . $validTo . "</strong>";
                    $html .= "</li>";
                  }
                  $html .= '</ul>';
									$html .= "</li>";
									
									$percorso = "{$config["arch_folder"]}/allegati_contratto/{$rec_contratto["codice"]}/";
									if (!is_dir($percorso)) mkdir($percorso,0770,true);
									$copy = copiafile_chunck($_POST["filechunk"], $percorso, str_replace($_POST["filechunk"], "", $file));

									if ($signedOriginal) {
										$operazione = "INSERT";
										$pdo->bindAndExec("DELETE FROM `b_allegati` WHERE `sezione` = 'contratti' AND `codice_gara` = :codice_contratto AND `cartella` = 'contratti_firmati'", array(':codice_contratto' => $rec_contratto["codice"]));
										$oggetto = array(
											'sezione' => "contratti",
											'codice_gara' => $rec_contratto["codice"],
											'cartella' => 'contratti_firmati',
											'online' => 'N',
											'hidden' => 'N',
											'codice_ente' => $_SESSION["ente"]["codice"],
											'nome_file' => $copy["nome_file"],
											'riferimento' => $copy["nome_fisico"],
											'titolo' => 'Contratto Firmato Digitalmente - ID'.$rec_contratto["codice"],
											'descrizione' => 'File contenente il contratto id ' . $rec_contratto["codice"] . ' firmato digitalmente',
										);
										$table = "b_allegati";
									} else {
										$operazione = (!empty($modulo["codice"]) && is_numeric($modulo["codice"])) ? 'UPDATE' : 'INSERT';
										$oggetto = array(
											"codice_contratto" => $rec_contratto["codice"],
											"codice_modulo" => 0,
											"codice_operatore" => $operatore["codice"],
											"titolo" => "CONTRATTO FIRMATO DALL'OPERATORE ECONOMICO",
											"tipologia" => "contratto",
											"nome_file" => $copy["nome_file"],
											"riferimento" => $copy["nome_fisico"]
										);
										$table = "b_allegati_contratto";	
									}
								
                  $salva = new salva();
                  $salva->debug = FALSE;
                  $salva->codop = $_SESSION["codice_utente"];
                  $salva->nome_tabella = $table;
                  $salva->operazione = $operazione;
                  $salva->oggetto = $oggetto;
                  $codice_allegato = $salva->save();
                  if(is_numeric($codice_allegato)) {
										if ($signedOriginal) {
											// $timestamp = $p7m->putTimestamp($percorso.$copy["nome_fisico"]);
										}
                    $html .= "<li>Salvataggio effettuato con successo</li>";
                    $file = $percorso . $salva->oggetto["riferimento"];
										$tmp_path_attach = $percorso . $salva->oggetto["nome_file"];
                    $ris = $pdo->bindAndExec("SELECT b_enti.pec FROM b_enti WHERE codice = :codice_ente", array(':codice_ente' => $rec_contratto["codice_ente"]));
                    if($ris->rowCount() > 0) {
											if (copy($file,$tmp_path_attach)) {
	                      $rec_ente = $ris->fetch(PDO::FETCH_ASSOC);
	                      $mailer = new Communicator();
	                      $mailer->oggetto = "CONFERMA DI STIPULA - CONTRATTO #".$rec_contratto["codice"];
	                      $mailer->corpo = "<h2>STIPULA DEL CONTRATTO ID: {$rec_contratto["codice"]}</h2>";
	                      $mailer->corpo .= "Si trasmette in allegato il file firmato digitalemente .p7m del contratto:<br>";
	                      $mailer->corpo .= "<br><strong>" . $rec_contratto["oggetto"] . "</strong><br><br>";
	                      $mailer->corpo .= "Distinti Saluti<br><br>";
	                      $mailer->attachment = $tmp_path_attach;
	                      $mailer->comunicazione = true;
                        $mailer->codice_pec = -1;
	                      $mailer->coda = FALSE;
	                      $mailer->sezione = "contratti";
	                      $mailer->codice_gara = $rec_contratto["codice"];
	                      $mailer->destinatari = $rec_ente["pec"];
                        $mailer->type = 'comunicazione-contratto';
	                      $esito = $mailer->send();
	                      if ($esito) {
	                        $html .= '<li>Comunicazione inviata correttamente all&#39;indirizzo: '.$rec_ente["pec"].'</li>';
	                      } else {
	                        $pdo->bindAndExec("DELETE FROM b_allegati_contratto WHERE codice = :codice_allegato", array(':codice_allegato' => $codice_allegato));
	                        ?><h3 class="ui-state-error">Non è stato possibile inviare la comunicazione all&#39;amministrazione. Si prega di riporvare.</h3><?
	                      }
												unlink($tmp_path_attach);
											} else {
		                    ?><h3 class="ui-state-error">Non è stato possibile salvare il file. Si prega di riporvare.</h3><?
		                  }
                    }
                  } else {
                    ?><h3 class="ui-state-error">Non è stato possibile salvare il file. Si prega di riporvare.</h3><?
                  }
                  $html .= "</ul>";
                  echo $html;
                } else {
                  ?><h3 class="ui-state-error">Non è stato possibile verificare la firma sul file. Si prega di riporvare.</h3><?
                }
              } else {
                ?><h3 class="ui-state-error">Il file non coincide con l'orginale. Si prega di riprovare.</h3><?
              }
            } else {
              ?><h3 class="ui-state-error">Non è stato possibile verificare il file con l'orginale. Si prega di riprovare.</h3><?
            }
					} else {
						?><h3 class="ui-state-error">Errore verifica hash. Si prega di riporvare.</h3><?
					}
				} else {
					?><h3 class="ui-state-error">Non &egrave; permessa la firma remota per questo contratto.</h3><?
				}
			} else {
				?><h3 class="ui-state-error">Impossibile accedere al contratto. Privilegi insufficienti.</h3><?
			}
		} else {
			?><h3 class="ui-state-error">Impossibile accedere al contratto. Privilegi insufficienti.</h3><?
		}
	} else {
		?><h3 class="ui-state-error">Impossibile accedere al contratto. Privilegi insufficienti.</h3><?
	}
	$_GET["codice"] = $rec_contratto["codice"];
	include_once($root . "/contratti_operatore/ritorna_pannello_contratto.php");
	include_once($root."/layout/bottom.php");
	die();
?>
