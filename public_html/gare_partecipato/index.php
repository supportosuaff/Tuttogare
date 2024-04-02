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

		$strsql  = "SELECT b_gare.*, b_tipologie.tipologia AS tipologia, b_criteri.criterio AS criterio, b_procedure.nome AS procedura ";
		$strsql .= "FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice ";
		$strsql .= "JOIN b_criteri ON b_gare.criterio = b_criteri.codice ";
		$strsql .= "JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice ";
		$strsql .= "WHERE b_gare.annullata = 'N' AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
		$strsql .= "AND b_gare.pubblica > 0 AND data_scadenza < now() ";
		$strsql .= "GROUP BY b_gare.codice ORDER BY cast(id as INT) DESC, codice DESC" ;
		$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

	if ($risultato->rowCount()>0) {
	?>
    <h1><?= traduci("Archivio gare") ?></h1>
    <table id="pagine" width="100%" id="gare" class="elenco">
    	<thead>
      	<tr>
					<td>ID</td>
					<td><?= traduci("Tipologia") ?></td>
					<td><?= traduci("Criterio") ?></td>
					<td><?= traduci("Procedura") ?></td>
					<td><?= traduci("Oggetto") ?></td>
					<td><?= traduci("Scadenza") ?></td>
        </tr>
      </thead>
            <tbody>
    <?
		while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
			$bind = array(":codice_utente"=>$_SESSION["codice_utente"],":codice_gara"=>$record["codice"]);
			$sql_check_partecipante = "SELECT * FROM r_partecipanti WHERE (conferma = TRUE OR conferma IS NULL) AND codice_gara = :codice_gara AND codice_utente = :codice_utente";
			$ris_check_partecipante = $pdo->bindAndExec($sql_check_partecipante,$bind);
			$sql_check_Ifase = "SELECT * FROM r_partecipanti_Ifase WHERE (conferma = TRUE OR conferma IS NULL) AND codice_gara = :codice_gara AND codice_utente = :codice_utente";
			$ris_check_Ifase = $pdo->bindAndExec($sql_check_Ifase,$bind);
			if ($ris_check_partecipante->rowCount() > 0 || $ris_check_Ifase->rowCount() > 0) {
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
