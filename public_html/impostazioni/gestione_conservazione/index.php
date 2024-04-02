<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$codice_ente = ($_SESSION["gerarchia"] === "0" && $_SESSION["record_utente"]["codice_ente"] == 0 && isset($_GET["codice_ente"])) ? $_GET["codice_ente"] : $_SESSION["record_utente"]["codice_ente"];
  if(empty($_SESSION["codice_utente"]) || !check_permessi("impostazioni",$_SESSION["codice_utente"])) {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  } else {
		$show = TRUE;
		?>
		<style media="screen">
			input[type="text"] {
				width: 100%;
				box-sizing : border-box;
				font-family: Tahoma, Geneva, sans-serif;
				font-size: 1em
			}
			input[type="text"]:disabled {
				background: #dddddd;
			}
		</style>
		<h1>IMPOSTAZIONI DI CONSERVAZIONE</h1>
		<form action="save.php" method="post" rel="validate">
			<div class="comandi"><button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button></div>
			<?
			if($_SESSION["record_utente"]["codice_ente"] == 0 && $_SESSION["gerarchia"] === "0" && isset($_SESSION["ente"])) {
				$show = !empty($_GET["codice_ente"]) ? TRUE : FALSE;
				$bind = array();
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$sql = "SELECT * FROM b_enti WHERE ((codice = :codice_ente) OR (sua = :codice_ente)) ORDER BY denominazione";
				$ris = $pdo->bindAndExec($sql,$bind);
				if($ris->rowCount() > 0) {
					?>
					<table class="box" style="width:100%">
						<tr>
							<td style="width:10%">
								<b>Ente: *</b>
							</td>
							<td>
								<select name="codice_ente" rel="S;1;0;N" title="Ente" onchange="window.location.href='/impostazioni/gestione_conservazione/index.php?codice_ente='+$(this).val()">
									<option value="">Seleziona..</option>
									<option value="0">Tutti</option>
									<?
									while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
										?><option <?= $codice_ente == $rec["codice"] ? 'selected="selected"' : null ?> value="<?= $rec["codice"] ?>"><?= $rec["denominazione"] ?></option><?
									}
									?>
								</select>
							</td>
						</tr>
					</table>
					<?
				}
			} elseif(!isset($_SESSION["ente"]) && $_SESSION["gerarchia"] === "0") {
				$codice_ente = 0;
			}
			if($show) {
				?>
				<div id="tabs">
					<ul>
						<li><a href="#metadati">Metadati File</a></li>
						<? if($codice_ente != 0) { ?><li><a href="#responsabile">Responsabile Conservazione</a></li><? } ?>
					</ul>
					<div id="metadati">
						<button type="button" class="submit_big" style="background-color:#feae1b; border:none !important; cursor:pointer" onClick="aggiungi('metadati/campo.php','#contenitore_metadati');return false;">Aggiungi Campo</button>
						<?
						$sql_metadati = "SELECT * FROM b_schema_metadati WHERE soft_delete = 'N' AND codice_ente = :codice_ente";
						$bind_metadati = array(':codice_ente' => $codice_ente);
						$ris_metadati = $pdo->bindAndExec($sql_metadati,$bind_metadati);
						if($ris_metadati->rowCount() < 1) {
							if(isset($_SESSION["ente"])) {
								$bind_metadati[':codice_ente'] = $_SESSION["ente"]["codice"];
								$ris_metadati = $pdo->bindAndExec($sql_metadati,$bind_metadati);
								if($ris_metadati->rowCount() < 1) {
									$bind_metadati[':codice_ente'] = 0;
									$ris_metadati = $pdo->bindAndExec($sql_metadati,$bind_metadati);
								}
							} else {
								$bind_metadati[':codice_ente'] = 0;
								$ris_metadati = $pdo->bindAndExec($sql_metadati,$bind_metadati);
							}
						}
						?>
						<div id="contenitore_metadati">
							<?
							if($ris_metadati->rowCount() > 0) {
								while($record_campo = $ris_metadati->fetch(PDO::FETCH_ASSOC)) {
									include 'metadati/campo.php';
								}
							}
							?>
						</div>
						<button type="button" class="submit_big" style="background-color:#feae1b; border:none !important; cursor:pointer" onClick="aggiungi('metadati/campo.php','#contenitore_metadati');return false;">Aggiungi Campo</button>
					</div>
					<?
						if($codice_ente != 0) {
							?>
							<div id="responsabile">
								<div class="box">
									<?
									$rec_conservatore = get_campi('b_conservatori');
									$ris_conservatore = $pdo->bindAndExec("SELECT * FROM b_conservatori WHERE codice_ente = :codice_ente", array(':codice_ente' => $codice_ente));
									if($ris_conservatore->rowCount() > 0) {
										$rec_conservatore = $ris_conservatore->fetch(PDO::FETCH_ASSOC);
									}
									?>
									<table id="tab_moduli" width="100%">
										<tbody>
					            <tr>
												<td class="etichetta">
					                Ruolo: *
					              </td>
					              <td>
													<input type="hidden" name="conservatore[codice]" value="<?= $rec_conservatore["codice"] ?>">
													<input type="hidden" name="conservatore[codice_ente]" value="<?= $rec_conservatore["codice_ente"] ?>">
					                <select id="ruolo" name="conservatore[ruolo]" rel="S;1;0;A" title="Titolo" id="ruolo_ufficiale_rogante" data-input="#ruolo_ufficiale_rogante_altro" onchange="check_altro($(this))">
					                  <option value="">Seleziona..</option>
														<?
														$ris = $pdo->bindAndExec("SELECT DISTINCT b_conservatori.ruolo FROM b_conservatori");
														if($ris->rowCount() > 0) {
															while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
																?>
																<option <?= strtolower(html_entity_decode($rec_conservatore["ruolo"], ENT_QUOTES, 'UTF-8')) == strtolower(html_entity_decode($rec["ruolo"], ENT_QUOTES, 'UTF-8')) ? 'selected="seleceted"' : null  ?> value="<?= strtolower(html_entity_decode($rec["ruolo"], ENT_QUOTES, 'UTF-8')) ?>"><?= ucfirst(strtolower(html_entity_decode($rec["ruolo"], ENT_QUOTES, 'UTF-8'))) ?></option>
																<?
															}
														}
														?>
					                  <option value="altro">Altro ruolo</option>
					                </select>
													<input type="text" style="display:none" disabled="disabled" rel="N;0;0;A" title="Altro ruolo ufficiale Rogante" name="conservatore[ruolo_altro]" id="ruolo_ufficiale_rogante_altro">
					              </td>
												<td class="etichetta">
					                Titolo: *
					              </td>
					              <td>
					                <select name="conservatore[titolo]" rel="S;1;0;A" title="Titolo" id="titolo" data-input="#titolo_ufficiale_rogante_altro" onchange="check_altro($(this))">
					                  <option value="">Seleziona..</option>
														<?
														$ris = $pdo->bindAndExec("SELECT DISTINCT b_conservatori.titolo FROM b_conservatori");
														if($ris->rowCount() > 0) {
															while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
																?>
																<option <?= strtolower(html_entity_decode($rec_conservatore["titolo"], ENT_QUOTES, 'UTF-8')) == strtolower(html_entity_decode($rec["titolo"], ENT_QUOTES, 'UTF-8')) ? 'selected="seleceted"' : null  ?> value="<?= strtolower(html_entity_decode($rec["titolo"], ENT_QUOTES, 'UTF-8')) ?>"><?= ucfirst(strtolower(html_entity_decode($rec["titolo"], ENT_QUOTES, 'UTF-8'))) ?></option>
																<?
															}
														}
														?>
					                  <option value="altro">Altro titolo</option>
					                </select>
													<input type="text" style="display:none" disabled="disabled" rel="N;0;0;A" title="Altro titolo ufficiale Rogante" name="conservatore[titolo_altro]" id="titolo_ufficiale_rogante_altro">
					              </td>
											</tr>
											<tr>
												<td class="etichetta">Nome: *</td>
												<td><input type="text" id="nome" name="conservatore[nome]" value="<?= $rec_conservatore["nome"] ?>" rel="S;1;0;A" title="Nome"></td>
												<td class="etichetta">Cognome: *</td>
												<td><input type="text" id="cognome" name="conservatore[cognome]" value="<?= $rec_conservatore["cognome"] ?>" rel="S;1;0;A" title="Cognome"></td>
											</tr>
											<tr>
												<td class="etichetta">Data di nascita:</td>
												<td><input type="text" class="datepick" id="data_nascita" name="conservatore[data_nascita]" value="<?= mysql2date($rec_conservatore["data_nascita"]) ?>" rel="N;10;10;D" title="Data di nascita"></td>
												<td class="etichetta">Comune di nascita:</td>
												<td><input type="text" id="comune_nascita" name="conservatore[comune_nascita]" value="<?= $rec_conservatore["comune_nascita"] ?>" rel="N;1;0;A" title="Comune di nascita"></td>
											</tr>
											<tr>
												<td class="etichetta">Provincia di nascita	:</td>
												<td><input type="text" id="provincia_nascita" name="conservatore[provincia_nascita]" value="<?= $rec_conservatore["provincia_nascita"] ?>" rel="N;1;0;A" title="Provincia di nascita	"></td>
												<td class="etichetta">Codice Fiscale:</td>
												<td><input type="text" id="cf" name="conservatore[cf]" value="<?= $rec_conservatore["cf"] ?>" title="Codice Fiscale"></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<?
						}
					?>
				</div>
				<script type="text/javascript">
					$("#tabs").tabs();
					function check_altro(element) {
						if(element.data('input').length > 0) {
							var inp = $(element.data('input'));
							inp.prop({'disabled':'disabled', 'rel':'N;0;0;A'});
							inp.attr({'disabled':'disabled', 'rel':'N;0;0;A'});
							if(inp.is(':visible')) {
								inp.slideUp('fast');
								inp.blur();
							}
							if(element.val() == "altro") {
								inp.removeAttr('disabled');
								inp.removeProp('disabled');
								inp.prop({'rel':'S;1;0;A'});
								inp.attr({'rel':'S;1;0;A'});
								inp.slideDown('fast');
							}
						}
					}
				</script>
				<input type="submit" class="submit_big" value="Salva">
				<?
			}
			?>
		</form>
		<?
	}
	include_once($root."/layout/bottom.php");
	?>
