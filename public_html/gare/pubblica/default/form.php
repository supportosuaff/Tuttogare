<? if(isset($record)) { ?>
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
		<? include_once($root."/gare/pubblica/directForm.php"); ?>
	</div>
	<? }
} ?>
