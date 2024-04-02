<?
	include_once("../../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON")) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	if ($edit && ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON")) {
			$codice = $_GET["codice"];
			$bind = array();
			$bind[":codice"] = $codice;
			$strsql = "SELECT * FROM b_paragrafi_ricorsivi WHERE codice = :codice ";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$operazione = "UPDATE";
			} else {
				$record = get_campi("b_paragrafi_ricorsivi");
				$operazione = "INSERT";
			}
?>
<div class="clear"></div>
<form name="box" method="post" action="save.php" rel="validate" >
  <input type="hidden" id="codice" name="codice" value="<? echo $record["codice"]; ?>">
  <input type="hidden" id="operazione" name="operazione" value="<? echo $operazione ?>">
  <div class="comandi">
		<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
  </div>
                  <div class="box">
        <table width="100%">
					<tr><td>
						<input type="text" class="titolo_edit" value="<? echo $record["titolo"] ?>" name="titolo" id="titolo" title="Titolo" rel="S;0;0;A">
					</td>
				</tr>
    <td>
    	<textarea id="contenuto" rel="S;0;0;A" name="contenuto" class="ckeditor_models"><? echo $record["contenuto"] ?></textarea>
    </td>
    </tr>
    </table>
	</div>
	<input type="submit" class="submit_big" value="Salva">

</form>
    <?
			} else {
						echo "<h1>Impossibile accedere!</h1>";
						echo '<meta http-equiv="refresh" content="0;URL=/enti/">';
						die();
				}
	?>


<?
	include_once($root."/layout/bottom.php");
	?>
