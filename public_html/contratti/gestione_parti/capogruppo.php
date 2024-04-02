<?
	if(!empty($_POST["id"]) && empty($record_operatore)) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
		$record_operatore = get_campi("b_contraenti");
		$record_operatore["tipo"] = "";
		$record_operatore["codice_contratto"] = $_GET["codice_contratto"];
		$id_capogruppo = $_POST["id"];
	} else {
		$id_capogruppo = $record_operatore["codice"];
	}
	if (!isset($mandatario)) $mandatario = FALSE;
	if(isset($id_capogruppo)) {
		if(is_numeric($id_capogruppo)) {
			$bind = array(':tipologia' => "oe", ':codice_contratto' => $record_operatore["codice_contratto"], ':codice_capogruppo' => $record_operatore["codice"]);
			$ris_mandante = $pdo->bindAndExec("SELECT b_contraenti.* , r_contratti_contraenti.codice_capogruppo FROM b_contraenti JOIN r_contratti_contraenti ON r_contratti_contraenti.codice_contraente = b_contraenti.codice WHERE b_contraenti.tipologia = :tipologia AND r_contratti_contraenti.codice_contratto = :codice_contratto AND r_contratti_contraenti.codice_capogruppo = :codice_capogruppo", $bind);
			if($ris_mandante->rowCount() > 0) $mandatario = TRUE;
		} 
		?>
		<div id="partecipante_<?= $id_capogruppo ?>" class="box edit-box" style="border-left:5px solid #999;">
			<table width="100%" id="tabella_<?= $id_capogruppo ?>">
				<thead>
	        <tr>
						<td>Codice Fiscale OE</td>
						<td>Denominazione</td>
						<td>Ruolo</td>
						<td colspan="2"></td>
					</tr>
	      </thead>
				<tbody>
					<tr>
						<td width="20%">
							<input type="hidden" name="partecipante[<?= $id_capogruppo ?>][id]" id="id_partecipante_<?= $id_capogruppo ?>" value="<?= $id_capogruppo ?>">
	            <input type="hidden" name="partecipante[<?= $id_capogruppo ?>][codice]" id="codice_partecipante_<?= $id_capogruppo ?>" value="<?= $record_operatore["codice"] ?>">
	            <input type="hidden" name="partecipante[<?= $id_capogruppo ?>][codice_operatore]" id="codice_operatore_partecipante_<?= $id_capogruppo ?>" value="<?= $record_operatore["codice_operatore"] ?>">
	            <input type="hidden" name="partecipante[<?= $id_capogruppo ?>][codice_utente]" id="codice_utente_partecipante_<?= $id_capogruppo ?>" value="<?= $record_operatore["codice_utente"] ?>">
	            <input type="text" style="font-weight:bold" class="partita_iva" name="partecipante[<?= $id_capogruppo ?>][partita_iva]"  title="Codice fiscale Impresa" rel="S;11;0;PICF" id="partita_iva_partecipante_<?= $id_capogruppo ?>" value="<?= $record_operatore["partita_iva"] ?>">
						</td>
						<td>
							<input type="text" style="font-weight:bold; width:99%" name="partecipante[<?= $id_capogruppo ?>][denominazione]"  title="Ragione Sociale" rel="S;3;255;A" id="denominazione_partecipante_<?= $id_capogruppo ?>" value="<?= $record_operatore["denominazione"] ?>">
						</td>
						<td width="10%">
							<select onchange="change_tipo('#aggiungi_gruppo_<?= $id_capogruppo ?>', '#tabella_<?= $id_capogruppo ?>', $(this).val());" style="font-weight:bold" name="partecipante[<?= $id_capogruppo ?>][tipo]" title="Ruolo" rel="N;2;250;A">
	              <option value="">NESSUNO</option>
								<option <?= $mandatario ? 'selected="selected"' : null ?> value="04-CAPOGRUPPO">CAPOGRUPPO</option>
							</select>
						</td>
						<td width="10" style="text-align:center">
							<button id="aggiungi_gruppo_<?= $id_capogruppo ?>" <? if(!$mandatario) echo 'style="display:none"' ?> type="button" class="button button-action button-circle button-small" onClick="aggiungi('membro.php?codice_capogruppo=<?= $id_capogruppo ?>','#tabella_<?= $id_capogruppo ?>');return false;"><i class="fa fa-plus"></i></button>
						</td>
						<td width="10" style="text-align:center">
							<button type="button" class="button button-caution button-circle button-small" onClick="elimina('<?= $id_capogruppo ?>','contratti/gestione_parti');return false;"><i class="fa fa-times"></i></button>
						</td>
					</tr>
					<tr>
						<td>
							Sede Legale:
						</td>
						<td colspan="4">
							<input type="text" name="partecipante[<?= $id_capogruppo ?>][sede]"  title="Sede legale" rel="N;1;0;A" id="sede_partecipante_<?= $id_capogruppo ?>" value="<?= $record_operatore["sede"] ?>">
						</td>
					</tr>
					<tr>
						<td colspan="5">
							<table width="100%">
								<thead>
									<tr>
										<td colspan="4">
											LEGALE RAPPRESENTANTE / LIBERO PROFESSIONISTA
										</td>
									</tr>
								</thead>
								<tbody>
									<tr>
	                  <td class="etichetta">
	                    Titolo: *
	                  </td>
	                  <td>
	                    <select name="partecipante[<?= $id_capogruppo ?>][titolo]" rel="S;1;0;A" title="Titolo" id="titolo_<?= $id_capogruppo ?>_oe" data-input="#titolo_<?= $id_capogruppo ?>_oe_altro" onchange="check_altro($(this))">
	                      <option value="">Seleziona..</option>
	                      <?
	                      $ris_titolo = $pdo->bindAndExec("SELECT DISTINCT b_contraenti.titolo FROM b_contraenti WHERE codice_gestore = :codice_gestore AND b_contraenti.tipologia = 'oe'", array(':codice_gestore' => $_SESSION["ente"]["codice"]));
	                      if($ris_titolo->rowCount() > 0) {
	                        while ($rec_titolo = $ris_titolo->fetch(PDO::FETCH_ASSOC)) {
	                          ?>
	                          <option <?= strtolower(html_entity_decode($record_operatore["titolo"], ENT_QUOTES, 'UTF-8')) == strtolower(html_entity_decode($rec_titolo["titolo"], ENT_QUOTES, 'UTF-8')) ? 'selected="seleceted"' : null  ?> value="<?= strtolower(html_entity_decode($rec_titolo["titolo"], ENT_QUOTES, 'UTF-8')) ?>"><?= ucfirst(strtolower(html_entity_decode($rec_titolo["titolo"], ENT_QUOTES, 'UTF-8'))) ?></option>
	                          <?
	                        }
	                      }
	                      ?>
	                      <option value="altro">Altro titolo</option>
	                    </select>
	                    <input type="text" style="display:none" disabled="disabled" rel="N;0;0;A" title="Altro titolo" name="partecipante[<?= $id_capogruppo ?>][titolo_altro]" id="titolo_<?= $id_capogruppo ?>_oe_altro">
	                  </td>
	                  <td class="etichetta">
	                    Ruolo: *
	                  </td>
	                  <td>
	                    <select name="partecipante[<?= $id_capogruppo ?>][ruolo]" rel="S;1;0;A" title="Titolo" id="ruolo_ore_<?= $id_capogruppo ?>">
	                      <option value="">Seleziona..</option>
												<option <?= $record_operatore["ruolo"] == "presidente" ? 'selected="selected"' : null ?> value="presidente">Presidente</option>
												<option <?= $record_operatore["ruolo"] == "delegato" ? 'selected="selected"' : null ?> value="delegato">Delegato</option>
												<option <?= $record_operatore["ruolo"] == "titolare" ? 'selected="selected"' : null ?> value="titolare">Titolare</option>
												<option <?= $record_operatore["ruolo"] == "amministratore delegato" ? 'selected="selected"' : null ?> value="amministratore delegato">Amministratore delegato</option>
												<option <?= $record_operatore["ruolo"] == "legale rappresentante" ? 'selected="selected"' : null ?> value="legale rappresentante">Legale rappresentante</option>
												<option <?= $record_operatore["ruolo"] == "amministratore unico" ? 'selected="selected"' : null ?> value="amministratore unico">Amministratore unico</option>
												<option <?= $record_operatore["ruolo"] == "libero professionista" ? 'selected="selected"' : null ?> value="libero professionista">Libero Professionista</option>
	                    </select>
	                  </td>
	                </tr>
									<tr>
										<td class="etichetta">Nome: *</td>
										<td>
											<input type="text" name="partecipante[<?= $id_capogruppo ?>][nome]"  title="Nome" rel="S;1;0;A" id="nome_partecipante_<?= $id_capogruppo ?>" value="<?= $record_operatore["nome"] ?>">
										</td>
										<td class="etichetta">Cognome: *</td>
										<td>
											<input type="text" name="partecipante[<?= $id_capogruppo ?>][cognome]"  title="Cognome" rel="S;1;0;A" id="cognome_partecipante_<?= $id_capogruppo ?>" value="<?= $record_operatore["cognome"] ?>">
										</td>
									</tr>
									<tr>
										<td class="etichetta">Data di nascita:</td>
										<td>
											<input type="text" name="partecipante[<?= $id_capogruppo ?>][data_nascita]" class="datepick"  title="Data di nascita" rel="N;10;10;D" id="data_nascita_partecipante_<?= $id_capogruppo ?>" value="<?= mysql2date($record_operatore["data_nascita"]) ?>">
										</td>
										<td class="etichetta">Luogo di Nascita:</td>
										<td>
											<input type="text" name="partecipante[<?= $id_capogruppo ?>][comune_nascita]"  title="Luogo di Nascita" rel="N;1;0;A" id="comune_nascita_partecipante_<?= $id_capogruppo ?>" value="<?= $record_operatore["comune_nascita"] ?>">
										</td>
									</tr>
									<tr>
										<td class="etichetta">Provincia di Nascita:</td>
										<td>
											<input type="text" name="partecipante[<?= $id_capogruppo ?>][provincia_nascita]" title="Provincia di Nascita" rel="N;0;0;A" id="provincia_nascita_partecipante_<?= $id_capogruppo ?>" value="<?= $record_operatore["provincia_nascita"] ?>">
										</td>
										<td class="etichetta">Codice Fiscale:</td>
										<td>
											<input type="text" name="partecipante[<?= $id_capogruppo ?>][cf]"  title="Codice Fiscale" rel="N;1;0;A" id="cf_partecipante_<?= $id_capogruppo ?>" value="<?= $record_operatore["cf"] ?>">
										</td>
									</tr>
									<tr>
										<td class="etichetta">Indirizzo di residenza: *</td>
										<td colspan="4">
											<input type="text" name="partecipante[<?= $id_capogruppo ?>][indirizzo_residenza]"  title="Indirizzo di residenza" rel="S;1;0;A" id="indirizzo_residenza_partecipante_<?= $id_capogruppo ?>" value="<?= $record_operatore["indirizzo_residenza"] ?>">
										</td>
									</tr>
									<tr>
										<td class="etichetta">Comune di residenza:</td>
										<td>
											<input type="text" name="partecipante[<?= $id_capogruppo ?>][comune_residenza]"  title="Comune di residenza" rel="S;1;0;A" id="comune_residenza_partecipante_<?= $id_capogruppo ?>" value="<?= $record_operatore["comune_residenza"] ?>">
										</td>
										<td class="etichetta">Provincia di residenza:</td>
										<td>
											<input type="text" name="partecipante[<?= $id_capogruppo ?>][provincia_residenza]"  title="Provincia di residenza" rel="N;1;0;A" id="provincia_residenza_partecipante_<?= $id_capogruppo ?>" value="<?= $record_operatore["provincia_residenza"] ?>">
										</td>
									</tr>
								</tbody>
								<?
								if(!empty($ris_mandante) && $ris_mandante->rowCount() > 0) {
									$i = 0;
									while ($record_membro = $ris_mandante->fetch(PDO::FETCH_ASSOC)) {
										$i++;
										if (!isset($record_membro["codice"])) $record_membro["codice"] = "i_i_".$i;
										include 'membro.php';
									}
								}
								?>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<script type="text/javascript">
				$('#partita_iva_partecipante_<?= $id_capogruppo ?>').autocomplete({
					source: function(request, response) {
						$.ajax({
						url: "/contratti/gestione_parti/operatori.php",
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
						$("#denominazione_partecipante_<?= $id_capogruppo ?>").val(result.item.ragione_sociale);
						$("#codice_operatore_partecipante_<?= $id_capogruppo ?>").val(result.item.codice_operatore);
						$("#codice_utente_partecipante_<?= $id_capogruppo ?>").val(result.item.codice_utente);
						$("#nome_partecipante_<?= $id_capogruppo ?>").val(result.item.codice_utente);
						$('#sede_partecipante_<?= $id_capogruppo ?>').val(result.item.sede_partecipante);
						$('#nome_partecipante_<?= $id_capogruppo ?>').val(result.item.nome_partecipante);
						$('#cognome_partecipante_<?= $id_capogruppo ?>').val(result.item.cognome_partecipante);
						$('#data_nascita_partecipante_<?= $id_capogruppo ?>').val(result.item.data_nascita_partecipante);
						$('#comune_nascita_partecipante_<?= $id_capogruppo ?>').val(result.item.comune_nascita_partecipante);
						$('#provincia_nascita_partecipante_<?= $id_capogruppo ?>').val(result.item.provincia_nascita_partecipante);
						$('#cf_partecipante_<?= $id_capogruppo ?>').val(result.item.cf_partecipante);
						$('#indirizzo_residenza_partecipante_<?= $id_capogruppo ?>').val(result.item.indirizzo_residenza_partecipante);
						$('#comune_residenza_partecipante_<?= $id_capogruppo ?>').val(result.item.comune_residenza_partecipante);
						$('#provincia_residenza_partecipante_<?= $id_capogruppo ?>').val(result.item.provincia_residenza_partecipante);
						$('#ruolo_ore_<?= $id_capogruppo ?>').val(result.item.ruolo_ore);
						$('#ruolo_ore_<?= $id_capogruppo ?>').trigger('chosen:updated');
						$(this).focus();
					},
					focus: function(e, result) {
						//e.preventDefault() // <--- Prevent the value from being inserted.
						$("#denominazione_partecipante_<?= $id_capogruppo ?>").val(result.item.ragione_sociale);
						$("#codice_operatore_partecipante_<?= $id_capogruppo ?>").val(result.item.codice_operatore);
						$("#codice_utente_partecipante_<?= $id_capogruppo ?>").val(result.item.codice_utente);
						$("#nome_partecipante_<?= $id_capogruppo ?>").val(result.item.codice_utente);
						$('#sede_partecipante_<?= $id_capogruppo ?>').val(result.item.sede_partecipante);
						$('#nome_partecipante_<?= $id_capogruppo ?>').val(result.item.nome_partecipante);
						$('#cognome_partecipante_<?= $id_capogruppo ?>').val(result.item.cognome_partecipante);
						$('#data_nascita_partecipante_<?= $id_capogruppo ?>').val(result.item.data_nascita_partecipante);
						$('#comune_nascita_partecipante_<?= $id_capogruppo ?>').val(result.item.comune_nascita_partecipante);
						$('#provincia_nascita_partecipante_<?= $id_capogruppo ?>').val(result.item.provincia_nascita_partecipante);
						$('#cf_partecipante_<?= $id_capogruppo ?>').val(result.item.cf_partecipante);
						$('#indirizzo_residenza_partecipante_<?= $id_capogruppo ?>').val(result.item.indirizzo_residenza_partecipante);
						$('#comune_residenza_partecipante_<?= $id_capogruppo ?>').val(result.item.comune_residenza_partecipante);
						$('#provincia_residenza_partecipante_<?= $id_capogruppo ?>').val(result.item.provincia_residenza_partecipante);
						$('#ruolo_ore_<?= $id_capogruppo ?>').val(result.item.ruolo_ore);
						$('#ruolo_ore_<?= $id_capogruppo ?>').trigger('chosen:updated');
					}
				}).data("ui-autocomplete")._renderItem = function( ul, item ) {
					return $( "<li id='val"+ item.value +"'>" ).append("<a><strong>" + item.value + "</strong> - " + item.label).appendTo( ul );
				}
			</script>
		</div>
		<?
	}
?>
