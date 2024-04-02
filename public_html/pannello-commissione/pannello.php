<?
	include_once("../../config.php");
	$error_permessi = true;
	if (!empty($_SESSION["codice_commissario"]) && !empty($_GET["codice"]))
	{
		if (checkCommissario($_GET["codice"])) {
			$codice_gara = $_GET["codice"];
			$pagina_login = true;
			include_once($root."/layout/top.php");
			include_once($root."/pannello-commissione/layout/intestazione.php");
			if (isset($gara)) {
				$error_permessi = false;
				if (!isset($codice_lotto)) {
					$ris_lotti = $pdo->bindAndExec("SELECT * FROM b_lotti WHERE codice_gara = :codice",[":codice"=>$gara["codice"]]);
					if ($ris_lotti->rowCount() > 0) {
						while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
							?>
							<a class="submit_big" href="/pannello-commissione/pannello.php?codice=<?= $gara["codice"] ?>&codice_lotto=<?= $lotto["codice"] ?>">
								<?= $lotto["oggetto"] ?>
							</a>
							<?
						}
					} else {
						echo '<meta http-equiv="refresh" content="0;URL=/pannello-commissione/pannello.php?codice='.$gara["codice"].'&codice_lotto=0">';
						die();
					}
				} else {
					?>
					<h1>CRITERI DI VALUTAZIONE</h1>
					<div class="box">
						<table width="100%">
							<thead>
								<tr>
									<td width="10"></td>
									<td width="10"></td>
									<td width="80">Tipo</td>
									<td>Descrizione</td>
									<td width="100">Riferimento</td>
									<td width="50">Peso</td>
									<td width="10"></td>
									<td width="10"></td>
								</tr>
							</thead>
							<tbody>
								<?
									$i = 0;
									$rows = [];
									foreach($echo_criteri AS $codice_criterio => $sub_c) {
										$criterio = $criteri[$codice_criterio];
										$i++;
										$tmp = [];
										$tmp[] = $i;
										$tmp[] = "";
										$tmp[] = $criterio["tipo"];
										$tmp[] = $criterio["descrizione"];
										$tmp[] = $criterio["nome"];
										$tmp[] = $criterio["punteggio"];
										$tmp[] = $criterio["codice"];
										$rows[] = $tmp;
										if (!empty($sub_c)) {
											$j = 0;
											foreach($sub_c AS $codice_criterio => $more) {
												$sub = $criteri[$codice_criterio];
												$j++;
												$tmp = [];
												$tmp[] = $i;
												$tmp[] = $j;
												$tmp[] = $sub["tipo"];
												$tmp[] = $sub["descrizione"];
												$tmp[] = $sub["nome"];
												$tmp[] = $sub["punteggio"];
												$tmp[] = $sub["codice"];
												$rows[] = $tmp;
											}
										}
									}
									$numero_partecipanti = count($partecipanti);
									$riferimento = $numero_partecipanti;
									$sql = "SELECT * FROM b_coefficienti_commissari WHERE codice_gara = :codice_gara
													AND codice_lotto = :codice_lotto
													AND codice_commissario = :codice_commissario
													AND codice_criterio = :codice_criterio ";
									if ($coppie) {
										$riferimento = ($numero_partecipanti * ($numero_partecipanti-1)) / 2;
										$sql = "SELECT * FROM b_confronto_coppie WHERE codice_gara = :codice_gara
														AND codice_lotto = :codice_lotto
														AND codice_commissario = :codice_commissario
														AND codice_criterio = :codice_criterio ";
									}
									$ris_check = $pdo->prepare($sql);
									$ris_check->bindValue(":codice_gara",$gara["codice"]);
									$ris_check->bindValue(":codice_lotto",$codice_lotto);
									$ris_check->bindValue(":codice_commissario",$_SESSION["codice_commissario"]);
									foreach($rows AS $criterio) {
									?>
									<tr>
										<td <?= (empty($criterio[1])) ? 'colspan="2"' : '' ?>><?= $criterio[0] ?></td>
										<? if (!empty($criterio[1])) { ?><td><?= $criterio[0] ?></td><? } ?>
										<td><?= ($criterio[2] == "Q") ? "Qualitativo" : "Quantitativo" ?></td>
										<td><strong><?= $criterio[3] ?></strong></td>
										<td><?= $criterio[4] ?></td>
										<td style="text-align:center"><?= $criterio[5] ?></td>
										<td  style="text-align:center">
											<? if (in_array($criterio[6],$criteri_valutazione) !== false) {
												$checkPunteggio = $pdo->go("SELECT punteggio FROM b_punteggi_criteri WHERE codice_criterio = :criterio 
																										AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto ",
																										[":criterio"=>$criterio[6],":codice_gara"=>$gara["codice"],":codice_lotto"=>$codice_lotto]);
												if ($checkPunteggio->rowCount() == 0) {
													?>
													<a href="valuta.php?codice=<?= $gara["codice"] ?>&criterio=<?= $criterio[6] ?>&codice_lotto=<?= $codice_lotto ?>" class="btn-round btn-warning">
														<span class="fa fa-pencil"></span>
													</a>
													<?
												} else {
													?>
													Valutazione Chiusa
													<?
												}
										    $ris_check->bindValue(":codice_criterio",$criterio[6]);
												$ris_check->execute();
												$status = $ris_check->rowCount();
												if (empty($status)) {
													$colore = "#C00";
												} else if ($status < $riferimento) {
													$colore = "#FC0";
												} else {
													$colore = "#3C0";
												}

											} else {
												$colore = "#3C0";
											} ?>
										</td>
										<td>
					      			<div class="status_indicator" style="background-color: <?= $colore ?>"></div>
										</td>
									</tr>
									<?
									}
								?>
							</tbody>
						</table>
					</div>
					<h1>PARTECIPANTI</h1>
					<div class="box">
						<table width="100%">
							<thead>
								<tr>
									<td width="10">#</td>
									<td width="120">Partita IVA</td>
									<td>Ragione Sociale</td>
									<?
										$amministrativa = false;
									 	$tecnica = false;
										$sql = "SELECT b_allegati.codice FROM b_allegati JOIN b_buste ON b_allegati.codice = b_buste.codice_allegato JOIN b_criteri_buste ON b_buste.codice_busta = b_criteri_buste.codice AND b_buste.codice_gara = :codice_gara AND b_criteri_buste.tecnica = 'S' "; 
										$check = $pdo->go($sql,[":codice_gara" => $gara["codice"]]);
										if ($check->rowCount() > 0) {
											$tecnica = true;
											?><td width="10">Busta tecnica</td><?
										}
										$economica = false;
										$sql = "SELECT b_allegati.codice FROM b_allegati JOIN b_buste ON b_allegati.codice = b_buste.codice_allegato JOIN b_criteri_buste ON b_buste.codice_busta = b_criteri_buste.codice AND b_buste.codice_gara = :codice_gara AND b_criteri_buste.economica = 'S' "; 
										$check = $pdo->go($sql,[":codice_gara" => $gara["codice"]]);
										if ($check->rowCount() > 0) {
											$economica = true;
											?><td width="10">Busta Economica</td><?
										}
									?>
								</tr>
							</thead>
							<tbody>
								<?
									$i = "A";
									foreach($partecipanti AS $partecipante) {
										?>
										<tr>
											<td><?= $i ?></td>
											<td style="text-align:center"><?= $partecipante["partita_iva"] ?></td>
											<td><?= (!empty($partecipante["tipo"])) ? "<strong>RAGGRUPPAMENTO</strong> - " : "" ?><?= $partecipante["ragione_sociale"] ?></td>
											<? if ($amministrativa) { ?>
												<td><a target="_blank" href="/pannello-commissione/download.php?codice=<?= $gara["codice"] ?>&busta=amministrativa&partecipante=<?= $partecipante["codice"] ?>" class="btn-round btn-primary" title="Download Busta">
														<span class="fa fa-download"></span>
													</a>
												</td>
											<? } ?>
											<? if ($tecnica) { ?>
												<td><a target="_blank" href="/pannello-commissione/download.php?codice=<?= $gara["codice"] ?>&busta=tecnica&partecipante=<?= $partecipante["codice"] ?>" class="btn-round btn-primary" title="Download Busta">
														<span class="fa fa-download"></span>
													</a>
												</td>
											<? } ?>
											<? if ($economica) { ?>
												<td><a target="_blank" href="/pannello-commissione/download.php?codice=<?= $gara["codice"] ?>&busta=economica&partecipante=<?= $partecipante["codice"] ?>" class="btn-round btn-primary" title="Download Busta">
														<span class="fa fa-download"></span>
													</a>
												</td>
											<? } ?>
										</tr>
										<?
										$i++;
									}
								?>
							</tbody>
						</table>
					</div>
					<?
				}
			}
			include_once($root."/layout/bottom.php");
		}
	}
	if ($error_permessi) {
		?>
		<h1>Impossibile accedere: Non si dispone dei permessi necessari o la gara non è in uno stato compatibile</h1>
		<?
	}
	?>
