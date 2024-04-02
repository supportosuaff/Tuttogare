<?
if(empty($chiavi)) $chiavi = !empty($_POST["chiavi"]) ? $_POST["chiavi"] : null;

if(!empty($chiavi)) {
	switch ($chiavi) {
		case 'URL_PARTICIPATION_AND_ADDRESS_PARTICIPATION_IDEM_ITEM_TO_IGNORE':
		case 'URL_PARTICIPATION':
			?>
			<table class="bordered">
				<tbody>
					<tr>
						<td class="etichetta">
							Indicare URL per la trasmissione elettronica:
						</td>
						<td>
							<input type="text" style="font-size: 1.3em" class="espandi" name="guue[CONTRACTING_BODY][URL_PARTICIPATION]" <?= !empty($guue["CONTRACTING_BODY"]["URL_PARTICIPATION"]) ? 'value="'.$guue["CONTRACTING_BODY"]["URL_PARTICIPATION"].'"' : null ?> title="URL" rel="S;0;200;L">
						</td>
					</tr>
				</tbody>
			</table>
			<? 
			if($chiavi == 'URL_PARTICIPATION_AND_ADDRESS_PARTICIPATION_IDEM_ITEM_TO_IGNORE')
			{
				?>
				<input type="hidden" name="" id="guue[CONTRACTING_BODY][ADDRESS_PARTICIPATION_IDEM]" value="">
				<?
			}
			break;
		case 'URL_PARTICIPATION_AND_ADDRESS_PARTICIPATION_ITEM_TO_IGNORE':
		case 'ADDRESS_PARTICIPATION':
			if($chiavi == 'URL_PARTICIPATION_AND_ADDRESS_PARTICIPATION_ITEM_TO_IGNORE') 
			{
			?>
			<table class="bordered">
				<tbody>
					<tr>
						<td class="etichetta">
							Indicare URL per la trasmissione elettronica:
						</td>
						<td>
							<input type="text" name="guue[CONTRACTING_BODY][URL_PARTICIPATION]" <?= !empty($guue["CONTRACTING_BODY"]["URL_PARTICIPATION"]) ? 'value="'.$guue["CONTRACTING_BODY"]["URL_PARTICIPATION"].'"' : null ?> title="URL" rel="S;0;200;L">
						</td>
					</tr>
				</tbody>
			</table>
			<? 
			}
			if(empty($root)) include_once '../../../../../config.php';
			include_once $root.'/inc/funzioni.php';
			$keys = '[CONTRACTING_BODY][ADDRESS_PARTICIPATION]';
			unset($_POST["chiavi"]);
			include 'ADDR-S1.php';
			break;
		default:
			# code...
			break;
	}
}

