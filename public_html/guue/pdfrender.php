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
		$sql = "SELECT id_pubblicazione FROM b_pubb_guue WHERE codice = :codice AND codice_ente = :codice_ente AND soft_delete = FALSE AND stato = 'PUBBLICATO'";
		$ris = $pdo->bindAndExec($sql, array(':codice' => $_GET["codice"], ':codice_ente' => $_SESSION["ente"]["codice"]));
		if($ris->rowCount() != 1) {
			include_once $root . '/layout/top.php';
			?>
			<h1 style="text-align:center; color:#F30">QUESTO MODELLO NON RISULTA ANCORA PUBBLICATO - ERRORE #10002</h1>
			<h3 style="text-align:center;">CORREGGI LA BOZZA E RIPROVA</h3>
			<a href="/guue" class="submit_big" style="background-color:#999;" style="text-decoration: none; color: #FFF;">
				RITORNA AL PANNELLO DI GESTIONE GUUE
			</a>
			<?
			include_once $root . '/layout/bottom.php';
		} else {
			$rec = $ris->fetch(PDO::FETCH_ASSOC);

			$tedesender = new TedEsender();

      $ris_id_guue_ente = $pdo->bindAndExec(
        "SELECT id_guue FROM b_enti WHERE codice = :codice_ente AND attivo = 'S'",
        array(':codice_ente' => $_SESSION["ente"]["codice"])
      );
      if($ris_id_guue_ente->rowCount() > 0) {
        $tedesender->username .= $ris_id_guue_ente->fetch(PDO::FETCH_ASSOC)["id_guue"];
      }

			$tedesender->submission_id = $rec["id_pubblicazione"];

			try {
				$response = $tedesender->getPDF();
				if(!empty($response["result"])) {
					$pdf = base64_decode($response["result"]);
					header('Content-Description: File Transfer');
			    header("Content-Type: application/force-download");
					header("Content-Type: application/octet-stream");
					header("Content-Type: application/download");
			    header('Content-Disposition: attachment; filename="'.basename($rec["id_pubblicazione"]).'.pdf"');
			    header('Expires: 0');
			    header('Cache-Control: must-revalidate');
			    header('Pragma: public');
			    header('Content-Length: ' . strlen($pdf));
			    echo $pdf;
				}
			} catch (Exception $e) {
				include_once $root . '/layout/top.php';
				?>
				<h1 style="text-align:center; color:#F30">ATTENZIONE SI &Egrave; VERIFICATO UN ERRORE DI COMUNICAZIONE CON I SERVIZI DI TED SENDER - ERRORE #10010</h1>
				<h3 style="text-align:center;">CODICE DI ERRORE: <b><?= $e->getCode() ?></b></h3>
				<a href="/guue" class="submit_big" style="background-color:#999;" style="text-decoration: none; color: #FFF;">RITORNA AL PANNELLO</a>
				<?
				include_once $root . '/layout/bottom.php';
			}
		}
	}
	?>
