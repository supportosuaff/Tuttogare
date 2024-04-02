<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
  if(empty($_SESSION["codice_utente"]) || !check_permessi("conservazione",$_SESSION["codice_utente"]) && ($_SESSION["gerarchia"]==="0" || $_SESSION["record_utente"]["codice_ente"] == $_SESSION["ente"]["codice"])) {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  } else {
    ?><h1>CONSERVAZIONE</h1>
    <?
    	$where = "";
    	$bind = array();
			$where = "WHERE (b_conservazione.codice_gestore = :codice_ente OR b_conservazione.codice_ente = :codice_ente)";
    	if($_SESSION["gerarchia"] > 0) {
    		$bind[":codice_ente"] = $_SESSION["record_utente"]["codice_ente"];
    	} else {
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			}
			$sql = "SELECT b_conservazione.*, COUNT(r_conservazione_file.codice) AS files, b_utenti.cognome, b_utenti.nome
							FROM b_conservazione JOIN b_enti ON b_conservazione.codice_ente = b_enti.codice
							JOIN b_utenti ON b_conservazione.utente_creazione = b_utenti.codice
							JOIN r_conservazione_file ON b_conservazione.codice = r_conservazione_file.codice_pacchetto
							{$where}
							GROUP BY b_conservazione.codice ORDER BY stato, codice DESC ";
			$ris = $pdo->bindAndExec($sql, $bind);
			if ($ris->rowCount() > 0) {
				$stati_conservazione = file_get_contents("stati.json");
				$stati_conservazione = json_decode($stati_conservazione,true);
				?>
				<table class="elenco" style="width:100%">
					<thead>
						<tr>
							<td width="2"></td>
							<td>Stato</td>
							<td>Denominazione</td>
							<td>Files</td>
							<td>Creatore</td>
							<td>Ultima operazione</td>
							<td width="10"></td>
							<td width="10"></td>
							<td width="10"></td>
						</tr>
					</thead>
					<tbody>
						<?
							while($pacchetto = $ris->fetch(PDO::FETCH_ASSOC)) {
								if (empty($pacchetto["stato"])) $pacchetto["stato"] = 0;
								$invia_pacchetto = check_permessi("conservazione/conserva",$_SESSION["codice_utente"]);
								$modulo_conservazione = true;
								include("tr_pacchetto.php");
						} ?>
					</tbody>
				</table>
				<script>
					function dettagli_conservazione(codice) {
						$.ajax({
							type: "POST",
							url: "/conservazione/check.php",
							data: {codice: codice},
							dataType: "html",
							beforeSend: function(e) {
								$('#wait').fadeIn();
							}
						}).done(function(html) {
							f_ready();
							if(html.length > 0) {
								$('#dettaglio_conservazione').html(html).dialog({
									title: 'Dettaglio conservazione #' + codice,
									modal: true,
									width: '70%'
								});
							} else {
								jalert('Si è verificato un errore. Si prega di riprovare!<br>Se il problema persiste contattare l&#39;helpdesk tecnico.');
							}
						})
						.fail(function() {
							jalert('Si è verificato un errore. Si prega di riprovare!<br>Se il problema persiste contattare l&#39;helpdesk tecnico.');
						})
						.always(function() {
							$('#wait').fadeOut();
						});

					}
				</script>
				<div id="dettaglio_conservazione" style="display:none !important;"></div>
				<?
			} else {
				?>
				<h3 style="text-align:center">
					<span class="fa fa-exclamation-triangle fa-4x"></span><br>
					Non sono presenti pacchetti.
				</h3>
				<?
			}
  }
  include_once($root."/layout/bottom.php");
?>
