<?
	include 'form_common.php';
?>
<h2 style="text-align: right;"><b>Sistema di qualificazione – Servizi di pubblica utilit&agrave;</b></h2>
<input type="hidden" name="tipologia_form" value="F07_2014">
<input type="hidden" name="numero_form" value="72">
<input type="hidden" name="v_form" value="2_0_9_S3">
<input id="stato_modello" type="hidden" name="bozza" value="0">
<input type="hidden" name="codice" value="<?= $codice_pubblicazione ?>">
<input type="hidden" name="codice_gara" value="<?= $codice_gara ?>">
<input type="hidden" name="attributi_form[CATEGORY]" value="ORIGINAL">
<input type="hidden" name="attributi_form[FORM]" value="F07">
<input type="hidden" name="attributi_form[LG]" value="IT">
<input type="hidden" name="guue[LEGAL_BASIS][ATTRIBUTE][VALUE]" value="32014L0025">
<input type="hidden" name="guue[NOTICE][ATTRIBUTE][TYPE]" value="QSU_CALL_COMPETITION">
<p style="text-align: right;" title="Tipologia di Avviso">
	Direttiva 2014/25/UE<br>
	<label>
		Sistema di qualificazione<br>
		Il presente avviso &egrave; un avviso di indizione di gara<br>
		<i>
			Gli operatori interessati devono informare l&#39;ente aggiudicatore di essere qualificati in base al sistema di qualificazione. <br>
			L&#39;appalto sar&agrave; aggiudicato senza pubblicazione di un ulteriore bando di gara.
		</i>
	</label>
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
		"OBJECT_CONTRACT-radio_as_select_for_lot_division",
		"radio_as_select_for_main_activity"
		);

	$_SESSION["guue"]["rel"] = $rel;
	$_SESSION["guue"]["numero_del_form"] = "f07-1";

	$f22 = array(
		0 => array(
			0 => "<h2><b>Sezione I: Amministrazione aggiudicatrice</b></h2>",
			1 => array(
				0 => "sezioni/sezione_1_1.php",
				1 => "sezioni/sezione_1_2.php",
				2 => "sezioni/sezione_1_3_f71.php",
				4 => "sezioni/sezione_1_6_f05.php",
				)
			),
		1 => array(
			0 => "<h2><b>Sezione II: Oggetto</b></h2>",
				1 => array(
					1 => "categorie/form.php",
					2 => array(
						0 => "<h3><b>II.1) Entit&agrave; dell&#39;appalto</b></h3>",
						1 => '<table class="bordered"><tbody>',
						2 => "sezioni/sezione_2_1_1.php",
						3 => "sezioni/sezione_2_1_2.php",
						4 => "sezioni/sezione_2_1_3.php",
						// 5 => "sezioni/sezione_2_1_4.php",
						// 6 => "sezioni/sezione_2_1_5.php",
						// 7 => "sezioni/sezione_2_1_6.php",
						// 8 => "sezioni/sezione_2_1_7.php",
						9 => '</tbody></table>',
						),
					3 => array(
						0 => "<h3><b>II.2) Descrizione</b></h3>",
						// 1 => "common/more_lots_button.php",
						2 => array(),
						// 3 => "common/more_lots.php",
						// 4 => "common/more_lots_button.php",
						),
					// 4 => "sezioni/sezione_2_3.php",
					)
			),
		2 => array(
			0 => "<h2><b>Sezione III: Informazioni di carattere giuridico, economico, finanziario e tecnico</b></h2>",
			1 => array(
				0 => array(
					0 => "<h3><b>III.1) Condizioni di partecipazione</b></h3>",
					1 => '<table class="bordered"><tbody>',
					// 2 => "sezioni/sezione_3_1_4.php",
					3 => "sezioni/sezione_3_1_5.php",
					4 => "sezioni/sezione_3_1_9.php",
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
				0 => array(
					0 => "<h3><b>IV.1) Descrizione</b></h3>",
					1 => '<table class="bordered"><tbody>',
					4 => "sezioni/sezione_4_1_6.php",
					6 => '</tbody></table>',
					),
			1 => array(
				0 => "<h3><b>IV.2) Informazioni di carattere amministrativo</b></h3>",
				1 => '<table class="bordered"><tbody>',
				2 => "sezioni/sezione_4_2_1.php",
				// 3 => "sezioni/sezione_4_2_2.php",
				4 => "sezioni/sezione_4_2_4.php",
				// 5 => "sezioni/sezione_4_2_5.php",
				// 6 => "sezioni/sezione_4_2_9.php",
				7 => '</tbody></table>',
				),
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
					7 => '</tbody></table>',
					),
				3 => "sezioni/sezione_6_5.php",
				)
			)
		);

	if(!empty($_SESSION["guue"]["codice_pubblicazione"])) {
    if(!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"])) {
      $j = 1;
      foreach ($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"] as $key_lot => $lot) {
        if(strpos($key_lot,"ITEM_") !== FALSE) {
          $f22[1][1][3][2][] = array(
            "index" => $j,
            0 => '<table id="lot_no_'.$j.'" class="bordered"><tbody>',
            // 2 => "sezioni/sezione_2_2_1.php",
            3 => "sezioni/sezione_2_2_2.php",
            4 => "sezioni/sezione_2_2_3.php",
            5 => "sezioni/sezione_2_2_4.php",
            6 => "sezioni/sezione_2_2_5.php",
            // 7 => "sezioni/sezione_2_2_6.php",
            // 8 => "sezioni/sezione_2_2_7.php",
            // 9 => "sezioni/sezione_2_2_10_f01.php",
            // 10 => "sezioni/sezione_2_2_11_f01.php",
            7 => "sezioni/sezione_2_2_8_f07.php",
            11 => "sezioni/sezione_2_2_13.php",
            // 12 => "common/delete_lot.php",
            // 12 => "sezioni/sezione_2_2_14.php",
            14 => '</tbody></table><div id="padding_lot_no_'.$j.'" class="padding"></div><script>var lot = '.$j.';</script>',
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
				$f22[1][1][3][2][] = array(
					"index" => $j,
					0 => '<table id="lot_no_'.$j.'" class="bordered"><tbody>',
					// 2 => "sezioni/sezione_2_2_1.php",
					3 => "sezioni/sezione_2_2_2.php",
					4 => "sezioni/sezione_2_2_3.php",
					5 => "sezioni/sezione_2_2_4.php",
					6 => "sezioni/sezione_2_2_5.php",
					// 7 => "sezioni/sezione_2_2_6.php",
					// 8 => "sezioni/sezione_2_2_7.php",
					// 9 => "sezioni/sezione_2_2_10_f01.php",
					// 10 => "sezioni/sezione_2_2_11_f01.php",
					7 => "sezioni/sezione_2_2_8_f07.php",
					11 => "sezioni/sezione_2_2_13.php",
					// 12 => "common/delete_lot.php",
					// 12 => "sezioni/sezione_2_2_14.php",
					14 => '</tbody></table><div id="padding_lot_no_'.$j.'" class="padding"></div><script>var lot = '.$j.';</script>',
				);
				$j++;
			}
		}
	} else {
		$j = 1;
		$f22[1][1][3][2][] = array(
			"index" => $j,
			0 => '<table id="lot_no_'.$j.'" class="bordered"><tbody>',
			// 2 => "sezioni/sezione_2_2_1.php",
			3 => "sezioni/sezione_2_2_2.php",
			4 => "sezioni/sezione_2_2_3.php",
			5 => "sezioni/sezione_2_2_4.php",
			6 => "sezioni/sezione_2_2_5.php",
			// 7 => "sezioni/sezione_2_2_6.php",
			// 8 => "sezioni/sezione_2_2_7.php",
			// 9 => "sezioni/sezione_2_2_10_f01.php",
			// 10 => "sezioni/sezione_2_2_11_f01.php",
			7 => "sezioni/sezione_2_2_8_f07.php",
			11 => "sezioni/sezione_2_2_13.php",
			// 12 => "common/delete_lot.php",
			// 12 => "sezioni/sezione_2_2_14.php",
			14 => '</tbody></table><div id="padding_lot_no_'.$j.'" class="padding"></div><script>var lot = '.$j.';</script>',
		);
	}

	$_SESSION["guue"]["form"] = $f22;
	loadForm($f22);
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
