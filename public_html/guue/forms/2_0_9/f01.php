<?
	include 'form_common.php';
?>
<h2 style="text-align: right;"><b>Avviso di Preinformazione</b></h2>
<input type="hidden" name="tipologia_form" value="F01_2014">
<input type="hidden" name="numero_form" value="101">
<input type="hidden" name="v_form" value="2_0_9">
<input id="stato_modello" type="hidden" name="bozza" value="0">
<input type="hidden" name="codice" value="<?= $codice_pubblicazione ?>">
<input type="hidden" name="codice_gara" value="<?= $codice_pubblicazione ?>">
<input type="hidden" name="attributi_form[CATEGORY]" value="ORIGINAL">
<input type="hidden" name="attributi_form[FORM]" value="F01">
<input type="hidden" name="attributi_form[LG]" value="IT">
<p style="text-align: right;" title="Tipologia di Avviso" class="valida" rel="S;0;0;checked;group_validate">
	Direttiva 2014/24/UE<br>
	<label>Il presente avviso &egrave; soltanto un avviso di preinformazione <input type="radio" <?= !empty($guue["NOTICE"]["ATTRIBUTE"]["TYPE"]) && $guue["NOTICE"]["ATTRIBUTE"]["TYPE"] == "PRI_ONLY" ? 'checked="checked"' : null  ?> name="guue[NOTICE][ATTRIBUTE][TYPE]" value="PRI_ONLY"></label><br>
	<label>Lo scopo del presente avviso &egrave; ridurre i termini per la ricezione delle offerte <input type="radio" <?= !empty($guue["NOTICE"]["ATTRIBUTE"]["TYPE"]) && $guue["NOTICE"]["ATTRIBUTE"]["TYPE"] == "PRI_REDUCING_TIME_LIMITS" ? 'checked="checked"' : null  ?> name="guue[NOTICE][ATTRIBUTE][TYPE]" value="PRI_REDUCING_TIME_LIMITS"></label><br>
	<label>Il presente avviso &egrave; un avviso di indizione di gara <input type="radio" name="guue[NOTICE][ATTRIBUTE][TYPE]" <?= !empty($guue["NOTICE"]["ATTRIBUTE"]["TYPE"]) && $guue["NOTICE"]["ATTRIBUTE"]["TYPE"] == "PRI_CALL_COMPETITION" ? 'checked="checked"' : null  ?> value="PRI_CALL_COMPETITION"><br></label><br>
	<i style="font-size: 12px; text-align: right;">
		Gli operatori interessati devono informare l&#39;autorità aggiudicatrice del loro interesse per i contratti d&#39;appalto.<br>
		I contratti d&#39;appalto saranno aggiudicati senza successiva pubblicazione di un avviso di indizione di gara.
	</i>
