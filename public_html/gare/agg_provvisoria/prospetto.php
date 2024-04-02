<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
				if (isset($_GET["Cod"])) $_GET["codice"] = $_GET["cod"];
				if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
					$codice_fase = getFase($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
					if ($codice_fase!==false) {
						$esito = check_permessi_gara($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
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
				$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}
				$strsql .= " AND data_apertura <= now() ";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					?>
					<h1>Graduatoria provvisoria</h1>
					<?
					$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

					$bind = array();
					$bind[":codice"]=$record_gara["codice"];

					$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice ORDER BY codice";
					$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
					$print_form = false;
					if ($ris_lotti->rowCount()>0) {
						if (isset($_GET["lotto"])) {
							$codice_lotto = $_GET["lotto"];

							$bind = array();
							$bind[":codice"]=$codice_lotto;
							$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice ORDER BY codice";
							$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
							if ($ris_lotti->rowCount()>0) {
								$print_form = true;
								$lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC);
								echo "<h2>" . $lotto["oggetto"] . "</h2>";
							}
						} else {
							while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {

								$bind = array();
								$bind[":codice_gara"]=$record_gara["codice"];
								$bind[":codice_lotto"]=$lotto["codice"];

								$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND primo = 'S'";
								$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
								$style = "";
								$primo = "";
								if ($ris_partecipanti->rowCount()>0) {
									$primo = $ris_partecipanti->fetch(PDO::FETCH_ASSOC);
									$primo = "<br>" . $primo["partita_iva"] . " - " . $primo["ragione_sociale"];
									$style = "style=\"background-color:#0C0\"";
								}
								?>

									<a class="submit_big" <?= $style ?> href ="prospetto.php?codice=<? echo $record_gara["codice"] ?>&lotto=<? echo $lotto["codice"] ?>">
										<? echo $lotto["oggetto"] . $primo ?>
									</a>

								<?
							}
						}
					} else {
						$print_form = true;
						$codice_lotto = 0;
					}

					if ($print_form) {
						if (isset($lotto)) {
								$record_gara["oggetto"] .= " - Lotto: " . $lotto["oggetto"];
								$record_gara["messaggio_anomalia"] = $lotto["messaggio_anomalia"];
								$record_gara["ribasso"] = $lotto["ribasso"];
							}

					$editor_tipo = "graduatoria_provvisoria";

					$bind = array();
					$bind[":codice_gara"]=$record_gara["codice"];
					$bind[":codice_lotto"]=$codice_lotto;
					$bind[":tipo"] = $editor_tipo;

					$strsql = "SELECT * FROM b_documentale WHERE tipo=:tipo AND attivo = 'S' AND sezione = 'gara' AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount()>0) {
						$elemento = $risultato->fetch(PDO::FETCH_ASSOC);
						$html = $elemento["corpo"];
						$operazione = "UPDATE";
						$codice_elemento = $elemento["codice"];
					} else {
						$operazione = "INSERT";
						$codice_elemento = 0;
						$bind = array();
						$bind[":codice"] = $record_gara["criterio"];
						$sql = "SELECT * FROM b_criteri WHERE codice = :codice";
						$ris = $pdo->bindAndExec($sql,$bind);
						$directory = "default";
						if ($ris->rowCount()>0) {
							$rec = $ris->fetch(PDO::FETCH_ASSOC);
							$directory = $rec["directory"];
							$record_gara["nome_criterio"] = $rec["criterio"];
							$record_gara["riferimento_criterio"] = $rec["riferimento_normativo"];
						}
						$bind = array();
						$bind[":codice"] = $record_gara["procedura"];
						$sql = "SELECT * FROM b_procedure WHERE codice = :codice";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount()>0) {
							$rec = $ris->fetch(PDO::FETCH_ASSOC);
							$record_gara["nome_procedura"] = $rec["nome"];
							$record_gara["riferimento_procedura"] = $rec["riferimento_normativo"];
						}

						$bind = array();
						$bind[":codice_gara"]=$record_gara["codice"];

						$sql = "SELECT b_importi_gara.*, b_tipologie.tipologia FROM b_importi_gara JOIN b_tipologie ON b_importi_gara.codice_tipologia = b_tipologie.codice WHERE codice_gara = :codice_gara";
						$ris_importi = $pdo->bindAndExec($sql,$bind);

						$record_gara["tipologie_gara"] = "";
						$sql = "SELECT tipologia FROM b_tipologie JOIN b_importi_gara ON b_tipologie.codice = b_importi_gara.codice_tipologia WHERE b_importi_gara.codice_gara = :codice_gara";
						$ris_tipologie = $pdo->bindAndExec($sql,$bind);
						if ($ris_tipologie->rowCount()>0) {
							while($rec_tipologia = $ris_tipologie->fetch(PDO::FETCH_ASSOC)) {
								$record_gara["tipologie_gara"] .= $rec_tipologia["tipologia"] . " ";
							}
						}

						$bind = array();
						$bind[":codice_ente"]=$record_gara["codice_ente"];

						$sql = "SELECT * FROM b_enti WHERE codice = :codice_ente";
						$ris_ente = $pdo->bindAndExec($sql,$bind);
						if ($ris_ente->rowCount()>0) $record_appaltatore = $ris_ente->fetch(PDO::FETCH_ASSOC);

						$bind = array();
						$bind[":codice_ente"]=$record_gara["codice_gestore"];
						$sql = "SELECT * FROM b_enti WHERE codice = :codice_ente";
						$ris_gestore = $pdo->bindAndExec($sql,$bind);
						if ($ris_gestore->rowCount()>0) $record_gestore = $ris_gestore->fetch(PDO::FETCH_ASSOC);

				$html = "<html><head><style>";
				$html.= "body {	font-family: Tahoma, Geneva, sans-serif; }";
				$html.= "div { margin:1px; padding:10px;border:1px solid #000; } ;";
				$html.= "div div { margin:0px; padding:0px; margin-left:20px; border:none }";
				$html.= "table td { padding:2px; border:1px solid #000 } ";
				$html.= "table.no_border td { padding:2px; border:none; vertical-align:top;} ";
				$html.= "ol li ol {list-style-type:lower-alpha;}";
				$html.= "</style></head><body>";
				$i = 0;
				$html.= "<table style=\"width:100%\">";
				$html.= "<tr><td style=\"width:20%; text-align:center;\"><img width=\"100\" src=\"" . $config["link_sito"] . "/documenti/enti/" . $record_gestore["logo"] . "\"></td>";
				$html.= "<td style=\"width:60%; text-align:center;\">";
				$html.= "<h1 style=\"text-align:center\">" . $record_gestore["denominazione"] . "</h1>";
				if ($record_gestore["codice"] != $record_appaltatore["codice"]) {
					$html.= "<h1 style=\"text-align:center\">Stazione unica appaltante</h1>";
					$html.= "<h2 style=\"text-align:center\">" . $record_appaltatore["denominazione"] . "</h2>";
				}
				$html.= "</td>";
				$html.= "<td style=\"width:20%; text-align:center;\">";
				if ($record_gestore["codice"] != $record_appaltatore["codice"]) {
					$html.= "<img src=\"" . $config["link_sito"] . "/documenti/enti/" . $record_appaltatore["logo"] . "\" width=\"150\">";
				}
				$html.= "</td></tr>";
				$html.= "<tr><td colspan=\"3\" style=\"text-align:center\"><h2 style=\"text-align:center\">Prospetto di aggiudicazione</h2>";
				$html.= "Procedura: " . $record_gara["nome_procedura"] . " ai sensi dell'" . $record_gara["riferimento_procedura"] . "<br>";
				$html.= "Criterio: " . $record_gara["nome_criterio"] . " ai sensi dell'" . $record_gara["riferimento_criterio"];
				$html.= "</td></tr>";
				$html.= "<tr><td colspan=\"3\"><strong>Oggetto</strong>:" . $record_gara["oggetto"] . "</td></tr>";
				$html.= "</table><br>";

				$bind = array();
				$bind[":codice"]=$record_gara["criterio"];
				$sql = "SELECT * FROM b_criteri_punteggi WHERE codice_criterio = :codice ORDER BY ordinamento";
				$ris_punteggi = $pdo->bindAndExec($sql,$bind);
				$ris_punteggi = $ris_punteggi->fetchAll(PDO::FETCH_ASSOC);

				$bind = array();
				$bind[":codice_gara"]=$record_gara["codice"];
				$bind[":codice_lotto"]=$codice_lotto;

				$sql = "SELECT r_partecipanti.*, SUM(r_punteggi_gare.punteggio) as totale_punteggio FROM r_partecipanti JOIN r_punteggi_gare ";
				$sql.= " ON r_partecipanti.codice = r_punteggi_gare.codice_partecipante WHERE
								 r_punteggi_gare.codice_gara = :codice_gara AND r_punteggi_gare.codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)
								 GROUP BY r_punteggi_gare.codice_gara,  r_punteggi_gare.codice_lotto, r_punteggi_gare.codice_partecipante ORDER BY primo DESC, secondo DESC, ammesso DESC, escluso, totale_punteggio DESC, codice";
				//echo $sql;
				$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
				$numero_partecipanti = $ris_partecipanti->rowCount();
				if ($ris_partecipanti->rowCount()>0) {
					  	$html.= "<table style=\"width:100%\">";
                        	$html.= "<thead>";
                        	$html.= "<tr>";
						$html.= "<td style=\"width:15%\">Protocollo</td>";
						$html.= "<td style=\"width:15%\">Partita IVA</td>";
						$html.= "<td style=\"width:35%\">Ragione Sociale</td>";
						$html.= "<td style=\"width:6%\">Ammesso</td>";

						 	if (count($ris_punteggi)>0) {
								foreach ($ris_punteggi AS $punteggio) {
									$html.= "<td style=\"width:6%;\">" . $punteggio["nome"] . "</td>";
										}
									$html.="<td style=\"width:6%;\">Totale</td>";
									}
						$html.= "</tr>";
						$html.= "</thead><tbody>";
						while ($record_partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
							$color = "";
							$color_stato = "";
							$posizione = "";
							if ($record_partecipante["ammesso"] == "N") $color = "#FF6600";
							if ($record_partecipante["escluso"] == "S") $color = "#FFCC00";
							if ($record_partecipante["secondo"] == "S") {
								$color = "#33CCFF";
								$posizione = "Secondo";
							}
							if ($record_partecipante["primo"] == "S") {
								$color = "#99FF66";
								$posizione = "Aggiudicatario";
							}
						$html.= "<tr id=\"" . $record_partecipante["codice"] . "\">";
						if ($record_partecipante["numero_protocollo"] == "") {
							$html.= "<td style=\"width:15%\"><strong>" . $record_partecipante["codice"] . "</strong> del " . mysql2date($record_partecipante["timestamp"]) . "</td>";	
						} else {
							$html.= "<td style=\"width:15%\"><strong>" . $record_partecipante["numero_protocollo"] . "</strong> del " . mysql2date($record_partecipante["data_protocollo"]) . "</td>";
						}
						$html.= "<td style=\"width:15%; background-color:" . $color . "\" width=\"10\">" . $record_partecipante["partita_iva"] . "</td>";
						$html.= "<td  style=\"width:35%\">";
						if ($record_partecipante["tipo"] != "") $html.= "<strong>RAGGRUPPAMENTO</strong> - ";
						$html.= $record_partecipante["ragione_sociale"];
  						$html.= " - <strong>" . $posizione  . "</strong>";
						if ($record_partecipante["motivazione"]!="") $html.= "<br>" . $record_partecipante["motivazione"];
						$html.= "</td>";
						$html.= "<td  style=\"width:6%; text-align:center\">" . $record_partecipante["ammesso"] . "</td>";
						if (count($ris_punteggi)>0) {
							$totale_punti = 0;
							foreach ($ris_punteggi AS $punteggio) {
								$punti = 0;
								$bind = array();
								$bind[":codice_partecipante"] = $record_partecipante["codice"];
								$bind[":codice_gara"]=$record_gara["codice"];
								$bind[":codice_punteggio"] = $punteggio["codice"];
								$sql_punteggi  = "SELECT * FROM r_punteggi_gare WHERE codice_partecipante = :codice_partecipante";
								$sql_punteggi .= " AND codice_gara = :codice_gara";
								$sql_punteggi .= " AND codice_punteggio = :codice_punteggio";
								$ris_punteggio = $pdo->bindAndExec($sql_punteggi,$bind);
								if ($ris_punteggio->rowCount()>0) {
									$arr_punti = $ris_punteggio->fetch(PDO::FETCH_ASSOC);
									$punti = $arr_punti["punteggio"];
								}
								$html.= "<td style=\"width:6%; text-align:right\">" . floatval($punti) . "</td>";
								$totale_punti += $punti;
							}
							$html.="<td style=\"width:6%; text-align:right\">".floatval($totale_punti)."</td>";
						}
      		    $html .= "</tr>";
						}
						$html.= "</tbody></table>";
						$html.= "<br>";
						$html.= "<table>";
						$html.= "<tr><td style=\"width:10%; padding:5px; background-color:#99FF66\"></td><td style=\"width:90%\">Aggiudicatario</td></tr>";
						$html.= "<tr><td style=\"width:10%; padding:5px; background-color:#33CCFF\"></td><td style=\"width:90%\">Secondo classificato</td></tr>";
						$html.= "<tr><td style=\"width:10%; padding:5px; background-color:#FFCC00\"></td><td style=\"width:90%\">Escluso</td></tr>";
						$html.= "<tr><td style=\"width:10%; padding:5px; background-color:#FF6600\"></td><td style=\"width:90%\">Non Ammesso</td></tr>";
						$html.= "</table>";

						include($directory."/prospetto.php");

						if ($record_gara["numero_sorteggio"] != "") {
							$html.= "<br><br>";
							$html.= "L'aggiudicatario della gara &egrave; stato decretato con atto " . $record_gara["numero_sorteggio"] . " a seguito di sorteggio pubblico effettuato in data " . mysql2date($record_gara["data_sorteggio"]);
						}

						$html.= "</body></html>";
				}}
			?>
                <? if (!$lock) { ?>
                 <form name="box" method="post" action="save_graduatoria.php" rel="validate">
                 <input type="hidden" name="operazione" value="<? echo $operazione ?>">
				 <input type="hidden" name="codice" value="<? echo $codice_elemento; ?>">
                 <input type="hidden" name="codice_gara" value="<? echo $record_gara["codice"]; ?>">
                 <input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
                <input type="hidden" name="allega" id="allega" value="N">
						<div class="comandi">
							<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
						</div>
        <?	}

				$file_title = "Graduatoria_provvisoria";
				$orientamento = "L";
				include($root."/moduli/editor.php");
				?>
                <? if (!$lock) { ?>
                <? if ($codice_elemento>0) { ?>
				<input type="button" style="background-color:#C00" class="submit_big" onClick="elimina('<? echo $codice_elemento ?>','gare/agg_provvisoria');" src="/img/del.png" value="Rielabora Graduatoria">
				<? } ?>
                <input class="submit_big" type="submit" value="Salva">
				<input class="submit_big" type="submit" onclick="$('#allega').val('S');return true;" value="Salva ed Allega">
                </form>
                <?
				} else {
					?>
                    <script>
                        $(":input").not('.espandi').prop("disabled", true);
					</script>
                    <?
				}
			}
				include($root."/gare/ritorna.php");
				} else {
					echo "<h1>Gara non trovata</h1>";
				}
		} else {
			echo "<h1>Gara non trovata</h1>";
		}
		include_once($root."/layout/bottom.php");
?>
