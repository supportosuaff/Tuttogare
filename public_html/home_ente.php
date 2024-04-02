<style type="text/css">
	.box_rapido {
		display: table-cell;
		width: 25%;
		height: 100%;
		vertical-align: middle;
		text-align: center;
		background-color: #EEE;
	}

	.box_rapido:hover {
		background-color: #DBFFA8;
	}
</style>

<div style="display: table; width: 100%; height:250px">
	<div style="display: table-cell; position: relative;">
		<div style="position: absolute; top:0; bottom: 0; left: 0; right: 15px;">
			<div style="display: table; width: 100%; height: 100%; border-spacing: 4px 0px;">
				<?
					$widthBox = 20;
					if(!empty($_SESSION["codice_utente"])) {
						if(is_operatore()) {
							?>
							<a href="/operatori_economici/id<?= $_SESSION["codice_utente"] ?>-edit" class="box_rapido" style="width: 20%">
								<i class="fa fa-user fa-4x"></i><br><br>
								<b><?= strtoupper(traduci("profilo")) ?></b>
							</a>
							<a href="/comunicazioni/" class="box_rapido" style="width: 20%">
								<i class="fa fa-comment fa-4x"></i><br><br>
								<b><?= strtoupper(traduci("comunicazioni")) ?></b>
							</a>
							<a href="/gare_bozza/" class="box_rapido" style="width: 20%">
								<i class="fa fa-pencil fa-4x"></i><br><br>
								<b><?= strtoupper(traduci("bozze")) ?></b>
							</a>
							<a href="<?= (is_operatore()) ? '/gare_attive/' : '/gare/' ?>" class="box_rapido" style="width: 20%">
								<i class="fa fa-star fa-4x"></i><br><br>
								<b><?= strtoupper(traduci("gare attive")) ?></b>
							</a>
							<?
						} else {
							$widthBox = 25;
							?>
							<a href="/gare/" class="box_rapido" style="width: 25%">
								<i class="fa fa-briefcase fa-4x"></i><br><br>
								<b>GESTIONE GARE</b>
							</a>
							<a href="/albo_fornitori/" class="box_rapido" style="width: 25%">
								<i class="fa fa-folder-open fa-4x"></i><br><br>
								<b>ALBO FORNITORI</b>
							</a>
							<a href="<?= "/user/id" . $_SESSION["codice_utente"] . "-edit" ?>" class="box_rapido" style="width: 25%">
								<i class="fa fa-user fa-4x"></i><br><br>
								<b>PROFILO UTENTE</b>
							</a>
							<?
						}
					} else {
						?>
						<a href="/operatori_economici/registrazione.php" class="box_rapido"  style="width: 20%">
							<i class="fa fa-user fa-4x"></i><br><br>
							<b>
								<?= strtoupper(traduci("registrazione-oe")) ?>
							</b>
						</a>
						<a href="/accesso.php" class="box_rapido"  style="width: 20%">
							<i class="fa fa-lock fa-4x"></i><br><br>
							<b><?= strtoupper(traduci("accedi")) ?></b>
						</a>
						<a href="/archivio_gare/" class="box_rapido"  style="width: 20%">
							<i class="fa fa-briefcase fa-4x"></i><br><br>
							<b><?= strtoupper(traduci("gare")) ?></b>
						</a>
						<a href="/archivio_avvisi/" class="box_rapido"  style="width: 20%">
							<i class="fa fa-bullhorn fa-4x"></i><br><br>
							<b><?= strtoupper(traduci("avvisi di gara")) ?></b>
						</a>
						<?
					}
					?>
				<a href="/scadenzario/" class="box_rapido" title="Scadenzario"  style="width: <?= $widthBox ?>%">
					<i class="fa fa-calendar fa-4x"></i><br><br>
					<b><?= strtoupper("Scadenzario") ?></b>
				</a>
			</div>
			<div class="clear"></div>
		</div>
	</div>
</div>

