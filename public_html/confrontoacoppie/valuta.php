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
				echo "<td>$key</td>";
			 }
			?>
		</tr>
	</thead>
	<tbody>
		<?
		foreach ($partecipanti as $char => $codice_partecipante) {
		 	?>
		 	<tr>
		 		<td style="text-align:center"><?= $char ?></td>
		 		<?
		 		foreach ($criteri as $key => $codice_criterio) {
					$bind = array();
					$bind[":codice_partecipante"] = $codice_partecipante;
					$bind[":codice_criterio"] = $codice_criterio;
		 			$sql_valutazioni = "SELECT * FROM b_confronto_coppie WHERE codice_partecipante_1 = :codice_partecipante AND codice_criterio = :codice_criterio";
		 			$ris_valutazioni = $pdo->bindAndExec($sql_valutazioni,$bind);
		 			$key_partecipanti = array_keys($partecipanti);
		 			$current_key = array_search($char, $key_partecipanti);
		 			$numero_confronti = count($partecipanti) - ++$current_key;
		 			$class = "red";
		 			if ($ris_valutazioni->rowCount() == $numero_confronti) $class = "green";
		 			if ($ris_valutazioni->rowCount() > 0 && $ris_valutazioni->rowCount() < $numero_confronti) $class = "orange";
		 			?>
		 			<td>
		 				<a class="confronto <?= $class ?>" href="confronto.php?codice=<?= $codice_gara ?>&lotto=<?= $codice_lotto ?>&partecipante=<?= $codice_partecipante ?>&criterio=<?= $codice_criterio ?>">
		 					<?= $key ?>
		 				</a>
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
