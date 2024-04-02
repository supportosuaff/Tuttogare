<?
	include_once("../../config.php");
  include_once($root."/layout/top.php");
  ?>
  <h1><?= traduci("registrazione-oe") ?></h1>
  <div class="padding" style="border: 3px dotted #21FF06; background-color: rgba(33,255,6, 0.1)">
    <h2 style="text-align:center">
			<?
				$path = $config["path_vocabolario"]."/{$_SESSION["language"]}/msg-conferma-pre.html";
				if (file_exists($path)) include($path);
			?>
    </h2>
  </div>
  <?
  include_once($root."/layout/bottom.php");
?>
