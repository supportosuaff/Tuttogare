<?
	if (isset($_POST["id"])) {
		if (!isset($pdo)) {
			session_start();
			include("../../../config.php");
			include_once($root."/inc/funzioni.php");
		}
		$record_iscritto = get_campi("b_commissari_albo");
		$id = $_POST["id"];
		$bind=array();
	} else if (isset($_GET["codice"]) && empty($new_line)) {
		if (!isset($pdo)) {
			session_start();
			include("../../../config.php");
			include_once($root."/inc/funzioni.php");
		}
		$bind=array();
		$bind[":codice"] = $_GET["codice"];
		if (empty($record["codice"])) $record["codice"] = $_GET["codice_albo"];
		$sql = "SELECT * FROM b_commissari_albo WHERE codice = :codice ";
		$ris_iscritti = $pdo->bindAndExec($sql,$bind);
		if ($ris_iscritti->rowCount()>0) {
			$record_iscritto = $ris_iscritti->fetch(PDO::FETCH_ASSOC);
			$id = $record_iscritto["codice"];
		}
	}
	$lock = false;
	if (isset($record_iscritto)) {
?>
<div id="iscritto_<? echo $id ?>" class="box edit-box" style="border-left:5px solid #999;">
  <table width="100%" id="tabella_<? echo $id ?>">
		<tr>
			<td class="etichetta">Codice Fiscale</td>
			<td class="etichetta">Cognome</td>
			<td class="etichetta">Nome</td>
			<td class="etichetta">Interno</td>
			<? if (!$lock) { ?>
				<td rowspan="3" width="10" style="text-align:center"><input type="image" onClick="elimina('<? echo $id ?>','albi_commissione/iscritti');return false;" src="/img/del.png" title="Elimina"></td>
			<? } ?>
		</tr>
		<tr>
			<td width="20%">
        <input type="hidden" name="iscritto[<? echo $id ?>][id]" id="id_iscritto_<? echo $id ?>" value="<? echo $id ?>">
        <input type="hidden" name="iscritto[<? echo $id ?>][codice]" id="codice_iscritto_<? echo $id ?>" value="<? echo $record_iscritto["codice"] ?>">
        <input type="text" style="font-weight:bold" class="codice_fiscale" size="20" name="iscritto[<? echo $id ?>][codice_fiscale]"  title="Codice fiscale" rel="S;11;0;PICF" id="codice_fiscale_<? echo $id ?>" value="<? echo $record_iscritto["codice_fiscale"] ?>">
			</td>
			<td width="35%">
				<input type="text" style="font-weight:bold; width:99%" name="iscritto[<? echo $id ?>][cognome]"  title="Cognome" rel="S;3;50;A" id="cognome_iscritto_<? echo $id ?>" value="<? echo $record_iscritto["cognome"] ?>">
			</td>
			<td width="35%">
				<input type="text" style="font-weight:bold; width:99%" name="iscritto[<? echo $id ?>][nome]"  title="Nome" rel="S;3;50;A" id="nome_iscritto_<? echo $id ?>" value="<? echo $record_iscritto["nome"] ?>">
			</td>
			<td width="10%">
				<select name="iscritto[<? echo $id ?>][interno]"  title="Commissario" rel="S;1;1;A" id="interno_iscritto_<? echo $id ?>">
					<option value="">Seleziona...</option>
					<option value="S">Si</option>
					<option value="N">No</option>
				</select>
				<script>
					$("#interno_iscritto_<? echo $id ?>").val("<? echo $record_iscritto["interno"] ?>");
				</script>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<table width="100%">
					<tr>
						<td class="etichetta">Telefono</td>
						<td class="etichetta">E-mail</td>
						<td class="etichetta">Fax</td>
						<td class="etichetta">Indirizzo</td>
						<td class="etichetta">Comune</td>
						<td class="etichetta">CAP</td>
					</tr>
					<tr>
						<td>
							<input type="text" style="font-weight:bold; width:99%" name="iscritto[<? echo $id ?>][telefono]"  title="Telefono" rel="N;1;20;A" id="telefono_iscritto_<? echo $id ?>" value="<? echo $record_iscritto["telefono"] ?>">
						</td>
						<td>
							<input type="text" style="font-weight:bold; width:99%" name="iscritto[<? echo $id ?>][email]"  title="email" rel="N;1;64;E" id="email_iscritto_<? echo $id ?>" value="<? echo $record_iscritto["email"] ?>">
						</td>
						<td>
							<input type="text" style="font-weight:bold; width:99%" name="iscritto[<? echo $id ?>][fax]"  title="Fax" rel="N;1;20;A" id="fax_iscritto_<? echo $id ?>" value="<? echo $record_iscritto["fax"] ?>">
						</td>
						<td>
							<input type="text" style="font-weight:bold; width:99%" name="iscritto[<? echo $id ?>][indirizzo]"  title="Indirizzo" rel="N;3;100;A" id="indirizzo_iscritto_<? echo $id ?>" value="<? echo $record_iscritto["indirizzo"] ?>">
						</td>
						<td>
							<input type="text" style="font-weight:bold; width:99%" name="iscritto[<? echo $id ?>][comune]"  title="Comune" rel="N;3;100;A" id="comune_iscritto_<? echo $id ?>" value="<? echo $record_iscritto["comune"] ?>">
						</td>
						<td>
							<input type="text" style="font-weight:bold; width:99%" name="iscritto[<? echo $id ?>][cap]"  title="CAP" rel="N;3;50;A" id="cap_iscritto_<? echo $id ?>" value="<? echo $record_iscritto["cap"] ?>">
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<script>
	$("#codice_fiscale_<? echo $id ?>").autocomplete({
						source: function(request, response) {
							$.ajax({
							url: "/albi_commissione/iscritti/commissari.php",
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
							$("#codice_fiscale_iscritto_<? echo $id ?>").val(ui.item.value);
							$("#cognome_iscritto_<? echo $id ?>").val(ui.item.cognome);
							$("#nome_iscritto_<?= $id ?>").val(ui.item.nome);
							$("#telefono_iscritto_<?= $id ?>").val(ui.item.telefono);
							$("#email_iscritto_<?= $id ?>").val(ui.item.email);
							$("#fax_iscritto_<?= $id ?>").val(ui.item.fax);
							$("#indirizzo_iscritto_<?= $id ?>").val(ui.item.indirizzo);
							$("#cap_iscritto_<?= $id ?>").val(ui.item.cap);
							$("#comune_iscritto_<?= $id ?>").val(ui.item.comune);
							$(this).focus();
						},
						focus: function(e, ui) {
							//e.preventDefault() // <--- Prevent the value from being inserted.
							$("#codice_fiscale_iscritto_<? echo $id ?>").val(ui.item.value);
							$("#cognome_iscritto_<? echo $id ?>").val(ui.item.cognome);
							$("#nome_iscritto_<?= $id ?>").val(ui.item.nome);
							$("#telefono_iscritto_<?= $id ?>").val(ui.item.telefono);
							$("#email_iscritto_<?= $id ?>").val(ui.item.email);
							$("#fax_iscritto_<?= $id ?>").val(ui.item.fax);
							$("#indirizzo_iscritto_<?= $id ?>").val(ui.item.indirizzo);
							$("#cap_iscritto_<?= $id ?>").val(ui.item.cap);
							$("#comune_iscritto_<?= $id ?>").val(ui.item.comune);
						}
			}).data("ui-autocomplete")._renderItem = function( ul, item ) {
						return $( "<li id='val"+ item.value +"'>" ).append("<a><strong>" + item.value + "</strong> - " + item.label).appendTo( ul );
			}
</script>
<? } ?>
