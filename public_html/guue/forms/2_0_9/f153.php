<?
	include 'form_common.php';
	$_SESSION["guue"]["form_model_no"] = 2;
?>
<h2 style="text-align: right;"><b>Avviso volontario per la trasparenza ex ante</b></h2>
<input type="hidden" name="tipologia_form" value="F15_2014">
<input type="hidden" name="numero_form" value="153">
<input type="hidden" name="v_form" value="2_0_9">
<input id="stato_modello" type="hidden" name="bozza" value="0">
<input type="hidden" name="codice" value="<?= $codice_pubblicazione ?>">
<input type="hidden" name="codice_gara" value="<?= $codice_gara ?>">
<input type="hidden" name="attributi_form[CATEGORY]" value="ORIGINAL">
<input type="hidden" name="attributi_form[FORM]" value="F15">
<input type="hidden" name="attributi_form[LG]" value="IT">
<input type="hidden" name="guue[DIRECTIVE][ATTRIBUTE][VALUE]" value="2014/25/UE">
<p style="text-align: right; margin-top: 0px">Direttiva 2014/25/UE</p>
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
	$_SESSION["guue"]["numero_del_form"] = "f15-3";
		
	$f03 = array(
		0 => array(
			0 => "<h2><b>Sezione I: Amministrazione aggiudicatrice</b></h2>",
			1 => array(
				0 => "sezioni/sezione_1_1.php",
				// 1 => "sezioni/sezione_1_2.php",
				// 3 => "sezioni/sezione_1_4.php",
				// 4 => "sezioni/sezione_1_5.php",
				5 => "sezioni/sezione_1_6.php",
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
					7 => "sezioni/sezione_2_1_7.php",
					6 => "sezioni/sezione_2_1_6.php",
					8 => '</tbody></table>',
					),
				2 => array(
					0 => "<h3><b>II.2) Descrizione</b></h3>",
					1 => "common/more_lots_button.php",
					2 => array(),
					3 => "common/more_lots.php",
					4 => "common/more_lots_button.php",
					)
				)
			),
		2 => array(
			0 => "<h2><b>Sezione IV: Procedura</b></h2>",
			1 => array(
				0 => array(
					0 => "<h3><b>IV.1) Descrizione</b></h3>",
					1 => '<table class="bordered"><tbody>',
					2 => "sezioni/sezione_4_1_1_f15-3.php",
					3 => "sezioni/sezione_4_1_3_f03.php",
					// 6 => "sezioni/sezione_4_1_6_f03.php",
					7 => "sezioni/sezione_4_1_8.php",
					8 => '</tbody></table>',
					),
				1 => array(
					0 => "<h3><b>IV.2) Informazioni di carattere amministrativo</b></h3>",
					1 => '<table class="bordered"><tbody>',
					2 => "sezioni/sezione_4_2_1.php",
					// 3 => "sezioni/sezione_4_2_8.php",
					// 4 => "sezioni/sezione_4_2_9.php",
					5 => '</tbody></table>',
					),
				)
			),
		3 => array(
			0 => "<h2><b>Sezione V: Aggiudicazione di appalto</b></h2>",
			1 => "common/more_lots_button_award.php",
			2 => array(),
			3 => "common/more_lots_award.php",
			4 => "common/more_lots_button_award.php",
			),
		4 => array(
			0 => "<h2><b>Sezione VI: Altre informazioni</b></h2>",
			1 => array(
				2 => "sezioni/sezione_6_3.php",
				3 => array(
					0 => "<h3><b>VI.4) Procedure di ricorso:</b></h3>",
					1 => '<table class="bordered"><tbody>',
					2 => "sezioni/sezione_6_4_1.php",
					3 => "sezioni/sezione_6_4_2.php",
					4 => "sezioni/sezione_6_4_3.php",
					5 => "sezioni/sezione_6_4_4.php",
					6 => '</tbody></table>',
					),
				4 => "sezioni/sezione_6_5.php",
				)
			),
		5 => array(
			0 => '<div id="allegato_d">',
			1 => '',
			2 => '</div>'
			)
		);

	if(!empty($_SESSION["guue"]["codice_gara"])) {
		$record_gara = $_SESSION["guue"]["gara"];
		$sql = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara";
		$ris = $pdo->bindAndExec($sql, array(':codice_gara' => $record_gara["codice"]));
		if($ris->rowCount() > 0) {
			$j = 1;
			while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
				$f03[1][1][2][2][] = array(
					"index" => $j,
					0 => '<table id="lot_no_'.$j.'" class="bordered"><tbody>',
					1 => "sezioni/sezione_2_2_1.php",
					2 => "sezioni/sezione_2_2_2.php",
					3 => "sezioni/sezione_2_2_3.php",
					4 => "sezioni/sezione_2_2_4.php",
					5 => "sezioni/sezione_2_2_5.php",
					10 => "sezioni/sezione_2_2_11_f01.php",
					12 => "sezioni/sezione_2_2_13.php",
					13 => "sezioni/sezione_2_2_14.php",
					14 => '</tbody></table><div id="padding_lot_no_'.$j.'" class="padding"></div>',
				);
				$f03[3][2][] = array(
					"index" => $j,
					0 => '<table id="lot_award_no_'.$j.'" class="bordered"><tbody>',
					1 => "sezioni/sezione_5_f151.php",
					14 => '</tbody></table><div id="padding_lot_no_1" class="padding"></div>',
					);
				$j++;
			}
		}
	} elseif(!empty($_SESSION["guue"]["codice_pubblicazione"])) {
		if(!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"])) {
			$j = 1;
			foreach ($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"] as $key_lot => $lot) {
				if(strpos($key_lot,"ITEM_") !== FALSE) {
					$f03[1][1][2][2][] = array(
						"index" => $j,
						0 => '<table id="lot_no_'.$j.'" class="bordered"><tbody>',
						1 => "sezioni/sezione_2_2_1.php",
						2 => "sezioni/sezione_2_2_2.php",
						3 => "sezioni/sezione_2_2_3.php",
						4 => "sezioni/sezione_2_2_4.php",
						5 => "sezioni/sezione_2_2_5.php",
						10 => "sezioni/sezione_2_2_11_f01.php",
						12 => "sezioni/sezione_2_2_13.php",
						13 => "sezioni/sezione_2_2_14.php",
						14 => '</tbody></table><div id="padding_lot_no_'.$j.'" class="padding"></div>',
					);

					// $f03[3][2][] = array(
					// 	"index" => $j,
					// 	0 => '<table id="lot_no_'.$j.'" class="bordered"><tbody>',
					// 	1 => "sezioni/sezione_5.php",
					// 	2 => "sezioni/sezione_5_1.php",
					// 	3  => '<tr><td colspan="4"><h3><b>V.2) Aggiudicazione di appalto</b></h3></td></tr>',
					// 	4 => "sezioni/sezione_5_2_1.php",
					// 	5 => "sezioni/sezione_5_2_2.php",
					// 	10 => "sezioni/sezione_5_2_3.php",
					// 	12 => "sezioni/sezione_5_2_4.php",
					// 	13 => "sezioni/sezione_5_2_5.php",
					// 	14 => '</tbody></table><div id="padding_lot_no_'.$j.'" class="padding"></div>',
					// 	);
					$f03[3][2][] = array(
						"index" => $j,
						0 => '<table id="lot_award_no_'.$j.'" class="bordered"><tbody>',
						1 => "sezioni/sezione_5_f151.php",
						14 => '</tbody></table><div id="padding_lot_no_1" class="padding"></div>',
						);
					$j++;
				}
			}
		}
	} else {
		$f03[1][1][2][2][] = array(
			"index" => 1,
			0 => '<table id="lot_no_1" class="bordered"><tbody>',
			1 => "sezioni/sezione_2_2_1.php",
			2 => "sezioni/sezione_2_2_2.php",
			3 => "sezioni/sezione_2_2_3.php",
			4 => "sezioni/sezione_2_2_4.php",
			5 => "sezioni/sezione_2_2_5.php",
			10 => "sezioni/sezione_2_2_11_f01.php",
			12 => "sezioni/sezione_2_2_13.php",
			13 => "sezioni/sezione_2_2_14.php",
			14 => '</tbody></table><div class="padding"></div>',
		);

		$f03[3][2][] = array(
			"index" => 1,
			0 => '<table id="lot_award_no_1" class="bordered"><tbody>',
			1 => "sezioni/sezione_5_f151.php",
			14 => '</tbody></table><div id="padding_lot_no_1" class="padding"></div>',
			);
	}

	if(!empty($guue["PROCEDURE"]["DIRECTIVE_2014_25_EU"]["radio_as_select_for_procedure_type"])) {
		if($guue["PROCEDURE"]["DIRECTIVE_2014_25_EU"]["radio_as_select_for_procedure_type"] == "PT_NEGOTIATED_WITHOUT_PUBLICATION") {
			$f03[5][1] = "sezioni/annex_d2_part1.php";
		} else if ($guue["PROCEDURE"]["DIRECTIVE_2014_25_EU"]["radio_as_select_for_procedure_type"] == "PT_AWARD_CONTRACT_WITHOUT_CALL") {
			$f03[5][1] = "sezioni/annex_d2_part2.php";
		}
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
