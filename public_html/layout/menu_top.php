<style type="text/css">
	.comandi {
		top: 90px;
	}
	#contenuto_top {
		padding-top: 100px;
	}
	#menu-top {
		/*padding: 30px;*/
		display: block;
		background-color: #999999;
		height: auto;
		position: fixed;
		left: 20%;
		right: 0;
		top: 0;
		z-index: 100;
		height: 85px;
		<?
		 if ($_SESSION["developEnviroment"]==true) {
		 	echo "border-bottom: 3px dashed #f7ba00;";
		 }
		?>
	}
	#menu-top .left,
	#menu-top .right {
		display: block;
		list-style: none;
		padding: 20px 5px;
		margin: 0px;
		float: left;
	}
	#menu-top .right {
		padding-top: 10px;
	}
	#menu-top .right {
		float: right;
	}
	#menu-top .right li,
	#menu-top .left li {
		float: left;
		width: auto;
		padding: 0px 5px;
		text-align: center;
	}
	#menu-top .right li.systime,
	#menu-top .right li.sysday {
		text-align: right;
		font-size: 14px;
		color: #FFF;
	}
	#menu-top .right li.systime {
		font-size: 36px;
		margin-top: -5px;
	}
	#menu-top .left li a {
		color: #FFF;
		cursor: pointer;
		text-decoration: none;
		font-size: 10px;
	}
	#menu-top .left li a .fa {
		font-size: 26px;
	}
	#menu-top .left li a:hover {
		color: #C30;
	}

	.spacecolon {
		width: 5px;
		height: 100%;
	}
	@-webkit-keyframes syscolonanimation {
		0% {opacity:1.0; text-shadow:0 0 20px #00c6ff;}
		50% {opacity:0; text-shadow:none; }
		100% {opacity:1.0; text-shadow:0 0 20px #00c6ff; }
	}

	@-moz-keyframes syscolonanimation {
		0% {opacity:1.0; text-shadow:0 0 20px #00c6ff;}
		50% {opacity:0; text-shadow:none; }
		100% {opacity:1.0; text-shadow:0 0 20px #00c6ff; }
	}

	@-o-keyframes syscolonanimation {
		0% {opacity:1.0; text-shadow:0 0 20px #00c6ff;}
		50% {opacity:0; text-shadow:none; }
		100% {opacity:1.0; text-shadow:0 0 20px #00c6ff; }
	}

	@keyframes syscolonanimation {
		0% {opacity:1.0; text-shadow:0 0 20px #00c6ff;}
		50% {opacity:0; text-shadow:none; }
		100% {opacity:1.0; text-shadow:0 0 20px #00c6ff; }
	}
	.syscolon {
		-webkit-animation: syscolonanimation 1s ease infinite;
		-moz-animation:    syscolonanimation 1s ease infinite;
		-o-animation:      syscolonanimation 1s ease infinite;
		animation:         syscolonanimation 1s ease infinite;
	}
	.sysh,
	.syscolon,
	.sysm {
		float: left;
	}
	.systime {
		width: auto !important;
	}

</style>
<div id="menu-top">
	<ul class="left">
		<? if (isset($_SESSION["codice_utente"]) && !is_operatore()) { ?>
	    <li><a href="#" onclick="show_bug_report();"><i class="fa fa-exclamation-circle"></i><br>Segnalazioni</a></li>
		<? } /* ?>
	    <li>
	    	<?
	    	$sql = "SELECT * FROM b_guida WHERE href = :href AND (gerarchia = -1 ";
				if(isset($_SESSION["gerarchia"]) && is_numeric($_SESSION["gerarchia"])) $sql .= "OR gerarchia >= " . $_SESSION['gerarchia'];
				$sql .= ")";
				$ris = $pdo->bindAndExec($sql, array(':href' => $_SERVER["PHP_SELF"]));
				if ($ris->rowCount() > 0) {
					?>
					<a href="#" onclick="show_help();"><i class="fa fa-question-circle"></i><br>Guida Online</a>
					<div id="detail_help" style="display:none" title="Guida">
						<?
						while ($record = $ris->fetch(PDO::FETCH_ASSOC)) {
							if (isset($_SESSION["codice_utente"]) && check_permessi("guida",$_SESSION["codice_utente"])) {
								?>
				        <div style="text-align:right;">
									<button class='btn-round btn-warning' title="Modifica" onClick="window.location.href='<?= makeurl('guida',$record["codice"],"edit") ?>'"><span class="fa fa-pencil"></span></button>
				           <button class='btn-round btn-danger' title="elimina" onClick="elimina('<?= $record["codice"]; ?>','guida'); return false;" title="Elimina"><span class="fa fa-remove"></span></button>
				        </div>
								<?
							}
							?>
							<h1><?= $record["titolo"] ?></h1>
							<?= $record["descrizione"] ?>
							<div class="clearfix"></div>
							<?
						}
						?>
					</div>
					<script type="text/javascript">
						function show_help() {
							$('#detail_help').dialog({
								modal:true,
								width: '80%'
							});
						}
						function edit_help() {
							$('#detail_help').dialog("close");
						}
					</script>
					<?
				} else {
					?>
	    		<a href="/guida/"><i class="fa fa-question-circle"></i><br><?= traduci("Guida Online") ?></a>
					<?
				}
			?>
	    </li>
			<? 
			if (isset($_SESSION["amministratore"]) && $_SESSION["amministratore"] == true && $ris->rowCount() == 0)
			{
				?>
				<li>
					<a href="#" title="Aggiungi contenuti alla guida" onclick="addHelp();">
						<i class="fa fa-plus-square"></i><br>Aggiungi Help
					</a>
				</li>
				<?
				$record_guida = get_campi("b_guida");
				$record_guida["href"] = $_SERVER["PHP_SELF"];
				$operazione = "INSERT";
				?>

					<script type="text/javascript">
						function addHelp() {
							$('#addHelp').dialog({
								modal:true,
								width: '80%',
								open: function( event, ui ) {
									$("#descrizione_guida").ckeditor();
								}
							});
						}
					</script>
					<div id="addHelp" style="display:none" title="Gestione Guida Online">
						<?
						if ($_SERVER["PHP_SELF"] != "/guida/edit.php")
						{
							include_once($root.'/guida/form.php');
						}
						?>
					</div>
				<?
			} */ ?>
	    <li>
	    	<a href="#" onclick="show_search();"><i class="fa fa-search"></i><br><?= traduci("cerca") ?></a>
	    </li>
			<li>
	    	<a href="/scadenzario/"><i class="fa fa-calendar-o"></i><br><?= traduci("scadenzario") ?></a>
	    </li>
	</ul>
	<?
		$label_lg = "English version";
		$input_lg = "EN";
		if (isset($_SESSION["language"]) && $_SESSION["language"] == "EN") {
			$label_lg = "Versione italiana";
			$input_lg = "IT";
		}
	?>

	<div style="text-align:right; padding-top:3px; padding-right:25px">
		<? if (!empty($_SESSION["loginHash"])) { ?>
			<strong style="color:#fff">ID SESSIONE: <?= strtoupper(substr($_SESSION["loginHash"],0,4)) ?></strong> | 
		<? } ?>
		<a style="color:#fff" href="/index.php?ipo=true" title="<?= traduci("Ipovedenti") ?>"><?= ($_SESSION["ipo"]) ? traduci("Standard") : traduci("Ipovedenti") ?></a> |
		<a style="color:#fff" href="/index.php?bigText=true" title="<?= traduci("Testo") ?> <? traduci("Grande") ?>"><?= traduci("Testo") ?> <?= ($_SESSION["bigText"]) ? traduci("normale") : traduci("grande") ?></a> |
		<a style="color:#fff" href="/index.php?language=<?= $input_lg ?>" title="<?= $label_lg ?>"><?= $label_lg ?></a>
	</div>
	<ul class="right">
		<?php
			setlocale(LC_TIME, 'ita', 'it_IT');
		?>
    <li class="sysday"><?
		$datestring = explode(" ",mb_convert_encoding(strftime("%A %d %B %Y"), 'UTF-8'));
		$datestring[0] = traduci($datestring[0])."<br>";
		$datestring[2] = traduci($datestring[2]);
		$datestring = implode(" ",$datestring);
		echo ucwords($datestring);
		?></li>
    <li class="systime">
    	<div class="sysh"><?= date('H') ?></div><div style="float:left;width: 14px !important; height: 50px; text-align:center;"><span class="syscolon">:</span></div><div class="sysm"><?= date('i') ?></div>
    </li>
    <li></li>
    <li></li>
	</ul>
	<div class="clear"></div>
</div>

<script type="text/javascript">
	var serverTime = new Date(<?= date("'Y','m','d','H','i','s'") ?>);
	var currentTime;

	var getTime = function () {
		$.ajax({
			type: 'GET',
			url: '/serverClock.php',
			data: {format: 'json'},
			dataType: 'json',
			async: "true",
		})
		.done(function(data) {
			serverTime = new Date(data.year,data.month,data.day,data.hour,data.minute,data.second);
			$('.sysday').html(data.datestring);
			$('.sysh').html(getHoursWithLeadingZeros(serverTime));
			$('.sysm').html(getMinutesWithLeadingZeros(serverTime));
		});
	}

	var updateClock = function() {
		currentTime = new Date(serverTime.getTime() + 250);
		$('.sysh').html(getHoursWithLeadingZeros(currentTime));
		$('.sysm').html(getMinutesWithLeadingZeros(currentTime));
		serverTime = currentTime;
	}

	var getMinutesWithLeadingZeros = function (date) {
		return (date.getMinutes() < 10 ? '0' : '') + date.getMinutes();
	}

	var getHoursWithLeadingZeros = function (date) {
		return (date.getHours() < 10 ? '0' : '') + date.getHours();
	}

	setInterval(updateClock, 250);
	// setInterval(getTime, 50250);
	setInterval(getTime, 180000);


</script>
