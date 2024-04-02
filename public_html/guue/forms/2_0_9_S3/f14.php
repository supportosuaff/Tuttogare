<?
	include 'form_common.php';
	$_SESSION["guue"]["form_model_no"] = 2;
?>
<h2 style="text-align: right;"><b>Rettifica</b></h2>
<p style="text-align: right; margin-top: 0px">
	Avviso relativo a informazioni complementari o modifiche<br>Direttiva 2014/24/UE<br><br>
	<i>Attenzione: qualora le correzioni o le modifiche degli avvisi comportino cambiamenti sostanziali delle condizioni di concorrenza,<br>
	&egrave; necessario prorogare le scadenze previste o avviare una nuova procedura.</i>
</p>
<input type="hidden" name="tipologia_form" value="F14_2014">
<input type="hidden" name="numero_form" value="14">
<input type="hidden" name="v_form" value="2_0_9_S3">
<input id="stato_modello" type="hidden" name="bozza" value="0">
<input type="hidden" name="codice" value="<?= $codice_pubblicazione ?>">
<input type="hidden" name="codice_gara" value="<?= $codice_gara ?>">
<input type="hidden" name="attributi_form[CATEGORY]" value="ORIGINAL">
<input type="hidden" name="attributi_form[FORM]" value="F14">
<input type="hidden" name="attributi_form[LG]" value="IT">
<input type="hidden" name="guue[LEGAL_BASIS][ATTRIBUTE][VALUE]" value="32014L0024">
<?

	$rel = array(
		"ADDRS5-OFFICIALNAME",
		"ADDRS5-TOWN",
		"ADDRS5-NUTS",
		"ADDRS5-COUNTRY",
		"ADDRS1-OFFICIALNAME",
		"ADDRS1-TOWN",
		"ADDRS1-NUTS",
		"ADDRS1-COUNTRY",
		"ADDRS1-E_MAIL",
		"ADDRS1-URL_GENERAL",
		"ADDRS1-CONTACT_POINT",
		"ADDRS1-URL_BUYER",
		"ADDRS6-OFFICIALNAME",
		"ADDRS6-TOWN",
		"ADDRS6-COUNTRY",
		"radio_as_select_for_document_url_opt",
		"radio_as_select_for_information",
		"radio_as_select_for_tenders_request",
		"radio_as_select_for_ca_type",
		"radio_as_select_for_ca_activity",
		"OBJECT_CONTRACT-CPV_CODE",
		"radio_as_select_for_type_contract",
		"OBJECT_CONTRACT-SHORT_DESCR",
		"OBJECT_CONTRACT-CPV_CODE",
		"OBJECT_CONTRACT-NUTS",
		"OBJECT_CONTRACT-SHORT_DESCR",
		"OBJECT_CONTRACT-radio_as_select_for_award_criteria_doc",
		"durata_del_contratto",
		"radio_as_select_for_variants",
		"radio_as_select_for_options",
		"radio_as_select_for_eu_union_funds",
		"radio_as_select_for_procedure_type",
		"radio_as_select_for_public_agreement",
		"DATE_RECEIPT_TENDERS",
		"radio_as_select_for_recurrent_procurement",
		"DATE_DISPATCH_NOTICE",
		"TIME_RECEIPT_TENDERS",
		"PROCEDURE-LANGUAGE",
		"total_final_value_of_the_contract"
		);

	$_SESSION["guue"]["rel"] = $rel;
		
	$f03 = array(
		0 => array(
			0 => "<h2><b>Sezione I: Amministrazione aggiudicatrice</b></h2>",
			1 => array(
				0 => "sezioni/sezione_1_1.php",
				)
			),
		1 => array(
			0 => "<h2><b>Sezione II: Oggetto</b></h2>",
			1 => array(
				0 => "categorie/form.php",
				1 => array(
					0 => "<h3><b>II.1) Entit&agrave; dell&#39;appalto</b></h3>",
					1 => '<table class="bordered"><tbody>',
					2 => "sezioni/sezione_2_1_1.php",
					3 => "sezioni/sezione_2_1_2.php",
					4 => "sezioni/sezione_2_1_3.php",
					5 => "sezioni/sezione_2_1_4.php",
					8 => '</tbody></table>',
					),
				)
			),
		2 => array(
			0 => "<h2><b>Sezione VI: Altre informazioni</b></h2>",
			1 => array(
				1 => "sezioni/sezione_6_5.php",
				2 => array(
					0 => "<h3><b>VI.6) Riferimento dell&#39;avviso originale</b></h3>",
					1 => '<table class="bordered"><tbody>',
					2 => 'sezioni/sezione_6_6.php',
					8 => '</tbody></table>',
					)
				)
			),
		3 => array(
			0 => "<h2><b>Sezione VII: Modifiche</b></h2>",
			1 => array(
				1 => array(
					0 => "<h3><b>VII.1) Informazioni da correggere o aggiungere</b></h3>",
					1 => '<table class="bordered"><tbody>',
					2 => 'sezioni/sezione_7_1.php',
					3 => 'sezioni/sezione_7_2.php',
					8 => '</tbody></table>',
					)
				)
			)
		);

	if(!empty($_SESSION["guue"]["codice_gara"])) {
		$record_gara = $_SESSION["guue"]["gara"];
		$sql = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara";
		$ris = $pdo->bindAndExec($sql, array(':codice_gara' => $record_gara["codice"]));
		if($ris->rowCount() > 0) {
			$j = 1;
			while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
				
			}
		}
	} elseif(!empty($_SESSION["guue"]["codice_pubblicazione"])) {
		if(!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"])) {
			$j = 1;
			foreach ($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"] as $key_lot => $lot) {
				if(strpos($key_lot,"ITEM_") !== FALSE) {
					
				}
			}
		}
	} else {

	}

	$_SESSION["guue"]["json_form"] = $f03;
	loadForm($f03);
	?>
	<input type="submit" class="submit_big" value="Salva una bozza" onclick="salvabozza()">
	<input type="submit" class="submit_big" style="background-color:#0C0;" value="Verifica e salva per l&#39;invio">
	<?
	if(!empty($codice_gara)) {
		?>
		<script>
			var modifica = false;
			$("* :input").change(function() {
				modifica = true;
			});

			function return_pannello() {
				window.location.href = "/gare/pannello.php?codice=<?= $codice_gara ?>";
			}

			function ritorna() {
				if (modifica) {
					jconfirm("<strong>Attenzione!</strong> Non hai salvato le modifiche.<br>Vuoi tornare al pannello?",return_pannello);
				} else {
					return_pannello()
				}
			}
		</script>
		<input type="button" class="espandi ritorna_button submit_big" style="background-color:#999;" value="Ritorna al pannello" onClick="ritorna()">
	<?
	}
?>
