<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	if (isset($_SESSION["ente"])) {
		?>
		<h1>Elenchi fornitori <? if (isset($_GET["scadute"])) { echo ($_GET["scadute"]) ? "Scaduti" : "Attivi"; } ?></h1>
		<div style="float:left">
			<a href="/archivio_albo/index.php">Tutti</a> | <a href="/archivio_albo/index.php?scadute=0">Attivi</a> | <a href="/archivio_albo/index.php?scadute=1">Scaduti</a>
		</div>
		<div style="float:right; width: 25%; text-align:right">
			<? 
				$bind = array();
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$sql  = "SELECT codice,denominazione FROM b_enti WHERE ((codice = :codice_ente) OR (sua = :codice_ente)) ORDER BY denominazione ";
				$ris = $pdo->bindAndExec($sql, $bind);
				if ($ris->rowCount() > 1) {
					?>
						<strong>Filtra Ente</strong><br><select onchange="window.location.href='<?= $_SERVER["PHP_SELF"] ?>?codice_ente='+$(this).val()">
							<option value="">Tutti</option>
							<?
								while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
									?><option <?= (!empty($_GET["codice_ente"]) && $rec["codice"] == $_GET["codice_ente"]) ? "selected" : "" ?> value="<? echo $rec["codice"] ?>"><? echo $rec["denominazione"] ?></option><?
								}
							?>
						</select>
					<?
				}
			?>
		</div>
		<div class="clear"></div>
		<br><br>
		<?
		$types = ["F"=>"Fornitori","P"=>"Professionisti"];
		include_once("data.php");
	}
	include_once($root."/layout/bottom.php");
	?>
