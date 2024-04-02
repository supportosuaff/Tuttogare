<?
	if(!empty($_POST["id"]) && empty($record_membro)) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record_membro = get_campi("b_contraenti");
		$record_membro["codice_capogruppo"] = $_GET["codice_capogruppo"];
		$id_membro = $_POST["id"];
	} else {
		$id_membro = $record_membro["codice"];
	}
	if(isset($id_membro)) {
		?>
		<tr class="membro_di_raggruppamento" id="partecipante_<?= $id_membro ?>">
			<td colspan="5">
				<table width="100%">
					<thead>
						<tr>
							<td colspan="5">
								MANDANTE
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="etichetta">
								C.F. - P.IVA: *
							</td>
							<td width="20%">
								<input type="hidden" name="partecipante[<?= $id_membro ?>][id]" id="id_partecipante_<?= $id_membro ?>" value="<?= $id_membro ?>">
		            <input type="hidden" name="partecipante[<?= $id_membro ?>][codice]" id="codice_partecipante_<?= $id_membro ?>" value="<?= $record_membro["codice"] ?>">
		            <input type="hidden" name="partecipante[<?= $id_membro ?>][codice_operatore]" id="codice_operatore_partecipante_<?= $id_membro ?>" value="<?= $record_membro["codice_operatore"] ?>">
		            <input type="hidden" name="partecipante[<?= $id_membro ?>][codice_utente]" id="codice_utente_partecipante_<?= $id_membro ?>" value="<?= $record_membro["codice_utente"] ?>">
								<input type="hidden" name="partecipante[<?= $id_membro ?>][tipo]" value="01-MANDANTE">
								<input type="hidden" name="partecipante[<?= $id_membro ?>][codice_capogruppo]" value="<?= !empty($record_membro["codice_capogruppo"]) ? $record_membro["codice_capogruppo"] : null ?>">
		            <input type="text" style="font-weight:bold" class="partita_iva" name="partecipante[<?= $id_membro ?>][partita_iva]"  title="Codice fiscale Impresa" rel="S;11;0;PICF" id="partita_iva_partecipante_<?= $id_membro ?>" value="<?= $record_membro["partita_iva"] ?>">
							</td>
							<td>
								Ragione sociale: *
							</td>
							<td>
								<input type="text" style="font-weight:bold; width:99%" name="partecipante[<?= $id_membro ?>][denominazione]"  title="Ragione Sociale" rel="S;3;255;A" id="denominazione_partecipante_<?= $id_membro ?>" value="<?= $record_membro["denominazione"] ?>">
							</td>
							<td rowspan="2" valign="middle" width="10">
								<button type="button" class="button button-caution button-circle button-small" onClick="elimina('<?= $id_membro ?>','contratti/gestione_parti');return false;"><i class="fa fa-times"></i></button>
							</td>
						</tr>
						<tr>
							<td>Sede</td>
							<td colspan="3">
								<input type="text" name="partecipante[<?= $id_membro ?>][sede]"  title="Sede legale" rel="S;1;0;A" id="sede_partecipante_<?= $id_membro ?>" value="<?= $record_membro["sede"] ?>">
							</td>
						</tr>
					</tbody>
				</table>
				<script type="text/javascript">
					$('#partita_iva_partecipante_<?= $id_membro ?>').autocomplete({
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
							$("#denominazione_partecipante_<?= $id_membro ?>").val(result.item.ragione_sociale);
							$("#codice_operatore_partecipante_<?= $id_membro ?>").val(result.item.codice_operatore);
							$("#codice_utente_partecipante_<?= $id_membro ?>").val(result.item.codice_utente);
							$('#sede_partecipante_<?= $id_membro ?>').val(result.item.sede_partecipante);
							$(this).focus();
						},
						focus: function(e, result) {
							//e.preventDefault() // <--- Prevent the value from being inserted.
							$("#denominazione_partecipante_<?= $id_membro ?>").val(result.item.ragione_sociale);
							$("#codice_operatore_partecipante_<?= $id_membro ?>").val(result.item.codice_operatore);
							$("#codice_utente_partecipante_<?= $id_membro ?>").val(result.item.codice_utente);
							$('#sede_partecipante_<?= $id_membro ?>').val(result.item.sede_partecipante);
						}
					}).data("ui-autocomplete")._renderItem = function( ul, item ) {
						return $( "<li id='val"+ item.value +"'>" ).append("<a><strong>" + item.value + "</strong> - " + item.label).appendTo( ul );
					}
				</script>
			</td>
		</tr>
		<?
	}
?>