<style type="text/css">
	.home,
	.home > a {
		color: #A00 !important;
		margin-top: 10px;
		text-decoration: underline;
		font-weight: bold;
		cursor: pointer;
		text-align: center;
	}

	.box-home {
		width: 100%;
		background-color: #e3e3e3;
		padding: 0px;
	}

	.box-home a {
		color: #000;
	}

	.box-home h3 {
		font-weight: bold;
	}

	.triangular td {
		padding: 2px 0px;
	}

	#contenuto_top a {
		color: #000;
	}

	.box.agg_norm {
		border-bottom: none;
		min-height: 100px;
		max-height: 200px;
		height: 200px;
		margin-bottom: 20px;
		background-color: transparent;
		border-left: solid 1px #000;
		padding-top: 0px;
		padding-bottom: 0px;
		overflow: hidden;
	}

	.box.agg_norm a {
		text-transform: uppercase;
	}

	.txt_oggetto {
		font-weight: 400;
		text-transform: uppercase;
	}
	.txt_oggetto:hover {
		color: #008040 !important;
	}
</style>
<? if ($_SESSION["bigText"] == false) { ?>
	<style type="text/css">
		#contenuto_top {
			font-size: 13px;
		}
		.home,
		.home > a {
			font-size: 20px;
		}

		.box-home h3 {
			font-size: 16px;
		}

		.box.agg_norm a {
			font-size: 15px;
		}

		.txt_oggetto {
			font-size: 17px;
		}
	</style>
	<? }
	$counter = 0;
	$strsql  = "SELECT b_news.*
							FROM b_news
							WHERE  b_news.data <= curdate() AND (b_news.scadenza_hp >= curdate() || b_news.scadenza_hp IS NULL || b_news.scadenza_hp = '0000-00-00' )
							AND (codice_ente = :codice_ente OR (codice_ente = 0 AND servizio = TRUE))
							ORDER BY b_news.data DESC,  b_news.timestamp DESC LIMIT 0,6" ;

	$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
	$ris_news  = $pdo->bindAndExec($strsql,$bind);
	$counter += $ris_news->rowCount();

	$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
	if (!isset($_SESSION["codice_utente"])) {
		$strsql  = "SELECT b_gare.*, b_tipologie.tipologia AS tipologia, b_criteri.criterio AS criterio, b_procedure.nome AS procedura
								FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice
								JOIN b_criteri ON b_gare.criterio = b_criteri.codice
								JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice
								WHERE data_scadenza >= now() AND pubblica = '2' AND annullata = 'N' AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
								GROUP BY b_gare.codice
								ORDER BY data_scadenza, id DESC, codice DESC";
	} else {
		if (is_operatore()) {
			$strsql  = "SELECT b_gare.*, b_tipologie.tipologia AS tipologia, b_criteri.criterio AS criterio, b_procedure.nome AS procedura  FROM b_gare LEFT JOIN r_inviti_gare ON b_gare.codice = r_inviti_gare.codice_gara
									JOIN b_procedure ON b_gare.procedura = b_procedure.codice
									JOIN b_criteri ON b_gare.criterio = b_criteri.codice
									JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice
									WHERE data_scadenza >= now() AND annullata = 'N' AND  (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
									AND (pubblica = '2' OR (pubblica = '1' AND ((b_procedure.invito = 'N' AND r_inviti_gare.codice_utente IS NULL) OR (b_procedure.invito = 'S' AND r_inviti_gare.codice_utente = :codice_utente))))
									GROUP BY b_gare.codice
									ORDER BY data_scadenza, id DESC, codice DESC" ;
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
		} else {
			$strsql  = "SELECT b_gare.*, b_tipologie.tipologia AS tipologia, b_criteri.criterio AS criterio, b_procedure.nome AS procedura
									FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice
									JOIN b_criteri ON b_gare.criterio = b_criteri.codice
									JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice
									WHERE  data_scadenza >= now() AND annullata = 'N' AND  (pubblica > 0) AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
									GROUP BY b_gare.codice
									ORDER BY data_scadenza, id DESC, codice DESC" ;
		}
	}
	$ris_gare  = $pdo->bindAndExec($strsql,$bind);
	$counter += $ris_gare->rowCount();

	if (!isset($_SESSION["codice_utente"])) {
		$strsql  = "SELECT b_concorsi.*, b_ente_gestore.dominio, b_enti.denominazione, b_conf_stati_concorsi.titolo AS fase, b_conf_stati_concorsi.colore  ";
		$strsql .= "FROM b_concorsi  ";
		$strsql .= "JOIN b_conf_stati_concorsi ON b_concorsi.stato = b_conf_stati_concorsi.fase ";
		$strsql .= "JOIN b_enti ON b_concorsi.codice_ente = b_enti.codice ";
		$strsql .= "JOIN b_enti AS b_ente_gestore ON b_concorsi.codice_gestore = b_ente_gestore.codice ";
		$strsql .= "WHERE pubblica = '2' AND (codice_gestore = :codice_ente OR codice_ente = :codice_ente) ";
		$strsql .= " AND b_concorsi.data_scadenza >= NOW() ";
		$strsql .= "GROUP BY b_concorsi.codice ";
		$strsql .= "ORDER BY codice DESC" ;
	} else {
		$strsql  = "SELECT b_concorsi.*, b_ente_gestore.dominio, b_enti.denominazione, b_conf_stati_concorsi.titolo AS fase, b_conf_stati_concorsi.colore  ";
		$strsql .= "FROM b_concorsi  ";
		$strsql .= "JOIN b_conf_stati_concorsi ON b_concorsi.stato = b_conf_stati_concorsi.fase ";
		$strsql .= "JOIN b_enti ON b_concorsi.codice_ente = b_enti.codice ";
		$strsql .= "JOIN b_enti AS b_ente_gestore ON b_concorsi.codice_gestore = b_ente_gestore.codice ";
		$strsql .= "WHERE pubblica > 0 AND (codice_gestore = :codice_ente OR codice_ente = :codice_ente) ";
		$strsql .= " AND b_concorsi.data_scadenza >= NOW() ";
		$strsql .= "GROUP BY b_concorsi.codice ";
		$strsql .= "ORDER BY codice DESC" ;
	}

	$ris_concorsi  = $pdo->bindAndExec($strsql,$bind);
	$counter += $ris_concorsi->rowCount();

	$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
	if (!isset($_SESSION["codice_utente"])) {
		$strsql  = "SELECT *
								FROM b_bandi_sda
								WHERE pubblica = '2' AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
								AND annullata = 'N' AND (data_scadenza > now() OR data_scadenza = '')
								ORDER BY id DESC, codice DESC" ;
	} else {
		$strsql  = "SELECT *
								FROM b_bandi_sda
								WHERE (pubblica = '2' OR pubblica = '1') AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
								AND annullata = 'N' AND (data_scadenza > now() OR data_scadenza = '')
								ORDER BY id DESC, codice DESC" ;
	}
	$ris_sda  = $pdo->bindAndExec($strsql,$bind);
	$counter += $ris_sda->rowCount();

	$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
	if (!isset($_SESSION["codice_utente"])) {
		$strsql  = "SELECT *
								FROM b_bandi_mercato
								WHERE pubblica = '2' AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
								AND annullata = 'N' AND (data_scadenza > now() OR data_scadenza = '')
								ORDER BY id DESC, codice DESC" ;
	} else {
		$strsql  = "SELECT *
								FROM b_bandi_mercato
								WHERE (pubblica = '2' OR pubblica = '1') AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
								AND annullata = 'N' AND (data_scadenza > now() OR data_scadenza = '')
								ORDER BY id DESC, codice DESC" ;
	}
	$ris_bandi_mercato  = $pdo->bindAndExec($strsql,$bind);
	$counter += $ris_bandi_mercato->rowCount();

	if (!isset($_SESSION["codice_utente"])) {
		$strsql  = "SELECT *
								FROM b_bandi_albo
								WHERE pubblica = '2' AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
								AND annullata = 'N' AND (data_scadenza > now() OR data_scadenza = '') AND manifestazione_interesse = 'N' AND tipologia = 'F'
								ORDER BY id DESC, codice DESC" ;
	} else {
		$strsql  = "SELECT *
								FROM b_bandi_albo
								WHERE (pubblica = '2' OR pubblica = '1') AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
								AND annullata = 'N' AND (data_scadenza > now() OR data_scadenza = '') AND manifestazione_interesse = 'N' AND tipologia = 'F'
								ORDER BY id DESC, codice DESC" ;
	}
	$ris_bandi_albo  = $pdo->bindAndExec($strsql,$bind);

	$counter += $ris_bandi_albo->rowCount();

	if (!isset($_SESSION["codice_utente"])) {
		$strsql  = "SELECT *
								FROM b_bandi_albo
								WHERE pubblica = '2' AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
								AND annullata = 'N' AND (data_scadenza > now() OR data_scadenza = '') AND manifestazione_interesse = 'N' AND tipologia = 'P'
								ORDER BY id DESC, codice DESC" ;
	} else {
		$strsql  = "SELECT *
								FROM b_bandi_albo
								WHERE (pubblica = '2' OR pubblica = '1') AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
								AND annullata = 'N' AND (data_scadenza > now() OR data_scadenza = '') AND manifestazione_interesse = 'N' AND tipologia = 'P'
								ORDER BY id DESC, codice DESC" ;
	}
	$ris_bandi_professionisti  = $pdo->bindAndExec($strsql,$bind);

	$counter += $ris_bandi_professionisti->rowCount();


	if (!isset($_SESSION["codice_utente"])) {
		$strsql  = "SELECT *
								FROM b_bandi_albo
								WHERE pubblica = '2' AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
								AND annullata = 'N' AND (data_scadenza > now() OR data_scadenza = '') AND manifestazione_interesse = 'S'
								ORDER BY id DESC, codice DESC" ;
	} else {
		$strsql  = "SELECT *
								FROM b_bandi_albo
								WHERE (pubblica = '2' OR pubblica = '1') AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
								AND annullata = 'N' AND (data_scadenza > now() OR data_scadenza = '') AND manifestazione_interesse = 'S'
								ORDER BY id DESC, codice DESC" ;
	}

	$ris_bandi_manifestazioni  = $pdo->bindAndExec($strsql,$bind);

	$counter += $ris_bandi_manifestazioni->rowCount();

	if (!isset($_SESSION["codice_utente"])) {
		$strsql  = "SELECT *
								FROM b_bandi_dialogo
								WHERE pubblica = '2' AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
								AND annullata = 'N' AND (data_scadenza > now() OR data_scadenza = '')
								ORDER BY id DESC, codice DESC" ;
	} else {
		$strsql  = "SELECT *
								FROM b_bandi_dialogo
								WHERE (pubblica = '2' OR pubblica = '1') AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
								AND annullata = 'N' AND (data_scadenza > now() OR data_scadenza = '')
								ORDER BY id DESC, codice DESC" ;
	}
	$ris_bandi_dialogo  = $pdo->bindAndExec($strsql,$bind);
	$counter += $ris_bandi_dialogo->rowCount();

	$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
	if (!isset($_SESSION["codice_utente"])) {
		$strsql  = "SELECT b_avvisi.*, b_gare.oggetto, b_gare.id ";
		$strsql .= "FROM b_avvisi JOIN b_gare ON b_avvisi.codice_gara =  b_gare.codice ";
		$strsql .= "WHERE b_avvisi.data <= now() AND (b_avvisi.data_scadenza >= now() OR ((b_avvisi.data_scadenza = '0000-00-00' OR b_avvisi.data_scadenza IS NULL) AND (b_avvisi.data > NOW() - INTERVAL 20 DAY))) AND pubblica = 2 AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente) ";
		$strsql .= "ORDER BY data DESC, codice DESC";
	} else {
		if (is_operatore()) {
			$strsql  = "SELECT b_avvisi.*, b_gare.oggetto, b_gare.id ";
			$strsql .= "FROM b_avvisi JOIN b_gare ON b_avvisi.codice_gara =  b_gare.codice ";
			$strsql .= "LEFT JOIN r_inviti_gare ON b_gare.codice = r_inviti_gare.codice_gara ";
			$strsql .= "WHERE (b_avvisi.data <= now() AND (b_avvisi.data_scadenza >= now() OR (((b_avvisi.data_scadenza = '0000-00-00' OR b_avvisi.data_scadenza IS NULL) OR b_avvisi.data_scadenza IS NULL) AND (b_avvisi.data > NOW() - INTERVAL 20 DAY)))) AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente) ";
			$strsql .= "AND (pubblica = '2' OR (pubblica = '1' AND r_inviti_gare.codice_utente = :codice_utente)) ";
			$strsql .= "ORDER BY data DESC, codice DESC LIMIT 0,10";
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
		} else {
			$strsql  = "SELECT b_avvisi.*, b_gare.oggetto, b_gare.id ";
			$strsql .= "FROM b_avvisi JOIN b_gare ON b_avvisi.codice_gara =  b_gare.codice ";
			$strsql .= "WHERE (b_avvisi.data <= now() AND (b_avvisi.data_scadenza >= now() OR (((b_avvisi.data_scadenza = '0000-00-00' OR b_avvisi.data_scadenza IS NULL) OR b_avvisi.data_scadenza IS NULL) AND (b_avvisi.data > NOW() - INTERVAL 20 DAY)))) AND pubblica > 0 AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente) ";
			$strsql .= "ORDER BY data DESC, codice DESC";
		}
	}
	$ris_avvisi_di_gara  = $pdo->bindAndExec($strsql,$bind);
	$counter += $ris_avvisi_di_gara->rowCount();

	$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
	if (!isset($_SESSION["codice_utente"])) {
		$strsql  = "SELECT b_avvisi_concorsi.*, b_concorsi.oggetto, b_concorsi.id ";
		$strsql .= "FROM b_avvisi_concorsi JOIN b_concorsi ON b_avvisi_concorsi.codice_gara =  b_concorsi.codice ";
		$strsql .= "WHERE b_avvisi_concorsi.data <= now() AND (b_avvisi_concorsi.data_scadenza >= now() OR ((b_avvisi_concorsi.data_scadenza = '0000-00-00' OR b_avvisi_concorsi.data_scadenza IS NULL) AND (b_avvisi_concorsi.data > NOW() - INTERVAL 20 DAY))) AND pubblica = '2' AND (b_concorsi.codice_ente = :codice_ente OR b_concorsi.codice_gestore = :codice_ente) ";
		$strsql .= "ORDER BY b_avvisi_concorsi.data DESC, b_avvisi_concorsi.codice DESC";
	} else {
		$strsql  = "SELECT b_avvisi_concorsi.*, b_concorsi.oggetto, b_concorsi.id ";
		$strsql .= "FROM b_avvisi_concorsi JOIN b_concorsi ON b_avvisi_concorsi.codice_gara =  b_concorsi.codice ";
		$strsql .= "WHERE (b_avvisi_concorsi.data <= now() AND (b_avvisi_concorsi.data_scadenza >= now() OR ((b_avvisi_concorsi.data_scadenza = '0000-00-00' OR b_avvisi_concorsi.data_scadenza IS NULL) AND (b_avvisi_concorsi.data > NOW() - INTERVAL 20 DAY)))) AND pubblica > 0 AND (b_concorsi.codice_ente = :codice_ente OR b_concorsi.codice_gestore = :codice_ente) ";
		$strsql .= "ORDER BY b_avvisi_concorsi.data DESC, b_avvisi_concorsi.codice DESC";
	}
	$ris_avvisi_concorsi  = $pdo->bindAndExec($strsql,$bind);
	$counter += $ris_avvisi_concorsi->rowCount();
	$two_column = FALSE;
	?>
	<div style="position: relative;">
			<?
		//START LEFT/TOP SIDE
		if($two_column) {echo '<div style="float:left; width:70%;"><div style="padding-right:15px;">';} else {echo '<div style="float:left; width:100%;">';}
		if($ris_news->rowCount() > 0) {
			?>
			<h2 class="home"><a href="/news"><?= traduci("NOTIZIE") ?></a></h2>
			<?
			while ($rec_news = $ris_news->fetch(PDO::FETCH_ASSOC)) {
				?>
				<div class="box">
					<?
					$codice	= $rec_news["codice"];
					$titolo	= $rec_news["titolo"];
					$data		= mysql2date($rec_news["data"]);
					$testo	= strip_tags($rec_news["testo"]);
					$href = "/news/id".$codice."-".sanitize_string($titolo);
					echo echo_calendario($data);
					?>
					<a class="txt_oggetto" href="<? echo $href ?>" title="<? echo $titolo ?>"><h3 style="display: inline;"><? echo $titolo; ?></h3></a></strong><br><? echo substr($testo,0,255); ?>...
					<div class="clear"></div>
				</div>
				<?
			}
		}
		if($ris_gare->rowCount() > 0) {
			?><h2 class="home"><a href="<?= (is_operatore()) ? '/gare_attive/' : '/archivio_gare/index.php?scadute=0' ?>"><?= strtoupper(traduci("gare attive")) ?></a></h2><?
			while($rec_gare = $ris_gare->fetch(PDO::FETCH_ASSOC)) {
				?>
				<div class="box">
					<table width="100%" style="background-color: none;">
						<tr class="triangular">
							<td>
								<i>ID:</i>
								<a href="/gare/id<? echo $rec_gare["codice"] ?>-dettaglio" title="Dettagli gara"><strong><?= $rec_gare["id"]; ?><?= (!empty($rec_gare["id_suaff"])) ? (" - ID SUAFF: " . $rec_gare["id_suaff"]) : "" ?></strong></a>
								<span style="float: right;">
									<i style="color:#C30"><?= traduci("scadenza") ?>:</i>
									<b><?= mysql2datetime($rec_gare["data_scadenza"]) ?></b>
								</span>
							</td>
						</tr>
						<tr class="triangular">
							<td>
								<i><?= traduci("scadenza") ?>:</i>
								<b><?= traduci($rec_gare["tipologia"]) ?></b> |
								<i><?= traduci("criterio") ?>:</i>
								<b><?= traduci($rec_gare["criterio"]) ?></b> |
								<i><?= traduci("procedura") ?>:</i>
								<b><?= traduci($rec_gare["procedura"]) ?></b>
							</td>
						</tr>
						<tr class="triangular">
							<td colspan="4">
								<a class="txt_oggetto" href="/gare/id<? echo $rec_gare["codice"] ?>-dettaglio" title="<?= traduci('dettagli') ?>"><? echo $rec_gare["oggetto"] ?></a>
							</td>
						</tr>
					</table>
					<div class="clear"></div>
				</div>
				<?
			}
		}

		if($ris_sda->rowCount() > 0) {
			?><h2 class="home"><a href="/archivio_sda"><?= traduci('sistema dinamico di acquisizione') ?></a></h2><?
			while ($rec_sda = $ris_sda->fetch(PDO::FETCH_ASSOC)) {
				?>
				<div class="box">
					<table width="100%" style="background-color: none;">
						<tr class="triangular">
							<td width="75%" colspan="3">ID: <a href="/sda/id<? echo $rec_sda["codice"] ?>-dettaglio" title="<?= traduci("dettagli") ?>"><?= $rec_sda["id"] ?></a></td>
							<td width="25%" style="text-align: right;">
								<i><?= traduci("scadenza") ?>:</i>
								<b><?= mysql2datetime($rec_sda["data_scadenza"]) ?></b>
							</td>
						</tr>
						<tr class="triangular">
							<td colspan="4">
								<a class="txt_oggetto" href="/sda/id<? echo $rec_sda["codice"] ?>-dettaglio" title="<?= traduci("dettagli") ?>"><? echo $rec_sda["oggetto"] ?></a>
							</td>
						</tr>
					</table>
					<div class="clear"></div>
				</div>
				<?
			}
		}

		if($ris_concorsi->rowCount() > 0) {
			?><h2 class="home"><a href="/archivio_concorsi"><?= traduci("concorsi progettazione") ?></a></h2><?
			while ($rec_concorso = $ris_concorsi->fetch(PDO::FETCH_ASSOC)) {
				?>
				<div class="box">
					<table width="100%" style="background-color: none;">
						<tr class="triangular">
							<td width="75%" colspan="3">ID: <a href="/concorsi/id<? echo $rec_concorso["codice"] ?>-dettaglio" title="<?= traduci('dettagli') ?>"><?= $rec_concorso["id"] ?></a></td>
							<td width="25%" style="text-align: right;">
								<i><?= traduci('scadenza') ?>:</i>
								<b><?= mysql2datetime($rec_concorso["data_scadenza"]) ?></b>
							</td>
						</tr>
						<tr class="triangular">
							<td colspan="4">
								<a class="txt_oggetto" href="/concorsi/id<? echo $rec_concorso["codice"] ?>-dettaglio" title="<?= traduci('dettagli') ?>"><? echo $rec_concorso["oggetto"] ?></a>
							</td>
						</tr>
					</table>
					<div class="clear"></div>
				</div>
				<?
			}
		}

		if($ris_bandi_mercato->rowCount() > 0) {
			?><h2 class="home"><a href="/archivio_mercato"><?= traduci('mercato elettronico') ?></a></h2><?
			while ($rec_bandi_mercato = $ris_bandi_mercato->fetch(PDO::FETCH_ASSOC)) {
				?>
				<div class="box">
					<table width="100%" style="background-color: none;">
						<tr class="triangular">
							<td width="75%" colspan="3">ID: <a href="/mercato_elettronico/id<? echo $rec_bandi_mercato["codice"] ?>-dettaglio" title="<?= traduci('sistema dinamico di acquisizione') ?>"><?= $rec_bandi_mercato["id"] ?></a></td>
							<td width="25%" style="text-align: right;">
								<i><?= traduci('scadenza') ?>:</i>
								<b><?= mysql2datetime($rec_bandi_mercato["data_scadenza"]) ?></b>
							</td>
						</tr>
						<tr class="triangular">
							<td colspan="4">
								<a class="txt_oggetto" href="/mercato_elettronico/id<? echo $rec_bandi_mercato["codice"] ?>-dettaglio" title="<?= traduci('sistema dinamico di acquisizione') ?>"><? echo $rec_bandi_mercato["oggetto"] ?></a>
							</td>
						</tr>
					</table>
					<div class="clear"></div>
				</div>
				<?
			}
		}

		if($ris_bandi_albo->rowCount() > 0) {
			?><h2 class="home"><a href="/archivio_albo"><?= traduci('albo dei fornitori') ?></a></h2><?
			while ($rec_bandi_albo = $ris_bandi_albo->fetch(PDO::FETCH_ASSOC)) {
				?>
				<div class="box">
					<table width="100%" style="background-color: none;">
						<tr class="triangular">
							<td width="75%" colspan="3">ID: <a href="/albo_fornitori/id<? echo $rec_bandi_albo["codice"] ?>-dettaglio" title="<?= traduci("dettagli") ?>"><?= $rec_bandi_albo["id"] ?></a></td>
							<td width="25%" style="text-align: right;">
								<i><?= traduci('scadenza') ?>:</i>
								<b><?= mysql2datetime($rec_bandi_albo["data_scadenza"]) ?></b>
							</td>
						</tr>
						<tr class="triangular">
							<td colspan="4">
								<a class="txt_oggetto" href="/albo_fornitori/id<? echo $rec_bandi_albo["codice"] ?>-dettaglio" title="<?= traduci("dettagli") ?>"><? echo $rec_bandi_albo["oggetto"] ?></a>
							</td>
						</tr>
					</table>
					<div class="clear"></div>
				</div>
				<?
			}
		}

		if($ris_bandi_professionisti->rowCount() > 0) {
			?><h2 class="home"><a href="/archivio_albo"><?= traduci('Albo dei professionisti') ?></a></h2><?
			while ($rec_bandi_albo = $ris_bandi_professionisti->fetch(PDO::FETCH_ASSOC)) {
				?>
				<div class="box">
					<table width="100%" style="background-color: none;">
						<tr class="triangular">
							<td width="75%" colspan="3">ID: <a href="/albo_fornitori/id<? echo $rec_bandi_albo["codice"] ?>-dettaglio" title="<?= traduci("dettagli") ?>"><?= $rec_bandi_albo["id"] ?></a></td>
							<td width="25%" style="text-align: right;">
								<i><?= traduci('scadenza') ?>:</i>
								<b><?= mysql2datetime($rec_bandi_albo["data_scadenza"]) ?></b>
							</td>
						</tr>
						<tr class="triangular">
							<td colspan="4">
								<a class="txt_oggetto" href="/albo_fornitori/id<? echo $rec_bandi_albo["codice"] ?>-dettaglio" title="<?= traduci("dettagli") ?>"><? echo $rec_bandi_albo["oggetto"] ?></a>
							</td>
						</tr>
					</table>
					<div class="clear"></div>
				</div>
				<?
			}
		}

		if($ris_bandi_manifestazioni->rowCount() > 0) {
			?><h2 class="home"><a href="/archivio_indagini"><?= traduci('indagini di mercato') ?></a></h2><?
			while ($rec_bandi_albo = $ris_bandi_manifestazioni->fetch(PDO::FETCH_ASSOC)) {
				?>
				<div class="box">
					<table width="100%" style="background-color: none;">
						<tr class="triangular">
							<td width="75%" colspan="3">ID: <a href="/albo_fornitori/id<? echo $rec_bandi_albo["codice"] ?>-dettaglio" title="<?= traduci('dettagli') ?>"><?= $rec_bandi_albo["id"] ?></a></td>
							<td width="25%" style="text-align: right;">
								<i><?= traduci('scadenza') ?>:</i>
								<b><?= mysql2datetime($rec_bandi_albo["data_scadenza"]) ?></b>
							</td>
						</tr>
						<tr class="triangular">
							<td colspan="4">
								<a class="txt_oggetto" href="/albo_fornitori/id<? echo $rec_bandi_albo["codice"] ?>-dettaglio" title="<?= traduci('dettagli') ?>"><? echo $rec_bandi_albo["oggetto"] ?></a>
							</td>
						</tr>
					</table>
					<div class="clear"></div>
				</div>
				<?
			}
		}

		if($ris_bandi_dialogo->rowCount() > 0) {
			?><h2 class="home"><a href="/archivio_dialogo"><?= traduci('dialogo competitivo') ?></a></h2><?
			while ($rec_bandi_dialogo = $ris_bandi_dialogo->fetch(PDO::FETCH_ASSOC)) {
				?>
				<div class="box">
					<table width="100%" style="background-color: none;">
						<tr class="triangular">
							<td width="75%" colspan="3">ID: <a href="/dialogo_competitivo/id<? echo $rec_bandi_dialogo["codice"] ?>-dettaglio" title="<?= traduci('dialogo competitivo') ?>"><?= $rec_bandi_dialogo["id"] ?></a></td>
							<td width="25%" style="text-align: right;">
								<i><?= traduci('scadenza') ?>:</i>
								<b><?= mysql2datetime($rec_bandi_dialogo["data_scadenza"]) ?></b>
							</td>
						</tr>
						<tr class="triangular">
							<td colspan="4">
								<a class="txt_oggetto" href="/dialogo_competitivo/id<? echo $rec_bandi_dialogo["codice"] ?>-dettaglio" title="<?= traduci('dialogo competitivo') ?>"><? echo $rec_bandi_dialogo["oggetto"] ?></a>
							</td>
						</tr>
					</table>
					<div class="clear"></div>
				</div>
				<?
			}
		}

		if($ris_avvisi_di_gara->rowCount() > 0) {
			?><h2 class="home"><?= traduci('avvisi di gara') ?></h2><?
			while($rec_avvisi_di_gara = $ris_avvisi_di_gara->fetch(PDO::FETCH_ASSOC)) {
				?>
				<div class="box">
					<?
					$codice	= $rec_avvisi_di_gara["codice"];
					$titolo	= $rec_avvisi_di_gara["titolo"];
					$data		= mysql2date($rec_avvisi_di_gara["data"]);
					$testo	= strip_tags($rec_avvisi_di_gara["testo"]);
					$href = "/gare/avvisi/dettaglio.php?cod=".$codice;
					echo echo_calendario($data);
					?>
					<a href="<? echo $href ?>" title="<? echo $titolo ?>"><h3 style="display: inline;"><? echo $titolo; ?></h3></a></strong><br><? echo substr($testo,0,255); ?>...
					<div class="clear"></div>
				</div>
				<?
			}
		}

		if($ris_avvisi_concorsi->rowCount() > 0) {
			?><h2 class="home"><?= traduci('avvisi di concorso') ?></h2><?
			while($rec_avvisi_concorsi = $ris_avvisi_concorsi->fetch(PDO::FETCH_ASSOC)) {
				?>
				<div class="box">
					<?
					$codice	= $rec_avvisi_concorsi["codice"];
					$titolo	= $rec_avvisi_concorsi["titolo"];
					$data		= mysql2date($rec_avvisi_concorsi["data"]);
					$testo	= strip_tags($rec_avvisi_concorsi["testo"]);
					$href = "/concorsi/avvisi/dettaglio.php?cod=".$codice;
					echo echo_calendario($data);
					?>
					<a href="<? echo $href ?>" title="<? echo $titolo ?>"><h3 style="display: inline;"><? echo $titolo; ?></h3></a></strong><br><? echo substr($testo,0,255); ?>...
					<div class="clear"></div>
				</div>
				<?
			}
		}

		if($two_column) {
			echo '</div></div>';
			echo '<div style="position:absolute; top:0; right:0; bottom:0; width:30%; height:100%; overflow:auto;">';
		} else {
			echo '</div>';
			echo '<div style="width:100%;">';

		}
		if (isset($_SESSION["ente"]["agg_normativi"]) && ($_SESSION["ente"]["agg_normativi"] == "N") && $counter == 0) {
			?><br>
			<h1 style="text-align:center"><?= traduci("Nessuna iniziativa attiva") ?></h1>
			<?
		}


		?>

		<?
		echo '</div>';
		//END RIGHT/BOTTOM SIDE
		?>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
<?
