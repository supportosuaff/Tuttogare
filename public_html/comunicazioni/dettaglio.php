<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	if (!is_operatore()) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
	} else {
		if (isset($_GET["cod"])) {
				$bind=array();
				$bind[":codice"] = $_GET["cod"];
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$bind[":codice_utente"] = $_SESSION["codice_utente"];

				$strsql = "SELECT b_comunicazioni.*, r_comunicazioni_utenti.protocollo, r_comunicazioni_utenti.data_protocollo AS data_protocollo_oe
									FROM b_comunicazioni JOIN r_comunicazioni_utenti ON b_comunicazioni.codice = r_comunicazioni_utenti.codice_comunicazione
									WHERE
									b_comunicazioni.codice = :codice
									AND b_comunicazioni.codice_ente = :codice_ente
									AND r_comunicazioni_utenti.codice_utente = :codice_utente";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()>0){
					$comunicazione = $risultato->fetch(PDO::FETCH_ASSOC);
					$bind=array();
					$bind[":codice"] = $comunicazione["codice"];
					$bind[":codice_utente"] = $_SESSION["codice_utente"];
					$sql = "UPDATE r_comunicazioni_utenti SET letto = 'S' WHERE codice_comunicazione = :codice AND codice_utente = :codice_utente";
					$ris = $pdo->bindAndExec($sql,$bind);
					include_once($root."/layout/top.php");
					$bind=array();
					$bind[":codice"] = $_GET["cod"];
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$bind[":codice_utente"] = $_SESSION["codice_utente"];

					$strsql = "SELECT b_comunicazioni.*, r_comunicazioni_utenti.protocollo, r_comunicazioni_utenti.data_protocollo AS data_protocollo_oe FROM b_comunicazioni JOIN r_comunicazioni_utenti ON b_comunicazioni.codice = r_comunicazioni_utenti.codice_comunicazione WHERE
										b_comunicazioni.codice = :codice
										AND b_comunicazioni.codice_ente = :codice_ente
										AND r_comunicazioni_utenti.codice_utente = :codice_utente";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					$comunicazione = $risultato->fetch(PDO::FETCH_ASSOC);
					$protocollo = "n." . $comunicazione["codice"] . " del " . mysql2date($comunicazione["timestamp"]) . " - <small>Assegnato dal sistema</small>";
					if (!empty($comunicazione["protocollo"])) {
						$protocollo = "n. " . $comunicazione["protocollo"] . " del " . mysql2date($comunicazione["data_protocollo_oe"]);
					}
					if ($comunicazione["cod_allegati"] != "" && preg_match("/^[0-9\;]+$/",$comunicazione["cod_allegati"])) {
								$allegati = explode(";",$comunicazione["cod_allegati"]);
								$str_allegati = ltrim(implode(",",$allegati),",");
								$sql = "SELECT * FROM b_allegati WHERE codice IN (" . $str_allegati . ")";
								$ris_allegati = $pdo->query($sql);
					}
					?>
          <h1>PROT. <? echo $protocollo ?></h1>
          <? echo $comunicazione["corpo"] ?>
          <? if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
						?>
            <div class="box"><h2><?= traduci("Allegati") ?></h2>
		          <table width="100%" id="tab_allegati">
		          <?
							while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
								include($root."/allegati/tr_allegati.php");
							}
							?>
		          </table>
            </div>
              <?
						}

						if ($comunicazione["codice_gara"] != "") {

							$bind=array();
							$bind[":codice_gara"] = $comunicazione["codice_gara"];
							$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

						if ($comunicazione["sezione"]=="gara") {
							$bind[":codice_utente"] = $_SESSION["codice_utente"];
							$sql  = "SELECT b_gare.* FROM b_gare LEFT JOIN r_inviti_gare ON b_gare.codice = r_inviti_gare.codice_gara JOIN b_procedure ON b_gare.procedura = b_procedure.codice ";
							$sql .= "WHERE (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente) AND ";
							$sql .= "(b_gare.pubblica = '2' OR (pubblica = '1' AND ((b_procedure.invito = 'N' AND r_inviti_gare.codice_utente IS NULL) OR (b_procedure.invito = 'S' AND r_inviti_gare.codice_utente = :codice_utente)))) AND b_gare.codice = :codice_gara";
							$ris = $pdo->bindAndExec($sql,$bind);
							if ($ris->rowCount()>0) {
								$rec_gara = $ris->fetch(PDO::FETCH_ASSOC);
								?><div class="box"><h2><?= traduci("riferimento") ?></h2>
										<a href="/gare/id<? echo $rec_gara["codice"] ?>-dettagli" title="Dettagli gara"><strong>ID: <? echo $rec_gara["id"] ?></strong> - <? echo $rec_gara["oggetto"] ?></a>
									</div>
	                            <?
							}
						} else if ($comunicazione["sezione"]=="mercato") {
							$sql  = "SELECT b_bandi_mercato.* FROM b_bandi_mercato ";
							$sql .= "WHERE (b_bandi_mercato.codice_ente = :codice_ente OR b_bandi_mercato.codice_gestore = :codice_ente) AND ";
							$sql .= "(b_bandi_mercato.pubblica = '2' OR b_bandi_mercato.pubblica = '1') AND b_bandi_mercato.codice = :codice_gara";
							$ris = $pdo->bindAndExec($sql,$bind);
							if ($ris->rowCount()>0) {
								$rec_gara = $ris->fetch(PDO::FETCH_ASSOC);
								?><div class="box"><h2><?= traduci("riferimento") ?></h2>
										<a href="/mercato_elettronico/id<? echo $rec_gara["codice"] ?>-dettagli" title="Dettagli bando"><strong>ID: <? echo $rec_gara["id"] ?></strong> - <? echo $rec_gara["oggetto"] ?></a>
									</div>
								<?
							}
						}
					} ?>
                    <?
				} else {
					echo "<h1>".traduci('impossibile accedere')."</h1>";
				}
		} else {
			echo "<h1>".traduci('impossibile accedere')."</h1>";
		}
	}
	include_once($root."/layout/bottom.php");
	?>
