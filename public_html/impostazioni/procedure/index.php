<?
include_once("../../../config.php");
include_once($root."/layout/top.php");

$edit = false;
if (isset($_SESSION["codice_utente"])) {
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
	echo "<h1>PROCEDURE</h1>";
	if ($_SESSION["gerarchia"] === "0") {
		$strsql = "SELECT * FROM b_procedure WHERE eliminato = 'N' ORDER BY ordinamento,codice ";
		$risultato_procedure = $pdo->query($strsql);
		?>
		<style type="text/css">
			input[type="text"] {
				width: 100% !important;
				box-sizing : border-box !important;
				font-family: Tahoma, Geneva, sans-serif !important;
				font-size: 1em !important;
			}
		</style>
		<form name="box" method="post" action="save.php" rel="validate" >
			<div class="comandi">
				<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
			</div>
			<div class="box">
				<table width="100%" >
					<tbody id="procedure_tabs" class="sortable">
						<? if (isset($risultato_procedure) && $risultato_procedure->rowCount() > 0) {
							while ($record = $risultato_procedure->fetch(PDO::FETCH_ASSOC)) {
								$id = $record["codice"];
								include("form.php");
							}
						} else {
							$id = "i_0";
							$record = get_campi("b_procedure");
							include("form.php");
						}?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="5">
								<button class="aggiungi" onClick="aggiungi('form.php','#procedure_tabs');return false;"><img src="/img/add.png" alt="Aggiungi procedura">Aggiungi procedura</button>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<input type="submit" class="submit_big" value="Salva">
		</form>
		<?
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
} else {
	echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
	die();
}

include_once($root."/layout/bottom.php");
?>
