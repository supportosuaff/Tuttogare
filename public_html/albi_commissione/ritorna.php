<script>
	var modifica = false;
	$("* :input").change(function() {
		modifica = true;
	});

	function return_pannello() {
		window.location.href = "/albi_commissione/index.php";
	}

	function ritorna() {
		if (modifica) {
			jconfirm("<strong>Attenzione!</strong> Non hai salvato le modifiche.<br>Vuoi tornare all'elenco?",return_pannello);
		} else {
			return_pannello()
		}
	}
</script>
<input type="button" class="submit_big" style="background-color:#999;" value="Ritorna all'elenco" onClick="ritorna()">
