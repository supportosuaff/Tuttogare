<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
				if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
				if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
					$codice_fase = getFase($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
					if ($codice_fase!==false) {
						$esito = check_permessi_gara($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
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
				$codice = $_GET["codice"];
				$bind = array();
				$bind[":codice"]=$codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}

				$risultato = $pdo->bindAndExec($strsql,$bind);

				?>
				<h1>INCARICHI</h1>

				<?
				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$operazione = "UPDATE";
					?>
  	    <form name="box" method="post" action="save.php" rel="validate">
        	<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
					<script>
						function aggiungi_incarico() {
							if ($(".edit-box").length < 50) {
								aggiungi('tr_incarico.php?codice_gara=<? echo $record["codice"] ?>','#incarichi');
							} else {
								alert("Troppi incarichi in modifica, procedere al salvataggio e riprovare");
							}
							return false;
						}

						function edit_incarico(id) {
							if ($(".edit").length < 50) {
								data = "codice=" + id;
								$.ajax({
									type: "GET",
									url: "tr_incarico.php",
									dataType: "html",
									data: data,
									async:false,
									success: function(script) {
										$("#incarico_"+id).replaceWith(script);
									}
								});
								f_ready();
								etichette_testo();
							} else {
								alert("Troppi incarichi in modifica, procedere al salvataggio e riprovare");
							}
							return false;
						}
					</script>
          <div id="incarichi">
          <?
						$bind=array();
						$bind[":codice"] = $record["codice"];
						$sql = "SELECT b_incaricati.*, r_incarichi.ruolo,r_incarichi.numero_atto, r_incarichi.data_atto,r_incarichi.codice AS codice_incarico FROM b_incaricati JOIN r_incarichi ON b_incaricati.codice = r_incarichi.codice_incaricato
						WHERE codice_riferimento = :codice AND sezione = 'gare' ";
						$ris_incarico = $pdo->bindAndExec($sql,$bind);

						if ($ris_incarico->rowCount()>0) {
							while ($record_incarico = $ris_incarico->fetch(PDO::FETCH_ASSOC)) {
								$id = $record_incarico["codice_incarico"];
								include("view_incarico.php");
							}
						} else {
							$record_incarico = get_campi("b_incaricati");
							$id = "i_".rand();
							$new_line = true;
							$record_incarico["ruolo"] = "14";
							$record_incarico["data_atto"] = "";
							$record_incarico["numero_atto"] = "";
							include("tr_incarico.php");
						}
					?>
					</div>
					<div>

				<? if (!$lock) { ?>
								<button class="aggiungi" onClick="aggiungi_incarico();return false;"><img src="/img/add.png" alt="Aggiungi incarico">Aggiungi incarico</button></div>
                <input type="submit" class="submit_big" value="Salva">
                </form>
                <?
        } else {
				?>
				<script>
					$("#contenuto_top :input").not('.espandi').prop("disabled", true);
					</script>
				</div>
				<?
				 }
				 include($root."/gare/ritorna.php");
			} else {
				echo "<h1>Gara non trovata</h1>";
			}
		} else {
			echo "<h1>Gara non trovata</h1>";
		}
	include_once($root."/layout/bottom.php");
	?>
