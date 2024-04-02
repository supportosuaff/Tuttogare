<?
	include_once("../../../config.php");
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
	if ($edit) {
			$codice = $_GET["codice"];
			$bind = array();
			$bind[":codice"] = $codice;
			$strsql = "SELECT * FROM b_modelli_standard WHERE codice = :codice ";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					if (isset($_SESSION["ente"])) {
						$bind = array();
						$bind[":codice"] = $record["codice"];
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$strsql = "SELECT * FROM b_modelli_enti WHERE codice = :codice AND codice_ente = :codice_ente";
						$ris = $pdo->bindAndExec($strsql,$bind);
						if ($ris->rowCount()>0) {
							$modello = $ris->fetch(PDO::FETCH_ASSOC);
							$record["corpo"] = $modello["corpo"];
						}
					}
					$operazione = "UPDATE";
			} else {
				$record = get_campi("b_modelli_standard");
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
        			   <? if (!isset($_SESSION["ente"])) { ?> <tr><td>
                                <input type="text" class="titolo_edit" value="<? echo $record["titolo"] ?>" name="titolo" id="titolo" title="Titolo" rel="S;0;0;A">
                            </td>
                    </tr>
                    <? } else { ?>
                    <h1><? echo $record["titolo"] ?></h1>
                    <? } ?><tr>
                    <td>
                    <textarea id="corpo" rel="S;0;0;A" name="corpo" class="ckeditor_models"><? echo $record["corpo"] ?></textarea>
                    </td>
                    </tr>
                    </table>
									</div>
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
