<?
  $exist = false;
  $to = 1;
  unset($valori);
  if (isset($_POST["id"]) && isset($_POST["target"])) {
    session_start();
    include("../../../../config.php");
    include_once($root."/inc/funzioni.php");
;
    $id_repeat = $_SESSION["id_repeat"] + 1;
    $certificato_soa = get_campi("b_certificazioni_soa");
    $certificato_soa["importo"] = "";
    $certificato_soa["numero"] = "";
  } else {
    $exist = true;
  }
?>
<div id="soa_<? echo $id_repeat ?>">
  <? if ($id_repeat > 0) echo "<hr style='border-bottom:3px solid #333'>"; ?>
<table width="100%">
	<tr><td class="etichetta">Ente certificatore</td><td colspan="3"><input type="text" name="soa[certificati][<? echo $id_repeat ?>][ente]" style="width:95%"  title="Ente certificatore" rel="S;3;255;A" id="ente_soa_<? echo $id_repeat ?>" value="<? echo $certificato_soa["ente"] ?>"></td>

    <td rowspan="5"><button onClick="$('#soa_<?= $id_repeat ?>').remove(); return false;" class="submit_big" style="background-color:#C30" title="Elimina elemento"><span class="fa fa-remove"></span></button></td>
  </tr>
  <tr>
    <td class="etichetta">Numero attestazione</td>
    <td><input type="text" name="soa[certificati][<? echo $id_repeat ?>][numero]" style="width:95%"  title="Numero attestazione" rel="N;0;255;A" id="numero_soa_<? echo $id_repeat ?>" value="<? echo $certificato_soa["numero"] ?>"></td>
    <td class="etichetta">Importo</td>
    <td><input type="text" name="soa[certificati][<? echo $id_repeat ?>][importo]" style="width:95%"  title="Importo" rel="S;0;255;N" id="importo_soa_<? echo $id_repeat ?>" value="<? echo $certificato_soa["importo"] ?>"></td>
</tr>
    <tr><td class="etichetta">Categoria</td><td>
			<select rel="S;0;0;N" title="Categoria SOA" name="soa[certificati][<? echo $id_repeat ?>][codice_categoria]" id="codice_categoria_soa_<? echo $id_repeat ?>">
				<option value="">Seleziona...</option>
				<?
	$sql_soa = "SELECT * FROM b_categorie_soa WHERE attivo = 'S' ORDER BY codice";
	$ris_elenco_soa = $pdo->query($sql_soa);
	if ($ris_elenco_soa->rowCount()>0) {
		while($oggetto_soa = $ris_elenco_soa->fetch(PDO::FETCH_ASSOC)) {
			?>
											<option value="<? echo $oggetto_soa["codice"] ?>"><strong><? echo $oggetto_soa["id"] ?></strong></option>
											<?
		}
	}
?>
			</select>
			<script>
				$("#codice_categoria_soa_<? echo $id_repeat ?>").val('<? echo $certificato_soa["codice_categoria"] ?>');
			</script>
		</td>
		<td class="etichetta">Classifica</td><td>
			<select rel="S;0;0;N" title="Classifica SOA" name="soa[certificati][<? echo $id_repeat ?>][codice_classifica]" id="codice_classifica_soa_<? echo $id_repeat ?>">
				<option value="">Seleziona...</option>
				<?
        	$sql_soa = "SELECT * FROM b_classifiche_soa WHERE attivo = 'S' ORDER BY codice";
        	$ris_elenco_soa = $pdo->query($sql_soa);
        	if ($ris_elenco_soa->rowCount()>0) {
        		while($oggetto_soa = $ris_elenco_soa->fetch(PDO::FETCH_ASSOC)) {
        			?>
							<option value="<? echo $oggetto_soa["codice"] ?>"><strong><? echo $oggetto_soa["id"] ?></option>
							<?
        		}
        	}
        ?>
			</select>
			<script>
				$("#codice_classifica_soa_<? echo $id_repeat ?>").val('<? echo $certificato_soa["codice_classifica"] ?>');
			</script>
		</td></tr>
		<tr>
    <td class="etichetta">Data rilascio*</td><td><input type="text" name="soa[certificati][<? echo $id_repeat ?>][data_rilascio]" class="datepick"  title="Data rilascio" rel="S;10;10;D" id="data_rilascio_soa_<? echo $id_repeat ?>" value="<? echo $certificato_soa["data_rilascio"] ?>"></td>
     <td class="etichetta">Data scadenza</td><td><input type="text" name="soa[certificati][<? echo $id_repeat ?>][data_scadenza]" class="datepick"  title="Data scadenza" rel="N;10;10;D" id="data_scadenza_soa_<? echo $id_repeat ?>" value="<? echo $certificato_soa["data_scadenza"] ?>"></td></tr>
   </table>

</div>
<? $_SESSION["id_repeat"] = $id_repeat; ?>
