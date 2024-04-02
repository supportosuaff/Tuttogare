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
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
				?>
				<h1>ANNULLA GARA</h1>
				<form name="box" method="post" action="save.php" rel="validate">
					<input type="hidden" name="codice" value="<? echo $record["codice"]; ?>">
					<table width="100%">
						<tr>
							<td class="etichetta">Numero atto di annullamento</td>
							<td><input type="text" style="font-weight:bold" name="numero_annullamento"  title="Numero atto" rel="S;1;100;A" id="numero_annullamento" size="12" value="<? echo $record["numero_annullamento"] ?>"></td>
							<td class="etichetta">Data atto di annullamento</td>
							<td><input type="text" style="font-weight:bold" name="data_annullamento"  title="Data atto" rel="S;10;10;D;<? echo date("d/m/Y H:i") ?>;<=" size="12" class="datepick" id="data_annullamento>" value="<? echo mysql2date($record["data_annullamento"]) ?>"></td>
						</tr>
					</table>
					<input type="submit" class="submit_big" style="background-color:#FF3300;" value="Annulla">
				</form>
				<? include($root."/concorsi/ritorna.php"); ?>
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
