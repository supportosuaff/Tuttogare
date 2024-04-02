<?
	include("../config.php");
	if (!$manutenzione) {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	$pagina_login = true;
	$pagina_manutenzione = true;
	include($root."/layout/top.php");
?>
	<style>
		body {
			background-color:#666;
		}
		h1,h2 {
			text-align: center;
		}
	</style>
    <div id="div_login">
			<div style="padding:50px">
				<div style="text-align:center">
					<? if (isset($_SESSION["ente"])) { ?>
						<img src="/documenti/enti/<? echo $ente["logo"] ?>" width="30%" alt="<? echo $ente["denominazione"] ?>"><br>
						<strong><? echo $ente["denominazione"]; ?></strong><br><br>
					<?
						} else {
					?><img alt="TUTTOGARE PA" src="/img/logo-tuttogare-pa-big.pngg">
					<? } ?>
            <div class="clear"></div>
            </div>
            <h1>Piattaforma in manutenzione</h1>
						<h2>
							<? if (time() > strtotime('2021-04-26 00:00:00')) { ?>
								Il servizio sar&agrave; ripristinato nel pi&ugrave; breve tempo possibile, ci scusiamo per il disagio.
							<? } else { ?>
								Il sistema torner&agrave; disponibile<br>dalle ore 0.00 del 26 aprile 2021
							<? } ?>
						</h2>
            <? if (isset($_SESSION["ente"])) {
						?><br><img alt="TUTTOGARE PA" width="150" src="/img/logo-tuttogare-pa-big.png"><?
					}
					?>
           </div>
		</div>
		<?
		include($root."/layout/bottom.php");
?>
