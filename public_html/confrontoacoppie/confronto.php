<?
	if (isset($_GET["token"]) && isset($_GET["codice"]))
	{
		include_once("../../config.php");
		$pagina_login = true;
		include_once($root."/layout/top.php");

		$token = $_GET["token"];
		$codice_gara = $_GET["codice"];
		$errore = false;

		if (isset($_GET["logout"]))
		{
			$edit = false;
			$errore = false;
			unset($_SESSION["codice_commissario"]);
			unset($_SESSION["token"]);
			unset($_SESSION["commissario"]);
			echo '<meta http-equiv="refresh" content="0;URL=/confrontoacoppie/confronto.php?token='.$token.'&codice='.$codice_gara.'">';
			die();
		}
	}
	else
	{
		header("Location: /index.php");
    	die();
	}

	$edit = false;
	if (!isset($_SESSION["codice_commissario"]) && isset($_POST["password"]))
	{
		$password = md5($_POST["password"]);
		$bind = array();
		$bind[":codice_gara"] = $codice_gara;
		$bind[":token"] = $token;
		$sql_login = "SELECT * FROM `b_commissioni` WHERE `b_commissioni`.`token` = :token AND `b_commissioni`.`codice_gara` = :codice_gara AND `b_commissioni`.`valutatore` = 'S'";
		$ris_login = $pdo->bindAndExec($sql_login,$bind);
		if ($ris_login->rowCount() > 0)
		{
			$rec_login = $ris_login->fetch(PDO::FETCH_ASSOC);
			if (password_verify($password,$rec_login["password"])) {
				$_SESSION["codice_commissario"] = $rec_login["codice"];
				$_SESSION["token"] = $rec_login["token"];
				$_SESSION["commissario"] = $rec_login["titolo"] . " " . $rec_login["cognome"] . " " . $rec_login["nome"];
				echo '<meta http-equiv="refresh" content="0;URL=/confrontoacoppie/confronto.php?token='.$token.'&codice='.$codice_gara.'">';
				$edit = true;
			}
		}
		else
		{
			$edit = false;
			unset($_SESSION["codice_commissario"]);
			unset($_SESSION["token"]);
			unset($_SESSION["commissario"]);
			$errore = true;
		}
	}
	else if (isset($_SESSION["codice_commissario"]))
	{
		$bind = array();
		$bind[":codice_gara"] = $codice_gara;
		$bind[":codice_commissario"] = $_SESSION["codice_commissario"];
		$bind[":token"] = $token;

		$sql_login = "SELECT * FROM `b_commissioni` WHERE `b_commissioni`.`token` = :token AND `b_commissioni`.`codice_gara` = :codice_gara AND `b_commissioni`.`codice` = :codice_commissario";
		$ris_login = $pdo->bindAndExec($sql_login,$bind);
		if ($ris_login->rowCount() > 0)
		{
			$rec_login = $ris_login->fetch(PDO::FETCH_ASSOC);
			$_SESSION["codice_commissario"] = $rec_login["codice"];
			$_SESSION["token"] = $rec_login["token"];
			$_SESSION["commissario"] = $rec_login["titolo"] . " " . $rec_login["cognome"] . " " . $rec_login["nome"];
			$edit = true;
		}
		else
		{
			$edit = false;
			unset($_SESSION["codice_commissario"]);
			unset($_SESSION["token"]);
			unset($_SESSION["commissario"]);
			echo '<meta http-equiv="refresh" content="0;URL=/confrontoacoppie/confronto.php?token='.$token.'&codice='.$codice_gara.'">';
		}
	}


	?>
	<style type="text/css">
		.padding {
			padding:5px 20px;
		}
		<? if (!$edit) { ?>
		body {
			background-color: #999999;
		}
		<? } ?>
	</style>
	<?
	if ($edit && isset($_GET["codice"]))
	{
		$sql_tecnici = "SELECT * FROM b_criteri_punteggi WHERE economica = 'N' AND temporale = 'N'";
		$ris_tecnici = $pdo->query($sql_tecnici);
		$codice_tecnici = array();
		if ($ris_tecnici->rowCount() > 0) {
			while($rec_tecnici = $ris_tecnici->fetch(PDO::FETCH_ASSOC)) $codice_tecnici[] = $rec_tecnici["codice"];
		}
		?>
		<div id="contenuto_top" class="padding">
			<?
			$codice_gara = $_GET["codice"];
			$codice_lotto = (isset($_GET["lotto"]) ? $_GET["lotto"] : 0);
			$partecipanti = array();
			$valutazione = false;
			if (isset($_GET["partecipante"]) && isset($_GET["criterio"]) && is_numeric($_GET["partecipante"]) && is_numeric($_GET["criterio"]))
			{
				$valutazione = true;
				$partecipante_valutazione = $_GET["partecipante"];
				$criterio_valutazione = $_GET["criterio"];

				$bind = array();
				$bind[":codice_gara"] = $codice_gara;
				$bind[":criterio_valutazione"] = $criterio_valutazione;

				$sql_criteri_verifica = "SELECT * FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara AND codice = :criterio_valutazione  AND tipo = 'Q'";
				$ris_criteri_verifica = $pdo->bindAndExec($sql_criteri_verifica,$bind);
				if ($ris_criteri_verifica->rowCount() < 1)
				{
					echo '<meta http-equiv="refresh" content="0;URL=/confrontoacoppie/confronto.php?token='.$token.'&codice='.$codice_gara.'&lotto='.$codice_lotto.'">';
					die();
				}
				else
				{
					$rec_criteri_verifica = $ris_criteri_verifica->fetch(PDO::FETCH_ASSOC);
					$criterio_valutazione_codice_padre = $rec_criteri_verifica["codice_padre"];
					if ($criterio_valutazione_codice_padre != 0)
					{
						$bind = array();
						$bind[":codice_gara"] = $codice_gara;
						$bind[":criterio_valutazione"] = $criterio_valutazione_codice_padre;

						$sql_criteri_verifica_padre = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND b_valutazione_tecnica.codice = :criterio_valutazione AND b_criteri_punteggi.economica = 'N' AND  b_criteri_punteggi.temporale = 'N' AND b_valutazione_tecnica.tipo = 'Q'";
						$ris_criteri_verifica_padre = $pdo->bindAndExec($sql_criteri_verifica_padre,$bind);
						if ($ris_criteri_verifica_padre->rowCount() < 1)
						{
							echo '<meta http-equiv="refresh" content="0;URL=/confrontoacoppie/confronto.php?token='.$token.'&codice='.$codice_gara.'&lotto='.$codice_lotto.'">';
							die();
						}
					}
					else
					{
						if (!in_array($rec_criteri_verifica["punteggio_riferimento"],$codice_tecnici) || $rec_criteri_verifica["tipo"] != 'Q')
						{
							echo '<meta http-equiv="refresh" content="0;URL=/confrontoacoppie/confronto.php?token='.$token.'&codice='.$codice_gara.'&lotto='.$codice_lotto.'">';
							die();
						}
					}
				}
			}

			$bind = array();
			$bind[":codice_gara"] = $codice_gara;
			$bind[":codice_gestore"] = $_SESSION["ente"]["codice"];

			$sql_gara  = "SELECT b_gare.* FROM b_gare WHERE ";
			$sql_gara .= "b_gare.codice = :codice_gara ";
			$sql_gara .= "AND b_gare.data_scadenza < now() ";
			$sql_gara .= "AND b_gare.codice_gestore = :codice_gestore ";
			$sql_gara .= "AND b_gare.stato < 4 ";

			$ris_gara  = $pdo->bindAndExec($sql_gara,$bind); //invia la query contenuta in $sql_gara al database apero e connesso

			if 	($ris_gara->rowCount() > 0 )
			{
				$record_gara = $ris_gara->fetch(PDO::FETCH_ASSOC);
				$bind = array();
				$bind[":codice_gara"] = $record_gara["codice"];
				$sql_opzione = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 124";
				$ris_opzione = $pdo->bindAndExec($sql_opzione,$bind);
				if ($ris_opzione->rowCount() > 0) {
				?>
				<table class="table" width="100%">
					<tbody>
						<tr>
							<td width="80%"></td>
							<td><a class="submit_big" style="background-color:#999999; padding:5px;" href="/confrontoacoppie/confronto.php?token=<?= $token ?>&codice=<?=$codice_gara ?>">HOME</a></td>
							<td><a class="submit_big" style="background-color:#cc0000; padding:5px;" href="/confrontoacoppie/confronto.php?token=<?= $token ?>&codice=<?=$codice_gara ?>&logout">LOGOUT</a></td>
						</tr>
					</tbody>
				</table>
				<?
				$lotti = true;
				$bind = array();
				$bind[":codice_gara"] = $codice_gara;
				$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara";
				$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);

				if ($ris_lotti->rowCount() < 1)
				{
					$lotti = false;
				}
				else
				{
					$bind = array();
					$bind[":codice_gara"] = $codice_gara;
					$bind[":codice_lotto"] = $codice_lotto;
					$sql_lotto = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto";
					$ris_lotto = $pdo->bindAndExec($sql_lotto,$bind);
					if ($ris_lotto->rowCount() > 0)
					{
						$lotti = false;
						$rec_lotto = $ris_lotto->fetch(PDO::FETCH_ASSOC);
					}
				}

				if ($lotti)
				{
					$bind = array();
					$bind[":codice_gara"] = $codice_gara;
					$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara";
					$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);

					?>
					<table width="100%">
						<h1>CONFRONTO A COPPIE - GARA #<?= $record_gara["id"] ?></h1>
						<tr><th>Lotto</th><th width="10">Partecipanti</th></tr>
						<?
						while ($rec_lotti = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
							$bind = array();
							$bind[":codice_gara"] = $record_gara["codice"];
							$bind[":codice_lotto"] = $rec_lotti["codice"];
							$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND ammesso = 'S' AND escluso = 'N' AND (conferma = TRUE OR conferma IS NULL)";
							$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
							?>
							<tr>
							<td>
								<a class="submit_big" href ="confronto.php?token=<?= $token ?>&codice=<?= $record_gara["codice"] ?>&lotto=<?= $rec_lotti["codice"] ?>">
									<?= $rec_lotti["oggetto"] ?>
								</a>
							</td>
							<td style="text-align:center">
								<strong style="font-size:24px"><?= $ris_partecipanti->rowCount() ?></strong>
							</td></tr>
							<?
						}
						?>
					</table>
					<?
				}
				else
				{
					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$bind[":codice_lotto"] = $codice_lotto;
					$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND ammesso = 'S' AND escluso = 'N' AND (conferma = TRUE OR conferma IS NULL) ORDER BY codice";
					$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,$bind);
					if ($ris_partecipanti->rowCount() > 0)
					{
						?>
						<h1>CONFRONTO A COPPIE - GARA #<?= $record_gara["id"] . ($codice_lotto != 0 ? " LOTTO: " . $rec_lotto["oggetto"] : "") ?></h1>
						<h2>CALCOLO DELL&#39;OFFERTA ECONOMICAMENTE PI&Ugrave; VANTAGGIOSA - METODO AGGREGATIVO - COMPENSATORE</h2>
						<!-- <h2>BANDO DI GARA: <?= strtoupper($record_gara["oggetto"]) ?></h2> -->
						<div class="padding">
							<h2 style="cursor:pointer" onclick="toggle_partecipanti()">PARTECIPANTI <img height="16" src="/img/arrowDown.png"></h2>
							<? include_once('partecipanti.php'); ?>
						</div>
						<div class="padding">
							<?
							$bind = array();
							$bind[":codice_gara"] = $codice_gara;
							$sql_criteri = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' AND b_valutazione_tecnica.codice_padre = 0  AND b_valutazione_tecnica.tipo = 'Q' ORDER BY b_valutazione_tecnica.codice";
							$ris_criteri = $pdo->bindAndExec($sql_criteri,$bind);
							include_once('criteri.php');
							?>
						</div>
						<div class="padding" <? if ($valutazione) echo 'style="display: none"' ?>>
							<h2 style="cursor:pointer" onclick="toggle_triangolari()">TABELLE TRIANGOLARI <img height="16" src="/img/arrowDown.png"></h2>
							<?
							include_once('tabelle_triangolari.php');
							?>
						</div>
						<div class="padding" <? if ($valutazione) echo 'style="display: none"' ?>>
							<h2 style="cursor:pointer" onclick="toggle_confronti()">CONFRONTI:</h2>
							<?
							include_once('scegli_confronto.php');
							?>
						</div>
						<div class="padding">
							<?
							if ($valutazione) include_once('valutazione.php');
							?>
						</div>
						<div class="padding">
							<?
							if ($codice_lotto != 0 && !$valutazione) {
								?>
								<a class="submit_big" style="background-color:#999;" href="/confrontoacoppie/confronto.php?token=<?= $token ?>&codice=<?= $codice_gara ?>">CAMBIA LOTTO</a>
								<?
							}
							?>
						</div>
						<?
					}
					else
					{
						?>
						<div style="padding:200px 0px;">
							<h1 style="text-align:center">
								<span class="fa fa-exclamation-circle fa-3x"></span>
								<br>Nessun Partecipante Trovato!
							</h1>
						</div>
						<?
					}
				}
			}
			else
			{
				?>
				<div style="padding:200px 0px;">
					<h1 style="text-align:center">
						<span class="fa fa-exclamation-circle fa-3x"></span>
						<br>Nessun risultato 1!
					</h1>
				</div>
				<?
			}
		}
		?>
		</div>
		<?
	}
	else
	{
		?>
		<div id="div_login">
			<div style="padding:50px">
				<div style="text-align:center">
					<img src="/documenti/enti/<?= $_SESSION["ente"]["logo"] ?>" width="30%" alt="<?= $_SESSION["ente"]["denominazione"] ?>"><br>
					<strong><?= $_SESSION["ente"]["denominazione"] ?></strong><br><br>
					<div class="clear"></div>
				</div>
				<br>
				<form name="login" method="post" target="_self" rel="validate" action="/confrontoacoppie/confronto.php?token=<?= $token?>&codice=<?= $codice_gara ?>">
					<label for="Password">Password</label><br>
					<input type="password" name="password" title="Password" rel="S;3;16;A" class="titolo_edit <?= ($errore ? "ui-state-error" : "") ?>" maxlength="16" placeholder="Password"><br>
					<input type="submit" value="login" class="submit_big">
				</form>
				<? if ($errore) { ?>
				<div id="msg_login" style="color:#000; font-weight:bold;">
					Utente non riconosciuto
				</div>
				<? } ?>
				<br><img alt="Gara amica" width="150" src="/img/tuttogarepa-logo-software-sx.png">
			</div>
		</div>

		<?
	}
	?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$("#btn_valutazione").click(function(event) {
				event.preventDefault();
				$(this).hide();
				$('#partecipanti').hide();
				$('#elementi_valutazione').hide();
			});
		});
		function toggle_partecipanti()
		{
			var element = $('#partecipanti');
			if (!element.is(':visible'))
			{
				element.show();
				element.parent('div').find('img').attr('src', '/img/arrowUp.png');
			}
			else
			{
				element.hide();
				element.parent('div').find('img').attr('src', '/img/arrowDown.png');
			}
		}

		function toggle_confronti()
		{
			var element = $('#confronti');
			if (!element.is(':visible'))
			{
				element.show();
			}
			else
			{
				element.hide();
			}
		}

		function toggle_triangolari()
		{
			var element = $('.triang');
			if (!element.is(':visible'))
			{
				element.show();
				element.parent('div').find('img').attr('src', '/img/arrowUp.png');
			}
			else
			{
				element.hide();
				element.parent('div').find('img').attr('src', '/img/arrowDown.png');
			}
		}

		function toggle_valutazione()
		{
			var element = $('#elementi_valutazione');
			if (!element.is(':visible'))
			{
				element.show();
				element.parent('div').find('img').attr('src', '/img/arrowUp.png');
			}
			else
			{
				element.hide();
				element.parent('div').find('img').attr('src', '/img/arrowDown.png');
			}
		}
	</script>
	<?
	include_once($root."/layout/bottom.php");
	?>
