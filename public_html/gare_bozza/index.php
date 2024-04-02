<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	if (!is_operatore()) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
	} else {
	if (isset($_SESSION["ente"])) {
		$bind = array();

		$bind=array();
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$bind[":codice_utente"] = $_SESSION["codice_utente"];

		$strsql  = "SELECT b_gare.*, b_tipologie.tipologia AS tipologia, b_criteri.criterio AS criterio, b_procedure.nome AS procedura ";
		$strsql .= "FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice ";
		$strsql .= "JOIN b_criteri ON b_gare.criterio = b_criteri.codice ";
		$strsql .= "JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice ";
		$strsql .= "JOIN r_partecipanti ON b_gare.codice = r_partecipanti.codice_gara ";
		$strsql .= "WHERE b_gare.annullata = 'N' AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
		$strsql .= "AND r_partecipanti.codice_utente = :codice_utente AND r_partecipanti.conferma = FALSE AND r_partecipanti.conferma IS NOT NULL ";
		$strsql .= "AND b_gare.pubblica > 0 ";
		$strsql .= "GROUP BY b_gare.codice ORDER BY cast(id as INT) DESC, codice DESC" ;
		$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

	if ($risultato->rowCount()>0) {
	?>
    <h1><?= traduci("Bozze") ?></h1>
    <table id="pagine" width="100%" id="gare" class="elenco">
    	<thead>
        	<tr><td>ID</td><td><?= traduci("Tipologia") ?></td><td><?= traduci("Criterio") ?></td><td><?= traduci("Procedura") ?></td><td><?= traduci("Oggetto") ?></td><td><?= traduci("Scadenza") ?></td>
            </tr>
            </thead>
            <tbody>
    <?
		while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
			$echo = false;
			if (strtotime($record["data_scadenza"]) > time()) {
				$echo = true;
			} else {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$sql_check = "SELECT * FROM r_partecipanti JOIN b_2fase ON r_partecipanti.codice_gara = b_2fase.codice_gara AND r_partecipanti.codice_lotto = b_2fase.codice_lotto ";
				$sql_check.= " WHERE r_partecipanti.codice_gara = :codice_gara AND data_fine > now() AND conferma = FALSE and conferma IS NOT NULL AND codice_utente = :codice_utente";
				$ris_check = $pdo->bindAndExec($sql_check,$bind);
				if ($ris_check->rowCount() >0)$echo = true;
			}
						$record["tipologie_gara"] = "";
if ($echo) {
	?>
    <tr id="<? echo $record["codice"] ?>">
		<td width="5%"><? echo $record["id"] ?></td>
        <td><? echo $record["tipologia"] ?></td>
        <td><? echo $record["criterio"] ?></td>
        <td><? echo $record["procedura"] ?></td>
        <td width="75%"><a href="/gare/id<? echo $record["codice"] ?>-dettaglio" title="Dettagli gara"><? echo $record["oggetto"] ?></a></td>
        <td><? echo mysql2datetime($record["data_scadenza"]) ?></td>
     </tr>
        <?
		}
}
	?>
    	</tbody>
    </table>
    <div class="clear"></div>

<?php
		} else { ?>
			<h1 style="text-align:center"><?= traduci("Nessun risultato") ?></h1>
  <? }
	}
	}
	include_once($root."/layout/bottom.php");
	?>
