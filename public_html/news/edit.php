<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("news",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

		if (isset($_GET["cod"])) {

			$codice = $_GET["cod"];
			$bind = array();
			$bind[":codice"] = $codice;
			$strsql = "SELECT * FROM b_news WHERE codice = :codice";
			if (isset($_SESSION["ente"])) {
				$strsql .= " AND codice_ente = " . $_SESSION["ente"]["codice"];
			} else {
				$strsql .= " AND codice_ente = 0";
			}
			//echo $strsql;
			$risultato = $pdo->bindAndExec($strsql,$bind);

			if ($risultato->rowCount() > 0) {
				$record = $risultato->fetch(PDO::FETCH_ASSOC);
				$operazione = "UPDATE";
				if ($record["cod_allegati"] != "" && preg_match("/^[0-9\;]+$/",$record["cod_allegati"])) {
							$allegati = explode(";",$record["cod_allegati"]);
							$str_allegati = ltrim(implode(",",$allegati),",");
							$sql = "SELECT * FROM b_allegati WHERE codice IN (" . $str_allegati . ")";
							$ris_allegati = $pdo->query($sql);
				}
			} else if ($codice == 0) {
					$record = get_campi("b_news");
					$operazione = "INSERT";
			} else {
					echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
					die();
			}
			?>
			<div class="clear"></div>
			<style type="text/css">
				input[type="text"] {
					width: 100%;
					box-sizing : border-box;
					font-family: Tahoma, Geneva, sans-serif;
					font-size: 1em
				}
			</style>
			<form name="box" method="post" action="save.php" rel="validate">
			    <input type="hidden" name="codice" value="<? echo $codice; ?>">
			    <input type="hidden" name="operazione" value="<? echo $operazione ?>">
			    <button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
			    <input type="image" onClick="elimina('<? echo $codice ?>','<? echo "news"; ?>');" src="/img/del.png" title="Elimina">
					<table width="100%">
						<? if ($_SESSION["amministratore"]) { ?>
						<tr>
							<th colspan="6"><b>Meta Tag:</b></th>
						</tr>
						<tr>
							<th><label>Title:</label></th>
							<td colspan="3">
								<input type="text" name="title" rel="S;5;50;A" value="<?= $record["title"] ?>" title="meta-title">
							</td>
							<th><label>Keywords:</label></th>
							<td>
								<input type="text" name="keywords" rel="S;5;0;A" value="<?= $record["keywords"] ?>" title="meta-keywords">
							</td>
						</tr>
						<tr>
							<th>
								<label>Description:</label>
							</th>
							<td colspan="5">
								<input type="text" name="description" rel="S;5;0;A" value="<?= $record["description"] ?>" title="meta-description">
							</td>
						</tr>
						<? } ?>
						<tr>
							<th width="150">
								<label for="data">Data:</label>
							</th>
							<td>
								<input type="text" name="data" value="<? echo mysql2date($record["data"]) ?>" class="datepick" size="10" title="Data" rel="S;10;10;D">
							</td>
							<th width="150">
			        	<label for="scadenza_hp">Scadenza Homepage:</label>
							</th>
							<td>
			        	<input type="text" name="scadenza_hp" value="<? echo mysql2date($record["scadenza_hp"]) ?>" class="datepick" size="10" title="Scadenza Homepage" rel="N;10;10;D">
							</td>
							<? if (!isset($_SESSION["ente"])) { ?>
							<th width="150"><label for="servizio">Comunicazione di servizio</label></th>
							<td><input type="checkbox" name="servizio" <? if ($record["servizio"]==1) echo "checked" ?>></td>
							<? } else {
								?>
								<td colspan="2"></td>
								<?
							} ?>
				    </tr>
						<tr>
							<th><label>Titolo:</label></th>
							<td colspan="5">
				  		  <input type="text" name="titolo" value="<? echo $record["titolo"] ?>" title="Titolo" class="titolo_edit" rel="S;3;255;A">
							</td>
						</tr>
						<tr>
							<td colspan="6">
						    <textarea rows='10' class="ckeditor" name="testo" cols='80' id="testo" title="Testo" rel="S;3;0;A">
						        <? echo $record[ "testo"]; ?>
						    </textarea>
							</td>
						</tr>
					</table>

			    <div id="allegati">
			        <? $cod_allegati = $record["cod_allegati"]; ?>
			        <input type="hidden" value="<? echo $cod_allegati ?>" name="cod_allegati" title="Allegati" id="cod_allegati" rel="N;0;0;A">
			        <button onClick="open_allegati();return false;" style="width:100%; padding:10px; background-color:#F60" class="submit">
			            <img src="/allegati/icon.png" alt="Allega" width="15" style="vertical-align:middle"> Allega file
			        </button>
			        <table width="100%" id="tab_allegati">
			            <? if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) { while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) { include($root."/allegati/tr_allegati.php"); } } ?>
			        </table>
			    </div>
			    <input type="submit" class="submit_big" value="Salva">
			</form>
			<?
			$form_upload["online"] = 'S';
			include($root."/allegati/form_allegati.php");
			?>
			<div class="clear"></div>
			<?

		} else {

			echo "<h1>Notizia non trovata</h1>";

		}

	include_once($root."/layout/bottom.php");
?>
