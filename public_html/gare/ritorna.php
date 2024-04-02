<? if (is_numeric($_GET["codice"])) { ?>
	<script>
		var modifica = false;
		$("* :input").change(function() {
			modifica = true;
		});

		function return_pannello() {
			window.location.href = "/gare/pannello.php?codice=<? echo $_GET["codice"] ?>";
		}
		function ritorna() {
		<? if (!isset($no_msg)) { ?>
			if (modifica) {
				jconfirm("<strong>Attenzione!</strong> Non hai salvato le modifiche.<br>Vuoi tornare al pannello?",return_pannello);
			} else { <? } ?>
				return_pannello()
		<? if (!isset($no_msg)) { ?> } <? } ?>
		}
	</script>
	<input type="button" class="espandi ritorna_button submit_big" style="background-color:#999;" value="Ritorna al pannello" onClick="ritorna()">
<? } ?>
