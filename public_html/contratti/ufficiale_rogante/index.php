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
			$rec_ufficiale = get_campi('b_ufficiale_rogante');
			$rec_ufficiale["codice"] = 0;

			$sql_ufficiale = "SELECT b_ufficiale_rogante.* FROM b_ufficiale_rogante ";
			$sql_ufficiale .= "JOIN r_contratti_ufficiale_rogante ON b_ufficiale_rogante.codice = r_contratti_ufficiale_rogante.codice_ufficiale ";
			$sql_ufficiale .= "WHERE r_contratti_ufficiale_rogante.codice_contratto = :codice_contratto ";
			$ris_ufficiale = $pdo->bindAndExec($sql_ufficiale, array(':codice_contratto' => $rec_contratto["codice"]));
			if($ris_ufficiale->rowCount() > 0) {
					$rec_ufficiale = $ris_ufficiale->fetch(PDO::FETCH_ASSOC);
			}

      $lock = FALSE;
      if(!empty($rec_contratto["codice"])) {
        $oe = $ore = 0;
        $oe = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "oe" GROUP BY b_contraenti.codice', array(':codice_contratto' => $rec_contratto["codice"]))->rowCount();
        $ore = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "ore" GROUP BY b_contraenti.codice', array(':codice_contratto' => $rec_contratto["codice"]))->rowCount();
        if($oe > 0 && $ore == 1) {
          $bind = array(':codice' => $rec_contratto["codice"], ':tipo' => 'contratto', ':sezione' => 'contratti');
          $ris = $pdo->bindAndExec("SELECT b_documentale.codice FROM b_documentale WHERE b_documentale.tipo = :tipo AND b_documentale.sezione = :sezione AND b_documentale.codice_gara = :codice AND codice_allegato > 0", $bind);
          if($ris->rowCount() > 0) $lock = TRUE;
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
			<h1>Gestione Ufficiale Rogante - Notaio</h1>
			<table width="100%" style="margin-bottom:25px; border:solid 1px #999;">
				<tr>
					<td colspan="2" class="etichetta">
						<b>&Egrave; possibile selezionare un ufficiale rogante/notaio dall&#39;elenco per compilare automaticamente i dati sottostanti con le informazioni gi&agrave; presenti nel sitema.</b>
					</td>
				</tr>
				<tr>
					<td class="etichetta"><i class="fa fa-search"></i> Ricerca ufficiale rogante:</td>
					<!-- <td><input type="text" name="ufficiale_rogante" title="Ricerca un ufficiale rogante gi&agrave; presente nella piattaforma digitanto il nome, il cognome o il C.F." value=""></td> -->
					<td>
						<select id="ricerca_ufficiale_rogante" onchange="fill_ufficiale($(this).val())" title="Ricerca ufficiale Rogante">
							<option	selected="selected" value="">Nuovo inserimento</option>
							<?
							$ris = $pdo->bindAndExec('SELECT * FROM b_ufficiale_rogante WHERE codice_ente = :codice_ente', array(':codice_ente' => $rec_contratto["codice_ente"]));
							if($ris->rowCount() > 0) {
								while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
									?><option value="<?= $rec["codice"] ?>"><?= ucwords(strtolower(html_entity_decode($rec["titolo"]  . " " . $rec["nome"] . " " . $rec["cognome"], ENT_QUOTES, 'UTF-8'))) ?></option><?
								}
							}
							?>
						</select>
					</td>
				</tr>
			</table>
			<? if(!$lock) { ?><form name="ufficiale_rogante" method="post" action="save.php" rel="validate"><? } ?>
				<input type="hidden" name="codice_ufficiale" id="codice_ufficiale" value="<?= $rec_ufficiale["codice"] ?>">
				<input type="hidden" name="codice_contratto" value="<?= $rec_contratto["codice"]; ?>">
				<input type="hidden" name="codice_ente" value="<?= $rec_contratto["codice_ente"]; ?>">
        <? if(!$lock) { ?><div class="comandi"><button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button></div><? } ?>
				<table id="tab_moduli" width="100%">
					<tbody>
            <tr>
              <td class="etichetta">
                Tipo: *
              </td>
              <td>
                <select id="tipo_ufficiale" name="tipo_ufficiale" rel="S;1;0;A" title="Tipologia ufficiale rogante">
                  <option value="">Seleziona..</option>
									<option <?= $rec_ufficiale["tipo_ufficiale"] == "1" ? 'selected="selected"' : null ?> value="1">Notaio</option>
	                <option <?= $rec_ufficiale["tipo_ufficiale"] == "2" ? 'selected="selected"' : null ?> value="2">Altro Ufficiale Rogante</option>
	                <option <?= $rec_ufficiale["tipo_ufficiale"] == "3" ? 'selected="selected"' : null ?> value="3">Autorit&agrave; Emittente</option>
	                <option <?= $rec_ufficiale["tipo_ufficiale"] == "4" ? 'selected="selected"' : null ?> value="2">Altro Ufficiale Rogante</option>
                </select>
              </td>
							<td class="etichetta">
                Ruolo: *
              </td>
              <td>
                <select id="ruolo" name="ruolo" rel="S;1;0;A" title="Titolo" id="ruolo_ufficiale_rogante" data-input="#ruolo_ufficiale_rogante_altro" onchange="check_altro($(this))">
                  <option value="">Seleziona..</option>
									<?
									$ris = $pdo->bindAndExec("SELECT DISTINCT b_ufficiale_rogante.ruolo FROM b_ufficiale_rogante WHERE codice_ente = :codice_ente", array(':codice_ente' => $rec_contratto["codice_ente"]));
									if($ris->rowCount() > 0) {
										while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
											?>
											<option <?= strtolower(html_entity_decode($rec_ufficiale["ruolo"], ENT_QUOTES, 'UTF-8')) == strtolower(html_entity_decode($rec["ruolo"], ENT_QUOTES, 'UTF-8')) ? 'selected="seleceted"' : null  ?> value="<?= strtolower(html_entity_decode($rec["ruolo"], ENT_QUOTES, 'UTF-8')) ?>"><?= ucfirst(strtolower(html_entity_decode($rec["ruolo"], ENT_QUOTES, 'UTF-8'))) ?></option>
											<?
										}
									}
									?>
                  <option value="altro">Altro ruolo</option>
                </select>
								<input type="text" style="display:none" disabled="disabled" rel="N;0;0;A" title="Altro ruolo ufficiale Rogante" name="ruolo_altro" id="ruolo_ufficiale_rogante_altro">
              </td>
            </tr>
						<tr>
							<td class="etichetta" colspan="4">
								<b>Riferimenti</b>
							</td>
						</tr>
						<tr>
							<td class="etichetta">
                Titolo: *
              </td>
              <td colspan="3">
                <select name="titolo" rel="S;1;0;A" title="Titolo" id="titolo" data-input="#titolo_ufficiale_rogante_altro" onchange="check_altro($(this))">
                  <option value="">Seleziona..</option>
									<?
									$ris = $pdo->bindAndExec("SELECT DISTINCT b_ufficiale_rogante.titolo FROM b_ufficiale_rogante WHERE codice_ente = :codice_ente", array(':codice_ente' => $rec_contratto["codice_ente"]));
									if($ris->rowCount() > 0) {
										while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
											?>
											<option <?= strtolower(html_entity_decode($rec_ufficiale["titolo"], ENT_QUOTES, 'UTF-8')) == strtolower(html_entity_decode($rec["titolo"], ENT_QUOTES, 'UTF-8')) ? 'selected="seleceted"' : null  ?> value="<?= strtolower(html_entity_decode($rec["titolo"], ENT_QUOTES, 'UTF-8')) ?>"><?= ucfirst(strtolower(html_entity_decode($rec["titolo"], ENT_QUOTES, 'UTF-8'))) ?></option>
											<?
										}
									}
									?>
                  <option value="altro">Altro titolo</option>
                </select>
								<input type="text" style="display:none" disabled="disabled" rel="N;0;0;A" title="Altro titolo ufficiale Rogante" name="titolo_altro" id="titolo_ufficiale_rogante_altro">
              </td>
						</tr>
						<tr>
							<td class="etichetta">Nome: *</td>
							<td><input type="text" id="nome" name="nome" value="<?= $rec_ufficiale["nome"] ?>" rel="S;1;0;A" title="Nome"></td>
							<td class="etichetta">Cognome: *</td>
							<td><input type="text" id="cognome" name="cognome" value="<?= $rec_ufficiale["cognome"] ?>" rel="S;1;0;A" title="Cognome"></td>
						</tr>
						<tr>
							<td class="etichetta">Data di nascita:</td>
							<td><input type="text" class="datepick" id="data_nascita" name="data_nascita" value="<?= mysql2date($rec_ufficiale["data_nascita"]) ?>" rel="N;10;10;D" title="Data di nascita"></td>
							<td class="etichetta">Comune di nascita:</td>
							<td><input type="text" id="comune_nascita" name="comune_nascita" value="<?= $rec_ufficiale["comune_nascita"] ?>" rel="N;1;0;A" title="Comune di nascita"></td>
						</tr>
						<tr>
							<td class="etichetta">Provincia di nascita	:</td>
							<td><input type="text" id="provincia_nascita" name="provincia_nascita" value="<?= $rec_ufficiale["provincia_nascita"] ?>" rel="N;1;0;A" title="Provincia di nascita	"></td>
							<td class="etichetta">Codice Fiscale:</td>
							<td><input type="text" id="cf" name="cf" value="<?= $rec_ufficiale["cf"] ?>" rel="S;16;16;A" title="Codice Fiscale"></td>
						</tr>
					</tbody>
				</table>
        <? if(!$lock) { ?>
          <button type="button" class="submit_big" style="background-color:#FF3300; border:none !important;" onclick="reset_form('ufficiale_rogante')">Svuota Form</button>
          <input type="submit" class="submit_big" value="Salva">
          </form>
        <? } ?>
			<script type="text/javascript">
				function fill_ufficiale(codice_ufficiale) {
					if(codice_ufficiale.length > 0) {
						$.ajax({
							url: 'get_info_ufficiale.php',
							type: 'POST',
							dataType: 'json',
							data: {codice_ufficiale: codice_ufficiale},
							beforeSend: function(e) {
								$('#wait').fadeIn('fast');
							}
						})
						.done(function(result) {
							reset_form('ufficiale_rogante');
							var key = ['codice_ufficiale', 'tipo_ufficiale','ruolo','titolo','nome','cognome','data_nascita','comune_nascita','provincia_nascita','cf'];
							$.each(key, function(index, name) {
								if(result.hasOwnProperty(name)) {
									$('#'+name).val(result[name]);
									if($('#'+name).is('select')) $('#'+name).trigger('chosen:updated');
								}
							});
						})
						.fail(function() {
							$('#ricerca_ufficiale_rogante').val('').trigger('chosen:updated');
							reset_form('ufficiale_rogante');
							jalert('<h2>Non è stato possibile caricare le informazioni. Si prega di riprovare</h2>');
						})
						.always(function() {
							$('#wait').fadeOut('fast');
						});
					}
				}

				function reset_form(name) {
					$('form[name='+name+']').find(':input').not(':input[type=submit], :input[type=button], :input[type=reset], :input[name=codice_contratto], :input[name=codice_ente]').val('');
					$('form[name='+name+']').find('select').val('').trigger('chosen:updated');
				}

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
