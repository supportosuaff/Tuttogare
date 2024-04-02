<?
	session_start();
	if (isset($_POST)) {
		include_once("../../config.php");
		include_once($root."/inc/funzioni.php");
		foreach($_POST AS $key => $value) $_POST[$key] = purify($value);
		if (!isset($_SESSION["ente"])) $_POST["online"] = "S";
		?>
		<div id="<? echo $_POST["uniqueIdentifier"] ?>">
			<div class="progress_bar"></div>
			<table width="100%">
				<tr>
					<td>
						<input type="hidden" name="allegato[<? echo $_POST["uniqueIdentifier"] ?>][filechunk]" value="<? echo $_POST["filename"] ?>">
						<img src="/img/<? echo substr($_POST["filename"],-3)?>.png" alt="File <? echo substr($_POST["filename"],-3)?>" style="vertical-align:middle"><? echo $_POST["filename"] ?>
					</td>
					<? if ($_POST["online"]=="") { ?><td>Pubblico</td><? } ?>
    			<td rowspan="2" width="10" align="center">
						<input type="image" src="/img/del.png" title="Elimina allegato" onClick="$('#<? echo $_POST["uniqueIdentifier"] ?>').remove(); uploader.resumable.getFromUniqueIdentifier('<? echo $_POST["uniqueIdentifier"] ?>').cancel();return false;">
					</td>
				</tr>
    		<tr>
        	<td width="95%">
						<input type="text" name="allegato[<? echo $_POST["uniqueIdentifier"] ?>][titolo]" value="<? echo substr($_POST["filename"],0,-4)?>" title="Titolo" rel= "S;3;0;A" class="titolo_edit"></td>
        		<? if ($_POST["online"]=="") { ?>
							<td>
								<select name="allegato[<? echo $_POST["uniqueIdentifier"] ?>][online]">
									<option value="S">Si</option>
									<option value="N">No</option>
								</select>
							</td>
						<? } else { ?>
							<input type="hidden" name="allegato[<? echo $_POST["uniqueIdentifier"] ?>][online]" value="<? echo $_POST["online"] ?>">
						<? } ?>
				</tr>
    	</table>
		</div>
		<?
	}
?>
