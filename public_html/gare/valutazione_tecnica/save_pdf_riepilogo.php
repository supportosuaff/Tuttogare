<?
	use Dompdf\Dompdf;
	use Dompdf\Options;
	session_start();
	session_write_close();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
  	ini_set('memory_limit', '1536M');
  	ini_set('max_execution_time', 600);
	if (isset($_POST) && isset($_POST["codice"]) && isset($_POST["lotto"]))
	{
		$codice_gara = $_POST["codice"];
		$codice_lotto = $_POST["lotto"];
		$n_partecipanti = $_POST["partecipanti"];
	}
	else
	{
		header("Location: /index.php");
		die();
	}

	$edit = false;
	if (isset($_SESSION["codice_utente"]))
	{
		$edit = check_permessi("gare/elaborazione",$_SESSION["codice_utente"]);
		if (!$edit)
		{
			header("Location: /index.php");
			die();
		}
	}
	else
	{
		header("Location: /index.php");
		die();
	}

	if (!$edit)
	{
		header("Location: /index.php");
		die();
	}
	else
	{
		$bind = array();
		$bind[":codice_gara"] = $codice_gara;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$sql_gara = "SELECT * FROM `b_gare` ";
		$sql_gara .= "WHERE `b_gare`.`codice` = :codice_gara ";
		$sql_gara .= "AND ( ";
			$sql_gara .= "`b_gare`.`codice_ente` = :codice_ente ";
			$sql_gara .= "OR `b_gare`.`codice_gestore` = :codice_ente ";
		$sql_gara .= ") ";
		if ($_SESSION["gerarchia"] > 0)
		{
			$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
			$sql_gara .= "AND ( ";
				$sql_gara .= "`b_gare`.`codice_ente` = :codice_ente_utente ";
				$sql_gara .= "OR `b_gare`.`codice_gestore` = :codice_ente_utente ";
			$sql_gara .= ") ";
		}

		$ris_gara = $pdo->bindAndExec($sql_gara,$bind);
		if ($ris_gara->rowCount() < 1)
		{
			header("Location: /edit.php?codice=$codice_gara");
			die();
		}
		else
		{
			$record_gara = $ris_gara->fetch(PDO::FETCH_ASSOC);

			$bind = array();
			$bind[":codice_gara"] = $record_gara["codice"];

			$sql_commissari = "SELECT * FROM `b_commissioni` WHERE `b_commissioni`.`codice_gara` = :codice_gara AND `b_commissioni`.`valutatore` = 'S'";
			$ris_commissari = $pdo->bindAndExec($sql_commissari,$bind);

			if ($ris_commissari->rowCount() > 0)
			{
				$i = 0;
				$commissari = array();
				while ($rec_commissari = $ris_commissari->fetch(PDO::FETCH_ASSOC))
				{
					$commissari[$i] = [$rec_commissari["codice"], $rec_commissari["titolo"] . " " . $rec_commissari["cognome"] . " " . $rec_commissari["nome"]];
					$i++;
				}
			}

			$bind = array();
			$bind[":codice_gara"] = $record_gara["codice"];
			$bind[":codice_lotto"] = $codice_lotto;

			$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND ammesso = 'S' AND escluso = 'N' AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) ORDER BY codice";
			$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,$bind);

			if ($ris_partecipanti->rowCount() > 0)
			{
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
				$sql_macro_criteri .= "AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' ";
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

						$sql_sub_criterio = "SELECT * FROM `b_valutazione_tecnica` ";
						$sql_sub_criterio .= "WHERE `codice_gara` = :codice_gara ";
						$sql_sub_criterio .= "AND `codice_padre` = :codice_padre ";
						$sql_sub_criterio .= "AND `tipo`='Q' ";
						$sql_sub_criterio .= "ORDER BY `codice` ";

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
										$bind[":codice_criterio"] = $codice_figlio;
										$bind[":commissario"] = $commissario[0];
										$bind[":partecipante"] = $partecipante[0];

										$sql = "SELECT SUM(`b_confronto_coppie`.`punteggio_partecipante_1`) AS parziale ";
										$sql .= "FROM `b_confronto_coppie` ";
										$sql .= "WHERE `codice_partecipante_1` = :partecipante ";
										$sql .= "AND `codice_commissario` = :commissario ";
										$sql .= "AND `codice_gara` = :codice_gara ";
										$sql .= "AND `codice_lotto` = :codice_lotto ";
										$sql .= "AND `codice_criterio` = :codice_criterio ";

										$ris = $pdo->bindAndExec($sql,$bind);

										if ($ris->rowCount() > 0)
										{
											$rec = $ris->fetch(PDO::FETCH_ASSOC);
											$punteggi[$cod_padre][$codice_figlio][$commissario[0]][$partecipante[0]] += intval($rec["parziale"]);
										}

										$bind = array();
										$bind[":codice_gara"] = $codice_gara;
										$bind[":codice_lotto"] = $codice_lotto;
										$bind[":codice_criterio"] = $codice_figlio;
										$bind[":commissario"] = $commissario[0];
										$bind[":partecipante"] = $partecipante[0];

										$sql = "SELECT SUM(`b_confronto_coppie`.`punteggio_partecipante_2`) AS parziale ";
										$sql .= "FROM `b_confronto_coppie` ";
										$sql .= "WHERE `codice_partecipante_2` = :partecipante ";
										$sql .= "AND `codice_commissario` = :commissario ";
										$sql .= "AND `codice_gara` = :codice_gara ";
										$sql .= "AND `codice_lotto` = :codice_lotto ";
										$sql .= "AND `codice_criterio` = :codice_criterio ";

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
									$bind[":codice_criterio"] = $cod_padre;
									$bind[":commissario"] = $commissario[0];
									$bind[":partecipante"] = $partecipante[0];

									$sql = "SELECT SUM(`b_confronto_coppie`.`punteggio_partecipante_1`) AS parziale ";
									$sql .= "FROM `b_confronto_coppie` ";
									$sql .= "WHERE `codice_partecipante_1` = :partecipante ";
									$sql .= "AND `codice_commissario` = :commissario ";
									$sql .= "AND `codice_gara` = :codice_gara ";
									$sql .= "AND `codice_lotto` = :codice_lotto ";
									$sql .= "AND `codice_criterio` = :codice_criterio ";

									$ris = $pdo->bindAndExec($sql,$bind);
									if ($ris->rowCount() > 0)
									{
										$rec = $ris->fetch(PDO::FETCH_ASSOC);
										$punteggi[$cod_padre][$commissario[0]][$partecipante[0]] += intval($rec["parziale"]);
									}
									$bind = array();
									$bind[":codice_gara"] = $codice_gara;
									$bind[":codice_lotto"] = $codice_lotto;
									$bind[":codice_criterio"] = $cod_padre;
									$bind[":commissario"] = $commissario[0];
									$bind[":partecipante"] = $partecipante[0];

									$sql = "SELECT SUM(`b_confronto_coppie`.`punteggio_partecipante_2`) AS parziale ";
									$sql .= "FROM `b_confronto_coppie` ";
									$sql .= "WHERE `codice_partecipante_2` = :partecipante ";
									$sql .= "AND `codice_commissario` = :commissario ";
									$sql .= "AND `codice_gara` = :codice_gara ";
									$sql .= "AND `codice_lotto` = :codice_lotto ";
									$sql .= "AND `codice_criterio` = :codice_criterio ";

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

				$record_gara["nome_procedura"] = "";
				$bind=array();
				$bind[":codice"] = $record_gara["procedura"];
				$sql_procedura = "SELECT * FROM b_procedure WHERE codice=:codice";
				$ris_procedura = $pdo->bindAndExec($sql_procedura,$bind);
				if ($ris_procedura->rowCount()>0) {
					$rec_procedura = $ris_procedura->fetch(PDO::FETCH_ASSOC);
					$record_gara["nome_procedura"] = $rec_procedura["nome"];
					$record_gara["riferimento_procedura"] = $rec_procedura["riferimento_normativo"];
				}
				$bind=array();
				$bind[":codice"] =  $record_gara["criterio"];
				$sql_criterio = "SELECT * FROM b_criteri WHERE codice=:codice";
				$ris_criterio = $pdo->bindAndExec($sql_criterio,$bind);
				if ($ris_criterio->rowCount()>0) {
					$rec_criterio = $ris_criterio->fetch(PDO::FETCH_ASSOC);
					$record_gara["nome_criterio"] = $rec_criterio["criterio"];
					/* Completamento in caso di Massimo ribasso */
					if ($record_gara["criterio"] == 6) {
						$oggetto_ribasso = " sull'importo a base di gara";
						$bind=array();
						$bind[":codice"] =  $record_gara["codice"];

						$sql_opzione = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice AND opzione = 58";
						$ris_opzione = $pdo->bindAndExec($sql_opzione,$bind);
						if ($ris_opzione->rowCount()>0) $oggetto_ribasso = " sull'elenco prezzi";
						$record_gara["nome_criterio"] .= $oggetto_ribasso;
					}
					/* Fine completamento MR */
					$record_gara["riferimento_criterio"] = $rec_criterio["riferimento_normativo"];
				}

				$bind = array();
				$bind[":codice"] = $record_gara["codice_gestore"];
				$sql_gestore  = "SELECT b_enti.* ";
				$sql_gestore .= "FROM b_enti ";
				$sql_gestore .= "WHERE b_enti.codice = :codice";

				$ris_gestore = $pdo->bindAndExec($sql_gestore,$bind);
				$bind = array();
				$bind[":codice"] = $record_gara["codice_ente"];
				$sql_appaltatore = "SELECT b_enti.* ";
				$sql_appaltatore .= "FROM b_enti ";
				$sql_appaltatore .= "WHERE b_enti.codice = :codice";

				$ris_appaltatore = $pdo->bindAndExec($sql_appaltatore,$bind);

				if ($ris_appaltatore->rowCount() > 0 && $ris_gestore->rowCount() > 0)
				{
					$rec_gestore = $ris_gestore->fetch(PDO::FETCH_ASSOC);
					$rec_appaltatore = $ris_appaltatore->fetch(PDO::FETCH_ASSOC);
					if (isset($punteggi) && is_array($punteggi))
					{
						ob_start();
						?>
						<html>
						<head>
							<style>
								body {	font-family: Tahoma, Geneva, sans-serif; text-align:justify; }
								div { margin:1px; padding:10px;border:1px solid #000; }
								div div { margin:0px; padding:0px; margin-left:20px; border:none }
								table.no_border td { padding:2px; border:none; vertical-align:top; }
								ol li ol {list-style-type:lower-alpha; }
								h2 { border-bottom:1px solid #000 }
								h2 > img { display: none; }
								h3 { border-bottom:1px solid #000 }
								.int { color: #000000 !important; background-color: #CCCCCC; text-align: left !important; }
								.pnt { background-color: #CCC !important; }
								td.transparent { background-color: #eee !important; }
								.macro {background-color: #CCCCCC; }
								.padding { padding: 20px; border: none; }
								.padding-bordered { padding: 10px; }
								.triang { border:1px solid #000;  border:none; }
								.commissario { color: #CC0000; }
								table td { padding:3px; border:1px solid #000; }
								table th { padding:3px; color: #000000; }
								div.no_padding {padding:0px;}

							</style>
						</head>
						<body>
						<table style="width:100%; table-layout: fixed;">
							<tbody>
								<tr>
									<td style="width:20%; text-align:center;">
										<img width="100" src="<?= $config["link_sito"] ?>/documenti/enti/<?=$rec_gestore["logo"]?>">
									</td>
									<td style="width:60%; text-align:center;">
										<h1 style="text-align:center"><?=$rec_gestore["denominazione"]?></h1>
										<?
										if ($rec_gestore["codice"] != $rec_appaltatore["codice"])
										{
											echo "<h1 style=\"text-align:center\">Stazione unica appaltante</h1>";
											echo "<h2 style=\"text-align:center\">" . $rec_appaltatore["denominazione"] . "</h2>";
										}
										?>
									</td>
									<td style="width:20%; text-align:center;">
										<?
										if ($rec_gestore["codice"] != $rec_appaltatore["codice"]) {
											echo "<img src=\"" . $config["link_sito"] . "/documenti/enti/" . $rec_appaltatore["logo"] . "\" width=\"150\">";
										}
										?>
										&nbsp;
									</td>
								</tr>
								<tr>
									<td colspan="3" style="text-align:center">
										<h2 style="text-align:center">CALCOLO DELL&#39;OFFERTA ECONOMICAMENTE PI&Ugrave; VANTAGGIOSA - METODO AGGREGATIVO - COMPENSATORE</h2>
										Procedura: <?= $record_gara["nome_procedura"] ?> ai sensi dell'<?= $record_gara["riferimento_procedura"] ?><br>
										Criterio: <?= $record_gara["nome_criterio"] ?> ai sensi dell'<?= $record_gara["riferimento_criterio"] ?>
									</td>
								</tr>
								<tr>
									<td colspan="3">
										<strong>Oggetto</strong>:<?= $record_gara["oggetto"] ?>
									</td>
								</tr>
							</tbody>
						</table>
						<br><br><br><br>
						<h2>PARTECIPANTI</h2>
						<table class="no_border" width="100%" style="table-layout: fixed;">
							<thead>
								<tr style="color: #000000 !important; background-color: #CCCCCC; text-align: left !important;">
									<td width="5%" align="center">#</td>
									<td width="20%">Codice Fiscale Impresa</td>
									<td width="75%">Ragione Sociale</td>
								</tr>
							</thead>
							<tbody>
								<?
								foreach ($partecipanti as $partecipante)
								{
									?>
									<tr>
										<td width="5%" style="padding:2px" align="center"><?= $partecipante[1] ?></td>
										<td width="20%" style="padding:2px"><?= $partecipante[3] ?></td>
										<td width="75%" style="padding:2px"><?= $partecipante[2] ?></td>
									</tr>
									<?
								}
								?>
							</tbody>
						</table>
						<br><br><br><br>
						<?
						$temp_criteri = $criteri;
						$valutazione = false;
						$bind=array();
						$bind[":codice"] = $codice_gara;
						$sql_criteri = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice WHERE b_valutazione_tecnica.codice_gara = :codice AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' AND b_valutazione_tecnica.codice_padre = 0  AND b_valutazione_tecnica.tipo = 'Q' ORDER BY b_valutazione_tecnica.codice";
						$ris_criteri = $pdo->bindAndExec($sql_criteri,$bind);
						include_once($root.'/confrontoacoppie/criteri.php');
						unset($criteri);
						$criteri = $temp_criteri;

						$tot_macro = array();
						$tot_offerta_economica = 0;
						foreach ($criteri as $i => $criterio)
						{


							$tot_offerta_economica += $criterio["punteggio"];
							$has_sub_cri = (isset($criterio["subcriteri"]) ? true : false);
							?>
							<br><br><br><br>
							<h2>Criterio EVT.<?= $i . " - " . $criterio["descrizione"] . " - " . $criterio["punteggio"] . " pnt"?></h2>
							<table width="100%;" style="table-layout: fixed;">
								<thead>
									<tr style="color: #000000 !important; background-color: #CCCCCC; text-align: center !important;">
										<td width="70%" colspan="2">Partecipanti</td>
										<td width="10%" style="vertical-align:middle">Somma Punteggi</td>
										<td width="10%" style="vertical-align:middle">Coefficienti</td>
										<td width="10%" style="vertical-align:middle">Punteggio Finale</td>
									</tr>
								</thead>
								<tbody>
								<?
									$pnt_macro = array();
									if ($has_sub_cri)
									{
										$tot_sub_criteri = array();
										foreach ($criterio["subcriteri"] as $k => $subcriterio)
										{
											$tot_sub_criteri[$subcriterio["codice"]] = array();
											$pnt_sub = array();
											foreach ($commissari as $commissario)
											{
												$pnt_sub_commissario = array();
												foreach ($partecipanti as $j => $partecipante)
												{
													if (!isset($tot_sub_criteri[$subcriterio["codice"]][$partecipante[0]])) $tot_sub_criteri[$subcriterio["codice"]][$partecipante[0]] = round(0.000,3);
													if (!isset($pnt_sub_commissario[$partecipante[0]])) $pnt_sub_commissario[$partecipante[0]] = round(0.000,3);
													if (!isset($pnt_sub[$partecipante[0]])) $pnt_sub[$partecipante[0]] = round(0.000,3);
													if (!isset($pnt_macro[$partecipante[0]])) $pnt_macro[$partecipante[0]] = round(0.000,3);
													if (!isset($tot_macro[$partecipante[0]])) $tot_macro[$partecipante[0]] = round(0.000,3);
													$pnt_sub_commissario[$partecipante[0]] += round($punteggi[$criterio["codice"]][$subcriterio["codice"]][$commissario[0]][$partecipante[0]],3);
												}

												$pnt_sub_commissario = normalizza($pnt_sub_commissario);

												//Sommo il punteggio del commissario al totale del subcriterio
												foreach ($pnt_sub_commissario as $part => $pnt)
												{
													$pnt_sub[$part] += round($pnt,3);
													$tot_sub_criteri[$subcriterio["codice"]][$part] += round($pnt,3);
												}
											}

											//Normalizzo il subcriterio per ottenere i punteggi di ogni partecipante
											$pnt_sub = normalizza($pnt_sub, $subcriterio["punteggio"]);

											foreach ($pnt_sub as $part => $pnt)
											{
												$pnt_macro[$part] += round($pnt,3);
											}
										}
									}
									else
									{
										# NON CI SONO SOTTOCRITERI
										$pnt_criterio = array();
										foreach ($commissari as $commissario)
										{
											$pnt_macro_commissario = array();
											foreach ($partecipanti as $j => $partecipante)
											{
												if (!isset($pnt_macro_commissario[$partecipante[0]])) $pnt_macro_commissario[$partecipante[0]] = round(0.000,3);
												if (!isset($pnt_criterio[$partecipante[0]])) $pnt_criterio[$partecipante[0]] = round(0.000,3);
												if (!isset($pnt_macro[$partecipante[0]])) $pnt_macro[$partecipante[0]] = round(0.000,3);
												if (!isset($tot_macro[$partecipante[0]])) $tot_macro[$partecipante[0]] = round(0.000,3);
												$pnt_macro_commissario[$partecipante[0]] += round($punteggi[$criterio["codice"]][$commissario[0]][$partecipante[0]],3);
											}

											$pnt_macro_commissario = normalizza($pnt_macro_commissario);

											//Sommo il punteggio del commissario al totale del macrocriterio
											foreach ($pnt_macro_commissario as $part => $pnt)
											{
												$pnt_macro[$part] += round($pnt,3);
											}
										}
									}

									$coef_macro = normalizza($pnt_macro);
									$subtot_macro = normalizza($pnt_macro, $criterio["punteggio"]);
									foreach ($partecipanti as $partecipante)
									{
										$tot_macro[$partecipante[0]] += round($subtot_macro[$partecipante[0]],3);
										?>
										<tr>
											<td width="10%" style="text-align:center"><?= $partecipante[1] ?></td>
											<td width="60%"><?= $partecipante[2] ?></td>
											<td width="10%" style="text-align:center"><?= number_format(round($pnt_macro[$partecipante[0]],3),3); ?></td>
											<td width="10%" style="text-align:center"><?= number_format(round($coef_macro[$partecipante[0]],3),3); ?></td>
											<td width="10%" style="text-align:center"><?= number_format(round($subtot_macro[$partecipante[0]],3),3); ?></td>
										</tr>
										<?
									}
									?>
								</tbody>
							</table>
							<div style="padding: 20px; border: none;"></div>
							<?
							if ($has_sub_cri)
							{
								foreach ($criterio["subcriteri"] as $k => $subcriterio)
								{
									?>
									<div>
										<div style="padding: 20px; border: none;">
											<h4>Sottocriterio EVT.<?= $i . "." . $k . " - " . $subcriterio["descrizione"] ?></h4>
											<table style="width:100%; table-layout: fixed;">
												<thead>
													<tr style="color: #000000 !important; background-color: #CCCCCC; text-align: left !important;">
														<td width="15%">Criterio EVT.<?= $i . "." . $k ?></td>
														<td width="75%"><?= $subcriterio["descrizione"] ?></td>
														<td width="10%">Peso <?= $subcriterio["punteggio"] ?></td>
													</tr>
												</thead>
											</table>
											<table width="100%" style="table-layout: fixed;">
												<thead>
													<tr style="color: #000000 !important; background-color: #CCCCCC; text-align: left !important;">
														<td width="70%" colspan="2">Partecipanti</td>
														<td width="10%">Somma Coefficienti</td>
														<td width="10%">Coefficienti</td>
														<td width="10%">Punteggio Finale</td>
													</tr>
												</thead>
												<tbody>
												<?
												foreach ($partecipanti as $partecipante)
												{
													$coef_tot_sub = normalizza($tot_sub_criteri[$subcriterio["codice"]]);
													$tot_sub = normalizza($tot_sub_criteri[$subcriterio["codice"]], $subcriterio["punteggio"]);
													?>
													<tr>
														<td width="10%"><?= $partecipante[1] ?></td>
														<td width="60%"><?= $partecipante[2] ?></td>
														<td width="10%" style="text-align:center"><?= round($tot_sub_criteri[$subcriterio["codice"]][$partecipante[0]],3); ?></td>
														<td width="10%" style="text-align:center"><?= round($coef_tot_sub[$partecipante[0]],3); ?></td>
														<td width="10%" style="text-align:center"><?= round($tot_sub[$partecipante[0]],3); ?></td>
													</tr>
													<?
												}
												?>
												</tbody>
											</table>
											<div style="margin-left: 20px; border: none;">
												<?
												foreach ($commissari as $p => $commissario)
												{
													?>
													<div style="padding: 20px; border: none;">
														<h4 style="color: #CC0000;"><?= $p + 1 . " " . $commissario[1] ?></h4>
														<table style="width:100%; table-layout: fixed;">
															<thead>
																<tr style="color: #000000 !important; background-color: #CCCCCC; text-align: center !important;">
																	<td width="70%" colspan="2">Partecipanti</td>
																	<td width="10%">Somma Punteggi</td>
																	<td width="10%">Coefficienti</td>
																	<td width="10%">Punteggio Finale</td>
																</tr>
															</thead>
															<tbody>
																<?
																foreach ($partecipanti as $partecipante)
																{
																	$coef = normalizza($punteggi[$criterio["codice"]][$subcriterio["codice"]][$commissario[0]]);
																	$tot = normalizza($punteggi[$criterio["codice"]][$subcriterio["codice"]][$commissario[0]],$subcriterio["punteggio"]);
																	?>
																	<tr>
																		<td width="10%"><?= $partecipante[1] ?></td>
																		<td width="60%"><?= $partecipante[2] ?></td>
																		<td width="10%" align="center"><?= $punteggi[$criterio["codice"]][$subcriterio["codice"]][$commissario[0]][$partecipante[0]]; ?></td>
																		<td width="10%" align="center"><?= $coef[$partecipante[0]] ?></td>
																		<td width="10%" align="center"><?= $tot[$partecipante[0]] ?></td>
																	</tr>
																	<?
																}
																?>
															</tbody>
														</table>
													</div>
													<?
													$triang = array();
													$cod_sub = $subcriterio["codice"];
													$cod_comm = $commissario[0];
													$bind = array();
													$bind[":codice_criterio"] = $cod_sub ;
													$bind[":codice_lotto"] = $codice_lotto ;
													$bind[":codice_gara"] = $codice_gara ;
													$bind[":codice_commissario"] = $cod_comm;
													$sql_valutazione  = "SELECT * FROM `b_confronto_coppie` ";
													$sql_valutazione .= "WHERE `codice_criterio` = :codice_criterio ";
													$sql_valutazione .= "AND `codice_lotto` = :codice_lotto ";
													$sql_valutazione .= "AND `codice_gara` = :codice_gara ";
													$sql_valutazione .= "AND `codice_commissario` = :codice_commissario";

													$ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);

													while ($rec_valutazione = $ris_valutazione->fetch(PDO::FETCH_ASSOC))
													{
														$triang[$rec_valutazione["codice_criterio"]][$rec_valutazione["codice_partecipante_1"]][$rec_valutazione["codice_partecipante_2"]] = [$rec_valutazione["punteggio_partecipante_1"],$rec_valutazione["punteggio_partecipante_2"]];
													}
													?>
														<table width="100%" style="table-layout: fixed; border:1px solid #000;">
															<tbody style="font-size: 90%">
																<tr>
																	<th style="color: #000000 !important; background-color: #CCCCCC; text-align: center !important;"></th>
																	<?
																	for ($n=1; $n < count($partecipanti); $n++)
																	{
																		?> <th style="color: #000000 !important; background-color: #CCCCCC; text-align: center !important;"><?= $partecipanti[$n][1] ?></th> <?
																	}
																	?>
																</tr>
																<?
																foreach ($partecipanti as $tmp_partecipante) {
																	?>
																	<tr>
																		<th style="color: #000000 !important; background-color: #CCCCCC; text-align: center !important;"><?= $tmp_partecipante[1] ?></th>
																		<?
																		echo str_repeat('<td style="background-color: #eee !important; border:none;"></td>', array_search($tmp_partecipante, $partecipanti));
																		for ($n = array_search($tmp_partecipante, $partecipanti); $n < count($partecipanti) - 1; $n++)
																		{
																			?><td style="background-color: #CCC !important;"><?= $tmp_partecipante[1] ?><?= (isset($triang[$subcriterio["codice"]][$tmp_partecipante[0]][$partecipanti[$n + 1][0]][0]) ? $triang[$subcriterio["codice"]][$tmp_partecipante[0]][$partecipanti[$n + 1][0]][0] : "N/P") ?><br>
																			<?= $partecipanti[$n + 1][1] ?><?= (isset($triang[$subcriterio["codice"]][$tmp_partecipante[0]][$partecipanti[$n + 1][0]][1]) ? $triang[$subcriterio["codice"]][$tmp_partecipante[0]][$partecipanti[$n + 1][0]][1] : "N/P") ?></td><?
																		}
																		?>
																	</tr>
																	<?
																}
																?>
															</tbody>
														</table>
													<?
												}
												?>
											</div>
										</div>
									</div>
									<?
								}
							}
							else
							{
								?>
								<div>
									<div style="margin-left: 20px; border: none;">
										<?
									foreach ($commissari as $p => $commissario)
									{
									?>
										<h4 style="color: #CC0000;"><?= $p + 1 . " " . $commissario[1] ?></h4>
										<table style="width:100%; table-layout: fixed;">
											<thead>
												<tr style="color: #000000 !important; background-color: #CCCCCC; text-align: center !important;">
													<td colspan="2">Partecipanti</td>
													<td>Somma Punteggi</td>
													<td>Coefficienti</td>
													<td>Punteggio Finale</td>
												</tr>
											</thead>
											<tbody>
												<?
												foreach ($partecipanti as $partecipante)
												{
													$coef = normalizza($punteggi[$criterio["codice"]][$commissario[0]]);
													$tot = normalizza($punteggi[$criterio["codice"]][$commissario[0]],$criterio["punteggio"]);
													?>
													<tr>
														<td><?= $partecipante[1] ?></td>
														<td><?= $partecipante[2] ?></td>
														<td align="center"><?= $punteggi[$criterio["codice"]][$commissario[0]][$partecipante[0]]; ?></td>
														<td align="center"><?= $coef[$partecipante[0]] ?></td>
														<td align="center"><?= $tot[$partecipante[0]] ?></td>
													</tr>
													<?
												}
												?>
											</tbody>
										</table>
										<br>
										<?
										$triang = array();
										$cod_cri = $criterio["codice"];
										$cod_comm = $commissario[0];
										$bind = array();
										$bind[":codice_criterio"] = $cod_cri;
										$bind[":codice_lotto"] = $codice_lotto;
										$bind[":codice_gara"] = $codice_gara;
										$bind[":codice_commissario"] = $cod_comm;
										$sql_valutazione  = "SELECT * FROM `b_confronto_coppie` ";
										$sql_valutazione .= "WHERE `codice_criterio` = :codice_criterio ";
										$sql_valutazione .= "AND `codice_lotto` = :codice_lotto ";
										$sql_valutazione .= "AND `codice_gara` = :codice_gara ";
										$sql_valutazione .= "AND `codice_commissario` = :codice_commissario";

										$ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);

										while ($rec_valutazione = $ris_valutazione->fetch(PDO::FETCH_ASSOC))
										{
											$triang[$rec_valutazione["codice_criterio"]][$rec_valutazione["codice_partecipante_1"]][$rec_valutazione["codice_partecipante_2"]] = [$rec_valutazione["punteggio_partecipante_1"],$rec_valutazione["punteggio_partecipante_2"]];
										}
										?>
											<table width="100%" style="table-layout: fixed; border:1px solid #000;">
												<tbody style="font-size: 90%">
													<tr>
														<td style="color: #000000 !important; background-color: #CCCCCC; text-align: center !important;"></td>
														<?
														for ($n=1; $n < count($partecipanti); $n++)
														{
															?> <th style="color: #000000 !important; background-color: #CCCCCC; text-align: center !important;" ><?= $partecipanti[$n][1] ?></th> <?
														}
														?>
													</tr>
													<?
													foreach ($partecipanti as $tmp_partecipante) {
														?>
														<tr>
															<th colspan="2" style="color: #000000 !important; background-color: #CCCCCC; text-align: center !important;" ><?= $tmp_partecipante[1] ?></th>
															<?
															echo str_repeat('<td style="background-color: #eee !important;"></td>', array_search($tmp_partecipante, $partecipanti));
															for ($n = array_search($tmp_partecipante, $partecipanti); $n < count($partecipanti) - 1; $n++)
															{
																?><td style="background-color: #CCC !important;"><?= $tmp_partecipante[1] ?><?= (isset($triang[$criterio["codice"]][$tmp_partecipante[0]][$partecipanti[$n + 1][0]][0]) ? $triang[$criterio["codice"]][$tmp_partecipante[0]][$partecipanti[$n + 1][0]][0] : "N/P") ?><br>
																<?= $partecipanti[$n + 1][1] ?><?= (isset($triang[$criterio["codice"]][$tmp_partecipante[0]][$partecipanti[$n + 1][0]][1]) ? $triang[$criterio["codice"]][$tmp_partecipante[0]][$partecipanti[$n + 1][0]][1] : "N/P") ?></td><?
															}
															?>
														</tr>
														<?
													}
													?>
												</tbody>
											</table>
										<?
									}
									?>
									</div>
								</div>
								<?
							}
						}
						?>
						<br><br><br><br>
						<h2>PUNTEGGIO FINALE</h2>
						<table style="width:100%; table-layout: fixed;">
							<thead>
								<tr style="color: #000000 !important; background-color: #CCCCCC; text-align: center !important;">
									<td width="70%" colspan="2">Partecipanti</td>
									<td width="10%">Totale Elementi Tecnici</td>
									<td width="10%">Coefficienti</td>
									<td width="10%">Punteggio Finale</td>
								</tr>
							</thead>
							<tbody>
								<?
								$tot_macro_coef = normalizza($tot_macro);
								$show_coeff = false;
								$tot = $tot_macro;
								//Riparametrazione Assoluta
								$bind = array();
								$bind[":codice_gara"] = $codice_gara;
								$sql_opzione = "SELECT * FROM b_opzioni_selezionate WHERE `codice_gara` = :codice_gara AND opzione = 128";
								$ris_opzione = $pdo->bindAndExec($sql_opzione,$bind);
								if ($ris_opzione->rowCount() > 0)
								{
									$show_coeff = true;
									$tot = normalizza($tot_macro,$tot_offerta_economica);
								}

								foreach ($partecipanti as $partecipante)
								{
									?>
									<tr>
										<td width="10%" align="center"><?= $partecipante[1] ?></td>
										<td width="60%"><?= $partecipante[2] ?></td>
										<td width="10%" align="center"><?= $tot_macro[$partecipante[0]]  ?></td>
										<td width="10%" align="center"><?= ($show_coeff ? $tot_macro_coef[$partecipante[0]] : "&nbsp;")  ?></td>
										<td width="10%" align="center"><?= $tot[$partecipante[0]]  ?></td>
									</tr>
									<?
								}
								?>
							</tbody>
						</table>
						<?
						if ($show_coeff == true) {
						?>
						<p align="right">(* &Egrave; stata applicata la riparametrazione assoluta)</p>
						<?
						}
						?>
						</body>
						</html>
						<?
						$html = ob_get_clean();
						$options = new Options();
						$options->set('defaultFont', 'Helvetica');

						$percorso = $config["arch_folder"];

						$allegato["online"] = 'N';
						$allegato["codice_gara"] = $codice_gara;
						$allegato["codice_ente"] = $_SESSION["ente"]["codice"];

						$percorso .= "/".$allegato["codice_gara"];

						if (!is_dir($percorso)) mkdir($percorso,0777,true);
						$allegato["nome_file"] = $allegato["codice_gara"] . " - Verbale confronto a coppie.".time().".pdf";
						$allegato["titolo"] = "Verbale confronto a coppie";

						$dompdf = new Dompdf($options);
						$dompdf->loadHtml(utf8_encode($html));
						$dompdf->setPaper('A3', 'landscape');
						$dompdf->render();

						$pdf = $dompdf->output();

						file_put_contents("{$percorso}/{$allegato["nome_file"]}", $pdf);

						if (file_exists($percorso."/".$allegato["nome_file"])) {
							$allegato["riferimento"] = getRealName($percorso."/".$allegato["nome_file"]);
							rename($percorso."/".$allegato["nome_file"],$percorso."/".$allegato["riferimento"]);
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_allegati";
							$salva->operazione = "INSERT";
							$salva->oggetto = $allegato;
							$codice_allegato = $salva->save();
							header("Location: /allegati/download_allegato.php?codice=".$codice_allegato);
							die();
						}
					}
				}
			}
		}
	}


?>
