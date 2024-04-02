<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	if ($edit) {
	echo "<h1>IMPOSTAZIONI</h1>";

		$strsql = "SELECT * FROM b_impostazioni WHERE attivo = 'S' AND gerarchia >= :gerarchia";
		if (!isset($_SESSION["ente"])) {
			$strsql .= " AND generali = 'S' ";
		} else {
			$strsql .= " AND ente = 'S' ";
			if(!empty($_SESSION["ente"]) && (($_SESSION["record_utente"]["codice_ente"] != $_SESSION["ente"]["codice"]) && $_SESSION["gerarchia"] > 0)) {
				$strsql .= " AND utente_esterno = 'S' ";
			}
			if ($_SESSION["record_utente"]["codice_ente"] != 0) $strsql.= " AND utente_ente = 'S'";
		}
		$strsql .= "ORDER BY ordinamento";
		$risultato = $pdo->bindAndExec($strsql,array(":gerarchia"=>$_SESSION["gerarchia"]));
		if ($risultato->rowCount()>0) {
			while($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
				?>
                	<a class="tiles" href="<? echo $record["directory"] ?>" title="<? echo $record["nome"] ?>">
										<?
											if ($record["glyph"] != "") { ?>
												<span class='<?= $record["glyph"] ?> fa-3x'></span>
											<? } else { ?>
                    		<img src="<? echo $record["directory"] ?>/icon.png" alt="<? echo $record["nome"] ?>">
											<? } ?><br>
                    <? echo $record["nome"] ?>
                    </a>
                <?
			}
		} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	include_once($root."/layout/bottom.php");
	?>
