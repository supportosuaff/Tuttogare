<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$edit = check_permessi("sda",$_SESSION["codice_utente"]);
			if (!$edit) {
				echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
				die();
			}
		} else {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
		if (isset($_GET["codice"])) {

				$codice = $_GET["codice"];
				$bind = array();
				$bind[":codice_bando"] = $codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql = "SELECT * FROM b_bandi_sda WHERE codice = :codice_bando ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);

				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$operazione = "UPDATE";
?>
				<h1>PUBBLICAZIONE BANDO</h1>
				<h3><?= $record["oggetto"] ?></h3><br>
				<form name="box" method="post" action="save.php" rel="validate">
				<input type="hidden" name="codice_bando" value="<? echo $record["codice"]; ?>">
				<div class="comandi">
					<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
				</div>
				<table width="100%">
					<tr>
						<td class="etichetta"><strong>Data di pubblicazione</strong></td>
						<td width="20%"><input type="text" class="datepick" id="data_pubblicazione" name="bando[data_pubblicazione]" title="Data di pubblicazione" value="<? echo mysql2date($record["data_pubblicazione"]) ?>" rel="S;10;10;D"></td>
						<td class="etichetta"><strong>Livello</strong></td>
						<td>
							<select title="Livello"  name="bando[pubblica]" id="valore" rel="S;0;0;N">
								<option value="0">Non pubblicare</option>
								<option value="1">Area riservata</option>
								<option value="2">Area pubblica</option>
							</select>
						</td>
					</tr>
				 </table>
				<script>
					$("#valore").val('<?= $record["pubblica"] ?>');
				</script>
				<input type="submit" class="submit_big" value="Salva">
							</form>
			    <? include($root."/sda/ritorna.php");
			} else {

				echo "<h1>Bando non trovato</h1>";

				}
			} else {

				echo "<h1>Bando non trovato</h1>";

				}

	include_once($root."/layout/bottom.php");
	?>
