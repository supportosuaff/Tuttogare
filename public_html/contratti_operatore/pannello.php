<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	if (!is_operatore()) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
	} else {
		if (isset($_GET["codice"])) {
			$href_contratto = null;
			$codice_contratto = $_GET["codice"];
			$sql = "SELECT b_contratti.*, b_conf_modalita_stipula.invio_remoto, b_conf_modalita_stipula.etichetta as modalita_di_stipula
							FROM b_contratti
							JOIN b_conf_modalita_stipula ON b_conf_modalita_stipula.codice = b_contratti.modalita_stipula
							JOIN r_contratti_contraenti ON r_contratti_contraenti.codice_contratto = b_contratti.codice
							JOIN b_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente
							WHERE b_contraenti.codice_utente = :codice_utente
							AND b_contratti.codice = :codice_contratto
							AND r_contratti_contraenti.codice_capogruppo = 0";
			$ris = $pdo->bindAndExec($sql, array(':codice_utente' => $_SESSION["record_utente"]["codice"], ':codice_contratto' => $codice_contratto));
			if($ris->rowCount() > 0) {
				$rec_contratto = $ris->fetch(PDO::FETCH_ASSOC);
				$href_contratto = "?codice={$rec_contratto["codice"]}";
				?>
				<h1>PANNELLO DI GESTIONE - CONTRATTO #<?= $rec_contratto["codice"] ?></h1>
	      <h2>Oggetto: <small><?= $rec_contratto["oggetto"] ?></small></h2>
				<h2 style="text-align:right; border-bottom:10px solid #999999; margin-bottom:20px;">
					Tipologia: <small><strong><?= $rec_contratto["modalita_di_stipula"] ?></strong></small>
					&nbsp;|&nbsp;Importo: <small><strong>&euro; <?= $rec_contratto["importo_totale"] ?></strong></small>
					<? if(!empty($rec_contratto["cig"])) echo "&nbsp;|&nbsp;CIG: <small><strong>{$rec_contratto["cig"]}</strong></small>" ?>
					<? if(!empty($rec_contratto["cup"])) echo "&nbsp;|&nbsp;CUP: <small><strong>{$rec_contratto["cup"]}</strong></small>" ?>
				</h2>
				<a class="pannello" href="documentazione/index.php<?= $href_contratto ?>" title="Dati preliminari">Documentazione Richiesta</a>
				<?
				if($rec_contratto["invio_remoto"] == "S") {?><a class="pannello" href="firma/index.php<?= $href_contratto ?>" title="Dati preliminari">Firma Remota</a><?}
			} else {
				?><h3 class="ui-state-error">Non hai i permessi per accedere a questo contratto.</h3><?
			}
		} else {
			?><h3 class="ui-state-error">Contratto non presente. Si prega di riprovare.</h3><?
		}
	}

  
	include_once($root."/layout/bottom.php");
	?>
