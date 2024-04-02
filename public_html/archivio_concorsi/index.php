<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	if (isset($_SESSION["ente"])) {
		$bind = array(':codice_ente' => $_SESSION["ente"]["codice"]);
		if (!isset($_SESSION["codice_utente"])) {
			$strsql  = "SELECT b_concorsi.*, b_ente_gestore.dominio, b_enti.denominazione, b_conf_stati_concorsi.titolo AS fase, b_conf_stati_concorsi.colore  ";
			$strsql .= "FROM b_concorsi  ";
			$strsql .= "JOIN b_conf_stati_concorsi ON b_concorsi.stato = b_conf_stati_concorsi.fase ";
			$strsql .= "JOIN b_enti ON b_concorsi.codice_ente = b_enti.codice ";
			$strsql .= "JOIN b_enti AS b_ente_gestore ON b_concorsi.codice_gestore = b_ente_gestore.codice ";
			$strsql .= "WHERE pubblica = '2' AND (codice_gestore = :codice_ente OR codice_ente = :codice_ente) ";
			if (isset($_GET["scadute"])) {
				if ($_GET["scadute"]) {
					$strsql .= " AND b_concorsi.data_scadenza < NOW() ";
				} else {
					$strsql .= " AND b_concorsi.data_scadenza >= NOW() ";
				}
			}
			if (isset($_GET["codice_ente"])) {
				$bind[":codice_ente_filtro"]=$_GET["codice_ente"];
				$strsql .= " AND codice_ente = :codice_ente_filtro ";
			}
			if (isset($_GET["esiti"])) {
				$strsql .= " AND (b_concorsi.stato = 4 OR b_concorsi.stato >= 7) ";
			}
			$strsql .= "GROUP BY b_concorsi.codice ";
			$strsql .= "ORDER BY codice DESC" ;
		} else {

			$strsql  = "SELECT b_concorsi.*, b_ente_gestore.dominio, b_enti.denominazione, b_conf_stati_concorsi.titolo AS fase, b_conf_stati_concorsi.colore  ";
			$strsql .= "FROM b_concorsi  ";
			$strsql .= "JOIN b_conf_stati_concorsi ON b_concorsi.stato = b_conf_stati_concorsi.fase ";
			$strsql .= "JOIN b_enti ON b_concorsi.codice_ente = b_enti.codice ";
			$strsql .= "JOIN b_enti AS b_ente_gestore ON b_concorsi.codice_gestore = b_ente_gestore.codice ";
			$strsql .= "WHERE pubblica > 0 AND (codice_gestore = :codice_ente OR codice_ente = :codice_ente) ";
			if (isset($_GET["scadute"])) {
				if ($_GET["scadute"]) {
					$strsql .= " AND b_concorsi.data_scadenza < NOW() ";
				} else {
					$strsql .= " AND b_concorsi.data_scadenza >= NOW() ";
				}
			}
			if (isset($_GET["codice_ente"])) {
				$bind[":codice_ente_filtro"]=$_GET["codice_ente"];
				$strsql .= " AND codice_ente = :codice_ente_filtro ";
			}
			if (isset($_GET["esiti"])) {
				$strsql .= " AND (b_concorsi.stato = 4 OR b_concorsi.stato >= 7) ";
			}
			$strsql .= "GROUP BY b_concorsi.codice ";
			$strsql .= "ORDER BY codice DESC" ;

		}
		$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso


?><h1><? if (!isset($_GET["esiti"])) { ?><?= traduci("Concorsi") ?>
	<? if (isset($_GET["scadute"])) { echo ($_GET["scadute"]) ? traduci("Scaduti") : traduci("Attivi"); } } else { ?><?= traduci("Esiti concorsi") ?><? }?></h1>
<a href="/archivio_concorsi/index.php"><?= traduci("Tutti") ?></a> |
<a href="/archivio_concorsi/index.php?<?= (!empty($_GET["codice_ente"])) ? "codice_ente=".$_GET["codice_ente"]."&" : "" ?>scadute=0"><?= traduci("Attivi") ?></a> |
<a href="/archivio_concorsi/index.php?<?= (!empty($_GET["codice_ente"])) ? "codice_ente=".$_GET["codice_ente"]."&" : "" ?>scadute=1"><?= traduci("Scaduti") ?></a> |
<a href="/archivio_concorsi/index.php?<?= (!empty($_GET["codice_ente"])) ? "codice_ente=".$_GET["codice_ente"]."&" : "" ?>esiti=1"><?= traduci("Esiti di concorso") ?></a><br><br>
<?
	if ($risultato->rowCount() > 0) {
	?>

    <table width="100%" id="concorsi" class="elenco">
    	<thead>
				<tr><td></td><td>ID</td><td><?= traduci("Stato") ?></td><td>CIG</td><td><?= traduci("Oggetto") ?></td>
					<? if ($_SESSION["ente"]["tipo"] == "SUA") echo "<td>" . traduci("Ente") . "</td>"; ?>
					<td>Scadenza</td>
            </tr>
            </thead>
            <tbody>
    <?
		while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
			if (($record["stato"]==3) && (strtotime($record["data_scadenza"])<time())) {
				// if (isset($_SESSION["codice_utente"])) {
					$sql_fasi = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara ";
					$ris_fasi = $pdo->bindAndExec($sql_fasi,array(":codice_gara"=>$record["codice"]));
					if ($ris_fasi->rowCount() > 0) {
						$ris_fasi = $ris_fasi->fetchAll(PDO::FETCH_ASSOC);
						$i = 0;
						$open = true;
						$last = array();
						$fase_attiva = array();
						foreach($ris_fasi AS $fase) {
							if ($fase["attiva"]=="S") {
								$last = $fase_attiva;
								$fase_attiva = $fase;
							}
							$i++;
						}
						if (!empty($last["codice"])) {
							if ((strtotime($fase_attiva["scadenza"])>time())) {
								// if (is_operatore()) {
								// 	$sql_check = "SELECT * FROM r_partecipanti_concorsi JOIN r_partecipanti_utenti_concorsi ON r_partecipanti_concorsi.codice = r_partecipanti_utenti_concorsi.codice_partecipante
								// 					WHERE r_partecipanti_concorsi.codice_gara = :codice_gara AND r_partecipanti_concorsi.codice_fase = :codice_fase AND r_partecipanti_concorsi.conferma = 1 AND r_partecipanti_concorsi.ammesso = 'S'
								// 					AND r_partecipanti_concorsi.escluso = 'N' AND r_partecipanti_utenti_concorsi.codice_utente = :codice_utente ";
								// 	$ris_check = $pdo->bindAndExec($sql_check,array(":codice_gara"=>$record["codice"],":codice_fase"=>$last["codice"],":codice_utente"=>$_SESSION["codice_utente"]));
								// 	if ($ris_check->rowCount() > 0) $record["data_scadenza"] = $fase_attiva["scadenza"];
								// } else {
									$record["data_scadenza"] = $fase_attiva["scadenza"];
								// }
							}
						}
					// }
				}
				if (strtotime($record["data_scadenza"])<time()) {
					$record["colore"] = $config["colore_scaduta"];
					$record["fase"] = "Scaduta";
				}
			}
	?>
    <tr id="<? echo $record["codice"] ?>">
			<td width="1" style="background-color:#<? echo $record["colore"] ?>"></td>
			<td width="5%"><? echo $record["id"] ?></td>
      <td width="10%"><? echo traduci($record["fase"]) ?></td>
			<td><? echo $record["cig"]; ?></td>
      <td>
      <? if ($record["annullata"] == "S") {
				echo "<strong>" . traduci("Annullata") . " - " . $record["numero_annullamento"] . " \ " . mysql2date($record["data_annullamento"]) . "</strong> - ";
			} ?>
      <a href="<?= $config["protocollo"] ?><?= $record["dominio"] ?>/concorsi/id<? echo $record["codice"] ?>-dettaglio" title="Dettagli gara"><? echo $record["oggetto"] ?></a></td>
			<? if ($_SESSION["ente"]["tipo"] == "SUA") echo "<td width='15%'>".$record["denominazione"]."</td>"; ?>
      <td width="15%">
				<span style="display:none"><? echo $record["data_scadenza"] ?></span>
				<? echo mysql2datetime($record["data_scadenza"]) ?></td>
     </tr>
        <?
		}

	?>
    	</tbody>
    </table>
    <div class="clear"></div>

<?php
		} else { ?>
			<h1 style="text-align:center">Nessun concorso disponibile</h1>
        <? }
	}
	include_once($root."/layout/bottom.php");
	?>
