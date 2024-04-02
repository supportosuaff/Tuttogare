<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	if (isset($_SESSION["ente"])) {
		$bind = array(':codice_ente' => $_SESSION["ente"]["codice"]);
		if (!isset($_SESSION["codice_utente"])) {
			$strsql  = "SELECT b_avvisi_concorsi.*, b_concorsi.oggetto, b_concorsi.cig, b_concorsi.id, b_enti.dominio  ";
			$strsql .= "FROM b_avvisi_concorsi JOIN b_concorsi ON b_avvisi_concorsi.codice_gara =  b_concorsi.codice JOIN b_enti ON b_concorsi.codice_gestore = b_enti.codice ";
			$strsql .= "WHERE b_avvisi_concorsi.data <= now() AND pubblica = '2' AND (b_concorsi.codice_ente = :codice_ente OR b_concorsi.codice_gestore = :codice_ente) ";
			if (isset($_GET["scadute"])) {
				if ($_GET["scadute"]) {
					$strsql .= " AND b_concorsi.data_scadenza < NOW() ";
				} else {
					$strsql .= " AND b_concorsi.data_scadenza >= NOW() ";
				}
			}
			$strsql .= "ORDER BY b_avvisi_concorsi.data DESC, b_avvisi_concorsi.codice DESC";
		} else {
			$strsql  = "SELECT b_avvisi_concorsi.*, b_concorsi.oggetto, b_concorsi.cig, b_concorsi.id, b_enti.dominio  ";
			$strsql .= "FROM b_avvisi_concorsi JOIN b_concorsi ON b_avvisi_concorsi.codice_gara =  b_concorsi.codice JOIN b_enti ON b_concorsi.codice_gestore = b_enti.codice ";
			$strsql .= "WHERE b_avvisi_concorsi.data <= now() AND pubblica > 0 AND (b_concorsi.codice_ente = :codice_ente OR b_concorsi.codice_gestore = :codice_ente) ";
			if (isset($_GET["scadute"])) {
				if ($_GET["scadute"]) {
					$strsql .= " AND b_concorsi.data_scadenza < NOW() ";
				} else {
					$strsql .= " AND b_concorsi.data_scadenza >= NOW() ";
				}
			}
			$strsql .= "ORDER BY b_avvisi_concorsi.data DESC, b_avvisi_concorsi.codice DESC";
		}
		$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
?><h1>Avvisi di concorso <? if (isset($_GET["scadute"])) { echo ($_GET["scadute"]) ? "Scaduti" : "Attivi"; } ?></h1>
<a href="/archivio_avvisi_concorso/index.php">Tutti</a> | <a href="/archivio_avvisi_concorso/index.php?scadute=0">Attivi</a> | <a href="/archivio_avvisi_concorso/index.php?scadute=1">Scaduti</a><br><br>
<?
	if ($risultato->rowCount() > 0) {
	?>

        <table class="elenco" style="width:100%">
					<thead><tr><td>Data</td><td>CIG</td><td>Avviso</td></tr>
        <tbody>
        <?
		while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
			$codice			= $record["codice"];
			$titolo			= $record["titolo"];
			$data			= mysql2date($record["data"]);
			$testo			= strip_tags($record["testo"]);
			$href = $config["protocollo"].$record["dominio"]."/concorsi/avvisi/dettaglio.php?cod=".$codice;
					?>
					<tr id="<? echo $codice ?>"><td width="10"><span style="display:none"><?= $record["data"] ?></span><strong><? echo $data ?></strong></td><td><?= $record["cig"] ?></td><td><strong><a style="text-transform:uppercase" href="<? echo $href ?>" title="<? echo $titolo ?>"><? echo $titolo; ?> - Gara <? echo $record["id"] . ": " . $record["oggetto"] ?></a></strong><br>
          	          <? echo substr($testo,0,255); ?>...
                      </td>
                     </tr>


<?php
	}
	?></tbody></table>
    <div class="clear"></div>
<?php
	} else {
		?>
		<h2 style="text-align:center">Nessun avviso</h2>
		<?
	}
	}
	include_once($root."/layout/bottom.php");
	?>
