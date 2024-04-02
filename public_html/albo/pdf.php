<?
	use Dompdf\Dompdf;
	use Dompdf\Options;
	include("../../config.php");
	include_once($root."/inc/funzioni.php");
	include_once($root."/inc/oeManager.class.php");

	$edit = false;
	$fullPDF = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]) && (empty($_GET["publicList"]))) {
		$edit = check_permessi("albo",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else if (!empty($_GET["publicList"]) && isset($_SESSION["ente"])) {
		$fullPDF = false;
		$_POST["filters"] = ["oeManager"=>["elenco"=>"albo-0"]];
		die();
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	if (isset($_POST["filters"])) {
		$oeManager = new oeManager();
		if (!isset($_GET["publicList"])) {
			parse_str($_POST["filters"],$filtri);
		} else {
			$filtri = $_POST["filters"];
			die();
		}
		foreach($filtri["oeManager"] AS $key => $value) {
			if (property_exists("oeManager",$key)) $oeManager->$key = $value;
			if ($key == "classifica_only_selected") $oeManager->$key = false;
			if ($key == "elenco" && !empty($value)) {
				$value = explode("-",$value);
				$oeManager->tipo_elenco = $value[0];
				$oeManager->codice_elenco = $value[1];
			}
		}
		$alloperatori = $oeManager->getList();
		if (count($alloperatori) > 0 && $alloperatori !== false) {
			$sql_albi = "SELECT r_partecipanti_albo.* FROM r_partecipanti_albo
										JOIN b_bandi_albo ON r_partecipanti_albo.codice_bando = b_bandi_albo.codice
										WHERE b_bandi_albo.codice_gestore = :codice_ente
										AND r_partecipanti_albo.codice_operatore = :codice_operatore
										AND r_partecipanti_albo.ammesso = 'S' ";
			$sth_albi = $pdo->prepare($sql_albi);
			ini_set('max_execution_time', 600);
			ini_set('memory_limit', '-1');
			ob_start();
		?>
			<html>
				<style>
					body { font-size:10px; }
					td, th { padding:5px; }
					tr.odd { background-color: #DDD; }
					table { width:100% }
				</style>
				<body>
					<table>
						<tr>
							<td style="width:150px"><img src="<?= $config["link_sito"] ?>/documenti/enti/<?= $_SESSION["ente"]["logo"] ?>" style="width:150px"></td>


							<td>
								<h1><?= $_SESSION["ente"]["denominazione"] ?></h1>
								<strong><?= $_SESSION["ente"]["indirizzo"] . " - " . $_SESSION["ente"]["citta"] . " (" . $_SESSION["ente"]["provincia"] . ")" ?></strong><br>
								<h2 style="margin:5px;padding:0px">Elenco OOEE al <?= date("d/m/Y H:i") ?></h2>
								<? //TODO: INSERIRE FILTRI ?>
							</td>
						</tr>
					</table>
					<table>
						<tr style="background-color: #AAA; color: #FFF; font-weight:bold">
							<th>Codice Fiscale</th>
							<th>Ragione Sociale</th>
							<? if ($fullPDF) { ?>
								<th>Referente</th>
								<th>PEC</th>
								<th style="width:20px">Albo</th>
							<? } ?>
						</tr>
						<?
							$i = 0;
							foreach($alloperatori AS $record) {
								$i++;
								$class= "even";
								if ($i%2!=0) $class = "odd";
								$nominativo		= $record["cognome"] . " " . $record["nome"];
								if ($record["codice_fiscale_impresa"] == "") $record["codice_fiscale_impresa"] = $record["cf"];
								?>
								<tr class="<?= $class ?>">
									<td><?= strtoupper($record["codice_fiscale_impresa"]) ?></td>
									<td><strong><?= strtoupper($record["ragione_sociale"]) ?></strong></td>
									<? if ($fullPDF) { ?>
										<td><?= strtoupper($nominativo) ?></td>
										<td><?= strtolower($record["pec"]) ?></td>
										<td>
											<?
												$bind_albi = array('codice_ente' => $_SESSION["ente"]["codice"], ':codice_operatore' => $record["codice_operatore"]);
												$sth_albi->execute($bind_albi);
												if ($sth_albi->rowCount() > 0) echo "Si";
											?>
										</td>
									<? } ?>
								</tr>
								<?
							}
	        ?>
					</table>
				</body>
			</html>
			<?
			$html = ob_get_clean();
			
			ini_set('max_execution_time', 600);
			ini_set('memory_limit', '-1');

			$options = new Options();
			$options->set('defaultFont', 'Helvetica');
			$options->setIsRemoteEnabled(true);
			$dompdf = new Dompdf($options);
			$dompdf->loadHtml($html);
			$dompdf->setPaper($formato, $orientamento);
			$dompdf->set_option('defaultFont', 'Helvetica');
			$dompdf->render();
			$dompdf->stream("Anteprima.pdf", array("Attachment" => false));
		} else {
			?><h1 style="text-align:center">
			<span class="fa fa-exclamation-circle fa-3x"></span><br>Nessun risultato!</h1>	<?
		}
	} else {
		?><h1 style="text-align:center">
		<span class="fa fa-exclamation-circle fa-3x"></span><br>Errore</h1>	<?
	}
	?>