</p>
<?
	$rel = array(
		"OBJECT_CONTRACT-TITLE",
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
	$_SESSION["guue"]["numero_del_form"] = "f06";

	$f01 = array(
		0 => array(
			0 => "<h2><b>Sezione I: Amministrazione aggiudicatrice</b></h2>",
			1 => array(
				0 => "sezioni/sezione_1_1.php",
				1 => "sezioni/sezione_1_2.php",
				2 => "sezioni/sezione_1_3.php",
				3 => "sezioni/sezione_1_4.php",
				4 => "sezioni/sezione_1_5.php",
				)
			),
		1 => array(
			0 => "<h2><b>Sezione II: Oggetto</b></h2>",
				1 => array(
					0 => '<input type="hidden" name="guue[OBJECT_CONTRACT][ATTRIBUTE][ITEM]" value="1">',
					1 => "categorie/form.php",
					2 => array(
						0 => "<h3><b>II.1) Entit&agrave; dell&#39;appalto</b></h3>",
						1 => '<table class="bordered"><tbody>',
						2 => "sezioni/sezione_2_1_1.php",
						3 => "sezioni/sezione_2_1_2.php",
						4 => "sezioni/sezione_2_1_3.php",
						5 => "sezioni/sezione_2_1_4.php",
						6 => "sezioni/sezione_2_1_5.php",
						7 => "sezioni/sezione_2_1_6.php",
						8 => '</tbody></table>',
						),
					3 => array(
						0 => "<h3><b>II.2) Descrizione</b></h3>",
						1 => "common/more_lots_button.php",
						2 => array(
							"index" => 1,
							0 => '<input type="hidden" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ATTRIBUTE][ITEM]" value="1">',
							1 => '<table class="bordered"><tbody>',
							2 => "sezioni/sezione_2_2_1.php",
							3 => "sezioni/sezione_2_2_2.php",
							4 => "sezioni/sezione_2_2_3.php",
							5 => "sezioni/sezione_2_2_4.php",
							6 => "sezioni/sezione_2_2_5.php",
							7 => "sezioni/sezione_2_2_6.php",
							8 => "sezioni/sezione_2_2_7.php",
							9 => "sezioni/sezione_2_2_10.php",
							10 => "sezioni/sezione_2_2_11.php",
							11 => "sezioni/sezione_2_2_13.php",
							12 => "sezioni/sezione_2_2_14.php",
							13 => '</tbody></table>',
							),
						3 => "common/more_lots.php",
						4 => "common/more_lots_button.php",
						),
					4 => "sezioni/sezione_2_3.php",
					)
			),
		2 => array(
			0 => "<h2><b>Sezione III: Informazioni di carattere giuridico, economico, finanziario e tecnico</b></h2>",
			1 => array(
				0 => array(
					0 => "<h3><b>III.1) Condizioni di partecipazione</b></h3>",
					1 => '<table class="bordered"><tbody>',
					2 => "sezioni/sezione_3_1_1.php",
					3 => "sezioni/sezione_3_1_2.php",
					4 => "sezioni/sezione_3_1_3.php",
					5 => "sezioni/sezione_3_1_5.php",
					6 => '</tbody></table>',
					),
				1 => array(
					0 => "<h3><b>III.2) Condizioni relative al contratto d&#39;appalto</b></h3>",
					1 => '<table class="bordered"><tbody>',
					2 => "sezioni/sezione_3_2_1.php",
					3 => "sezioni/sezione_3_2_2.php",
					4 => "sezioni/sezione_3_2_3.php",
					5 => '</tbody></table>',
					),
				)
			),
		3 => array(
			0 => "<h2><b>Sezione IV: Procedura</b></h2>",
			1 => array(
				0 => array(
					0 => "<h3><b>IV.1) Descrizione</b></h3>",
					1 => '<table class="bordered"><tbody>',
					2 => "sezioni/sezione_4_1_1.php",
					3 => "sezioni/sezione_4_1_3.php",
					4 => "sezioni/sezione_4_1_6.php",
					5 => "sezioni/sezione_4_1_8.php",
					6 => '</tbody></table>',
					),
				1 => array(
					0 => "<h3><b>IV.2) Informazioni di carattere amministrativo</b></h3>",
					1 => '<table class="bordered"><tbody>',
					2 => "sezioni/sezione_4_2_2.php",
					3 => "sezioni/sezione_4_2_4.php",
					4 => "sezioni/sezione_4_2_5.php",
					5 => '</tbody></table>',
					),
				)
			),
		4 => array(
			0 => "<h2><b>Sezione VI: Altre informazioni</b></h2>",
			1 => array(
				0 => "sezioni/sezione_6_2.php",
				1 => "sezioni/sezione_6_3.php",
				2 => array(
					0 => "<h3><b>VI.4) Procedure di ricorso:</b></h3>",
					1 => '<table class="bordered"><tbody>',
					2 => "sezioni/sezione_6_4_1.php",
					3 => "sezioni/sezione_6_4_2.php",
					4 => "sezioni/sezione_6_4_3.php",
					5 => "sezioni/sezione_6_4_4.php",
					6 => "sezioni/sezione_6_5.php",
					7 => '</tbody></table>',
					),
				)
			)
		);

	$_SESSION["guue"]["form"] = $f01;
	loadForm($f01);
	?>
	<input type="submit" class="submit_big" style="background-color:#66FF66;" value="Verifica e salva per l&#39;invio">
	<input type="submit" class="submit_big" value="Salva una bozza" onclick="salvabozza()">
	<?
?>
