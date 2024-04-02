<?
	include_once("../../config.php");
	if (!isset($_SESSION["ente"]) && !isset($_SESSION["codice_utente"])) $open_page = true;
	$subtitle = " - Notizie";

	$meta = array(
		"title" => "TUTTOGARE - NOTIZIE & AGGIORNAMENTI IN MERITO A CONTRATTI E APPALTI",
		"description" => "Il portale Tuttogare è aggiornato costantemente con news in materia di appalti e pubblica amministrazione. Powered by Studio Amica",
		"keywords" => "gare,gare online,gare telematiche,gare interamente telematiche,stazioneappalti,stazione appalti,tuttogare,tutto gare,procurement,e-procurement,e procurement,sourcing,e-sourcing,e sourcing,appalti,codice appalti,nuovo codice appalti,direttiva europea 2014/24/UE,bosetti,bosetti e gatti,bosetti e gatti e partners, albo dei fornitori, mercato elettronico, gare telematiche, sistema dinamico d'acquisizione,accordo quadro,compila bandi,integrazioni,vendor rating,pubblica amministrazione,servizi,forniture,lavori pubblici,controllo spesa,razionalizzazione della spesa,tuttogare,gestione telemtica,procedimenti di gara,procedimenti di gara telematici,soursing,procurement,public sector,realizzazione bandi di gara,capitolati di gara telematici,disciplinari telematici,codice appalti,163 del 2006 bosetti,dlgs 50 del 2016,gara d appalto,163 2006 bosetti,forum PA,forum pubblica amministrazione,forum pa roma",
		);

	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("news",$_SESSION["codice_utente"]);
	}

	echo "<h2>Notizie</h2>";

	if ($edit) {
		?>
	    <hr>
	    <a href="/news/id0-edit" title="Inserisci nuova notizia"><div class="add_new">
	    <span class="fa fa-plus-circle fa-3x"></span><br>
	    Aggiungi nuova notizia
	    </div></a>
	    <hr>
    <?
	}

  if ($edit) {
		$strsql  = "SELECT b_news.* ";
		$strsql .= "FROM b_news ";
		$strsql.= "WHERE ";
		if (isset($_SESSION["ente"])) {
			$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
			$strsql.= "(codice_ente = :codice_ente OR (codice_ente = 0 AND servizio = true)) ";
		} else {
			$strsql.= "codice_ente = 0";
		}
		$strsql .= " ORDER BY b_news.data DESC,  b_news.timestamp DESC " ;
	} else {
		$strsql  = "SELECT b_news.* ";
		$strsql .= "FROM b_news ";
		$strsql .= "WHERE  b_news.data <= curdate() ";
		if (isset($_SESSION["ente"])) {
			$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
			$strsql.= " AND codice_ente = :codice_ente OR (codice_ente = 0 AND servizio = true) ";
		} else {
			$strsql.= " AND codice_ente = 0 AND servizio = false";
		}
		$strsql .= " ORDER BY b_news.data DESC,  b_news.timestamp DESC " ;
	}

	$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

	if ($risultato->rowCount()>0) {
		while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
			$codice			= $record["codice"];
			$titolo			= strtoupper($record["titolo"]);
			$data			= mysql2date($record["data"]);
			$testo			= strip_tags($record["testo"]);
			$href = "/news/dettaglio.php?cod=".$codice;
			?>
      <div class="box">
				<? echo echo_intestazione(mysql2date($record["data"]),$record["titolo"],"news",$record["codice"],TRUE);
        echo substr($testo,0,255); ?>...
			</div>
			<?
		}
	} else {
		?>
		<h2 style="text-align:center">
		<br><? echo "Nessun risultato" ?>!</h2>
		<?
	}

	if ($edit) {
		?>
      <hr>
      <a href="/news/id0-edit" title="Inserisci nuova notizia"><div class="add_new">
      <span class="fa fa-plus-circle fa-3x"></span><br>
      Aggiungi nuova notizia
      </div></a>
      <hr>
    <?
  }
	include_once($root."/layout/bottom.php");
	?>
