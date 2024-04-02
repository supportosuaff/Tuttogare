<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$edit = check_permessi("albo_fornitori",$_SESSION["codice_utente"]);
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
				$bind = array(":codice"=>$codice,":codice_ente"=>$_SESSION["ente"]["codice"]);
				$strsql = "SELECT * FROM b_bandi_albo WHERE codice = :codice ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_utente_ente OR codice_gestore = :codice_utente_ente) ";
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);

				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
?>
				<h1>MODULISTICA</h1>
				<h3><?= $record["oggetto"] ?></h3><br>
					<form name="box" method="post" action="save.php" rel="validate">
            <input type="hidden" name="codice_bando" value="<? echo $record["codice"]; ?>">
					<div class="comandi">
						<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
					</div>

						<script>
							var uploader = new Array();
						</script>
					<script type="text/javascript" src="/js/resumable.js"></script>
					<script type="text/javascript" src="resumable-uploader.js"></script>
					<table id="tab_moduli" width="100%">
						<thead>
							<tr><th>Titolo</th><th width="30%">Modello</th><th width="10">Obbligatorio</th><th width="10">Elimina</th></tr>
						</thead>
						<?
							$bind = array(":codice"=>$record["codice"]);
							$strsql = "SELECT * FROM b_modulistica_albo WHERE codice_bando = :codice AND attivo = 'S'";
							$risultato = $pdo->bindAndExec($strsql,$bind);
							if ($risultato->rowCount()>0) {
								while($modulo=$risultato->fetch(PDO::FETCH_ASSOC)) {
									$id = $modulo["codice"];
									if ($modulo["nome_file"] != "") $modulo["nome_file"] = "<a href=\"/documenti/allegati/albo/" . $modulo["codice_bando"] . "/" . $modulo["nome_file"] . "\" target=\"_blank\">" . $modulo["nome_file"] . "</a>";
									include("tr_modulo.php");
								}
							}
						?>
					</table>
					<button class="aggiungi" onClick="aggiungi('tr_modulo.php','#tab_moduli');return false;"><span class="fa fa-plus-circle fa-3x" alt="Aggiungi voce"></span><br>Aggiungi modulo</button>
					<input type="submit" class="submit_big" value="Salva">
				</form>
			    <? include($root."/albo_fornitori/ritorna.php");
			} else {

				echo "<h1>Bando non trovato</h1>";

				}
			} else {

				echo "<h1>Bando non trovato</h1>";

				}

	include_once($root."/layout/bottom.php");
	?>
