<?
	include '../../config.php';
	include_once $root . '/inc/funzioni.php';
	if(!empty($_POST["param"]["item"])) {
		$guue["supplementary_cpv"] = $_POST["param"]["supplementary_cpv"];
		$f = array(
			// 0 => '<input type="hidden" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ATTRIBUTE][ITEM]" value="'.$_POST["item"].'">',
			0 => '<table id="lot_award_no_'.$_POST["param"]["item"].'" class="bordered"><tbody>',
			1 => "sezioni/sezione_5.php",
			2 => '</tbody></table><div id="padding_lot_award_no_'.$_POST["param"]["item"].'" class="padding"></div>',
			);
		if(!empty($_POST["param"]["form"]) && $_POST["param"]["form"] == "f06") $f[1] = "sezioni/sezione_5_f06.php";
		if(!empty($_POST["param"]["form"]) && $_POST["param"]["form"] == "f25") $f[1] = "sezioni/sezione_5_f25.php";
		if(!empty($_POST["param"]["form"]) && $_POST["param"]["form"] == "f21") $f[1] = "sezioni/sezione_5_f21.php";
		loadForm($f, $_POST["param"]["item"]);
	}
?>
