<?
	include_once "../../../config.php";
	include_once "{$root}/layout/top.php";
  include_once "{$root}/inc/p7m.class.php" ;

	if(empty($_GET["codice"]) || empty($_SESSION["codice_utente"]) || !isset($_SESSION["ente"]) || !check_permessi("contratti",$_SESSION["codice_utente"])) {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	} else {
		$codice = $_GET["codice"];
		$codice_gara = !empty($_GET["codice_gara"]) ? $_GET["codice_gara"] : null;

	  $bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ':codice' => $codice);
	  $sql  = "SELECT b_contratti.* FROM b_contratti ";
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

		if($ris->rowCount() == 1) {
			$rec_contratto = $ris->fetch(PDO::FETCH_ASSOC);
      $lock = FALSE;
      if(!empty($rec_contratto["codice"])) {
        $oe = $ore = 0;
        $oe = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "oe" GROUP BY b_contraenti.codice', array(':codice_contratto' => $rec_contratto["codice"]))->rowCount();
        $ore = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "ore" GROUP BY b_contraenti.codice', array(':codice_contratto' => $rec_contratto["codice"]))->rowCount();
        if($oe > 0 && $ore == 1) {
          $bind = array(':codice' => $rec_contratto["codice"], ':tipo' => 'contratto', ':sezione' => 'contratti');
          $ris = $pdo->bindAndExec("SELECT b_documentale.codice FROM b_documentale WHERE b_documentale.tipo = :tipo AND b_documentale.sezione = :sezione AND b_documentale.codice_gara = :codice AND codice_allegato > 0", $bind);
          if($ris->rowCount() > 0) {
            $lock = TRUE;
          } else {
            $bind = array(':codice_contratto' => $rec_contratto["codice"], ':codice_ente' => $_SESSION["ente"]["codice"]);
            $ris_documento = $pdo->bindAndExec("SELECT b_allegati.* FROM b_allegati WHERE sezione = 'contratti' AND codice_gara = :codice_contratto AND cartella = 'contratti_firmati' AND online = 'N' AND hidden = 'N' AND codice_ente = :codice_ente", $bind);
            if($ris_documento->rowCount() > 0) $lock = TRUE;
          }
        }
      }

			?>
			<link rel="stylesheet" href="/contratti/css.css" media="screen">
			<h1>MODULISTICA - Richiesta documentazione</h1>
			<? if(!$lock) { ?><form name="box" method="post" action="save.php" rel="validate"><? } ?>
				<input type="hidden" name="codice_contratto" value="<?= $rec_contratto["codice"]; ?>">
				<? if(!$lock) { ?><div class="comandi"><button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button></div><? } ?>
				<script>
					var uploader = new Array();
				</script>
				<script type="text/javascript" src="/js/resumable.js"></script>
				<script type="text/javascript" src="resumable-uploader.js"></script>
        <style type="text/css">
          td table th,
          td table * {
            font-size: 1em;
            text-align: left;
          }
          td table th {
            color: #000000 !important;
          }
          .btn {
            -webkit-border-radius: 100;
            -moz-border-radius: 100;
            border: none;
            border-radius: 100%;
            background: #3498db;
            padding: 5px 11px 5px 11px;
            text-decoration: none;
            display: inline-block;
            appearance: none;
            box-shadow: none;
            text-decoration: none;
            cursor: pointer;
            color: #FFF;
          }
          .btn-circle {
            width: 35px;
            height: 35px;
          }
        </style>
				<table id="tab_moduli" width="100%">
					<thead>
						<tr>
							<th>Titolo</th>
							<th width="30%">Modello</th>
							<th width="10">Obbligatorio</th>
							<th width="10">Elimina</th>
						</tr>
					</thead>
					<tbody>
						<?
						$bind = array(":codice"=>$rec_contratto["codice"]);
						$strsql = "SELECT * FROM b_modulistica_contratto WHERE codice_contratto = :codice ";
						$ris_moduli = $pdo->bindAndExec($strsql,$bind);
						if ($ris_moduli->rowCount()>0) {
              $sth_upload = $pdo->prepare("SELECT b_allegati_contratto.*, b_operatori_economici.ragione_sociale, b_operatori_economici.partita_iva FROM b_allegati_contratto JOIN b_operatori_economici ON b_allegati_contratto.codice_operatore = b_operatori_economici.codice WHERE codice_modulo = :codice_modulo");
							while($modulo = $ris_moduli->fetch(PDO::FETCH_ASSOC)) {
                $id = $modulo["codice"];
                $allegati_modulo = array();
                $sth_upload->bindValue(':codice_modulo', $modulo["codice"]);
                $sth_upload->execute();
                if($sth_upload->rowCount() > 0) $allegati_modulo = $sth_upload->fetchAll(PDO::FETCH_ASSOC);
								if ($modulo["nome_file"] != "") $modulo["nome_file"] = "<a href=\"/documenti/allegati/contratti/" . $modulo["codice_contratto"] . "/" . $modulo["nome_file"] . "\" target=\"_blank\">" . $modulo["nome_file"] . "</a>";
								include("tr_modulo.php");
							}
						}
						?>
					</tbody>
				</table>
				<? if(!$lock) { ?>
          <button class="button button-highlight button-block" style="width:100%;" onClick="aggiungi('tr_modulo.php','#tab_moduli');return false;">Aggiungi modulo</button>
          <input type="submit" class="submit_big" value="Salva">
        </form>
        <? } ?>
        <script type="text/javascript">
          <? if($lock) { ?>
            $(':input').not('.submit_big').attr('disabled', true).prop('disabled', true);
          <? } ?>
        </script>
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
