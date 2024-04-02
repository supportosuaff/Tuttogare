<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
  $href_contratto = null;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("contratti",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	if (isset($_GET["codice"])) {
    $codice = $_GET["codice"];
    $codice_gara = !empty($_GET["codice_gara"]) ? $_GET["codice_gara"] : null;

		$bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ':codice' => $codice);
		$sql  = "SELECT b_contratti.*, b_conf_modalita_stipula.ufficiale_rogante, b_conf_modalita_stipula.invio_remoto, b_conf_modalita_stipula.etichetta as modalita_di_stipula FROM b_contratti ";
		$sql .= "JOIN b_conf_modalita_stipula ON b_conf_modalita_stipula.codice = b_contratti.modalita_stipula ";
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
    $ris  = $pdo->bindAndExec($sql,$bind);

    if($ris->rowCount() == 1) {
      $rec_contratto = $ris->fetch(PDO::FETCH_ASSOC);
      $href_contratto = "?codice=".$rec_contratto["codice"] . (!empty($rec_contratto["codice_gara"]) ? "&codice_gara=".$rec_contratto["codice_gara"] : null);
      $gara_riferimento = "";
      if(!empty($rec_contratto["codice_gara"])) {
        $bind = array();
    		$bind[":codice"] = $codice;
    		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
        $sql_gara = "SELECT * FROM b_gare WHERE codice = :codice AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
        if ($_SESSION["gerarchia"] > 0) {
    			$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
    			$sql_gara .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
    		}
        $ris = $pdo->bindAndExec($sql_gara, $bind);
        if($ris->rowCount() > 0) {
          $rec_gara = $ris->fetch(PDO::FETCH_ASSOC);
          $gara_riferimento = " - GARA DI RIFERIMENTO ID:".$rec_gara["id"];
        }
      }
      ?>
      <h1>PANNELLO DI GESTIONE - CONTRATTO #<?= $rec_contratto["codice"] . $gara_riferimento ?></h1>
      <h2>Oggetto: <small><?= $rec_contratto["oggetto"] ?></small></h2>
			<h2 style="text-align:right; border-bottom:10px solid #999999; margin-bottom:20px;">Tipologia: <small><strong><?= $rec_contratto["modalita_di_stipula"] ?></strong></small> | Importo <small><strong>&euro; <?= number_format($rec_contratto["importo_totale"], 2, ',', '.') ?></strong></small></h2>
			<? if(empty($codice_gara) && check_permessi('user', $_SESSION["codice_utente"])) {?><a class="pannello" href="permessi/edit.php<?= $href_contratto ?>" title="Allegati0">Permessi</a><?} ?>
			<a class="pannello" href="allegati/edit.php<?= $href_contratto ?>" title="Dati preliminari">Allegati</a>
			<?
      $locked = array("scarica_contratto_firmato", "firma", 'plicoae', 'pacchetto_conservazione', 'dati_preliminari', 'ufficiale_rogante', 'gestione_parti', 'documentazione', 'elaborazione');
      $locked = array_flip($locked);
			$oe = $ore = 0;
			$oe = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "oe" GROUP BY b_contraenti.codice', array(':codice_contratto' => $rec_contratto["codice"]))->rowCount();
			$ore = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "ore" GROUP BY b_contraenti.codice', array(':codice_contratto' => $rec_contratto["codice"]))->rowCount();
			if($oe > 0 && $ore == 1) {
          $bind = array(':codice_contratto' => $rec_contratto["codice"], ':codice_ente' => $_SESSION["ente"]["codice"]);
          $ris_documento = $pdo->bindAndExec("SELECT b_allegati.* FROM b_allegati WHERE sezione = 'contratti' AND codice_gara = :codice_contratto AND cartella = 'contratti_firmati' AND online = 'N' AND hidden = 'N' AND codice_ente = :codice_ente", $bind);
          if($ris_documento->rowCount() > 0) {
            $show = array('dati_preliminari','ufficiale_rogante', 'gestione_parti', 'documentazione', 'firma', 'scarica_contratto_firmato', 'plicoae', 'pacchetto_conservazione');
            unset($locked["scarica_contratto_firmato"], $locked["plicoae"], $locked["pacchetto_conservazione"]);
          } else {
            $show = array('dati_preliminari','ufficiale_rogante', 'gestione_parti', 'documentazione', 'firma');
            unset($locked["dati_preliminari"], $locked["ufficiale_rogante"], $locked["gestione_parti"], $locked["documentazione"], $locked["elaborazione"], $locked["firma"]);
          }
			} else {
        $show = array('dati_preliminari','ufficiale_rogante', 'gestione_parti', 'documentazione');
        unset($locked["dati_preliminari"], $locked["ufficiale_rogante"], $locked["gestione_parti"], $locked["documentazione"], $locked["firma"]);
    	}
			$locked = array_flip($locked);
      ?>
      <? if(in_array("dati_preliminari", $show)) { ?><a class="pannello<?= in_array("dati_preliminari", $locked) ? ' locked' : null ?>" href="edit.php<?= $href_contratto ?>" title="Dati preliminari">Modifica dati preliminari</a><? } ?>
      <? if(in_array("ufficiale_rogante", $show)) { ?><? if($rec_contratto["ufficiale_rogante"] == "S") {?><a class="pannello <?= in_array("ufficiale_rogante", $locked) ? ' locked' : null ?>" href="ufficiale_rogante/index.php<?= $href_contratto ?>" title="Gestione ufficiale rogante">Gestione ufficiale rogante / Notaio</a><?} ?><? } ?>
      <? if(in_array("gestione_parti", $show)) { ?><a class="pannello<?= in_array("gestione_parti", $locked) ? ' locked' : null ?>" href="gestione_parti/index.php<?= $href_contratto ?>" title="Gestione parti">Gestione delle parti</a><? } ?>
      <? if(in_array("documentazione", $show)) { ?><a class="pannello<?= in_array("documentazione", $locked) ? ' locked' : null ?>" href="modulistica/index.php<?= $href_contratto ?>" title="Modulistica">Richiesta documentazione</a><? } ?>
      <? if(in_array("firma", $show)) { ?><a class="pannello<?= in_array("firma", $locked) ? ' locked' : null ?>" href="firma/index.php<?= $href_contratto ?>" title="Firm Contratto">Firma del Contratto</a><? } ?>
      <? if(in_array("scarica_contratto_firmato", $show)) { ?><a class="pannello<?= in_array("scarica_contratto_firmato", $locked) ? ' locked' : null ?>" href="firma/contratto.php<?= $href_contratto ?>" target="_blank" title="Contratto firmato digitalmente">Scarica il contratto firmato digitalmente</a><? } ?>
			<? if(in_array("plicoae", $show)) { ?><a class="pannello<?= in_array("plicoae", $locked) ? ' locked' : null ?>" href="plicoae/index.php<?= $href_contratto ?>" title="Pubblica">Plico Agenzia delle Entrate</a><? } ?>
			<? if(in_array("plicoae", $show) && $_SESSION["ente"]["codice"] == "797") { // integrazione per invimit
				 ?>
				<br>
				<a class="pannello<?= in_array("plicoae", $locked) ? ' locked' : null ?>" href="ws/index.php<?= $href_contratto ?>" title="Pubblica">Trasmetti ad ERP</a>
			<? } ?>
      <!-- <a class="pannello<?= in_array("pacchetto_conservazione", $locked) ? ' locked' : null ?>" href="#" title="Pubblica">Pacchetto di Conservazione</a> -->
      <?
    } else {
      ?>
      <h2 class="ui-state-error">Si è verificato un errore nella lettura delle informazioni. Si prega di riprovare o se il problema persiste di contattare l'amministratore.</h2>
      <?
    }
  } else {
    ?>
    <h2>Contratto non trovato</h2>
    <?
  }
  include 'ritorna.php';
	include_once($root."/layout/bottom.php");
	?>
