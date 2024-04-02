<?
	$current_folder = substr(dirname($_SERVER['PHP_SELF']), 1);
?>
<div class="padding"></div>
<ul id="list_menu">
	<li class="first_level" ord="0"><a href="/index.php" <?= empty($current_folder) && $_SERVER['PHP_SELF'] == "/index.php" ? 'class="attuale"' : null ?> title="<?= traduci('Homepage') ?>"><?= traduci('Homepage') ?></a></li>
	<?
	if (registrazione_abilitata()) {
		$href="/operatori_economici/registrazione.php";
		?>
		<li class="first_level" ord="0"><a href="<?= $href ?>" title="<?= traduci('registrazione-oe') ?>"><?= traduci('registrazione-oe') ?></a></li>
	 	<?
	}

	if(! empty($_SESSION["menu"]["timestamp"]) && time() - $_SESSION["menu"]["timestamp"] > 1800) unset($_SESSION["menu"]);
	if(empty($_SESSION["menu"]["timestamp"])) $_SESSION["menu"]["timestamp"] = time();
	if(empty($_SESSION["menu"]["sezione"])) {
		$strsql  = "SELECT sezione FROM b_pagina WHERE attivo = 'S' AND sezione <> '' ";
		if (isset($_SESSION["ente"])) {
			$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
			$strsql.= " AND codice_ente = :codice_ente ";
		} else {
			$strsql.= " AND codice_ente = 0 ";
		}
		$strsql .= " GROUP BY sezione ORDER BY ordinamento";
		$ris = $pdo->bindAndExec($strsql,$bind);
		$_SESSION["menu"]["sezione"] = $ris->fetchAll(PDO::FETCH_ASSOC);
	}

	if (count($_SESSION["menu"]["sezione"]) > 0) {
		foreach ($_SESSION["menu"]["sezione"] as $rec) {
			$sezione = $rec["sezione"];
			echo "<li class=\"first_level\"><a>" . $sezione . "</a><ul>";
			$bind_sezione = array(":sezione"=>$sezione);
			$strsql = "SELECT * FROM b_pagina WHERE attivo ='S' AND sezione = :sezione ORDER BY ordinamento";
			$risultato = $pdo->bindAndExec($strsql,$bind_sezione);
			if ($risultato->rowCount() > 0) {
				while($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
					$codice	= $record["codice"];
					$titolo = $record["titolo"];
					$href = "/pagine/id".$codice."-".$titolo;
					$href = str_replace('"',"",$href);
					$href = str_replace(' ',"-",$href);
					$link = $record["link"];
					$tipologia = $record["tipologia"];
					switch($tipologia) {
						case "HTML": echo "<li><a href=\"" . $href ."\" title=\"" . $titolo . "\">" . $titolo . "</a></li>"; break;
						case "Link": echo "<li><a href=\"" . $link ."\" title=\"" . $titolo . "\">" . $titolo . "</a></li>"; break;
					}
				}
			}
			echo "</ul></li>";
		}
	}

	if(empty($_SESSION["menu"]["pagine"])) {
		$strsql = "SELECT * FROM b_pagina WHERE attivo ='S' AND (sezione = '' OR sezione IS NULL) ";
		if (isset($_SESSION["ente"])) {
			$strsql.= " AND codice_ente = :codice_ente ";
		} else {
			$strsql.= " AND codice_ente = 0 ";
		}
		$strsql.= " ORDER BY ordinamento";
		$ris = $pdo->bindAndExec($strsql,$bind);
		$_SESSION["menu"]["pagine"] = $ris->fetchAll(PDO::FETCH_ASSOC);
	}


	if (count($_SESSION["menu"]["pagine"]) > 0) {
		foreach ($_SESSION["menu"]["pagine"] as $record) {
			$codice	= $record["codice"];
			$titolo = $record["titolo"];
			$href = "/pagine/id".$codice."-".$titolo;
			$href = str_replace('"',"",$href);
			$href = str_replace(' ',"-",$href);
			$link = $record["link"];
			$tipologia = $record["tipologia"];
			$ordinamento = $record["ordinamento"];
			switch($tipologia) {
				case "HTML": echo "<li class=\"first_level\" ord=\"".$ordinamento."\"><a id=\"menu_pag_" . $codice . "\" href=\"" . $href ."\" title=\"" . $titolo . "\">" . $titolo . "</a></li>"; break;
				case "Link": echo "<li class=\"first_level\" ord=\"".$ordinamento."\"><a id=\"menu_pag_" . $codice . "\" href=\"" . $link ."\" title=\"" . $titolo . "\">" . $titolo . "</a></li>"; break;
			}
		}
	}

	if(empty($_SESSION["menu"]["moduli"])) {
		$strsql = "SELECT b_moduli.* FROM b_moduli WHERE b_moduli.attivo = 'S' AND b_moduli.menu = 'S'";
		if (!isset($_SESSION["ente"])) {
			 $strsql.= " AND b_moduli.admin = 'S' ";
		} else {
			$strsql.= " AND (b_moduli.ente = 'S' AND (b_moduli.tutti_ente = 'S' OR (b_moduli.tutti_ente = 'N' AND b_moduli.codice IN (SELECT cod_modulo FROM r_moduli_ente WHERE cod_ente = :codice_ente))))";
		}
		$ris = $pdo->bindAndExec($strsql,$bind);
		$_SESSION["menu"]["moduli"] = $ris->fetchAll(PDO::FETCH_ASSOC);
	}


	if (count($_SESSION["menu"]["moduli"]) > 0) {
		foreach ($_SESSION["menu"]["moduli"] as $record) {
			$radice = $record["radice"];
			$titolo = traduci($record["titolo"]);
			$ordinamento = $record["ordinamento"];
			$descrizione = $record["descrizione"];

			echo "<li class=\"first_level\"  ord=\"".$ordinamento."\"><a id=\"menu_mod_".$radice."\" href=\"/". $radice . "/\" title=\"" . $descrizione . "\">" . $titolo;
			if (file_exists($root."/".$radice."/badge.php")) {
				include($root."/".$radice."/badge.php");
			}
			echo "</a></li>";
		}
	}

	if(empty($_SESSION["menu"]["menu_moduli"])) {
		$strsql = "SELECT b_moduli_menu.* FROM b_moduli_menu JOIN b_moduli ON b_moduli_menu.codice_modulo = b_moduli.codice WHERE b_moduli.attivo = 'S' ";
		if (!isset($_SESSION["ente"])) {
			$strsql.= " AND b_moduli.admin = 'S' ";
		} else {
			$strsql.= " AND (b_moduli.ente = 'S' AND (b_moduli.tutti_ente = 'S' OR (b_moduli.tutti_ente = 'N' AND b_moduli.codice IN (SELECT cod_modulo FROM r_moduli_ente WHERE cod_ente = :codice_ente))))";
		}

		$ris = $pdo->bindAndExec($strsql,$bind);
		$_SESSION["menu"]["menu_moduli"] = $ris->fetchAll(PDO::FETCH_ASSOC);
	}


	if (count($_SESSION["menu"]["menu_moduli"]) > 0) {
		foreach ($_SESSION["menu"]["menu_moduli"] as $record) {
			$radice = $record["radice"];
			$titolo = traduci($record["titolo"]);
			$ordinamento = $record["ordinamento"];
			$descrizione = $record["descrizione"];
			$attivo = $radice == $current_folder ? 'class="attuale"' : null;
			echo "<li class=\"first_level\"  ord=\"".$ordinamento."\"><a id=\"menu_mod_".$radice."\" ".$attivo." href=\"/". $radice . "/\" title=\"" . $descrizione . "\">" . $titolo;
			if (file_exists($root."/".$radice."/badge.php")) {
				include($root."/".$radice."/badge.php");
			}
			echo "</a></li>";
		}
	}
	if(! empty($_SESSION["ente"]) && !$hide_amica) {
		?><li class="first_level" ord="99"><a href="/assistenza.php" title="<?= traduci("Supporto") ?>" target="_blank"><?= traduci("Supporto") ?></a></li><?
	}
	?>
