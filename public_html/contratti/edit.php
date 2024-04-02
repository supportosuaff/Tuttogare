<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
  if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
		if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$edit = check_permessi("contratti",$_SESSION["codice_utente"]);
		}
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
  } else {
  	echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
  	die();
  }
  $codice = !empty($_GET["codice"]) ? $_GET["codice"] : 0;
  $codice_gara = !empty($_GET["codice_gara"]) ? $_GET["codice_gara"] : null;
  $codice_lotto = !empty($_GET["codice_lotto"]) ? $_GET["codice_lotto"] : null;

  $bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ':codice' => $codice);
  $sql  = "SELECT b_contratti.* FROM b_contratti ";
	if (empty($codice_gara) && $_SESSION["gerarchia"] > 1) {
		$sql .= "JOIN b_permessi_contratti ON b_contratti.codice = b_permessi_contratti.codice_contratto ";
	}
	$sql .= "WHERE b_contratti.codice = :codice AND b_contratti.codice_gestore = :codice_ente ";
  if ($_SESSION["gerarchia"] > 0) {
    $bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
    $sql .= "AND (b_contratti.codice_ente = :codice_ente_utente OR b_contratti.codice_gestore = :codice_ente_utente) ";
  }
	if (empty($codice_gara) && $_SESSION["gerarchia"] > 1) {
		$bind[":codice_utente"] = $_SESSION["codice_utente"];
		$sql .= " AND (b_permessi_contratti.codice_utente = :codice_utente)";
	}
	
  $ris  = $pdo->bindAndExec($sql,$bind);
  if($ris->rowCount() > 0) {
    $rec_contratto = $ris->fetch(PDO::FETCH_ASSOC);
	$codice_lotto = $rec_contratto["codice_lotto"];
    $operazione = "UPDATE";
  } else {
    $lock = false;
    $rec_contratto = get_campi("b_contratti");
		$rec_contratto["anno_stipula"] = date('Y');
		if (!empty($codice_lotto)) {
			$sql = "SELECT b_lotti.*, b_gare.codice_ente,b_gare.tipologia, b_gare.cup FROM b_lotti JOIN b_gare ON b_lotti.codice_gara = b_gare.codice
							WHERE b_lotti.codice = :codice AND b_gare.codice_gestore = :codice_gestore ";
			$ris = $pdo->go($sql,[":codice"=>$codice_lotto,":codice_gestore"=>$_SESSION["ente"]["codice"]]);
			if ($ris->rowCount() > 0) $traccia = $ris->fetch(PDO::FETCH_ASSOC);
		} else if (!empty($codice_gara)) {
			$sql = "SELECT * FROM b_gare 
			WHERE codice = :codice AND b_gare.codice_gestore = :codice_gestore ";
			$ris = $pdo->go($sql,[":codice"=>$codice_gara,":codice_gestore"=>$_SESSION["ente"]["codice"]]);
			if ($ris->rowCount() > 0) $traccia = $ris->fetch(PDO::FETCH_ASSOC);
		}
		if (isset($traccia)) {
			$rec_contratto["tipologia"] = "appalto";
			$rec_contratto["importo_totale"] = $traccia["importoAggiudicazione"];
			$rec_contratto["codice_ente"] = $traccia["codice_ente"];
			$rec_contratto["oggetto"] = $traccia["oggetto"];
			$rec_contratto["descrizione"] = $traccia["descrizione"];
			$rec_contratto["tipologia_appalto"] = $traccia["tipologia"];
			$rec_contratto["n_atto"] = $traccia["numero_atto_esito"];
			$rec_contratto["data_atto"] = $traccia["data_atto_esito"];
			$rec_contratto["cig"] = $traccia["cig"];
			$rec_contratto["cup"] = $traccia["cup"];
		}
    $operazione = "INSERT";
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
        $lock = TRUE;
      } else {
        $bind = array(':codice_contratto' => $rec_contratto["codice"], ':codice_ente' => $_SESSION["ente"]["codice"]);
        $ris_documento = $pdo->bindAndExec("SELECT b_allegati.* FROM b_allegati WHERE sezione = 'contratti' AND codice_gara = :codice_contratto AND cartella = 'contratti_firmati' AND online = 'N' AND hidden = 'N' AND codice_ente = :codice_ente", $bind);
        if($ris_documento->rowCount() > 0) $lock = TRUE;
      }
    }
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
	<h1>INSERIMENTO PRELIMINARE</h1>
	<? if(!$lock) { ?><form name="box" method="post" action="save.php" rel="validate"><? } ?>
		<input type="hidden" name="codice" value="<? echo $codice; ?>">
    <input type="hidden" name="codice_gara" value="<? echo $codice_gara; ?>">
    <input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
    <input type="hidden" name="operazione" value="<? echo $operazione ?>">
    <? if(!$lock) { ?>
      <div class="comandi">
        <button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
      </div>
    <? } ?>
		<div id="tabs">
    	<ul>
      	<li><a href="#generali">Dati generali</a></li>
				<li><a href="#descrizione">Descrizione</a></li>
				<li><a href="#contratti_appalto">Info appalto</a></li>
				<li><a href="#luogo_stipula">Luogo di Stipula</a></li>
      </ul>
			<div id="generali">
				<table width="100%">
					<tr>
						<td class="etichetta">Modalit&agrave; di stipula:</td>
						<td width="20%">
							<select name="modalita_stipula" rel="S;1;0;A" title="Modalit&agrave; di stipula">
								<option value="">Seleziona..</option>
								<?
								$ris = $pdo->query("SELECT * FROM b_conf_modalita_stipula WHERE eliminato = 'N' AND attivo = 'S' ORDER BY etichetta ASC");
								if($ris->rowCount() > 0) {
									while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
										?>
										<option <?= $rec_contratto["modalita_stipula"] == $rec["codice"] ? 'selected="selected"' : null ?> value="<?= $rec["codice"] ?>"><?= $rec["etichetta"] ?></option>
										<?
									}
								}
								?>
							</select>
						</td>
						<td class="etichetta">Tipologia del contratto:</td>
						<td width="20%">
							<select id="tipologia_contratto" name="tipologia" rel="S;1;0;A" title="Tipologia">
								<option value="">Seleziona..</option>
								<?
								$ris = $pdo->query("SHOW COLUMNS FROM b_contratti WHERE Field = 'tipologia'");
								$tipologia = $ris->fetch(PDO::FETCH_ASSOC)["Type"];
								preg_match("/^enum\(\'(.*)\'\)$/", $tipologia, $matches);
								$enum = explode("','", $matches[1]);
								foreach ($enum as $value) {
									?><option <?= $value != "appalto" ? "disabled" : null ?> <?= $rec_contratto["tipologia"] == $value ? 'selected="selected"' : null ?> value="<?= $value ?>"><?= ucfirst($value) ?></option><?
								}
								?>
							</select>
						</td>
						<td class="etichetta">Anno di Stipula:</td>
						<td width="20%"><input class="espandi" type="text" name="anno_stipula" title="Anno di stipula" value="<?= $rec_contratto["anno_stipula"] ?>" rel="S;4;4;N"></td>
					</tr>
					<tr>
						<td class="etichetta">Data inizio:</td>
						<td><input class="espandi datepick_today" type="text" name="data_inizio" title="Data inizio" value="<?= mysql2date($rec_contratto["data_inizio"]) ?>" rel="N;10;10;D"></td>
						<td class="etichetta">Data fine:</td>
						<td><input class="espandi datepick_today" type="text" name="data_fine" title="Data fine" value="<?= mysql2date($rec_contratto["data_fine"]) ?>" rel="N;10;10;D"></td>
						<td class="etichetta">Promemoria scadenza:</td>
						<td><input class="espandi datepick_today" type="text" name="promemoria" title="Promemoria scadenza" value="<?= mysql2date($rec_contratto["promemoria"]) ?>" rel="N;10;10;D"></td>
					</tr>
					<?
					$bind = array();
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$sql  = "SELECT * FROM b_enti WHERE ((codice = :codice_ente) ";
					$sql .= " OR (sua = :codice_ente)) ";
					if ($_SESSION["gerarchia"] > 0) {
						$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
						$sql .= " AND ((codice = :codice_ente_utente) ";
						$sql .= " OR (sua = :codice_ente_utente))";
					}
					$sql .= "ORDER BY denominazione ";
					$ris = $pdo->bindAndExec($sql,$bind);
					if($ris->rowCount() > 1) {
						?>
						<tr>
							<td class="etichetta">Ente beneficiario:</td>
							<td colspan="5">
							<select class="espandi" name="codice_ente" id="codice_ente" rel="S;0;0;N" title="Ente appaltatore">
								<?
								while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
									?><option <?= $rec_contratto["codice_ente"] == $rec["codice"] ? 'selected="selected"' : null ?> value="<?= $rec["codice"] ?>"><?= $rec["denominazione"] ?></option><?
								}
								?>
							</select>
						</tr>
						<?
					}
					?>
					<tr>
            <td class="etichetta">Importo totale:</td>
          	<td colspan="5">
							<input type="text" name="importo_totale" id="importo_totale" title="Importo totale" value="<?= $rec_contratto["importo_totale"] ?>" rel="N;0;0;N">
         		</td>
          </tr>
          <tr>
            <td class="etichetta">Oggetto:</td>
          	<td colspan="5">
            	<textarea name="oggetto" id="oggetto" title="Oggetto" rel="S;3;0;A" class="ckeditor_simple"><?= $rec_contratto["oggetto"] ?></textarea>
         		</td>
          </tr>
				</table>
				<div class="clear"></div>
				<a class="precedente" style="float:left" href="#">Step precedente</a>
				<a class="successivo" style="float:right" href="#">Step successivo</a>
				<div class="clear"></div>
			</div>
			<div id="descrizione">
				<textarea name="descrizione" id="descrizione" title="Descrizione" class="ckeditor_full" rel="S;3;0;A"><?= $rec_contratto["descrizione"] ?></textarea>
				<div class="clear"></div>
				<a class="precedente" style="float:left" href="#">Step precedente</a>
				<a class="successivo" style="float:right" href="#">Step successivo</a>
				<div class="clear"></div>
			</div>
			<div id="contratti_appalto">
				<table width="100%" class="info_appalto">
					<tr>
						<td class="etichetta">Tipologia Appalto:</td>
						<td colspan="3">
							<select disabled="disabled" name="tipologia_appalto" rel="S;1;0;N" title="Tipologia Appalto">
              <option value="">Seleziona..</option>
							<?
								$sql = "SELECT * FROM b_tipologie WHERE attivo = 'S' AND eliminato = 'N' ORDER BY codice";
								$ris = $pdo->query($sql);
								if ($ris->rowCount()>0) {
									while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
										?><option <?= $rec_contratto["tipologia_appalto"] == $rec["codice"] ? 'selected="selected"' : null ?> value="<?= $rec["codice"] ?>"><?= $rec["tipologia"] ?></option><?
									}
								}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="etichetta">N. provvedimento aggiudicazione:</td>
						<td><input disabled="disabled" class="espandi" type="text" name="n_atto" title="N. provvedimento aggiudicazione" value="<?= $rec_contratto["n_atto"] ?>" rel="N;1;10;A"></td>
						<td class="etichetta">Data provvedimento aggiudicazione:</td>
						<td><input disabled="disabled" class="espandi datepick" type="text" name="data_atto" title="Data provvedimento aggiudicazione" value="<?= mysql2date($rec_contratto["data_atto"]) ?>" rel="N;1;10;A"></td>
					</tr>
					<tr>
						<td class="etichetta">CIG:</td>
						<td><input disabled="disabled" class="espandi" type="text" name="cig" title="CIG" value="<?= $rec_contratto["cig"] ?>" rel="N;10;10;A"></td>
						<td class="etichetta">CUP:</td>
						<td><input disabled="disabled" class="espandi" type="text" name="cup" title="CUP" value="<?= $rec_contratto["cup"] ?>" rel="N;15;15;A"></td>
					</tr>
				</table>
				<div class="clear"></div>
				<a class="precedente" style="float:left" href="#">Step precedente</a>
				<a class="successivo" style="float:right" href="#">Step successivo</a>
				<div class="clear"></div>
			</div>
			<div id="luogo_stipula">
				<table width="100%">
					<tr>
						<td class="etichetta" colspan="4">
							<strong>Informazioni luogo di firma</strong>
						</td>
					</tr>
					<tr>
						<td class="etichetta">Indirizzo: *</td>
						<td colspan="3"><input type="text" name="indirizzo_stipula" value="<?= $rec_contratto["indirizzo_stipula"] ?>" rel="S;1;0;A" title="Indirizzo"></td>
					</tr>
					<tr>
						<td class="etichetta">Comune: *</td>
						<td><input type="text" name="comune_stipula" id="comune_stipula" value="<?= $rec_contratto["comune_stipula"] ?>" rel="S;1;0;A" title="Comune"></td>
						<td class="etichetta">Provincia: *</td>
						<td><input type="text" name="provincia_stipula" id="provincia_stipula" value="<?= $rec_contratto["provincia_stipula"] ?>" rel="S;1;2;A" title="Provincia"></td>
					</tr>
					<tr>
						<td class="etichetta">Codice Comune: *</td>
						<td><input type="text" name="codice_comune_stipula" id="codice_comune_stipula" value="<?= $rec_contratto["codice_comune_stipula"] ?>" rel="S;1;0;A" title="Comune"></td>
						<td class="etichetta">Regione: *</td>
						<td><input type="text" name="regione_stipula" id="regione_stipula" value="<?= $rec_contratto["regione_stipula"] ?>" rel="S;1;0;A" title="Regione"></td>
					</tr>
				</table>
				<script type="text/javascript">
					$('#comune_stipula').autocomplete({
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
							$("#provincia_stipula").val(result.item.provincia_stipula);
							$("#codice_comune_stipula").val(result.item.codice_comune_stipula);
							$("#regione_stipula").val(result.item.regione_stipula);
							$(this).focus();
						},
						focus: function(e, result) {
							//e.preventDefault() // <--- Prevent the value from being inserted.
							$("#provincia_stipula").val(result.item.provincia_stipula);
							$("#codice_comune_stipula").val(result.item.codice_comune_stipula);
							$("#regione_stipula").val(result.item.regione_stipula);
						}
					}).data("ui-autocomplete")._renderItem = function( ul, item ) {
						return $( "<li id='val"+ item.value +"'>" ).append("<a><strong>" + item.value + "</strong> - " + item.label).appendTo( ul );
					}
				</script>
			</div>
		</div>
    <div class="clear"></div>
    <? if(!$lock) { ?>
      <input type="submit" class="submit_big" value="Salva">
    <? } ?>
	<? if(!$lock) { ?></form><? } ?>
	<script type="text/javascript">
    $("#tabs").tabs();
		$('#tipologia_contratto').on('change', function(event) {
			event.preventDefault();
			if($(this).val() == "appalto") {
				$('.info_appalto').find(':input').each(function(index, el) {
					$(el).removeAttr('disabled');
					$(el).removeProp('disabled');
					if($(el).is('select')) {
						$(el).trigger('chosen:updated');
					}
				});
			} else {
				$('.info_appalto').find(':input').each(function(index, el) {
					$(el).attr('disabled', 'disabled');
					$(el).prop('disabled', 'disabled');
					if($(el).is('select')) {
						$(el).trigger('chosen:updated');
					}
				});
			}
		});
		$(window).load(function(){
			$('#tipologia_contratto').trigger('change');
      <? if($lock) { ?>
        $(':input').not('.submit_big').attr('disabled', true).prop('disabled', true);
      <? } ?>
		})
		$('#importo_totale').on('blur', function(e) {
			$(this).val(number_format($(this).val(),2,".",""));
		});
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
	</script>
	<?
	if(!empty($rec_contratto["codice"])) include_once($root . "/contratti/ritorna_pannello_contratto.php");
	include_once($root."/layout/bottom.php");
?>
