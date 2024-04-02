<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	$guue = array();
	$form = array();
	$numero_form = "";

	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("guue",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	echo "<h1>NUOVA PUBBLICAZIONE GUUE</h1>";

	$codice_gara;
	$numero_form = 0;
	unset($_SESSION["guue"]);
	$_SESSION["guue"]["v_form"] = "2_0_9_S3";
	if($_SESSION["developEnviroment"]) $_SESSION["guue"]["v_form"] = "2_0_9_S3";

	if(!empty($_GET["cod"])) {
		$codice_pubblicazione = $_GET["cod"];

		$bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ":codice" => $codice_pubblicazione);
		$sql = "SELECT * FROM b_pubb_guue WHERE codice = :codice AND codice_ente = :codice_ente AND soft_delete = FALSE ";
		if(!empty($_GET["codice_gara"])) {
			$codice_gara = $_GET["codice_gara"];
			$sql .= "AND codice_gara = :codice_gara";
			$bind[":codice_gara"] = $codice_gara;
		}

		$ris = $pdo->bindAndExec($sql, $bind);

		if($ris->rowCount() > 0)
		{
			$rec = $ris->fetch(PDO::FETCH_ASSOC);
			if (!empty($rec["codice_gara"])) {
				$codice_gara = $rec["codice_gara"];
			}
			$numero_form = $rec["numero_form"];
			$_SESSION["guue"]["post_form"] = json_decode($rec["post_form"], TRUE);
			// echo "<pre>"; var_dump($_SESSION["guue"]["post_form"]); echo "</pre>";
			$_SESSION["guue"]["v_form"] = $rec["v_form"];
			$_SESSION["guue"]["titolo_pubblicazione"] = $rec["titolo_pubblicazione"];
			$_SESSION["guue"]["codice_pubblicazione"] = $rec["codice"];
			if($rec["stato"] == "TRASMESSO" || $rec["stato"] == "RIFIUTATO") {
				$_SESSION["guue"]["id_pubblicazione"] = $rec["id_pubblicazione"];
			}
		} else {
			if(!empty($_GET["codice_gara"])) {
				echo '<meta http-equiv="refresh" content="0;URL=/gare/guue/index.php?codice='.$_GET["codice_gara"].'">';
				die();
			} else {
				echo '<meta http-equiv="refresh" content="0;URL=/guue/">';
				die();
			}
		}
	} else {
		$codice_gara;
		if(!empty($_GET["codice_gara"])) $codice_gara = $_GET["codice_gara"];
	}

	if(!empty($codice_gara)) {

		$_SESSION["guue"]["codice_gara"] = $codice_gara;

		$permessi_gara = check_permessi_gara(1,$codice_gara,$_SESSION["codice_utente"]);
		if(!$permessi_gara["permesso"]) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}

		$bind = array();
		$bind[":codice"] = $codice_gara;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql_gara = "SELECT * FROM b_gare WHERE codice = :codice AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
		if ($_SESSION["gerarchia"] > 0) {
			$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
			$strsql_gara .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
		}
		$risultato_gara = $pdo->bindAndExec($strsql_gara,$bind);
		if($risultato_gara->rowCount() < 1) {
			if(!empty($_GET["codice_gara"])) {
				echo '<meta http-equiv="refresh" content="0;URL=/gare/guue/index.php?codice='.$_GET["codice_gara"].'">';
				die();
			} else {
				echo '<meta http-equiv="refresh" content="0;URL=/guue/">';
				die();
			}
		} else {
			$record_gara = $risultato_gara->fetch(PDO::FETCH_ASSOC);
			$_SESSION["guue"]["gara"] = $record_gara;
		}
	}
	?>
	<table style="width: 100%">
		<tbody>
			<tr>
				<td class="etichetta">Modello Pubblicazione:</td>
				<td>
					<select id="form-select" name="form-select" onchange="caricaform()">
						<option value="">Seleziona..</option>
						<?
						$bind = array();
						$sql_form = "SELECT * FROM b_gestione_guue WHERE attivo = 'S' ";
						if(!empty($record_gara)) {
							$sql_form .= "AND (fase_minima <= :stato AND (fase_massima >= :stato OR fase_massima = 0)) ";
							$bind[":stato"] = $record_gara["stato"];
						}
						$sql_form .= "ORDER BY ordinamento ASC";
						$ris_form = $pdo->bindAndExec($sql_form, $bind);
						if($ris_form->rowCount() > 0) {
							while ($rec_form = $ris_form->fetch(PDO::FETCH_ASSOC)) {
								?>
								<option <?= $numero_form == (int)str_replace(array('f', '-'),array('', '000'),$rec_form["form"]) ? 'selected="selected"' : null ?> value="<?= $rec_form["form"] ?>"><?= $rec_form["titolo"] ?></option>
								<?
							}
						}
						?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
	<div style="padding-top: 0px; padding-bottom: 20px;">
		<form id="form" method="POST" rel="validate" action="save.php">
			<?
			if(!empty($numero_form)) {
				?>
				<script type="text/javascript">
					$(document).ready(function() {
						$('#form-select').trigger('change');
					})
				</script>
				<?
			}
			?>
		</form>
	</div>
	<script type="text/javascript">
		function maincpvexist(codici) {
			var elenco = codici.split(';');
			if ($(document).find('#cpv_main').length > 0) {
				var main = $('#cpv_main').val();
				if($.inArray(main,elenco) == -1) {
					$('#cpv_main').val('');
				}
			}
		}

		function caricacpv(codici) {
			$('.link_to_cpv_table').css('display','inline');
			$('.cpv_selection_element').chosen("destroy");
			$('.cpv_selection_element').css('display', 'none');

			if(codici.length > 0) {
				$.ajax({
					url: 'get_cpv_select_option.php',
					type: 'post',
					dataType: "html",
					data: {codici: codici},
					success: function (options) {
						if(options.length > 0) {
							$('.link_to_cpv_table').css('display','none');
							$('.cpv_selection_element').html(options);
							$('.cpv_selection_element').chosen({
					    	width: '100%'
					    });
					    $('.cpv_selection_element').trigger("chosen:updated");
						}
					},
					beforeSend: function() {
						$("#wait").fadeIn('fast');
					},
					complete: function() {
						$("#wait").fadeOut('fast');
					}
				});
			}
		}

		function salvabozza() {
			$('#form').removeAttr('rel');
			$('#form').removeProp('rel');
			$('#stato_modello').val('1');
		}

		function caricaform()
		{
			var selected_form = $('#form-select').val();
			if(selected_form.length > 0)
			{
				$.ajax({
						url: 'getform.php',
						type: 'post',
						dataType: "html",
						data: {codice: selected_form},
						success: function (form) {
							$('#form').html(form);
							f_ready();
						},
						beforeSend: function() {
							$("#wait").fadeIn('fast');
						},
						complete: function() {
							$("#wait").fadeOut('fast');
						}
					});
			}
			else
			{
				$('#form').html('');
			}
		}

		var lot = 1;
		var lotaward = 1;
		function aggiungi_lotto() {
			lot++;
			$.ajax({
				url: 'getlot.php',
				type: 'post',
				dataType: "html",
				data: {indice: lot},
				success: function (form) {
					$('#more_lots').append(form);
					f_ready();
				},
				beforeSend: function() {
					$("#wait").fadeIn('fast');
				},
				complete: function() {
					$("#wait").fadeOut('fast');
				}
			});
		}

		function disable_field(target) {
			if($(target).length) {
				var rel = $(target).attr('rel');
				if(typeof rel !== typeof undefined && rel !== false) {
					rel = rel.replace('S;', 'N;');
					$(target).prop('rel', rel);
					$(target).attr('rel', rel);
				}
				$(target).prop('disabled', 'disabled');
				$(target).attr('disabled', 'disabled');
				$(target).val('');
				$(target).trigger('change');
				if($(target).is('select')) {
					$(target).trigger("chosen:updated");
				} else if ($(target).is('textarea')) {
					$(target).ckeditorGet().setReadOnly();
				}
				valida($(target));
			}
		}

		function enable_field(target) {
			if($(target).length) {
				var rel = $(target).attr('rel');
				if(typeof rel !== typeof undefined && rel !== false) {
					var controlli = rel.split(";");
					if(typeof controlli[0] !== typeof undefined && controlli[0] == "S") rel = rel.replace('N;', 'S;');
					$(target).prop('rel', rel);
					$(target).attr('rel', rel);
				}
				$(target).removeProp('disabled');
				$(target).removeAttr('disabled');
				if($(target).is('select')) {
					$(target).trigger("chosen:updated");
				} else if ($(target).is('textarea')) {
					$(target).ckeditorGet().setReadOnly(false);
				}
			}
		}

		function toggle_field(el, target) {
			if(el.is(':checked'))
			{
				$('input[name="'+el.attr('name')+'"]:not(:checked)').trigger('change');
				if(target.constructor === Array) {
					$.each(target, function(index, value) {
						enable_field(value);
					});
				} else {
					enable_field(target);
				}
			}
			else
			{
				if(target.constructor === Array) {
					$.each(target, function(index, value) {
						disable_field(value);
					});
				} else {
					disable_field(target);
				}
			}
		}

		function set_main_cpv(cpv) {
			$('#cpv_main').val(cpv);
		}

		function add_extra_info(value,param) {
			var ajax = false;
			var should_ramain_active;
			for(var key in param) {
				if(param.hasOwnProperty(key)) {
					var operation = param[key][0];
					var file = param[key][1];
					var keys = param[key][2];
					var target = $('#'+param[key][3]);
					var data = {};
					if(param[key].hasOwnProperty(4)) {data = param[key][4]}

					$("#wait").fadeIn('fast');
					if(operation == 'ajax_load') {
						target.html('');
					} else if (operation == 'enable_field' && should_ramain_active != param[key][3]) {
						if(param[key][3].constructor === Array) {
							$.each(param[key][3], function(index, value) {
								if(should_ramain_active != value) {
									disable_field($('#'+value));
								}
							});
						} else {
							disable_field(target);
						}
					}

					if(key == value) {
						if(operation == 'ajax_load') {
							ajax = true
							var url_ajax_load;
							if($.isArray(file)) {
								url_ajax_load = 'forms/<?= $_SESSION["guue"]["v_form"] ?>/'+file[0]+'/'+file[1]+'.php';
							} else {
								url_ajax_load = 'forms/<?= $_SESSION["guue"]["v_form"] ?>/common/'+file+'.php';
							}
							$.ajax({
								url: url_ajax_load,
								type: 'post',
								dataType: 'html',
								data: {chiavi: keys, data: data},
								success: function (html_element) {
									target.html(html_element);
									f_ready();
								},
								beforeSend: function() {
									if($("#wait").is(':visible') === false) {
										$("#wait").fadeIn('fast');
									}
								},
								complete: function() {
									$("#wait").fadeOut('fast');
								}
							});
						} else if (operation == 'enable_field') {
							if(param[key][3].constructor === Array) {
								$.each(param[key][3], function(index, value) {
									enable_field($('#'+value));
								});
							}
							enable_field(target);
							should_ramain_active = param[key][3];
						}
					}
				}
			}
			if(!ajax) {
				$("#wait").fadeOut('fast');
			}
			should_ramain_active = "";
		}
	</script>
	<?
	include_once($root."/layout/bottom.php");
?>
