<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$public = true;

	if (isset($_GET["codice_gara"]) && isset($_GET["codice_lotto"]) && (isset($_GET["economica"]) || isset($_GET["tecnica"])) && is_operatore()) {

		$codice_gara = $_GET["codice_gara"];
		$codice_lotto = $_GET["codice_lotto"];

		$bind = array();
		$bind[":codice"] = $codice_gara;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql  = "SELECT b_gare.*, b_procedure.invito, b_procedure.fasi, b_procedure.mercato_elettronico FROM b_gare JOIN b_modalita ON b_gare.modalita = b_modalita.codice JOIN b_procedure ON b_gare.procedura = b_procedure.codice
								WHERE b_gare.codice = :codice ";
		$strsql .= "AND b_gare.annullata = 'N' AND b_modalita.online = 'S' ";
		$strsql .= "AND codice_gestore = :codice_ente ";
		$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
		$risultato = $pdo->bindAndExec($strsql,$bind);

		$accedi = false;

		if ($risultato->rowCount() > 0) {
			$bind = array();
			$bind[":codice"] = $codice_gara;
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

			$derivazione = "";
			$sql = "SELECT * FROM b_procedure WHERE codice = :codice_procedura ";
			$ris = $pdo->bindAndExec($sql,array(":codice_procedura"=>$record_gara["procedura"]));
			if ($ris->rowCount()>0) {
				$rec_procedura = $ris->fetch(PDO::FETCH_ASSOC);
				$directory = $rec_procedura["directory"];
				$record["nome_procedura"] = $rec_procedura["nome"];
				$record["riferimento_procedura"] = $rec_procedura["riferimento_normativo"];
				if ($rec_procedura["mercato_elettronico"] == "S") $derivazione = "me";
				if ($rec_procedura["directory"] == "sda")  $derivazione = "sda";
				if ($rec_procedura["directory"] == "dialogo")  $dialogo = true;

			}

			$strsql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice";
			$ris_inviti = $pdo->bindAndExec($strsql,$bind);
			if ($ris_inviti->rowCount()>0) {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$strsql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice AND r_inviti_gare.codice_utente = :codice_utente";
				$ris_invitato = $pdo->bindAndExec($strsql,$bind);
				if ($ris_invitato->rowCount()>0) $accedi = true;
			} else {
				if($record_gara["invito"] == "N" || !empty($derivazione)) {
					$accedi = true;
				}
			}
			if ($derivazione != "") {
				$sql_abilitato = "SELECT * FROM r_partecipanti_".$derivazione." WHERE codice_bando = :codice_derivazione AND ammesso = 'S' AND codice_utente = :codice_utente ";
				$ris_abilitato = $pdo->bindAndExec($sql_abilitato,array(":codice_derivazione"=>$record_gara["codice_derivazione"],":codice_utente"=>$_SESSION["codice_utente"]));
				if ($ris_abilitato->rowCount() == 0) {
					$accedi = false;
				}
			}
		}
		if ($accedi) {
			$print_form = true;
			$record_gara["tipologie_gara"] = "";
			$bind = array();
			$bind[":tipologia"] = $record_gara["tipologia"];
			$sql = "SELECT tipologia FROM b_tipologie WHERE b_tipologie.codice = :tipologia";
			$ris_tipologie = $pdo->bindAndExec($sql,$bind);
			if ($ris_tipologie->rowCount()>0) {
				$rec_tipologia = $ris_tipologie->fetch(PDO::FETCH_ASSOC);
				$record_gara["tipologie_gara"] .= $rec_tipologia["tipologia"] . " ";
			}
			?>
			<h1><?= traduci("GENERA") ?> <?= traduci("OFFERTA") ?> - ID <? echo $record_gara["id"] ?></h1>
			<h2><strong><? echo traduci(trim($record_gara["tipologie_gara"])) ?></strong> - <? echo $record_gara["oggetto"] ?></h2>
			<?
			$bind = array();
			$bind[":codice_gara"] = $record_gara["codice"];
			$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara ORDER BY codice";
			$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
			if ($ris_lotti->rowCount() > 0) {
				$print_form =false;
				$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto ORDER BY codice";
				$bind[":codice_lotto"] = $codice_lotto;
				$ris_check_lotti = $pdo->bindAndExec($sql_lotti,$bind);
				if ($ris_check_lotti->rowCount() > 0) {
						$lotto = $ris_check_lotti->fetch(PDO::FETCH_ASSOC);
						if ($record_gara["modalita_lotti"]==1) {
							$bind =array();
							$bind[":codice_gara"] = $record_gara["codice"];
							$bind[":codice_utente"] = $_SESSION["codice_utente"];
							$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND conferma = TRUE AND codice_utente = :codice_utente";
							$ris_partecipazioni = $pdo->bindAndExec($sql,$bind);
							if ($ris_partecipazioni->rowCount() > 0) {
								$bind = array();
								$bind[":lotto"] = $codice_lotto;
								$bind[":codice_gara"] = $record_gara["codice"];
								$bind[":codice_utente"] = $_SESSION["codice_utente"];
								$sql = "SELECT * FROM r_partecipanti WHERE codice_lotto = :lotto AND conferma = TRUE AND codice_gara = :codice_gara AND codice_utente = :codice_utente";
								$ris_partecipante_lotto = $pdo->bindAndExec($sql,$bind);
								if ($ris_partecipante_lotto->rowCount() > 0) {
									$print_form = true;
								} else {
									?>
									<h2 style="color:#C00"><?= traduci("Impossibile partecipare a più lotti") ?></h2>
									<?
								}
							} else {
								$print_form = true;
							}
					} else {
						$print_form = true;
					}
				}
			} else {
				$codice_lotto = 0;
			}

			if ($print_form) {

				$submit = false;

				if (isset($lotto)) {
					$codice_lotto = $lotto["codice"];
					echo "<div class=\"box\"><h3>" . $lotto["oggetto"] . "</h3>";
					echo $lotto["descrizione"]."</div>";
				}

				if (strtotime($record_gara["data_scadenza"]) > time()) {
						$submit = true;
				} else if ($record_gara["fasi"] == 'S') {
					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$bind[":codice_utente"] = $_SESSION["codice_utente"];
					$bind[":lotto"] = $codice_lotto;
					$sql_fase = "SELECT r_partecipanti.* FROM r_partecipanti JOIN b_2fase ON r_partecipanti.codice_gara = b_2fase.codice_gara AND r_partecipanti.codice_lotto = b_2fase.codice_lotto
											WHERE r_partecipanti.codice_utente = :codice_utente AND r_partecipanti.codice_lotto = :lotto AND r_partecipanti.codice_gara = :codice_gara
											AND ammesso = 'S' AND escluso = 'N' AND b_2fase.data_inizio <= now() AND b_2fase.data_fine > now()";
					$ris_fase = $pdo->bindAndExec($sql_fase,$bind);
					if ($ris_fase->rowCount() > 0) $submit = true;
				}

				$filtro_mercato = "";
				if ($record_gara["mercato_elettronico"]=="S") $filtro_mercato = " AND mercato_elettronico = 'S' ";
				$filtro_fase = "";
				if ($record_gara["fasi"]=="S") {
					if (strtotime($record_gara["data_scadenza"]) > time()) {
						$filtro_fase = " AND 2fase = 'N' ";
					}
				}

				if (isset($dialogo) && $dialogo = true) $filtro_fase = " AND 2fase = 'S' ";

				$bind = array();
				$bind[":codice_criterio"] = $record_gara["criterio"];

				$strsql = "SELECT b_criteri_buste.* FROM b_criteri_buste
									 WHERE codice_criterio = :codice_criterio " . $filtro_fase . $filtro_mercato ." AND ";
				if (isset($_GET["economica"])) {
					$strsql .= " economica = ";
					$tipo = "economica";
				} else if (isset($_GET["tecnica"])) {
					$strsql .= " tecnica = ";
					$tipo = "tecnica";
				}
				$strsql.= " 'S' AND eliminato = 'N' LIMIT 0,1";
				$ris_buste = $pdo->bindAndExec($strsql,$bind);
				if ($ris_buste->rowCount() > 0 && $submit && isset($tipo)) {
					?>
					<form action="save_offerta.php" id="upload_busta" method="post" target="_self" rel="validate">
			      <input type="hidden" name="codice_gara" value="<? echo $record_gara["codice"] ?>">
			      <input type="hidden" name="codice_lotto" value="<? echo $codice_lotto ?>">
			      <input type="hidden" name="tipo" value="<? echo $tipo ?>">
						<?
						if ($record_gara["nuovaOfferta"] == "S") {
							include_once('form-offerta/new.php');
						} else {
							include_once('form-offerta/old.php');
						}
						?>
						<div class="modulo_partecipazione">
							<?= traduci("Chiave personalizzata") ?>*<br>
							<input class="titolo_edit" style="width:25%" type="password" name="salt" id="salt" title="<?= traduci("Chiave personalizzata") ?>" rel="S;12;0;P"><br>
							<?= traduci('Minimo 12 caratteri') ?><br><br>
							<?= traduci("Ripeti") ?> <?= traduci("Chiave personalizzata") ?>*<br>
							<input class="titolo_edit" style="width:25%" type="password" id="repeat-salt" title="<?= traduci("Chiave personalizzata") ?>" rel="S;12;0;P;salt;="><br><br>
							<span style="font-weight:normal"><?= traduci('memo-chiave') ?></span>
						</div>
						<input type="submit" class="submit_big" value="<?= traduci("Salva") ?>" id="invio" onClick="if (confirm('<?= traduci("msg-conferma-revoca") ?>')) { $('#upload_busta').submit(); } else { return false; }">
					</form>
					<a class="submit_big" style="background-color:#444" href="/gare/telematica2.0/modulo.php?cod=<?= $codice_gara ?><?= ($codice_lotto > 0) ? "&codice_lotto=".$codice_lotto : "" ?>"><?= traduci("Ritorna al pannello") ?></a>
					<?
				} else {
					if (!$submit) {
						echo "<h1>" . traduci("Impossibile accedere") . ": " . traduci("Termini scaduti") . "</h1>";
					} else {
						echo "<h1>" . traduci("Impossibile accedere") . ": 1</h1>";
					}
				}
			} else {
				echo "<h1>" . traduci("Impossibile accedere") . ": 2</h1>";
			}
		} else {
			echo "<h1>" . traduci("Impossibile accedere") . ": 3</h1>";
		}
	} else {
		echo "<h1>" . traduci("Impossibile accedere") . ": 4</h1>";
	}
	include_once($root."/layout/bottom.php");
	?>
