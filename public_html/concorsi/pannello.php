<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("concorsi",$_SESSION["codice_utente"]);
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
				$bind = array();
				$bind[":codice"] = $codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql  = "SELECT b_concorsi.*, b_conf_stati_concorsi.titolo AS fase, b_conf_stati_concorsi.colore ";
				$strsql .= "FROM b_concorsi JOIN b_conf_stati_concorsi ON b_concorsi.stato = b_conf_stati_concorsi.fase ";
				if ($_SESSION["gerarchia"] == 2) {
					$strsql .= "JOIN b_permessi_concorsi ON b_concorsi.codice = b_permessi_concorsi.codice_gara ";
				}
				$strsql .= "WHERE b_concorsi.codice = :codice ";
				$strsql .= "AND b_concorsi.codice_gestore = :codice_ente ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (b_concorsi.codice_ente = :codice_ente_utente OR b_concorsi.codice_gestore = :codice_ente_utente) ";
				}
				if ($_SESSION["gerarchia"] > 1) {
					$bind[":codice_utente"] = $_SESSION["codice_utente"];
					$strsql .= " AND (b_permessi_concorsi.codice_utente = :codice_utente)";
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$scaduta = "N";
					$apertura = "N";

					$bind = array();
					$bind[":codice_gara"] = $record["codice"];
					$sql = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara AND attiva = 'S' ORDER BY codice DESC LIMIT 0,1";
					$ris = $pdo->bindAndExec($sql,$bind);
					if ($ris->rowCount() > 0) {
						$fase = $ris->fetch(PDO::FETCH_ASSOC);
						if (strtotime($fase["scadenza"])<=time()) $scaduta = "S";
						if (strtotime($fase["apertura"])<=time()) $apertura = "S";
					}
					if (($record["stato"]==3) && ($scaduta=="S")) {
						$record["colore"] = $config["colore_scaduta"];
						 $record["fase"] = "Scaduta";
					} ?>
          <h1>PANNELLO DI GESTIONE - CONCORSO #<? echo $record["id"] ?> <input type="image" onclick="$('#info').toggle()" src="/img/info.png" title="Ulteriori informazioni"></h1>
          <div id="info" class="ui-state-error padding" style="display:none">
          	<h2>Ulteriori informazioni</h2>
          	<table width="100%">
          		<tr><td class="etichetta">Codice Gara Univoco</td><td><strong><? echo $record["codice"]; ?></strong></td></tr>
          		<tr><td class="etichetta">Codice Gara Relativo</td><td><strong><? echo $record["id"]; ?></strong></td></tr>
          		<tr><td class="etichetta">Codice Ente</td><td><strong><? echo $record["codice_ente"]; ?></strong></td></tr>
          	</table>
          </div>
					<h2><? echo $record["oggetto"] ?></h2>
					<h3 style="text-align:right">Stato: <strong><? echo $record["fase"] ?></strong></h3>
					<div style="background-color:#<? echo $record["colore"] ?>; padding:5px;"></div>
					<br>
					<?

						$sql = "SELECT tipo FROM b_conf_gestione_concorsi GROUP BY tipo ORDER BY  min(ordinamento)";
						$ris_tipi = $pdo->query($sql);
						if ($ris_tipi->rowCount() > 0) {
							while($rec_tipo = $ris_tipi->fetch(PDO::FETCH_ASSOC)) {
								$bind = array();
								$bind[":tipo"] = $rec_tipo["tipo"];
								$bind[":stato_a"] = $record["stato"].",%";
								$bind[":stato_b"] = "%,".$record["stato"].",%";
								$bind[":stato_c"] = "%,".$record["stato"];
								$bind[":stato_d"] = $record["stato"];

								$sql = "SELECT b_conf_gestione_concorsi.* FROM b_conf_gestione_concorsi ";
								$sql .= " WHERE b_conf_gestione_concorsi.tipo = :tipo AND b_conf_gestione_concorsi.fase_minima <= :stato_d ";
								$sql .= " AND ((b_conf_gestione_concorsi.stati_esclusi NOT LIKE :stato_b ";
								$sql .= " AND b_conf_gestione_concorsi.stati_esclusi NOT LIKE :stato_c ";
								$sql .= " AND b_conf_gestione_concorsi.stati_esclusi NOT LIKE :stato_a) OR b_conf_gestione_concorsi.stati_esclusi IS NULL) ";
								if ($scaduta == "N") $sql .= " AND b_conf_gestione_concorsi.scaduta = 'N'";
								if ($apertura == "N") $sql .= " AND b_conf_gestione_concorsi.apertura = 'N'";
								$sql .= " ORDER BY b_conf_gestione_concorsi.ordinamento";
								$ris_comandi = $pdo->bindAndExec($sql,$bind);

								if ($ris_comandi->rowCount() > 0) {

									$percentuale = (100/$ris_tipi->rowCount()) - 1;
									?>
									<div style="float:left; width:<? echo $percentuale ?>%; margin:1px;">
									<h3><? echo ucfirst($rec_tipo["tipo"]) ?></h3>
									<?
									while($rec = $ris_comandi->fetch(PDO::FETCH_ASSOC)) {
										if ($rec["cross_p"] == "S" || $_SESSION["gerarchia"]==="0" || ($_SESSION["record_utente"]["codice_ente"] == $_SESSION["ente"]["codice"]) || $_SESSION["ente"]["permit_cross"]=="S") {
											if (check_permessi($rec["modulo_riferimento"],$_SESSION["codice_utente"])) {
											$show = true;
											$folder = explode("/",$rec["link"]);
											$temp = array_pop($folder);
											$folder = implode("/",$folder);
											if (file_exists($root.$folder."/check.php")) include($root.$folder."/check.php");
											if ($show) {
												$lock = check_lock_concorsi($rec["codice"],$record["codice"]);
											?>
											<a class="pannello <? if ($lock==true) echo "locked"?>" href="<? echo $rec["link"] ?><?= (strpos($rec["link"], "?") === false) ? "?" : "&" ?>codice=<? echo $record["codice"]?>" title="<? echo $rec["titolo"] ?>">
												<? echo $rec["titolo"] ?>
												<? if ($rec["badge"] != "") {
													if (file_exists($root.$rec["badge"])) include($root.$rec["badge"]);
												} ?>
											</a>
	            	    	<? }
											}
										}
									} ?>
									<div class="clear"></div>
			    	                </div>
                    			<? }
                    	} ?>
						  <div class="clear"></div>
                          <?
					}
			} else {
					echo "<h1>Non autorizzato - Contattare l'amministratore</h1>";
				}
		} else {
			echo "<h1>Gara non trovata</h1>";
		}

	include_once($root."/layout/bottom.php");
	?>
