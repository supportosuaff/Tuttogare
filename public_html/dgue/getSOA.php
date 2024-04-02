<?
	include_once("../../config.php");
	$echo = true;
	include_once($root."/inc/funzioni.php");
	include_once($root."/dgue/config.php");
	require_once($root.'/tcpdf/tcpdf_import.php');

	include_once($root."/inc/xml2json.php");
	if (isset($_GET["codice_dgue"]) && is_operatore()) {
		$codice_riferimento = $_GET["codice_dgue"];
		$bind = array();
		$bind[":codice_riferimento"] = $codice_riferimento;
		$bind[":codice_utente"] = $_SESSION["codice_utente"];
		$sql = "SELECT soa FROM b_dgue_compilati WHERE codice = :codice_riferimento AND
							codice_utente = :codice_utente";
		$ris_old = $pdo->bindAndExec($sql,$bind);
		if ($ris_old->rowCount() > 0) {
			$db_record = $ris_old->fetchAll(PDO::FETCH_ASSOC)[0];
			if (!empty($db_record["soa"])) {
				$soa = json_decode($db_record["soa"],true);
				if ($soa["tipo"]=="non_applicabile") unset($soa);
			}
		}
		if (!empty($soa)) {
						ob_start();
						$styles["th"] = "font-weight: bold;background-color:#DDD;";
						?>
						<html>
							<style>
								h1 {
									text-align: center;
								}
								table {
									width:100%;
									border-spacing: 2px;
									padding:5px;
								}
								th {
									width:25%;
								}
								th,td {
									padding:10;
									border:1px solid #999;
								}
								.padding {
									padding: 20px;
								}

								.box {
									border-bottom:1px solid #999;
								}

								.dgue_label {
									background-color: #CCC;
									padding:20px;
									width:49%;
									float:left;
								}

								.clear {
									clear:both;
									margin:0px;
									padding:0px;
								}
							</style>
							<body>
								<h1>Dichiarazioni su S.O.A. o requisiti sostitutivi richiesti dal Disciplinare di Gara</h1>
								Si dichiara che il requisito richiesto dal disciplinare di gara, &egrave; posseduto da questa impresa come segue:<br><br>
								<?
									if ($soa["tipo"] == "soa") {
										?>
										Attestazione S.O.A. cos&igrave; posseduta:<br><br>
										<?
										  foreach ($soa["certificati"] as $certificato_soa) {
												?>
												<table width="100%">
													<tr><th style="<?= $styles["th"] ?>">Ente certificatore</th><td colspan="3"><? echo $certificato_soa["ente"] ?></td></tr>
												  <tr>
												    <th style="<?= $styles["th"] ?>">Numero attestazione</th>
												    <td style="width:25%"><? echo $certificato_soa["numero"] ?></td>
												    <th style="<?= $styles["th"] ?>">Importo</th>
												    <td>&euro; <? echo number_format($certificato_soa["importo"],2,",",".") ?></td>
													</tr>
												  <tr><th style="<?= $styles["th"] ?>">Categoria</th><td style="width:25%">
															<?
																$sql_soa = "SELECT * FROM b_categorie_soa WHERE attivo = 'S' AND codice = :codice_categoria ORDER BY codice";
																$ris_elenco_soa = $pdo->bindAndExec($sql_soa,array(":codice_categoria"=>$certificato_soa["codice_categoria"]));
																if ($ris_elenco_soa->rowCount()>0) {
																while($oggetto_soa = $ris_elenco_soa->fetch(PDO::FETCH_ASSOC)) {
																?>
																<strong><? echo $oggetto_soa["id"] ?></strong> - <? echo $oggetto_soa["descrizione"] ?>
																<?
																}
															}
														?>
														</td>
														<th style="<?= $styles["th"] ?>">Classifica</th><td style="width:25%">
															<?
												        	$sql_soa = "SELECT * FROM b_classifiche_soa WHERE attivo = 'S' AND codice = :codice_classifica ORDER BY codice";
												        	$ris_elenco_soa = $pdo->bindAndExec($sql_soa,array(":codice_classifica"=>$certificato_soa["codice_classifica"]));
												        	if ($ris_elenco_soa->rowCount()>0) {
												        		while($oggetto_soa = $ris_elenco_soa->fetch(PDO::FETCH_ASSOC)) {
																			?>
																			<strong><? echo $oggetto_soa["id"] ?></strong>
																			<?
												        		}
												        	}
												        ?>
														</td></tr>
														<tr>
												    <th style="<?= $styles["th"] ?>">Data rilascio</th><td style="width:25%"><? echo $certificato_soa["data_rilascio"] ?></td>
												     <th style="<?= $styles["th"] ?>">Data scadenza</th><td style="width:25%"><? echo $certificato_soa["data_scadenza"] ?></td></tr>
												   </table>
												<?
											}
									} else if ($soa["tipo"] == "alternativo") {
										?>
										in sostituzione dell'attestazione S.O.A. &egrave; in possesso dei seguenti requisiti di cui all'articolo 90, comma 1, primo periodo, del d.P.R. n. 207 del 2010:<br>
										requisiti tecnici di cui all'articolo 90 del d.P.R. n. 207 del 2010, come segue:
										<ol>
											<li>
												importo dei lavori eseguiti direttamente nel quinquennio antecedente la data di pubblicazione del bando non inferiore all'importo dei lavori in appalto; i lavori eseguiti sono analoghi a quelli in gara e pertanto riconducibili alla declaratoria di una o pi&ugrave; d'una delle categorie di cui all'allegato A al d.P.R. n. 207 del 2010;
											</li>
											<li>
												costo complessivo sostenuto per il personale dipendente non inferiore al 15% dell'importo dei lavori eseguiti nel quinquennio antecedente la data di pubblicazione del bando;
											</li>
											<li>adeguata attrezzatura tecnica, come richiesta dal disciplinare di gara;</li>
										</ol>
										<table width="100%">
							        <tr>
							          <th style="<?= $styles["th"] ?>">
							            Anno
							          </th>
							          <th style="<?= $styles["th"] ?>">
							            Importo lavori
							          </th>
							          <th style="<?= $styles["th"] ?>">
							            Costo personale
							          </th>
							        </tr>
							        <?
											$totale_lavori = 0;
											$totale_personale = 0;
							        for ($i=0;$i<5;$i++) {

												if (!empty($soa["dichiarazioni"][$i]["anno"])) {
							        ?>
							        <tr>
							          <td>
							            <?= $soa["dichiarazioni"][$i]["anno"] ?>
							          </td>
							          <td>
							            <?= (!empty($soa["dichiarazioni"][$i]["lavori"])) ? number_format($soa["dichiarazioni"][$i]["lavori"],2,",",".") : ""; ?>
							            <? if (!empty($soa["dichiarazioni"][$i]["valuta_lavori"])) {
							            echo $valute[$soa["dichiarazioni"][$i]["valuta_lavori"]];
							            }
													$totale_lavori += $soa["dichiarazioni"][$i]["lavori"];
							            ?>
							          </td>
												<td>
													<?= (!empty($soa["dichiarazioni"][$i]["personale"])) ? number_format($soa["dichiarazioni"][$i]["personale"],2,",",".") : ""; ?>
													<? if (!empty($soa["dichiarazioni"][$i]["valuta_personale"])) {
													echo $valute[$soa["dichiarazioni"][$i]["valuta_personale"]];
													}
													$totale_personale += $soa["dichiarazioni"][$i]["personale"];
													?>
												</td>
							        </tr>
							        <? }
											} ?>
											<tr>
												<td>Totale</td>
												<td><?= number_format($totale_lavori,2,",",".") ?></td>
												<td><?= number_format($totale_personale,2,",",".") ?></td>
											</tr>
							      </table>
										<br>
										<?
										$rapporto = $totale_personale * 100 / $totale_lavori;
										?><br>
										<strong>Rapporto tra Costo del personale e importo dei lavori eseguiti: <?= number_format($rapporto,2,",",".") ?>%</strong>
										<?
									}
									?><br><br>
									e che tali requisiti
									<?
										if ($soa["requisiti"] === "1") {
										?>
										sono sufficienti per la partecipazione alla gara da parte di questa impresa;
										<?
										} else if ($soa["requisiti"] === "0") {
											?>
											non sono adeguati alla partecipazione alla gara da parte di questa impresa, per cui, ai sensi dell'articolo 89 del decreto legislativo n. 50 del 2016, il possesso del requisito del quale questa impresa &egrave; carente, &egrave; soddisfatto avvalendosi dei requisiti della/e impresa/e ausiliaria/e, come indicato nel DGUE; la/e predetta/e imprese ausiliare a loro volta presentano e allegano le pertinenti dichiarazioni.
											<?
										}
					$html = ob_get_clean();

					class MYPDF extends TCPDF {
			              public function Footer() {
			                $this->WriteHTML('<div style="border-top:1px solid #000; text-align:right"> Pagina '.$this->getAliasNumPage().'/'.$this->getAliasNbPages() . '</div>');
			              }
			            }
					$pdf = new MYPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
					@$pdf->setPrintHeader(false);
					@$pdf->setPrintFooter(true);
		      @$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		      @$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		      @$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
					@$pdf->SetFont('helvetica', '', 8);
					@$pdf->AddPage();
					@$pdf->WriteHTML(utf8_encode($html));
					@$pdf->lastPage();
					// @$pdf->Output("SOA.pdf","D");
					@$pdf->Output("SOA.pdf");

			} else {
				?>
				<h1>Documento non esistente</h1>
				<?
			}
	} else {
		?>
		<h1>Impossibile accedere</h1>
		<?
	}
	?>
