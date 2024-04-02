<?
	$codice_partecipante_post = 0;
	$errore_salvataggio = false;
	$values = array();

	if (isset($_POST) &&
		in_array("codice_lotto", array_keys($_POST)) &&
		in_array("codice_gara", array_keys($_POST)) &&
		in_array("codice_commissario", array_keys($_POST)) &&
		in_array("codice_criterio", array_keys($_POST)) &&
		in_array("valori", array_keys($_POST)) && is_array($_POST["valori"]))
	{
		foreach ($_POST["valori"] as $key => $valutazione)
		{
			$relazioni = array();
			$chiavi_relazioni_escluse = array();
			$chiavi_relazioni_incluse = array();
			$chiavi_testi = array("testo");
			$chiavi_date = array("data_atto_esito");
			$chiavi_password = array();
			$chiavi_ignora = array("id");
			$dati = array();

			$dati["codice_lotto"] = $_POST["codice_lotto"];
			$dati["codice_gara"] = $_POST["codice_gara"];
			$dati["codice_commissario"] = $_SESSION["codice_commissario"];
			$dati["codice_criterio"] = $_POST["codice_criterio"];
			$codice_partecipante_post = $dati["codice_partecipante_1"] = $valutazione["codice_partecipante_1"];
			$dati["punteggio_partecipante_1"] = $valutazione["punteggio_partecipante_1"];
			$dati["codice_partecipante_2"] = $valutazione["codice_partecipante_2"];
			$dati["punteggio_partecipante_2"] = $valutazione["punteggio_partecipante_2"];

			if ((!is_numeric($dati["punteggio_partecipante_1"]) && !is_numeric($dati["punteggio_partecipante_2"])) ||
				($dati["punteggio_partecipante_1"] == "" || $dati["punteggio_partecipante_2"] == "") ||
				 $dati["codice_partecipante_1"] != $partecipante_valutazione ||
				 $_POST["codice_lotto"] != $codice_lotto ||
				 $dati["codice_gara"] != $codice_gara ||
				 $dati["codice_criterio"] != $criterio_valutazione)
			{
				$errore_salvataggio = true;
				$values = $_POST["valori"];
				$values[$key]["error"] = true;
				$partecipante_valutazione = (isset($dati["codice_partecipante_1"]) ? $dati["codice_partecipante_1"] : $_GET["partecipante"]);
				$criterio_valutazione = $_POST["codice_criterio"];
				break;
			}
			else
			{
				if (($dati["punteggio_partecipante_1"] <= 6 && $dati["punteggio_partecipante_2"] <= 6) &&
					(
						($dati["punteggio_partecipante_1"] == 1 && $dati["punteggio_partecipante_2"] == 1) ||
						($dati["punteggio_partecipante_1"] > 0 && $dati["punteggio_partecipante_2"] == 0) ||
						($dati["punteggio_partecipante_1"] == 0 && $dati["punteggio_partecipante_2"] > 0) ||
						($dati["punteggio_partecipante_1"] == 0 && $dati["punteggio_partecipante_2"] == 0)
					))
				{
					$bind = array();
					$bind[":codice_lotto"] = $codice_lotto;
					$bind[":codice_gara"] = $codice_gara;
					$bind[":codice_commissario"] = $_SESSION["codice_commissario"];
					$bind[":codice_criterio"] = $criterio_valutazione;
					$bind[":codice_partecipante_1"] = $partecipante_valutazione;
					$bind[":codice_partecipante_2"] = $dati["codice_partecipante_2"];

					$check_sql  = "SELECT * FROM b_confronto_coppie ";
					$check_sql .= "WHERE codice_lotto = :codice_lotto ";
					$check_sql .= "AND codice_gara = :codice_gara ";
					$check_sql .= "AND codice_commissario = :codice_commissario ";
					$check_sql .= "AND codice_criterio = :codice_criterio ";
					$check_sql .= "AND codice_partecipante_1 = :codice_partecipante_1 ";
					$check_sql .= "AND codice_partecipante_2 = :codice_partecipante_2 ";

					$res_check = $pdo->bindAndExec($check_sql,$bind);
					$operazione_query = "INSERT";
					$codice_query = 0;
					if ($res_check->rowCount() > 0)
					{
						$rec_check = $res_check->fetch(PDO::FETCH_ASSOC);
						$operazione_query = "UPDATE";
						$codice_query = $rec_check["codice"];
					}

					$dati["codice"] = $codice_query;

					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_commissario"];
					$salva->nome_tabella = "b_confronto_coppie";
					$salva->operazione = $operazione_query;
					$salva->oggetto = $dati;

					$codice = $salva->save();

					if (!$codice)
					{
						$errore_salvataggio = true;
						$values[$key]["error"] = true;
						$codice_gara = $_POST["codice_gara"];
						$codice_lotto = $_POST["codice_lotto"];
						$partecipante_valutazione = $valutazione["codice_partecipante_1"];
						$criterio_valutazione = $_POST["codice_criterio"];
						break;
					}
				}
				else
				{
					$errore_salvataggio = true;
					$values = $_POST["valori"];
					$values[$key]["error"] = true;
					$codice_gara = $_POST["codice_gara"];
					$codice_lotto = $_POST["codice_lotto"];
					$partecipante_valutazione = $valutazione["codice_partecipante_1"];
					$criterio_valutazione = $_POST["codice_criterio"];
					break;
				}
			}
		}
		if (!$errore_salvataggio)
		{
			echo '<meta http-equiv="refresh" content="0;URL=/confrontoacoppie/confronto.php?token='.$token.'&codice='.$codice_gara.'&lotto='.$codice_lotto.'">';
			die();
		}
	}

	if(!isset($_POST) || $codice_partecipante_post != $partecipante_valutazione)
	{
		$sql_valutazione  = "SELECT * FROM b_confronto_coppie ";
		$sql_valutazione .= "WHERE codice_partecipante_1 = $partecipante_valutazione ";
		$sql_valutazione .= "AND codice_criterio = $criterio_valutazione ";
		$sql_valutazione .= "AND codice_commissario = " . $_SESSION["codice_commissario"] . " ";
		$sql_valutazione .= "AND codice_lotto = '" . $codice_lotto . "' ";
		$sql_valutazione .= "AND codice_gara = '" . $codice_gara . "' ";

		$ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);

		$k = 0;
		while ($rec_valutazione = $ris_valutazione->fetch(PDO::FETCH_ASSOC))
		{
			$values[$k] = array(
				"codice_partecipante_1" => $rec_valutazione["codice_partecipante_1"],
				"punteggio_partecipante_1" => $rec_valutazione["punteggio_partecipante_1"],
				"codice_partecipante_2" => $rec_valutazione["codice_partecipante_2"],
				"punteggio_partecipante_2" => $rec_valutazione["punteggio_partecipante_2"]);
				if ($rec_valutazione["punteggio_partecipante_1"] == 0 && $rec_valutazione["punteggio_partecipante_2"] == 0) $values[$k]["error"] = true;
				$k++;
		}
	}

	foreach ($partecipanti as $cod => $array) {
		if (in_array($partecipante_valutazione, $array))
		{
			$pos = $cod;
			break;
		}
	}

	$numero_confronti = count($partecipanti) - ($pos + 1);

	if ($errore_salvataggio) echo '<h3 class="errore">Errore di Salvataggio. Verifica i dati inseriti</h3>';
	if (in_array($pos + 1, array_keys($partecipanti)) && $pos + 1 < count($partecipanti))
	{
		$next_partecipante_valutazione = $partecipanti[$pos + 1][0];
		$next_criterio_valutazione = $criterio_valutazione;
	}
	else
	{
		echo '<meta http-equiv="refresh" content="0;URL=/confrontoacoppie/confronto.php?token='.$token.'&codice='.$codice_gara.'&lotto='.$codice_lotto.'">';
		die();
	}
	?>
	<style type="text/css">
		th {
			color: #000000 !important;
			text-align: left;
		}
		td {
			text-align: left;
		}
	</style>
	<form action="confronto.php?token=<?= $token ?>&codice=<?= $codice_gara ?>&lotto=<?= $codice_lotto ?>&partecipante=<?= $partecipante_valutazione ?>&criterio=<?= $criterio_valutazione ?>" method="POST" role="form" target="_self">
		<input type="hidden" name="codice_lotto" id="inputLotto" class="form-control" value="<?= $codice_lotto ?>">
		<input type="hidden" name="codice_gara" id="inputGara" class="form-control" value="<?= $codice_gara ?>">
		<input type="hidden" name="codice_commissario" id="inputCommissario" class="form-control" value="<?= $_SESSION["codice_commissario"] ?>">
		<input type="hidden" name="codice_criterio" id="inputCriterio" class="form-control" value="<?= $criterio_valutazione ?>">
		<table width="100%">
			<thead>
				<tr>
					<td colspan="4">EVT
					<?
					foreach ($criteri as $indice => $codice) {
						if (!is_array($codice))
						{
							if ($codice == $criterio_valutazione) echo $indice;
						}
						else
						{
							foreach ($codice as $sub_indice => $sub_codice) {
								if ($sub_codice == $criterio_valutazione) echo $indice.".".$sub_indice;
							}
						}
					}
					?>
					</td>
				</tr>
				<tr>
					<td>PARTECIPANTE</td>
					<td>PUNTEGGIO</td>
					<td>PARTECIPANTE</td>
					<td>PUNTEGGIO</td>
				</tr>
			</thead>
			<tbody>
				<?
				$j = 0;
				for($i=$pos + 1;$i<count($partecipanti);$i++)
				{
					?>
					<tr>
						<th><?= $partecipanti[$pos][1] . " - " . $partecipanti[$pos][2] ?></th>
						<td>
							<input type="hidden" name="valori[<?= $j ?>][codice_partecipante_1]" class="form-control" value="<?= $partecipanti[$pos][0] ?>">
							<input type="text" name="valori[<?= $j ?>][punteggio_partecipante_1]" class="form-control <? if (isset($values[$j]["error"])) echo "ui-state-error"; ?>" value="<? if (isset($values[$j]["punteggio_partecipante_1"])) echo $values[$j]["punteggio_partecipante_1"]; ?>" title="Punteggio Partecipante" rel="N;1;1;N;6;<=">
						</td>
						<th style="text-alig:left"><?= $partecipanti[$i][1] . " - " . $partecipanti[$i][2] ?></th>
						<td>
							<input type="hidden" name="valori[<?= $j ?>][codice_partecipante_2]" class="form-control" value="<?= $partecipanti[$i][0] ?>">
							<input type="text" name="valori[<?= $j ?>][punteggio_partecipante_2]" class="form-control <? if (isset($values[$j]["error"])) echo "ui-state-error"; ?>" value="<? if (isset($values[$j]["punteggio_partecipante_2"])) echo $values[$j]["punteggio_partecipante_2"]; ?>" title="Punteggio Partecipante" rel="N;1;1;N;6;<=">
						</td>
					</tr>
					<?
					$j++;
				}
				?>
			</tbody>
		</table>
		<button type="submit"  class="submit_big" style="cursor:pointer">SALVA VALUTAZIONE</button>
		<a class="submit_big" style="background-color:#999;" href="/confrontoacoppie/confronto.php?token=<?= $token ?>&codice=<?= $codice_gara ?>&lotto=<?= $codice_lotto ?>">ANNULLA</a>
	</form>
