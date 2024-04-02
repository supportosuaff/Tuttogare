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
				} ?>
	<h1>Chiarimenti</h1>

<?
		$bind = array();
		$bind[":codice"] = $_GET["codice"];
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

		$strsql  = "SELECT b_quesiti.*, b_risposte.testo AS risposta
								FROM b_quesiti LEFT JOIN b_risposte ON b_quesiti.codice = b_risposte.codice_quesito
								WHERE b_quesiti.codice_ente = :codice_ente AND b_quesiti.codice_gara = :codice
								ORDER BY b_quesiti.codice DESC " ;

	$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

	if ($risultato->rowCount()>0) {
		?>
        <table class="elenco" style="width:100%">
        <thead><tr>
					<td></td>
					<td>#</td>
					<td>Oggetto</td>
					<td>Data e ora</td>
					<td></td>
					<td></td></tr>
        <tbody>
        <?
				$i = $risultato->rowCount();
		while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
			$codice			= $record["codice"];
			$attivo 		= $record["attivo"];
			$testo			= strip_tags($record["testo"]);
			$colore = "#3C0";

			if ($attivo == "N") {
				$colore =  (empty($record["risposta"])) ? "#C00" : "#F90";
			}
					?>
					<tr id="<? echo $codice ?>">
						<td width="10" id="flag_<? echo $codice ?>" style="background-color: <? echo $colore ?>"></td>
						<td style="text-align:center" width="10"><?= $i ?></td>
						<td>
							<? echo $testo; ?>
						</td>
						<td width="120"><?= mysql2datetime($record["timestamp"]) ?></td>
						<td width="10"><input type="image" onClick="window.location.href='/gare/chiarimenti/edit.php?codice=<? echo $codice ?>&codice_gara=<? echo $_GET["codice"] ?>'" src="/img/edit.png" title="Modifica"></td>
						<td width="10"><input type="image" onClick="disabilita('<? echo $codice ?>','gare/chiarimenti');" src="/img/switch.png" title="Abilita/Disabilita"></td>
					</tr>


<?php
	$i--;
	}
	?></tbody></table>
    <div class="clear"></div>
		<?
      } else {
        ?><h2 style="text-align:center">
        <span class="fa fa-exclamation-circle fa-3x"></span><br><? echo "Nessun risultato" ?>!</h2>	<?
      }
    include($root."/gare/ritorna.php");
	}
	include_once($root."/layout/bottom.php");
	?>
