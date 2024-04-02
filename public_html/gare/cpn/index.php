<?
	include_once "../../../config.php";
	if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
	if ((empty($_GET["codice"]))) {
    die("<meta http-equiv='refresh' content='0;URL=/gare'>");
  } else {
		$codice_gara = $_GET["codice"];
		if (!isset($_SESSION["codice_utente"]) || ! isset($_SESSION["ente"])) {
      die("<meta http-equiv='refresh' content='0;URL=/gare/pannello.php?codice={$codice_gara}'>");
    } else {
			$codice_fase = getFase($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
			if ($codice_fase !== false) {
				$esito = check_permessi_gara($codice_fase, $_GET["codice"], $_SESSION["codice_utente"]);
				if(! $esito["permesso"]) {
          die("<meta http-equiv='refresh' content='0;URL=/gare/pannello.php?codice={$codice_gara}'>");
        } else {
          $bind = [':codice' => $codice_gara, ':codice_ente' => $_SESSION["ente"]["codice"]];
          $sql = "SELECT * FROM b_gare WHERE codice = :codice AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
          if ($_SESSION["gerarchia"] > 0) {
						$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
						$sql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
					}
          $risultato = $pdo->bindAndExec($sql, $bind);
					if($risultato->rowCount() < 1) {
						die("<meta http-equiv='refresh' content='0;URL=/gare'>");
					} else {
            $gara = $risultato->fetch(PDO::FETCH_ASSOC);
            $cpn = $pdo->go("SELECT * FROM b_contratti_pubblici_nazionali WHERE codice_gara = :codice_gara", [':codice_gara' => $gara["codice"]])->fetch(PDO::FETCH_ASSOC);
            if(! empty($cpn)) {
              die("<meta http-equiv='refresh' content='0;URL=/cpn/pannello.php?codice={$cpn["codice"]}'>");
            } else {
              $gestore = $pdo->go("SELECT * FROM b_enti WHERE codice = :codice_gestore", [':codice_gestore' => $gara["codice_gestore"]])->fetch(PDO::FETCH_ASSOC);
              $beneficiario = $pdo->go("SELECT * FROM b_enti WHERE codice = :codice_gestore", [':codice_gestore' => $gara["codice_ente"]])->fetch(PDO::FETCH_ASSOC);
              $id_anac = $pdo->go("SELECT id_gara FROM b_simog JOIN b_lotti_simog ON b_lotti_simog.codice_simog = b_simog.codice WHERE b_lotti_simog.codice_gara = :codice_gara", [':codice_gara' => $gara["codice"]])->fetch(PDO::FETCH_COLUMN, 0);
              $importo_gara = $pdo->go("SELECT (`importo_base` + `importo_oneri_no_ribasso` + `importo_oneri_ribasso`) AS importo FROM b_importi_gara WHERE codice_gara = :codice_gara", [':codice_gara' => $gara["codice"]])->fetch(PDO::FETCH_COLUMN, 0);
              $rup = $pdo->go("SELECT * FROM b_incaricati JOIN r_incarichi ON b_incaricati.codice = r_incarichi.codice_incaricato WHERE r_incarichi.codice_riferimento = :codice_gara AND r_incarichi.sezione = 'gare' AND r_incarichi.ruolo = 14", [':codice_gara' => $gara["codice"]])->fetch(PDO::FETCH_ASSOC);

              $cpn = [];
              $cpn["codice_ente"] = $gara["codice_ente"];
              $cpn["codice_gestore"] = $gara["codice_gestore"];
              $cpn["codice_gara"] = $gara["codice"];
              $cpn["stato"] = 0;
              $cpn["oggetto"] = $gara["oggetto"];

              $json = file_get_contents("{$root}/cpn/api/WSPubblicazioni.json");
              $json = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($json));
              $json = json_decode($json, JSON_FORCE_OBJECT);
              $cpn["gara"] = array_fill_keys(array_keys($json["definitions"]["PubblicaGaraEntry"]["properties"]), '');
              $cpn["lotti"][] = array_fill_keys(array_keys($json["definitions"]["PubblicaLottoEntry"]["properties"]), '');
              $cpn["rup"] = array_fill_keys(array_keys($json["definitions"]["DatiGeneraliTecnicoEntry"]["properties"]), '');

              $cpn["gara"]['oggetto'] = $gara["oggetto"];
              $cpn["gara"]['idAnac'] = $id_anac;
              $cpn["gara"]['codiceFiscaleSA'] = $gestore["cf"];
              $cpn["gara"]['indirizzo'] = $gestore["indirizzo"];
              $cpn["gara"]['comune'] = $gestore["citta"];
              $cpn["gara"]['provincia'] = $gestore["provincia"];
              $cpn["gara"]['ufficio'] = $gara["struttura_proponente"];
              $cpn["gara"]['saAgente'] = 'N';
              $cpn["gara"]['importoGara'] = $importo_gara;
              if($gestore["codice"] != $beneficiario["codice"]) {
                $cpn["gara"]["saAgente"] = "S";
                $cpn["gara"]["nomeSA"] = $beneficiario["denominazione"];
                $cpn["gara"]["cfAgente"] = $beneficiario["cf"];
              }

              unset($cpn["gara"]["primaPubblicazioneSCP"], $cpn["gara"]["ultimaModificaSCP"], $cpn["gara"]["tecnicoRup"], $cpn["gara"]["lotti"], $cpn["gara"]["atti"], $cpn["gara"]["idRicevuto"]);

              $cpn["gara"] = json_encode($cpn["gara"]);

              if(! empty($rup)) {
                $cpn["rup"]['cognome'] = $rup["cognome"];
                $cpn["rup"]['nome'] = $rup["nome"];
                $cpn["rup"]['indirizzo'] = $rup["indirizzo"];
                $cpn["rup"]['cap'] = $rup["cap"];
                $cpn["rup"]['cfPiva'] = $rup["codice_fiscale"];
              }

              $cpn["rup"] = json_encode($cpn["rup"]);

              $lotti = $pdo->go("SELECT * FROM b_lotti WHERE codice_gara = :codice_gara", [':codice_gara' => $gara["codice"]]);
              $sth_categorie = $pdo->prepare('SELECT b_categorie_soa.id AS categoria_soa, b_classifiche_soa.id AS classifica_soa FROM b_qualificazione_lavori JOIN b_categorie_soa ON b_qualificazione_lavori.codice_categoria = b_categorie_soa.codice LEFT JOIN b_classifiche_soa ON b_qualificazione_lavori.codice_classifica = b_classifiche_soa.codice WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto');
              $sth_categorie->bindValue(':codice_gara', $gara["codice"]);

              if($lotti->rowCount() > 0) {
                $i = 0;
                $cpn["lotti"] = [];
                while ($lotto = $lotti->fetch(PDO::FETCH_ASSOC)) {
                  $cpn["lotti"][] = [
                    'uuid' => strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 10)),
                    'oggetto' => $lotto["descrizione"],
                    'importoLotto' => $lotto["importo_base"] + $lotto["importo_oneri_no_ribasso"] + $lotto["importo_oneri_ribasso"],
                    'cpv' => $lotto["cpv"],
                    'cig' => $lotto["cig"],
                    'idSceltaContraente50' => '',
                    'tipoAppalto' => ''
                  ];

                  if($gara["tipologia"] == "2") $cpn["lotti"][$i]["tipoAppalto"] = "S";
                  if($gara["tipologia"] == "3") $cpn["lotti"][$i]["tipoAppalto"] = "F";
                  if($gara["tipologia"] == "6") $cpn["lotti"][$i]["tipoAppalto"] = "S";
                  if($gara["tipologia"] == "7") $cpn["lotti"][$i]["tipoAppalto"] = "L";
                  if($gara["tipologia"] == "8") $cpn["lotti"][$i]["tipoAppalto"] = "L";

                  if($gara["procedura"] == 1) $cpn["lotti"][$i]["idSceltaContraente50"] = 1;
                  if($gara["procedura"] == 2) $cpn["lotti"][$i]["idSceltaContraente50"] = 10;
                  if($gara["procedura"] == 3) $cpn["lotti"][$i]["idSceltaContraente50"] = 2;
                  if($gara["procedura"] == 5) $cpn["lotti"][$i]["idSceltaContraente50"] = 6;
                  if($gara["procedura"] == 6) $cpn["lotti"][$i]["idSceltaContraente50"] = 8;
                  if($gara["procedura"] == 7) $cpn["lotti"][$i]["idSceltaContraente50"] = 18;
                  if($gara["procedura"] == 10) $cpn["lotti"][$i]["idSceltaContraente50"] = 19;
                  if($gara["procedura"] == 11) $cpn["lotti"][$i]["idSceltaContraente50"] = 15;

                  if($gara["tipologia"] == "8") $cpn["lotti"][$i]["criterioAggiudicazione"] = 3;
                  if($gara["tipologia"] == "9") $cpn["lotti"][$i]["criterioAggiudicazione"] = 4;

                  $sth_categorie->bindValue(':codice_lotto', $lotto["codice"]);
                  $sth_categorie->execute();
                  if($sth_categorie->rowCount() > 0) {
                    while($categoria = $sth_categorie->fetch(PDO::FETCH_ASSOC)) {
                      $cpn["lotti"][$i]["categorie"][] = [
                        "categoria" => str_replace([' ', '-'], null, $categoria["categoria_soa"]),
                        "classe" => str_replace('-bis', 'B', $categoria["classifica_soa"])
                      ];
                    }
                  }

                  $i++;
                }
              } else {
                $cpv = $pdo->go("SELECT r_cpv_gare.codice FROM r_cpv_gare WHERE codice_gara = :codice_gara LIMIT 0, 1", [':codice_gara' => $gara["codice"]])->fetch(PDO::FETCH_COLUMN, 0);
                $cpn["lotti"][0] = [
                  'uuid' => strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 10)),
                  'oggetto' => $gara["oggetto"],
                  'importoLotto' => $importo_gara,
                  'cpv' => $cpv,
                  'cig' => $gara["cig"],
                  'idSceltaContraente50' => '',
                  'tipoAppalto' => ''
                ];

                if($gara["tipologia"] == "8") $cpn["lotti"][0]["criterioAggiudicazione"] = 3;
                if($gara["tipologia"] == "9") $cpn["lotti"][0]["criterioAggiudicazione"] = 4;

                if($gara["tipologia"] == "2") $cpn["lotti"][0]["tipoAppalto"] = "S";
                if($gara["tipologia"] == "3") $cpn["lotti"][0]["tipoAppalto"] = "F";
                if($gara["tipologia"] == "6") $cpn["lotti"][0]["tipoAppalto"] = "S";
                if($gara["tipologia"] == "7") $cpn["lotti"][0]["tipoAppalto"] = "L";
                if($gara["tipologia"] == "8") $cpn["lotti"][0]["tipoAppalto"] = "L";

                if($gara["procedura"] == 1) $cpn["lotti"][0]["idSceltaContraente50"] = 1;
                if($gara["procedura"] == 2) $cpn["lotti"][0]["idSceltaContraente50"] = 10;
                if($gara["procedura"] == 3) $cpn["lotti"][0]["idSceltaContraente50"] = 2;
                if($gara["procedura"] == 5) $cpn["lotti"][0]["idSceltaContraente50"] = 6;
                if($gara["procedura"] == 6) $cpn["lotti"][0]["idSceltaContraente50"] = 8;
                if($gara["procedura"] == 7) $cpn["lotti"][0]["idSceltaContraente50"] = 18;
                if($gara["procedura"] == 10) $cpn["lotti"][0]["idSceltaContraente50"] = 19;
                if($gara["procedura"] == 11) $cpn["lotti"][0]["idSceltaContraente50"] = 15;

                $sth_categorie->bindValue(':codice_lotto', 0);
                  $sth_categorie->execute();
                  if($sth_categorie->rowCount() > 0) {
                    while($categoria = $sth_categorie->fetch(PDO::FETCH_ASSOC)) {
                      $cpn["lotti"][0]["categorie"][] = [
                        "categoria" => str_replace([' ', '-'], null, $categoria["categoria_soa"]),
                        "classe" => str_replace('-bis', 'B', $categoria["classifica_soa"])
                      ];
                    }
                  }

              }

              $cpn["lotti"] = json_encode($cpn["lotti"]);

              $salva = new salva();
              $salva->debug = FALSE;
              $salva->codop = $_SESSION["codice_utente"];
              $salva->nome_tabella = "b_contratti_pubblici_nazionali";
              $salva->operazione =  'INSERT';
              $salva->oggetto = $cpn;
              $codice_cpn = $salva->save();
              if(is_numeric($codice_cpn) && $codice_cpn > 0) {
                echo "<meta http-equiv='refresh' content='0;URL=/cpn/pannello.php?codice={$codice_cpn}'>";
              } else {
                echo "<meta http-equiv='refresh' content='0;URL=/gare/pannello.php?codice={$codice_gara}'>";
              }
            }
          }
        }
			}
		}
	}
?>