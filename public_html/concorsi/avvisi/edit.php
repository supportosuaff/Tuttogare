<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFaseRefererConcorso($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
		if ($codice_fase !== false) {
			$esito = check_permessi_concorso($codice_fase,$_GET["codice_gara"],$_SESSION["codice_utente"]);
				$edit = $esito["permesso"];
				$lock = $esito["lock"];
			}
			if (!$edit) {
						echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
				die();
			}
		} else {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	if (isset($_GET["codice"]) && isset($_GET["codice_gara"])) {

				$codice = $_GET["codice"];
				$bind = array();
				$bind[":codice"] = $codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$bind[":codice_gara"] = $_GET["codice_gara"];

				$strsql = "SELECT * FROM b_avvisi_concorsi WHERE codice = :codice";
				$strsql .= " AND codice_ente = :codice_ente ";
				$strsql .= " AND codice_gara = :codice_gara ";

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
						$record = get_campi("b_avvisi_concorsi");
						$operazione = "INSERT";
				} else {
						echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
						die();
				}
?>

<div class="clear"></div>
<form name="box" method="post" action="save.php" rel="validate">
                    <input type="hidden" name="codice" value="<? echo $codice; ?>">
                    <input type="hidden" name="operazione" value="<? echo $operazione ?>">
                    <input type="hidden" name="codice_gara" value="<? echo $_GET["codice_gara"] ?>">
					<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
					<input type="image" onClick="elimina('<? echo $codice ?>','<? echo "concorsi/avvisi"; ?>');" src="/img/del.png" title="Elimina">
					<table width="100%">
						<tr>
							<td class="etichetta">Data *</td><td><input type="text" name="data" value="<? echo mysql2date($record["data"]) ?>" class="datepick" size="10" title="Data" rel="S;10;10;D"></td>
							<td class="etichetta">Data scadenza Homepage</td><td><input type="text" name="data_scadenza" value="<? echo mysql2date($record["data_scadenza"]) ?>" class="datepick" size="10" title="Data Scadenza" rel="N;10;10;D"></td>
						</tr>
						<tr>
							<td colspan="6">
                    <input type="text" name="titolo" value="<? echo $record["titolo"] ?>" title="Titolo" class="titolo_edit" rel="S;3;255;A">
									</td>
								</tr>
								<tr><td colspan="6">
                    <textarea rows='10' class="ckeditor" name="testo" cols='80' id="testo" title="Testo" rel="S;3;0;A"><? echo $record["testo"]; ?></textarea>
									</td></tr></table>
<div id="allegati">
	<?	$cod_allegati = $record["cod_allegati"]; ?>
		<input type="hidden" value="<? echo $cod_allegati ?>" name="cod_allegati" title="Allegati" id="cod_allegati" rel="N;0;0;A">
        <button onClick="open_allegati();return false;" style="width:100%; padding:10px; background-color:#F60" class="submit">
		           <img src="/allegati/icon.png" alt="Allega" width="15" style="vertical-align:middle"> Allega file
		</button>
       	<table width="100%" id="tab_allegati">
        	<? if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
            	while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
					include($root."/allegati/tr_allegati.php");
				}
			} ?>
        </table>
</div>

               <input type="submit" class="submit_big" value="Salva">
    </form>
     <?
	 $form_upload["codice_gara"] = $_GET["codice_gara"];
	 $form_upload["online"] = 'S';
	 $form_upload["sezione"] = 'concorsi';
	 include($root."/allegati/form_allegati.php"); ?>

    <div class="clear"></div>
    <?

			} else {

				echo "<h1>Avviso non trovata</h1>";

				}

				$_GET["codice"] = $_GET["codice_gara"];
				include($root."/concorsi/ritorna.php");

	?>


<?
	include_once($root."/layout/bottom.php");
	?>
