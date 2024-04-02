<?
	use Dompdf\Dompdf;
	use Dompdf\Options;
	session_start();
	session_write_close();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	ini_set('memory_limit', '512M');
  	ini_set('max_execution_time', 600);
	if (isset($_POST) && isset($_POST["codice"]) && isset($_POST["lotto"]) && isset($_POST["codice_commissario"]))
	{
		$codice_gara = $_POST["codice"];
		$codice_lotto = $_POST["lotto"];
		$n_partecipanti = $_POST["partecipanti"];
		$codice_commissario = $_POST["codice_commissario"];
	}
	else
	{
		?>Errore. Si prega di riprovare!<?
		die();
	}

	$edit = false;
	if (isset($_SESSION["codice_utente"]))
	{
		$edit = check_permessi("gare/elaborazione",$_SESSION["codice_utente"]);
		if (!$edit)
		{
			?>Errore. Non si dispone dei permessi<br>per questa operazione!<?
			die();
		}
	}
	else
	{
		?>Errore. Non si dispone dei permessi<br>per questa operazione!<?
		die();
	}

	if (!$edit)
	{
		?>Errore. Non si dispone dei permessi<br>per questa operazione!<?
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
			?>Errore. Gara non trovata!<?
			die();
		}
		else
		{
			$bind = array();
			$bind[":codice_gara"] = $codice_gara;
			$bind[":codice_commissario"] = $codice_commissario;
			$sql_commissario = "SELECT * FROM `b_commissioni` WHERE `b_commissioni`.`codice_gara` = :codice_gara AND `b_commissioni`.`codice` = :codice_commissario";
			$ris_commissario = $pdo->bindAndExec($sql_commissario,$bind);

			if ($ris_commissario->rowCount() > 0)
			{
				$bind = array();
				$bind[":codice_gara"] = $codice_gara;
				$rec_commissario = $ris_commissario->fetch(PDO::FETCH_ASSOC);
				//Seleziono i criteri di tipo Qualitativo
				$sql_criteri  = "SELECT b_valutazione_tecnica.codice ";
				$sql_criteri .= "FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice ";
				$sql_criteri .= "WHERE ( ";
					$sql_criteri .= "b_valutazione_tecnica.tipo = 'Q' ";
					$sql_criteri .= "AND b_valutazione_tecnica.codice_padre IN ( ";
						$sql_criteri .= "SELECT b_valutazione_tecnica.codice ";
						$sql_criteri .= "FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice ";
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
					$n_criteri = $ris_criteri->rowCount();
					$n_confronti = fattoriale($n_partecipanti) / ( fattoriale($n_partecipanti - 2) * fattoriale(2) ) * $n_criteri;

					$bind = array();
					$bind[":codice_gara"] = $codice_gara;
					$bind[":codice_lotto"] = $codice_lotto;
					$bind[":codice_commissario"] = $codice_commissario;
					$sql_valutazione  = "SELECT * FROM `b_confronto_coppie` ";
					$sql_valutazione .= "WHERE `codice_lotto` = $codice_lotto ";
					$sql_valutazione .= "AND `codice_gara` = $codice_gara ";
					$sql_valutazione .= "AND `codice_commissario` = " . $codice_commissario;

					$ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);

					$n_valutazioni = $ris_valutazione->rowCount();
					if ($n_valutazioni > 0)
					{
						while ($rec_valutazioni = $ris_valutazione->fetch(PDO::FETCH_ASSOC))
						{
							if ($rec_valutazioni["punteggio_partecipante_1"] == 0 && $rec_valutazioni["punteggio_partecipante_2"] == 0) $n_valutazioni -= 1;
						}

						if ((string)$n_confronti == (string)$n_valutazioni)
						{
							$rec_gara = $ris_gara->fetch(PDO::FETCH_ASSOC);
							$rec_gara["nome_procedura"] = "";
							$bind = array();
							$bind[":codice"] = $rec_gara["procedura"];
							$sql_procedura = "SELECT * FROM b_procedure WHERE codice = :codice";
							$ris_procedura = $pdo->bindAndExec($sql_procedura,$bind);
							if ($ris_procedura->rowCount()>0) {
								$rec_procedura = $ris_procedura->fetch(PDO::FETCH_ASSOC);
								$rec_gara["nome_procedura"] = $rec_procedura["nome"];
								$rec_gara["riferimento_procedura"] = $rec_procedura["riferimento_normativo"];
							}
							$bind = array();
							$bind[":codice"] = $rec_gara["criterio"];
							$sql_criterio = "SELECT * FROM b_criteri WHERE codice = :codice";
							$ris_criterio = $pdo->bindAndExec($sql_criterio,$bind);
							if ($ris_criterio->rowCount()>0) {
								$rec_criterio = $ris_criterio->fetch(PDO::FETCH_ASSOC);
								$rec_gara["nome_criterio"] = $rec_criterio["criterio"];
								/* Completamento in caso di Massimo ribasso */
								if ($rec_gara["criterio"] == 6) {
									$oggetto_ribasso = " sull'importo a base di gara";
									$bind = array();
									$bind[":codice"] = $rec_gara["codice"];
									$sql_opzione = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice AND opzione = 58";
									$ris_opzione = $pdo->bindAndExec($sql_opzione,$bind);
									if ($ris_opzione->rowCount()>0) $oggetto_ribasso = " sull'elenco prezzi";
									$rec_gara["nome_criterio"] .= $oggetto_ribasso;
								}
								/* Fine completamento MR */
								$rec_gara["riferimento_criterio"] = $rec_criterio["riferimento_normativo"];
							}
							$bind = array();
							$bind[":codice"] = $rec_gara["codice_gestore"];
							$sql_gestore  = "SELECT b_enti.* ";
							$sql_gestore .= "FROM b_enti ";
							$sql_gestore .= "WHERE b_enti.codice = :codice";

							$ris_gestore = $pdo->bindAndExec($sql_gestore,$bind);
							$bind = array();
							$bind[":codice"] = $rec_gara["codice_ente"];
							$sql_appaltatore = "SELECT b_enti.* ";
							$sql_appaltatore .= "FROM b_enti ";
							$sql_appaltatore .= "WHERE b_enti.codice = :codice";

							$ris_appaltatore = $pdo->bindAndExec($sql_appaltatore,$bind);

							if ($ris_appaltatore->rowCount() > 0 && $ris_gestore->rowCount() > 0)
							{
								$rec_gestore = $ris_gestore->fetch(PDO::FETCH_ASSOC);
								$rec_appaltatore = $ris_appaltatore->fetch(PDO::FETCH_ASSOC);
								$valutazione = false;
								ob_start();
								?>
								<html>
									<head>
										<style>
											body {	font-family: Tahoma, Geneva, sans-serif; text-align:justify; }
											div { margin:1px; padding:10px;border:1px solid #000; }
											div div { margin:0px; padding:0px; margin-left:20px; border:none }
											table td { padding:2px; border:1px solid #000 }
											table.no_border td { padding:2px; border:none; vertical-align:top; }
											ol li ol {list-style-type:lower-alpha; }
											h2 { border-bottom:1px solid #000 }
											h2 > img { display: none; }
											.partecipante { color: #000000 !important; background-color: #CCCCCC; text-align: center !important; }
											.pnt { background-color: #CCC !important; }
											.transparent { background-color: #eee !important; }
											.macro {background-color: #CCCCCC; }
											.padding { padding: 20px; border: none; }
											.triang { border:1px solid #000; }
										</style>
									</head>
									<body>
									<table style="width:100%">
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
												<h2 style="text-align:center">Esito valutazione confronto a coppie commissario <?= $rec_commissario["titolo"] . " " . $rec_commissario["cognome"] . " " . $rec_commissario["nome"] ?></h2>
												Procedura: <?= $rec_gara["nome_procedura"] ?> ai sensi dell'<?= $rec_gara["riferimento_procedura"] ?><br>
												Criterio: <?= $rec_gara["nome_criterio"] ?> ai sensi dell'<?= $rec_gara["riferimento_criterio"] ?>
											</td>
										</tr>
										<tr>
											<td colspan="3">
												<strong>Oggetto</strong>:<?= $rec_gara["oggetto"] ?>
											</td>
										</tr>
									</table>
									<br><br><br>
									<h2>CALCOLO DELL&#39;OFFERTA ECONOMICAMENTE PI&Ugrave; VANTAGGIOSA - METODO AGGREGATIVO - COMPENSATORE</h2><br><br>
									<h2 style="cursor:pointer" onclick="toggle_partecipanti()">PARTECIPANTI</h2>
									<?
									$_SESSION["codice_commissario"] = $rec_commissario["codice"];

									$bind = array();
									$bind[":codice_gara"] = $rec_gara["codice"];
									$bind[":codice_lotto"] = $codice_lotto;
									$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) ORDER BY codice";
									$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,$bind);
									include_once($root.'/confrontoacoppie/partecipanti.php');
									?>
									<br><br>
									<?
									$bind = array();
									$bind[":codice_gara"] = $rec_gara["codice"];
									$sql_criteri = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' AND b_valutazione_tecnica.codice_padre = 0  AND b_valutazione_tecnica.tipo = 'Q' ORDER BY b_valutazione_tecnica.codice";
									$ris_criteri = $pdo->bindAndExec($sql_criteri,$bind);
									include_once($root.'/confrontoacoppie/criteri.php');
									?>
									<br><br>
									<h2 style="cursor:pointer">TABELLE TRIANGOLARI</h2>
									<?

									include_once('tabelle_triangolari.php');
									unset($_SESSION["codice_commissario"]);
									?>
									</body></html>
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
									$allegato["nome_file"] = $allegato["codice_gara"] . " - Verbale confronto a coppie " . $rec_commissario["titolo"] . " " . $rec_commissario["cognome"] . " " . $rec_commissario["nome"] . "." . time() . ".pdf";
									$allegato["titolo"] = "Verbale confronto a coppie " . $rec_commissario["titolo"] . " " . $rec_commissario["cognome"] . " " . $rec_commissario["nome"];

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

									}

									$file = $percorso."/".$allegato["nome_file"];
									header('Content-type: application/pdf');
									header('Content-Disposition: inline; filename="Verbale.pdf"');
									header('Content-Transfer-Encoding: binary');
									header('Content-Length: ' . filesize($file));
									echo $pdf;

							}
						}
						else
						{
							?><p align="center">Il commissario non ha ancora terminato i lavori.<br>Si prega di riprovare in seguito</p<?
							die();
						}
					}
					else
					{
						?><p align="center">Il commissario non ha ancora terminato i lavori.<br>Si prega di riprovare in seguito</p<?
						die();
					}

				}

			}
		}
	}


?>
