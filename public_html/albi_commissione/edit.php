<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
				if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
				if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
					$edit = check_permessi("albi_commissione",$_SESSION["codice_utente"]);
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
				$bind = array(":codice"=>$codice,":codice_ente"=>$_SESSION["ente"]["codice"]);
				$strsql = "SELECT * FROM b_albi_commissione WHERE codice = :codice ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_utente_ente OR codice_gestore = :codice_utente_ente) ";
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$string_cpv = "";
					$cpv = array();
					$bind=array(":codice_albo"=>$record["codice"]);
					$strsql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_albi_commissione ON b_cpv.codice = r_cpv_albi_commissione.codice WHERE r_cpv_albi_commissione.codice_bando = :codice_albo ORDER BY codice";
					$risultato_cpv = $pdo->bindAndExec($strsql,$bind);
					if ($risultato_cpv->rowCount()>0) {
						$risultato_cpv = $risultato_cpv->fetchAll(PDO::FETCH_ASSOC);
						foreach ($risultato_cpv as $rec_cpv) {
							$cpv[] = $rec_cpv["codice"];
						}
						$string_cpv = implode(";",$cpv);
					}
						$operazione = "UPDATE";
					} else {
						$lock = false;
						$record = get_campi("b_albi_commissione");
						$operazione = "INSERT";
						$string_cpv = "";
					}
?>
<h1>INSERIMENTO PRELIMINARE</h1>
	<form name="box" method="post" action="save.php" rel="validate">
    <input type="hidden" name="codice" value="<? echo $codice; ?>">
    <input type="hidden" name="operazione" value="<? echo $operazione ?>">

	<div class="comandi">
	<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
	</div>
			<div id="tabs">
                	<ul>
                    	<li><a href="#generali">Dati generali</a></li>
                        <li><a href="#descrizione">Descrizione *</a></li>
                        <li><a href="#categorie">Categorie merceologiche *</a></li>
                    </ul>

                    <div id="generali"><table width="100%">
                    	<tr>
                        <td class="etichetta">Provvedimento</td>
                        <td><input type="text" id="Provvedimento indizione" name="bando[numero_atto]" title="Provvedimento di indizione" value="<? echo $record["numero_atto"] ?>" rel="N;1;255;A"></td>
                       	<td class="etichetta">Data provvedimento</td>
                        <td><input type="text" class="datepick_today" id="data_atto" name="bando[data_atto]" title="Data atto di indizione" value="<? echo mysql2date($record["data_atto"]) ?>" rel="N;10;10;D"></td>
                       </tr>
    	                <tr>
                        <td class="etichetta">Oggetto *</td>
                        	<td colspan="3">

													<input type="hidden" name="bando[codice_ente]" id="codice_ente" rel="S;0;0;N" value="<?= $_SESSION["ente"]["codice"] ?>">
													<input type="hidden" name="bando[codice_gestore]" id="codice_gestore" rel="S;0;0;N" value="<?= $_SESSION["ente"]["codice"] ?>">
	    	                	<textarea name="bando[oggetto]" id="oggetto" title="Oggetto" rel="S;3;0;A" class="ckeditor_simple"><? echo $record["oggetto"] ?></textarea>
	                   		</td>
                       </tr>
                       <tr>
                        <td class="etichetta">Struttura proponente *</td>
                        <td colspan="3"><input style="width:95%" type="text" id="struttura_proponente" name="bando[struttura_proponente]" title="Struttura proponente" value="<? echo $record["struttura_proponente"] ?>" rel="S;3;255;A"></td>
                        </tr>
                       <tr>
                       	<td class="etichetta">R.U.P. *</td>
                        <td><input style="width:95%" type="text" id="rup" name="bando[rup]" title="RUP" value="<? echo $record["rup"] ?>" rel="S;3;255;A"></td>
						<td class="etichetta">Responsabile della struttura *</td>
                        <td><input style="width:95%" type="text" id="responsabile_struttura" name="bando[responsabile_struttura]" title="Responsabile del servizio" value="<? echo $record["responsabile_struttura"] ?>" rel="S;3;255;A"></td></tr>
                   </table>
                      <div class="clear"></div>
    	               <a class="precedente" style="float:left" href="#">Step precedente</a>
        	           <a class="successivo" style="float:right" href="#">Step successivo</a>
                       <div class="clear"></div>
</div>
                    <div id="descrizione">
	    	                	<textarea name="bando[descrizione]" id="descrizione" title="Descrizione" class="ckeditor_full" rel="S;3;0;A">
                                <? echo $record["descrizione"] ?>
                                </textarea>
                                                      <div class="clear"></div>
    	               <a class="precedente" style="float:left" href="#">Step precedente</a>
        	           <a class="successivo" style="float:right" href="#">Step successivo</a>
                       <div class="clear"></div>

	               </div>
                    <div id="categorie">
                    <? include("categorie/form.php"); ?>
                                          <div class="clear"></div>
    	               <a class="precedente" style="float:left" href="#">Step precedente</a>
        	           <a class="successivo" style="float:right" href="#">Step successivo</a>
                       <div class="clear"></div>

					</div>

                    </div>
    <div class="clear"></div>
               <input type="submit" class="submit_big" value="Salva">
    </form>
    <script>

	    $("#tabs").tabs();

			$(".precedente").each(function() {
					var id_parent = $("#tabs").children("div").index($(this).parent("div"));
					if (id_parent == 0) {
						$(this).remove();
					} else {
						var target = id_parent - 1;
						$(this).click(function() { $('#tabs').tabs('option','active',target) });
					}
				});

				$(".successivo").each(function() {
					var id_parent = $("#tabs").children("div").index($(this).parent("div"));
					if (id_parent == ($("#tabs").children("div").length - 1)) {
						$(this).remove();
					} else {
						var target = id_parent + 1;
						$(this).click(function() { $('#tabs').tabs('option','active',target) });
					}
				});
	</script>
	<?
	include($root."/albi_commissione/ritorna.php");
	include_once($root."/layout/bottom.php");
	?>
