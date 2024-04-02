<?
	include_once("../../../config.php");
	$disable_alert_sessione = true;
  include_once($root."/layout/top.php");

	if(empty($_GET["codice"]) || empty($_SESSION["codice_utente"]) || !isset($_SESSION["ente"]) || !check_permessi("contratti",$_SESSION["codice_utente"])) {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	} else {
		$codice = $_GET["codice"];
		$codice_gara = !empty($_GET["codice_gara"]) ? $_GET["codice_gara"] : null;

    $oe = $ore = 0;
    $oe = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "oe" GROUP BY b_contraenti.codice', array(':codice_contratto' => $codice))->rowCount();
    $ore = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "ore" GROUP BY b_contraenti.codice', array(':codice_contratto' => $codice))->rowCount();
    if($oe > 0 && $ore == 1) {
      $bind = array(':codice' => $codice, ':tipo' => 'contratto', ':sezione' => 'contratti');
      $ris = $pdo->bindAndExec("SELECT b_documentale.codice FROM b_documentale WHERE b_documentale.tipo = :tipo AND b_documentale.sezione = :sezione AND b_documentale.codice_gara = :codice AND codice_allegato > 0", $bind);
      if($ris->rowCount() > 0) {
        $ris = $pdo->bindAndExec("SELECT * FROM `b_allegati` WHERE `sezione` = 'contratti' AND `codice_gara` = :codice_contratto AND `cartella` = 'contratti_firmati'", array(':codice_contratto' => $codice));
        if($ris->rowCount() > 0) {
          $rec_contratto_firmato = $ris->fetch(PDO::FETCH_ASSOC);
          if(file_exists("{$config["arch_folder"]}/allegati_contratto/{$codice}/{$rec_contratto_firmato["riferimento"]}")) {
            ?>
            <h1>UPLOAD DEL CONTRATTO FIRMATO DIGITALMENTE</h1><br>
            <h3 class="ui-state-error">Il contratto firmato digitalmente &egrave; stato gi&agrave; caricato.</h3>
            <?
            include_once($root."/layout/bottom.php");
            die();
          }
        }
      }
    }

	  $bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ':codice' => $codice);
	  $sql  = "SELECT b_contratti.*, b_conf_modalita_stipula.ufficiale_rogante, b_conf_modalita_stipula.invio_remoto FROM b_contratti JOIN b_conf_modalita_stipula ON b_contratti.modalita_stipula = b_conf_modalita_stipula.codice ";
	  if(!empty($codice_gara) && $_SESSION["gerarchia"] > 1) {
	    $sql .= "JOIN b_permessi ON b_contratti.codice_gara = b_permessi.codice_gara ";
	  } elseif (empty($codice_gara) && $_SESSION["gerarchia"] > 1) {
			$sql .= "JOIN b_permessi_contratti ON b_contratti.codice = b_permessi_contratti.codice_contratto ";
		}
	  $sql .= "WHERE b_contratti.codice = :codice ";
	  $sql .= "AND b_contratti.codice_gestore = :codice_ente ";
	  if ($_SESSION["gerarchia"] > 0) {
	    $bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
	    $sql .= "AND (b_contratti.codice_ente = :codice_ente_utente OR b_contratti.codice_gestore = :codice_ente_utente) ";
	  }
	  if (!empty($codice_gara)) {
	    $bind[":codice_gara"] = $codice_gara;
	    $sql .= " AND b_contratti.codice_gara = :codice_gara";
	    if($_SESSION["gerarchia"] > 1) {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
	      $sql .= " AND (b_permessi.codice_utente = :codice_utente)";
	    }
	  } else {
			if($_SESSION["gerarchia"] > 1) {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$sql .= " AND (b_permessi_contratti.codice_utente = :codice_utente)";
			}
		}
	  $ris = $pdo->bindAndExec($sql,$bind);
		$href_contratto = null;
		if($ris->rowCount() == 1) {
			$rec_contratto = $ris->fetch(PDO::FETCH_ASSOC);
			$href_contratto = "?codice=".$rec_contratto["codice"] . (!empty($rec_contratto["codice_gara"]) ? "&codice_gara=".$rec_contratto["codice_gara"] : null);
			?>
      <style media="screen">
				input[type="text"] {
					width: 100%;
					box-sizing : border-box;
					font-family: Tahoma, Geneva, sans-serif;
					font-size: 1em
				}
				input[type="text"]:disabled {
					background: #dddddd;
				}
			</style>
			<link rel="stylesheet" href="/contratti/css.css" media="screen" title="no title">
			<h1>UPLOAD DEL CONTRATTO <?= !empty($_GET["dafirmare"]) ? "PER LA FIRMA REMOTA" : "FIRMATO DIGITALMENTE" ?></h1><br>
      <script type="text/javascript" src="/js/spark-md5.min.js"></script>
      <script type="text/javascript" src="/js/resumable.js"></script>
      <script type="text/javascript" src="resumable-uploader.js"></script>
      <div class="box">
        <form action="upload.php" id="upload_contratto" method="post" target="_self" rel="validate">
          <? if(!empty($_GET["dafirmare"])) {?><input type="hidden" name="dafirmare" value="1"><?} ?>
          <input type="hidden" name="codice_contratto" value="<?= $rec_contratto["codice"] ?>">
          <input type="hidden" name="codice_gara" value="<?= $rec_contratto["codice_gara"] ?>">
          <input type="hidden" name="md5_file" id="md5_file" title="File" rel="S;0;0;A">
          <input type="hidden" id="filechunk" name="filechunk">
            <table style="width:100%;">
              <tbody>
                <tr>
                  <td colspan="4" style="color: #000;">
										<div class="scegli_file"><i class="fa fa-folder-open fa-5x"></i><br>Clicca per selezionare il file
											<? if (empty($_GET["dafirmare"])) { ?>
	                    	firmato digitalmente dalle parti <?= $rec_contratto["ufficiale_rogante"] == "S" ? "e dall&#39;ufficiale rogante" : null ?>
											<? } else { ?>
												da far firmare all'operatore economico
											<? } ?>
										</div>
                    <script>
                      var uploader = (function($){
                      return (new ResumableUploader($('.scegli_file'), "<?= !empty($_GET["dafirmare"]) ? "pdf" : "p7m" ?>"));
                      })(jQuery);
                    </script>
                  </td>
                </tr>
                <tr>
                  <td colspan="4" class="etichetta">
                    <div id="progress_bar" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
                  </td>
                </tr>
                <tr>
                  <td colspan="4" class="etichetta"><b>Informazioni Repertorio</b></td>
                </tr>
                <tr>
                  <td style="width: 200px;" class="etichetta">Numero: *</td>
                  <td>
                    <input type="text" name="repertorio" id="repertorio_numero" value="<?= !empty($rec_contratto["repertorio"]) ? $rec_contratto["repertorio"] : null ?>" rel="S;1;0;A" title="Repertorio" style="width: 40%;">
                  </td>
                  <td style="width: 200px;" class="etichetta">Data: *</td>
                  <td>
                    <input type="text" name="data_repertorio" id="repertorio_data" value="<?= !empty($rec_contratto["data_repertorio"]) ? mysql2date($rec_contratto["data_repertorio"]) : null ?>" rel="S;1;0;A" class="datepick" title="Data Repertorio" style="width: 50%;">
                  </td>
                </tr>
                <tr>
                  <td colspan="4" class="etichetta"><b>Invio comunicazione all&#39;operatore economico</b></td>
                </tr>
                <tr>
                  <td width="200px" class="etichetta">PEC invio comunicazione:</td>
                  <td colspan="3" style="text-align: left !important; color:#000 !important;">
                    <select name="codice_pec" rel="S;0;0;N" class="espandi" title="PEC">
                      <option value="0"><? echo $_SESSION["ente"]["pec"] ?> - Predefinito</option>
                      <?
                        $bind = array();
                        $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
                        $sql_pec = "SELECT * FROM b_pec WHERE codice_ente = :codice_ente AND attivo = 'S'";
                        $ris_pec = $pdo->bindAndExec($sql_pec,$bind);
                        if ($ris_pec->rowCount() > 0) {
                          while ($indirizzo_pec = $ris_pec->fetch(PDO::FETCH_ASSOC)) {
                            ?><option value="<? echo $indirizzo_pec["codice"] ?>"><? echo $indirizzo_pec["pec"] ?></option><?
                          }
                        }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="etichetta">E-mail: *</td>
                  <td colspan="3">
                    <?
                    $rec_operatore["pec"] = $rec_operatore["email"] = "";
                    $bind = array(':tipologia' => "oe", ':codice_contratto' => $codice);
                    $ris_operatore = $pdo->bindAndExec("SELECT b_contraenti.nome, b_contraenti.cognome, b_utenti.pec, b_utenti.email FROM b_contraenti JOIN r_contratti_contraenti ON r_contratti_contraenti.codice_contraente = b_contraenti.codice JOIN b_utenti ON b_utenti.codice = b_contraenti.codice_utente WHERE b_contraenti.tipologia = :tipologia AND r_contratti_contraenti.codice_contratto = :codice_contratto AND (r_contratti_contraenti.codice_capogruppo = 0 OR r_contratti_contraenti.codice_capogruppo IS NULL)", $bind);
                    if($ris_operatore->rowCount() > 0) {
                      $rec_operatore = $ris_operatore->fetch(PDO::FETCH_ASSOC);
                    }
                    ?>
                    <input type="text" rel="S;2;0;E" name="email" title="Indirizzo Email" value="<?= !empty($rec_operatore["pec"]) ? $rec_operatore["pec"] : $rec_operatore["email"] ?>">
                  </td>
                </tr>
                <tr>
                  <th colspan="4">
                    <button type="submit" class="button button-primary button-block" style="width:100%;" onClick="$('#wait').show(); uploader.resumable.upload();return false;">Carica</button>
                  </th>
                </tr>
              </tbody>
            </table>
        </form>
      </div>
      <?
		} else {
			?>
			<h2 class="ui-state-error">Si è verificato un errore nella lettura delle informazioni. Si prega di riprovare o se il problema persiste di contattare l'amministratore</h2>
			<?
		}
	}
	include_once($root . "/contratti/ritorna_pannello_contratto.php");
	include_once($root."/layout/bottom.php");
  die();
?>

<h1>SELEZIONA IL FILE FIRMATO DIGITALEMENTE DA ENTRAMBE LE PARTI</h1><BR>
