<?
	include_once "../../../config.php";
	include_once $root . "/layout/top.php";

	if(empty($_GET["codice"]) || empty($_SESSION["codice_utente"]) || !isset($_SESSION["ente"]) || !check_permessi("contratti",$_SESSION["codice_utente"])) {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
	} else {
		$codice = $_GET["codice"];
		$codice_gara = !empty($_GET["codice_gara"]) ? $_GET["codice_gara"] : null;
		$bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ':codice' => $codice);
	  $sql  = "SELECT b_contratti.*, b_conf_modalita_stipula.invio_remoto FROM b_contratti JOIN b_conf_modalita_stipula ON b_contratti.modalita_stipula = b_conf_modalita_stipula.codice ";
	  if(!empty($codice_gara) && $_SESSION["gerarchia"] > 1) {
	    $sql .= "JOIN b_permessi ON b_contratti.codice_gara = b_permessi.codice_gara ";
	  } elseif (empty($codice_gara) && $_SESSION["gerarchia"] > 1) {
			$sql .= "JOIN b_permessi_contratti ON b_contratti.codice = b_permessi_contratti.codice_contratto ";
		}
	  $sql .= "WHERE b_contratti.codice = :codice ";
	  $sql .= "AND b_contratti.codice_gestore = :codice_ente ";
	  if ($_SESSION["gerarchia"] > 0) {
	    $bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
	    $sql .= "AND (b_contratti.codice_ente = :codice_ente_utente OR b_contratti.codice_gestore = :codice_ente_utente) ";
	  }
	  if (!empty($codice_gara)) {
	    $bind[":codice_gara"] = $codice_gara;
	    $sql .= " AND b_contratti.codice_gara = :codice_gara";
	    if($_SESSION["gerarchia"] > 1) {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
	      $sql .= " AND (b_permessi.codice_utente = :codice_utente)";
	    }
	  } else {
			if($_SESSION["gerarchia"] > 1) {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$sql .= " AND (b_permessi_contratti.codice_utente = :codice_utente)";
			}
		}
	  $ris = $pdo->bindAndExec($sql,$bind);
		$href_contratto = null;
		if($ris->rowCount() == 1) {
			$rec_contratto = $ris->fetch(PDO::FETCH_ASSOC);
			$href_contratto = "?codice=".$rec_contratto["codice"] . (!empty($rec_contratto["codice_gara"]) ? "&codice_gara=".$rec_contratto["codice_gara"] : null);
			?>
			<h1>ALLEGATI</h1>
			<form action="save.php" method="post" rel="validate">
				<input type="hidden" name="codice_contratto" value="<?= $rec_contratto["codice"] ?>">
				<input type="hidden" name="codice_gara" value="<?= $rec_contratto["codice_gara"] ?>">
				<table width="100%" id="tab_allegati">
					<thead>
						<tr>
							<td>File</td>
							<!-- <td width="15%" style="text-align:center">Aggiungere al contratto</td> -->
						</tr>
					</thead>
					<tbody>
						<?
						$ris_allegati = $pdo->bindAndExec("SELECT b_allegati_contratto.*, b_modulistica_contratto.titolo as titolo_modulo FROM b_allegati_contratto LEFT JOIN b_modulistica_contratto ON b_modulistica_contratto.codice = b_allegati_contratto.codice_modulo WHERE b_allegati_contratto.codice_contratto = :codice_contratto ORDER BY b_allegati_contratto.timestamp ASC", array(':codice_contratto' => $rec_contratto["codice"]));
						if($ris_allegati->rowCount() > 0) {
							while ($rec_allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
								$size = number_format(floor(filesize("{$config["arch_folder"]}/allegati_contratto/{$rec_allegato["codice_contratto"]}/{$rec_allegato["riferimento"]}")/1024),0,",",".");
								?>
								<tr>
									<td><?= !empty($rec_allegato["titolo_modulo"]) ? 'Modulo: '.$rec_allegato["titolo_modulo"].'<br>' : ucfirst($rec_allegato["tipologia"]).':<br>' ?><a href="download_allegato.php?codice=<?= $rec_contratto["codice"] ?>&codice_allegato=<?= $rec_allegato["codice"] ?>"><strong><?= !empty($rec_allegato["titolo"]) ? $rec_allegato["titolo"] : $rec_allegato["nome_file"] ?></strong></a> - <?= $size ?> KB </td>
									<?
                  /*
                   *<td><?
                    if($rec_allegato["tipologia"] != "contratto") {
                      ?>
                      <select name="allegato[<?= $rec_allegato["codice"] ?>][includi]" rel="S;1;0;A" title="Includi allegato">
                        <option <?= $rec_allegato["includi"] == "N" ? 'selected="selected"' : null ?> value="N">No</option>
                        <option <?= $rec_allegato["includi"] == "S" ? 'selected="selected"' : null ?> value="S">Si</option>
                      </select>
                      <?
                    }
                  ?></td>
                  */
                  ?>
								</tr>
								<?
							}
						}
						$ris_allegati = $pdo->bindAndExec("SELECT b_allegati.* FROM b_allegati WHERE b_allegati.codice_gara = :codice_contratto AND sezione = 'contratti' AND online = 'S' ORDER BY b_allegati.timestamp ASC", array(':codice_contratto' => $rec_contratto["codice"]));
						if($ris_allegati->rowCount() > 0) {
							while ($rec_allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
								if(file_exists($config["pub_doc_folder"]."/allegati/contratti/{$_SESSION["ente"]["codice"]}/{$rec_allegato["codice_gara"]}/{$rec_allegato["riferimento"]}")) {
									$size = number_format(floor(filesize($config["pub_doc_folder"]."/allegati/contratti/{$_SESSION["ente"]["codice"]}/{$rec_allegato["codice_gara"]}/{$rec_allegato["riferimento"]}")/1024),0,",",".");
									?>
									<tr>
										<td><?= !empty($rec_allegato["titolo"]) ? $rec_allegato["titolo"].'<br>' : ucfirst($rec_allegato["nome_file"]).':<br>' ?>
											<a href="<?= "/documenti/allegati/contratti/{$_SESSION["ente"]["codice"]}/{$rec_allegato["codice_gara"]}/{$rec_allegato["nome_file"]}" ?>">
												<strong><?= !empty($rec_allegato["titolo"]) ? $rec_allegato["titolo"] : $rec_allegato["nome_file"] ?></strong>
											</a> - <?= $size ?> KB
										</td>
										<?
                    /*
                     <td>
                      <select name="allegato[generali][<?= $rec_allegato["codice"] ?>][includi]" rel="S;1;0;A" title="Includi allegato">
                        <option <?= $rec_allegato["includi"] == "N" ? 'selected="selected"' : null ?> value="N">No</option>
                        <option <?= $rec_allegato["includi"] == "S" ? 'selected="selected"' : null ?> value="S">Si</option>
                      </select>
                     </td>
                    */
                    ?>
									</tr>
									<?
								}
							}
						}
						?>
					</tbody>
				</table>
				<!-- <input type="submit" class="submit_big" style="cursor:pointer" value="Salva"> -->
			</form>
			<button onClick="open_allegati();" class="submit_big" style="width:100%; background-color:#ff9800; border:none; cursor:pointer"><i class="fa fa-paperclip"></i> Allega file</button>
			<?
			$form_upload["sezione"] = "contratti";
			$form_upload["codice_gara"] = $rec_contratto["codice"];
			$form_upload["online"] = "S;S";
			include_once($root."/allegati/form_allegati.php");
			include_once($root."/contratti/ritorna_pannello_contratto.php");
		}
	}
	include_once($root."/layout/bottom.php");
?>
