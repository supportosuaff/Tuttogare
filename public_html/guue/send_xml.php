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
		$sql = "SELECT b_pubb_guue.* FROM b_pubb_guue
						WHERE b_pubb_guue.codice = :codice AND b_pubb_guue.codice_ente = :codice_ente AND b_pubb_guue.soft_delete = FALSE AND b_pubb_guue.stato = 'PRONTO PER LA TRASMISSIONE'";
		$ris = $pdo->bindAndExec($sql, array(':codice' => $_GET["codice"], ':codice_ente' => $_SESSION["ente"]["codice"]));
		if($ris->rowCount() != 1) {
			include_once $root . '/layout/top.php';
			?>
			<h1 style="text-align:center; color:#F30">QUESTO MODELLO NON RISULTA PRONTO PER L&#39;INVIO - ERRORE #10002</h1>
			<h3 style="text-align:center;">CORREGGI LA BOZZA E RIPROVA</h3>
			<a href="/guue" class="submit_big" style="background-color:#999;" style="text-decoration: none; color: #FFF;">
				RITORNA AL PANNELLO
			</a>
			<?
			include_once $root . '/layout/bottom.php';
		} else {
			$rec = $ris->fetch(PDO::FETCH_ASSOC);
			$form = array('guue' => json_decode($rec["post_form"], TRUE));

			$no_guue = 0;
			$bind = array(':current_year' => date('Y'));
			$sql = "SELECT MAX(no_guue) AS no_guue FROM b_pubb_guue WHERE anno_no_guue = :current_year";
			$ris = $pdo->bindAndExec($sql, $bind);
			if($ris->rowCount() > 0) {
				$no_guue = $ris->fetch(PDO::FETCH_ASSOC)["no_guue"] + 1;
			}

			$sql = "SELECT id_guue FROM b_enti WHERE codice = :codice_ente AND attivo = 'S'";
			$bind = array(':codice_ente' => $_SESSION["ente"]["codice"]);
			$ris = $pdo->bindAndExec($sql,$bind);
			if($ris->rowCount() > 0) {
				$id_guue = $ris->fetch(PDO::FETCH_ASSOC)["id_guue"];
			}

			$xml = json_decode(json_encode($rec["xml"]));
      /*=======================================
      =            Check XMLSchema            =
      =======================================*/
      $check = TRUE;
      // if(! $_SESSION["developEnviroment"]) {
        libxml_use_internal_errors(true);
        $xmlchecker = new DOMDocument();
        if ($xmlchecker->loadXML($xml)) {
          if($_SESSION["developEnviroment"]) {
            if (!$xmlchecker->schemaValidate("{$root}/guue/forms/2_0_9_S3-validator/TED_ESENDERS.xsd")) {
              $check = libxml_get_errors();
              libxml_clear_errors();
            }
          } else {
            if (!$xmlchecker->schemaValidate("{$root}/guue/forms/2_0_9_S3-validator/TED_ESENDERS.xsd")) {
              $check = libxml_get_errors();
              libxml_clear_errors();
            }
          }
        }
        libxml_use_internal_errors(false);
      // }
      /*=====  End of Check XMLSchema  ======*/

      if(!is_array($check) && $check) {
        $tedesender = new TedEsender();
				if (!empty($rec["id_guue"])) $tedesender->id_guue = $rec["id_guue"];
				if(!empty($id_guue)) $tedesender->username .= $id_guue;
				$tedesender->setPostData($xml);
        try {
          $response = $tedesender->sendNotice();
          include_once $root . '/layout/top.php';
          if(!empty($response["submission_id"])) {
            $salva = new salva();
            $salva->debug = FALSE;
            $salva->codop = $_SESSION["codice_utente"];
            $salva->nome_tabella = "b_pubb_guue";
            $salva->operazione = "UPDATE";
            $salva->oggetto = array("id_pubblicazione" => $response["submission_id"], "stato" => "TRASMESSO", "data_trasmissione" => date("d/m/Y H:i:s"), "codice" => $_GET["codice"]);
            $salva->save();

            $sql_update_no_guue = "UPDATE b_pubb_guue SET no_guue = :no_guue, anno_no_guue = :current_year WHERE codice = :codice";
            $pdo->bindAndExec($sql_update_no_guue, array(':codice' => $rec["codice"], ':no_guue' => $no_guue, ':current_year' => date('Y')));
            ?>
              <h1 style="text-align:center; color:#339900">COMUNICAZIONE INVIATA CORRETTAMENTE</h1>
              <h3>IL FORM &Egrave; STATO CORRETTAMENTE INVIATO PER LA PUBBLICAZIONE SULLA GAZZETTA UFFICIALE DELL&#39;UNIONE EUROPEA.<br>RITORNA AL PANNELLO DI GESTIONE DEGLI INVII PER VERIFICARE LO STATO DELLA PUBBLICAZIONE</h3><br><br>
              <a href="/guue" class="submit_big" style="background-color:#999;" style="text-decoration: none; color: #FFF;">RITORNA AL PANNELLO</a>
            <?
          } else {
            ?>
            <h1 style="text-align:center; color:#F30">ATTENZIONE SI &Egrave; VERIFICATO UN ERRORE DI COMUNICAZIONE CON I SERVIZI DI TED SENDER - ERRORE #10004</h1>
            <h3 style="text-align:center;">IL FORM NON &Egrave; STATO PROCESSATO CORRETTAMENTE. SI PREGA DI RIPROVARE</b></h3>
            <a href="/guue" class="submit_big" style="background-color:#999;" style="text-decoration: none; color: #FFF;">RITORNA AL PANNELLO</a>
            <?
          }
          include_once $root . '/layout/bottom.php';
        } catch (Exception $e) {
          include_once $root . '/layout/top.php';
          ?>
          <h1 style="text-align:center; color:#F30">ATTENZIONE SI &Egrave; VERIFICATO UN ERRORE DI COMUNICAZIONE CON I SERVIZI DI TED SENDER - ERRORE #10003</h1>
          <h3 style="text-align:center;">CODICE DI ERRORE: <b><?= $e->getCode() ?></b></h3>
          <a href="/guue" class="submit_big" style="background-color:#999;" style="text-decoration: none; color: #FFF;">RITORNA AL PANNELLO</a>
          <?
          include_once $root . '/layout/bottom.php';
        }
      } else {
        include_once $root . '/layout/top.php';
        if(!empty($check)) {
          ?>
          <h1 style="text-align:left; padding-top: 30px; color:#F30">ATTENZIONE SI &Egrave; VERIFICATO UN ERRORE DURANTE LA VERIFICA FORMALE DEL FILE XML - ERRORE #10005</h1>
          <h3>IL FORM NON HA SUPERATO I CONTROLLI STANDARD. SI PREGA DI RIPROVARE</b></h3><br><br>
          <table style="width: 100%">
            <tbody>
              <tr class="even">
                <td class="etichetta">
                  <h3 style="color: #FF0000;"><i class="fa fa-times-circle"></i> ERRORI DI VALIDAZIONE DEL FORM: </h3>
                </td>
              </tr>
              <tr>
                <td>
                  <?
                  foreach($check as $index => $errore) {
                    if($errore->level > LIBXML_ERR_WARNING) {
                      ?>
                      <ul style="color:#FF0000;">
                        <li><b>RIGA: </b> <?= $errore->line ?> <b>COLONNA: </b> <?= $errore->column ?></li>
                        <li><b>ERRORE: </b> <?= $errore->message ?></li>
                      </ul>
                      <?
                    }
                  }
                  ?>
                </td>
              </tr>
            </tbody>
          </table>
          <?
        }
        ?>
        <h2>Per ulteriori informazioni in merito all&#39;errore si prega di contattare l&#39;Help Desk Specialistico.</h2>
        <a href="/guue" class="submit_big" style="background-color:#999;" style="text-decoration: none; color: #FFF;">RITORNA AL PANNELLO</a>
        <?
        include_once $root . '/layout/bottom.php';
      }
		}
	}
	?>
