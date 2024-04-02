<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("pagine",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
?>
	<?

		if (isset($_GET["cod"])) {

				$codice = $_GET["cod"];
				$_SESSION["codice_pagina"] = $codice;
				$bind = array();
				$bind[":codice"] = $codice;
				$strsql = "SELECT * FROM b_pagina WHERE codice = :codice ";
				if (isset($_SESSION["ente"])) {
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$strsql.= " AND codice_ente = :codice_ente";
				} else {
					$strsql.= " AND codice_ente = 0 ";
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);

				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$operazione = "UPDATE";
				} else if ($codice == 0) {
					$record = get_campi("b_pagina");
					$operazione = "INSERT";
					$record["tipologia"] = "HTML";
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
                    <input type="hidden" name="codice" value="<? echo $record["codice"]; ?>">
                    <input type="hidden" name="operazione" value="<? echo $operazione ?>">
										<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
										<input type="image" onClick="elimina('<? echo $record["codice"] ?>','pagine');" src="/img/del.png" title="Elimina">
										<table width="100%">
											<? if ($_SESSION["amministratore"]) { ?>
											<tr>
												<th colspan="4"><b>Meta Tag:</b></th>
											</tr>
											<tr>
												<th><label>Title:</label></th>
												<td colspan="3">
													<input type="text" name="title" rel="S;5;50;A" value="<?= $record["title"] ?>" title="meta-title">
												</td>
											</tr>
											<tr>
												<th><label>Keywords:</label></th>
												<td colspan="3">
													<input type="text" name="keywords" rel="S;5;0;A" value="<?= $record["keywords"] ?>" title="meta-keywords">
												</td>
											</tr>
											<tr>
												<th>
													<label>Description:</label>
												</th>
												<td colspan="3">
													<input type="text" name="description" rel="S;5;0;A" value="<?= $record["description"] ?>" title="meta-description">
												</td>
											</tr>
											<? } ?>
											<tr>
												<th><label>Titolo:</label></th>
												<td colspan="3">
                    			<input type="text" name="titolo" value="<? echo $record["titolo"] ?>" title="Titolo" class="titolo_edit" rel="S;3;255;A"><br>
												</td>
											</tr>
											<tr>
												<th width="10%"><label for="sezione">Sezione</label></th>
                    		<td >
													<select name="sezione" id="sezione" title="Sezione">
				                    <option value="">Nessuna</option>
			                    	<?
															$codice_ente = 0;
															if (isset($_SESSION["ente"])) $codice_ente = $_SESSION["ente"]["codice"];
															$sql = "SELECT sezione FROM b_pagina WHERE codice_ente = :codice_ente  GROUP BY sezione";
															$ris = $pdo->bindAndExec($sql,array(":codice_ente"=>$codice_ente));
															if($ris->rowCount()>0) {
																while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
																	echo "<option>" . $rec["sezione"] . "</option>";
																}
															}
															?>
			                        <option>Altro</option>
			                    </select>
													<input type="text" disabled="disabled" name="sezione_altro" id="sezione_altro" value="" title="Sezione" class="sezione_edit" rel="N;3;255;A">
												</td>
												<th width="10%">
                    			<label for="tipologia">Tipologia</label>
												</th>
												<td width="30%">
			                    <select name="tipologia" id="tipologia" onChange="change_tipologia()" width="300">
			                    	<option>HTML</option>
			                        <option>Link</option>
			                    </select>
												</td>
											</tr>
											<tr>
												<td colspan="4">
										   		<div class="div_tipologia box" id="HTML">
														<h2>HTML</h2>
														<textarea rows='10' class="ckeditor_models" name="testo" cols='80' id="testo" title="Testo" rel="N;3;0;A"><? echo $record["testo"]; ?></textarea>
													</div>
													<div class="div_tipologia box" id="Link">
														<h2>Link</h2>
														<input type="text" name="link" id="link" value="<? echo $record["link"] ?>" style="width:98%" title="Link" rel="N;3;255;<? if ($_SESSION["amministratore"]) { ?>A<? } else { ?>L<? } ?>">
													</div>
												</td>
											</tr>
										</table>	<script type="text/javascript">
			$(".div_tipologia").hide();
		function change_tipologia() {
			$(".div_tipologia").slideUp();
			$("#" + $("#tipologia").val()).slideDown();
		}

		$("#tipologia").val("<? echo $record["tipologia"] ?>");
		change_tipologia();

		$("#sezione").val("<? echo $record["sezione"] ?>");
		$("#sezione").change(function() {
					  if ($("#sezione").val() == "Altro") {
						  $("#sezione_altro").attr("disabled",false);
					  } else {
						  $("#sezione_altro").attr("disabled",true);
					  }
		});
			</script>


	</form>
    <div class="clear"></div>
    <?

			} else {

				echo "<h1>Notizia non trovata</h1>";

				}

	?>


<?
	include_once($root."/layout/bottom.php");
	?>
