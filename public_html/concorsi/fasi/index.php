<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
				if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
				if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
					$codice_fase = getFaseConcorso($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
					if ($codice_fase!==false) {
						$esito = check_permessi_concorso($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
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
				$strsql = "SELECT * FROM b_concorsi WHERE codice = :codice ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}

				$risultato = $pdo->bindAndExec($strsql,$bind);

				?>
				<h1>FASI</h1>

				<?
				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					if (!$lock) {
					?>
  	    		<form name="box" method="post" action="save.php" rel="validate">
					<? } ?>
        	<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
					<script>
						function verifica_punteggi() {
							$(".totale_punteggio").val("0");
							$(".punteggio").each(function() {
								totale_attuale = Number($("#"+$(this).attr("rif")).val());
								totale_attuale = totale_attuale + Number($(this).val());
								$("#"+$(this).attr("rif")).val(totale_attuale);
					        });
							$('.totale_punteggio').each(function() {
								if ($(this).attr("id") != "punteggio_complessivo") {
									if ($('.punteggio[rif="'+$(this).attr("id")+'"]').length == 0) {
										$(this).val($("#"+$(this).attr("rif")).val());
									}
								}
							})
						}

						function check_steps(id) {
							if ($("tr","#list_step_"+id).length > 0) {
								$("#is_steps_"+id).val("true");
							} else {
								$("#is_steps_"+id).val("");
							}
						}

						function verifica_valutazione() {
							$('.punteggio_riferimento').each(function(index, el) {
								id_crit = $(this).data('identificativo');
								$('.add_sub_'+id_crit).show();
							});
							$(".div_valutazione_automatica").each(function() {
								id_div = $(this).attr("rel").split("_");
								if (typeof id_div[3] !== 'undefined') {
									id_div = id_div[2] + "_" + id_div[3];
								} else {
									id_div = id_div[2];
								}
								if ($("#tipo_cr_valutazione_"+id_div).val()=='N') {
									$(this).show();
								} else {
									$(this).hide();
									$("[type='radio']",this).each(function(){
										if ($(this).val() == "") {
											$(this).prop("checked",true);
										} else {
											$(this).prop("checked",false);
										}
									});
								}
								if ($("[rel='valutazione_automatica_"+id_div+"']").length > 1) {
									$(".padre[rel='valutazione_automatica_"+id_div+"']").hide();
									$(".step_div > tbody", ".padre[rel='valutazione_automatica_"+id_div+"']").html("");
									$(".step_div", ".padre[rel='valutazione_automatica_"+id_div+"']").hide();
									$("[type='radio']",".padre[rel='valutazione_automatica_"+id_div+"']").each(function(){
										if ($(this).val() == "") {
											$(this).prop("checked",true);
										} else {
											$(this).prop("checked",false);
										}
									});
								}
							});
						}
					</script>

					<div id="fasi">
	          <?
	          $bind = array();
	          $bind[":codice"] = $record["codice"];
	          $strsql = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice";
	          $ris_fasi = $pdo->bindAndExec($strsql,$bind);
	          if ($ris_fasi->rowCount()>0) {
	            while($fase = $ris_fasi->fetch(PDO::FETCH_ASSOC)) {
	              $padre = true;
	              $id_fase = $fase["codice"];
	              include("fase.php");
	            }
	          } else {
	            $padre = true;
	            $id_fase = "i_0";
	            $fase = get_campi("b_fasi_concorsi");
	            include("fase.php");
	          }
	        	?>
					</div>
	        <? if (!$lock) { ?>
	          <button class="aggiungi" onClick="aggiungi('fase.php','#fasi');verifica_valutazione();return false;"><img src="/img/add.png" alt="Aggiungi fase">Aggiungi fase</button>
	        <? } ?>

					<script>
						verifica_punteggi();
					</script>

					<? if (!$lock) { ?>
	          <input type="submit" class="submit_big" value="Salva">
					</form>
						<?
        } else {
				?>
				<script>
					$("#contenuto_top :input").not('.espandi').prop("disabled", true);
					</script>
				<?
				 }
				 include($root."/concorsi/ritorna.php");
			} else {
				echo "<h1>Concorso non trovato</h1>";
			}
		} else {
			echo "<h1>Concorso non trovato</h1>";
		}
	include_once($root."/layout/bottom.php");
	?>
