<?
	if (isset($_POST["id"])) {
		if (!isset($pdo)) {
			session_start();
			include("../../../config.php");
			include_once($root."/inc/funzioni.php");
;
		}
		$record_incarico = get_campi("b_incaricati");
		$record_incarico["data_atto"] = "";
		$record_incarico["numero_atto"] = "";
		$record_incarico["ruolo"] = "";
		$id = $_POST["id"];
		$bind=array();
	} else if (isset($_GET["codice"]) && empty($new_line)) {
		if (!isset($pdo)) {
			session_start();
			include("../../../config.php");
			include_once($root."/inc/funzioni.php");
;
		}
		$bind=array();
		$bind[":codice"] = $_GET["codice"];
		$sql = "SELECT b_incaricati.*, r_incarichi.ruolo, r_incarichi.numero_atto, r_incarichi.data_atto, r_incarichi.codice AS codice_incarico FROM b_incaricati JOIN r_incarichi ON b_incaricati.codice = r_incarichi.codice_incaricato
		WHERE r_incarichi.codice = :codice ";
		$ris_incarichi = $pdo->bindAndExec($sql,$bind);
		if ($ris_incarichi->rowCount()>0) {
			$record_incarico = $ris_incarichi->fetch(PDO::FETCH_ASSOC);
			$id = $record_incarico["codice_incarico"];
		}
	}
	$lock = false;
	$lock_ruolo = false;
	if (isset($record_incarico)) {
		$ruoli = getListeSIMOG()["RuoloResponsabileType"];
		if (!empty($record_incarico["ruolo"]) && ($record_incarico["ruolo"] == "14")) $lock_ruolo = true;
?>
<div id="incarico_<? echo $id ?>" class="box edit-box" style="border-left:5px solid #999;">
  <table width="100%" id="tabella_<? echo $id ?>">
		<tr>
			<td class="etichetta">Ruolo</td>
			<td colspan="4">
				<select name="incarico[<? echo $id ?>][ruolo]"  title="Ruolo" rel="S;1;0;N" id="ruolo_incarico_<? echo $id ?>">
					<? if (!$lock_ruolo) { ?>
						<option value="">Seleziona...</option>
						<?
							foreach($ruoli AS $codice_ruolo => $etichetta) {
						?>
							<option value="<?= $codice_ruolo ?>"><?= $etichetta ?></option>
						<? }
						} else { ?>
						<option value="<?= $record_incarico["ruolo"] ?>"><?= $ruoli[$record_incarico["ruolo"]] ?></option>
					<? } ?>
				</select>
				<?
					if (!empty($record_incarico["ruolo"])) { ?>
						<script>
							$("#ruolo_incarico_<? echo $id ?>").val("<?= $record_incarico["ruolo"] ?>");
						</script>
					<? } ?>
			</td>
			<? if (!$lock && !$lock_ruolo) { ?>
				<td rowspan="4" width="10" style="text-align:center"><input type="image" onClick="elimina('<? echo $id ?>','esecuzione/incarichi');return false;" src="/img/del.png" title="Elimina"></td>
			<? } ?>
		</tr>
		<tr>
			<td class="etichetta">Codice Fiscale</td>
			<td class="etichetta">Cognome</td>
			<td class="etichetta">Nome</td>
			<td class="etichetta">Atto</td>
			<td class="etichetta">Datto</td>

		</tr>
		<tr>
			<td>
        <input type="hidden" name="incarico[<? echo $id ?>][id]" id="id_incarico_<? echo $id ?>" value="<? echo $id ?>">
        <input type="hidden" name="incarico[<? echo $id ?>][codice]" id="codice_incarico_<? echo $id ?>" value="<? echo $record_incarico["codice"] ?>">
				<input type="hidden" name="incarico[<? echo $id ?>][codice_relazione]" id="codice_relazione_incarico_<? echo $id ?>" value="<? echo $record_incarico["codice_incarico"] ?>">
        <input type="text" style="font-weight:bold" class="codice_fiscale" size="16" name="incarico[<? echo $id ?>][codice_fiscale]"  title="Codice fiscale" rel="S;11;0;PICF" id="codice_fiscale_<? echo $id ?>" value="<? echo $record_incarico["codice_fiscale"] ?>">
			</td>
			<td>
				<input type="text" style="font-weight:bold; width:99%" name="incarico[<? echo $id ?>][cognome]"  title="Cognome" rel="S;3;50;A" id="cognome_incarico_<? echo $id ?>" value="<? echo $record_incarico["cognome"] ?>">
			</td>
			<td>
				<input type="text" style="font-weight:bold; width:99%" name="incarico[<? echo $id ?>][nome]"  title="Nome" rel="S;3;50;A" id="nome_incarico_<? echo $id ?>" value="<? echo $record_incarico["nome"] ?>">
			</td>
			<td>
				<input type="text" style="font-weight:bold; width:99%" name="incarico[<? echo $id ?>][numero_atto]"  title="Numero Atto" rel="N;1;50;A" id="numero_atto_incarico_<? echo $id ?>" value="<? echo $record_incarico["numero_atto"] ?>">
			</td>
			<td>
				<input type="text" style="font-weight:bold; width:99%" class="datepick" name="incarico[<? echo $id ?>][data_atto]"  title="Data Atto" rel="N;10;10;D" id="data_atto_incarico_<? echo $id ?>" value="<? echo mysql2date($record_incarico["data_atto"]) ?>">
			</td>
		</tr>
		<tr>
			<td colspan="5">
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
							<input type="text" style="font-weight:bold; width:99%" name="incarico[<? echo $id ?>][telefono]"  title="Telefono" rel="N;1;20;A" id="telefono_incarico_<? echo $id ?>" value="<? echo $record_incarico["telefono"] ?>">
						</td>
						<td>
							<input type="text" style="font-weight:bold; width:99%" name="incarico[<? echo $id ?>][email]"  title="email" rel="N;1;64;E" id="email_incarico_<? echo $id ?>" value="<? echo $record_incarico["email"] ?>">
						</td>
						<td>
							<input type="text" style="font-weight:bold; width:99%" name="incarico[<? echo $id ?>][fax]"  title="Fax" rel="N;1;20;A" id="fax_incarico_<? echo $id ?>" value="<? echo $record_incarico["fax"] ?>">
						</td>
						<td>
							<input type="text" style="font-weight:bold; width:99%" name="incarico[<? echo $id ?>][indirizzo]"  title="Indirizzo" rel="N;3;100;A" id="indirizzo_incarico_<? echo $id ?>" value="<? echo $record_incarico["indirizzo"] ?>">
						</td>
						<td>
							<input type="text" style="font-weight:bold; width:99%" name="incarico[<? echo $id ?>][comune]"  title="Comune" rel="N;3;100;A" id="comune_incarico_<? echo $id ?>" value="<? echo $record_incarico["comune"] ?>">
						</td>
						<td>
							<input type="text" style="font-weight:bold; width:99%" name="incarico[<? echo $id ?>][cap]"  title="CAP" rel="N;3;50;A" id="cap_incarico_<? echo $id ?>" value="<? echo $record_incarico["cap"] ?>">
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
							url: "/esecuzione/incarichi/incaricati.php",
							dataType: "json",
							data: {
								term : request.term,
							},
							success: function(data) {
								response(data);
							}
							});
						},
						minLenght: 14,
						search  : function(){$(this).addClass('working');},
						open    : function(){$(this).removeClass('working');},
						select: function(e, ui) {
							//e.preventDefault() // <--- Prevent the value from being inserted.
							$("#codice_fiscale_incarico_<? echo $id ?>").val(ui.item.value);
							$("#cognome_incarico_<? echo $id ?>").val(ui.item.cognome);
							$("#nome_incarico_<?= $id ?>").val(ui.item.nome);
							$("#codice_incarico_<?= $id ?>").val(ui.item.codice);
							$("#telefono_incarico_<?= $id ?>").val(ui.item.telefono);
							$("#email_incarico_<?= $id ?>").val(ui.item.email);
							$("#fax_incarico_<?= $id ?>").val(ui.item.fax);
							$("#indirizzo_incarico_<?= $id ?>").val(ui.item.indirizzo);
							$("#cap_incarico_<?= $id ?>").val(ui.item.cap);
							$("#comune_incarico_<?= $id ?>").val(ui.item.comune);
							$(this).focus();
						},
						focus: function(e, ui) {
							//e.preventDefault() // <--- Prevent the value from being inserted.
							$("#codice_fiscale_incarico_<? echo $id ?>").val(ui.item.value);
							$("#cognome_incarico_<? echo $id ?>").val(ui.item.cognome);
							$("#nome_incarico_<?= $id ?>").val(ui.item.nome);
							$("#codice_incarico_<?= $id ?>").val(ui.item.codice);
							$("#telefono_incarico_<?= $id ?>").val(ui.item.telefono);
							$("#email_incarico_<?= $id ?>").val(ui.item.email);
							$("#fax_incarico_<?= $id ?>").val(ui.item.fax);
							$("#indirizzo_incarico_<?= $id ?>").val(ui.item.indirizzo);
							$("#cap_incarico_<?= $id ?>").val(ui.item.cap);
							$("#comune_incarico_<?= $id ?>").val(ui.item.comune);
						}
			}).data("ui-autocomplete")._renderItem = function( ul, item ) {
						return $( "<li id='val"+ item.value +"'>" ).append("<a><strong>" + item.value + "</strong> - " + item.label).appendTo( ul );
			}
</script>
<? } ?>
