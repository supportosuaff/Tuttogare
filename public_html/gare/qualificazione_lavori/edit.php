<?
include_once("../../../config.php");
include_once($root."/layout/top.php");
$edit = false;
$lock = true;
if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
	if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		if ($_GET["codice"] == 0) {
			$edit = check_permessi("gare",$_SESSION["codice_utente"]);
		} else {
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
	<h1>QUALIFICAZIONE LAVORI</h1>

	<? if (!$lock) { ?>
		<form name="box" method="post" action="save.php" rel="validate">
			<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
			<div class="comandi">
				<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
			</div>
				<script>
					function check_importi() {
						$(".totale_importi").each(function() {
							importo = $(this).attr('id');
							totale = 0;
							$("."+importo).each(function() {
									valida($(this));
									totale = totale + +(parseFloat($(this).val()));
							})
							$(this).val(number_format(totale,2,".",""));
							$("#totale_importi_"+importo).html(number_format(totale,2,",","."));
						})
					}
				</script>
							<? } ?>

							<?
							$bind = array();
							$bind[":codice"]=$record["codice"];
							$strsql = "SELECT * FROM b_lotti WHERE codice_gara = :codice";
							$ris_lotti = $pdo->bindAndExec($strsql,$bind);
							$lotti_soa = array();
							if ($ris_lotti->rowCount() > 0 && $record["modalita_lotti"] != 2) {
								while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
									$lotti_soa[] = array("codice"=>$lotto["codice"],"oggetto"=>$lotto["oggetto"],"importo_base"=>$lotto["importo_base"]+$lotto["importo_oneri_no_ribasso"]); // +$lotto["importo_oneri_ribasso"] + $lotto["importo_personale"]
								}
							} else {
								$sql = "SELECT SUM(importo_base + importo_oneri_no_ribasso) AS importo_base FROM b_importi_gara "; // + importo_personale  + importo_oneri_ribasso
								$sql .= "WHERE (codice_tipologia = \"22\" OR codice_tipologia = \"23\" OR codice_tipologia = \"25\") AND codice_gara = :codice GROUP BY codice_gara ";
								$ris_importi = $pdo->bindAndExec($sql,$bind);
								if ($ris_importi->rowCount()>0) {
									$importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
									$lotti_soa[] = array("codice"=>0,"oggetto"=>"","importo_base"=>$importi["importo_base"]);
								}
							}
							foreach($lotti_soa as $lotto) {
								?>
								<?	if ($lotto["oggetto"]!="") echo "<h2>" . $lotto["oggetto"] . "</h2>"; ?>
								<input type="hidden" id="lotto_<?= $lotto["codice"] ?>" class="totale_importi" title="Somma importi" value="" rel="S;0;0;N;<?= number_format($lotto["importo_base"], 2, '.', ''); ?>">
								<table width="100%" id="qualificazione_lavori">
									<thead>
										<tr><th>Categoria</th><th>Importo</th><th>Elimina</th></tr>
									</thead>
									<tbody id="qualificazione_lavori_<?= $lotto["codice"] ?>">
										<?
										$bind = array();
										$bind[":codice"]=$record["codice"];
										$bind[":lotto"]=$lotto["codice"];
										$strsql = "SELECT * FROM b_qualificazione_lavori WHERE codice_gara = :codice AND codice_lotto = :lotto ORDER by tipo";
										$ris_qualificazione = $pdo->bindAndExec($strsql,$bind);
										if ($ris_qualificazione->rowCount()>0) {
											while($qualificazione = $ris_qualificazione->fetch(PDO::FETCH_ASSOC)) {
												$id = $qualificazione["codice"];
												include("record.php");
											}
											?>
											<?
										} else {
											if (!isset($id_cont)) {
												$id_cont = 0;
											} else {
												$id_cont++;
											}
											$id = "i_".$id_cont;
											$qualificazione = get_campi("b_qualificazione_lavori");
											$qualificazione["tipo"] = "P";
											$qualificazione["codice_lotto"] = $lotto["codice"];
											$qualificazione["importo_base"] = 0;
											include("record.php");
										}
										?>
									</tbody>
									<tfoot>
										<tr>
											<td style="text-align:right">Totale</td>
											<td style="text-align:right; font-weight:bold;" id="totale_importi_lotto_<?= $lotto["codice"] ?>"></td>
											<td></td>
										</tr>
										<tr>
											<td style="text-align:right">Totale Lavori</td>
											<td style="text-align:right; font-weight:bold;"><?= number_format($lotto["importo_base"],2,",",".") ?></td>
											<td></td>
										</tr>
									</tfoot>
									</table>
									<button class="aggiungi" onClick="aggiungi('record.php','#qualificazione_lavori_<?= $lotto["codice"] ?>');return false;"><img src="/img/add.png" alt="Aggiungi criterio">Aggiungi qualificazione scorporabile</button>	<?
							}
							?>
					<? if (!$lock) { ?>
						<input type="submit" class="submit_big" value="Salva">
						<script>
						<? if (isset($id_cont)) echo "id_inserimento = " . $id_cont . ";"; ?>
							check_importi();
						</script>
					</form>
					<? } ?>

					<? include($root."/gare/ritorna.php"); ?>
					<script>
						<? if ($lock) { ?>
							$(":input").not('.espandi').prop("disabled", true);
						<? } ?>
						</script>
		<?
} else {
	echo "<h1>Gara non trovata</h1>";
}
					} else {

						echo "<h1>Gara non trovata</h1>";

					}

					?>


					<?
					include_once($root."/layout/bottom.php");
					?>
