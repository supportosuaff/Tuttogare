<?
	@session_start();

	if (!isset($_SESSION["ipo"])) $_SESSION["ipo"] = false;
	if (!isset($_SESSION["bigText"])) $_SESSION["bigText"] = false;

	if (isset($_GET["ipo"])) $_SESSION["ipo"] = ($_SESSION["ipo"]==true) ? false : true;
	if (isset($_GET["bigText"])) $_SESSION["bigText"] = ($_SESSION["bigText"]==true) ? false : true;

	include_once($root."/inc/funzioni.php");
	check_utente((isset($change_pwd)) ? true : false);
  $bind =array(":dominio"=>$_SERVER["SERVER_NAME"]);
  $strsql = "SELECT * FROM b_enti WHERE dominio = :dominio AND attivo = 'S'";
  $risultato = $pdo->bindAndExec($strsql,$bind);
	$_SESSION["numero_assistenza"] = $config["numero_verde"];
  $_SESSION["email_assistenza"] = $config["email_assistenza"];
  if ($risultato->rowCount() > 0) {
   $ente = $risultato->fetch(PDO::FETCH_ASSOC);
	 if ($ente["hide_amica"]=="S") $hide_amica = true;
	 $_SESSION["check_pec"] = false;
	 if (!empty($ente["password"])) $_SESSION["check_pec"] = true;
	 unset($ente["smtp"]);
	 unset($ente["smtp_port"]);
	 unset($ente["usa_ssl"]);
	 unset($ente["password"]);
   $_SESSION["ente"] = $ente;
   $_SESSION["config"]["nome_sito"] = "Portale gare - " . $ente["denominazione"];
	 $_SESSION["config"]["link_sito"] = $config["protocollo"] . $ente["dominio"];
	 $_SESSION["config"]["agg_normativi"] = $ente["agg_normativi"];

	 if(! empty($_SESSION["ente"]["email_assistenza_oe"])) $_SESSION["email_assistenza"] = $_SESSION["ente"]["email_assistenza_oe"];

	 if ($ente["ambienteTest"] == "S") {
		$_SESSION["developEnviroment"] = true;
	 }
	 if ($hide_amica) {
	 	$config["nome_sito"] = "";
	 	$_SESSION["config"]["nome_sito"] = "";
	 }
   $strsql = "SELECT * FROM b_interfaccia WHERE codice_ente = :codice_ente";
   $bind = array(":codice_ente"=>$ente["codice"]);
   $risultato = $pdo->bindAndExec($strsql,$bind);
   if ($risultato->rowCount() > 0) {
  	 $interfaccia = $risultato->fetch(PDO::FETCH_ASSOC);
   }
   unset($open_page);
	 $_SESSION["numero_assistenza"] = $ente["numero_assistenza_oe"];
	 if (isset($_SESSION["gerarchia"]) && $_SESSION["gerarchia"] <= 2) $_SESSION["numero_assistenza"] = $ente["numero_assistenza_sa"];
  }
  $risultato->closeCursor();
  if (!isset($_SESSION["developEnviroment"])) $_SESSION["developEnviroment"] = false;

  if (!isset($echo_layout)) $echo_layout = true;
  if (empty($ente) && empty($_SESSION["codice_utente"])) $echo_layout = false;
	if (isset($pagina_login)) $echo_layout = false;
	if (isset($open_page) && !isset($ente) && !isset($_SESSION["codice_utente"])) $echo_layout = false;


	if (!$echo_layout && !isset($pagina_login) && !isset($pagina_reset)) {
		echo "<h1>Impossibile accedere</h1>";
		die();
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="it">
<head>
<!-- <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"> -->
<?

	$_nome_sito = $config["nome_sito"];
	
	if(empty($subtitle)) {
		$subtitle = "";
		if (isset($_GET["titolo"])) {
			 $subtitle = " - " . str_replace("-"," ",$_GET["titolo"]);
		 } else if (isset($_SESSION["ente"])) {
			 $subtitle = " - " . $_SESSION["ente"]["denominazione"];
			 $meta["title"] = $_nome_sito . $subtitle;
		 } else {
			 $subtitle = " - Software per la gestione interamente telematica delle gare d'appalto";
		 }
	}

	if(!empty($meta["title"]) && !empty($meta["description"]) && !empty($meta["keywords"])) {
		?>
		<title><? if (isset($_SESSION["ente"])) echo $_SESSION["ente"]["denominazione"] . " - "; ?><?= $meta["title"] ?></title>
		<meta name="title" content="<?= $meta["title"] ?>">
		<meta name=description content="<?= $meta["description"] ?>">
		<meta name="keywords" content="<?= $meta["keywords"] ?>">
		<?
	} else {
		?>
		<title><? echo $_nome_sito . $subtitle ?></title>
		<meta name="title" content="<? echo $_nome_sito . $subtitle ?>">
		<meta name=description content="TuttoGare è la piattaforma web based progettata per ottimizzare tutti i processi caratteristici di sourcing e procurement nel Public Sector e offre una totale copertura funzionale delle componenti del processo di sourcing e procurement.">
		<!-- <meta name="keywords" content="gare,gare online,gare telematiche,gare interamente telematiche,stazioneappalti,stazione appalti,tuttogare,tutto gare,procurement,e-procurement,e procurement,sourcing,e-sourcing,e sourcing,appalti,codice appalti,nuovo codice appalti,direttiva europea 2014/24/UE,bosetti,bosetti e gatti,bosetti e gatti e partners, albo dei fornitori, mercato elettronico, gare telematiche, sistema dinamico d'acquisizione,accordo quadro,compila bandi,integrazioni,vendor rating,pubblica amministrazione,servizi,forniture,lavori pubblici,controllo spesa,razionalizzazione della spesa,tuttogare,gestione telemtica,procedimenti di gara,procedimenti di gara telematici,soursing,procurement,public sector,realizzazione bandi di gara,capitolati di gara telematici,disciplinari telematici,codice appalti,163 del 2006 bosetti,dlgs 50 del 2016,gara d appalto,163 2006 bosetti,forum PA,forum pubblica amministrazione,forum pa roma"/>-->
		<meta name="keywords" content="smart cig,acquistinretepa,simog,acquistinrete,appalti e contratti,acquistiinrete,appalto,appalti pubblici,acquistiinretepa,gare d appalto,contratti,contratti pubblici">
		<?
	}
?>
<meta http-equiv="Cache-control" content="no-cache">
<link rel="stylesheet" type="text/css" href="/css/graphics.css">
<link rel="stylesheet" type="text/css" href="/css/chosen.min.css">
<link rel="stylesheet" type="text/css" href="/css/datatables.min.css">
<link rel="stylesheet" type="text/css" href="/css/jquery.datetimepicker.css">
<link rel="stylesheet" type="text/css" href="/css/jquery.countdown.css">
<link rel="stylesheet" type="text/css" href="/css/font-awesome.min.css">
<? if ($_SESSION["ipo"]) { ?><link rel="stylesheet" type="text/css" href="/css/ipo.css"><? } ?>
<? if ($_SESSION["bigText"]) { ?><link rel="stylesheet" type="text/css" href="/css/bigText.css"><? } ?>


<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
<link rel="manifest" href="/favicon/site.webmanifest">
<link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">
<link rel="stylesheet" type="text/css" href="/js/colorpicker-master/jquery.colorpicker.css">
<link type="text/css" href="/css/redmond/jquery-ui-1.10.4.custom.min.css" rel="Stylesheet">
<link rel="stylesheet" type="text/css" href="/css/extended-css.css">
<script type="text/javascript" src="/jquery.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.10.4.custom.min.js"></script>
<script type="text/javascript" src="/js/moment.min.js"></script>
<script type="text/javascript" src="/ckeditor4/ckeditor.js"></script>
<script>CKEDITOR.env.isCompatible = true;</script>
<script type="text/javascript" src="/ckeditor/adapters/jquery.js"></script>
<script type="text/javascript" src="/js/timepicker.js"></script>
<script type="text/javascript" src="/js/password_strenght.js"></script>
<script type="text/javascript" src="/js/chosen.jquery.min.js"></script>
<!-- <script type="text/javascript" src="/js/jquery.geocomplete.min.js"></script> -->
<script type="text/javascript" src="/js/datatables.min.js"></script>
<script type="text/javascript" src="/js/dataTables.Defaults.js"></script>
<script type="text/javascript" src="/js/colorpicker-master/jquery.colorpicker.js"></script>
<script type="text/javascript" src="/js/jquery.datetimepicker.js"></script>
<script type="text/javascript" src="/js/jquery.ui.datepicker-it.js"></script>
<script type="text/javascript" src="/personal.js?date=20200306"></script>
<?
if (isset($interfaccia) && ((isset($_SESSION["ipo"]) && $_SESSION["ipo"] == false) || (!isset($_SESSION["ipo"])))) {
  ?>
  <style>
  	<? if ($interfaccia["a"] != "") { ?>a { color: #<? echo $interfaccia["a"]; ?> !important} .descr_ente {color: #<? echo $interfaccia["a"]; ?> !important}<? } ?>
  	<? if ($interfaccia["a_hover"] != "") { ?>a:hover { color: #<? echo $interfaccia["a_hover"]; ?> !important}<? } ?>
  	<? if ($interfaccia["menu"] != "") { ?>#menu { background-color: #<? echo $interfaccia["menu"]; ?> }<? } ?>
  	<? if ($interfaccia["menu_background_a"] != "") { ?>#list_menu li { background-color: #<? echo $interfaccia["menu_background_a"]; ?> }<? } ?>
  	<? if ($interfaccia["menu_color_a"] != "") { ?>#list_menu li a, .descr_ente h2, .descr_ente a, .info_add { color: #<? echo $interfaccia["menu_color_a"]; ?> !important} .descr_ente div.info_add {border-bottom: solid 2px #<? echo $interfaccia["menu_color_a"]; ?> !important}<? } ?>
  	<? if ($interfaccia["menu_background_a_hover"] != "") { ?>#list_menu li a:hover { background-color: #<? echo $interfaccia["menu_background_a_hover"]; ?> }<? } ?>
  	<? if ($interfaccia["menu_color_a_hover"] != "") { ?>#list_menu li a:hover { color: #<? echo $interfaccia["menu_color_a_hover"]; ?> !important}<? } ?>
  	<? if ($interfaccia["menu_moduli_background_a"] != "") { ?>#menu_moduli li { background-color: #<? echo $interfaccia["menu_moduli_background_a"]; ?> }<? } ?>
  	<? if ($interfaccia["menu_moduli_color_a"] != "") { ?>#menu_moduli li a { color: #<? echo $interfaccia["menu_moduli_color_a"]; ?> !important}<? } ?>
  	<? if ($interfaccia["menu_moduli_background_a_hover"] != "") { ?>#menu_moduli li a:hover { background-color: #<? echo $interfaccia["menu_moduli_background_a_hover"]; ?> }<? } ?>
  	<? if ($interfaccia["menu_moduli_color_a_hover"] != "") { ?>#menu_moduli li a:hover { color: #<? echo $interfaccia["menu_moduli_color_a_hover"]; ?> !important}<? } ?>
  	<? if ($interfaccia["utente_background_a"] != "") { ?>#utente li { background-color: #<? echo $interfaccia["utente_background_a"]; ?> }<? } ?>
  	<? if ($interfaccia["utente_color_a"] != "") { ?>#utente li a { color: #<? echo $interfaccia["utente_color_a"]; ?> !important}<? } ?>
  	<? if ($interfaccia["utente_background_a_hover"] != "") { ?>#utente li a:hover { background-color: #<? echo $interfaccia["utente_background_a_hover"]; ?> }<? } ?>
  	<? if ($interfaccia["utente_color_a_hover"] != "") { ?>#utente li a:hover { color: #<? echo $interfaccia["utente_color_a_hover"]; ?> !important}<? } ?>
  	<? if ($interfaccia["bottom"] != "") { ?>body { background-color: #<? echo $interfaccia["bottom"]; ?> !important }<? } ?>
  	<? if ($interfaccia["bottom_color"] != "") { ?>#bottom { color: #<? echo $interfaccia["bottom_color"]; ?> !important}<? } ?>
  	<? if ($interfaccia["bottom_a"] != "") { ?>#bottom a { color: #<? echo $interfaccia["bottom_a"]; ?> !important}<? } ?>
  	<? if ($interfaccia["menu_top"] != "") { ?>#menu-top { background-color: #<? echo $interfaccia["menu_top"]; ?> !important}<? } ?>
		<? if ($interfaccia["menu_top_a"] != "") { ?>#menu-top a, #menu-top .left li a, #menu-top .right li { color: #<? echo $interfaccia["menu_top_a"]; ?> !important}<? } ?>
		<? if ($interfaccia["menu_active_border"]!="") { ?>.attuale, #menu li a.attuale { border-color: #<?= $interfaccia["menu_active_border"] ?> }<? } ?>  	<? if ($interfaccia["menu_active_background"]!="") { ?>.attuale, #menu li a.attuale { background-color: #<?= $interfaccia["menu_active_background"] ?> }<? } ?>
  </style>
  <?
}
?>
</head>
<body>
	<script>
		var $buoop = {required:{e:0,f:0,o:0,s:0,c:0},insecure:true,unsupported:true,api:2018.10 };
		function $buo_f(){
		 var e = document.createElement("script");
		 e.src = "//browser-update.org/update.min.js";
		 document.body.appendChild(e);
		};
		try {document.addEventListener("DOMContentLoaded", $buo_f,false)}
		catch(e){window.attachEvent("onload", $buo_f)}
	</script>
	<div id="wait"></div>
	<iframe name="operazioni" id="operazioni" style="display:none" ></iframe>
	<form id="exportPDF" action="/moduli/exportPDF.php" target="_blank" rel="validate" method="post">
		<div>
			<input type="hidden" class="espandi" id="exp_corpo" name="corpo" value="" rel="S;0;0;A">
			<input type="hidden" class="espandi" name="file_title" id="exp_file_title" value="">
			<input type="hidden" class="espandi" name="orientamento" id="exp_orientamento" value="P">
			<input type="hidden" class="espandi" name="formato" id="exp_formato" value="A4">
		</div>
	</form>
	<?

	if ($echo_layout) {
		?>
    <div id="contenitore">
      <div id="menu">
    	 	<div id="logo_ente">
      	<?
					if (!isset($ente)) {
						?>
	    			<img src="/img/logo-tuttogare-pa-big.png" width="100%" alt="<? echo $config["nome_sito"] ?>">
						<?
					} else {
						if (!empty($ente["url"])) echo "<a href=\"".$ente["url"]."\" target=\"_blank\" title=\"sito istituzionale\">";
						?>
			    		<img src="/documenti/enti/<? echo $ente["logo"] ?>" style="max-width:95%; max-height:215px" alt="<? echo $ente["denominazione"] ?>">
						<?
						if (!empty($ente["url"])) echo "</a>";
					}
				?>
       	</div>
       	<?
       		if(isset($ente)) {
       			?>
       			<div class="descr_ente">
       				<h2 style="text-transform:uppercase;"><?= $ente["denominazione"] ?></h2>
       				<div class="info_add">
       					<?
       					echo trim($ente["indirizzo"]) . " - " . $ente["citta"] . " (" . $ente["provincia"] . ")<br>";
								if ($ente["telefono"]!="") echo '<i class="fa fa-phone"></i> ' . $ente["telefono"] . '<br>';
								// if ($ente["email"]!="") echo '<i class="fa fa-envelope"></i> <a href="mailto:' . $ente["email"] . '">' . $ente["email"] . '</a><br>';
								// if ($ente["pec"]!="") echo '<i class="fa fa-envelope"></i> <a href="mailto:' . $ente["pec"] . '">' . $ente["pec"] . '</a><br>';
       					?>
       				</div>
       			</div>
       			<?
       		}
       	?>
				<? include_once($root."/layout/menu_dev.php"); ?>
				<? include_once($root."/moduli/utente.php"); ?>
			</div>
			<div id="contenuto">
				<? include $root.'/layout/menu_top.php'; ?>
				<? if (isset($_SESSION["gerarchia"]) && $_SESSION["gerarchia"] <= 2 && isset($form_comunicazione)) {
				?>
				<div id="comunicazione">
					<? include($root."/moduli/form_invio.php"); ?>
				</div>
	      <?
	     }
	     ?>
	     <div id="contenuto_top">
			 <?
			if (isset($_SESSION["codice_utente"]) && !is_operatore() && !pecConfigurata()) {
					?>
					<h3 class="ui-state-error">
						<span class="fa fa-alert-sign"></span> Attenzione il sistema non è in grado in inviare comunicazioni PEC.
						<?
							if ($_SESSION["gerarchia"] <= 1) {
								?>
								<a href="/impostazioni/pec/" title="PEC">Cliccare qui per procedere alla configurazione.</a>
								<?
							} else {
								?>
								Si prega di rivolgersi ad un utente amministratore per procedere alla configurazione.
								<?
							}
						?>
					</h3><br>
					<?
			}
	}
	if ($manutenzione == true && isset($_SESSION["ente"]) && !$_SESSION["developEnviroment"] && !isset($pagina_manutenzione)) {
		echo '<meta http-equiv="refresh" content="0;URL=/manutenzione.php">';
		die();
	}
	if (!$echo_layout && !isset($index_page) && !isset($pagina_login) && !isset($pagina_reset)) {
		?>
		<div class="container">
		<?
	}
?>
<noscript>
	<div class="padding">
		<div class="">
			<img src="/img/alert.png" style="vertical-align:middle" alt="attenzione"> E' necessario abilitare Javascript per utilizzare il portale
		</div>
	</div>
</noscript>
