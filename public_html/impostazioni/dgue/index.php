<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	include_once($root."/dgue/config.php");

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
	echo "<h1>IMPOSTAZIONI DOCUMENTO GARA UNICO EUROPEO</h1>";
		if ($_SESSION["gerarchia"] === "0") {
			if (empty($_GET["version"])) {
				$versions = DGUEversions();
				foreach($versions AS $versionKey => $version) {
					?>
					<a class="submit_big" href="/impostazioni/dgue/index.php?version=<?= $versionKey ?>"><?= $version["label"] ?></a>
					<?
				}
			} else {
				$version = purify($_GET["version"]);
				$strsql = "SELECT * FROM b_dgue_settings WHERE version = :version ORDER BY codifica_criterio ";
				$risultato = $pdo->go($strsql,[":version"=>$version]);
		?>
		<script>
			function check_altro(campo,id) {
				if ($("#"+campo+"_form_"+id).val() == "-altro-") {
					$("#"+campo+"_altro_form_"+id).slideDown();
				} else {
					$("#"+campo+"_altro_form_"+id).slideUp();
					$("#"+campo+"_altro_form_"+id).val('');
				}
			}
		</script>
			<form name="box" method="post" action="save.php" rel="validate" >
				<input type="hidden" name="version" value="<?= $version ?>">
			<div class="comandi">
						<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
			</div>
						<div class="box">
							<table width="100%" >
										<tbody id="forms">
									<? if (isset($risultato) && $risultato->rowCount() > 0) {
										while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
											$id = $record["codice"];
											include("form.php");
										}
									} else {
										$id = "i_0";
										$record = get_campi("b_dgue_settings");
										include("form.php");
									}
									?>
								</tbody>
								<tfoot>
								<tr><td colspan="4">
						<button class="aggiungi" onClick="aggiungi('form.php','#forms');return false;"><img src="/img/add.png" alt="Aggiungi campo">Aggiungi campo</button></td></tr>
								</tfoot>
							</table>
							</div>
				<input type="submit" class="submit_big" value="Salva">
		</form>
			<?
			} 
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
