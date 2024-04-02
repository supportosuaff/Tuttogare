<?
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	if (!isset($_SESSION["ente"]) && !isset($_SESSION["codice_utente"])) $open_page = true;
	if(!empty($_GET["cod"])) {
		$codice = $_GET["cod"];
		$bind = array(':codice' => $codice);
		$strsql = "SELECT * FROM b_pagina WHERE codice = :codice ";
		if (isset($_SESSION["ente"])) {
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql.= "AND (codice_ente = :codice_ente OR codice_ente = 0)";
		}
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount() > 0) {
			$record_pagina = $risultato->fetch(PDO::FETCH_ASSOC);
		} else {
			header('Location : /index.php');
			die();
		}
	} else {
		header('Location : /index.php');
		die();
	}

	$meta = array(
		"title" => $config["nome_sito"] . " - " . $record_pagina["title"],
		"description" => $record_pagina["description"],
		"keywords" => $record_pagina["keywords"]
		);

	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("pagine",$_SESSION["codice_utente"]);
	}

	if(!empty($record_pagina)) {
		if ($edit) {
		?>
    	<div style="text-align:right">
      	<input type="image" onClick="window.location.href='/pagine/id<? echo $record_pagina["codice"] ?>-edit'" src="/img/edit.png" title="Modifica">
        <input type="image" onClick="elimina('<? echo $record_pagina["codice"] ?>','pagine');" src="/img/del.png" title="Elimina">
      </div>
  	<?
		}
		echo "<h2>" . $record_pagina["titolo"] . "</h2>";
		?><div class="clear"></div><?
		echo $record_pagina["testo"];
		?><div class="clear"></div><div class="note">Ultimo aggiornamento il <?= $record_pagina["timestamp"] ?></div><?
	} else {
		echo "<h1>Pagina non trovata</h1>";
	}
	include_once($root."/layout/bottom.php");
	?>
