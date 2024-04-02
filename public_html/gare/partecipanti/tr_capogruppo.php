<?
	if (isset($_POST["id"])) {
		if (!isset($pdo)) {
			session_start();
			include("../../../config.php");
			include_once($root."/inc/funzioni.php");
;
		}
		$record_partecipante = get_campi("r_partecipanti");
		$record["criterio"] = $_GET["codice_criterio"];
		$record["codice"] = $_GET["codice_gara"];
		$id_capogruppo = $_POST["id"];
		$bind=array();
		$bind[":criterio"] = $record["criterio"];
		$sql = "SELECT * FROM b_criteri_punteggi WHERE codice_criterio = :criterio ORDER BY ordinamento ";
		$ris_punteggi = $pdo->bindAndExec($sql,$bind);
		if (isset($_GET["codice_capogruppo"])) $record_partecipante["codice_capogruppo"] = $_GET["codice_capogruppo"];
	} else if (isset($_GET["codice"]) && empty($new_line)) {
		if (!isset($pdo)) {
			session_start();
			include("../../../config.php");
			include_once($root."/inc/funzioni.php");
;
		}
		$bind=array();
		$bind[":codice"] = $_GET["codice"];
		$sql = "SELECT * FROM r_partecipanti WHERE codice = :codice";
		$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
		if ($ris_partecipanti->rowCount()>0) {
			$record_partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC);
			$id_capogruppo = $record_partecipante["codice"];
		}
	}
	$lock = false;
	if (isset($record_partecipante)) {
?>
<div id="partecipante_<? echo $id_capogruppo ?>" class="box edit-box" style="border-left:5px solid #999;">
    <table align="right">
			<tr>
				<td class="etichetta">Protocollo</td>
				<td>
					<input type="text" style="font-weight:bold" name="partecipante[<? echo $id_capogruppo ?>][numero_protocollo]"  title="Numero protocollo" rel="N;1;100;A" id="numero_protocollo_partecipante_<? echo $id_capogruppo ?>" size="12" value="<? echo $record_partecipante["numero_protocollo"] ?>">
					<input type="text" style="font-weight:bold" name="partecipante[<? echo $id_capogruppo ?>][data_protocollo]"  title="Data protocollo" rel="N;10;10;D;<? echo date("d/m/Y H:i") ?>;<=" size="12" class="datepick" id="data_protocollo_partecipante_<? echo $id_capogruppo ?>" value="<? echo mysql2date($record_partecipante["data_protocollo"]) ?>">
				</td>
			</tr>
		</table>
		<table width="100%" id="tabella_<? echo $id_capogruppo ?>">
    	<thead>
        <tr><td>Codice Fiscale Impresa</td><td>Ragione sociale</td><td>Pec</td><td>Identificativo Estero</td><td>Ruolo</td><td colspan="2"></td></tr>
        </thead>
		<tr>
			<td width="10">
            <input type="hidden" name="partecipante[<? echo $id_capogruppo ?>][id]" id="id_partecipante_<? echo $id_capogruppo ?>" value="<? echo $id_capogruppo ?>">
            <input type="hidden" name="partecipante[<? echo $id_capogruppo ?>][codice]" id="codice_partecipante_<? echo $id_capogruppo ?>" value="<? echo $record_partecipante["codice"] ?>">
            <input type="hidden" name="partecipante[<? echo $id_capogruppo ?>][codice_operatore]" id="codice_operatore_partecipante_<? echo $id_capogruppo ?>" value="<? echo $record_partecipante["codice_operatore"] ?>">
            <input type="hidden" name="partecipante[<? echo $id_capogruppo ?>][codice_utente]" id="codice_utente_partecipante_<? echo $id_capogruppo ?>" value="<? echo $record_partecipante["codice_utente"] ?>">
            <input type="text" style="font-weight:bold" class="partita_iva" size="16" name="partecipante[<? echo $id_capogruppo ?>][partita_iva]"  title="Codice fiscale Impresa" rel="<?= (empty($record_partecipante["identificativoEstero"])) ? "S" : "N" ?>;8;0;PICF" id="partita_iva_partecipante_<? echo $id_capogruppo ?>" value="<? echo $record_partecipante["partita_iva"] ?>"></td>
    <td><input type="text" style="font-weight:bold; width:99%" name="partecipante[<? echo $id_capogruppo ?>][ragione_sociale]"  title="Ragione Sociale" rel="S;3;255;A" id="ragione_sociale_partecipante_<? echo $id_capogruppo ?>" value="<? echo $record_partecipante["ragione_sociale"] ?>"></td>
			<td><input type="text" style="font-weight:bold; width:99%" name="partecipante[<? echo $id_capogruppo ?>][pec]"  title="Pec" rel="N;3;255;E" id="pec_partecipante_<? echo $id_capogruppo ?>" value="<? echo $record_partecipante["pec"] ?>"></td>
    	<td width="150"><input style="font-weight:bold; width:99%"  type="text" name="partecipante[<? echo $id_capogruppo ?>][identificativoEstero]" onChange="if ($(this).val() == '') { $('#partita_iva_partecipante_<? echo $id_capogruppo ?>').attr('rel','S;8;0;PICF'); } else { $('#partita_iva_partecipante_<? echo $id_capogruppo ?>').attr('rel','N;8;0;PICF'); }" title="Identificativo fiscale estero" rel="N;5;0;A" id="identificativoEstero_partecipante_<? echo $id_capogruppo ?>" value="<? echo $record_partecipante["identificativoEstero"] ?>"></td>
		 <td width="150"><select style="font-weight:bold" name="partecipante[<? echo $id_capogruppo ?>][tipo]" title="Ruolo" id="tipo_partecipante_<? echo $id_capogruppo ?>" rel="N;2;250;A">
  	                          <option value="">NESSUNO</option>
                                    <option>04-CAPOGRUPPO</option>
                                    </select>
     </td>
                        <? if (!$lock) { ?>
                          <td width="10" id="aggiungi_gruppo_<? echo $id_capogruppo ?>" <? if ($record_partecipante["tipo"] == "") echo "style=\"display:none\""; ?>>
  <input type="image" src="/img/add.png" class="aggiungi" onClick="aggiungi('tr_membro.php?codice_capogruppo=<? echo $id_capogruppo ?>','#tabella_<? echo $id_capogruppo ?>');return false;"></td>
<td width="10" style="text-align:center"><input type="image" onClick="elimina('<? echo $id_capogruppo ?>','gare/partecipanti');return false;" src="/img/del.png" title="Elimina"></td>
<? } ?></tr>
<? if ($record_partecipante["tipo"] == "04-CAPOGRUPPO") {
	$bind = array();
	$bind[":codice"] = $record_partecipante["codice"];
	$sql = "SELECT * FROM r_partecipanti WHERE codice_capogruppo = :codice";
						$ris_membri = $pdo->bindAndExec($sql,$bind);
						if ($ris_membri->rowCount()>0) {
							while ($record_membro = $ris_membri->fetch(PDO::FETCH_ASSOC)) {
								$id_membro = $record_membro["codice"];
								include("tr_membro.php");
							}
						}
}
?>
</table>
  </div>
<script>
$("#tipo_partecipante_<? echo $id_capogruppo ?>").val("<? echo $record_partecipante["tipo"] ?>");
$("#tipo_partecipante_<? echo $id_capogruppo ?>").change(function() {
	if ($(this).val()=="04-CAPOGRUPPO") {
		$("#aggiungi_gruppo_<? echo $id_capogruppo ?>").show();
	} else {
		$("#aggiungi_gruppo_<? echo $id_capogruppo ?>").hide();
	}
});
$("#partita_iva_partecipante_<? echo $id_capogruppo ?>").autocomplete({
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
							$("#ragione_sociale_partecipante_<? echo $id_capogruppo ?>").val($("<div></div>").html(ui.item.ragione_sociale).text());
							$("#pec_partecipante_<? echo $id_capogruppo ?>").val($("<div></div>").html(ui.item.pec).text());
							$("#identificativoEstero_partecipante_<? echo $id_capogruppo ?>").val(ui.item.identificativoEstero);
							$("#codice_operatore_partecipante_<? echo $id_capogruppo ?>").val(ui.item.codice_operatore);
							$("#codice_utente_partecipante_<? echo $id_capogruppo ?>").val(ui.item.codice_utente);
							$(this).focus();
						},
						focus: function(e, ui) {
							//e.preventDefault() // <--- Prevent the value from being inserted.
							$("#ragione_sociale_partecipante_<? echo $id_capogruppo ?>").val($("<div></div>").html(ui.item.ragione_sociale).text());
							$("#pec_partecipante_<? echo $id_capogruppo ?>").val($("<div></div>").html(ui.item.pec).text());
							$("#identificativoEstero_partecipante_<? echo $id_capogruppo ?>").val(ui.item.identificativoEstero);
							$("#codice_operatore_partecipante_<? echo $id_capogruppo ?>").val(ui.item.codice_operatore);
							$("#codice_utente_partecipante_<? echo $id_capogruppo ?>").val(ui.item.codice_utente);
						}
			}).data("ui-autocomplete")._renderItem = function( ul, item ) {
						return $( "<li id='val"+ item.value +"'>" ).append("<a><strong>" + item.value + "</strong> - " + item.label).appendTo( ul );
			}

</script>
<? } ?>
