<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("contratti",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	echo "<h1>GESTIONE CONTRATTI</h1>";
	$codice_gara = !empty($_GET["codice"]) ? $_GET["codice"] : null;
	if ($edit) {
		if(time() < strtotime('2021-05-03 00:00:00')) {
			?>
			<a href="/contratti/edit.php?codice=0<?= !empty($codice_gara) ? '&codice_gara='.$codice_gara : null ?>" title="Inserisci nuovo contratto">
				<div class="add_new" style="margin-bottom:20px !important;">
					<i class="fa fa-plus-circle fa-3x" style="color:#28b311"></i><br />
					Crea nuovo contratto
				</div>
			</a>
			<?
		}
		$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
		$sql  = "SELECT b_contratti.*, b_enti.denominazione FROM b_contratti JOIN b_enti ON b_contratti.codice_ente = b_enti.codice ";
		if(!empty($codice_gara) && $_SESSION["gerarchia"] > 1) {
			$sql .= "JOIN b_permessi ON b_contratti.codice_gara = b_permessi.codice_gara ";
		} elseif (empty($codice_gara) && $_SESSION["gerarchia"] > 1) {
			$sql .= "JOIN b_permessi_contratti ON b_contratti.codice = b_permessi_contratti.codice_contratto ";
		}
		$sql .= "WHERE b_contratti.codice_gestore = :codice_ente AND b_contratti.solo_esecuzione = 'N' ";
		if ($_SESSION["gerarchia"] > 0) {
			$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
			$sql .= "AND (b_contratti.codice_ente = :codice_ente_utente OR b_contratti.codice_gestore = :codice_ente_utente) ";
		}
		if (!empty($codice_gara)) {
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
			$sql .= " AND b_contratti.codice_gara = :codice_gara";
			if($_SESSION["gerarchia"] > 1) {
				$bind[":codice_gara"] = $codice_gara;
				$sql .= " AND (b_permessi.codice_utente = :codice_utente)";
			}
		} else {
			if($_SESSION["gerarchia"] > 1) {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$sql .= " AND (b_permessi_contratti.codice_utente = :codice_utente)";
			}
		}
		$sql .= " ORDER BY b_contratti.codice DESC ";
		$ris  = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount()>0) {
			$statusPath = __DIR__."/ws/script/".$_SESSION["ente"]["codice"]."/status.php";
		?>
	    <table id="pagine" width="100%" id="contratti" class="elenco">
	    	<thead>
	      	<tr>
						<td>#</td>
						<? if (file_exists($statusPath)) { ?> 
							<td width="10"></td>
							<td>Stato</td>
							<?
							} 
						?>
						<td>CIG</td>
						<td>Oggetto</td>
						<td>Tipologia</td>
						<?= $_SESSION["ente"]["tipo"] == "SUA" ? '<td width="15%">Ente</td>' : null ?>
	        </tr>
	      </thead>
				<tbody>
					<?
					while ($rec = $rec_contratto = $ris->fetch(PDO::FETCH_ASSOC)) {
						?>
						<tr>
							<td><?= $rec["codice"] ?></td>
							<? if (file_exists($statusPath)) { 
								$color = "#AAA";
								$label = "Non trasmesso";
								include($statusPath); ?> 
								<td style="background-color: <? echo $color ?>"></td>
								<td><?= $label ?></td>
							<? } ?>
							<td style="text-align: center;"><?= !empty($rec["cig"]) ? $rec["cig"] : '/'  ?></td>
							<td><a href="pannello.php?codice=<?= $rec["codice"] . (!empty($rec["codice_gara"]) ? "&codice_gara=".$rec["codice_gara"] : null) ?>"><?= str_limit($rec["oggetto"], 120) ?></a></td>
							<td><?= ucfirst($rec["tipologia"]) ?></td>
							<?= $_SESSION["ente"]["tipo"] == "SUA" ? '<td style="text-align: center;">'.$rec["denominazione"]."</td>" : null ?>
						</tr>
						<?
					}
					?>
	    	</tbody>
	    </table>
	    <div class="clear"></div>
			<?
		} else {
			?>
			<h2 style="text-align:center; padding:100px 0px; margin:5px 0px; background-color:#eee;">
				<? if(! empty($_SESSION["ente"]) && !$hide_amica) { ?><img src="/img/tuttogarepa-logo-software-sx-small.png" height="50" alt="TuttoGare"><br><?} ?>
				Nessun contratto disponibile!
			</h2>
			<?
		}
		if(time() < strtotime('2021-05-03 00:00:00')) {
			?>
			<a href="/contratti/edit.php?codice=0<?= !empty($codice_gara) ? '&codice_gara='.$codice_gara : null ?>" title="Inserisci nuovo contratto">
				<div class="add_new" style="margin-top:20px !important;">
					<i class="fa fa-plus-circle fa-3x" style="color:#28b311"></i><br />
					Crea nuovo contratto
				</div>
			</a>
			<?
		}
	}
	if(!empty($codice_gara)) include $root . '/gare/ritorna.php';
	include_once($root."/layout/bottom.php");
	?>
