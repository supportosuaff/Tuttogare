<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	if(empty($_GET["codice"]) || empty($_SESSION["codice_utente"]) || !isset($_SESSION["ente"]) || !check_permessi("contratti",$_SESSION["codice_utente"])) {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	} else {
		$codice = $_GET["codice"];
		$codice_gara = !empty($_GET["codice_gara"]) ? $_GET["codice_gara"] : null;

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
			$rec_ore = get_campi('b_contraenti');
			$rec_ore["codice"] = 0;

			$sql_ore = "SELECT b_contraenti.* FROM b_contraenti ";
			$sql_ore .= "JOIN r_contratti_contraenti ON r_contratti_contraenti.codice_contraente = b_contraenti.codice ";
			$sql_ore .= "WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia = 'ore' ";
			$ris_ufficiale = $pdo->bindAndExec($sql_ore, array(':codice_contratto' => $rec_contratto["codice"]));
			if($ris_ufficiale->rowCount() > 0) {
					$rec_ore = $ris_ufficiale->fetch(PDO::FETCH_ASSOC);
			}

      $lock = FALSE;
      if(!empty($rec_contratto["codice"])) {
        $oe = $ore = 0;
        $oe = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "oe" GROUP BY b_contraenti.codice', array(':codice_contratto' => $rec_contratto["codice"]))->rowCount();
        $ore = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "ore" GROUP BY b_contraenti.codice', array(':codice_contratto' => $rec_contratto["codice"]))->rowCount();
        if($oe > 0 && $ore == 1) {
          $bind = array(':codice' => $rec_contratto["codice"], ':tipo' => 'contratto', ':sezione' => 'contratti');
          $ris = $pdo->bindAndExec("SELECT b_documentale.codice FROM b_documentale WHERE b_documentale.tipo = :tipo AND b_documentale.sezione = :sezione AND b_documentale.codice_gara = :codice AND codice_allegato > 0", $bind);
          if($ris->rowCount() > 0) {
            $lock = false;
          } else {
            $bind = array(':codice_contratto' => $rec_contratto["codice"], ':codice_ente' => $_SESSION["ente"]["codice"]);
            $ris_documento = $pdo->bindAndExec("SELECT b_allegati.* FROM b_allegati WHERE sezione = 'contratti' AND codice_gara = :codice_contratto AND cartella = 'contratti_firmati' AND online = 'N' AND hidden = 'N' AND codice_ente = :codice_ente", $bind);
            if($ris_documento->rowCount() > 0) $lock = false;
          }
        }
      }

			?>
      <link rel="stylesheet" href="/contratti/css.css" media="screen">
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
			<h1>Gestione delle parti</h1>
      <? if(!$lock) { ?><form action="save.php" method="POST" rel="validate" name="gestione_parti"><? } ?>
        <? if(!$lock) { ?><div class="comandi"><button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button></div><? } ?>
        <div id="tabs">
        	<ul>
            <li><a href="#organo_rappresentanza_esterna">Organo di rappresentanza esterna dell'&#39;amministrazione</a></li>
          	<li><a href="#operatore_economico">Operatori Economici</a></li>
          </ul>
    			<div id="organo_rappresentanza_esterna">
            <?
            $ris = $pdo->bindAndExec('SELECT * FROM b_contraenti WHERE codice_gestore = :codice_gestore AND tipologia = "ore"', array(':codice_gestore' => $_SESSION["ente"]["codice"]));
            if($ris->rowCount() > 0) {
              ?>
              <table width="100%" style="margin-bottom:25px; border:solid 1px #999;">
                <tr>
                  <td colspan="2" class="etichetta">
                    <b>&Egrave; possibile selezionare l&#39;organo di rappresentanza esterna dall&#39;elenco per compilare automaticamente i dati sottostanti con le informazioni gi&agrave; presenti nel sitema.</b>
                  </td>
                </tr>
                <tr>
                  <td class="etichetta"><i class="fa fa-search"></i> Ricerca ufficiale rogante:</td>
                  <!-- <td><input type="text" name="ufficiale_rogante" title="Ricerca un ufficiale rogante gi&agrave; presente nella piattaforma digitanto il nome, il cognome o il C.F." value=""></td> -->
                  <td>
                    <select id="ricerca_ufficiale_rogante" onchange="fill_ore($(this).val())" title="Ricerca ufficiale Rogante">
                      <option	selected="selected" value="">Nuovo inserimento</option>
                      <?
                      while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
                        ?><option value="<?= $rec["codice"] ?>"><?= ucwords(strtolower(html_entity_decode($rec["titolo"]  . " " . $rec["nome"] . " " . $rec["cognome"], ENT_QUOTES, 'UTF-8'))) ?></option><?
                      }
                      ?>
                    </select>
                  </td>
                </tr>
              </table>
              <?
            }
            ?>
            <input type="hidden" name="codice_ente" value="<?= $rec_contratto["codice_ente"]; ?>">
            <input type="hidden" name="codice_contratto" value="<?= $rec_contratto["codice"]; ?>">
            <input type="hidden" name="ore[codice_organo]" id="codice_organo" value="<?= $rec_ore["codice"] ?>">
            <table width="100%">
              <tbody>
                <tr>
                  <td class="etichetta">
                    Titolo: *
                  </td>
                  <td>
                    <select name="ore[titolo]" rel="S;1;0;A" title="Titolo" id="titolo_ore" data-input="#titolo_ore_altro" onchange="check_altro($(this))">
                      <option value="">Seleziona..</option>
                      <?
                      $ris = $pdo->bindAndExec("SELECT DISTINCT b_contraenti.titolo FROM b_contraenti WHERE codice_gestore = :codice_gestore AND b_contraenti.tipologia = 'ore'", array(':codice_gestore' => $_SESSION["ente"]["codice"]));
                      if($ris->rowCount() > 0) {
                        while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
                          ?>
                          <option <?= strtolower(html_entity_decode($rec_ore["titolo"], ENT_QUOTES, 'UTF-8')) == strtolower(html_entity_decode($rec["titolo"], ENT_QUOTES, 'UTF-8')) ? 'selected="seleceted"' : null  ?> value="<?= strtolower(html_entity_decode($rec["titolo"], ENT_QUOTES, 'UTF-8')) ?>"><?= ucfirst(strtolower(html_entity_decode($rec["titolo"], ENT_QUOTES, 'UTF-8'))) ?></option>
                          <?
                        }
                      }
                      ?>
                      <option value="altro">Altro titolo</option>
                    </select>
                    <input type="text" style="display:none" disabled="disabled" rel="N;0;0;A" title="Altro titolo" name="ore[titolo_altro]" id="titolo_ore_altro">
                  </td>
                  <td class="etichetta">
                    Ruolo: *
                  </td>
                  <td>
                    <select name="ore[ruolo]" rel="S;1;0;A" title="Ruolo" id="ruolo_ore">
                      <option value="">Seleziona..</option>
                      <option <?= $rec_ore["ruolo"] == "dirigente" ? 'selected="selected"' : null ?> value="dirigente">Dirigente</option>
											<option <?= $rec_ore["ruolo"] == "segretario-generale" ? 'selected="selected"' : null ?> value="segretario-generale">Segretario Generale</option>
											<option <?= $rec_ore["ruolo"] == "rettore" ? 'selected="selected"' : null ?> value="rettore">Rettore</option>
											<option <?= $rec_ore["ruolo"] == "pro-Rettore" ? 'selected="selected"' : null ?> value="pro-Rettore">Pro-Rettore</option>
                      <option <?= $rec_ore["ruolo"] == "direttore-Generale" ? 'selected="selected"' : null ?> value="direttore-Generale">Direttore Generale</option>
                      <option <?= $rec_ore["ruolo"] == "amministratore-delegato" ? 'selected="selected"' : null ?> value="amministratore-delegato">Amministratore Delegato</option>
                      <option <?= $rec_ore["ruolo"] == "responsabile-funzione" ? 'selected="selected"' : null ?> value="responsabile-funzione">Responsabile Funzione</option>
                      <option <?= $rec_ore["ruolo"] == "altro" ? 'selected="selected"' : null ?> value="altro">Altro</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="etichetta">Nome: *</td>
                  <td><input type="text" id="nome" name="ore[nome]" value="<?= $rec_ore["nome"] ?>" rel="S;1;0;A" title="Nome"></td>
                  <td class="etichetta">Cognome: *</td>
                  <td><input type="text" id="cognome" name="ore[cognome]" value="<?= $rec_ore["cognome"] ?>" rel="S;1;0;A" title="Cognome"></td>
                </tr>
                <tr>
                  <td class="etichetta">Data di nascita:</td>
                  <td><input type="text" class="datepick" id="data_nascita" name="ore[data_nascita]" value="<?= mysql2date($rec_ore["data_nascita"]) ?>" rel="S;10;10;D" title="Data di nascita"></td>
                  <td class="etichetta">Comune di nascita:</td>
                  <td><input type="text" id="comune_nascita" name="ore[comune_nascita]" value="<?= $rec_ore["comune_nascita"] ?>" rel="S;1;0;A" title="Comune di nascita"></td>
                </tr>
                <tr>
                  <td class="etichetta">Provincia di nascita	:</td>
                  <td><input type="text" id="provincia_nascita" name="ore[provincia_nascita]" value="<?= $rec_ore["provincia_nascita"] ?>" rel="N;1;0;A" title="Provincia di nascita	"></td>
                  <td class="etichetta">Codice Fiscale:</td>
                  <td><input type="text" id="cf" name="ore[cf]" value="<?= $rec_ore["cf"] ?>" rel="N;1;0;A" title="Codice Fiscale"></td>
                </tr>
              </tbody>
            </table>
            <div class="clear"></div>
    				<a class="precedente" style="float:left" href="#">Step precedente</a>
    				<a class="successivo" style="float:right" href="#">Step successivo</a>
    				<div class="clear"></div>
    			</div>
          <div id="operatore_economico">
            <div id="partecipanti">
							<?
							$bind = array(':tipologia' => "oe", ':codice_contratto' => $codice);
							$ris = $pdo->bindAndExec("SELECT b_contraenti.*, r_contratti_contraenti.codice_contratto FROM b_contraenti JOIN r_contratti_contraenti ON r_contratti_contraenti.codice_contraente = b_contraenti.codice WHERE b_contraenti.tipologia = :tipologia AND r_contratti_contraenti.codice_contratto = :codice_contratto AND (r_contratti_contraenti.codice_capogruppo = 0 OR r_contratti_contraenti.codice_capogruppo IS NULL)", $bind);
							if($ris->rowCount() > 0) {
								while ($record_operatore = $ris->fetch(PDO::FETCH_ASSOC)) {
									include 'capogruppo.php';
								}
							} else if (!empty($rec_contratto["codice_gara"])) {
                $sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND primo = 'S' ";
                $rec_contratto["codice_lotto"] = empty($rec_contratto["codice_lotto"]) ? 0 : $rec_contratto["codice_lotto"];
                $ris = $pdo->go($sql,[":codice_gara"=>$rec_contratto["codice_gara"],":codice_lotto"=>$rec_contratto["codice_lotto"]]);
                if ($ris->rowCount() > 0) {
                  $partecipante = $ris->fetch(PDO::FETCH_ASSOC);
                  $codice_utente_gara = $partecipante["codice_utente"];
                  ob_start();
                  $include = true;
                  include_once("operatori.php");
                  ob_clean();
                  if (isset($tmp)) {
                    $record_operatore = [];
                    $record_operatore["codice"] = "i_0";
                    $record_operatore["titolo"] = "";
                    $mandatario = (!empty($partecipante["tipo"])) ? TRUE : FALSE;
                    $ris_mandante = $pdo->go("SELECT partita_iva, ragione_sociale AS denominazione, '' AS sede FROM r_partecipanti WHERE codice_capogruppo = :codice_partecipante ",[":codice_partecipante"=>$partecipante["codice"]]);
                    $record_operatore["codice_contratto"] = $rec_contratto["codice"];
                    $record_operatore["denominazione"] = $tmp["ragione_sociale"];
                    $record_operatore["partita_iva"] = $partecipante["partita_iva"];
                    $record_operatore["codice_operatore"] = $tmp["codice_operatore"];
                    $record_operatore["codice_utente"] = $tmp["codice_utente"];
                    $record_operatore["sede"] = $tmp["sede_partecipante"];
                    $record_operatore["nome"] = $tmp["nome_partecipante"];
                    $record_operatore["cognome"] = $tmp["cognome_partecipante"];
                    $record_operatore["data_nascita"] = $tmp["data_nascita_partecipante"];
                    $record_operatore["comune_nascita"] = $tmp["comune_nascita_partecipante"];
                    $record_operatore["provincia_nascita"] = $tmp["provincia_nascita_partecipante"];
                    $record_operatore["cf"] = $tmp["cf_partecipante"];
                    $record_operatore["indirizzo_residenza"] = $tmp["indirizzo_residenza_partecipante"];
                    $record_operatore["comune_residenza"] = $tmp["comune_residenza_partecipante"];
                    $record_operatore["provincia_residenza"] = $tmp["provincia_residenza_partecipante"];
                    $record_operatore["ruolo"] = $tmp["ruolo_ore"];
                    include 'capogruppo.php';
                  }
                }
              }
							?>
            </div>
            <button type="button" class="button button-highlight button-block" style="width:100%;" onClick="aggiungi('capogruppo.php?codice_contratto=<?= $codice ?>','#partecipanti');return false;">Aggiungi operatore</button>
            <div class="clear"><br></div>
    				<a class="precedente" style="float:left" href="#">Step precedente</a>
    				<a class="successivo" style="float:right" href="#">Step successivo</a>
    				<div class="clear"></div>
    			</div>
        </div>
        <? if(!$lock) { ?><input type="submit" class="submit_big" value="Salva"><? } ?>
      <? if(!$lock) { ?></form><? } ?>
      <script type="text/javascript">
        function change_tipo(btn, tbl, val) {
          $(btn).hide();
          if(val == "04-CAPOGRUPPO") {
            $(btn).show();
          } else {
						$(tbl).find('.membro_di_raggruppamento').each(function(index, el) {
							el.remove();
						});
					}
        }
        $("#tabs").tabs();
        function check_altro(element) {
          if(element.data('input').length > 0) {
            var inp = $(element.data('input'));
            inp.prop({'disabled':'disabled', 'rel':'N;0;0;A'});
            inp.attr({'disabled':'disabled', 'rel':'N;0;0;A'});
            if(inp.is(':visible')) {
              inp.slideUp('fast');
              inp.blur();
            }
            if(element.val() == "altro") {
              inp.removeAttr('disabled');
              inp.removeProp('disabled');
              inp.prop({'rel':'S;1;0;A'});
              inp.attr({'rel':'S;1;0;A'});
              inp.slideDown('fast');
            }
          }
        }
        function fill_ore(codice_ore) {
          if(codice_ore.length > 0) {
            $.ajax({
              url: 'get_info_organo_rappresentanza.php',
              type: 'POST',
              dataType: 'json',
              data: {codice: codice_ore},
              beforeSend: function(e) {
                $('#wait').fadeIn('fast');
              }
            })
            .done(function(result) {
              var key = ['codice_organo', 'titolo_ore','ruolo_ore','nome','cognome','data_nascita','comune_nascita','provincia_nascita','cf'];
              $.each(key, function(index, name) {
                if(result.hasOwnProperty(name)) {
                  $('#'+name).val(result[name]);
                  if($('#'+name).is('select')) $('#'+name).trigger('chosen:updated');
                }
              });
            })
            .fail(function() {
              $('#ricerca_ufficiale_rogante').val('').trigger('chosen:updated');
              jalert('<h2>Non è stato possibile caricare le informazioni. Si prega di riprovare</h2>');
            })
            .always(function() {
              $('#wait').fadeOut('fast');
            });
          }
        }
        function reset_form(name) {
          $('form[name='+name+']').find(':input').not(':input[type="submit"], :input[type="button"], :input[type="reset"], :input[name="codice_contratto"], :input[name="codice_ente"]').val('');
          $('form[name='+name+']').find('select').val('').trigger('chosen:updated');
        }
        $(".precedente").each(function() {
    			var id_parent = $("#tabs").children("div").index($(this).parent("div"));
    			if (id_parent == 0) {
    				$(this).remove();
    			} else {
    				var target = id_parent - 1;
    				$(this).click(function() { $('#tabs').tabs('option','active',target) });
    			}
    		});
    		$(".successivo").each(function() {
    			var id_parent = $("#tabs").children("div").index($(this).parent("div"));
    			if (id_parent == ($("#tabs").children("div").length - 1)) {
    				$(this).remove();
    			} else {
    				var target = id_parent + 1;
    				$(this).click(function() { $('#tabs').tabs('option','active',target) });
    			}
    		});
        <? if($lock) { ?>
          $(':input').not('.submit_big').attr('disabled', true).prop('disabled', true);
        <? } ?>
      </script>
			<?
		} else {
			?>
			<h2 class="ui-state-error">Si è verificato un errore nella lettura delle informazioni. Si prega di riprovare o se il problema persiste di contattare l'amministratore</h2>
			<?
		}
	}
	include_once($root . "/contratti/ritorna_pannello_contratto.php");
	include_once($root."/layout/bottom.php");
?>
