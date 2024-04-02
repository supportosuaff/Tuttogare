<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");

	include_once("../../config.php");
	include_once($root."/layout/top.php");
	if (!is_operatore()) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
	} else {
		if (isset($_SESSION["ente"])) {
			echo "<h1>GESTIONE CONTRATTI</h1>";
			$sql = "SELECT b_contratti.* FROM b_contratti JOIN r_contratti_contraenti ON r_contratti_contraenti.codice_contratto = b_contratti.codice JOIN b_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE b_contraenti.codice_utente = :codice_utente";
			$ris = $pdo->bindAndExec($sql, array(':codice_utente' => $_SESSION["record_utente"]["codice"]));
			?>
			<table id="pagine" width="100%" id="contratti" class="elenco">
				<thead>
					<tr>
						<td>#</td>
						<td>ID Gara</td>
						<td>Oggetto</td>
						<td>Tipologia</td>
					</tr>
				</thead>
				<tbody>
					<?
					if($ris->rowCount() > 0) {
						while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
							?>
							<tr>
								<td><?= $rec["codice"] ?></td>
								<td style="text-align: center;"><?= !empty($rec["codice_gara"]) ? $rec["codice_gara"] : '/'  ?></td>
								<td><a href="pannello.php?codice=<?= $rec["codice"] ?>"><?= $rec["oggetto"] ?></a></td>
								<td><?= ucfirst($rec["tipologia"]) ?></td>
							</tr>
							<?
						}
					}
					?>
				</tbody>
			</table>
			<?
		}
	}
	include_once($root."/layout/bottom.php");
?>
