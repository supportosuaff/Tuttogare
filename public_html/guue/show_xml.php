<?
	@session_start();
	include_once "../../config.php";
	include_once $root . "/inc/funzioni.php";
	include_once "post2xml.class.php";
	include_once "tedesender.class.php";

	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("guue",$_SESSION["codice_utente"]);
		if (!$edit) {
			header('Location: /index.php');
			die();
		}
	} else {
		header('Location: /index.php');
		die();
	}

	if(!empty($_GET["codice"])) {
		$sql = "SELECT * FROM b_pubb_guue WHERE codice = :codice AND codice_ente = :codice_ente AND soft_delete = FALSE ";
		$ris = $pdo->bindAndExec($sql, array(':codice' => $_GET["codice"], ':codice_ente' => $_SESSION["ente"]["codice"]));
		if($ris->rowCount() != 1) {
			include_once $root . '/layout/top.php';
			?>
			<h1 style="text-align:center; color:#F30">QUESTO MODELLO NON RISULTA VALIDO PER L&#39;INVIO - ERRORE #10002</h1>
			<h3 style="text-align:center;">CORREGGI LA BOZZA E RIPROVA</h3>
			<?
			include_once $root . '/layout/bottom.php';
		} else {
			$rec = $ris->fetch(PDO::FETCH_ASSOC);
			$form = array('guue' => json_decode($rec["post_form"], TRUE));

			$no_guue = 0;
			$sql_no_guue = "SELECT no_guue FROM b_enti WHERE codice = :codice_ente AND attivo = 'S'";
			$ris_no_guue = $pdo->bindAndExec($sql_no_guue, array(':codice_ente' => $rec["codice_ente"]));
			if($ris_no_guue->rowCount() > 0) {
				$rec_no_guue = $ris_no_guue->fetch(PDO::FETCH_ASSOC);
				$no_guue = $rec_no_guue["no_guue"] + 1;
			}

			if(!empty($rec["xml"])) {
				$xml = $rec["xml"];
			} else {
				$xmlgen = new post2xml();

				$xmlgen->setNoGuue($no_guue);
				$xmlgen->codice_gara = 1;
				$xmlgen->customer_login = $_SESSION["ente"]["id_guue"];
				$xmlgen->form = $rec["tipologia_form"];
				$xmlgen->form_attribute = array(
					'CATEGORY' => 'ORIGINAL',
					'FORM' => 'F' . (strlen($rec["numero_form"]) > 1 ? $rec["numero_form"] : '0' . $rec["numero_form"]),
					'LG' => 'IT',
					);
				$xmlgen->post = $form;
				$xmlgen->setMainCpv($form["guue"]["main_cpv"]);
				$xmlgen->setSupplementaryCpv($form["guue"]["supplementary_cpv"]);

				$xml = $xmlgen->createXML();
			}
			header('Content-Type: text/xml');
			echo $xml;
			die();

		}

	}

	?>
