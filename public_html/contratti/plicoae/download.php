<?
  session_start();
  include_once "../../../config.php";
  include_once $root . "/inc/funzioni.php";
  include_once $root . "/contratti/plicoae/array2xml.class.php";

  if(empty($_POST["codice"]) || empty($_SESSION["codice_utente"]) || !isset($_SESSION["ente"]) || !check_permessi("contratti",$_SESSION["codice_utente"])) {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  } else {
    $codice = $_POST["codice"];
    $codice_gara = !empty($_GET["codice_gara"]) ? $_POST["codice_gara"] : null;
    $bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ':codice' => $codice);
    $sql  = "SELECT b_contratti.*, b_conf_modalita_stipula.invio_remoto FROM b_contratti JOIN b_conf_modalita_stipula ON b_contratti.modalita_stipula = b_conf_modalita_stipula.codice ";
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
    $href_contratto = null;
    if($ris->rowCount() == 1) {
      $plico = $_POST["plico"];
      $adempimento = $_POST["adempimento"];
      $datisoggetto = $_POST["datisoggetto"];
      $rec_contratto = $ris->fetch(PDO::FETCH_ASSOC);
      $href_contratto = "?codice=".$rec_contratto["codice"] . (!empty($rec_contratto["codice_gara"]) ? "&codice_gara=".$rec_contratto["codice_gara"] : null);
      $post_plico_ae = json_encode($_POST);
      $pdo->bindAndExec("UPDATE b_contratti SET post_plico_ae = :post_plico_ae WHERE codice = :codice_contratto", array(':post_plico_ae' => $post_plico_ae, ':codice_contratto' => $rec_contratto["codice"]));
      $i = 1;

      if(! empty($adempimento["TestoAtto"]["TestoLibero"])) {

      }
      $adempimento["TestoAtto"]["TestoLibero"] = html_entity_decode($adempimento["TestoAtto"]["TestoLibero"], ENT_QUOTES, 'UTF-8');
      $adempimento["TestoAtto"]["TestoLibero"] = mb_convert_encoding($adempimento["TestoAtto"]["TestoLibero"], "UTF-8", "auto");

      $adempimento["TestoAtto"]["TestoLibero"] = iconv('UTF-8', 'ASCII//TRANSLIT', $adempimento["TestoAtto"]["TestoLibero"]);
      $adempimento["TestoAtto"]["TestoLibero"] = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $adempimento["TestoAtto"]["TestoLibero"]);
      $adempimento["TestoAtto"]["TestoLibero"] = strtoupper($adempimento["TestoAtto"]["TestoLibero"]);

      $xml = [
        'Telematico' => [
          'DatiTelematico' => [
            '@CodUfficioEntrate' => $plico["CodUfficioEntrate"],
            'ChiaveFile' => [
              '@Controllo' => 'TUTTOGARE v2.0',
              '@ProgressivoInvio' => $rec_contratto["codice"],
              'CodiceFiscalePU' => $plico["PubblicoUfficiale"]["CodiceFiscalePU"],
              'PubblicoUfficiale' => [
                '@Cap' => $plico["PubblicoUfficiale"]["Cap"],
                '@CodiceComune' => $plico["PubblicoUfficiale"]["CodiceComune"],
                '@DenominazionePU' => $plico["PubblicoUfficiale"]["cognome"] . " " . $plico["PubblicoUfficiale"]["nome"],
                '@Indirizzo' => $plico["PubblicoUfficiale"]["Indirizzo"],
                '@TipoPU' => $plico["PubblicoUfficiale"]["TipoPU"],
              ]
            ],
            'PagamentoTelematico' => [
              '@CodiceFiscale' => $plico["CodiceFiscale"],
              '@CodiceFiscaleStudio' => '',
              '@CodiceIBAN' => $plico["CodiceIBAN"],
            ],
            'Adempimento' => [
              'ChiaveAdempimento' => [
                'PrimoNumeroRepertorio' => $adempimento["PrimoNumeroRepertorio"],
                'SecondoNumeroRepertorio' => $adempimento["SecondoNumeroRepertorio"],
                'CodiceFiscalePU' => $plico["PubblicoUfficiale"]["CodiceFiscalePU"],
                'PubblicoUfficiale' => [
                  '@Cap' => $plico["PubblicoUfficiale"]["Cap"],
                  '@CodiceComune' => $plico["PubblicoUfficiale"]["CodiceComune"],
                  '@DenominazionePU' => $plico["PubblicoUfficiale"]["cognome"] . " " . $plico["PubblicoUfficiale"]["nome"],
                  '@Indirizzo' => $plico["PubblicoUfficiale"]["Indirizzo"],
                  '@TipoPU' => $plico["PubblicoUfficiale"]["TipoPU"],
                ],
              ],
              'DatiTitolo' => [
                '@Elaborazione' => $adempimento["DatiTitolo"]["Elaborazione"],
                '@SoloVoltura' => '0',
                '@TipoBollo' => $adempimento["DatiTitolo"]["TipoBollo"],
                'Titolo' => [
                  '@ModalitaPagamentoCorr' => $adempimento["DatiTitolo"]["Titolo"]["ModalitaPagamentoCorr"],
                  '@AttoEsenteRegistrazione' => $adempimento["DatiTitolo"]["Titolo"]["AttoEsenteRegistrazione"],
                  // '@DataAtto' => substr($adempimento["DatiTitolo"]["Titolo"]["DataAtto"], 6, 4).substr($adempimento["DatiTitolo"]["Titolo"]["DataAtto"], 3, 2).substr($adempimento["DatiTitolo"]["Titolo"]["DataAtto"], 0, 2),
                  '@DataAtto' => substr($adempimento["DatiTitolo"]["Titolo"]["DataAtto"], 0, 2).substr($adempimento["DatiTitolo"]["Titolo"]["DataAtto"], 3, 2).substr($adempimento["DatiTitolo"]["Titolo"]["DataAtto"], 6, 4),
                  '@Descrizione' => $adempimento["DatiTitolo"]["Titolo"]["Descrizione"],
                ]
              ],
              'DatiSoggetto' => [],
              'DatiAltroSoggetto' => [],
              'DatiNegozio' => [
                '@IdNegozio' => 'N'.str_repeat('0',6-strlen($i)).$i,
                '@ValoreNegozio' => number_format($adempimento["DatiNegozio"]["ValoreNegozio"], 2, '', ''),
                'Negozio' => [
                  '@CodiceNegozio' => '7003',
                  '@GaranziaPerDebitoNonProprio' => $adempimento["DatiNegozio"]["GaranziaPerDebitoNonProprio"],
                  '@IdSoggettoAventeCausa' => 'S000002',
                  '@IdSoggettoDanteCausa' => 'S000001',
                  'Imponibili' => [
                    '@Registro' => number_format($adempimento["DatiNegozio"]["ValoreNegozio"], 2, '', ''),
                  ],
                  'Agevolazione' => [
                    '@Tipo' => $adempimento["DatiNegozio"]["Agevolazione"],
                  ],
                  'InfoTassazione' => [
                    '@Esente' => $adempimento["DatiNegozio"]["Esente"] == 1 ? 0 : 1,
                  	'@SoggettoIVA' => $adempimento["DatiNegozio"]["SoggettoIVA"],
                  	'@EffettiSospesi' => $adempimento["DatiNegozio"]["EffettiSospesi"],
                  ],
                  'Tassazione' => []
                ]
              ],
              'TestoAtto' => [
                'TestoLibero' => $adempimento["TestoAtto"]["TestoLibero"],
                'PrimoNumeroRepertorio' => $adempimento["PrimoNumeroRepertorio"],
                'SecondoNumeroRepertorio' => $adempimento["SecondoNumeroRepertorio"],
              ],
              'TitoloDigitale' => [
                '@Formato' => 'PDF',
                'CopiaTitolo' => 'TEST'
              ]
            ]
          ]
        ]
      ];

      if(!empty($adempimento["DatiNegozio"]["Negozio"]["Tassazione"])) {
        foreach ($adempimento["DatiNegozio"]["Negozio"]["Tassazione"] as $codice_tributo => $info) {
          if(isset($info["Importo"]) && isset($info["Aliquota"])) {
            $xml["Telematico"]["DatiTelematico"]["Adempimento"]["DatiNegozio"]["Negozio"]["Tassazione"][] = [
              '@CodiceTributo' => $codice_tributo,
              '@Importo' => number_format($info["Importo"], 2, '', ''),
            ];
          }
        }
      }

      $sql_ente = "SELECT * FROM b_enti WHERE codice = :codice_ente AND attivo = 'S'";
      $ris_ente = $pdo->bindAndExec($sql_ente, array(':codice_ente' => $rec_contratto["codice_ente"]));
      $rec_ente = $ris_ente->fetch(PDO::FETCH_ASSOC);

      $rec_ore = get_campi('b_contraenti');
      $sql_ore = "SELECT b_contraenti.* FROM b_contraenti ";
      $sql_ore .= "JOIN r_contratti_contraenti ON r_contratti_contraenti.codice_contraente = b_contraenti.codice ";
      $sql_ore .= "WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia = 'ore' ";

      $ris_ufficiale = $pdo->bindAndExec($sql_ore, array(':codice_contratto' => $rec_contratto["codice"]));
      if($ris_ufficiale->rowCount() > 0) {
        $rec_ore = $ris_ufficiale->fetch(PDO::FETCH_ASSOC);
        $xml["Telematico"]["DatiTelematico"]["PagamentoTelematico"]["@CodiceFiscaleStudio"] = $rec_ente["cf"];
        $xml["Telematico"]["DatiTelematico"]["Adempimento"]["DatiNegozio"]["Negozio"]["@IdSoggettoDanteCausa"] = 'S'.str_repeat('0',6-strlen($rec_ore["codice"])).$rec_ore["codice"];
        $xml["Telematico"]["DatiTelematico"]["Adempimento"]["DatiSoggetto"][] = [
          '@IdAltroSoggetto' => 'SR'.str_repeat('0',6-strlen($rec_ore["codice"])).$rec_ore["codice"],
          '@IdSoggetto' => 'S'.str_repeat('0',6-strlen($rec_ore["codice"])).$rec_ore["codice"],
          'SoggettoN' => [
            '@CodiceFiscale' => $rec_ente["cf"],
          	'@Denominazione' => $rec_ente["denominazione"],
          	'@Sede' => $rec_ente["indirizzo"] . " " . $rec_ente["citta"],
          	'@Provincia' => $rec_ente["provincia"],
          ]
        ];
        $xml["Telematico"]["DatiTelematico"]["Adempimento"]["DatiAltroSoggetto"][] = [
          '@IdAltroSoggetto' => 'SR'.str_repeat('0',6-strlen($rec_ore["codice"])).$rec_ore["codice"],
          '@Qualifica' => '5',
          '@TipoAltroSoggetto' => '1',
          'SoggettoF' => [
            '@CodiceFiscale' => $rec_ore["cf"],
          	'@Cognome' => $rec_ore["cognome"],
            '@DataNascita' => substr($rec_ore["data_nascita"],8,2).substr($rec_ore["data_nascita"],5,2).substr($rec_ore["data_nascita"],0,4),
          	'@Nome' => $rec_ore["nome"],
          	'@ComuneNascita' => $rec_ore["comune_nascita"],
          	'@Provincia' => $rec_ore["provincia_residenza"],
          ],
          'Residenza' => [
            '@TipoDomicilio' => $datisoggetto[$rec_ore["codice"]]["TipoDomicilio"],
            'IndirizzoAnagrafico' => [
              '@CodiceComune' => $datisoggetto[$rec_ore["codice"]]["CodiceComune"],
            ]
          ]
        ];
      }

      $ris_oe = $pdo->bindAndExec("SELECT b_contraenti.*, r_contratti_contraenti.codice_contratto FROM b_contraenti JOIN r_contratti_contraenti ON r_contratti_contraenti.codice_contraente = b_contraenti.codice WHERE b_contraenti.tipologia = :tipologia AND r_contratti_contraenti.codice_contratto = :codice_contratto AND (r_contratti_contraenti.codice_capogruppo = 0 OR r_contratti_contraenti.codice_capogruppo IS NULL) LIMIT 0,1", array(':tipologia' => "oe", ':codice_contratto' => $codice));
      if($ris_oe->rowCount() > 0) {
        while($rec_oe = $ris_oe->fetch(PDO::FETCH_ASSOC)) {
          $xml["Telematico"]["DatiTelematico"]["Adempimento"]["DatiNegozio"]["Negozio"]["@IdSoggettoAventeCausa"] = 'S'.str_repeat('0',6-strlen($rec_oe["codice"])).$rec_oe["codice"];
          if($rec_oe["ruolo"] == "libero_professionista") {
            $xml["Telematico"]["DatiTelematico"]["Adempimento"]["DatiSoggetto"][] = [
              '@IdSoggetto' => 'S'.str_repeat('0',6-strlen($rec_oe["codice"])).$rec_oe["codice"],
              'SoggettoF' => [
                '@CodiceFiscale' => $rec_oe["cf"],
                '@Cognome' => $rec_oe["cognome"],
                '@Nome' => $rec_oe["nome"],
                '@DataNascita' => substr($rec_oe["data_nascita"],8,2).substr($rec_oe["data_nascita"],5,2).substr($rec_oe["data_nascita"],0,4),
                '@ComuneNascita' => $rec_oe["comune_nascita"],
                '@Provincia' => $rec_oe["provincia_residenza"],
              ]
            ];
          } else {
            $xml["Telematico"]["DatiTelematico"]["Adempimento"]["DatiNegozio"]["Negozio"]["@IdSoggettoAventeCausa"] = 'S'.str_repeat('0',6-strlen($rec_oe["codice"])).$rec_oe["codice"];
            $xml["Telematico"]["DatiTelematico"]["Adempimento"]["DatiSoggetto"][] = [
              '@IdAltroSoggetto' => 'SR'.str_repeat('0',6-strlen($rec_oe["codice"])).$rec_oe["codice"],
              '@IdSoggetto' => 'S'.str_repeat('0',6-strlen($rec_oe["codice"])).$rec_oe["codice"],
              'SoggettoN' => [
                '@CodiceFiscale' => $rec_oe["partita_iva"],
              	'@Denominazione' => $rec_oe["denominazione"],
              	'@Sede' => $rec_oe["sede"],
              ]
            ];
            $xml["Telematico"]["DatiTelematico"]["Adempimento"]["DatiAltroSoggetto"][] = [
              '@IdAltroSoggetto' => 'SR'.str_repeat('0',6-strlen($rec_oe["codice"])).$rec_oe["codice"],
              '@Qualifica' => $rec_oe["ruolo"] == "legale rappresentante" ? '4' : '5',
              '@TipoAltroSoggetto' => '1',
              'SoggettoF' => [
                '@CodiceFiscale' => $rec_oe["cf"],
              	'@Cognome' => $rec_oe["cognome"],
                '@DataNascita' => substr($rec_oe["data_nascita"],8,2).substr($rec_oe["data_nascita"],5,2).substr($rec_oe["data_nascita"],0,4),
              	'@Nome' => $rec_oe["nome"],
              	'@ComuneNascita' => $rec_oe["comune_nascita"],
              	'@Provincia' => $rec_oe["provincia_residenza"],
              ],
              'Residenza' => [
                '@TipoDomicilio' => $datisoggetto[$rec_oe["codice"]]["TipoDomicilio"],
                'IndirizzoAnagrafico' => [
                  '@CodiceComune' => $datisoggetto[$rec_oe["codice"]]["CodiceComune"],
                ]
              ]
            ];
          }
        }
      }

      header('Content-Type: text/xml');
      header('Content-disposition: attachment; filename="Plico'.$rec_contratto["codice"].'.xml"');
      header('Content-type: "text/xml"; charset="ISO-8859-1"');

      unset($xml["Telematico"]["DatiTelematico"]["Adempimento"]["TitoloDigitale"]);

      $xml = array_filter(array_map('array_filter', $xml));
      $xml = array2XML($xml,null,true);
      $xml = trim(preg_replace('/\s\s+/', ' ', $xml));
      $xml = preg_replace("/[\n\r]/","",$xml);
      $xml = html_entity_decode($xml, ENT_QUOTES, 'ISO-8859-1');
      $xml = str_replace("&", "&amp;", $xml);


      $doc = new DOMDocument('1.0', 'ISO-8859-1');
      $doc->preserveWhiteSpace = false;
      $doc->formatOutput = true;
      $doc->loadXML($xml);

      $imp = new DOMImplementation;
      $doctype = $imp->createDocumentType("Telematico", "", "Unico18012016.dtd");
      $telematico = $doc->getElementsByTagName('Telematico')->item(0);
      $doc->insertBefore($doctype, $telematico);
      $doc->encoding = 'ISO-8859-1';

      // $ris = $pdo->bindAndExec("SELECT * FROM `b_allegati` WHERE `sezione` = 'contratti' AND `codice_gara` = :codice_contratto AND `cartella` = 'contratti_firmati'", array(':codice_contratto' => $rec_contratto["codice"]));
      // if($ris->rowCount() > 0) {
      //   $rec_contratto_firmato = $ris->fetch(PDO::FETCH_ASSOC);
      //   if(file_exists("{$config["arch_folder"]}/allegati_contratto/{$rec_contratto["codice"]}/{$rec_contratto_firmato["riferimento"]}")) {
      //     $doc->getElementsByTagName("CopiaTitolo")->item(0)->nodeValue = base64_encode(file_get_contents("{$config["arch_folder"]}/allegati_contratto/{$rec_contratto["codice"]}/{$rec_contratto_firmato["riferimento"]}"));
      //   } else {
      //     unset($xml["Telematico"]["DatiTelematico"]["Adempimento"]["TitoloDigitale"]);
      //   }
      // }

      echo $doc->saveXML();
    }
  }
?>
