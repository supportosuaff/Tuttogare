<?
	if(!empty($_POST["guue"]["CHANGES"]["CHANGE"])) {
		foreach ($_POST["guue"]["CHANGES"]["CHANGE"] as $changes) {
			if(!empty($changes["WHERE"]["SECTION"])) {
				$pattern = "/(I.[1-6]( )?(\))?)|(II.[1-8](.[1-9][0-4]?)?( )?(\))?)|(III.[1-3](.([1-9]|10))?( )?(\))?)|(IV.[1-3](.([1-9]|10|11))?( )?(\))?)|(V(.[1-5](.([1-9]|10))?)?( )?(\))?)|(VI.[1-4](.[1-4])?( )?(\))?)|(VII.[1-2](.[1-7])?( )?(\))?)/";
				if(!preg_match($pattern, $changes["WHERE"]["SECTION"])) {
					?>
					jalert('<div style="text-align:center">Errore di compilazione <strong>cod. #141</strong><br><strong>Verificare che il numero di sezione indicato nella sez. VII.1.2 sia corretto</strong></div>');
					<?
					die();
				}
			}
		}
	}

	$pattern_notice_number_oj = "/(19|20)\d{2}\/(S)\s\d{3}-\d{6}/";
	if(!empty($_POST["guue"]["COMPLEMENTARY_INFO"]["NOTICE_NUMBER_OJ"]) && !preg_match($pattern_notice_number_oj, $_POST["guue"]["COMPLEMENTARY_INFO"]["NOTICE_NUMBER_OJ"])) {
		?>
		jalert('<div style="text-align:center">Errore di compilazione <strong>cod. #142</strong><br><strong>Verificare che il numero dell&#39;avviso nella GU S nella sez. VI.6 sia corretto</strong></div>');
		<?
		die();
	}

	$pattern_no_doc_ext_pubblication_no = "/(20\d{2}\-\d{6})/";
	if(!empty($_POST["guue"]["COMPLEMENTARY_INFO"]["NO_DOC_EXT_PUBBLICATION_NO"]) && !preg_match($pattern_no_doc_ext_pubblication_no, $_POST["guue"]["COMPLEMENTARY_INFO"]["NO_DOC_EXT_PUBBLICATION_NO"])) {
		?>
		jalert('<div style="text-align:center">Errore di compilazione <strong>cod. #142</strong><br><strong>Verificare che il numero di riferimento dell&#39;avviso (anno-numero del documento) nella sez. VI.6 sia corretto</strong></div>');
		<?
		die();
	}

	if(!empty($_POST["guue"]["COMPLEMENTARY_INFO"]["ORIGINAL_OTHER_MEANS_PUBBLICATION_NO"])) {
		if(!empty($_POST["guue"]["COMPLEMENTARY_INFO"]["NO_DOC_EXT_PUBBLICATION_NO"])) {
			?>
			jalert('<div style="text-align:center">Errore di compilazione <strong>cod. #143</strong><br><strong>Se nella sez. VI.6 &egrave; indicato un altro sistema per l&#39;invio della comunicazione originale non &egrave; possibile indicare il numero di riferimento dell&#39;avviso.</strong></div>');
			<?
			die();
		}
		unset($_POST["guue"]["COMPLEMENTARY_INFO"]["NO_DOC_EXT_PUBBLICATION_NO"]);
	}
?>