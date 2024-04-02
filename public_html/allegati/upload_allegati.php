<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {

			ini_set('memory_limit', '-1');
	  	ini_set('max_execution_time', 600);
			$codice_gara = 0;
			if (isset($_POST["codice_gara"])) $codice_gara = $_POST["codice_gara"];

			foreach($_POST["allegato"] as $identifier => $allegato) {
				$percorso = $config["pub_doc_folder"] . "/allegati";
				$tab_elenco = "tab_allegati";
				if (isset($allegato["online"]) && $allegato["online"] == "N") {
					$tab_elenco = "tab_riservati";
					$allegato["online"] = 'N';
					$percorso = $config["arch_folder"];
				} else if (isset($allegato["online"]) && $allegato["online"] == "S;S") {
					$allegato["hidden"] = 'S';
				}
					$allegato["codice_gara"] = $codice_gara;
					if (isset($_POST["sezione"]) && $_POST["sezione"]=="simog") {
						$tab_elenco = "tab_simog";
						$percorso .= "/simog";
						$allegato["sezione"] = "simog";
					}
					if (isset($_POST["sezione"]) && $_POST["sezione"]=="tmp_avv") {
						$allegato["sezione"] = "tmp_avv";
					}
					if (isset($_POST["sezione"]) && $_POST["sezione"]=="cpn") {
						$percorso .= "/cpn/{$_SESSION["ente"]["codice"]}";
						$allegato["sezione"] = "cpn";
					}
					if (isset($_POST["sezione"]) && $_POST["sezione"]=="mercato") {
						$percorso .= "/mercato_elettronico";
						$allegato["sezione"] = "mercato";
					}
					if (isset($_POST["sezione"]) && $_POST["sezione"]=="progetti") {
						$percorso .= "/progetti";
						$allegato["sezione"] = "progetti";
					}
					if (isset($_POST["sezione"]) && $_POST["sezione"]=="sda") {
						$percorso .= "/sda";
						$allegato["sezione"] = "sda";
					}
					if (isset($_POST["sezione"]) && $_POST["sezione"]=="albo") {
						$percorso .= "/albo";
						$allegato["sezione"] = "albo";
					}
					if (isset($_POST["sezione"]) && $_POST["sezione"]=="concorsi") {
						$percorso .= "/concorsi";
						$allegato["sezione"] = "concorsi";
					}
					if (isset($_POST["sezione"]) && $_POST["sezione"]=="dialogo") {
						$percorso .= "/dialogo";
						$allegato["sezione"] = "dialogo";
					}
					if (isset($_POST["sezione"]) && $_POST["sezione"]=="fabbisogno") {
						$percorso .= "/fabbisogno";
						$allegato["sezione"] = "fabbisogno";
					}
					if (isset($_POST["sezione"]) && $_POST["sezione"]=="documentale") {
						$percorso .= "/documentale/{$_SESSION["ente"]["codice"]}";
						$allegato["sezione"] = "documentale";
					}
					if (isset($_POST["sezione"]) && $_POST["sezione"]=="contratti") {
						$percorso .= "/contratti/{$_SESSION["ente"]["codice"]}";
						$allegato["sezione"] = "contratti";
					}
					if (isset($_POST["sezione"]) && $_POST["sezione"]=="esecuzione") {
						$percorso .= "/esecuzione";
						$allegato["sezione"] = "esecuzione";
					}
					if (isset($_POST["sezione"]) && $_POST["sezione"]=="guida") {
						$percorso .= "/guida";
						$allegato["sezione"] = "guida";
					}
					if (isset($_POST["sezione"]) && $_POST["sezione"]=="nso") {
						$percorso .= "/nso";
						$allegato["sezione"] = "nso";
					}
					if ($codice_gara <> 0) $percorso .= "/".$allegato["codice_gara"];
					if (isset($_POST["cartella"]) && (strpos($_POST["cartella"],"..")===false)) {
						 $percorso .= "/" . $_POST["cartella"];
						 $allegato["cartella"] = $_POST["cartella"];
					}

					if (!is_dir($percorso)) mkdir($percorso,0777,true);

			$copy = copiafile_chunck($allegato["filechunk"],$percorso."/",$config["chunk_folder"]."/".$_SESSION["codice_utente"]);
			if (file_exists($percorso."/".$copy["nome_fisico"])) {
				$allegato["nome_file"] = $copy["nome_file"];
				$allegato["riferimento"] = $copy["nome_fisico"];
				if (isset($_SESSION["ente"])) $allegato["codice_ente"] = $_SESSION["ente"]["codice"];

				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_allegati";
				$salva->operazione = "INSERT";
				$salva->oggetto = $allegato;
				$codice = $salva->save();

				if ($codice > 0) {
					if ($codice_gara!=0 && !isset($_POST["sezione"])) {
						log_gare($_SESSION["ente"]["codice"],$codice_gara,"UPLOAD","Allegato - " . $allegato["titolo"]);
					} else if ($codice_gara!=0 && $_POST["sezione"]=="esecuzione") {
						log_esecuzione($_SESSION["ente"]["codice"],$codice_gara,"UPLOAD","Allegato - " . $allegato["titolo"]);
					}
					if(!empty($allegato["sezione"]) && ($allegato["sezione"] == "contratti" || $allegato["sezione"] == "documentale")) {
						?>window.location.reload();<?
					} else {	?>
						var allegati = $("#cod_allegati").val();
						if (allegati != undefined) {
							allegati = allegati.split(";");
							allegati.push("<? echo $codice ?>");
							allegati = allegati.join(";");
							$("#cod_allegati").val(allegati);
						}

							$.ajax({
										type: "POST",
										url: "/allegati/tr_allegati.php",
										dataType: "html",
										data: "codice=<? echo $codice ?>",
										async:false,
										success: function(script) {
											$("#<? echo $tab_elenco ?>").prepend(script);
										}
							});
							<?
					}
					?>
					$("#<? echo $identifier ?>").remove();
					f_ready();
					$("#progress_bar").find(".complete_bar").removeClass("complete_bar");
					$("#submit_allegati").hide();
            <?
					} else { ?>
						alert("Errore nell'upload");
		                <?
					}
			} else { ?>
				alert("Errore nell'upload");
                <?
			}
		}
	}

?>
