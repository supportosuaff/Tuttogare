
				<?
				$strsql  = "SELECT b_news.* ";
				$strsql .= "FROM b_news ";
				$strsql .= "WHERE  b_news.data <= curdate() ";
				$strsql.= " AND codice_ente = 0 AND servizio = false";
				$strsql .= " ORDER BY b_news.data DESC,  b_news.timestamp DESC LIMIT 0,3 " ;
				$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

				if ($risultato->rowCount()>0) {
					?>
					<div class="container-fluid">
						<div class="row">
							<div class="jumbotron">
								<div class="row">
					<?
					while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
						$codice			= $record["codice"];
						$titolo			= strtoupper($record["titolo"]);
						$data			= mysql2date($record["data"]);
						$testo			= strip_tags($record["testo"]);
						$href = "/news/id".$codice."-".sanitize_string($titolo);
								?>
													<div class="col-xs-12 col-sm-6 col-md-4">
														<div class="thumbnail" style="height:250px; overflow:hidden">
															<div class="caption">
																<h4 style="text-transform:uppercase"><a href="<?= $href ?>" title="<?= $record["titolo"] ?>"><?= $record["titolo"] ?></a></h4>
																<?= echo_calendario(mysql2date($record["data"])) ?><? echo substr($testo,0,255); ?>...
															</div>
														</div>
													</div>
			<?php
				}
				?></div>
				</div>
			</div>
		</div>
		<?
			}
				?>

				<div class="openpage_container">
					<div  class="openpage_text">
						Per <strong>Electronic Public Procurement</strong> s'intende l'approvvigionamento di beni e servizi attraverso l'utilizzo delle nuove tecnologie informatiche e telematiche. L'e-Procurement &egrave; un elemento portante di un sistema di e-government e rappresenta uno dei temi emergenti nel processo di modernizzazione della pubblica amministrazione.<br><br>
						Il valore del settore pubblico degli appalti &egrave; stimato in circa 1.429 miliardi di euro annui e rappresenta il 16 % del PIL dell’Unione Europea. Le procedure per l'aggiudicazione degli appalti pubblici sono operazioni complesse che richiedono un considerevole dispendio di tempo e di risorse. Un loro impiego inefficiente pu&ograve; causare, pertanto, forti perdite in termini di produttivit&agrave;. La <strong>normativa nazionale e comunitaria</strong> favorisce l'uso degli strumenti informatici e telematici nell’ambito delle tradizionali procedure di gara, prevedendo l’istituzione di sistemi di approvvigionamento interamente gestiti mediante strumenti elettronici.
					</div>
				</div>
				<div class="openpage_container" style="height: 400px; text-align: center; background-image:url('/img/clouding.png'); background-position: center center; background-size: cover; background-attachment: fixed; background-repeat: no-repeat">
				</div>
					<div class="openpage_container">
					<div  class="openpage_text">
						<h2 style="font-size: 24px"><strong>Tuttogare</strong> copre ogni aspetto del procedimento di gara, garantendo:</h2>
					    <ul>
					    	<li>efficientamento dei tempi e dei costi delle gare pubbliche, sia per i partecipanti che per la PA</li>
								<li>controlli puntuali su tempi e date del bando</li>
								<li>trasparenza assoluta dei passaggi di gara</li>
								<li>sicurezza dei dati con cripting dei documenti</li>
								<li>comunicazioni automatiche via Posta Elettronica Certificata (PEC)</li>
								<li>firma digitale, per sostituire la firma autografa</li>
								<li>marca temporale, sostitutivo elettronico della data certa</li>
								<li>eliminazione del cartaceo</li>
					    </ul>
						</div>
					</div>
					<div class="openpage_container" style="height: 400px; text-align: center; background-image:url('/img/launchImage.jpg'); background-position: center center; background-attachment: fixed; background-repeat: no-repeat">
				</div>
