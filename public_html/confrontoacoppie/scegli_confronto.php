<style type="text/css">
	.confronto {
		width: 100%;
		height: 100%;
		min-height: 17px;
		cursor: pointer;
		text-decoration: none;
		color: #000000;
		padding: 3px;
	}
	.red {
		background-color: #ff3333;
	}
	.red:hover {
		background-color: #ff6666;
		color: #000000;
	}
	.orange {
		background-color: #ff6633;
	}
	.orange:hover {
		background-color: #ff9933;
		color: #000000;
	}
	.green {
		background-color: #33cc33;
	}
	.green:hover {
		background-color: #66ff99;
		color: #000000;
	}
</style>
<table id="confronti" width="100%">
	<thead>
		<tr>
			<td width="20" style="text-align:center">#</td>
			<?
			foreach ($criteri as $key => $value) {
				if (is_array($value))
				{
					foreach ($value as $key_sub => $sub_value)
					{
						echo "<td>$key.$key_sub</td>";
					}
				}
				else
				{
					echo "<td>$key</td>";
				}
			 }
			?>
		</tr>
	</thead>
	<tbody>
		<?
		foreach ($partecipanti as $pos => $codice_partecipante) {
		 	?>
		 	<tr>
		 		<td style="text-align:center"><?= $codice_partecipante[1] ?></td>
		 		<?
		 		foreach ($criteri as $key => $codice_criterio) {
		 			if (is_array($codice_criterio))
		 			{
		 				foreach ($codice_criterio as $key_sub => $sub_criterio) {
							$bind = array();
							$bind[":codice_partecipante"] = $codice_partecipante[0];
							$bind[":codice_criterio"] = $sub_criterio;
							$bind[":codice_commissario"] = $_SESSION["codice_commissario"];
		 					$sql_valutazioni = "SELECT * FROM b_confronto_coppie WHERE codice_partecipante_1 = :codice_partecipante AND codice_criterio = :codice_criterio AND codice_commissario = :codice_commissario";
		 					$ris_valutazioni = $pdo->bindAndExec($sql_valutazioni,$bind);
				 			$numero_confronti = count($partecipanti) - ($pos + 1);
				 			$class = "red";
				 			if ($ris_valutazioni->rowCount() == $numero_confronti)
				 			{
				 				$class = "green";
				 				while ($rec_valutazioni = $ris_valutazioni->fetch(PDO::FETCH_ASSOC))
				 				{
				 					if ($rec_valutazioni["punteggio_partecipante_1"] == 0 && $rec_valutazioni["punteggio_partecipante_2"] == 0) { $class = "orange"; break; }
				 				}
				 			}
				 			if ($ris_valutazioni->rowCount() > 0 && $ris_valutazioni->rowCount() < $numero_confronti) $class = "orange";
				 			?>
				 			<td>
				 				<a class="confronto <?= $class ?>" href="confronto.php?token=<?= $token ?>&codice=<?= $codice_gara ?>&lotto=<?= $codice_lotto ?>&partecipante=<?= $codice_partecipante[0] ?>&criterio=<?= $sub_criterio ?>">
				 					<?= "EVT.".$key.".".$key_sub ?>
				 				</a>
				 			</td>
				 			<?
		 				}
		 			}
		 			else
		 			{
						$bind = array();
						$bind[":codice_partecipante"] = $codice_partecipante[0];
						$bind[":codice_criterio"] = $codice_criterio;
						$bind[":codice_commissario"] = $_SESSION["codice_commissario"];

		 				$sql_valutazioni = "SELECT * FROM b_confronto_coppie WHERE codice_partecipante_1 = :codice_partecipante AND codice_criterio = :codice_criterio AND codice_commissario = :codice_commissario";
			 			$ris_valutazioni = $pdo->bindAndExec($sql_valutazioni,$bind);

			 			$numero_confronti = count($partecipanti) - ($pos + 1);
			 			$class = "red";
			 			if ($ris_valutazioni->rowCount() == $numero_confronti)
			 			{
			 				$class = "green";
			 				while ($rec_valutazioni = $ris_valutazioni->fetch(PDO::FETCH_ASSOC))
			 				{
			 					if ($rec_valutazioni["punteggio_partecipante_1"] == 0 && $rec_valutazioni["punteggio_partecipante_2"] == 0) $class = "orange";
			 				}
			 			}
			 			if ($ris_valutazioni->rowCount() > 0 && $ris_valutazioni->rowCount() < $numero_confronti) $class = "orange";
			 			?>
			 			<td>
			 				<a class="confronto <?= $class ?>" href="confronto.php?token=<?= $token ?>&codice=<?= $codice_gara ?>&lotto=<?= $codice_lotto ?>&partecipante=<?= $codice_partecipante[0] ?>&criterio=<?= $codice_criterio ?>">
			 					<?= "EVT.".$key ?>
			 				</a>
			 			</td>
			 			<?
		 			}
		 		}
		 		?>
		 	</tr>
		 	<?
		 }
		?>
	</tbody>
</table>
