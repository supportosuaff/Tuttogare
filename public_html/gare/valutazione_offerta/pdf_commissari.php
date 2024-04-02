<?
  use Dompdf\Dompdf;
  use Dompdf\Options;
  include_once("../../../config.php");
  $edit = false;
  $lock = true;
  if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
    $strsql = "SELECT * FROM b_gestione_gare WHERE link LIKE '/gare/valutazione_offerta/edit.php%'";
    $risultato = $pdo->query($strsql);
    if ($risultato->rowCount()>0) {
      $gestione = $risultato->fetch(PDO::FETCH_ASSOC);
      $esito = check_permessi_gara($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
      $edit = $esito["permesso"];
      $lock = $esito["lock"];
    }
    if (!$edit) {
      die();
    }
  } else {
    die();
  }
  if ($edit)
  {
		ini_set('memory_limit', '1536M');
		ini_set('max_execution_time', 600);
    if (isset($_POST["codice_lotto"])) {
			$ris_gara = $pdo->bindAndExec("SELECT * FROM b_gare WHERE codice = :codice",[":codice"=>$_POST["codice_gara"]]);
	    if ($ris_gara->rowCount() === 1) {
	      $gara = $ris_gara->fetch(PDO::FETCH_ASSOC);
				$codice_lotto = $_POST["codice_lotto"];
        if ($codice_lotto != 0) {
          $ris_lotto = $pdo->bindAndExec("SELECT * FROM b_lotti WHERE codice = :codice",[":codice"=>$codice_lotto]);
          if ($ris_lotto->rowCount() === 1) {
            $lotto = $ris_lotto->fetch(PDO::FETCH_ASSOC);
            $gara["oggetto"] .= "<br>" . $lotto["oggetto"];
          }
        }
			}
			$path = "diretto";
			$bind = array();
			$bind[":codice_gara"] = $gara["codice"];
			$sql_opzione = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 124";
			$ris_opzione = $pdo->bindAndExec($sql_opzione,$bind);
			if ($ris_opzione->rowCount() > 0) $path = "coppie";

			$sql_partecipanti = "SELECT r_partecipanti.* FROM r_partecipanti
													 WHERE
													 (
														(
															 r_partecipanti.ammesso = 'S' AND
															 r_partecipanti.escluso = 'N' AND 
															 (r_partecipanti.conferma IS NULL or r_partecipanti.conferma = 1) AND 
															 r_partecipanti.codice_gara = :codice_gara AND 
															 r_partecipanti.codice_lotto = :codice_lotto
														) OR 
														r_partecipanti.codice IN (SELECT codice_partecipante FROM b_coefficienti_commissari WHERE b_coefficienti_commissari.codice_gara = :codice_gara AND b_coefficienti_commissari.codice_lotto = :codice_lotto)
													 )
													 AND r_partecipanti.codice_capogruppo = 0;
													";
			$partecipanti = $pdo->bindAndExec($sql_partecipanti,[":codice_gara"=>$gara["codice"],":codice_lotto"=>$codice_lotto])->fetchAll(PDO::FETCH_ASSOC);

			$sql_criteri = "SELECT b_valutazione_tecnica.*, b_criteri_punteggi.nome
											FROM b_valutazione_tecnica
											JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
											WHERE b_valutazione_tecnica.codice_gara = :codice_gara
											AND (b_valutazione_tecnica.codice_lotto = :codice_lotto OR b_valutazione_tecnica.codice_lotto = 0)";
			$tmp_criteri = $pdo->bindAndExec($sql_criteri,[":codice_gara"=>$gara["codice"],":codice_lotto"=>$codice_lotto])->fetchAll(PDO::FETCH_ASSOC);
			$criteri = [];
			$echo_criteri = [];
			$criteri_valutazione = [];
			foreach ($tmp_criteri as $criterio) {
				$criteri[$criterio["codice"]] = $criterio;
				if (empty($criterio["codice_padre"])) {
					$echo_criteri[$criterio["codice"]] = [];
				} else {
					$echo_criteri[$criterio["codice_padre"]][$criterio["codice"]] = [];
				}
				if ($criterio["tipo"] == "Q") {
					$criteri_valutazione[] = $criterio["codice"];
          if (!empty($criterio["codice_padre"])) {
            foreach($criteri_valutazione AS $sk => $sc) {
              if ($sc == $criterio["codice_padre"]) {
                unset($criteri_valutazione[$sk]);
              }
            }
          }
				}
			}
			$bind = [];
			$bind[":codice_gara"] = $gara["codice"];
			$sql="SELECT * FROM b_commissioni WHERE codice_gara = :codice_gara AND valutatore = 'S' ";
			if (!empty($_POST["codice_commissario"])) {
				$bind[":codice_commissario"] = $_POST["codice_commissario"];
				$sql .= "AND b_commissioni.codice = :codice_commissario ";
			}
			$sql .= " ORDER BY cognome, nome";
			$commissari = $pdo->bindAndExec($sql,$bind)->fetchAll(PDO::FETCH_ASSOC);
			ob_start();
			?>
			<html>
				<head>
					<style>
            body { font-size: 10px }
						table { width:100%; }
						table td { padding:10px; border:1px solid #CCC }
						.etichetta { line-height:24px; background-color:#DDD }
					</style>
				</head>
				<body>
          <div style="text-align:center">
				    <img src="/documenti/enti/<?= $_SESSION["ente"]["logo"] ?>" height="80" alt="<?= $_SESSION["ente"]["denominazione"] ?>"><br>
		      </div>
					<h1 style="text-align:center"><?= $gara["oggetto"] ?></h1>
		      <div style="clear:both"></div>
					<h2>PARTECIPANTI</h2>
					<div class="box">
						<table width="100%">
							<thead>
								<tr>
									<td style="text-align:center" class="etichetta" width="10">#</td>
									<td class="etichetta" width="120">Partita IVA</td>
									<td class="etichetta">Ragione Sociale</td>
								</tr>
							</thead>
							<tbody>
								<?
									$i = "A";
									$tmp = [];
									foreach($partecipanti AS $partecipante) {
										$partecipante["id"] = $i;
										$tmp[$partecipante["codice"]] = $partecipante;
										?>
										<tr>
											<td style="text-align:center"><?= $i ?></td>
											<td style="text-align:center"><?= $partecipante["partita_iva"] ?></td>
											<td><?= (!empty($partecipante["tipo"])) ? "<strong>RAGGRUPPAMENTO</strong> - " : "" ?><?= $partecipante["ragione_sociale"] ?></td>
										</tr>
										<?
										$i++;
									}
									$partecipanti = $tmp;
								?>
							</tbody>
						</table>
					</div>
					<?
					if (count($commissari) == 1) {
						$commissario = $commissari[0];
						?>
						<h2><?= $commissario["cognome"] . " " . $commissario["nome"] ?></h2>
						<?
					} else {
						?>
						<h2>COMMISSIONE</h2>
						<table width="100%">
							<tr>
								<td width="10" style="text-align:center" class="etichetta">#</td>
								<td width="150" style="text-align:center" class="etichetta">Ruolo</td>
								<td class="etichetta">Nome</td>
							</tr>
							<?
							$cont = 1;
							foreach($commissari AS $commissario) {
								?>
								<tr>
									<td style="text-align:center"><?= $cont ?></td>
									<td style="text-align:center"><?= $commissario["ruolo"] ?></td>
									<td><?= $commissario["cognome"] . " " . $commissario["nome"] ?></td>
								</tr>
								<?
								$cont++;
							}
							?>
						</table>
						<?
					}
					foreach($criteri_valutazione AS $codice_criterio) {
						include($path."/report.php");
					}
					?>
				</body>
			</html>
			<?
			$html = ob_get_clean();
      $options = new Options();
  		$options->set('defaultFont', 'Helvetica');
  		$options->setIsRemoteEnabled(true);
  		$dompdf = new Dompdf($options);
  		$dompdf->loadHtml($html);
  		$dompdf->setPaper('A4', 'landscape');
  		$dompdf->set_option('defaultFont', 'Helvetica');
  		$dompdf->render();
  		$dompdf->stream("Valutazione.pdf", array("Attachment" => false));


    }
  }
?>
