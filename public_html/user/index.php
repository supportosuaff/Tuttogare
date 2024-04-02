<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("user",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	?>

	<script>
		function showUserPM(codice) {
			if (typeof codice !== "undefinded") {
				$("#userPM_details").load("userPm.php?cod="+codice,function(){
					$("#userPM_div").dialog({
						modal: true,
						width: "800px",
						title: "Permessi utente"
					});
					$("#userPM_div").show();
					f_ready();
				});
			}
		}
		function showModuliPM(codice) {
			if (typeof codice !== "undefinded") {
				$("#modPM_details").load("moduliPm.php?cod="+codice,function(){
					f_ready();
				});
			}
		}
		function showModuli() {
			$("#moduloPM_div").dialog({
				modal: true,
				width: "90%",
				title: "Permessi moduli"
			});
			$("#moduloPM_div").show();
		}
	</script>
	<div id="userPM_div" style="display:none;">
		<div id="userPM_details" style="max-height:500px; overflow:scroll"></div>
	</div>
	<div id="moduloPM_div" style="display:none">
		<div style="float:left; width: 30%; max-height:500px; overflow:scroll">
		<?
			$bind = array();
			$s = "SELECT b_moduli.* FROM b_moduli WHERE b_moduli.tutti_utente = 'N' AND
							gerarchia >= '". $_SESSION["gerarchia"] . "'
							GROUP BY b_moduli.codice
							ORDER BY b_moduli.titolo";
			$r = $pdo->bindAndExec($s,$bind);
			if ($r->rowCount()>0) {
				$count = 0;
				while($re = $r->fetch(PDO::FETCH_ASSOC)) {
					$show = true;
					if ($re["ente"] == "S" && $re["tutti_ente"] == "N" && isset($_SESSION["ente"])) {
						$sql = "SELECT * FROM r_moduli_ente WHERE cod_modulo = :cod_modulo AND cod_ente = :cod_ente";
						$check_ente = $pdo->bindAndExec($sql,array(":cod_modulo"=>$re["codice"],":cod_ente"=>$_SESSION["ente"]["codice"]));
						if ($check_ente->rowCount() == 0) $show=false;
					}
					if ($show) {
						?>
						<button onClick="showModuliPM(<?= $re["codice"] ?>);" class="box" style="display:block; width:100%; border:0; cursor:pointer; text-align:left">
							<span class="<?= $re["glyph"] ?>"></span> <?= $re["titolo"] ?>
						</button>
						<?
					}
				}
			}
		?>
		</div>
		<div id="modPM_details" style="float:right; width: 69%;">

		</div>
		<div class="clear"></div>
	</div>
	<h1>GESTIONE UTENTI</h1>
	<div style="float:left; width:75%">
		<a href="/user/id0-edit" title="Inserisci nuovo utente"><div class="add_new"><span class="fa fa-plus-circle fa-3x"></span><br>Aggiungi nuovo utente</div></a>
	</div>
	<div style="float:right; width:24%">
		<a onClick="showModuli()"><div class="add_new"><span class="fa fa-users fa-3x"></span><br>Permessi moduli</div></a>
	</div>
	<div class="clear"></div>
	<?
		if ($_SESSION["amministratore"] && !isset($_SESSION["ente"])) {
			?>
			<div class="box">
				<h2>Inserimento massivo</h2>
				<small>CODICE_RIFERIMENTO: Inserire codice utente per l'attribuzione dei permessi</small>
				<form action="massive.php" method="post" enctype="multipart/form-data">
					<img src="/img/xls.png" alt="Modello lotti" style="vertical-align:middle"/><a href="dl-modello.php">Modello CSV</a>
					<input type="file" name="utenti" id="file">
					<input type="submit" name="submit" value="Upload">
				</form>
			</div>
			<?
		}
	?>
  <?
		$bind=array();
		$strsql  = "SELECT b_utenti.*, b_enti.denominazione, b_enti.sua, b_enti.attivo AS attivo_ente, b_gruppi.gruppo as ruolo, b_gruppi.gerarchia ";
		$strsql .= "FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice ";
		$strsql .= "LEFT JOIN b_enti ON b_utenti.codice_ente = b_enti.codice ";
		$strsql .= "WHERE (b_gruppi.gerarchia < 3 OR b_gruppi.gerarchia = 5) ";
		if (isset($_SESSION["ente"])) {
			$bind = array(':codice_ente' => $_SESSION["ente"]["codice"]);
			$strsql .= " AND (b_enti.codice = :codice_ente ";
			$strsql .= " OR b_enti.sua = :codice_ente)";
		}
		if ($_SESSION["gerarchia"] > 0 && $_SESSION["ente"]["codice"] != $_SESSION["record_utente"]["codice_ente"]) {
			$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
			$strsql .= " AND codice_ente = :codice_ente_utente";
		}
		$strsql .= " ORDER BY cognome,nome,dnascita" ;
		$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

	if ($risultato->rowCount()>0) {
		?>
        <table style="text-align:center; width:100%; font-size:0.8em" id="utenti" class="elenco">
        <thead>
        <tr>
					<th width="5"></th>
					<th width="5"></th>
					<th width="5">#</th>
					<th>Nominativo</th>
					<th  width="5">E-mail</th>
					<th width="250">Ente</th>
					<? if (!isset($_SESSION["ente"])) { ?>
						<th width="250">Ambiente</th>
					<? } ?>
					<th width="150">Ruolo</th>
					<? if ($_SESSION["gerarchia"]==="0") { ?>
						<th width="10">Abilitazioni</th>
					<? } ?>
					<th width="10">Rigenera password</th>
					<th width="10">Modifica</th>
					<th width="10">Attiva Disattiva</th>
					<? /* <th width="10">Elimina</th> */ ?>
				</tr>
        </thead>
        <tbody>
       	<?
				$_SESSION["export_user"] = array();
		while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {

			$codice			= $record["codice"];
			$nominativo		= $record["cognome"] . " " . $record["nome"];
			$attivo			= $record["attivo"];
			if ($record["denominazione"]=="") $record["denominazione"] = "Studio Amica";
			$colore = "#3C0";
			if ($attivo == "N") { $colore = "#C00"; }
			$colore_scaduto = "#3C0";
			if ($record["scaduto"] == "S") { $colore_scaduto = "#C00"; }
			$tmp = array();
			$tmp["codice"] = $record["codice"];
			$tmp["cognome"] = $record["cognome"];
			$tmp["nome"] = $record["nome"];
			$tmp["email"] = $record["email"];
			$tmp["denominazione"] = $record["denominazione"];
			$tmp["ufficio"] = $record["note_ufficio_dipartimento"];
			$tmp["codice_ente"] = $record["codice_ente"];
			$_SESSION["export_user"][] = $tmp;
			?>
			<tr id="<? echo $codice ?>">
				<td id="flag_<? echo $codice ?>" style="background-color: <? echo $colore ?>"></td>
				<td class="unsort" id="flag_scaduto_<? echo $codice ?>" style="background-color: <? echo $colore_scaduto ?>"></td>
				<td><? echo $codice ?></td>
				<td style="text-align:left"><strong><? echo $nominativo ?></strong></td>
        <td><? echo $record["email"] ?></td>
        <td><? echo $record["denominazione"]; if(! empty($record["note_ufficio_dipartimento"])) echo "<br><b>{$record["note_ufficio_dipartimento"]}</b>"; ?></td>
				<? if (!isset($_SESSION["ente"])) { ?>
					<td>
						<?
							if (!empty($record["sua"]) && ($record["attivo_ente"] == "N")) {
								$sql_sua = "SELECT * FROM b_enti WHERE codice = :sua";
								$ris_sua = $pdo->bindAndExec($sql_sua,array(":sua"=>$record["sua"]));
								if ($ris_sua->rowCount() > 0) {
									echo $ris_sua->fetch(PDO::FETCH_ASSOC)["denominazione"];
								}
							} else {
								echo $record["denominazione"];
							}
						?>
					</td>
				<? } ?>
        <td style="text-align:center">
					<? if ($record["temporaneo"] == "C") { echo "Commissario"; } else { echo $record["ruolo"] . ($record["temporaneo"] == "S" ? ' Temporaneo' : null); } ?>
				</td>
				<? if ($_SESSION["gerarchia"]==="0") { ?>
					<td style="text-align:center">
						<button onClick="showUserPM(<?= $record["codice"] ?>)">
							<?
								$sql = "SELECT * FROM r_moduli_utente WHERE cod_utente = :codice_utente";
								$ris_perm = $pdo->bindAndExec($sql,array(":codice_utente"=>$record["codice"]));
								echo $ris_perm->rowCount();
							?>
						</button>
					</td>
				<? } ?>
				<td style"text-align:center"><input type="image" onClick="reinvia('<? echo $codice ?>')" src="/img/undo.png" title="Rigenera password"></td>
        <td style"text-align:center"><input type="image" onClick="window.location.href='/user/id<? echo $codice ?>-edit'" src="/img/edit.png" title="Modifica"></td>
        <td style"text-align:center"><input type="image" onClick="disabilita('<? echo $codice ?>','user');" src="/img/switch.png" title="Abilita/Disabilita"></td>
        <? /* <td style"text-align:center"><input type="image" onClick="elimina('<? echo $codice ?>','user');" src="/img/del.png" title="Elimina"></td> */ ?>
    	</tr>
        <?
		}
		?>
        </tbody>
         </table>
				 <br>
				 <div style="text-align:right">
				 	<a href="export-list.php" target="_blank"><span class="fa fa-download"></span> Esporta CSV</a>
				</div>
				<script>
				function reinvia(id){
					$.ajax({
						type: "GET",
						url: "rigenera.php",
						dataType: "html",
						data: {id: id},
						success: function(e) {
							jalert(e);
						}
					});
				}
				</script>
         <div class="clear"></div>
          <?
	}		 else {
?><h1 style="text-align:center">
<span class="fa fa-exclamation-circle fa-3x"></span><br>Nessun risultato!</h1>	<?
}
		?>
        <hr><a href="/user/id0-edit" title="Inserisci nuovo utente"><div class="add_new"><span class="fa fa-plus-circle fa-3x"></span><br>Aggiungi nuovo utente</div></a><hr>

	<?
	include_once($root."/layout/bottom.php");
	?>
