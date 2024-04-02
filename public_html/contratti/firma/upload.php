<?
	ini_set('max_execution_time', 600);
	ini_set('memory_limit', '-1');
	include_once("../../../config.php");
	include_once($root."/inc/p7m.class.php");
	include_once($root."/layout/top.php");
	include($root."/inc/pdftotext.phpclass");

	function fatal_handler() {
	  $error = error_get_last();
	  if( $error !== NULL && $error['type'] === E_ERROR)
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
	if(!empty($_POST["codice_contratto"]) && isset($_POST["repertorio"]) && !empty($_POST["data_repertorio"]) && isset($_POST["codice_gara"]) && isset($_POST["filechunk"]) && isset($_POST["codice_pec"]) && !empty($_POST["email"])) {
		$codice = $_POST["codice_contratto"];
		$codice_gara = !empty($_POST["codice_gara"]) ? $_POST["codice_gara"] : null;

	  $bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ':codice' => $codice);
	  $sql  = "SELECT b_contratti.* FROM b_contratti ";
	  if(!empty($codice_gara) && $_SESSION["gerarchia"] > 1) {
	    $sql .= "JOIN b_permessi ON b_contratti.codice_gara = b_permessi.codice_gara ";
	  } elseif (empty($codice_gara) && $_SESSION["gerarchia"] > 1) {
			$sql .= "JOIN b_permessi_contratti ON b_contratti.codice = b_permessi_contratti.codice_contratto ";
		}
	  $sql .= "WHERE b_contratti.codice = :codice ";
	  $sql .= "AND b_contratti.codice_gestore = :codice_ente ";
	  if ($_SESSION["gerarchia"] > 0) {
	    $bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
	    $sql .= "AND (b_contratti.codice_ente = :codice_ente_utente OR b_contratti.codice_gestore = :codice_ente_utente) ";
	  }
	  if (!empty($codice_gara)) {
	    $bind[":codice_gara"] = $codice_gara;
	    $sql .= " AND b_contratti.codice_gara = :codice_gara";
	    if($_SESSION["gerarchia"] > 1) {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
	      $sql .= " AND (b_permessi.codice_utente = :codice_utente)";
	    }
	  } else {
			if($_SESSION["gerarchia"] > 1) {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$sql .= " AND (b_permessi_contratti.codice_utente = :codice_utente)";
			}
		}
    $ris = $pdo->bindAndExec($sql,$bind);
		if($ris->rowCount() == 1) {
      $rec_contratto = $ris->fetch(PDO::FETCH_ASSOC);
      if(!empty($_POST["repertorio"]) && !empty($_POST["data_repertorio"])) {
        $sql = "UPDATE b_contratti SET repertorio = :repertorio, data_repertorio = :data_repertorio, anno_repertorio = :anno_repertorio WHERE codice = :codice";
        $repertorio = $_POST["repertorio"];
        $data_repertorio = date2mysql($_POST["data_repertorio"]);
        $anno_repertorio = substr($data_repertorio, 0, 4);
        $ris = $pdo->bindAndExec($sql, array(':codice' => $rec_contratto["codice"], ':repertorio' => $repertorio, ':data_repertorio' => $data_repertorio, ':anno_repertorio' => $anno_repertorio));
      }
      $path_chunk = "{$config["chunk_folder"]}/{$_SESSION["codice_utente"]}/";
      $destination_file = "{$config["arch_folder"]}/allegati_contratto/{$rec_contratto["codice"]}/";
      if (!is_dir($destination_file)) mkdir($destination_file, 0777, true);
			$copy = copiafile_chunck($_POST["filechunk"], $destination_file, $path_chunk);
			$tmp_path_attach = $destination_file . $copy["nome_file"];
      $destination_file .= $copy["nome_fisico"];

      $file = $destination_file;
      ?>
      <h1>UPLOAD DEL CONTRATTO - ID <?= $rec_contratto["codice"] ?></h1>
      <h2><?= $rec_contratto["oggetto"] ?></h2>
        <?
        $html = '<ul class="success">';
        $p7m = new P7Manager($file);
        $md5_file = $p7m->getHash('md5');
        if ($md5_file == $_POST["md5_file"]) {
          $html .= '<li>File integro - HASH MD5: '.$md5_file.'</li>';
          if(empty($_POST["dafirmare"])) {
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
			} else {
				?><h3 class="ui-state-error">Non è stato possibile verificare le firme sul contratto.)</h3><?
			}

			//Elimino eventuali altri contratti presenti nel database
			$pdo->bindAndExec("DELETE FROM `b_allegati` WHERE `sezione` = 'contratti' AND `codice_gara` = :codice_contratto AND `cartella` = 'contratti_firmati'", array(':codice_contratto' => $rec_contratto["codice"]));

			$salva = new salva();
			$salva->debug = FALSE;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_allegati";
			$salva->operazione = "INSERT";
			$salva->oggetto = array(
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
			$codice_allegato = $salva->save();
			if(is_numeric($codice_allegato)) {
			$html .= "<li>Salvataggio effettuato con successo</li>";
			// $timestamp = $p7m->putTimestamp($file);
			if(!$_SESSION["developEnviroment"]) {
				if ($timestamp !== false) {
				$html .= '<li>Marcatura termporale effettuata con successo: '.$timestamp.'</li>';
				} else {
				unlink($file);
				?><h3 class="ui-state-error">Errore durante la marcatura temporale. Si prega di riporvare.</h3><?
				die();
				}
			}
							if (copy($file,$tmp_path_attach)) {
				$mailer = new Communicator();
				$mailer->oggetto = "CONFERMA DI STIPULA - CONTRATTO #".$rec_contratto["codice"];
				$mailer->corpo = "<h2>STIPULA DEL CONTRATTO ID: {$rec_contratto["codice"]}</h2>";
				$mailer->corpo .= "Si trasmette in allegato il file firmato digitalmente .p7m del contratto:<br>";
				$mailer->corpo .= "<br><strong>" . $rec_contratto["oggetto"] . "</strong><br><br>";
				$mailer->corpo .= "Distinti Saluti<br><br>";
				$mailer->codice_pec = $_POST["codice_pec"];
				$mailer->attachment = $tmp_path_attach;
				$mailer->comunicazione = true;
				$mailer->coda = FALSE;
				$mailer->sezione = "contratti";
				$mailer->codice_gara = $rec_contratto["codice"];
				$mailer->destinatari = explode(";", $_POST["email"]);
				if(empty($mailer->destinatari)) $mailer->destinatari = $_POST["email"];
				$esito = $mailer->send();
				if ($esito) {
									unlink($tmp_path_attach);
					$html .= '<li>Comunicazione inviata correttamente all&#39;indirizzo: '.$_POST["email"].'</li>';
				} else {
									unlink($tmp_path_attach);
					unlink($file);
					?><h3 class="ui-state-error">Non è stato possibile inviare la comunicazione all&#39;operatore economico. Si prega di riporvare.</h3><?
					die();
				}
				$html .= '</ul>';
				echo $html;
							} else {
				unlink($file);
				?><h3 class="ui-state-error">Non è stato possibile salvare il file. Si prega di riporvare.</h3><?
				die();
				}
			} else {
			unlink($file);
			?><h3 class="ui-state-error">Non è stato possibile salvare il file. Si prega di riporvare.</h3><?
			die();
			}

          } else {
						$pdo->bindAndExec("DELETE FROM b_allegati WHERE codice_gara = :codice_contratto AND sezione = 'contratti' AND cartella = 'contratti_da_firmare'", array(':codice_contratto' => $rec_contratto["codice"]));

						$salva = new salva();
						$salva->debug = FALSE;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "b_allegati";
						$salva->operazione = "INSERT";
						$salva->oggetto = array(
							'sezione' => "contratti",
							'codice_gara' => $rec_contratto["codice"],
							'cartella' => 'contratti_da_firmare',
							'online' => 'N',
							'hidden' => 'N',
							'codice_ente' => $_SESSION["ente"]["codice"],
							'nome_file' => $copy["nome_file"],
							'riferimento' => $copy["nome_fisico"],
							'titolo' => 'Contratto da firmare digitalmente - ID'.$rec_contratto["codice"],
							'descrizione' => 'File contenente il contratto id ' . $rec_contratto["codice"] . ' non firmato',
						);
						$codice_allegato = $salva->save();
						if(is_numeric($codice_allegato)) {
							if (copy($file,$tmp_path_attach)) {
								$html .= "<li>Salvataggio effettuato con successo</li>";
								$mailer = new Communicator();
								$mailer->oggetto = "INVITO A STIPULARE - CONTRATTO #".$rec_contratto["codice"];
								$mailer->corpo = "<h2>INVITO A STIPULARE</h2>";
								$mailer->corpo .= "Si trasmette in allegato il file .pdf del contratto:<br>";
								$mailer->corpo .= "<br><strong>" . $rec_contratto["oggetto"] . "</strong><br><br>";
								$mailer->corpo .= "Una volta firmato il file deve essere caricato sul sitema dell&#39;amministrazione al link:";
								$mailer->corpo .= '<a href="' . $config["protocollo"] .$_SERVER["SERVER_NAME"].'/contratti_operatore/pannello.php?codice='.$rec_contratto["codice"].'">'.$config["protocollo"].$_SERVER["SERVER_NAME"].'/contratti_operatore/pannello.php?codice='.$rec_contratto["codice"].'</a>';
								$mailer->corpo .= "<br><br>";
								$mailer->corpo .= "Distinti Saluti<br><br>";
								$mailer->codice_pec = $_POST["codice_pec"];
								$mailer->attachment = $tmp_path_attach;
								$mailer->comunicazione = true;
								$mailer->coda = FALSE;
								$mailer->sezione = "contratti";
								$mailer->codice_gara = $rec_contratto["codice"];
								$mailer->destinatari = explode(";", $_POST["email"]);
								if(empty($mailer->destinatari)) $mailer->destinatari = $_POST["email"];
								$esito = $mailer->send();
								if ($esito) {
									unlink($tmp_path_attach);
									$html .= "<li>Contratto inviato correttamente all&#39;operatore economico.</li>";
								} else {
									unlink($file);
									?><h3 class="ui-state-error">Non è stato possibile inviare la comunicazione! Errore indirizzo pec destinatario.</h3><?
									die();
								}
							}
						} else {
							unlink($file);
							?><h3 class="ui-state-error">Non è stato possibile salvare il file. Si prega di riporvare.</h3><?
							die();
						}
						$html .= '</ul>';
						echo $html;
				}
      } else {
        unlink($file);
        ?><h3 class="ui-state-error">Non è stato possibile verificare l&#39;integrit&agrave; del file. Si prega di riporvare.</h3><?
        die();
      }
		} else {
			?><h3 class="ui-state-error">Non hai i permessi per eseguire questa operazione!</h3><?
  		die();
		}
	} else {
		?><h3 class="ui-state-error">Non hai i permessi per eseguire questa operazione!</h3><?
		die();
	}
	$_GET["codice"] = $rec_contratto["codice"];
	include_once($root . "/contratti/ritorna_pannello_contratto.php");
	include_once($root."/layout/bottom.php");
?>
