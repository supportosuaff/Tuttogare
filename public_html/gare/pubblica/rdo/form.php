<? if (isset($record)) { ?>
<ul>
	<li><a href="#pubblica">Pubblicazione</a></li>
	<? if ($record_procedura["invito"] == "S") { ?>
    	<li><a href="#invito">Inviti</a></li>
    <? } ?>
</ul>

<div id="pubblica">
<? include($root."/gare/pubblica/common.php"); ?>
</div>
<? if ($record_procedura["invito"] == "S") { ?>
	<div id="invito">
      <?
			$bind = array();
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$bind[":codice_derivazione"] = $record["codice_derivazione"];

			$strsql  = "SELECT b_utenti.*, b_gruppi.gruppo AS tipo, b_operatori_economici.ragione_sociale, b_operatori_economici.codice_fiscale_impresa ";
			$strsql .= "FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice LEFT JOIN b_operatori_economici ON b_utenti.codice = b_operatori_economici.codice_utente ";
			$strsql .= "JOIN r_partecipanti_me ON r_partecipanti_me.codice_operatore = b_operatori_economici.codice ";
			$strsql .= "JOIN b_bandi_mercato ON b_bandi_mercato.codice = r_partecipanti_me.codice_bando ";
			$strsql .= "JOIN r_cpv_operatori_me ON r_cpv_operatori_me.codice_bando = b_bandi_mercato.codice AND r_cpv_operatori_me.codice_operatore = b_operatori_economici.codice ";
			$strsql .= "WHERE b_gruppi.gerarchia > 2 AND b_utenti.attivo = 'S' AND r_partecipanti_me.ammesso = 'S' ";
			$strsql .= "AND (b_bandi_mercato.annullata = 'N' AND  b_bandi_mercato.data_scadenza > now() ";
			$strsql .= "AND (b_bandi_mercato.codice_ente = :codice_ente OR b_bandi_mercato.codice_gestore = :codice_ente) ";
			$strsql .= "AND (b_bandi_mercato.pubblica = '2' OR b_bandi_mercato.pubblica = '1') AND b_bandi_mercato.codice = :codice_derivazione)";
			if (isset($string_cpv) && $string_cpv != "") {
				$strsql .= " AND (";
				$categorie = explode(";",$string_cpv);
				$cont = 0;
					foreach($categorie as $codice) {
						$cont++;
						if ($codice != "") {
							$bind[":cpv_".$cont] = $codice;
							$strsql .= "(r_cpv_operatori_me.codice = :cpv_" . $cont . " ";
							if (strlen($codice)>2) {
								$diff = strlen($codice) - 2;
								for($i=1;$i<=$diff;$i++) {
									$bind[":sub_".$cont."_".$i] = substr($codice,0,$i*-1);
									$strsql .= "OR r_cpv_operatori_me.codice = :sub_".$cont."_".$i." ";
								}
							}
						$strsql.=") OR ";
					}
				}
				$strsql = substr($strsql,0,-4);
				$strsql .= ")";
			}
			$strsql .= " GROUP BY b_utenti.codice ";
			$strsql .= " ORDER BY ragione_sociale,cognome,nome,dnascita" ;
			$ris_operatori  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
			$indirizzi = array();
			$msg = "";
			if ($ris_operatori->rowCount()===0) {

				$msg = "<div class='ui-state-highlight padding'>Nessun operatore abilitato nelle categorie selezionate, la ricerca &egrave; stata estesa a tutti i partecipanti al mercato elettronico di riferimento.</div><br><br>";

				$bind = array();
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$bind[":codice_derivazione"] = $record["codice_derivazione"];

				$strsql  = "SELECT b_utenti.*, b_gruppi.gruppo AS tipo, b_operatori_economici.ragione_sociale, b_operatori_economici.codice_fiscale_impresa ";
				$strsql .= "FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice LEFT JOIN b_operatori_economici ON b_utenti.codice = b_operatori_economici.codice_utente ";
				$strsql .= "JOIN r_partecipanti_me ON r_partecipanti_me.codice_operatore = b_operatori_economici.codice ";
				$strsql .= "JOIN b_bandi_mercato ON b_bandi_mercato.codice = r_partecipanti_me.codice_bando ";
				$strsql .= "WHERE b_gruppi.gerarchia > 2 AND b_utenti.attivo = 'S' AND r_partecipanti_me.ammesso = 'S' ";
				$strsql .= "AND (b_bandi_mercato.annullata = 'N' AND  b_bandi_mercato.data_scadenza > now() ";
				$strsql .= "AND (b_bandi_mercato.codice_ente = :codice_ente OR b_bandi_mercato.codice_gestore = :codice_ente) ";
				$strsql .= "AND (b_bandi_mercato.pubblica = '2' OR b_bandi_mercato.pubblica = '1') AND b_bandi_mercato.codice = :codice_derivazione)";
				$strsql .= " GROUP BY b_utenti.codice ";
				$strsql .= " ORDER BY ragione_sociale,cognome,nome,dnascita" ;
				$ris_operatori  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
			}

			if ($ris_operatori->rowCount()>0) {
				echo $msg;
			?>
			<script>
			function invitato(codice) {
				var invitati = $("#indirizzi").val().split(";");
				if ($.inArray(codice,invitati)==-1) {
					invitati.push(codice);
					$("#indirizzi").val(invitati.join(";"));
					$("#invia_"+codice).attr("src","/img/del.png");
					$("#"+codice).removeClass("disabilitato");
				} else {
					index = $.inArray(codice,invitati);
					invitati.splice(index,1);
					$("#indirizzi").val(invitati.join(";"));
					$("#invia_"+codice).attr("src","/img/add.png");
					$("#"+codice).addClass("disabilitato");
				}
			}
			function invita_tutti() {
				$("#indirizzi").val('');
				var invitati = new Array();
				elenco.page.len(-1).draw();
				$(".invita").each(function(){
					codice = $(this).attr("id").split("_");
					codice = codice[1];
					invitati.push(codice);
					$(this).attr("src","/img/del.png");
					$(this).parents('tr').removeClass("disabilitato");
				});
				$("#indirizzi").val(invitati.join(";"));
			}
			function rimuovi_tutti() {
				$("#indirizzi").val('');
				elenco.page.len(-1).draw();
				$(".invita").each(function() {
					codice = $(this).attr("id").split("_");
					codice = codice[1];
					$(this).attr("src","/img/add.png");
					$(this).parents('tr').addClass("disabilitato");
				});
			}
		</script>
				<div style="text-align:right">
					<button class="submit" style="background-color:#0C0" type="button" onClick='invita_tutti();return false;' title="Invita tutti"><img width="24" src="/img/add.png" style="vertical-align:middle"> Invita tutti</button>
					<button class="submit" style="background-color:#C00" type="button" onClick='rimuovi_tutti();return false;' title="Rimuovi tutti"><img width="24" src="/img/del.png" style="vertical-align:middle"> Rimuovi tutti</button>
				</div>
				<table style="text-align:center; width:100%; font-size:0.8em" id="invitati">
				<thead>
					<tr><th>Ragione Sociale</th><th>Referente</th><th width="10">Tipo</th><th width="100">Partita IVA</th><th width="10">Invito</th></tr>
				</thead>
				<tbody>
					<?
						$rel = "N;0;0;A";
						while ($record_operatore = $ris_operatori->fetch(PDO::FETCH_ASSOC)) {
							$nominativo		= ucwords($record_operatore["cognome"] . " " . $record_operatore["nome"]);
							if ($record_operatore["codice_fiscale_impresa"] == "") $record_operatore["codice_fiscale_impresa"] = $record_operatore["cf"];
							$invitato = true;
							$bind = array();
							$bind[":codice_utente"] = $record_operatore["codice"];
							$bind[":codice_gara"] =$record["codice"];
							$sql = "SELECT * FROM r_inviti_gare WHERE codice_utente = :codice_utente AND codice_gara = :codice_gara";
							$ris_inviti  = $pdo->bindAndExec($sql,$bind); //invia la query contenuta in $strsql al database apero e connesso
							$classe = "";
							$img = "del";
							if ($ris_inviti->rowCount()==0) {
								$invitato = false;
								$classe="disabilitato";
									$img = "add";
								}
							?>
							<tr class="<? echo $classe ?>" id="<? echo $record_operatore["codice"] ?>">
								<td style="text-align:left"><strong><? echo strtoupper($record_operatore["ragione_sociale"]) ?></strong></td>
								<td style="text-align:left"><? echo $nominativo ?></td>
								<td><? echo $record_operatore["tipo"] ?></td>
								<td><? echo strtoupper($record_operatore["codice_fiscale_impresa"]); ?></td>
								<td width="10"><? if (!$invitato) { ?><input id="invia_<? echo $record_operatore["codice"] ?>" class="invita" type="image" src="/img/<? echo $img ?>.png" onClick='invitato("<? echo $record_operatore["codice"] ?>");return false;' width="24" title="Invita"><? } else { ?>Gi&agrave; invitato<? } ?></td>
							</tr>
							<?
							}
						}
						?>
				</tbody>
			</table>
      <script>
			var elenco = $("#invitati").dataTable({
				"paging": true,
				"lengthChange": true,
				"searching": true,
				"ordering": true,
				"info": false,
				"autoWidth": false,
				"pageLength": -1,
				"lengthMenu": [[5,10,25,50,-1],[5, 10, 25, 50,"Tutti"]]});
		 </script>
         <div class="clear"></div>
          <?
	} else {
		?>
			<div class='ui-state-warning padding'>Nessun operatore abilitato nelle categorie selezionate</div>
		<?
	}
		?>
        <input type="hidden" id="indirizzi" name="indirizzi" title="Destinatari" value="<? echo implode(";",$indirizzi) ?>" rel="<? echo $rel ?>">
    </div>
<? } ?>
