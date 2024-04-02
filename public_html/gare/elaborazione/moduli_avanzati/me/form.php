<?
if ($mercato_elettronico) {
?>
<table width="100%">
	<tr>
		<td class="etichetta"style="background-color: #CCC; text-align:left;"><strong>Mercato elettronico di riferimento</strong></td>
	</tr>
	<tr>
		<td>
				<select name="codice_derivazione" id="codice_derivazione" title="Mercato elettronico di riferimento" rel="S;0;0;N">
					<option value="">Seleziona...</option>
				<?
					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$strsql = "SELECT codice FROM r_cpv_gare WHERE codice_gara = :codice_gara";
					$ris_me = $pdo->bindAndExec($strsql,$bind);
					$errore_me = false;
					if ($ris_me->rowCount()>0) {

						$bind = array();
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

						$sql  = "SELECT b_bandi_mercato.* FROM b_bandi_mercato JOIN r_cpv_bandi_mercato ON b_bandi_mercato.codice = r_cpv_bandi_mercato.codice_bando ";
						$sql .= "WHERE b_bandi_mercato.data_scadenza >= now() AND b_bandi_mercato.annullata = 'N' ";
						$sql .= "AND (b_bandi_mercato.codice_ente = :codice_ente OR b_bandi_mercato.codice_gestore = :codice_ente) ";
						$sql .= "AND (b_bandi_mercato.pubblica = '2' OR b_bandi_mercato.pubblica = '1') AND (";
						$cont_gen = 0;
						while($rec_cpv = $ris_me->fetch(PDO::FETCH_ASSOC)) {
							$cont_gen++;
							$cont_cpv = 0;
							while(strlen($rec_cpv["codice"])>1) {
							 $cont_cpv++;
							 $bind[":cpv_".$cont_gen."_".$cont_cpv] = $rec_cpv["codice"];
							 $sql .= "r_cpv_bandi_mercato.codice = :cpv_".$cont_gen."_".$cont_cpv." OR ";
							 $rec_cpv["codice"] = substr($rec_cpv["codice"],0,-1);
							}
						}
						$sql = substr($sql,0,-4);
						$sql .= ")";
						$sql .= " GROUP BY b_bandi_mercato.codice ";
						$sql .= " ORDER BY b_bandi_mercato.oggetto ";
						$ris_me = $pdo->bindAndExec($sql,$bind);
						if ($ris_me->rowCount()>0) {
							while($rec_me = $ris_me->fetch(PDO::FETCH_ASSOC)) {
								?><option value="<? echo $rec_me["codice"] ?>"><? echo $rec_me["oggetto"] ?></option><?
							}
						} else {
							$errore_me = true;
						}
					}
				?>
			</select>
				<? if ($errore_me) echo "<h3>Attenzione: Nessun mercato elettronico disponibile per le categorie selezionate</h3>"; ?>
    </td>
     </tr>
</table>
<?
	if ($record_gara["codice_derivazione"]!==0) {
		?>
			<script>
				$("#codice_derivazione").val("<? echo $record_gara["codice_derivazione"] ?>");
			</script>
		<?
	}
}
?>
