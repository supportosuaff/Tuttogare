<?
if ($derivata) {
?>
<table width="100%">
	<tr>
		<td class="etichetta" style="background-color: #CCC; text-align:left;"><strong>Bando di riferimento</strong></td>
	</tr>
	<tr>
		<td>
				<select name="codice_derivazione" id="codice_derivazione" title="Bando di riferimento" rel="S;0;0;N">
					<option value="">Seleziona...</option>
					<?
					$errore_aq = false;
					$bind = array();
					$bind[":codice"] = $record_gara["procedura"];
					$strsql = "SELECT * FROM b_procedure WHERE derivata > 0 AND codice = :codice";
					$ris_aq = $pdo->bindAndExec($strsql,$bind);
					if ($ris_aq->rowCount()>0) {
						$procedura_derivata = $ris_aq->fetch(PDO::FETCH_ASSOC);
						$procedura_derivata = $procedura_derivata["derivata"];

						$bind = array();
						$bind[":codice"] = $procedura_derivata;
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

						$strsql  = "SELECT b_gare.* FROM b_gare JOIN r_partecipanti ON b_gare.codice = r_partecipanti.codice_gara ";
						$strsql .= "WHERE ";
						$strsql .= "(b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente) ";
						$strsql .= "AND r_partecipanti.primo = 'S' AND b_gare.procedura = :codice GROUP BY b_gare.codice ";
						$ris_aq = $pdo->bindAndExec($strsql,$bind);
						if ($ris_aq->rowCount()>0) {
								while($rec_aq = $ris_aq->fetch(PDO::FETCH_ASSOC)) {
									?><option value="<? echo $rec_aq["codice"] ?>"><? echo $rec_aq["oggetto"] ?></option><?
								}
						} else {
							$errore_aq = true;
						}
					}
				?>
			</select>
				<? if ($errore_aq) echo "<h3>Attenzione: Nessun bando di appoggio disponibile</h3>"; ?>
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
