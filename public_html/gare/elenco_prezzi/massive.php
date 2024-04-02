<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		if (!empty($_POST["codice_gara"])) {
			$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/elenco_prezzi/edit.php'";
			$risultato = $pdo->query($strsql);
			if ($risultato->rowCount()>0) {
				$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
				$esito = check_permessi_gara($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
				$edit = $esito["permesso"];
				$lock = $esito["lock"];
			}
			if (!$edit) {
				echo "1";// echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
				die();
			}
		} else {
			echo "2";// echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo "3";// echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
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
							foreach($array AS $i => $data) {
                $keys = array_keys($data);
                $diff = array_diff($keys,$cmp);
                if (count($diff) > 0) {
                  $msg .= "Conteggio campi errato in riga #{$i}<br>";
                } else {
									foreach($data AS $key => $value) {
										if (empty($value) && $value != "0") $msg .= "Campo {$key} obbligatorio alla riga #{$i}<br>";
									}
								}
              }
              if (empty($msg)) {
								$bind = array();
								$bind[":codice_gara"] = $_POST["codice_gara"];
								$strsql = "DELETE FROM b_elenco_prezzi WHERE codice_gara = :codice_gara ";
								$delete = $pdo->bindAndExec($strsql,$bind);
								
								$sql_check = "SELECT * FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara AND codice = :codice AND valutazione = 'E'";
                $ris_check = $pdo->prepare($sql_check);
                $ris_check->bindValue(":codice_gara",$_POST["codice_gara"]);
								foreach($array AS $i => $data) {
                  $ris_check->bindValue(":codice",$data["ID_ELENCO"]);
                  $ris_check->execute();
                  if ($ris_check->rowCount() !== 1) {
                    if (empty($value)) $msg .= "ID Elenco non trovato per la riga #{$i}<br>";
                  } else {
                    $key_transform_importi = ["QUANTITA"];
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
                    $voce = [];
                    $voce["codice_gara"] = $_POST["codice_gara"];
                    $voce["codice_criterio"] = $data["ID_ELENCO"];
                    $voce["tipo"] = (strtolower($data["TIPO"])=="misura") ? "misura" : "corpo";
                    $voce["descrizione"] = $data["DESCRIZIONE"];
                    $voce["unita"] = $data["UNITA"];
                    $voce["quantita"] = $data["QUANTITA"];
                    $salva = new salva();
                    $salva->debug = false;
                    $salva->codop = $_SESSION["codice_utente"];
                    $salva->nome_tabella = "b_elenco_prezzi";
                    $salva->operazione = "INSERT";
                    $salva->oggetto = $voce;
                    if ($salva->save() == false) {
                      $msg .= "Errore salvataggio criterio in riga #{$i}<br>";
                    }
									}
								}
								log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Elenco prezzi");
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
				<a href="edit.php?codice=<?= $_POST["codice_gara"] ?>" class="espandi ritorna_button submit_big" style="background-color:#999;">Indietro</a>
				<?
			}
    }
	} else {
		echo "4";// echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	

	include_once($root."/layout/bottom.php");
	?>
