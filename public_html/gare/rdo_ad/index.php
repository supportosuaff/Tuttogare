<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
	if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
		if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$codice_fase = getFase($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
			if ($codice_fase!==false) {
				$esito = check_permessi_gara($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
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
		$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
		$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
		if ($_SESSION["gerarchia"] > 0) {
			$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
			$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
		}
		$risultato = $pdo->bindAndExec($strsql,$bind);
		?>
		<h1>RICHIESTE DI OFFERTA</h1>
          <?
		if ($risultato->rowCount() > 0) {
				$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
				$bind = array();
				$bind[":codice"]=$codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$sql = "SELECT * FROM b_rdo_ad WHERE codice_gara = :codice AND codice_ente = :codice_ente ORDER BY timestamp DESC";
				$ris_rdo_ad = $pdo->bindAndExec($sql,$bind);
				$continua = true;
				if ($record_gara["check_private"]=="N") {
					$continua = false;
					include($root."/gare/verifyPrivateForm.php");
				}
				if (!$lock && $continua) { ?>
				<hr>
        <a href="/gare/rdo_ad/edit.php?codice=0&codice_gara=<?=$codice ?>" title="Richiedi nuova offerta"><div class="add_new">
        <img src="/img/add.png" alt="Richiedi nuova offerta"><br>
        Richiedi nuova offerta
        </div></a>
        <hr>
				<?
				}
				if ($ris_rdo_ad->rowCount()>0) {
					?>
					<table width="100%">
						<thead>
							<tr>
								<th>Richiesta</th>
								<th>Operatori</th>
								<th width="150">Timestamp</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						<?
							while ($record = $ris_rdo_ad->fetch(PDO::FETCH_ASSOC)) {
							?>
							<tr>
								<td><?= $record["titolo"] ?></td>
								<td>
									<?
										$bind = array();
										$bind[":codice_rdo"] = $record["codice"];
										$sql = "SELECT r_partecipanti.* FROM r_partecipanti JOIN r_rdo_ad ON r_partecipanti.codice = r_rdo_ad.codice_partecipante
										WHERE r_rdo_ad.codice_rdo = :codice_rdo";
										$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
										if ($ris_partecipanti->rowCount() > 0) {
											while($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
												echo $partecipante["ragione_sociale"] . "<br>";
											}
										}
									?>
								</td>
								<td><?= mysql2datetime($record["timestamp"]) ?></td>
								<td width="10" style="text-align:center">
									<a class='btn-round btn-primary' title="Vedi dati minimi" href='/gare/rdo_ad/edit.php?codice=<? echo $record["codice"] ?>&codice_gara=<? echo $record["codice_gara"] ?>' title="Vedi"><span class="fa fa-search"></span></button></td>
							</tr>
							<?
							}
						?>
						</tbody>
					</table>
					<?
				} else {
					?>
					<h2 style="text-align:center">Nessuna offerta richiesta</h2>
					<? if (!$lock) { ?>
					<div class="box">
						<h3><a onclick="$('#form-partecipante').slideToggle('fast')" href="#">Salta fase offerta</a></h3>
						<div id="form-partecipante" style="display:none">
							Se necessario &egrave; possibile inserire direttamente i dati degli operatori economici interessati.
							<form action="salta-fase.php" method="post" rel="validate">
								<input type="hidden" name="codice" value="<? echo $codice; ?>">
								<table width="100%">
									<thead>
										<tr>
											<th class="etichetta">Data richiesta offerta</th>
											<th>
										    <input type="text" id="data_pubblicazione" name="data_pubblicazione" value="<? echo mysql2date($record_gara["data_pubblicazione"]) ?>" class="datepick" size="16" title="Data Offerta" rel="S;10;10;D">
											</th>
										</tr>
										<tr>
											<th width="120">Codice fiscale azienda*</th>
											<th width="120">identificativo Estero</th>
											<th>Ragione Sociale*</th>
											<th>PEC</th>
											<th width="10"></th>
										</tr>
									</thead>
									<tbody id="partecipanti">
										<?
											$id = 0;
											include('tr_partecipante.php');
										?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="5">
												<button type="button" class="submit_big" onClick="aggiungi('tr_partecipante.php','#partecipanti')">Aggiungi partecipante</button>
											</td>
										</tr>
									</tfoot>
								</table>
								<input type="submit" class="submit_big" value="Salva">
							</form>
						</div>
					</div>
					<?
					}
				}
				include($root."/gare/ritorna.php");
			} else {
				echo "<h1>Gara non trovata</h1>";
			}
	} else {
		echo "<h1>Gara non trovata</h1>";
	}
	include_once($root."/layout/bottom.php");
?>
