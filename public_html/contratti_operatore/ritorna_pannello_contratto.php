
<script>
	var modifica = false;
	$("* :input").change(function() {
		modifica = true;
	});

	function return_pannello() {
		window.location.href = "/contratti_operatore/pannello.php?codice=<? echo $_GET["codice"] ?>";
	}

	function ritorna() {
		if (modifica) {
			jconfirm("<strong>Attenzione!</strong> Non hai salvato le modifiche.<br>Vuoi tornare al pannello?",return_pannello);
		} else {
			return_pannello()
		}
	}
</script>
<input type="button" class="submit_big" style="background-color:#999; cursor:pointer !important;" value="Ritorna al pannello di Contratto" onClick="ritorna()">
