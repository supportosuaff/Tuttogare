<tr id="<? echo $record_partecipante["codice"] ?>">
			<td>
				<strong><? echo $record_partecipante["identificativo"] ?></strong></td>
				<?
					if (count($ris_buste)>0) {
					foreach($ris_buste AS $busta) {
								$bind = array();
								$bind[":codice_partecipante"] = $record_partecipante["codice"];
								$bind[":codice_busta"] = $busta["codice"];
								// $bind[":codice_fase"] = $busta["codice_fase"];
								$bind[":codice_gara"] = $record["codice"];
								$sql  = "SELECT * FROM b_buste_concorsi ";
								$sql .= "WHERE codice_partecipante = :codice_partecipante AND codice_busta  = :codice_busta ";
								$sql .= " AND codice_gara = :codice_gara";
								$ris_exist = $pdo->bindAndExec($sql,$bind);
								?>
								<td width="150" id="<? echo $record_partecipante["codice"] . "_" . $busta["codice"] ?>" style="text-align:center">
								<?
								if ($ris_exist->rowCount()>0) {
									$busta_partecipante = $ris_exist->fetch(PDO::FETCH_ASSOC);
									if ($busta_partecipante["aperto"] == "N") {
										if (!$lock) { ?>
										<form action="open.php" rel="validate" method="post">
											<input type="hidden" name="codice_gara" value="<? echo $record["codice"] ?>">
											<input type="hidden" name="codice_partecipante" value="<? echo $record_partecipante["codice"] ?>">
											<input type="hidden" name="codice_fase" value="<? echo $fase["codice"] ?>">
											<input type="hidden" name="codice_busta" value="<? echo $busta["codice"] ?>">
											<input type="hidden" name="private_key" class="private" rel="S;0;0;A" title="Chiave privata">
											<input type="submit" value="Apri busta">
										</form>
										<? } else { ?>
											Impossibile aprire
										<? }
									} else { ?>
										<a href="/allegati/download_allegato.php?codice=<? echo $busta_partecipante["codice_allegato"] ?>" title="Scarica Allegato">
											<img src="/img/download.png" alt="Scarica Allegato" width="25"></a>
											<a href="/allegati/open_p7m.php?codice=<? echo $busta_partecipante["codice_allegato"] ?>" title="Estrai Contenuto">
												<img src="/img/p7m.png" alt="Download Allegato" width="25">
											</a>
											<br>Busta aperta
											<? }
										} else { ?>
											Non presentata
										<? } ?>
									</td>
							<?	}
						} ?>
</tr>
