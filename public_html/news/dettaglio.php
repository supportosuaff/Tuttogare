<?
	include_once("../../config.php");
	include_once($root.'/inc/funzioni.php');
	if (!isset($_SESSION["ente"]) && !isset($_SESSION["codice_utente"])) $open_page = true;
	if(!empty($_GET["cod"])) {
		$codice = $_GET["cod"];
		$bind = array(':codice' => $codice);
		$strsql = "SELECT * FROM b_news WHERE codice = :codice ";
		if (isset($_SESSION["ente"])) {
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql.= "AND (codice_ente = :codice_ente OR codice_ente = 0)";
		}
		$strsql.= " AND data <= curdate()";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount() > 0) {
			$record_notizia = $risultato->fetch(PDO::FETCH_ASSOC);
		} else {
			header('Location : /news');
			die();
		}
	} else {
		header('Location : /news');
		die();
	}

	$meta = array(
		"title" => $config["nome_sito"] . " - " . $record_notizia["title"],
		"description" => $record_notizia["description"],
		"keywords" => $record_notizia["keywords"]
		);

	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("news",$_SESSION["codice_utente"]);
		$per_newsletter = check_permessi("newsletter",$_SESSION["codice_utente"]);
	}

	if(!empty($record_notizia)) {
		if ($record_notizia["cod_allegati"] != "" && preg_match("/^[0-9\;]+$/",$record_notizia["cod_allegati"])) {
			$allegati = explode(";",$record_notizia["cod_allegati"]);
			$str_allegati = ltrim(implode(",",$allegati),",");
			$sql = "SELECT * FROM b_allegati WHERE codice IN (" . $str_allegati . ")";
			$ris_allegati = $pdo->query($sql);
		}
		if ($edit) {
			?>
      <div style="text-align:right;">
      	<input type="image" onClick="window.location.href='/news/id<? echo $record_notizia["codice"] ?>-edit'" src="/img/edit.png" title="Modifica" style="vertical-align:top">
				<input type="image" onClick="elimina('<? echo $record_notizia["codice"] ?>','news');" src="/img/del.png" title="Elimina" style="vertical-align:top">
      </div>
      <?
		}
		?>
		<div itemscope itemtype="http://schema.org/NewsArticle">
			<meta itemscope itemprop="mainEntityOfPage"  itemType="https://schema.org/WebPage" itemid="https://google.com/article"/>
			<h2 itemprop="headline"><?= $record_notizia["titolo"] ?></h2>
			<span style="display:none" itemprop="description"><?= $record_notizia["description"] ?></span>
			<h3 style="display:none" itemprop="author" itemscope itemtype="https://schema.org/Person">
    		<span itemprop="name">Tutto Gare - Powered By Studio Amica</span>
  		</h3>
			<div style="display:none" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
				<img alt="Logo - Tutto Gare" src="http://www.tuttogare.it/img/tuttogarepa-logo-software-sx.png"/>
				<meta itemprop="url" content="http://www.tuttogare.it/img/tuttogarepa-logo-software-sx.png">
				<meta itemprop="width" content="960">
				<meta itemprop="height" content="280">
		  </div>
			<div style="display:none" itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
    		<div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
      		<img alt="Logo - Tutto Gare" src="http://www.tuttogare.it/img/tuttogarepa-logo-software-sx.png"/>
		      <meta itemprop="url" content="http://www.tuttogare.it/img/tuttogarepa-logo-software-sx.png">
		      <meta itemprop="width" content="960">
		      <meta itemprop="height" content="280">
		    </div>
    		<meta itemprop="name" content="Tutto Gare - Powered By Studio Amica">
  		</div>
  		<meta itemprop="datePublished" content="<?= date('c',strtotime($record_notizia["data"])) ?>"/>
			<meta itemprop="dateModified" content="<?= date('c',strtotime($record_notizia["data"])) ?>"/>
			<?
		echo echo_calendario(mysql2date($record_notizia["data"])) . $record_notizia["testo"];
		?><div class="clear"></div><?
		if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
			$public = true;
			?>
      <div class="box"><h2>Allegati</h2>
        <table width="100%" id="tab_allegati">
        <?
     			while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
						include($root."/allegati/tr_allegati.php");
					}
				?>
        </table>
			</div>
      <?
		}
		?>
		</div>
		<?
	} else {
		echo "<h1>Notizia non trovata</h1>";
	}
	include_once($root."/layout/bottom.php");
	?>