</ul>
<?
if (isset($_SESSION["codice_utente"]) && !is_operatore()) {
	if(empty($_SESSION["menu"]["moduli_amministratore"])) {
		$bind = array();
		$bind[":codice_utente"] = $_SESSION["codice_utente"];
		$bind[":gerarchia"] = $_SESSION["gerarchia"];
		$strsql  = "SELECT b_moduli.radice, b_moduli.titolo, b_moduli.descrizione, b_moduli.glyph FROM b_moduli LEFT JOIN r_moduli_utente ON b_moduli.codice = r_moduli_utente.cod_modulo ";
		$strsql .= " WHERE b_moduli.nascosto = 'N' AND b_moduli.gerarchia >=  :gerarchia AND b_moduli.attivo = 'S' AND (";
		if (!empty($_SESSION["ente"])) {
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql.= " (b_moduli.ente = 'S' AND (b_moduli.tutti_ente = 'S' OR (b_moduli.tutti_ente = 'N' AND b_moduli.codice IN (SELECT cod_modulo FROM r_moduli_ente WHERE cod_ente = :codice_ente))))";

			if ($_SESSION["ente"]["permit_cross"]=="N" && $_SESSION["ente"]["codice"] != $_SESSION["record_utente"]["codice_ente"] && $_SESSION["gerarchia"]!=="0") {
				$strsql.= " AND b_moduli.cross_p = 'S' ";
			}
		} else {
			$strsql.= " b_moduli.admin = 'S' ";
		}
		$strsql .= ") AND (b_moduli.tutti_utente = 'S' OR (b_moduli.tutti_utente = 'N' AND r_moduli_utente.cod_utente = :codice_utente))
								GROUP BY b_moduli.radice, b_moduli.titolo, b_moduli.descrizione ORDER BY b_moduli.ordinamento, b_moduli.codice ";
		$ris = $pdo->bindAndExec($strsql,$bind);
		$_SESSION["menu"]["moduli_amministratore"] = $ris->fetchAll(PDO::FETCH_ASSOC);
	}

	if (count($_SESSION["menu"]["moduli_amministratore"]) > 0) {
		$sezione_attuale = "";
		$prima = true;
		?>
		<div id="menu_moduli">
			<ul>
				<?
				foreach ($_SESSION["menu"]["moduli_amministratore"] as $record) {
					$radice = $record["radice"];
					$titolo = $record["titolo"];
					$descrizione = $record["descrizione"];
					$attivo = $radice == $current_folder ? 'class="attuale"' : null;

          echo "<li><a ".$attivo." href='/". $radice . "/' title='". $descrizione . "'>";
					if ($record["glyph"] != "") {
					 echo "<span class='".$record["glyph"]."'></span>&nbsp;&nbsp;";
					} else if (file_exists($root."/".$radice."/icon.png")) {
						echo "<img src='/". $radice . "/icon.png' alt='". $titolo . "'>";
					}
					echo $titolo;
					if (file_exists($root."/".$radice."/badge.php")) {
						include($root."/".$radice."/badge.php");
					}
					echo "</a></li>";
				}
				?>
			</ul>
		</div>
		<?
	}
} else if (is_operatore() && isset($_SESSION["ente"])) {
	if(empty($_SESSION["menu"]["moduli_operatori"])) {
		$strsql = "SELECT b_moduli_operatori.* FROM b_moduli_operatori LEFT JOIN b_moduli ON b_moduli_operatori.codice_modulo = b_moduli.codice WHERE ";
		$strsql.= "b_moduli_operatori.codice_modulo = 0 OR (b_moduli.attivo = 'S' AND (b_moduli.ente = 'S' AND (b_moduli.tutti_ente = 'S' OR (b_moduli.tutti_ente = 'N' AND b_moduli.codice IN (SELECT cod_modulo FROM r_moduli_ente WHERE cod_ente = :codice_ente)))))";
		$strsql.= "ORDER BY b_moduli_operatori.ordinamento";
		$ris = $pdo->bindAndExec($strsql,$bind);

		$_SESSION["menu"]["moduli_operatori"] = $ris->fetchAll(PDO::FETCH_ASSOC);
	}
	if (count($_SESSION["menu"]["moduli_operatori"]) > 0) {
	?>
		<div id="menu_moduli">
			<ul>
			<?
				foreach ($_SESSION["menu"]["moduli_operatori"] as $record) {
					echo "<li><a href=\"/". $record["radice"] . "/\" title=\"" . $record["descrizione"] . "\">";
					if ($record["glyph"] != "") {
					 echo "<span class='".$record["glyph"]."'></span>&nbsp;&nbsp;";
					}
					echo traduci($record["titolo"]);
					if (file_exists($root."/".$record["radice"]."/badge.php")) {
						include($root."/".$record["radice"]."/badge.php");
					}
					echo "</a></li>";
				}
				?>
			</ul>
		</div>
	<?
	}
}
?>
