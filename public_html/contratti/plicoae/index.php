<?
  session_start();
  include_once "../../../config.php";
  include_once $root . "/layout/top.php";

  if(empty($_GET["codice"]) || empty($_SESSION["codice_utente"]) || !isset($_SESSION["ente"]) || !check_permessi("contratti",$_SESSION["codice_utente"])) {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  } else {
    $codice = $_GET["codice"];
    $codice_gara = !empty($_GET["codice_gara"]) ? $_GET["codice_gara"] : null;
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
      $rec_contratto = $ris->fetch(PDO::FETCH_ASSOC);
      $href_contratto = "?codice=".$rec_contratto["codice"] . (!empty($rec_contratto["codice_gara"]) ? "&codice_gara=".$rec_contratto["codice_gara"] : null);

      $rec_ufficiale_rogante = get_campi('b_ufficiale_rogante');
      $sql_ufficiale = "SELECT b_ufficiale_rogante.* FROM b_ufficiale_rogante ";
			$sql_ufficiale .= "JOIN r_contratti_ufficiale_rogante ON b_ufficiale_rogante.codice = r_contratti_ufficiale_rogante.codice_ufficiale ";
			$sql_ufficiale .= "WHERE r_contratti_ufficiale_rogante.codice_contratto = :codice_contratto ";
			$ris_ufficiale = $pdo->bindAndExec($sql_ufficiale, array(':codice_contratto' => $rec_contratto["codice"]));
			if($ris_ufficiale->rowCount() > 0) {
				$rec_ufficiale_rogante = $ris_ufficiale->fetch(PDO::FETCH_ASSOC);
			}

      $rec_ore = get_campi('b_contraenti');
      $sql_ore = "SELECT b_contraenti.* FROM b_contraenti ";
      $sql_ore .= "JOIN r_contratti_contraenti ON r_contratti_contraenti.codice_contraente = b_contraenti.codice ";
      $sql_ore .= "WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia = 'ore' ";
      $ris_ufficiale = $pdo->bindAndExec($sql_ore, array(':codice_contratto' => $rec_contratto["codice"]));
      if($ris_ufficiale->rowCount() > 0) {
          $rec_ore = $ris_ufficiale->fetch(PDO::FETCH_ASSOC);
      }

      $adempimento = $plico = $data = $datisoggetto = array();
      $plico = [
        'CodUfficioEntrate' => 'RJ6',
        'CodiceFiscale' => '',
        'CodiceIBAN' => '',
        'PubblicoUfficiale' => [
          'TipoPU' => $rec_ufficiale_rogante["tipo_ufficiale"],
          'cognome' => ucwords(strtolower(html_entity_decode($rec_ufficiale_rogante["cognome"]))),
          'nome' => ucwords(strtolower(html_entity_decode($rec_ufficiale_rogante["nome"]))),
          'CodiceFiscalePU' => strtoupper($rec_ufficiale_rogante["cf"]),
          'Comune' => $rec_contratto["comune_stipula"],
          'CodiceComune' => $rec_contratto["codice_comune_stipula"] ,
          'Cap' => '',
          'Pr' => $rec_contratto["provincia_stipula"],
          'Indirizzo' => $rec_contratto["indirizzo_stipula"],
        ]
      ];



      $adempimento = [
        'PrimoNumeroRepertorio' => '2',
        'SecondoNumeroRepertorio' => '2016',
        'DatiTitolo' => [
          'Elaborazione' => 1,
          'TipoBollo' => 1,
          'Titolo' => [
            'Descrizione' => 'LOREM IPSUM',
            'DataAtto' => '03/10/2016',
            'AttoEsenteRegistrazione' => '0',
            'ModalitaPagamentoCorr' => 'C',
          ],
        ],
        'Amministrazione' => [
          'SoggettoF' => [
            'Nome' => ucwords(strtolower(html_entity_decode($rec_ore["nome"], ENT_QUOTES, 'UTF-8'))),
            'Cognome' => ucwords(strtolower(html_entity_decode($rec_ore["cognome"], ENT_QUOTES, 'UTF-8'))),
            'CodiceFiscale' => $rec_ore["cf"],
            'DataNascita' => mysql2date($rec_ore["data_nascita"]),
          ]
        ],
        'DatiNegozio' => [
          'ValoreNegozio' => '',
          'GaranziaPerDebitoNonProprio' => '',
          'Agevolazione' => '',
          'Esente' => '',
          'SoggettoIVA' => '',
          'EffettiSospesi' => '',
          'Negozio' => [
            'Tassazione' => [
              '9814' => [
                'Importo' => '',
                'Aliquota' => '',
              ],
              '9802' => [
                'Importo' => '',
                'Aliquota' => '',
              ],
            ]
          ]
        ],
        'TestoAtto' => [
          'TestoLibero' => '',
        ]
      ];


      if (!empty($rec_contratto["post_plico_ae"])) {
        $data = array_replace($data, json_decode($rec_contratto["post_plico_ae"], TRUE));
        if(!empty($data["plico"])) $plico = array_replace($plico, $data["plico"]);
        if(!empty($data["adempimento"])) $adempimento = array_replace($adempimento, $data["adempimento"]);
        if(!empty($data["datisoggetto"])) $datisoggetto = array_replace($datisoggetto, $data["datisoggetto"]);
      }

      ?>
      <style media="screen">
				input[type="text"] {
					width: 100%;
					box-sizing : border-box;
					font-family: Tahoma, Geneva, sans-serif;
					font-size: 1em
				}
				input[type="text"]:disabled {
					background: #dddddd;
				}
			</style>
      <h1>GENERZIONE AUTOMATICA PLICO UNIMOD - AGENZIA DELLE ENTRATE</h1><br>
      <form action="download.php" method="post" target="_blank" rel="validate">
        <input type="hidden" name="codice" value="<? echo $codice; ?>">
        <input type="hidden" name="codice_gara" value="<? echo $codice_gara; ?>">
    		<div class="comandi">
    			<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
    		</div>
        <h3>Informazioni Plico</h3>
        <div class="box">
          <table style="width:100%">
            <tr>
              <td style="width:25%">Ufficio Agenzia delle Entrate: *</td>
              <td colspan="3">
                <select name="plico[CodUfficioEntrate]" rel="S;1;0;A" title="Ufficio Agenzia">
                  <option value="">Seleziona..</option>
                  <?
                  $ris = $pdo->bindAndExec("SELECT * FROM b_conf_uffici_entrate ORDER BY denominazione ASC");
                  if($ris->rowCount() > 0) {
                    while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
                      ?><option <?= $plico["CodUfficioEntrate"] == $rec["codice_ufficio"] ? 'selected="selected"' : null ?> value="<?= $rec["codice_ufficio"] ?>"><?= $rec["denominazione"] ?></option> <?
                    }
                  }
                  ?>
                </select>
              </td>
            </tr>
          </table>
        </div>
        <br><h3>Informazioni per il pagamento telematico</h3>
        <div class="box">
          <table style="width:100%">
            <tr>
              <td style="width:25%;">C.F. Intestatario del conto: *</td>
              <td><input type="text" name="plico[CodiceFiscale]" value="<?= $plico["CodiceFiscale"] ?>" rel="S;11;16;A" title="CF Intestatario del conto"></td>
            </tr>
            <tr>
              <td>IBAN: *</td>
              <td><input type="text" name="plico[CodiceIBAN]" value="<?= $plico["CodiceIBAN"] ?>" rel="S;27;27;A" title="Codice Iban"></td>
            </tr>
          </table>
        </div>
        <br><h3>Informazioni Pubblico Ufficiale abilitato alla trasmissione dell&#39;atto</h3>
        <div class="box">
          <table style="width:100%">
            <tr>
              <td class="etichtetta">
                <b>Tipologia: *</b>
              </td>
              <td>
                <b>Cognome: *</b>
              </td>
              <td style="width:25%">
                <b>Nome: *</b>
              </td>
              <td style="width:25%">
                <b>C.F.: *</b>
              </td>
            </tr>
            <tr>
              <td>
                <select id="tipo_ufficiale" name="plico[PubblicoUfficiale][TipoPU]" rel="S;1;0;A" title="Tipologia ufficiale rogante">
                  <option value="">Seleziona..</option>
                  <option <?= $plico["PubblicoUfficiale"]["TipoPU"] == "1" ? 'selected="selected"' : null ?> value="1">Notaio</option>
                  <option <?= $plico["PubblicoUfficiale"]["TipoPU"] == "2" ? 'selected="selected"' : null ?> value="2">Altro Ufficiale Rogante</option>
                  <option <?= $plico["PubblicoUfficiale"]["TipoPU"] == "3" ? 'selected="selected"' : null ?> value="3">Autorit&agrave; Emittente</option>
                  <option <?= $plico["PubblicoUfficiale"]["TipoPU"] == "4" ? 'selected="selected"' : null ?> value="2">Altro Ufficiale Rogante</option>
                </select>
              </td>
              <td><input type="text" title="Cognome PU" name="plico[PubblicoUfficiale][cognome]" value="<?= $plico["PubblicoUfficiale"]["cognome"] ?>" rel="S;1;0;A"></td>
              <td><input type="text" title="Nome PU" name="plico[PubblicoUfficiale][nome]" value="<?= $plico["PubblicoUfficiale"]["nome"] ?>" rel="S;1;0;A"></td>
              <td><input type="text" title="Codice Fiscale PU" name="plico[PubblicoUfficiale][CodiceFiscalePU]" value="<?= $plico["PubblicoUfficiale"]["CodiceFiscalePU"] ?>" rel="S;16;16;A"></td>
            </tr>
            <tr>
              <td><b>Comune:</b></td>
              <td><b>Codice Comune: *</b></td>
              <td><b>CAP:</b></td>
              <td><b>Provincia:</b></td>
            </tr>
            <tr>
              <td><input type="text" title="Comune" name="plico[PubblicoUfficiale][Comune]" value="<?= $plico["PubblicoUfficiale"]["Comune"] ?>" rel="N;0;0;A"></td>
              <td><input type="text" title="Codice Comune" name="plico[PubblicoUfficiale][CodiceComune]" value="<?= $plico["PubblicoUfficiale"]["CodiceComune"] ?>" rel="S;2;4;A"></td>
              <td><input type="text" title="Cap" name="plico[PubblicoUfficiale][Cap]" value="<?= $plico["PubblicoUfficiale"]["Cap"] ?>" rel="N;0;0;N"></td>
              <td><input type="text" title="PR" name="plico[PubblicoUfficiale][Pr]" value="<?= $plico["PubblicoUfficiale"]["Pr"] ?>" rel="N;0;2;A"></td>
            </tr>
            <tr>
              <td><b>Indirizzo: *</b></td>
              <td colspan="3"><input type="text" title="Indirizzo" name="plico[PubblicoUfficiale][Indirizzo]" value="<?= $plico["PubblicoUfficiale"]["Indirizzo"] ?>" rel="N;0;0;A"></td>
            </tr>
          </table>
        </div>
        <br><h3>Informazioni Adempimento</h3>
        <div class="box">
          <table style="width:100%">
            <tr>
              <td style="width:25%;"><b>Primo N. Repertorio: *</b></td>
              <td style="width:25%;"><input type="text" name="adempimento[PrimoNumeroRepertorio]" value="<?= $adempimento["PrimoNumeroRepertorio"] ?>" rel="S;1;7;N" title="Primo N. Repertorio"></td>
              <td style="width:25%;"><b>Secondo N. Repertorio:</b></td>
              <td style="width:25%;"><input type="text" name="adempimento[SecondoNumeroRepertorio]" value="<?= $adempimento["SecondoNumeroRepertorio"] ?>" rel="N;0;5;N" title="Secondo N. Repertorio"></td>
            </tr>
            <tr>
              <td><b>Uffici Interessati dall&#39;Adempimento: *</b></td>
              <td>
                <select class="" name="adempimento[DatiTitolo][Elaborazione]" rel="S;1;0;A" title="Uffici interesati dall&#39;adempimento">
                  <option value="">Seleziona...</option>
                  <option <?= $adempimento["DatiTitolo"]["Elaborazione"] ==  1 ? 'selected="seleced"' : null?> value="1">Solo Ufficio delle Entrate  (ex E)</option>
            			<option <?= $adempimento["DatiTitolo"]["Elaborazione"] ==  2 ? 'selected="seleced"' : null?> value="2">Solo Uffici del Territorio  (ex T)</option>
            			<option <?= $adempimento["DatiTitolo"]["Elaborazione"] ==  3 ? 'selected="seleced"' : null?> value="3">Uffici delle Entrate e del Territorio  (ex C)</option>
            			<option <?= $adempimento["DatiTitolo"]["Elaborazione"] ==  4 ? 'selected="seleced"' : null?> value="4">Solo Uffici del Libro Fondiario</option>
            			<option <?= $adempimento["DatiTitolo"]["Elaborazione"] ==  5 ? 'selected="seleced"' : null?> value="5">Uffici dell'Entrate e del Libro Fondiario</option>
            			<option <?= $adempimento["DatiTitolo"]["Elaborazione"] ==  6 ? 'selected="seleced"' : null?> value="6">Uffici del Territorio  e del Libro Fondiario</option>
            			<option <?= $adempimento["DatiTitolo"]["Elaborazione"] ==  7 ? 'selected="seleced"' : null?> value="7">Uffici delle Entrate, del Territorio e del Libro Fondiario</option>
                </select>
              </td>
              <td><b>Tipo Bollo: *</b></td>
              <td>
                <select class="" name="adempimento[DatiTitolo][TipoBollo]" rel="S;1;0;A" title="Tipo Bollo: *">
                  <option value="">Seleziona...</option>
                  <option <?= $adempimento["DatiTitolo"]["TipoBollo"] ==  1 ? 'selected="seleced"' : null?> value="1">L&#39;atto &egrave; esente da imposta di bollo</option>
            		  <option <?= $adempimento["DatiTitolo"]["TipoBollo"] ==  0 ? 'selected="seleced"' : null?> value="0">Imposta forfetaria (registrazione e pubblicit&agrave; immobiliare) in base alle norme vigenti</option>
                </select>
              </td>
            </tr>
            <tr>
              <td><b>Descrizione Titolo :*</b></td>
              <td colspan="3">
                <input type="text" name="adempimento[DatiTitolo][Titolo][Descrizione]" value="<?= $adempimento["DatiTitolo"]["Titolo"]["Descrizione"] ?>" title="Descrizione Titolo" rel="S;1;61;A">
                <small>(forma del titolo, secondo quanto indicato nella Circolare n.128/T del 2 maggio 1995 e 24/E del 17 giugno 2015)</small>
              </td>
            </tr>
            <tr>
              <td><b>Data dell&#39;atto: *</b></td>
              <td style="width:25%;"><input type="text" class="datepick" name="adempimento[DatiTitolo][Titolo][DataAtto]" value="<?= $adempimento["DatiTitolo"]["Titolo"]["DataAtto"] ?>" rel="S;10;10;D" title="Data dell&#39;atto"></td>
              <td><b>Esente da registrazione: *</b></td>
              <td>
                <select name="adempimento[DatiTitolo][Titolo][AttoEsenteRegistrazione]" rel="S;1;0;N" title="Registrazione">
                  <option value="">Seleziona...</option>
                  <option <?= $adempimento["DatiTitolo"]["Titolo"]["AttoEsenteRegistrazione"] ==  0 ? 'selected="seleced"' : null?> value="0">atto soggetto a formalit&agrave; registrazione</option>
                  <option <?= $adempimento["DatiTitolo"]["Titolo"]["AttoEsenteRegistrazione"] ==  1 ? 'selected="seleced"' : null?> value="1">atto non soggetto a formalit&agrave; registrazione</option>
                </select>
              </td>
            </tr>
            <tr>
              <td><b>Modalit&agrave; di pagamento: *</b></td>
              <td>
                <select name="adempimento[DatiTitolo][Titolo][ModalitaPagamentoCorr]" rel="S;1;0;A" title="Modalit&agrave; di pagamento">
                  <option value="">Seleziona...</option>
                  <option <?= $adempimento["DatiTitolo"]["Titolo"]["ModalitaPagamentoCorr"] ==  'C' ? 'selected="seleced"' : null?>  value="C">Contante</option>
            			<option <?= $adempimento["DatiTitolo"]["Titolo"]["ModalitaPagamentoCorr"] ==  'A' ? 'selected="seleced"' : null?>  value="A">Assegno</option>
            			<option <?= $adempimento["DatiTitolo"]["Titolo"]["ModalitaPagamentoCorr"] ==  'B' ? 'selected="seleced"' : null?>  value="B">Bonifico</option>
            			<option <?= $adempimento["DatiTitolo"]["Titolo"]["ModalitaPagamentoCorr"] ==  'M' ? 'selected="seleced"' : null?>  value="M">Pagamento eseguito con modalit&agrave;  miste (contante, assegno,bonifico)</option>
            			<option <?= $adempimento["DatiTitolo"]["Titolo"]["ModalitaPagamentoCorr"] ==  'D' ? 'selected="seleced"' : null?>  value="D">Pagamento eseguito con modalit&agrave;  diverse dalle precedenti</option>
                </select>
              </td>
              <td colspan="2"></td>
            </tr>
          </table>
        </div>
        <br><h3>Opertatore Economico</h3>
        <div class="box">
          <?
          $ris_oe = $pdo->bindAndExec("SELECT b_contraenti.*, r_contratti_contraenti.codice_contratto FROM b_contraenti JOIN r_contratti_contraenti ON r_contratti_contraenti.codice_contraente = b_contraenti.codice WHERE b_contraenti.tipologia = :tipologia AND r_contratti_contraenti.codice_contratto = :codice_contratto AND (r_contratti_contraenti.codice_capogruppo = 0 OR r_contratti_contraenti.codice_capogruppo IS NULL) LIMIT 0,1", array(':tipologia' => "oe", ':codice_contratto' => $codice));
          if($ris_oe->rowCount() > 0) {
            $rec_oe = $ris_oe->fetch(PDO::FETCH_ASSOC);
            ?>
            <h3>
              <b><?= $rec_oe["denominazione"] ?></b> - <?= ucwords(strtolower(html_entity_decode($rec_oe["titolo"] . " " . $rec_oe["nome"] . " " . $rec_oe["cognome"], ENT_QUOTES, 'UTF-8'))) ?>
            </h3>
            <table width="100%">
    					<tr>
    						<td class="etichetta" colspan="4">
    							<b>Informazioni Domicilio/Residenza</b>
    						</td>
    					</tr>
    					<tr>
                <td class="etichetta">Tipologia: *</td>
                <td colspan="3">
                  <select title="Tipologia" name="datisoggetto[<?= $rec_oe["codice"] ?>][TipoDomicilio]" rel="S;1;0;A">
                    <option value="">Seleziona...</option>
                    <option <?= (isset($datisoggetto[$rec_oe["codice"]]["TipoDomicilio"]) && $datisoggetto[$rec_oe["codice"]]["TipoDomicilio"] == 0) ? 'selected="selected"' : null ?> value="0">Residenza</option>
               	   	<option <?= (isset($datisoggetto[$rec_oe["codice"]]["TipoDomicilio"]) && $datisoggetto[$rec_oe["codice"]]["TipoDomicilio"] == 1) ? 'selected="selected"' : null ?> value="1">Domicilio</option>
                  </select>
                </td>
    					</tr>
    					<tr>
    						<td class="etichetta">Comune: *</td>
    						<td><input type="text" name="datisoggetto[<?= $rec_oe["codice"] ?>][Comune]" id="Comune<?= $rec_oe["codice"] ?>" value="<?= !empty($datisoggetto[$rec_oe["codice"]]["Comune"]) ? $datisoggetto[$rec_oe["codice"]]["Comune"] : null ?>" rel="S;1;0;A" title="Comune"></td>
                <td class="etichetta">Codice Comune: *</td>
    						<td><input type="text" name="datisoggetto[<?= $rec_oe["codice"] ?>][CodiceComune]" id="CodiceComune<?= $rec_oe["codice"] ?>" value="<?= !empty($datisoggetto[$rec_oe["codice"]]["CodiceComune"]) ? $datisoggetto[$rec_oe["codice"]]["CodiceComune"] : null ?>" rel="S;1;0;A" title="Codice comune"></td>
    					</tr>
    				</table>
            <script type="text/javascript">
    					$('#Comune<?= $rec_oe["codice"] ?>').autocomplete({
    						source: function(request, response) {
    							$.ajax({
    							url: "/contratti/comuni.php",
    							dataType: "json",
    							data: {
    								term : request.term,
    							},
    							success: function(data) {
    								response(data);
    							}
    							});
    						},
    						minLenght: 3,
    						search  : function(){$(this).addClass('working');},
    						open    : function(){$(this).removeClass('working');},
    						select: function(e, result) {
    							//e.preventDefault() // <--- Prevent the value from being inserted.
    							$("#CodiceComune<?= $rec_oe["codice"] ?>").val(result.item.codice_comune_stipula);
    							$(this).focus();
    						},
    						focus: function(e, result) {
    							//e.preventDefault() // <--- Prevent the value from being inserted.
    							$("#CodiceComune<?= $rec_oe["codice"] ?>").val(result.item.codice_comune_stipula);
    						}
    					}).data("ui-autocomplete")._renderItem = function( ul, item ) {
    						return $( "<li id='val"+ item.value +"'>" ).append("<a><strong>" + item.value + "</strong> - " + item.label).appendTo( ul );
    					}
    				</script>
            <?
          }
          ?>
        </div>
        <br><h3>Organo di Rappresentanza</h3>
        <div class="box">
          <h3>
            <b><?= $_SESSION["ente"]["denominazione"] ?></b> - <?= $_SESSION["ente"]["cf"] ?> - <?= $_SESSION["ente"]["citta"] ?> (<?= $_SESSION["ente"]["provincia"] ?>)
          </h3>
          <table style="width:100%;">
            <tr>
              <td style="width:25%;"><b>Nome: *</b></td>
              <td style="width:25%;"><input type="text" name="adempimento[Amministrazione][SoggettoF][Nome]" value="<?= $adempimento["Amministrazione"]["SoggettoF"]["Nome"] ?>" rel="S;1;0;A" title="Nome"></td>
              <td style="width:25%;"><b>Cognome: *</b></td>
              <td style="width:25%;"><input type="text" name="adempimento[Amministrazione][SoggettoF][Cognome]" value="<?= $adempimento["Amministrazione"]["SoggettoF"]["Cognome"] ?>" rel="S;1;0;A" title="Cognome"></td>
            </tr>
            <tr>
              <td style="width:25%;"><b>C.F.: *</b></td>
              <td style="width:25%;"><input type="text" name="adempimento[Amministrazione][SoggettoF][CodiceFiscale]" value="<?= $adempimento["Amministrazione"]["SoggettoF"]["CodiceFiscale"] ?>" rel="S;16;16;A" title="Codice Fiscale"></td>
              <td style="width:25%;"><b>Data di Nascita: </b></td>
              <td style="width:25%;"><input type="text" class="datepick" name="adempimento[Amministrazione][SoggettoF][DataNascita]" value="<?= $adempimento["Amministrazione"]["SoggettoF"]["DataNascita"] ?>" rel="S;10;10;D" title="Data di Nascita"></td>
            </tr>
            <tr>
              <td class="etichetta" colspan="4">
                <b>Informazioni domicilio/residenza</b>
              </td>
            </tr>
            <tr>
              <td class="etichetta">Tipologia: *</td>
              <td colspan="3">
                <select title="Tipologia" name="datisoggetto[<?= $rec_ore["codice"] ?>][TipoDomicilio]" rel="S;1;0;A">
                  <option value="">Seleziona...</option>
                  <option <?= (isset($datisoggetto[$rec_ore["codice"]]["TipoDomicilio"]) && $datisoggetto[$rec_ore["codice"]]["TipoDomicilio"] == 0) ? 'selected="selected"' : null ?> value="0">Residenza</option>
                  <option <?= (isset($datisoggetto[$rec_ore["codice"]]["TipoDomicilio"]) && $datisoggetto[$rec_ore["codice"]]["TipoDomicilio"] == 1) ? 'selected="selected"' : null ?> value="1">Domicilio</option>
                </select>
              </td>
            </tr>
            <tr>
              <td class="etichetta">Comune: *</td>
              <td><input type="text" name="datisoggetto[<?= $rec_ore["codice"] ?>][Comune]" id="Comune<?= $rec_ore["codice"] ?>" value="<?= !empty($datisoggetto[$rec_ore["codice"]]["Comune"]) ? $datisoggetto[$rec_ore["codice"]]["Comune"] : null ?>" rel="S;1;0;A" title="Comune"></td>
              <td class="etichetta">Codice Comune: *</td>
              <td><input type="text" name="datisoggetto[<?= $rec_ore["codice"] ?>][CodiceComune]" id="CodiceComune<?= $rec_ore["codice"] ?>" value="<?= !empty($datisoggetto[$rec_ore["codice"]]["CodiceComune"]) ? $datisoggetto[$rec_ore["codice"]]["CodiceComune"] : null ?>" rel="S;1;0;A" title="Codice comune"></td>
            </tr>
          </table>
          <script type="text/javascript">
            $('#Comune<?= $rec_ore["codice"] ?>').autocomplete({
              source: function(request, response) {
                $.ajax({
                url: "/contratti/comuni.php",
                dataType: "json",
                data: {
                  term : request.term,
                },
                success: function(data) {
                  response(data);
                }
                });
              },
              minLenght: 3,
              search  : function(){$(this).addClass('working');},
              open    : function(){$(this).removeClass('working');},
              select: function(e, result) {
                //e.preventDefault() // <--- Prevent the value from being inserted.
                $("#CodiceComune<?= $rec_ore["codice"] ?>").val(result.item.codice_comune_stipula);
                $(this).focus();
              },
              focus: function(e, result) {
                //e.preventDefault() // <--- Prevent the value from being inserted.
                $("#CodiceComune<?= $rec_ore["codice"] ?>").val(result.item.codice_comune_stipula);
              }
            }).data("ui-autocomplete")._renderItem = function( ul, item ) {
              return $( "<li id='val"+ item.value +"'>" ).append("<a><strong>" + item.value + "</strong> - " + item.label).appendTo( ul );
            }
          </script>
        </div>
        <br><h3>Dati del negozio</h3>
        <div class="box">
          <table style="width:100%;">
            <tr>
              <td style="width:25%;"><b>Valore: *</b></td>
              <td style="width:25%;"><input type="text" name="adempimento[DatiNegozio][ValoreNegozio]" value="<?= $adempimento["DatiNegozio"]["ValoreNegozio"] ?>" rel="S;0;13;N" title="Valore del Negozio"></td>
              <td style="width:25%;"><b>Codice del Negozio: *</b></td>
              <td style="width:25%;">7003 - APPALTO</td>
            </tr>
            <tr>
              <td style="width:25%;"><b>Garanzia per debito non proprio: *</b></td>
              <td style="width:25%;">
                <select title="Garanzia per debito non proprio" name="adempimento[DatiNegozio][GaranziaPerDebitoNonProprio]" rel="S;1;0;A">
                  <option value="">Seleziona...</option>
                  <option <?= $adempimento["DatiNegozio"]["GaranziaPerDebitoNonProprio"] == 0 ? 'selected="selected"' : null ?> value="0">Garanzia non a favore di terzi</option>
             	   	<option <?= $adempimento["DatiNegozio"]["GaranziaPerDebitoNonProprio"] == 1 ? 'selected="selected"' : null ?> value="1">Garanzia a favore di terzi</option>
                </select>
              </td>
              <td><b>Agevolazione: *</b></td>
              <td>
                <select title="Garanzia per debito non proprio" name="adempimento[DatiNegozio][Agevolazione]" rel="S;1;0;A">
                  <option value="">Seleziona...</option>
                  <option <?= $adempimento["DatiNegozio"]["Agevolazione"] == 0 ? 'selected="selected"' : null ?> value="0">assenza di agevolazioni</option>
              		<!-- <option value="1">agevolazione prima casa</option> -->
              		<!-- <option value="2">piccola proprieta' contadina</option> -->
              		<!-- <option value="4">trasferimento a favore di imprese immobiliari</option> -->
              		<!-- <option value="5">trasferimento a favore di imprenditore agricolo</option> -->
              		<!-- <option value="6">immobile di interesse storico-artistico</option> -->
              		<!-- <option value="8">trasferimento a favore di cooperative</option> -->
              		<!-- <option value="9">compravendita a favore di giovani agricoltori</option> -->
              		<!-- <option value="10">trasferimento territori montani</option> -->
              		<!-- <option value="11">edilizia economico - popolare</option> -->
              		<!-- <option value="12">assegnazione alloggi a soci di cooperative edilizie</option> -->
              		<!-- <option value="13">piani urbanistici particolareggiati</option> -->
              		<!-- <option value="14">finanziamenti esenti (DPR 601/73 art.15)</option> -->
              		<!-- <option value="16">prima casa con residenza da acquisire</option> -->
               		<!-- <option value="17">pertinenza prima casa</option> -->
              		<!-- <option value="18">trasferimento a favore di enti pubblici o comunita'  montane</option> -->
              		<!-- <option value="19">trasferimento a favore di ONLUS</option> -->
              		<!-- <option value="20">trasferimento a favore di IPAB</option> -->
              		<!-- <option value="21">donazione a favore di enti pubblici, fondazioni o associazioni (art.3, comma 1. D.LGS n.346, 1990)</option>
              		<option value="22">donazione a favore di enti pubblici, fondazioni o associazioni (art.3, comma 2. D.LGS n.346, 1990)</option>
              		<option value="23">trasferimento a favore di cooperative per case economiche e popolari</option>
              		<option value="24">trasferimento a favore di cooperative sociali</option>
              		<option value="25">compendio unico</option>
              		<option value="26">trasferimento fondi a favore di cooperative e societa'  forestali</option>
              		<option value="27">trasferimento a favore di aziende agricole montane</option>
              		<option value="28">trasferimento a favore di imprenditore agricolo professionale</option>
              		<option value="29">piani di recupero urbanistici</option>
              		<option value="30">trasferimento di immobile dello Stato, enti pubblici, regioni, enti locali  a favore di fondi comuni di investimento</option>
              		<option value="31">trasferimento a favore dello Stato</option>
              		<option value="32">trasferimento a seguito di divorzio o separazione</option>
              		<option value="33">mutuo acquisto prima casa</option>
              		<option value="34">mutuo costruzione/ristrutturazione prima casa</option>
              		<option value="35">cessione di immobile strumentale (art.35, comma 10-ter DL n.223/2006)</option>
              		<option value="36">trasferimenti  ex art.1, co.78, L.296/2006</option>
              		<option value="37">assegnazione a soci (art. 1, comma 116, L. n.296/2006)</option> -->
              		<option <?= $adempimento["DatiNegozio"]["Agevolazione"] == 99 ? 'selected="selected"' : null ?> value="99">agevolazione di altro tipo</option>
                </select>
              </td>
            </tr>
            <tr>
              <td style="width:25%;"><b>Tassazione: *</b></td>
              <td style="width:25%;">
                <select title="Garanzia per debito non proprio" name="adempimento[DatiNegozio][Esente]" rel="S;1;0;A">
                  <option value="">Seleziona...</option>
                  <option <?= $adempimento["DatiNegozio"]["Esente"] == 0 ? 'selected="selected"' : null ?> value="0">negozio esente dalle imposte dovute per la registrazione</option>
             	   	<option <?= $adempimento["DatiNegozio"]["Esente"] == 1 ? 'selected="selected"' : null ?> value="1">negozio non esente</option>
                </select>
              </td>
              <td style="width:25%;"><b>Soggetto a IVA: *</b></td>
              <td style="width:25%;">
                <select title="Garanzia per debito non proprio" name="adempimento[DatiNegozio][SoggettoIVA]" rel="S;1;0;A">
                  <option value="">Seleziona...</option>
                  <option <?= $adempimento["DatiNegozio"]["SoggettoIVA"] == 1 ? 'selected="selected"' : null ?> value="1">negozio soggetto ad IVA</option>
              		<option <?= $adempimento["DatiNegozio"]["SoggettoIVA"] == 0 ? 'selected="selected"' : null ?> value="0">negozio non soggetto ad IVA</option>
                </select>
              </td>
            </tr>
            <tr>
              <td style="width:25%;"><b>Effetti Sospesi: *</b></td>
              <td style="width:25%;">
                <select title="Garanzia per debito non proprio" name="adempimento[DatiNegozio][EffettiSospesi]" rel="S;1;0;A">
                  <option value="">Seleziona...</option>
                  <option <?= $adempimento["DatiNegozio"]["EffettiSospesi"] == 1 ? 'selected="selected"' : null ?> value="1">presenza di condizioni sospensive</option>
              		<option <?= $adempimento["DatiNegozio"]["EffettiSospesi"] == 0 ? 'selected="selected"' : null ?> value="0">assenza di condizioni sospensive</option>
                </select>
              </td>
              <td colspan="2"></td>
            </tr>
          </table>
        </div>
        <br><h3>IMPOSTA REGISTRO ATTI CONTR. VERB. E DENUNCE:</h3>
        <div class="box">
          <table style="width:100%;">
            <tr>
              <td style="width:25%"><b>Importo: *</b></td>
              <td style="width:25%"><input type="text" name="adempimento[DatiNegozio][Negozio][Tassazione][9814][Importo]" value="<?= $adempimento["DatiNegozio"]["Negozio"]["Tassazione"]["9814"]["Importo"] ?>" title="Importo Tributo 9814" rel="N;0;0;N"></td>
              <td style="width:25%"><b>Aliquota: *</b></td>
              <td style="width:25%"><input type="text" name="adempimento[DatiNegozio][Negozio][Tassazione][9814][Aliquota]" value="<?= $adempimento["DatiNegozio"]["Negozio"]["Tassazione"]["9814"]["Aliquota"] ?>" title="Aliquota Tributo 9814" rel="N;0;0;N"></td>
            </tr>
          </table>
        </div>
        <br><h3>IMPOSTA DI BOLLO VERSATA DA UFFICIALI ROGANTI:</h3>
        <div class="box">
          <table style="width:100%;">
            <tr>
              <td style="width:25%"><b>Importo: *</b></td>
              <td style="width:25%"><input type="text" name="adempimento[DatiNegozio][Negozio][Tassazione][9802][Importo]" value="<?= $adempimento["DatiNegozio"]["Negozio"]["Tassazione"]["9802"]["Importo"] ?>" title="Importo Tributo 9802" rel="N;0;0;N"></td>
              <td style="width:25%"><b>Aliquota: *</b></td>
              <td style="width:25%"><input type="text" name="adempimento[DatiNegozio][Negozio][Tassazione][9802][Aliquota]" value="<?= $adempimento["DatiNegozio"]["Negozio"]["Tassazione"]["9802"]["Aliquota"] ?>" title="Aliquota Tributo 9802" rel="N;0;0;N"></td>
            </tr>
          </table>
        </div>
        <br><h3>Testo Atto</h3>
        <div class="box">
          <textarea name="adempimento[TestoAtto][TestoLibero]" class="ckeditor_simple" rel="N;0;0;A" title="Testo Atto"><?= $adempimento["TestoAtto"]["TestoLibero"] ?></textarea>
        </div>
        <input type="submit" class="submit_big" value="Salva">
        <!-- <input type="submit" class="submit_big" value="Salva e Scarica"> -->
      </form>
      <script type="text/javascript">
        $(document).ready(function() {
          $('select').trigger('chosen:updated');
        });
      </script>
      <?
    }
  }
  include_once $root . '/contratti/ritorna_pannello_contratto.php';
	include_once $root."/layout/bottom.php";
  die();
?>
