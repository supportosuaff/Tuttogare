<?
	include_once "../../../config.php";
	include_once $root."/layout/top.php";

	if(empty($_GET["codice"]) || empty($_SESSION["codice_utente"]) || !isset($_SESSION["ente"]) || !check_permessi("contratti",$_SESSION["codice_utente"])) {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
	} else {
		$codice = $_GET["codice"];
		$codice_gara = !empty($_GET["codice_gara"]) ? $_GET["codice_gara"] : null;

	  $bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ':codice' => $codice);
	  $sql  = "SELECT b_contratti.*, b_conf_modalita_stipula.invio_remoto FROM b_contratti JOIN b_conf_modalita_stipula ON b_contratti.modalita_stipula = b_conf_modalita_stipula.codice ";
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
			<h1>FIRMA DEL CONTRATTO</h1><h2><?= $rec_contratto["oggetto"] ?></h2><br>
      <div class="box">
        <h3 style="text-align:center;"><i class="fa fa-file-text fa-4x"></i><br><br>CONTRATTO</h3><br>
        <?
        $class = "";
        $oe = $ore = 0;
        $oe = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "oe" GROUP BY b_contraenti.codice', array(':codice_contratto' => $codice))->rowCount();
        $ore = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "ore" GROUP BY b_contraenti.codice', array(':codice_contratto' => $codice))->rowCount();
      
        $class = "";
        if($rec_contratto["invio_remoto"] == "S") {
          $bind = array(':codice_contratto' => $rec_contratto["codice"], ':codice_ente' => $_SESSION["ente"]["codice"]);
          $ris_documento_da_firmare = $pdo->bindAndExec("SELECT b_allegati.* FROM b_allegati WHERE sezione = 'contratti' AND codice_gara = :codice_contratto AND cartella = 'contratti_da_firmare' AND codice_ente = :codice_ente", $bind);
          if($ris_documento_da_firmare->rowCount() > 0) $class = " locked";
          ?><a class="pannello<?= $class ?>" href="upload-contratto.php<?= $href_contratto ?>&dafirmare=1">Carica il contratto per la firma remota</a><?
        }
        $class = "";
        $bind = array(':codice_contratto' => $rec_contratto["codice"], ':codice_ente' => $_SESSION["ente"]["codice"]);
        $ris_documento = $pdo->bindAndExec("SELECT b_allegati.* FROM b_allegati WHERE sezione = 'contratti' AND codice_gara = :codice_contratto AND cartella = 'contratti_firmati' AND online = 'N' AND hidden = 'N' AND codice_ente = :codice_ente", $bind);
        if($ris_documento->rowCount() > 0) $class = " locked";
        ?>
        <a class="pannello<?= $class ?>" href="upload-contratto.php<?= $href_contratto ?>">Carica il contratto firmato digitalmente gi&agrave; sottoscritto dalle parti</a>
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
?>
