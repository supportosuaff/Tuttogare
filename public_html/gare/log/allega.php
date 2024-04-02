<?
	use Dompdf\Dompdf;
	use Dompdf\Options;
	include_once("../../../config.php");
	$error = true;
	$edit = false;
	$lock = true;
		if (isset($_POST["codice"])) {
			if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
				$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
				if ($codice_fase!==false) {
					$esito = check_permessi_gara($codice_fase,$_POST["codice"],$_SESSION["codice_utente"]);
					$edit = $esito["permesso"];
					$lock = $esito["lock"];
				}
				if ($edit) {
					ini_set('memory_limit', '512M');
				  ini_set('max_execution_time', 600);
					$codice = $_POST["codice"];
					$bind = array();
					$bind[":codice"]=$codice;
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
					$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
					if ($_SESSION["gerarchia"] > 0) {
						$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
						$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
					}
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount()>0) {
						$gara = $risultato->fetch(PDO::FETCH_ASSOC);
						$bind = array();
						$bind[":codice"]=$codice;
						$strsql  = "SELECT b_log_gare.*, b_utenti.cognome, b_utenti.nome FROM b_log_gare JOIN b_utenti ON b_log_gare.utente_modifica = b_utenti.codice WHERE b_log_gare.codice_gara = :codice ";
						$strsql .= " ORDER BY timestamp DESC" ;
						$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
						if ($risultato->rowCount() > 0) {
							$ifase = false;
							if ($pdo->go("SELECT codice FROM r_partecipanti_Ifase WHERE codice_gara = :codice_gara",[":codice_gara"=>$codice])->rowCount() > 0) {
								$ifase = true;
								$sql_aperture  = "SELECT b_log_aperture_IFase.*, b_utenti.cognome, b_utenti.nome, b_criteri_buste.nome AS busta, r_partecipanti_Ifase.ragione_sociale ";
								$sql_aperture .= "FROM b_log_aperture_IFase JOIN b_utenti ON b_log_aperture_IFase.utente_modifica = b_utenti.codice ";
								$sql_aperture .= "JOIN b_criteri_buste ON b_log_aperture_IFase.codice_busta = b_criteri_buste.codice ";
								$sql_aperture .= "JOIN r_partecipanti_Ifase ON b_log_aperture_IFase.codice_partecipante = r_partecipanti_Ifase.codice ";
								$sql_aperture .= "WHERE b_log_aperture_IFase.codice_gara = :codice ";
								$sql_aperture .= "ORDER BY timestamp DESC" ;
								$risultato_aperture_Ifase  = $pdo->bindAndExec($sql_aperture,$bind); //invia la query contenuta in $strsql al database apero e connesso
								$aperture["aperture_Ifase"] = $risultato_aperture_Ifase;
							}
							$sql_aperture  = "SELECT b_log_aperture.*, b_utenti.cognome, b_utenti.nome, b_criteri_buste.nome AS busta, r_partecipanti.ragione_sociale ";
							$sql_aperture .= "FROM b_log_aperture JOIN b_utenti ON b_log_aperture.utente_modifica = b_utenti.codice ";
							$sql_aperture .= "JOIN b_criteri_buste ON b_log_aperture.codice_busta = b_criteri_buste.codice ";
							$sql_aperture .= "JOIN r_partecipanti ON b_log_aperture.codice_partecipante = r_partecipanti.codice ";
							$sql_aperture .= "WHERE b_log_aperture.codice_gara = :codice ";
							$sql_aperture .= "ORDER BY timestamp DESC" ;
							$risultato_aperture  = $pdo->bindAndExec($sql_aperture,$bind); //invia la query contenuta in $strsql al database apero e connesso
							$aperture["aperture"] = $risultato_aperture;
							
							$sql_asta = "SELECT b_offerte_economiche_asta.codice, b_offerte_economiche_asta.stato,b_offerte_economiche_asta.timestamp, r_partecipanti.ragione_sociale, r_partecipanti.partita_iva, b_lotti.oggetto FROM
													 b_offerte_economiche_asta JOIN r_partecipanti ON b_offerte_economiche_asta.codice_partecipante = r_partecipanti.codice
													LEFT JOIN b_lotti ON b_offerte_economiche_asta.codice_lotto = b_lotti.codice WHERE b_offerte_economiche_asta.codice_gara = :codice
													ORDER BY b_offerte_economiche_asta.codice_lotto, b_offerte_economiche_asta.codice DESC";

							$risultato_asta = $pdo->bindAndExec($sql_asta,$bind);

						ob_start();
						?>
						<h1>Generale</h1>
						<h2><?= $gara["oggetto"] ?></h2>
						<table style="width:100%;" id="utenti" class="elenco">
			        <thead>
			        	<tr><th style="width:33%">Utente</th><th style="width:33%">Operazione</th><th style="width:33%">Data</th></tr>
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
								<? } ?>
							</tbody>
						</table>

         <? if ((isset($risultato_aperture_Ifase)) && ($risultato_aperture_Ifase->rowCount() > 0)) {  ?>
         	<h1>Apertura buste I Fase</h1>
					<table style="width:100%;" id="utenti" class="elenco">
						<thead>
							<tr><th style="width:20%">Utente</th><th style="width:20%">Esito</th><th style="width:20%">Busta</th><th style="width:20%">Partecipante</th><th style="width:20%">Data</th></tr>
						</thead>
						<tbody>
							<?
								while ($record = $risultato_aperture_Ifase->fetch(PDO::FETCH_ASSOC)) {
									$nominativo		= $record["cognome"] . " " . $record["nome"];
									$data			= mysql2completedate($record["timestamp"]);
									$colore = "#C00";
									if ($record["esito"] == "Positivo") $colore = "#0C0";
									?>
									<tr>
										<td style="text-align:left"><? echo $nominativo ?></td>
		                <td><strong style="color:<? echo $colore ?>"><? echo $record["esito"] ?></strong></td>
		                <td><? echo $record["busta"] ?></td>
		                <td><? echo $record["ragione_sociale"] ?></td>
		                <td><? echo $data ?></td>
									</tr>
						  <? } ?>
        		</tbody>
         	</table>
				<? } ?>
				<? if ($risultato_aperture->rowCount() > 0) {  ?>
         	<h1>Apertura buste</h1>
					<table style="width:100%;" id="utenti" class="elenco">
						<thead>
							<tr><th style="width:20%">Utente</th><th style="width:20%">Esito</th><th style="width:20%">Busta</th><th style="width:20%">Partecipante</th><th style="width:20%">Data</th></tr>
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
		                <td><? echo $record["ragione_sociale"] ?></td>
		                <td><? echo $data ?></td>
									</tr>
						  <? } ?>
        		</tbody>
         	</table>
				<? } ?>
		<? if ($risultato_asta->rowCount() > 0) {  ?>
			<h1>Log Asta</h1>
			<table style="width:100%;">
				<thead>
					<tr><th style="width:5%">Codice Offerta</th><th style="width:28%">Operatore Economico</th><th style="width:33%">Esito</th><th style="width:33%">Data</th></tr>
				</thead>
				<tbody>
				<?
				$lotto_attuale = "";
				while ($record = $risultato_asta->fetch(PDO::FETCH_ASSOC)) {
					if ($record["oggetto"] != $lotto_attuale) {
						?>
						<tr><td class="etichetta" colspan="4"><?= $record["oggetto"] ?></td></tr>
						<?
					}
				$operatore		= $record["partita_iva"] . " - <strong>" . $record["ragione_sociale"] . "</strong>";
				$data			= mysql2completedate($record["timestamp"]);
				$colore = "#C00";
				$esito = "Non confermata";
				if ($record["stato"] == 1) { $colore = "#0C0"; $esito = "Ultima offerta valida"; }
				if ($record["stato"] == 98) { $colore = "#FC0"; $esito = "Offerta superata"; }
				?>
				<tr>
					<td style="width:5%"><? echo "#".$record["codice"] ?></td>
					<td style="width:28%; text-align:left"><? echo $operatore ?></td>
					<td style="width:33%"><strong style="color:<? echo $colore ?>"><? echo $record["stato"] . " - " . $esito ?></strong></td>
					<td style="width:33%"><? echo $data ?></td>
				</tr>
				<? } ?>
				</tbody>
				</table>
			<? }
			$corpo = ob_get_clean();

			$html = "<html>";
			$html.= "<style>";
			$html.= "body { font-size:10px } table { width:100%; } ";
			$html.= "table td { padding:2px; border:1px solid #CCC } ";
			$html.= "table.no_border td { padding:2px; border:none; vertical-align:top;} ";
			$html.= "</style><body>";
			$html.= $corpo;
			$html.= "</body></html>";

			$percorso = $config["arch_folder"];
			$allegato["online"] = 'N';
			$allegato["codice_gara"] = (int)$_POST["codice"];
			$allegato["codice_ente"] = (int)$_SESSION["ente"]["codice"];
			$percorso .= "/".$allegato["codice_gara"];
			if (!is_dir($percorso)) mkdir($percorso,0777,true);
			$allegato["nome_file"] = $allegato["codice_gara"] . "-log-" . date('Ymd-His'). ".pdf";
			$allegato["titolo"] = "Registro di gara al " . date('Y-m-d H:i:s');

			$options = new Options();
			$options->set('defaultFont', 'Helvetica');
			$options->setIsRemoteEnabled(true);
			$dompdf = new Dompdf($options);
			$dompdf->loadHtml($html);
			$dompdf->setPaper('A4', 'portrait');
			$dompdf->set_option('defaultFont', 'Helvetica');
			$dompdf->render();
			$content = $dompdf->output();
			file_put_contents($percorso."/".$allegato["nome_file"],$content);
			
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
					if ($codice_allegato != false) {
						$error = false;
						?>
						alert("Esportazione effettuata con successo");
						window.location.reload();
						<?
					}
				}
			}
		}
	}
}
}
if ($error) {
	?>
	alert("Si è verificato un errore durante l'esportazione");
	<?
}
