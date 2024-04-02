<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
				if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
				if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
					$codice_fase = getFaseConcorso($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
					if ($codice_fase!==false) {
						$esito = check_permessi_concorso($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
						$edit = $esito["permesso"];
						$lock = $esito["lock"];
					}
					if (!$edit) {
						echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
						die();
					}
				} else {
					echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
					die();
				}
				$codice = $_GET["codice"];
				$bind = array();
				$bind[":codice"]=$codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql = "SELECT * FROM b_concorsi WHERE codice = :codice ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()>0) {
?>
				<h1>LOG OPERAZIONI</h1>
				<?
				$bind = array();
				$bind[":codice"]=$codice;
				$strsql  = "SELECT b_log_concorsi.*, b_utenti.cognome, b_utenti.nome FROM b_log_concorsi JOIN b_utenti ON b_log_concorsi.utente_modifica = b_utenti.codice WHERE b_log_concorsi.codice_gara = :codice ";
				$strsql .= " ORDER BY timestamp DESC" ;
				$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
				if ($risultato->rowCount() > 0) {

					$sql_aperture  = "SELECT b_log_aperture_concorsi.*, r_partecipanti_concorsi.identificativo, b_fasi_concorsi_buste.nome AS busta, b_utenti.cognome, b_utenti.nome
														FROM b_log_aperture_concorsi
														JOIN b_fasi_concorsi_buste ON b_log_aperture_concorsi.codice_busta = b_fasi_concorsi_buste.codice
														JOIN r_partecipanti_concorsi ON b_log_aperture_concorsi.codice_partecipante = r_partecipanti_concorsi.codice
														JOIN b_utenti ON b_log_aperture_concorsi.utente_modifica = b_utenti.codice
														WHERE b_log_aperture_concorsi.codice_gara = :codice
														ORDER BY timestamp DESC" ;
					$risultato_aperture  = $pdo->bindAndExec($sql_aperture,$bind); //invia la query contenuta in $strsql al database apero e connesso


				?>
                <div id="tabs">
					<ul>
                    	<li><a href="#generale">Generale</a></li>
						<? if ($risultato_aperture->rowCount() > 0) {  ?> <li><a href="#aperture">Apertura buste</a></li> <? } ?>
                    </ul>
                    <div id="generale">
                   <table style="text-align:center; width:100%; font-size:0.8em" id="utenti" class="elenco">
        <thead>
        <tr><th width="150">Utente</th><th>Operazione</th><th width="200">Data</th></tr>
        </thead>
        <tbody>
       	<?

		while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
			$nominativo		= $record["cognome"] . " " . $record["nome"];
			$data			= mysql2completedate($record["timestamp"]);

			switch ($record["operazione"]) {
				case "INSERT": $record["operazione"] = "CREAZIONE"; break;
				case "UPDATE": $record["operazione"] = "AGGIORNAMENTO"; break;
				case "DELETE": $record["operazione"] = "ELIMINAZIONE"; break;
			}
			?>
			<tr>
            	<td style="text-align:left"><? echo $nominativo ?></td>
                <td><strong><? echo $record["operazione"] . "</strong> " . $record["oggetto"] ?></td>
                <td><? echo $data ?></td>
            </tr>

        <?

		}

		?>
        </tbody>
         </table>
         </div>
         <? if ($risultato_aperture->rowCount() > 0) {  ?>
         <div id="aperture">
                            <table style="text-align:center; width:100%; font-size:0.8em" id="utenti" class="elenco">
        <thead>
        <tr><th width="150">Utente</th><th>Esito</th><th>Busta</th><th>Partecipante</th><th width="200">Data</th></tr>
        </thead>
        <tbody>
       	<?

		while ($record = $risultato_aperture->fetch(PDO::FETCH_ASSOC)) {
			$nominativo		= $record["cognome"] . " " . $record["nome"];
			$data			= mysql2completedate($record["timestamp"]);
			$colore = "#C00";
			if ($record["esito"] == "Positivo") $colore = "#0C0";
			?>
			<tr>
            	<td style="text-align:left"><? echo $nominativo ?></td>
                <td><strong style="color:<? echo $colore ?>"><? echo $record["esito"] ?></strong></td>
                <td><? echo $record["busta"] ?></td>
                <td><? echo $record["identificativo"] ?></td>
                <td><? echo $data ?></td>
            </tr>

        <?

		}

		?>
        </tbody>
         </table>

         </div>
                     <?
		 } ?>
		</div>
<form action="allega.php" method="POST">
	<input type="hidden" name="codice" value="<?= $codice ?>">
	<input type="submit" class="submit_big" value="Allega">
</form><script>
		 $("#tabs").tabs();
		 </script>
         <?
				}
				 include($root."/concorsi/ritorna.php");
			} else {

				echo "<h1>Concorso non trovato</h1>";

				}
			} else {

				echo "<h1>Concorso non trovato</h1>";

				}

	?>


<?
	include_once($root."/layout/bottom.php");
	?>
