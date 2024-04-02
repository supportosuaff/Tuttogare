<?
	if (!isset($pdo))
	{
		session_start();
		include_once("../../../config.php");
		include_once($root."/inc/funzioni.php");
		if (isset($_POST) && isset($_POST["codice"]) && isset($_POST["partecipanti"]) && isset($_POST["lotto"]))
		{
			$codice_gara = $_POST["codice"];
			$n_partecipanti = $_POST["partecipanti"];
			$codice_lotto = $_POST["lotto"];
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
		$bind = array();
		$bind[":codice_gara"] = $codice_gara;
		//Seleziono i criteri di tipo Qualitativo
		$sql_criteri  = "SELECT b_valutazione_tecnica.codice ";
		$sql_criteri .= "FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice ";
		$sql_criteri .= "WHERE ( ";
			$sql_criteri .= "b_valutazione_tecnica.tipo = 'Q' ";
			$sql_criteri .= "AND b_valutazione_tecnica.codice_padre IN ( ";
				$sql_criteri .= "SELECT b_valutazione_tecnica.codice ";
				$sql_criteri .= "FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento ";
				$sql_criteri .= "WHERE b_valutazione_tecnica.codice_padre = 0  ";
				$sql_criteri .= "AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' ";
				$sql_criteri .= "AND b_valutazione_tecnica.codice_gara = :codice_gara ";
				$sql_criteri .= "AND b_valutazione_tecnica.tipo = 'Q' ";
			$sql_criteri .= ") ";
		$sql_criteri .= ") OR ( ";
			$sql_criteri .= "b_valutazione_tecnica.codice_padre = 0 ";
			$sql_criteri .= "AND b_valutazione_tecnica.codice_gara = :codice_gara ";
			$sql_criteri .= "AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' ";
			$sql_criteri .= "AND b_valutazione_tecnica.tipo = 'Q' ";
			$sql_criteri .= "AND b_valutazione_tecnica.codice NOT IN ( ";
				$sql_criteri .= "SELECT b_valutazione_tecnica.codice_padre ";
				$sql_criteri .= "FROM b_valutazione_tecnica ";
				$sql_criteri .= "WHERE b_valutazione_tecnica.codice_gara = :codice_gara ";
				$sql_criteri .= "AND b_valutazione_tecnica.tipo = 'Q' ";
				$sql_criteri .= "GROUP BY b_valutazione_tecnica.codice_padre ";
			$sql_criteri .= ") ";
		$sql_criteri .= ") ";
		$ris_criteri = $pdo->bindAndExec($sql_criteri,$bind);

		if ($ris_criteri->rowCount() > 0)
		{
			$sql_commissione = "SELECT * FROM b_commissioni WHERE b_commissioni.codice_gara = :codice_gara AND b_commissioni.valutatore = 'S'";
			$ris_commissione = $pdo->bindAndExec($sql_commissione,$bind);

			$n_criteri = $ris_criteri->rowCount();
			$n_confronti = ((($n_partecipanti * ($n_partecipanti - 1))/2) * $n_criteri);

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
							$sql_valutazioni = "SELECT * FROM b_confronto_coppie WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_commissario = :codice_commissario";
							$ris_valutazioni = $pdo->bindAndExec($sql_valutazioni,$bind);
							$n_valutazioni = $ris_valutazioni->rowCount();
							while ($rec_valutazioni = $ris_valutazioni->fetch(PDO::FETCH_ASSOC))
							{
								if ($rec_valutazioni["punteggio_partecipante_1"] == 0 && $rec_valutazioni["punteggio_partecipante_2"] == 0) $n_valutazioni -= 1;
							}

							$avanzamento =  number_format(($n_valutazioni / $n_confronti)*100,2);
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
										<form action="/gare/valutazione_tecnica/save_pdf_commissario.php" method="POST" role="form" target="_blank">
											<input type="hidden" name="codice" id="input_gara" class="form-control" value="<?= $codice_gara ?>">
											<input type="hidden" name="lotto" id="inputLotto" class="form-control" value="<?= $codice_lotto ?>">
											<input type="hidden" name="partecipanti" id="inputPartecipanti" class="form-control" value="<?= $n_partecipanti ?>">
											<input type="hidden" name="codice_commissario" id="inputCodice_commissario" class="form-control" value="<?= $rec_commissione["codice"] ?>">
											<button type="submit" class="submit"><img src="/img/pdf.png" style="vertical-align:middle" alt="Esporta in PDF"> PDF</button>
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
							?>
							<tr>
								<td colspan="3" style="text-align:right"><b>Riepilogo Calcolo Punteggi&nbsp;&nbsp;&nbsp;</b></td>
								<td>
									<form action="/gare/valutazione_tecnica/save_pdf_riepilogo.php" method="POST" role="form" target="_blank">
										<input type="hidden" name="codice" id="input_gara" class="form-control" value="<?= $codice_gara ?>">
										<input type="hidden" name="lotto" id="inputLotto" class="form-control" value="<?= $codice_lotto ?>">
										<input type="hidden" name="partecipanti" id="inputPartecipanti" class="form-control" value="<?= $n_partecipanti ?>">
										<button type="submit" class="submit"><img src="/img/pdf.png" style="vertical-align:middle" alt="Esporta in PDF"> PDF</button>
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
				<p style="text-align:center; font-size:24px;">Non è possibile determinare l'avanzamento dei confronti a coppie<br>Verificare Commissione</p>
				<?
			}
		}
		else
		{
			?>
			<h2>Non è possibile determinare l'avanzamento dei confronti a coppie</h2>
			<?
		}
	}
?>
