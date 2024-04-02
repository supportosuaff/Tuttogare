<ul id="utente"><?
			if (!isset($_SESSION["codice_utente"])) { ?>
				<li><a href="/accesso.php" title="Accedi all'area riservata"><span class="fa fa-lock"></span> <?= traduci("Accedi") ?></a></li>
          	<? } else {
					$link = "/user/id" . $_SESSION["codice_utente"] . "-edit";
					if ($_SESSION["tipo_utente"] == "OPE" || $_SESSION["tipo_utente"] == "PRO") $link = "/operatori_economici/id" . $_SESSION["codice_utente"] . "-edit";
					if (empty($_SESSION["ente"])) {
						echo "<li><a href=\"/scadenzario/\"><span class='fa fa-calendar'></span>&nbsp;&nbsp;Scadenzario</a></strong></li>";
					} else {
						$bind = array();
						$strsql  = "SELECT * FROM b_moduli JOIN r_moduli_ente ON b_moduli.codice = r_moduli_ente.cod_modulo
												WHERE b_moduli.radice = 'user' AND b_moduli.attivo = 'S' AND r_moduli_ente.cod_ente = :codice_ente ";
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$risultato_user = $pdo->bindAndExec($strsql,$bind);
						if ($risultato_user->rowCount() == 0 && $_SESSION["gerarchia"]==="0") {
							echo "<li><a href=\"/user/\"><span class='fa fa-users'></span>&nbsp;&nbsp;Utenti</a></strong></li>";
						}
					}
					echo "<li><a href=\"" . $link . "\"><span class='fa fa-user'></span>&nbsp;&nbsp;" . $_SESSION["nome_utente"] . "</a></strong></li>";
					echo "<li class=\"logout\"><a href=\"#\" onClick=\"logout()\"><span class='fa fa-power-off'></span>&nbsp;&nbsp;logout</a></li>";

					} ?></ul>
