<?
	include 'form_common.php';
?>
<h2 style="text-align: right;"><b>Avviso di modifica - Modifica di appalto/concessione durante il periodo di validit&agrave;</b></h2>
<input type="hidden" name="tipologia_form" value="F20_2014">
<input type="hidden" name="numero_form" value="201">
<input type="hidden" name="v_form" value="2_0_9_S2">
<input id="stato_modello" type="hidden" name="bozza" value="0">
<input type="hidden" name="codice" value="<?= $codice_pubblicazione ?>">
<input type="hidden" name="codice_gara" value="<?= $codice_gara ?>">
<input type="hidden" name="attributi_form[CATEGORY]" value="ORIGINAL">
<input type="hidden" name="attributi_form[FORM]" value="F20">
<input type="hidden" name="attributi_form[LG]" value="IT">
<input type="hidden" name="guue[DIRECTIVE][ATTRIBUTE][VALUE]" value="2014/24/EU">
<p style="text-align: right;">
	Direttiva 2014/24/UE<br>
</p>
<?
	$rel = array(
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
		"URL_DOCUMENT",
		"radio_as_select_for_document_url_opt",
		"radio_as_select_for_information",
		"radio_as_select_for_tenders_request",
		"radio_as_select_for_ca_type",
		"radio_as_select_for_ca_activity",
		"radio_as_select_for_renewal",
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
		"OBJECT_CONTRACT-radio_as_select_for_lot_division"
		);

	$_SESSION["guue"]["rel"] = $rel;
	$_SESSION["guue"]["numero_del_form"] = "f21-1";

	$f21 = array(
		0 => array(
			0 => "<h2><b>Sezione I: Amministrazione aggiudicatrice</b></h2>",
			1 => array(
				0 => "sezioni/sezione_1_1.php",
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
						9 => '</tbody></table>',
						),
					3 => array(
						0 => "<h3><b>II.2) Descrizione</b></h3>",
						1 => "common/more_lots_button.php",
						2 => array(),
						3 => "common/more_lots.php",
						4 => "common/more_lots_button.php",
						),
					4 => "sezioni/sezione_2_3.php",
					)
			),
		2 => array(
			0 => "<h2><b>Sezione IV: Procedura</b></h2>",
			1 => array(
				1 => array(
					0 => "<h3><b>IV.2) Informazioni di carattere amministrativo</b></h3>",
					1 => '<table class="bordered"><tbody>',
					2 => "sezioni/sezione_4_2_1.php",
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
				// 0 => "sezioni/sezione_6_2.php",
				1 => "sezioni/sezione_6_3.php",
				2 => array(
					0 => "<h3><b>VI.4) Procedure di ricorso:</b></h3>",
					1 => '<table class="bordered"><tbody>',
					2 => "sezioni/sezione_6_4_1.php",
					3 => "sezioni/sezione_6_4_2.php",
					4 => "sezioni/sezione_6_4_3.php",
					5 => "sezioni/sezione_6_4_4.php",
					7 => '</tbody></table>',
					),
				3 => "sezioni/sezione_6_5.php",
				)
			),
		5 => array(
			0 => "<h3><b>Sezione VII: Modifiche all&#39;appalto/concessione</b></h3>",
			1 => array(
				0 => "<h3><b>VII.1) Descrizione dell&#39;appalto dopo le modifiche</b></h3>",
				1 => '<table class="bordered"><tbody>',
				2 => "sezioni/sezione_7_1_1.php",
				// 3 => "sezioni/sezione_7_1_2.php",
				4 => "sezioni/sezione_7_1_3.php",
				5 => "sezioni/sezione_7_1_4.php",
				6 => "sezioni/sezione_7_1_5_f202.php",
				7 => "sezioni/sezione_7_1_6.php",
				8 => "sezioni/sezione_7_1_7.php",
				9 => '</tbody></table>',
				),
			2 => array(
				0 => "<h3><b>VII.2) Informazioni relative alle modifiche</b></h3>",
				1 => '<table class="bordered"><tbody>',
				2 => "sezioni/sezione_7_2_1.php",
				3 => "sezioni/sezione_7_2_2.php",
				4 => "sezioni/sezione_7_2_3.php",
				9 => '</tbody></table>',
				)
			)
		);

	if(!empty($_SESSION["guue"]["codice_pubblicazione"])) {
    if(!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"])) {
      $j = 1;
      foreach ($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"] as $key_lot => $lot) {
        if(strpos($key_lot,"ITEM_") !== FALSE) {
          $f21[1][1][3][2][] = array(
            "index" => 1,
            0 => '<input type="hidden" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ATTRIBUTE][ITEM]" value="1">',
            1 => '<table id="lot_no_'.$j.'" class="bordered"><tbody>',
            2 => "sezioni/sezione_2_2_1.php",
            3 => "sezioni/sezione_2_2_2.php",
            4 => "sezioni/sezione_2_2_3.php",
            5 => "sezioni/sezione_2_2_4.php",
            // 6 => "sezioni/sezione_2_2_5.php",
            // 7 => "sezioni/sezione_2_2_6.php",
            8 => "sezioni/sezione_2_2_7_f202.php",
            // 9 => "sezioni/sezione_2_2_10_f01.php",
            // 10 => "sezioni/sezione_2_2_11_f01.php",
            // 11 => "sezioni/sezione_2_2_13.php",
            12 => "sezioni/sezione_2_2_14.php",
            13 => '</tbody></table>',
          );

          $f21[3][2][] = array(
            "index" => $j,
            0 => '<table id="lot_award_no_'.$j.'" class="bordered"><tbody>',
            1 => "sezioni/sezione_5_f201.php",
            14 => '</tbody></table><div id="padding_lot_no_1" class="padding"></div>',
            );
          $j++;
        }
      }
    }
  } elseif(!empty($_SESSION["guue"]["codice_gara"])) {
		$record_gara = $_SESSION["guue"]["gara"];
		$sql = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara";
		$ris = $pdo->bindAndExec($sql, array(':codice_gara' => $record_gara["codice"]));
		if($ris->rowCount() > 0) {
			$j = 1;
			while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
				$f21[1][1][3][2][] = array(
					"index" => 1,
					0 => '<input type="hidden" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ATTRIBUTE][ITEM]" value="1">',
					1 => '<table id="lot_no_'.$j.'" class="bordered"><tbody>',
					2 => "sezioni/sezione_2_2_1.php",
					3 => "sezioni/sezione_2_2_2.php",
					4 => "sezioni/sezione_2_2_3.php",
					5 => "sezioni/sezione_2_2_4.php",
					// 6 => "sezioni/sezione_2_2_5.php",
					// 7 => "sezioni/sezione_2_2_6.php",
					8 => "sezioni/sezione_2_2_7_f202.php",
					// 9 => "sezioni/sezione_2_2_10_f01.php",
					// 10 => "sezioni/sezione_2_2_11_f01.php",
					// 11 => "sezioni/sezione_2_2_13.php",
					12 => "sezioni/sezione_2_2_14.php",
					13 => '</tbody></table>',
				);

				$f21[3][2][] = array(
					"index" => $j,
					0 => '<table id="lot_award_no_'.$j.'" class="bordered"><tbody>',
					1 => "sezioni/sezione_5_f201.php",
					14 => '</tbody></table><div id="padding_lot_no_1" class="padding"></div>',
					);
		
				$j++;
			}
		}
	} else {
		$f21[1][1][3][2][] = array(
			"index" => 1,
			0 => '<input type="hidden" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ATTRIBUTE][ITEM]" value="1">',
			1 => '<table id="lot_no_1" class="bordered"><tbody>',
			2 => "sezioni/sezione_2_2_1.php",
			3 => "sezioni/sezione_2_2_2.php",
			4 => "sezioni/sezione_2_2_3.php",
			5 => "sezioni/sezione_2_2_4.php",
			// 6 => "sezioni/sezione_2_2_5.php",
			// 7 => "sezioni/sezione_2_2_6.php",
			8 => "sezioni/sezione_2_2_7_f202.php",
			// 9 => "sezioni/sezione_2_2_10_f01.php",
			// 10 => "sezioni/sezione_2_2_11_f01.php",
			// 11 => "sezioni/sezione_2_2_13.php",
			12 => "sezioni/sezione_2_2_14.php",
			13 => '</tbody></table>',
		);

		$f21[3][2][] = array(
			"index" => 1,
			0 => '<table id="lot_award_no_1" class="bordered"><tbody>',
			1 => "sezioni/sezione_5_f201.php",
			14 => '</tbody></table><div id="padding_lot_no_1" class="padding"></div>',
			);
	}

	$_SESSION["guue"]["form"] = $f21;
	loadForm($f21);
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
