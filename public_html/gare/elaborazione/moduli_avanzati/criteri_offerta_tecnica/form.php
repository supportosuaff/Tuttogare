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
		console.log($("tr","#list_step_"+id).length);
		if ($("tr","#list_step_"+id).length > 0) {
			$("#is_steps_"+id).val("true");
		} else {
			$("#is_steps_"+id).val("");
		}
	}
	<?
	$sql_auto = "SELECT * FROM b_criteri_punteggi WHERE (economica = 'N' AND temporale = 'N') || (economica = 'S' AND migliorativa = 'S')";
	$ris_auto = $pdo->query($sql_auto);
	$codice_auto = array();
	if ($ris_auto->rowCount() > 0) {
		while($rec_auto = $ris_auto->fetch(PDO::FETCH_ASSOC)) $codice_auto[] = $rec_auto["codice"];
	}

	$sql_no_sub = "SELECT * FROM b_criteri_punteggi WHERE (economica = 'S' OR temporale = 'S') AND migliorativa = 'N'";
	$ris_no_sub = $pdo->query($sql_no_sub);
	$codice_no_sub = array();
	if ($ris_no_sub->rowCount() > 0) {
		while($rec_no_sub = $ris_no_sub->fetch(PDO::FETCH_ASSOC)) $codice_no_sub[] = $rec_no_sub["codice"];
	}
	?>
	function verifica_valutazione() {
		var no_sub = ["<?= implode('","',$codice_no_sub); ?>"];
		$('.punteggio_riferimento').each(function(index, el) {
			id_crit = $(this).data('identificativo');
			if ($.inArray($(this).val(), no_sub) != -1) {
				$('.sub_'+id_crit).remove();
				$('.add_sub_'+id_crit).hide();
			} else {
				$('.add_sub_'+id_crit).show();
			}
		});
		<?
			if ($record_gara["online"] == "S") {
				?>
				$(".div_valutazione_automatica").each(function() {
					id_div = $(this).attr("rel").split("_");
					if (typeof id_div[3] !== 'undefined') {
						id_div = id_div[2] + "_" + id_div[3];
					} else {
						id_div = id_div[2];
					}
					if ($("#tipo_cr_valutazione_"+id_div).val()=='N' && (<?
						$tec = 0;
						foreach($codice_auto AS $singleCode) {
							echo '$("#punteggio_riferimento_cr_valutazione_"+id_div).val()=="'.$singleCode.'"';
							$tec++;
							if ($tec < count($codice_auto)) echo " || ";
						}
					?>)) {
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
				<?
			} else {
				?>
					$(".div_valutazione_automatica").remove();
				<?
			}
			?>

	}
</script>
<input class="totale_punteggio" type="hidden" title="Punteggio complessivo" rel="S;0;0;N;100" id="punteggio_complessivo">
	<table width="100%">
		<tr><td class="etichetta"style="background-color: #CCC;"><strong>Criteri di valutazione dell'offerta</strong></td></tr>
		<tr><td>
			<div id="criteri_offerta_tecnica">
			<?
			$bind = array();
			$bind[":codice"] = $record_gara["codice"];
			$strsql = "SELECT * FROM b_valutazione_tecnica WHERE codice_padre = 0 AND codice_gara = :codice";
			$ris_valutazione = $pdo->bindAndExec($strsql,$bind);
			if ($ris_valutazione->rowCount()>0) {
				while($criterio_valutazione = $ris_valutazione->fetch(PDO::FETCH_ASSOC)) {
					$padre = true;
					$id = $criterio_valutazione["codice"];
					include("moduli_avanzati/criteri_offerta_tecnica/record.php");
				}
			} else {
				$padre = true;
				$id = "i_0";
				$criterio_valutazione = get_campi("b_valutazione_tecnica");
				include("moduli_avanzati/criteri_offerta_tecnica/record.php");
			}
		?>
		</div>
		<button class="aggiungi" onClick="aggiungi('moduli_avanzati/criteri_offerta_tecnica/record.php','#criteri_offerta_tecnica');verifica_valutazione();return false;"><img src="/img/add.png" alt="Aggiungi criterio">Aggiungi criterio</button>
	</td>
	</tr></table>
<script>
	verifica_punteggi();
</script>
