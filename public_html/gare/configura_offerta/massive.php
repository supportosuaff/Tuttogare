<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		if (!empty($_POST["codice_gara"])) {
			$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/configura_offerta/index.php'";
			$risultato = $pdo->query($strsql);
			if ($risultato->rowCount()>0) {
				$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
				$esito = check_permessi_gara($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
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
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	if ($edit && !$lock) {
		if (isset($_POST["submit"])) {
      //echo "sono dentro";
      $upload_path = "/tmp/";
      $allowed_filetypes = array('.csv');
      $filename = $_FILES["tracciato"]["name"];
      $ext = substr($filename, strpos($filename, '.'), strlen($filename) - 1);
      $msg='';
      if (!in_array($ext, $allowed_filetypes)) {
        $msg = "Errore formato file importazione";
      } else {
        if (move_uploaded_file($_FILES["tracciato"]["tmp_name"], $upload_path . $filename)) {
					
					$formule = json_decode(file_get_contents($root."/gare/configura_offerta/formule.json"),TRUE);
					
					ini_set('auto_detect_line_endings',TRUE);
          $handle = fopen($upload_path . $filename, "r");
          $handle_source = fopen("tracciato.csv", "r");
          $array = $fields = array(); $i = 0;
          if ($handle && $handle_source) {
            while (($row = fgetcsv($handle, 0, ";")) !== false) {
              if (empty($fields)) {
                  $fields = $row;
                  continue;
              }
              foreach ($row as $k=>$value) {
                  $array[$i][$fields[$k]] = $value;
              }
              $i++;
            }
            if (!feof($handle)) {
              $msg = "Errore nella lettura del file - 1";;
            }
            $cmp = fgetcsv($handle_source, 0, ";");
            $diff = array_diff($fields,$cmp);
            fclose($handle);
            if (count($diff) > 0) $msg = "Impossibile trovare riga di intestazione";
            if (empty($msg)) {
							$mandatory = ["ID","DESCRIZIONE","TIPO","PUNTI","RIFERIMENTO","DECIMALI"];
							foreach($array AS $i => $data) {
                $keys = array_keys($data);
                $diff = array_diff($keys,$cmp);
                if (count($diff) > 0) {
                  $msg .= "Conteggio campi errato in riga #{$i}<br>";
                } else {
									foreach($mandatory AS $man) {
										if (empty($data[$man]) && $data[$man] != "0") $msg .= "Campo {$man} obbligatorio alla riga #{$i}<br>";
									}
								}
              }
              if (empty($msg)) {
								$bind = array();
								$bind[":codice_gara"] = $_POST["codice_gara"];
								$strsql = "DELETE FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara ";
								$delete = $pdo->bindAndExec($strsql,$bind);
								
								$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara ORDER BY codice";
								$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
								if ($ris_lotti->rowCount() > 0) $lotti = $ris_lotti->fetchAll(PDO::FETCH_ASSOC);
								$codice_criteri = [];
								foreach($array AS $i => $data) {
									$codice_lotto = 0;
									if (!empty($data["NUMERO_LOTTO"]) && isset($lotti)) {
										$index = (int)$data["NUMERO_LOTTO"];
										$index--;
										$codice_lotto = $lotti[$index]["codice"];
									}
									$codice_padre = 0;
									if (!empty($data["ID_PADRE"]) && isset($codice_criteri[$data["ID_PADRE"]])) {
										$codice_padre = $codice_criteri[$data["ID_PADRE"]];
									}
									if (!empty($data["FORMULA"]) && empty($formule[$data["FORMULA"]])) {
										$msg .= "Formula non prevista alla riga #{$i}<br>";
									} else {
										$bind = array();
										$bind[":codice_gara"] = $_POST["codice_gara"];
										$bind[":codice"] = $data["RIFERIMENTO"];
										$sql_punteggi_riferimento = "SELECT b_criteri_punteggi.* FROM b_criteri_punteggi JOIN b_gare ON b_criteri_punteggi.codice_criterio = b_gare.criterio
																									WHERE b_gare.codice = :codice_gara AND b_criteri_punteggi.codice = :codice";
										$ris_punteggi_riferimento = $pdo->bindAndExec($sql_punteggi_riferimento,$bind);
										if ($ris_punteggi_riferimento->rowCount() !== 1) {
											$msg .= "Riferimento errato alla riga #{$i}<br>";
										} else {
											$key_transform_importi = ["COEF"];
											foreach($key_transform_importi AS $key) {
												$import = $data[$key];
												$point = substr_count($import,".");
												$comma = substr_count($import,",");
												if ($point > 0) {
													if ($comma > 0) {
														$import = str_replace(".","",$import);
														$import = str_replace(",",".",$import);
													} else if ($point > 1) {
														$import = str_replace(".","",$import);
													}
												} else if ($comma > 0) {
													$import = str_replace(",",".",$import);
												}
												$data[$key] = $import;
											}
											$criterio = [];
											$criterio["codice_padre"] = $codice_padre;
											$criterio["codice_gara"] = $_POST["codice_gara"];
											$criterio["codice_lotto"] = $codice_lotto;
											$criterio["tipo"] = $data["TIPO"];
											$criterio["valutazione"] = $data["FORMULA"];
											$criterio["descrizione"] = $data["DESCRIZIONE"];
											$criterio["punteggio"] = $data["PUNTI"];
											$criterio["punteggio_riferimento"] = $data["RIFERIMENTO"];
											$criterio["options"] = $data["COEF"];
											$criterio["decimali"] = (int)$data["DECIMALI"];
											$salva = new salva();
											$salva->debug = false;
											$salva->codop = $_SESSION["codice_utente"];
											$salva->nome_tabella = "b_valutazione_tecnica";
											$salva->operazione = "INSERT";
											$salva->oggetto = $criterio;
											$codice_criteri[$data["ID"]] = $codice_criterio = $salva->save();
											if ($codice_criterio == false) {
												$msg .= "Errore salvataggio criterio in riga #{$i}<br>";
											}
										}
									}
								}
								log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Criteri valutazione");
							}
            } 
          } else {
            $msg = "Errore nella lettura del file - 2";
          }
          unlink($upload_path . $filename);
        } else {
          $msg = "Errore nella lettura del file - 3";
        }
      } 
      if (!empty($msg)) {
        ?>
        <h3 class="ui-alert"><?= $msg ?></h3>
        <?
      } else {
				?>
				<h1>Importazione effettuata con successo</h1>
				<a href="index.php?codice=<?= $_POST["codice_gara"] ?>" class="espandi ritorna_button submit_big" style="background-color:#999;">Indietro</a>
				<?
			}
    }
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	

	include_once($root."/layout/bottom.php");
	?>
