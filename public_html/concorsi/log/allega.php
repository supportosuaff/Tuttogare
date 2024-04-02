<?
	use Dompdf\Dompdf;
	use Dompdf\Options;
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$error = true;
	$edit = false;
	$lock = true;
		if (isset($_POST["codice"])) {
			if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
				$codice_fase = getFaseRefererConcorso($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
				if ($codice_fase!==false) {
					$esito = check_permessi_concorso($codice_fase,$_POST["codice"],$_SESSION["codice_utente"]);
					$edit = $esito["permesso"];
					$lock = $esito["lock"];
				}
				if ($edit) {
					$codice = $_POST["codice"];
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
						$gara = $risultato->fetch(PDO::FETCH_ASSOC);
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
						                <td><? echo $record["identificativo"] ?></td>
						                <td><? echo $data ?></td>
						            </tr>
						  <? } ?>
        		</tbody>
         	</table>
				<? } ?>

			<?
			$corpo = ob_get_clean();

			$html = "<html>";
			$html.= "<style>";
			$html.= "table { width:100%; }
			body { font-size:10px; } ";
			$html.= "table td { padding:2px; border:1px solid #CCC } ";
			$html.= "table.no_border td { padding:2px; border:none; vertical-align:top;} ";
			$html.= "</style><body>";
			$html.= $corpo;
			$html.= "</body></html>";

			$percorso = $config["arch_folder"]."/concorsi/";
			$allegato["online"] = 'N';
			$allegato["codice_gara"] = $_POST["codice"];
			$allegato["codice_ente"] = $_SESSION["ente"]["codice"];
			$allegato["sezione"] = "concorsi";
			$percorso .= $allegato["codice_gara"];
			if (!is_dir($percorso)) mkdir($percorso,0777,true);
			$allegato["nome_file"] = $allegato["codice_gara"] . "-log-" . date('Ymd-His'). ".pdf";
			$allegato["titolo"] = "Registro operazioni al " . date('Y-m-d H:i:s');

			$options = new Options();
			$options->set('defaultFont', 'Helvetica');
			$options->setIsRemoteEnabled(true);
			$dompdf = new Dompdf($options);
			$dompdf->loadHtml($html);
			$dompdf->setPaper('A4', 'portrait');
			$dompdf->set_option('defaultFont', 'Helvetica');
			$dompdf->render();
			$content = $dompdf->output();
			file_put_contents($percorso."/".$allegato["nome_file"], $content);

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
