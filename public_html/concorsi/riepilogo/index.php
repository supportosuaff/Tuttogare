<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
				if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
				if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
					$codice_fase = getFaseConcorso($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
					if ($codice_fase!==false) {
						$esito = check_permessi_concorso($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
						$edit = $esito["permesso"];
						$lock = $esito["lock"];
					}
					if (!$edit) {
						echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
						die();
					}
				} else {
					echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
					die();
				}
				$codice = $_GET["codice"];

				$bind = array();
				$bind[":codice"]=$codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

				$strsql = "SELECT * FROM b_concorsi WHERE codice = :codice";
				$strsql .= " AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= " AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}

				$risultato = $pdo->bindAndExec($strsql,$bind);

				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					if (empty($_GET["codice_fase"])) {
						$bind = array();
						$bind[":codice_gara"] = $record["codice"];
						$sql = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara AND apertura <= now() ORDER BY codice ASC";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount() > 0) {
							echo "<h1>RIEPILOGO FASE: Scelta fase</h1>";
							while($fase = $ris->fetch(PDO::FETCH_ASSOC)) {
								?>
								<a href="index.php?codice=<?= $record["codice"] ?>&codice_fase=<?= $fase["codice"] ?>" class="submit_big">
									<?= $fase["oggetto"] ?>
								</a>
								<?
							}
						}
					} else {
						$bind = array();
						$bind[":codice_gara"] = $record["codice"];
						$bind[":codice_fase"] = $_GET["codice_fase"];
						$sql = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara AND apertura <= now() AND codice = :codice_fase ORDER BY codice DESC LIMIT 0,1";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount() > 0) {
						$fase = $ris->fetch(PDO::FETCH_ASSOC);
								ob_start();
								echo "<h1>". $record["oggetto"] . "<br>RIEPILOGO FASE: " . $fase["oggetto"] . "</h1>";
								$bind = array();
								$bind[":codice"]=$record["codice"];
								$bind[":codice_fase"]=$fase["codice"];
								$sql = "SELECT r_partecipanti_concorsi.*, r_partecipanti_utenti_concorsi.codice_operatore, r_partecipanti_utenti_concorsi.codice_utente,
												r_partecipanti_utenti_concorsi.partita_iva, r_partecipanti_utenti_concorsi.ragione_sociale, r_partecipanti_utenti_concorsi.identificativoEstero,
												r_partecipanti_utenti_concorsi.pec
												FROM r_partecipanti_concorsi
												JOIN r_partecipanti_utenti_concorsi ON r_partecipanti_concorsi.codice = r_partecipanti_utenti_concorsi.codice_partecipante
												WHERE codice_gara = :codice AND codice_fase = :codice_fase AND (conferma = TRUE OR conferma IS NULL) ORDER BY primo DESC,secondo DESC, codice ASC";
								$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
								if ($ris_partecipanti->rowCount()>0) {
								?>
								<table id="buste" width="100%">
									<thead>
										<tr>
											<td>Identificativo</td>
											<td>Codice fiscale</td>
											<td>Denominazione</td>
											<td>P.E.C.</td>
											<td>Identificativo Estero</td>
											<td>Punteggio</td>
											<td>Stato</td>
										</tr>
									</thead>
									<tbody>
									<?
									while ($record_partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
										include("tr_partecipante.php");
									}
									?></tbody>
									</table>
									<br>
					<?
								$riepilogo = ob_get_clean();
								$_SESSION["riepilogo_fase_concorso"][$record["codice"]][$fase["codice"]] = $riepilogo;
								echo $riepilogo;
								?>
								<a target="_blank" class="submit_big" href="download.php?codice=<?= $record["codice"] ?>&codice_fase=<?= $fase["codice"] ?>">Scarica PDF</a>
								<a href="index.php?codice=<?= $record["codice"] ?>" class="submit_big" style="background-color:#999;">Scegli fase</a>
								<?
						} else {
							echo "<h1>ATTENZIONE</h1>";
							echo "<h3>Non sono presenti partecipanti</h3>";
						}
				} else {
					echo "<h1>IMPOSSIBILE ACCEDERE</h1>";
					echo "<h3>Fase inesistente</h3>";
				}
			}
			include($root."/concorsi/ritorna.php");
			} else {
				echo "<h1>Concorso non trovato</h1>";
			}
		} else {
			echo "<h1>Concorso non trovato</h1>";
		}
?>
<?
	include_once($root."/layout/bottom.php");
	?>
