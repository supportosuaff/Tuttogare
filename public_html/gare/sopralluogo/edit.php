<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
			if ($codice_fase !== false) {
				$esito = check_permessi_gara($codice_fase,$_GET["codice_gara"],$_SESSION["codice_utente"]);
				$edit = $esito["permesso"];
				$lock = $esito["lock"];
			}
			if (!$edit) {
				die();
			}
		} else {
			die();
		}

	if (isset($_GET["codice"]) && isset($_GET["codice_gara"])) {

				$codice = $_GET["codice"];

				$bind = array();
				$bind[":codice"] = $codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$bind[":codice_gara"] = $_GET["codice_gara"];

				$strsql = "SELECT b_sopralluoghi.*, b_operatori_economici.ragione_sociale
									 FROM b_sopralluoghi JOIN b_operatori_economici ON b_sopralluoghi.codice_utente = b_operatori_economici.codice_utente
									 WHERE b_sopralluoghi.codice = :codice AND b_sopralluoghi.codice_ente = :codice_ente AND b_sopralluoghi.codice_gara = :codice_gara";

				$risultato = $pdo->bindAndExec($strsql,$bind);

				if ($risultato->rowCount() > 0) {
					$rec = $risultato->fetch(PDO::FETCH_ASSOC);
			?>
				<div class="clear"></div>
				<form name="box" method="post" action="save.php" rel="validate">
					<input type="hidden" name="codice" value="<? echo $rec["codice"]; ?>">
					<input type="hidden" name="operazione" value="<? echo $operazione ?>">
					<input type="hidden" name="codice_gara" value="<? echo $_GET["codice_gara"] ?>">
					<h1>RICHIESTA SOPRALLUOGO</h1>
					<div class="box">
						<h2><b><u>Note richiedente:</u></b></h2>
						<div style="padding:5px">
							<? echo $rec["note"] ?>
						</div>
					</div>
					<div style="text-align:right"><strong><?= $rec["ragione_sociale"] ?> - <?= mysql2datetime($rec["timestamp_richiesta"]) ?></strong>
					</div>
					<br><br>
					<h2>Risposta</h2>
					<div class="box">
						<table width="100%">
							<tr>
								<td class="etichetta">Appuntamento:</td>
								<td><input type="text" inline="true" class="datetimepick" title="Appuntamento"  name="appuntamento" id="appuntamento" value="<? echo mysql2datetime($rec["appuntamento"]); ?>" rel="S;16;16;DT"></td>
							</tr>
						</table>
						<textarea rows='10' class="ckeditor_full" name="note_risposta" cols='80' id="note_risposta" title="Risposta" rel="S;3;0;A"><? echo $rec["note_risposta"]; ?></textarea><br>
					</div>
					<h2>Note interne</h2>
					<textarea rows='10' class="ckeditor_full" name="note_interne" cols='80' id="note_interne" title="Note interne" rel="N;3;0;A"><? echo $rec["note_interne"]; ?></textarea>
					<input type="submit" class="submit_big" style="background-color:#0C0"value="Rispondi">
				</form>
				<div class="clear"></div>
    	<?

			} else {

				echo "<h1>Quesito non trovato</h1>";

				}
			} else {

				echo "<h1>Quesito non trovato</h1>";

				}

	?>


<?
	include_once($root."/layout/bottom.php");
	?>
