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

		$strsql = "SELECT * FROM b_concorsi WHERE codice = :codice
							 AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";

		if ($_SESSION["gerarchia"] > 0) {
			$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
			$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
		}

		$risultato = $pdo->bindAndExec($strsql,$bind);

		$continua = true;
		if ($risultato->rowCount() > 0) {
			$record = $risultato->fetch(PDO::FETCH_ASSOC);
			if(empty($record["check_private"]) || $record["check_private"] == 'N') {
				$continua = false;
				$record_concorso = $record;
				include("{$root}/concorsi/verifyPrivateForm.php");
			}
			$operazione = "UPDATE";
			if($continua) {
				?>
				<h1>PUBBLICAZIONE GARA</h1>
				<? if (!$lock) { ?>
					<form name="box" id="pubblica" method="post" action="default/save.php" rel="validate">
						<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
						<div class="comandi">
							<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
						</div>
				<? } ?>
				<div id="tabs">
					<? include("default/form.php"); ?>
					<div class="clear"></div>
				</div>
				<?
				if (!$lock) { ?><input type="submit" class="submit_big" value="Salva"></form><? }
				include($root."/concorsi/ritorna.php");
				?>
				<script>
					$("#tabs").tabs();
					<? if ($lock) { ?>$("#tabs :input").not('.espandi').prop("disabled", true);<? } ?>
				</script>
				<?
			}
		} else {
			echo "<h1>Concorso non trovato</h1>";
		}
	} else {
		echo "<h1>Concorso non trovato</h1>";
	}
	include_once($root."/layout/bottom.php");
?>
