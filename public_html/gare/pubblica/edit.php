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

				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$bind = array();
					$bind[":codice"] = $record["procedura"];
					$sql = "SELECT * FROM b_procedure WHERE codice = :codice";
						$ris = $pdo->bindAndExec($sql,$bind);
						$directory = "default";
						if ($ris->rowCount()>0) {
							$record_procedura = $ris->fetch(PDO::FETCH_ASSOC);
							$directory = $record_procedura["directory"];
							$record["nome_procedura"] = $record_procedura["nome"];
							$record["riferimento_procedura"] = $record_procedura["riferimento_normativo"];
						}
					$string_cpv = "";
					$cpv = array();
					$bind = array();
					$bind[":codice"] = $record["codice"];
					$strsql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_gare ON b_cpv.codice = r_cpv_gare.codice WHERE r_cpv_gare.codice_gara = :codice ORDER BY codice";
					$risultato_cpv = $pdo->bindAndExec($strsql,$bind);
					if ($risultato_cpv->rowCount()>0) {
						while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
							$cpv[] = $rec_cpv["codice"];
						}
						$string_cpv = implode(";",$cpv);
					}
					$operazione = "UPDATE";
?>
				<h1>PUBBLICAZIONE GARA</h1>
				<? if (pecConfigurata()) {
					$continua = true;
					if ($record["stato"] < 3) {
						$sql = "SELECT * FROM b_modalita WHERE codice = :modalita ";
						$ris = $pdo->bindAndExec($sql,array(":modalita"=>$record["modalita"]));
						if ($ris->rowCount()>0) {
							$rec = $ris->fetch(PDO::FETCH_ASSOC);
							if ($rec["online"]=="S" && $record["check_private"] == "N") {
								$continua = false;
								$record_gara = $record;
								include($root."/gare/verifyPrivateForm.php");
							}
						}
					}
					if ($continua) {
						if (!$lock) { ?>
							<form name="box" id="pubblica" method="post" action="<? echo $directory ?>/save.php" rel="validate">
								<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
								<div class="comandi">
									<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
								</div>
						<? } ?>
						<div id="tabs">
							<? include($directory."/form.php"); ?>
							<div class="clear"></div>
							</div>
							<? if (!$lock) { ?>
							<a class="submit_big btn-warning" href="/gare/id<?= $record["codice"] ?>-dettaglio" target="_blank"><span class="fa fa-search"></span> Anteprima</a>
							<input type="submit" class="submit_big" value="Salva">
						</form>
						<? } ?>
						<? include($root."/gare/ritorna.php"); ?>
						<script>
							$("#tabs").tabs();
							<? if ($lock) { ?>
									$("#tabs :input").not('.espandi').prop("disabled", true);
							<? } ?>
						</script>
					<?
					}
				} else {
					?>
					<h2>Impossibile procedere. Configurare PEC</h2>
					<?
				}
			} else {
				echo "<h1>Gara non trovata</h1>";
			}
		} else {
			echo "<h1>Gara non trovata</h1>";
		}
	include_once($root."/layout/bottom.php");
	?>
