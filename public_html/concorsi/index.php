<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("concorsi",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
?>
<h1>Gestione concorsi di progettazione</h1>
<?php


	if ($edit) {
		?>
        <a href="/concorsi/id0-edit" title="Inserisci nuovo concorso"><div class="add_new">
        <span class="fa fa-plus-circle fa-3x"></span><br>
        Aggiungi nuovo concorso
        </div></a>
        <?

				$bind = array();
				$strsql  = "SELECT b_concorsi.*, b_enti.denominazione, b_conf_stati_concorsi.titolo AS fase, b_conf_stati_concorsi.colore ";
				$strsql .= "FROM b_concorsi JOIN b_conf_stati_concorsi ON b_concorsi.stato = b_conf_stati_concorsi.fase ";
				$strsql .= "JOIN b_enti ON b_concorsi.codice_ente = b_enti.codice ";
				if ($_SESSION["gerarchia"] > 1) $strsql .= "JOIN b_permessi_concorsi ON b_permessi_concorsi.codice_gara = b_concorsi.codice ";
				$strsql .= "WHERE codice_gestore = :codice_ente ";
				if ($_SESSION["gerarchia"] > 0 && $_SESSION["ente"]["codice"] != $_SESSION["record_utente"]["codice_ente"]) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= " AND b_concorsi.codice_ente = :codice_ente_utente";
				}
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				if ($_SESSION["gerarchia"] > 1) {
					 $bind[":codice_utente"] = $_SESSION["codice_utente"];
					 $strsql .= " AND b_permessi_concorsi.codice_utente = :codice_utente ";
				 }
				$strsql .= " ORDER BY b_concorsi.codice DESC" ;

				$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

				if ($risultato->rowCount()>0) {
					?>
				    <table id="pagine" width="100%" id="concorsi" class="elenco">
				    	<thead>
				        	<tr>
										<td width="1"></td><td>ID</td><td>CIG</td><td>Stato</td><td>Oggetto</td>
										<? if ($_SESSION["ente"]["tipo"] == "SUA") echo "<td>Ente</td>"; ?>
				            </tr>
				            </thead>
				            <tbody>
				    <?
						while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
							$record["quesiti"] = 0;
							$bind = array();
							$bind[":codice_gara"] = $record["codice"];
							$sql_quesiti = "SELECT b_quesiti_concorsi.codice FROM b_quesiti_concorsi LEFT JOIN b_risposte_concorsi ON b_quesiti_concorsi.codice = b_risposte_concorsi.codice_quesito
											WHERE b_quesiti_concorsi.codice_gara = :codice_gara AND (b_risposte_concorsi.quesito = '' OR b_risposte_concorsi.quesito IS NULL) AND b_quesiti_concorsi.attivo = 'N'";
							$ris_quesiti = $pdo->bindAndExec($sql_quesiti,$bind);
							$record["quesiti"] = $ris_quesiti->rowCount();
							if (($record["stato"]==3) && (strtotime($record["data_scadenza"])<time())) {
								$record["colore"] = $config["colore_scaduta"];
								$record["fase"] = "Scaduta";
						}
					?>
						<tr id="<? echo $record["codice"] ?>" <? if ($record["quesiti"] > 0) echo "style='font-weight:bold'" ?>>
							<td width="1" style="background-color:#<? echo $record["colore"] ?>"></td>
							<td width="5%"><? echo $record["id"] ?></td>
							<td width="5%"><? echo $record["cig"] ?></td>
							<td width="15%"><? echo $record["fase"] ?></td>
							<td <?= ($_SESSION["ente"]["tipo"] == "SUA") ? 'width="60%"' : 'width="80%"'; ?> style="position:relative;">
								<a href="/concorsi/pannello.php?codice=<? echo $record["codice"] ?>" title="Pannello gara">
									<? if ($record["quesiti"] >0) { ?><span class="badge"><?= $record["quesiti"] ?> Chiarimenti pendenti</span><br><br><? } ?>
									<? echo $record["oggetto"] ?>
								</a>
							</td>
							<? if ($_SESSION["ente"]["tipo"] == "SUA") echo "<td width='20%'>".$record["denominazione"]."</td>"; ?>
				     </tr>
				        <?
						}

					?>
				    	</tbody>
				    </table>
				    <div class="clear"></div>

				<?php

					}		else {
				?><h1 style="text-align:center">
				<span class="fa fa-exclamation-circle fa-3x"></span><br>Nessun risultato!</h1>	<?
				}
		?>
        <a href="/concorsi/id0-edit" title="Inserisci nuovo concorso"><div class="add_new">
        <span class="fa fa-plus-circle fa-3x"></span><br>
        Aggiungi nuovo concorso
        </div></a>
        <? } ?>

<?
	include_once($root."/layout/bottom.php");
	?>
