<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record_membro = get_campi("r_partecipanti");
		$id_membro = $_POST["id"];
		$lock = false;
		if (isset($_GET["codice_capogruppo"])) $record_membro["codice_capogruppo"] = $_GET["codice_capogruppo"];
	}
?>
		<tr id="partecipante_<? echo $id_membro ?>">
			<td width="10">
            <input type="hidden" name="partecipante[<? echo $id_membro ?>][id]" id="id_partecipante_<? echo $id_membro ?>" value="<? echo $id_membro ?>">
            <input type="hidden" name="partecipante[<? echo $id_membro ?>][codice]" id="codice_partecipante_<? echo $id_membro ?>" value="<? echo $record_membro["codice"] ?>">
            <input type="hidden" name="partecipante[<? echo $id_membro ?>][codice_operatore]" id="codice_operatore_partecipante_<? echo $id_membro ?>" value="<? echo $record_membro["codice_operatore"] ?>">
            <input type="hidden" name="partecipante[<? echo $id_membro ?>][codice_utente]" id="codice_utente_partecipante_<? echo $id_membro ?>" value="<? echo $record_membro["codice_utente"] ?>">
            <input type="text" class="partita_iva" size="16" name="partecipante[<? echo $id_membro ?>][partita_iva]"  title="Codice fiscale Impresa" rel="<?= (empty($record_partecipante["identificativoEstero"])) ? "S" : "N" ?>;8;0;PICF" id="partita_iva_partecipante_<? echo $id_membro ?>" value="<? echo $record_membro["partita_iva"] ?>"></td>
    <td><input type="text" style="width:99%" name="partecipante[<? echo $id_membro ?>][ragione_sociale]"  title="Ragione Sociale" rel="S;3;255;A" id="ragione_sociale_partecipante_<? echo $id_membro ?>" value="<? echo $record_membro["ragione_sociale"] ?>"></td>
		<td><input type="text" style="width:99%" name="partecipante[<? echo $id_membro ?>][pec]"  title="Pec" rel="N;3;255;E" id="pec_partecipante_<? echo $id_membro ?>" value="<? echo $record_membro["pec"] ?>"></td>
    	<td width="10"><input type="text" size="16" name="partecipante[<? echo $id_membro ?>][identificativoEstero]"  onChange="if ($(this).val() == '') { $('#partita_iva_partecipante_<? echo $id_membro ?>').attr('rel','S;8;0;PICF'); } else { $('#partita_iva_partecipante_<? echo $id_membro ?>').attr('rel','N;8;0;PICF'); }" title="Identificativo fiscale estero" rel="N;10;20;A" id="identificativoEstero_partecipante_<? echo $id_membro ?>" value="<? echo $record_membro["identificativoEstero"] ?>"></td>
		 <td width="150"><select name="partecipante[<? echo $id_membro ?>][tipo]" title="Ruolo" id="tipo_partecipante_<? echo $id_membro ?>" rel="N;2;250;A">
				<option>01-MANDANTE</option>
				<option>05-CONSORZIATA</option>

                                    </select>

            <input type="hidden" name="partecipante[<? echo $id_membro ?>][codice_capogruppo]" id="codice_capogruppo_partecipante_<? echo $id_membro ?>" value="<? echo $record_membro["codice_capogruppo"] ?>">
    </td>
                        <? if (!$lock) { ?>
<td width="10" style="text-align:center"><input type="image" onClick="elimina('<? echo $id_membro ?>','gare/partecipanti');return false;" src="/img/del.png" title="Elimina"></td>
<? } ?></tr>
<script>
$("#tipo_partecipante_<? echo $id_membro ?>").val("<? echo $record_membro["tipo"] ?>");
$("#partita_iva_partecipante_<? echo $id_membro ?>").autocomplete({
						source: function(request, response) {
							$.ajax({
							url: "/gare/partecipanti/operatori.php",
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
						select: function(e, ui) {
							//e.preventDefault() // <--- Prevent the value from being inserted.
							$("#ragione_sociale_partecipante_<? echo $id_membro ?>").val($("<div/>").html(ui.item.ragione_sociale).text());
							$("#pec_partecipante_<? echo $id_membro ?>").val($("</div>").html(ui.item.pec).text());
							$("#identificativoEstero_partecipante_<? echo $id_membro ?>").val(ui.item.identificativoEstero);
							$("#codice_operatore_partecipante_<? echo $id_membro ?>").val(ui.item.codice_operatore);
							$("#codice_utente_partecipante_<? echo $id_membro ?>").val(ui.item.codice_utente);
							$(this).focus();
						},
						focus: function(e, ui) {
							//e.preventDefault() // <--- Prevent the value from being inserted.
							$("#ragione_sociale_partecipante_<? echo $id_membro ?>").val($("<div/>").html(ui.item.ragione_sociale).text());
							$("#pec_partecipante_<? echo $id_membro ?>").val($("</div>").html(ui.item.pec).text());
							$("#identificativoEstero_partecipante_<? echo $id_membro ?>").val(ui.item.identificativoEstero);
							$("#codice_operatore_partecipante_<? echo $id_membro ?>").val(ui.item.codice_operatore);
							$("#codice_utente_partecipante_<? echo $id_membro ?>").val(ui.item.codice_utente);
						}
			}).data("ui-autocomplete")._renderItem = function( ul, item ) {
						return $( "<li id='val"+ item.value +"'>" ).append("<a><strong>" + item.value + "</strong> - " + item.label).appendTo( ul );
			}
</script>
