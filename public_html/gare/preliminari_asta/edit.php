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

	if ($risultato->rowCount() > 0) {
		$record = $risultato->fetch(PDO::FETCH_ASSOC);
		$_SESSION["gara"] = $record;
	?>
	<h1>PRELIMINARI ASTA ELETTRONICA</h1>

	<? if (!$lock) { ?>
		<form name="box" method="post" action="save.php" rel="validate">
			<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
			<div class="comandi">
				<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
			</div>
							<? } ?>
							<?
							$bind = array();
							$bind[":codice"] = $record["codice"];
							$strsql = "SELECT * FROM b_lotti WHERE codice_gara = :codice";
							$ris_lotti = $pdo->bindAndExec($strsql,$bind);
							$lotti = array();
							if ($ris_lotti->rowCount() > 0) {
								while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
									$lotti[] = array("codice"=>$lotto["codice"],"oggetto"=>$lotto["oggetto"]);
								}
							} else {
								$lotti[] = array("codice"=>0,"oggetto"=>"");
							}
							?>
							<table width="100%">
								<?
							foreach($lotti as $lotto) {
								$bind = array();
								$bind[":codice"] = $record["codice"];
								$bind[":lotto"] =$lotto["codice"];
									if ($lotto["oggetto"]!="") echo "<tr><td class='etichetta' colspan='6'>" . $lotto["oggetto"] . "</td></tr>";
									$strsql = "SELECT * FROM b_aste WHERE codice_gara = :codice AND codice_lotto = :lotto";
									$ris_asta = $pdo->bindAndExec($strsql,$bind);
									if ($ris_asta->rowCount()>0) {
										$asta = $ris_asta->fetch(PDO::FETCH_ASSOC);
									} else {
						        $asta = get_campi("b_aste");
										$asta["tempo_base"] = "60";
									}
									?>
										<tr>
											<td class="etichetta">Tempo base in minuti</td>
											<td>
												<input type="hidden" name="asta[<? echo $lotto["codice"] ?>][codice_lotto]" id="codice_lotto_<? echo $lotto["codice"] ?>" value="<? echo $lotto["codice"] ?>">
												<input title="Tempo base" type="text" name="asta[<? echo $lotto["codice"] ?>][tempo_base]" id="tempo_base_<? echo $lotto["codice"] ?>" rel="S;2;0;N;5;>=" value="<? echo $asta["tempo_base"] ?>">
											</td>
											<td class="etichetta">Rilancio minimo in punti %</td>
											<td>
												<input title="Rilancio minimo" type="text" name="asta[<? echo $lotto["codice"] ?>][rilancio_minimo]" id="rilancio_minimo_<? echo $lotto["codice"] ?>" rel="S;1;0;N;100;<" value="<? echo $asta["rilancio_minimo"] ?>">
											</td>
											<td class="etichetta">Visualizzazione offerte</td>
											<td>
												<select title="Visualizzazione offerte" name="asta[<? echo $lotto["codice"] ?>][visualizzazione]" id="visualizzazione_<? echo $lotto["codice"] ?>" rel="S;1;1;N">
													<option value="">Seleziona...</option>
													<option value="0">Al buio</option>
													<option value="1">Visibile</option>
													<script>
														$('#visualizzazione_<? echo $lotto["codice"] ?>').val('<? echo $asta["visualizzazione"] ?>');
													</script>
												</select>
											</td>
										</tr>
								<?
							}
							?>
							</table>
					<? if (!$lock) { ?>
						<input type="submit" class="submit_big" value="Salva">
					</form>
					<? } ?>

					<? include($root."/gare/ritorna.php"); ?>
					<script>
						<? if ($lock) { ?>
							$(":input").not('.espandi').not('.ritorna_button').prop("disabled", true);
						<? } ?>
						</script>
						<?
			} else {
				echo "<h1>Gara non trovata</h1>";
			}

					?>


					<?
					include_once($root."/layout/bottom.php");
					?>
