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
		<h1>QUALIFICAZIONE PROGETTAZIONE</h1>

		<? if (!$lock) { ?>
		<form name="box" method="post" action="save.php" rel="validate">
			<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
			<div class="comandi">
				<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
			</div>
			<script>
				function check_importi() {
					$(".totale_importi").each(function() {
						totale = 0;
						$(".importo").each(function() {
							valida($(this));
							totale = totale + +(parseFloat($(this).val()));
						})
						$(this).val(number_format(totale,2,".",""));
						$("#totale_importi").html(number_format(totale,2,",","."));
					})
				}
			</script>
			<? }
				$bind = array();
				$bind[":codice"]=$record["codice"];
				$sql = "SELECT SUM(importo_base + importo_oneri_no_ribasso) AS importo_base FROM b_importi_gara "; // + importo_personale + importo_oneri_ribasso
				$sql .= "WHERE (codice_tipologia = \"24\" OR codice_tipologia = \"26\" OR codice_tipologia = \"21\" OR codice_tipologia = \"27\") AND codice_gara = :codice GROUP BY codice_gara ";
				$ris_importi = $pdo->bindAndExec($sql,$bind);
				if ($ris_importi->rowCount()>0) {
					$importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
					$totale_importo = $importi["importo_base"];
				}
			?>
				<input type="hidden" class="totale_importi" title="Somma importi" value="" rel="S;0;0;N">
				<table width="100%" id="qualificazione_progettazione">
					<thead>
						<tr><th>Categoria</th><th>Importo</th><th>Elimina</th></tr>
					</thead>
					<tbody id="qualificazione_progettazione">
						<?
						$strsql = "SELECT * FROM b_qualificazione_progettazione WHERE codice_gara = :codice ORDER by codice";
						$ris_qualificazione = $pdo->bindAndExec($strsql,$bind);
						if ($ris_qualificazione->rowCount()>0) {
							while($qualificazione = $ris_qualificazione->fetch(PDO::FETCH_ASSOC)) {
								$id = $qualificazione["codice"];
								include("record.php");
							}
						} else {
							$id = "i_0";
							$qualificazione = get_campi("b_qualificazione_progettazione");
							$qualificazione["importo"] = 0;
							include("record.php");
						}
						?>
						<tfoot>
							<tr>
								<td style="text-align:right">Totale</td>
								<td style="text-align:right; font-weight:bold;" id="totale_importi"></td>
								<td></td>
							</tr>
							<tr>
								<td style="text-align:right">Totale Progettazione</td>
								<td style="text-align:right; font-weight:bold;"><?=number_format($totale_importo, 2, ",", ".")?></td>
								<td></td>
							</tr>
						</tfoot>
					</table>
					<button class="aggiungi" onClick="aggiungi('record.php','#qualificazione_progettazione');return false;"><img src="/img/add.png" alt="Aggiungi criterio">Aggiungi qualificazione</button>	<?
				?>

				<? if (!$lock) { ?>
				<input type="submit" class="submit_big" value="Salva">
				<script>
					check_importi();
				</script>
			</form>
			<? } ?>
			<script>
				<? if ($lock) { ?>
					$(":input").not('.espandi').prop("disabled", true);
					<? } ?>
				</script>
				<?
			} else {
				echo "<h1>Gara non trovata</h1>";
			}
			include($root."/gare/ritorna.php");
		} else {

			echo "<h1>Gara non trovata</h1>";

		}
		?>


		<?
		include_once($root."/layout/bottom.php");
		?>
