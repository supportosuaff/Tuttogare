<?
	die();
	/*
	if (!isset($pdo))
	{
		include_once("../../../config.php");
		include_once($root."/inc/funzioni.php");
		if (isset($_POST) && isset($_POST["codice"]) && isset($_POST["partecipanti"]) && isset($_POST["lotto"]) && isset($_POST["tecnica"]))
		{
			$codice_gara = $_POST["codice"];
			$n_partecipanti = $_POST["partecipanti"];
			$codice_lotto = $_POST["lotto"];
			$economica = true;
			if ($_POST["tecnica"] == "true") $economica = false;
		}
		else
		{
			die();
		}
	}

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("gare/elaborazione",$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	} else {
		die();
	}

	if (!$edit) {
		die();
	} else {
		if (!isset($qualitativi)) {
			$sql_criteri = "SELECT b_valutazione_tecnica.*,
											 b_criteri_punteggi.economica,
											 b_criteri_punteggi.temporale,
											 b_criteri_punteggi.migliorativa
								FROM b_valutazione_tecnica
								JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
								WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND b_valutazione_tecnica.tipo = 'Q' AND
											(b_valutazione_tecnica.codice_lotto = :codice_lotto OR b_valutazione_tecnica.codice_lotto = 0)
								AND b_valutazione_tecnica.codice NOT IN
								(SELECT codice_padre FROM b_valutazione_tecnica WHERE codice_padre <> 0 AND codice_gara = :codice_gara)";
			if ($economica) {
				$sql_criteri .= "AND (b_criteri_punteggi.economica = 'S' OR b_criteri_punteggi.temporale = 'S') ";
			} else {
				$sql_criteri .= "AND (b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N') ";
			}
			$bind = array();
			$bind[":codice_gara"] = $codice_gara;
			$bind[":codice_lotto"] = $codice_lotto;
			$ris_criteri = $pdo->bindAndExec($sql_criteri,$bind);
			$qualitativi = $ris_criteri->rowCount();
			$coppie = false;
			$bind = array();
			$bind[":codice_gara"] = $codice_gara;
			$sql_confronto = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 124";
			$ris_confronto = $pdo->bindAndExec($sql_confronto,$bind);
			if ($ris_confronto->rowCount()>0) $coppie = true;
		}
		if ($qualitativi > 0) {
			$n_confronti = $qualitativi * $n_partecipanti;
			if ($coppie) {
				$n_confronti = ((($n_partecipanti * ($n_partecipanti - 1))/2) * $qualitativi);
			}
			$bind = array();
			$bind[":codice_gara"] = $codice_gara;
			$sql_commissione = "SELECT * FROM b_commissioni WHERE b_commissioni.codice_gara = :codice_gara AND b_commissioni.valutatore = 'S'";
			$ris_commissione = $pdo->bindAndExec($sql_commissione,$bind);
			if($ris_commissione->rowCount() > 0)
			{
				?>
				<table class="table" width="100%">
					<thead>
						<tr>
							<th>Commissario</th>
							<th width="65%"></th>
							<th colspan="2">Avanzamento</th>
						</tr>
					</thead>
					<tbody>
						<?
						$show_riepilogo = true;
						while ($rec_commissione = $ris_commissione->fetch(PDO::FETCH_ASSOC))
						{
							$bind = array();
							$bind[":codice_gara"] = $codice_gara;
							$bind[":codice_lotto"] = $codice_lotto;
							$bind[":codice_commissario"] = $rec_commissione["codice"];
							if ($coppie) {
								$sql_valutazioni = "SELECT * FROM b_confronto_coppie WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_commissario = :codice_commissario";
								$ris_valutazioni = $pdo->bindAndExec($sql_valutazioni,$bind);
								$n_valutazioni = $ris_valutazioni->rowCount();
								while ($rec_valutazioni = $ris_valutazioni->fetch(PDO::FETCH_ASSOC))
								{
									if ($rec_valutazioni["punteggio_partecipante_1"] == 0 && $rec_valutazioni["punteggio_partecipante_2"] == 0) $n_valutazioni -= 1;
								}
							} else {
								$sql_valutazioni = "SELECT * FROM b_coefficienti_commissari WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_commissario = :codice_commissario";
								$ris_valutazioni = $pdo->bindAndExec($sql_valutazioni,$bind);
								$n_valutazioni = $ris_valutazioni->rowCount();
							}
							$avanzamento = 0;
							if ($n_confronti > 0) $avanzamento =  number_format(($n_valutazioni / $n_confronti)*100,2);
							$show_export = false;
							if ($avanzamento == 100.00) $show_export = true;

							$avanzamento = ($avanzamento > 100) ? 100.00 : $avanzamento;
							$class = ($avanzamento > 100.00 ? "complete_bar" : "progress_bar");
							$style = 'style="width:'.number_format($avanzamento,0).'%"';

							if ($avanzamento == 100.00)
							{
								$class = "complete_bar";
								$style = '';
							}
							if (!$show_export)	$show_riepilogo = false;
							?>
							<tr>
								<td><?= $rec_commissione["titolo"] . " " . $rec_commissione["cognome"] . " " . $rec_commissione["nome"] ?></td>
								<td><div id="progress_bar" class="big_progress_bar"><div class="<?= $class ?>" <?= $style ?>></div></div></td>
								<td><?= $avanzamento . " %" ?></td>
								<?
								if ($show_export)
								{
									?>
									<td>
										<form action="pdf_commissari.php" method="POST" role="form" target="_blank">
											<input type="hidden" name="codice_gara" id="input_gara" class="espandi form-control" value="<?= $codice_gara ?>">
											<input type="hidden" name="codice_lotto" id="inputLotto" class="espandi form-control" value="<?= $codice_lotto ?>">
											<input type="hidden" name="riparametraMedie" class="riparametraMedie" value="S">
											<input type="hidden" name="codice_commissario" id="inputCodice_commissario" class="espandi form-control" value="<?= $rec_commissione["codice"] ?>">
											<button type="submit" class="submit espandi"><span class="fa fa-file"></span> PDF</button>
										</form>
									</td>
									<?
								}
								else
								{
									echo "<td></td>";
								}
								?>
							</tr>
							<?
						}

						if ($show_riepilogo)
						{
							$continua = true;
							?>
							<tr>
								<td colspan="3" style="text-align:right"><b>Riepilogo Calcolo Punteggi&nbsp;&nbsp;&nbsp;</b></td>
								<td>
									<form action="pdf_commissari.php" method="POST" role="form" target="_blank">
										<input type="hidden" name="codice_gara" id="input_gara" class="espandi form-control" value="<?= $codice_gara ?>">
										<input type="hidden" name="codice_lotto" id="inputLotto" class="espandi form-control" value="<?= $codice_lotto ?>">
										<input type="hidden" name="riparametraMedie" class="riparametraMedie" value="S">
										<button type="submit" class="submit espandi"><span class="fa fa-file"></span> PDF</button>
									</form>
								</td>
							</tr>
							<?
						}
						?>
					</tbody>
				</table>
				<?
			}
			else
			{
				?>
				<p style="text-align:center; font-size:24px;">Non è possibile determinare l'avanzamento <br>Verificare Commissione</p>
				<?
			}
		}
		else
		{
			?>
			<h2>Non è possibile determinare l'avanzamento</h2>
			<?
		}
	} 
	*/
?>
