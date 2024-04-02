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
	<h1>Sopralluoghi</h1>

<?
		$bind = array();
		$bind[":codice"] = $_GET["codice"];
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

		$strsql  = "SELECT * FROM b_sopralluoghi 
								WHERE codice_ente = :codice_ente AND codice_gara = :codice
								ORDER BY codice DESC " ;

	$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

	if ($risultato->rowCount()>0) {
		?>
        <table class="elenco" style="width:100%">
        <thead><tr>
					<td></td>
					<td>#</td>
					<td>Oggetto</td>
					<td>Inserimento richiesta</td>
					<td>Appuntamento</td>
					<td></td>
				</tr>
        <tbody>
        <?
				$i = $risultato->rowCount();
				while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
					$codice			= $record["codice"];
					$testo			= strip_tags($record["note"]);
					$colore =  (empty($record["appuntamento"])) ? "#C00" : "#3C0";
					?>
					<tr id="<? echo $codice ?>">
						<td width="10" id="flag_<? echo $codice ?>" style="background-color: <? echo $colore ?>"></td>
						<td style="text-align:center" width="10"><?= $i ?></td>
						<td>
							<? echo $testo; ?>
						</td>
						<td width="120"><?= mysql2datetime($record["timestamp_richiesta"]) ?></td>
						<td width="120"><?= mysql2datetime($record["appuntamento"]) ?></td>
						<td width="10"><a href="/gare/sopralluogo/edit.php?codice=<? echo $codice ?>&codice_gara=<? echo $_GET["codice"] ?>" class="btn-round btn-warning" title="Modifica"><span class="fa fa-pencil"></span></a></td>
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
