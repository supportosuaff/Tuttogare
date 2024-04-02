
<script>
	var modifica = false;
	$("* :input").change(function() {
		modifica = true;
	});

	function return_pannello() {
		window.location.href = "/contratti/pannello.php?codice=<? echo $_GET["codice"] ?><?= (!empty($_GET["codice_gara"])) ? "&codice_gara={$_GET["codice_gara"]}" : "" ?>";
	}

	function ritorna() {
		if (modifica) {
			jconfirm("<strong>Attenzione!</strong> Non hai salvato le modifiche.<br>Vuoi tornare al pannello?",return_pannello);
		} else {
			return_pannello()
		}
	}
</script>
<input type="button" class="submit_big" style="background-color:#999;" value="Ritorna al pannello di Contratto" onClick="ritorna()">
