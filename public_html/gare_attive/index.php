<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	if (!is_operatore()) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
	} else {
	if (isset($_SESSION["ente"])) {
	?><h1>Gare attive</h1><?
		$bind=array();
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

		$strsql  = "SELECT b_gare.*, b_tipologie.tipologia AS tipologia, b_criteri.criterio AS criterio, b_procedure.nome AS procedura, b_procedure.invito
								FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice
								JOIN b_criteri ON b_gare.criterio = b_criteri.codice
								JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice
								JOIN b_enti ON b_gare.codice_gestore = b_enti.codice
								WHERE b_gare.annullata = 'N' AND codice_gestore = :codice_ente
								AND pubblica > 0 ORDER BY id DESC, codice DESC" ;
		$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
		if ($risultato->rowCount()>0) {
			$array_risultati = array();
			$array_risultati[0] = array();
			$array_risultati[1] = array();

							$sql_inviti = "SELECT * FROM r_inviti_gare WHERE codice_utente = :codice_utente AND codice_gara = :codice_gara";
							$ris_inviti = $pdo->prepare($sql_inviti);

							$sql_partecipanti = "SELECT codice FROM r_partecipanti WHERE codice_utente = :codice_utente AND codice_gara = :codice_gara";
							$ris_partecipanti = $pdo->prepare($sql_partecipanti);

							$sql_cpv = "SELECT r_cpv_operatori.codice
													FROM r_cpv_operatori JOIN r_cpv_gare ON r_cpv_gare.codice LIKE CONCAT(r_cpv_operatori.codice,'%')
													WHERE r_cpv_gare.codice_gara = :codice_gara AND r_cpv_operatori.codice_utente = :codice_utente ";
							$ris_cpv = $pdo->prepare($sql_cpv);

							$sql_fase = "SELECT * FROM r_partecipanti JOIN b_2fase ON r_partecipanti.codice_gara = b_2fase.codice_gara AND r_partecipanti.codice_lotto = b_2fase.codice_lotto
												 WHERE r_partecipanti.codice_gara = :codice_gara AND data_fine > now() AND ammesso = 'S' AND escluso = 'N' AND codice_utente = :codice_utente";
							$ris_fase = $pdo->prepare($sql_fase);

							$sql_asta = "SELECT * FROM r_partecipanti JOIN b_aste ON r_partecipanti.codice_gara = b_aste.codice_gara AND r_partecipanti.codice_lotto = b_aste.codice_lotto
												 WHERE r_partecipanti.codice_gara = :codice_gara AND data_fine > now() AND ammesso = 'S' AND escluso = 'N' AND codice_utente = :codice_utente";
							$ris_asta = $pdo->prepare($sql_asta);

							$sql_integrazioni = "SELECT r_integrazioni.* FROM r_integrazioni JOIN b_integrazioni ON r_integrazioni.codice_integrazione = b_integrazioni.codice
												 WHERE b_integrazioni.codice_gara = :codice_gara AND b_integrazioni.data_scadenza > now() AND r_integrazioni.codice_utente = :codice_utente";
							$ris_integrazioni = $pdo->prepare($sql_integrazioni);

							while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
								$echo = false;
								$aperta = false;
								if (strtotime($record["data_scadenza"]) > time()) {
									if ($record["invito"]=="S") {
										$ris_inviti->bindValue(":codice_gara",$record["codice"]);
										$ris_inviti->bindValue(":codice_utente",$_SESSION["codice_utente"]);
										$ris_inviti->execute();
										if ($ris_inviti->rowCount()>0) $echo  = true;
									} else {
										$ris_partecipanti->bindValue(":codice_gara",$record["codice"]);
										$ris_partecipanti->bindValue(":codice_utente",$_SESSION["codice_utente"]);
										$ris_partecipanti->execute();
										if ($ris_partecipanti->rowCount()>0) {
											$echo  = true;
										} else {
											$ris_cpv->bindValue(":codice_gara",$record["codice"]);
											$ris_cpv->bindValue(":codice_utente",$_SESSION["codice_utente"]);
											$ris_cpv->execute();
											if ($ris_cpv->rowCount()>0) {
												 $echo  = true;
												 $aperta = true;
											}
										}
									}
								} else {
									$ris_fase->bindValue(":codice_gara",$record["codice"]);
									$ris_fase->bindValue(":codice_utente",$_SESSION["codice_utente"]);
									$ris_fase->execute();
									if ($ris_fase->rowCount()>0) {
										$echo  = true;
									} else {
										$ris_asta->bindValue(":codice_gara",$record["codice"]);
										$ris_asta->bindValue(":codice_utente",$_SESSION["codice_utente"]);
										$ris_asta->execute();
										if ($ris_asta->rowCount()>0) {
											$echo  = true;
										} else {
											$ris_integrazioni->bindValue(":codice_gara",$record["codice"]);
											$ris_integrazioni->bindValue(":codice_utente",$_SESSION["codice_utente"]);
											$ris_integrazioni->execute();
											if ($ris_integrazioni->rowCount()>0) $echo  = true;
										}
									}
								}
						if ($echo) {
							if (!$aperta) {
								$array_risultati[0][] = $record;
							} else {
								$array_risultati[1][] = $record;
							}
						}
					}
				}
				if (count($array_risultati[0]) > 0 || count($array_risultati[1]) > 0) {
					?>
					<div id="tabs">
						<ul>
							<?
								foreach($array_risultati AS $index => $risultato) {
									if (count($risultato) > 0) {
										?>
										<li><a href="#tab<?= $index ?>"><?= ($index===0) ? "Attive" : "Di interesse"; ?> <strong><?= count($risultato) ?></strong></a></li>
										<?
									}
								}
							?>
						</ul>
						<?
						foreach($array_risultati AS $index => $risultato) {
							if (count($risultato) > 0) {
						?>
								<div id="tab<?= $index ?>">
									<h2><?= ($index===0) ? "Attive" : "Di interesse"; ?></h2>
									<table width="100%" class="elenco">
										<thead>
											<tr>
												<td>ID</td>
												<td>Tipologia</td>
												<td>Criterio</td>
												<td>Procedura</td>
												<td>Oggetto</td>
												<td>Scadenza</td>
											</tr>
										</thead>
										<tbody>
										<? foreach ($risultato as $record) { ?>
				    					<tr id="<? echo $record["codice"] ?>">
												<td width="5%"><? echo $record["id"] ?></td>
												<td><? echo $record["tipologia"] ?></td>
												<td><? echo $record["criterio"] ?></td>
												<td><? echo $record["procedura"] ?></td>
												<td width="75%"><a href="/gare/id<? echo $record["codice"] ?>-dettaglio" title="Dettagli gara"><? echo $record["oggetto"] ?></a></td>
												<td><? echo mysql2datetime($record["data_scadenza"]) ?></td>
											</tr>
											<?
										}
										?>
										</tbody>
						    	</table>
									<div class="clear"></div>
								</div>
						<? }
						}
					?>
							</div>
					<script>
						$("#tabs").tabs();
					</script>
					<?
		} else { ?>
			<h1 style="text-align:center">Nessuna gara attiva</h1>
    <? }
		}
	}
	include_once($root."/layout/bottom.php");
	?>
