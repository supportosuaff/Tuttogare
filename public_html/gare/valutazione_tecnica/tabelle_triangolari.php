<style type="text/css">
	.partecipante {
		text-align: left;
		color: #000000 !important;
		text-align: center !important;
	}
	.pnt {
		background-color: #CCC !important;
	}
	.transparent {
		background-color: #eee !important;
	}
	.triang * {
		font-size: 90%;
	}
</style>
<?
$punteggio = array();
foreach ($criteri as $key => $codice_criterio) {
	if (is_array($codice_criterio)) {
		foreach ($codice_criterio as $key_sub => $sub_criterio) {
			$bind = array();
			$bind[":codice_criterio"] = $sub_criterio;
			$bind[":codice_lotto"] = $codice_lotto;
			$bind[":codice_gara"] = $codice_gara;
			$bind[":codice_commissario"] = $_SESSION["codice_commissario"];
			$sql_valutazione  = "SELECT * FROM `b_confronto_coppie` ";
			$sql_valutazione .= "WHERE `codice_criterio` = :codice_criterio ";
			$sql_valutazione .= "AND `codice_lotto` = :codice_lotto ";
			$sql_valutazione .= "AND `codice_gara` = :codice_gara ";
			$sql_valutazione .= "AND `codice_commissario` = :codice_commissario";

			$ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);

			while ($rec_valutazione = $ris_valutazione->fetch(PDO::FETCH_ASSOC))
			{
				$punteggio[$rec_valutazione["codice_criterio"]][$rec_valutazione["codice_partecipante_1"]][$rec_valutazione["codice_partecipante_2"]] = [$rec_valutazione["punteggio_partecipante_1"],$rec_valutazione["punteggio_partecipante_2"]];
			}

			?>
			<table width="100%">
				<thead>
					<tr>
						<th class="partecipante" align="left" colspan="<?= count($partecipanti) ?>">EVT.<?= $key ?>.<?= $key_sub ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="partecipante"></td>
						<?
						for ($i=1; $i < count($partecipanti); $i++) {
							?> <th class="partecipante" style="text-align:left;"><?= $partecipanti[$i][1] ?></th> <?
						}
						?>
					</tr>
					<?
					foreach ($partecipanti as $tmp_partecipante) {
						?>
						<tr>
							<th class="partecipante"><?= $tmp_partecipante[1] ?></th>
							<?
							echo str_repeat('<td class="transparent"></td>', array_search($tmp_partecipante, $partecipanti));
							for ($i = array_search($tmp_partecipante, $partecipanti); $i < count($partecipanti) - 1; $i++)
							{
								?>
								<td class="pnt">
									<?= $tmp_partecipante[1] ?><?= (isset($punteggio[$sub_criterio][$tmp_partecipante[0]][$partecipanti[$i + 1][0]][0]) ? $punteggio[$sub_criterio][$tmp_partecipante[0]][$partecipanti[$i + 1][0]][0] : "N/P") ?><br><?= $partecipanti[$i + 1][1] ?><?= (isset($punteggio[$sub_criterio][$tmp_partecipante[0]][$partecipanti[$i + 1][0]][1]) ? $punteggio[$sub_criterio][$tmp_partecipante[0]][$partecipanti[$i + 1][0]][1] : "N/P") ?>
								</td>
								<?
							}
							?>
						</tr>
						<?
					}
					?>
				</tbody>
			</table>
			<div class="padding"></div>
			<?
		}
	} else {
		$bind = array();
		$bind[":codice_criterio"] = $codice_criterio;
		$bind[":codice_lotto"] = $codice_lotto;
		$bind[":codice_gara"] = $codice_gara;
		$bind[":codice_commissario"] = $_SESSION["codice_commissario"];
		$sql_valutazione  = "SELECT * FROM `b_confronto_coppie` ";
		$sql_valutazione .= "WHERE `codice_criterio` = :codice_criterio ";
		$sql_valutazione .= "AND `codice_lotto` = :codice_lotto ";
		$sql_valutazione .= "AND `codice_gara` = :codice_gara ";
		$sql_valutazione .= "AND `codice_commissario` = :codice_commissario";

		$ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);

		while ($rec_valutazione = $ris_valutazione->fetch(PDO::FETCH_ASSOC)) {
			$punteggio[$rec_valutazione["codice_criterio"]][$rec_valutazione["codice_partecipante_1"]][$rec_valutazione["codice_partecipante_2"]] = [$rec_valutazione["punteggio_partecipante_1"],$rec_valutazione["punteggio_partecipante_2"]];
		}

		?>
		<table width="100%">
			<thead>
				<tr>
					<th class="partecipante" colspan="<?= count($partecipanti) ?>">EVT.<?= $key ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="partecipante"></td>
					<?
					for ($i=1; $i < count($partecipanti); $i++)
					{
						?> <th class="partecipante" ><?= $partecipanti[$i][1] ?> - <?= $partecipanti[$i][2] ?></th> <?
					}
					?>
				</tr>
				<?
				foreach ($partecipanti as $tmp_partecipante) {
					?>
					<tr class="">
						<th class="partecipante" ><?= $tmp_partecipante[1] ?> - <?= $tmp_partecipante[2] ?></th>
						<?
						echo str_repeat('<td class="transparent"></td>', array_search($tmp_partecipante, $partecipanti));
						for ($i = array_search($tmp_partecipante, $partecipanti); $i < count($partecipanti) - 1; $i++)
						{
							?>
							<td class="pnt">
								<?= $tmp_partecipante[1] ?><?= (isset($punteggio[$codice_criterio][$tmp_partecipante[0]][$partecipanti[$i + 1][0]][0]) ? $punteggio[$codice_criterio][$tmp_partecipante[0]][$partecipanti[$i + 1][0]][0] : "N/P") ?><br><?= $partecipanti[$i + 1][1] ?><?= (isset($punteggio[$codice_criterio][$tmp_partecipante[0]][$partecipanti[$i + 1][0]][1]) ? $punteggio[$codice_criterio][$tmp_partecipante[0]][$partecipanti[$i + 1][0]][1] : "N/P") ?>
							</td>
							<?
						}
						?>
					</tr>
					<?
				}
				?>
			</tbody>
		</table>
		<div class="padding"></div>
		<?
	}
}
?>
