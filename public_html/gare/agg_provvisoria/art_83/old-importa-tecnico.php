<?
  if (isset($_SESSION["gerarchia"]) && $_SESSION["gerarchia"] <= 2 && isset($numero_partecipanti)) {
		$n_commissari = 0;
		$codice_gara = $_POST["codice_gara"];
		$codice_lotto = $_POST["codice_lotto"];

		$bind = array();
		$bind[":codice_gara"] = $codice_gara;
		$sql_codice_tecnico = "SELECT b_criteri_punteggi.codice FROM b_criteri_punteggi JOIN b_gare ON b_criteri_punteggi.codice_criterio = b_gare.criterio
													 WHERE b_gare.codice = :codice_gara AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' AND b_criteri_punteggi.eliminato = 'N'";
		$ris_codice_tecnico = $pdo->bindAndExec($sql_codice_tecnico,$bind);
		$codice_tecnico = $ris_codice_tecnico->fetch(PDO::FETCH_ASSOC)["codice"];

		$bind = array();
		$bind[":codice_gara"] = $codice_gara;

		$sql_commissione = "SELECT * FROM b_commissioni WHERE b_commissioni.codice_gara = :codice_gara AND b_commissioni.valutatore = 'S'";
		$ris_commissione = $pdo->bindAndExec($sql_commissione,$bind);
		if ($ris_commissione->rowCount() > 0)
		{
			$n_commissari = $ris_commissione->rowCount();
		}

		$bind = array();
		$bind[":codice_gara"] = $codice_gara;
		$bind[":codice_lotto"] = $codice_lotto;
		$n_partecipanti = 0;
		$sql_partecipanti = "SELECT * FROM r_partecipanti
												WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0  AND ammesso = 'S' AND escluso = 'N'
												AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) ORDER BY codice";
		$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,$bind);
		if ($ris_partecipanti->rowCount() > 0) $n_partecipanti = $ris_partecipanti->rowCount();

		$n_criteri = 0;

		$bind = array();
		$bind[":codice_gara"] = $codice_gara;

		$sql_criteri  = "SELECT b_valutazione_tecnica.codice, b_valutazione_tecnica.punteggio ";
		$sql_criteri .= "FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice ";
		$sql_criteri .= "WHERE ( ";
			$sql_criteri .= "b_valutazione_tecnica.codice_gara = :codice_gara ";
			$sql_criteri .= "AND b_valutazione_tecnica.tipo = 'Q' ";
			$sql_criteri .= "AND b_valutazione_tecnica.codice_padre IN ( ";
				$sql_criteri .= "SELECT b_valutazione_tecnica.codice ";
				$sql_criteri .= "FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice ";
				$sql_criteri .= "WHERE b_valutazione_tecnica.codice_padre = 0  ";
				$sql_criteri .= " AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' ";
				$sql_criteri .= "AND b_valutazione_tecnica.codice_gara = :codice_gara ";
				$sql_criteri .= "AND b_valutazione_tecnica.tipo = 'Q' ";
			$sql_criteri .= ") ";
		$sql_criteri .= ") OR ( ";
			$sql_criteri .= "b_valutazione_tecnica.codice_padre = 0 ";
			$sql_criteri .= "AND b_valutazione_tecnica.codice_gara = :codice_gara ";
			$sql_criteri .= " AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' ";
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
		if ($ris_criteri->rowCount() > 0) $n_criteri = $ris_criteri->rowCount();
		$sql_opzione = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 124";
		$ris_opzione = $pdo->bindAndExec($sql_opzione,$bind);


		$bind = array();
		$bind[":codice"]=$codice_gara;
		$sql_qualitativi = "SELECT * FROM b_valutazione_tecnica WHERE codice_gara = :codice AND tipo = 'Q'";
		$ris_qualitativi = $pdo->bindAndExec($sql_qualitativi,$bind);

		$bind = array();
		$bind[":codice_gara"] = $codice_gara;
		$bind[":codice_lotto"] = $codice_lotto;

		if ($ris_opzione->rowCount() == 0 || $ris_qualitativi->rowCount() == 0) {
			if (empty($_POST["riparametrazione_semplice"]) || (!empty($_POST["riparametrazione_semplice"]) && $_POST["riparametrazione_semplice"] == "N")) {
				$sql_punteggi = "SELECT b_punteggi_criteri.codice_partecipante, SUM(b_punteggi_criteri.punteggio) AS punteggio
												 FROM b_punteggi_criteri JOIN b_valutazione_tecnica ON b_punteggi_criteri.codice_criterio = b_valutazione_tecnica.codice
												 JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
												 WHERE b_punteggi_criteri.codice_gara = :codice_gara
												 AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N'
												 AND b_punteggi_criteri.codice_lotto = :codice_lotto
												 GROUP BY codice_partecipante ORDER BY codice_partecipante";
				$ris_punteggi = $pdo->bindAndExec($sql_punteggi,$bind);
				if ($ris_punteggi->rowCount() > 0) {
					while($rec_punteggio = $ris_punteggi->fetch(PDO::FETCH_ASSOC)) {
						?>
						$('#punteggio_<? echo $rec_punteggio["codice_partecipante"] ?>_<?= $codice_tecnico ?>').val('<? echo number_format($rec_punteggio["punteggio"],3); ?>');
						<?
					}
				}
			} else {

				$bind = array();
				$bind[":codice_gara"] = $codice_gara;
				$bind[":codice_lotto"] = $codice_lotto;
				$strsql = "SELECT b_valutazione_tecnica.punteggio AS punteggio_max, b_punteggi_criteri.* FROM b_valutazione_tecnica
								JOIN b_punteggi_criteri ON b_valutazione_tecnica.codice = b_punteggi_criteri.codice_criterio
								JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
								WHERE b_punteggi_criteri.codice_gara = :codice_gara AND b_punteggi_criteri.codice_lotto = :codice_lotto
								AND b_criteri_punteggi.economica = 'N' AND  b_criteri_punteggi.temporale = 'N'";
				$ris = $pdo->bindAndExec($strsql,$bind);
				if ($ris->rowCount()>0) {
					$punteggi_max = array();
					$singoli_punteggi = array();
					$somma_punteggio = array();
					while($punteggio = $ris->fetch(PDO::FETCH_ASSOC)) {
						if (empty($punteggi_max[$punteggio["codice_criterio"]])) $punteggi_max[$punteggio["codice_criterio"]] = $punteggio["punteggio_max"];
						if (empty($singoli_punteggi[$punteggio["codice_criterio"]][$punteggio["codice_partecipante"]])) {
							$singoli_punteggi[$punteggio["codice_criterio"]][$punteggio["codice_partecipante"]] = $punteggio["punteggio"];
						}
					}
					foreach ($punteggi_max as $codice_criterio => $punteggio_max) {
						$max = max($singoli_punteggi[$codice_criterio]);
						foreach($singoli_punteggi[$codice_criterio] AS $codice_partecipante => $punteggio) {
							$punteggio_ottenuto = 0;
							if ($punteggio_max>0 && $max > 0) $punteggio_ottenuto = $punteggio * $punteggio_max / $max;
							if (empty($somma_punteggio[$codice_partecipante])) {
								$somma_punteggio[$codice_partecipante] = $punteggio_ottenuto;
							} else {
								$somma_punteggio[$codice_partecipante] += $punteggio_ottenuto;
							}
						}
					}
					if (count($somma_punteggio) > 0) {
						foreach ($somma_punteggio as $codice_partecipante => $punteggio) {
							?>
							$('#punteggio_<? echo $codice_partecipante ?>_<?= $codice_tecnico ?>').val('<? echo number_format($punteggio,3); ?>');
							<?
						}
					}
				}
			}
		} else {
			$n_confronti = fattoriale($n_partecipanti) / ( fattoriale($n_partecipanti - 2) * fattoriale(2) );
			$sql_confrontoacoppie = "SELECT * FROM b_confronto_coppie WHERE b_confronto_coppie.codice_gara = :codice_gara AND b_confronto_coppie.codice_lotto = :codice_lotto ";
			$ris_confrontoacoppie = $pdo->bindAndExec($sql_confrontoacoppie,$bind);

			if ($ris_confrontoacoppie->rowCount() < 1 || (string) $ris_confrontoacoppie->rowCount() != (string) ($n_confronti * $n_criteri * $n_commissari))
			{
				// var_dump($ris_confrontoacoppie->rowCount());
				// var_dump($n_confronti * $n_criteri * $n_commissari);
				// echo $ris_confrontoacoppie->rowCount(). ":". $n_confronti * $n_criteri * $n_commissari;
				?>
				jalert("Codice 0x01. Attenzione, prima di procedere la commissione valutatrice deve terminare i lavori!");
				<?
				die();
			}
			else
			{
				while ($rec_confrontoacoppie = $ris_confrontoacoppie->fetch(PDO::FETCH_ASSOC))
				{
					if ($rec_confrontoacoppie["punteggio_partecipante_1"] == 0 && $rec_confrontoacoppie["punteggio_partecipante_2"] == 0)
					{
						?>
						jalert("Codice 0x02. Attenzione, prima di procedere la commissione valutatrice deve terminare i lavori");
						<?
						die();
					}
				}

				$i = 0;
				$commissari = array();
				while ($rec_commissari = $ris_commissione->fetch(PDO::FETCH_ASSOC))
				{
					$commissari[$i] = [$rec_commissari["codice"], $rec_commissari["titolo"] . " " . $rec_commissari["cognome"] . " " . $rec_commissari["nome"]];
					$i++;
				}

				$ch = "A";
				$i = 0;
				$partecipanti = array();
				while ($rec_partecipanti = $ris_partecipanti->fetch(PDO::FETCH_ASSOC))
				{
					$partecipanti[$i] = [$rec_partecipanti["codice"], $ch,  $rec_partecipanti["ragione_sociale"], $rec_partecipanti["partita_iva"]];
					$ch++;
					$i++;
				}

				$bind = array();
				$bind[":codice_gara"] = $codice_gara;

				$sql_macro_criteri = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice ";
				$sql_macro_criteri .= "WHERE b_valutazione_tecnica.codice_gara = :codice_gara ";
				$sql_macro_criteri .= "AND b_valutazione_tecnica.tipo='Q' ";
				$sql_macro_criteri .= "AND b_valutazione_tecnica.codice_padre = 0 ";
				$sql_macro_criteri .= " AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' ";
				$sql_macro_criteri .= "ORDER BY b_valutazione_tecnica.codice ";

				$ris_macro_criteri = $pdo->bindAndExec($sql_macro_criteri,$bind);
				if ($ris_macro_criteri->rowCount() > 0)
				{
					$k = 1;
					$criteri = array();
					$punteggi = array();
					while ($rec_macro_criteri = $ris_macro_criteri->fetch(PDO::FETCH_ASSOC))
					{
						$cod_padre = $rec_macro_criteri["codice"];
						$criteri[$k]["codice"] = $cod_padre;
						$criteri[$k]["descrizione"] = $rec_macro_criteri["descrizione"];
						$criteri[$k]["punteggio"] = $rec_macro_criteri["punteggio"];

						$bind = array();
						$bind[":codice_gara"] = $codice_gara;
						$bind[":codice_padre"] = $cod_padre;

						$sql_sub_criterio = "SELECT * FROM b_valutazione_tecnica ";
						$sql_sub_criterio .= "WHERE codice_gara = :codice_gara ";
						$sql_sub_criterio .= "AND codice_padre = :codice_padre ";
						$sql_sub_criterio .= "AND tipo='Q' ";
						$sql_sub_criterio .= "ORDER BY codice ";

						$ris_sub_criterio = $pdo->bindAndExec($sql_sub_criterio,$bind);

						if ($ris_sub_criterio->rowCount() > 0)
						{
							$y = 1;
							while ($rec_sub_criterio = $ris_sub_criterio->fetch(PDO::FETCH_ASSOC))
							{
								$codice_figlio = $rec_sub_criterio["codice"];

								foreach ($commissari as $j => $commissario)
								{
									foreach ($partecipanti as $i => $partecipante)
									{
										$punteggi[$cod_padre][$codice_figlio][$commissario[0]][$partecipante[0]] = 0;

										$bind = array();
										$bind[":codice_gara"] = $codice_gara;
										$bind[":codice_lotto"] = $codice_lotto;
										$bind[":codice_figlio"] = $codice_figlio;
										$bind[":codice_commissario"] = $commissario[0];
										$bind[":codice_partecipante"] = $partecipante[0];

										$sql = "SELECT SUM(b_confronto_coppie.punteggio_partecipante_1) AS parziale ";
										$sql .= "FROM b_confronto_coppie ";
										$sql .= "WHERE codice_partecipante_1 = :codice_partecipante ";
										$sql .= "AND codice_commissario = :codice_commissario ";
										$sql .= "AND codice_gara = :codice_gara " . " ";
										$sql .= "AND codice_lotto = :codice_lotto " . " ";
										$sql .= "AND codice_criterio = :codice_figlio ";

										$ris = $pdo->bindAndExec($sql,$bind);

										if ($ris->rowCount() > 0)
										{
											$rec = $ris->fetch(PDO::FETCH_ASSOC);
											$punteggi[$cod_padre][$codice_figlio][$commissario[0]][$partecipante[0]] += intval($rec["parziale"]);
										}

										$bind = array();
										$bind[":codice_gara"] = $codice_gara;
										$bind[":codice_lotto"] = $codice_lotto;
										$bind[":codice_figlio"] = $codice_figlio;
										$bind[":codice_commissario"] = $commissario[0];
										$bind[":codice_partecipante"] = $partecipante[0];

										$sql = "SELECT SUM(b_confronto_coppie.punteggio_partecipante_2) AS parziale ";
										$sql .= "FROM b_confronto_coppie ";
										$sql .= "WHERE codice_partecipante_2 = :codice_partecipante ";
										$sql .= "AND codice_commissario = :codice_commissario ";
										$sql .= "AND codice_gara = :codice_gara " . " ";
										$sql .= "AND codice_lotto = :codice_lotto " . " ";
										$sql .= "AND codice_criterio = :codice_figlio ";
										$ris = $pdo->bindAndExec($sql,$bind);
										if ($ris->rowCount() > 0)
										{
											$rec = $ris->fetch(PDO::FETCH_ASSOC);
											$punteggi[$cod_padre][$codice_figlio][$commissario[0]][$partecipante[0]] += intval($rec["parziale"]);
										}

									}
								}
								$criteri[$k]["subcriteri"][$y]["codice"] = $codice_figlio;
								$criteri[$k]["subcriteri"][$y]["descrizione"] = $rec_sub_criterio["descrizione"];
								$criteri[$k]["subcriteri"][$y]["punteggio"] = $rec_sub_criterio["punteggio"];

								$y++;
							}
						}
						else
						{
							foreach ($commissari as $j => $commissario)
							{
								foreach ($partecipanti as $i => $partecipante)
								{
									$punteggi[$cod_padre][$commissario[0]][$partecipante[0]] = 0;

									$bind = array();
									$bind[":codice_gara"] = $codice_gara;
									$bind[":codice_lotto"] = $codice_lotto;
									$bind[":codice_padre"] = $cod_padre;
									$bind[":codice_commissario"] = $commissario[0];
									$bind[":codice_partecipante"] = $partecipante[0];

									$sql = "SELECT SUM(b_confronto_coppie.punteggio_partecipante_1) AS parziale ";
									$sql .= "FROM b_confronto_coppie ";
									$sql .= "WHERE codice_partecipante_1 = :codice_partecipante ";
									$sql .= "AND codice_commissario = :codice_commissario ";
									$sql .= "AND codice_gara = :codice_gara " . " ";
									$sql .= "AND codice_lotto = :codice_lotto " . " ";
									$sql .= "AND codice_criterio = :codice_padre ";
									$ris = $pdo->bindAndExec($sql,$bind);
									if ($ris->rowCount() > 0)
									{
										$rec = $ris->fetch(PDO::FETCH_ASSOC);
										$punteggi[$cod_padre][$commissario[0]][$partecipante[0]] += intval($rec["parziale"]);
									}

									$bind = array();
									$bind[":codice_gara"] = $codice_gara;
									$bind[":codice_lotto"] = $codice_lotto;
									$bind[":codice_padre"] = $cod_padre;
									$bind[":codice_commissario"] = $commissario[0];
									$bind[":codice_partecipante"] = $partecipante[0];

									$sql = "SELECT SUM(b_confronto_coppie.punteggio_partecipante_2) AS parziale ";
									$sql .= "FROM b_confronto_coppie ";
									$sql .= "WHERE codice_partecipante_2 = :codice_partecipante ";
									$sql .= "AND codice_commissario = :codice_commissario ";
									$sql .= "AND codice_gara = :codice_gara " . " ";
									$sql .= "AND codice_lotto = :codice_lotto " . " ";
									$sql .= "AND codice_criterio = :codice_padre ";
									$ris = $pdo->bindAndExec($sql,$bind);
									if ($ris->rowCount() > 0)
									{
										$rec = $ris->fetch(PDO::FETCH_ASSOC);
										$punteggi[$cod_padre][$commissario[0]][$partecipante[0]] += intval($rec["parziale"]);
									}
								}
							}
						}
						$k++;
					}
				}

				$tot_offerta = 0;
				$tot_macro = array();

				foreach ($criteri as $criterio)
				{
					$tot_offerta += $criterio["punteggio"];
					$codice_padre = $criterio["codice"];
					$tot_cri = array();
					if (isset($criterio["subcriteri"]))
					{
						$tmp_tot_macro = array();
						foreach ($criterio["subcriteri"] as $subcriterio)
						{
							$tot_sub = array();
							$codice_figlio = $subcriterio["codice"];
							foreach ($commissari as $commissario)
							{
								$codice_commissario = $commissario[0];
								$pnt_commissario = normalizza($punteggi[$codice_padre][$codice_figlio][$codice_commissario]);

								foreach ($partecipanti as $partecipante)
								{
									$codice_partecipante = $partecipante[0];
									if (!isset($tot_sub[$codice_partecipante])) $tot_sub[$codice_partecipante] = 0;
									$tot_sub[$codice_partecipante] += number_format($pnt_commissario[$codice_partecipante],3);
								}
							}

							$tot_sub = normalizza($tot_sub, $subcriterio["punteggio"]);

							foreach ($partecipanti as $partecipante)
							{
								$codice_partecipante = $partecipante[0];
								if (!isset($tmp_tot_macro[$codice_partecipante])) $tmp_tot_macro[$codice_partecipante] = 0;
								$tmp_tot_macro[$codice_partecipante] += number_format($tot_sub[$codice_partecipante],3);
							}
						}

						$tmp_tot_macro = normalizza($tmp_tot_macro,$criterio["punteggio"]);
						foreach ($tmp_tot_macro as $codice_partecipante => $punteggio)
						{
							if (!isset($tot_macro[$codice_partecipante])) $tot_macro[$codice_partecipante] = 0;
							$tot_macro[$codice_partecipante] += number_format($tmp_tot_macro[$codice_partecipante],3);

							$data = array();

							$operazione = "INSERT";
							$data["codice"] = 0;

							$sql = "SELECT * FROM b_punteggi_criteri WHERE codice_partecipante = :codice_partecipante AND codice_gara = :codice_gara AND codice_criterio = :codice_criterio AND codice_lotto = :codice_lotto";

							$bind = array(
								':codice_partecipante' => $codice_partecipante ,
								':codice_gara' => $codice_gara ,
								':codice_criterio' => $codice_padre ,
								':codice_lotto' => $codice_lotto
							);
							$sth = $pdo->bindAndExec($sql,$bind);
							if ($sth->rowCount() > 0)
							{
								$rec = $sth->fetch(PDO::FETCH_ASSOC);
								$data["codice"] = $rec["codice"];
								$operazione = "UPDATE";
							}

							$data["codice_gara"] = $codice_gara;
							$data["codice_lotto"] = $codice_lotto;
							$data["codice_criterio"] = $codice_padre;
							$data["codice_partecipante"] = $codice_partecipante;
							$data["punteggio"] = $tmp_tot_macro[$codice_partecipante];
							$data["utente_modifica"] = $_SESSION["codice_utente"];

							$salva = new salva;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_punteggi_criteri";
							$salva->operazione = $operazione;
							$salva->oggetto = $data;

							$codice = $salva->save();
						}
					}
					else
					{
						foreach ($commissari as $commissario)
						{
							$codice_commissario = $commissario[0];
							$pnt_commissario = normalizza($punteggi[$codice_padre][$codice_commissario]);

							foreach ($partecipanti as $partecipante)
							{
								$codice_partecipante = $partecipante[0];
								if (!isset($tot_cri[$codice_partecipante])) $tot_cri[$codice_partecipante] = 0;
								$tot_cri[$codice_partecipante] += number_format($pnt_commissario[$codice_partecipante],3);
							}
						}

						$tot_cri = normalizza($tot_cri, $criterio["punteggio"]);

						foreach ($partecipanti as $partecipante)
						{
							$codice_partecipante = $partecipante[0];
							if (!isset($tot_macro[$codice_partecipante])) $tot_macro[$codice_partecipante] = 0;
							$tot_macro[$codice_partecipante] += number_format($tot_cri[$codice_partecipante],3);

							$data = array();

							$operazione = "INSERT";
							$data["codice"] = 0;

							$sql = "SELECT * FROM b_punteggi_criteri WHERE codice_partecipante = :codice_partecipante AND codice_gara = :codice_gara AND codice_criterio = :codice_criterio AND codice_lotto = :codice_lotto";
							$bind = array(
								':codice_partecipante' => $codice_partecipante ,
								':codice_gara' => $codice_gara ,
								':codice_criterio' => $codice_padre ,
								':codice_lotto' => $codice_lotto
							);
							$sth = $pdo->bindAndExec($sql,$bind);
							if ($sth->rowCount() > 0)
							{
								$rec = $sth->fetch(PDO::FETCH_ASSOC);
								$data["codice"] = $rec["codice"];
								$operazione = "UPDATE";
							}

							$data["codice_gara"] = $codice_gara;
							$data["codice_lotto"] = $codice_lotto;
							$data["codice_criterio"] = $codice_padre;
							$data["codice_partecipante"] = $codice_partecipante;
							$data["punteggio"] = $tot_cri[$codice_partecipante];
							$data["utente_modifica"] = $_SESSION["codice_utente"];

							$salva = new salva;
							$salva->codop = $_SESSION["codice_utente"];
      				$salva->nome_tabella = "b_punteggi_criteri";
      				$salva->operazione = $operazione;
      				$salva->oggetto = $data;

      				$codice = $salva->save();
						}
					}
				}

				$bind = array();
				$bind[":codice_gara"] = $codice_gara;

				//Verifico la presenza di Criteri di tipo quantitativo e li sommo al punteggio totale
				$sql_quantitativi  = "SELECT b_valutazione_tecnica.codice, b_valutazione_tecnica.descrizione, b_valutazione_tecnica.punteggio ";
				$sql_quantitativi .= "FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice ";
				$sql_quantitativi .= "WHERE ( ";
					$sql_quantitativi .= "b_valutazione_tecnica.codice_padre IN ( ";
						$sql_quantitativi .= "SELECT b_valutazione_tecnica.codice ";
						$sql_quantitativi .= "FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice ";
						$sql_quantitativi .= "WHERE b_valutazione_tecnica.codice_padre = 0  ";
						$sql_quantitativi .= "AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N'  ";
						$sql_quantitativi .= "AND b_valutazione_tecnica.codice_gara = :codice_gara ";
						$sql_quantitativi .= "AND b_valutazione_tecnica.tipo = 'N' ";
					$sql_quantitativi .= ") ";
				$sql_quantitativi .= ") OR ( ";
					$sql_quantitativi .= "b_valutazione_tecnica.codice_padre = 0 ";
					$sql_quantitativi .= "AND b_valutazione_tecnica.codice_gara = :codice_gara ";
					$sql_quantitativi .= "AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' ";
					$sql_quantitativi .= "AND b_valutazione_tecnica.codice NOT IN ( ";
						$sql_quantitativi .= "SELECT b_valutazione_tecnica.codice_padre ";
						$sql_quantitativi .= "FROM b_valutazione_tecnica ";
						$sql_quantitativi .= "WHERE b_valutazione_tecnica.codice_gara = :codice_gara ";
						$sql_quantitativi .= "AND b_valutazione_tecnica.tipo = 'N' ";
						$sql_quantitativi .= "GROUP BY b_valutazione_tecnica.codice_padre ";
					$sql_quantitativi .= ") ";
				$sql_quantitativi .= ") AND b_valutazione_tecnica.tipo = 'N'";

				$ris_quantitativi = $pdo->bindAndExec($sql_quantitativi,$bind);
				$punteggi_quantitativi = array();
				if ($ris_quantitativi->rowCount() > 0)
				{
					$punteggi_max = array();
					while ($rec_quantitativi = $ris_quantitativi->fetch(PDO::FETCH_ASSOC))
					{
						$bind = array();
						$bind[":codice_gara"] = $codice_gara;
						$bind[":codice_lotto"] = $codice_lotto;
						$bind[":codice_criterio"] = $rec_quantitativi["codice"];

						$tot_offerta += $rec_quantitativi["punteggio"];
						if (empty($punteggi_max[$rec_quantitativi["codice"]])) {
							$punteggi_max[$rec_quantitativi["codice"]] = $rec_quantitativi["punteggio"];
						} else {
							if ($punteggi_max[$rec_quantitativi["codice"]] < $rec_quantitativi["punteggio"]) $punteggi_max[$rec_quantitativi["codice"]] = $rec_quantitativi["punteggio"];
						}
						$sql_punteggi_quantitativi = "SELECT * FROM b_punteggi_criteri ";
						$sql_punteggi_quantitativi .= "WHERE codice_lotto = :codice_lotto ";
						$sql_punteggi_quantitativi .= "AND codice_gara = :codice_gara ";
						$sql_punteggi_quantitativi .= "AND codice_criterio = :codice_criterio ";

						$ris_punteggi_quantitativi = $pdo->bindAndExec($sql_punteggi_quantitativi,$bind);
						if ($ris_punteggi_quantitativi->rowCount() > 0 && $ris_punteggi_quantitativi->rowCount() == $n_partecipanti)
						{
							while ($rec_punteggi_quantitativi = $ris_punteggi_quantitativi->fetch(PDO::FETCH_ASSOC))
							{
								if ($rec_punteggi_quantitativi["punteggio"] >= 0 && $rec_punteggi_quantitativi["punteggio"] <= $rec_quantitativi["punteggio"])
								{
									$punteggi_quantitativi[$rec_punteggi_quantitativi["codice_criterio"]][$rec_punteggi_quantitativi["codice_partecipante"]] = number_format($rec_punteggi_quantitativi["punteggio"],3);
								}
								else
								{
									echo "alert('Non è possibile calcolare i punteggi per l\'offerta tecnica.<br>Si prega di verifcare i punteggi per i criteri di tipo quantitativo!');";
									die();
								}
							}
						}
						else
						{
							echo "alert('Non è possibile calcolare i punteggi per l\'offerta tecnica.<br>Si prega di verifcare i punteggi per i criteri di tipo quantitativo!');";
							die();
						}
					}

					//Sommo i punteggi quantitativi
					foreach ($punteggi_quantitativi as $codice_criterio_quantitativo => $array_quantitativo)
					{
						$max = max($array_quantitativo);
						$punteggio_max = $punteggi_max[$codice_criterio_quantitativo];
						foreach ($array_quantitativo as $cod_partecipante_quantitativo => $pnt_quantitativo)
						{
							if (empty($_POST["riparametrazione_semplice"]) && (!empty($_POST["riparametrazione_semplice"]) && $_POST["riparametrazione_semplice"] == "N")) {
								$pnt_quantitativo = 0;
								if ($punteggio_max>0) $pnt_quantitativo = $pnt_quantitativo * $punteggio_max / $max;
							}
							$tot_macro[$cod_partecipante_quantitativo] += number_format($pnt_quantitativo,3);
						}
					}
				}
				/*
				//Riparametrazione Assoluta
				$sql_opzione = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = $codice_gara AND opzione = 128";
				$ris_opzione = $pdo->bindAndExec($sql_opzione,$bind);
				if ($ris_opzione->rowCount() > 0)
				{
					if ($tot_offerta != 0)
					{
						$tot_macro = normalizza($tot_macro, $tot_offerta);
					}
				}
				*/
				foreach ($tot_macro as $codice_partecipante => $punteggio) {
				?>
					$('#punteggio_<? echo $codice_partecipante ?>_<?= $codice_tecnico ?>').val('<?= number_format($punteggio,3) ?>');
				<?
				}
			}
		}
  }
?>
