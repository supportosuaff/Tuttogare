<?
	session_start();
	include '../../config.php';
	include_once $root . '/inc/funzioni.php';

	if(!empty($_POST["param"]["item"])) {
		$guue["supplementary_cpv"] = $_POST["param"]["supplementary_cpv"];

		switch ($_SESSION["guue"]["numero_del_form"]) {
			case 'f01-1':
			case 'f04-1':
			case 'f21-1':
			case 'f04-1':
				$f = array();
				$f[0] = '<table id="lot_no_'.$_POST["param"]["item"].'" class="bordered"><tbody>';
				$f[1] = "sezioni/sezione_2_2_1.php";
				$f[2] = "sezioni/sezione_2_2_2.php";
				$f[3] = "sezioni/sezione_2_2_3.php";
				$f[4] = "sezioni/sezione_2_2_4.php";
				$f[13] = "sezioni/sezione_2_2_14.php";
				$f[14] = '</tbody></table><div id="padding_lot_no_'.$_POST["param"]["item"].'" class="padding"></div>';
				break;
			case 'f21-4':
			case 'f22-6':
				$f = array();
				$f[0] = '<table id="lot_no_'.$_POST["param"]["item"].'" class="bordered"><tbody>';
				$f[1] = "sezioni/sezione_2_2_1.php";
				$f[2] = "sezioni/sezione_2_2_2.php";
				$f[3] = "sezioni/sezione_2_2_3.php";
				$f[4] = "sezioni/sezione_2_2_4.php";
				$f[12] = "sezioni/sezione_2_2_13.php";
				$f[13] = "sezioni/sezione_2_2_14.php";
				$f[14] = '</tbody></table><div id="padding_lot_no_'.$_POST["param"]["item"].'" class="padding"></div>';
				break;
			case 'f21-2':
			case 'f22-2':
			case 'f21-3':
			case 'f22-5':
				$f = array();
				$f[0] = '<table id="lot_no_'.$_POST["param"]["item"].'" class="bordered"><tbody>';
				$f[1] = "sezioni/sezione_2_2_1.php";
				$f[2] = "sezioni/sezione_2_2_2.php";
				$f[3] = "sezioni/sezione_2_2_3.php";
				$f[4] = "sezioni/sezione_2_2_4.php";
				$f[6] = "sezioni/sezione_2_2_6.php";
				$f[7] = "sezioni/sezione_2_2_7_f203.php";
				$f[12] = "sezioni/sezione_2_2_13.php";
				$f[13] = "sezioni/sezione_2_2_14.php";
				$f[14] = '</tbody></table><div id="padding_lot_no_'.$_POST["param"]["item"].'" class="padding"></div>';
				break;
			case 'f23-1':
				$f = array();
				$f[0] = '<table id="lot_no_'.$_POST["param"]["item"].'" class="bordered"><tbody>';
				$f[1] = "sezioni/sezione_2_2_1.php";
				$f[2] = "sezioni/sezione_2_2_2.php";
				$f[3] = "sezioni/sezione_2_2_3.php";
				$f[4] = "sezioni/sezione_2_2_4.php";
				$f[6] = "sezioni/sezione_2_2_6.php";
				$f[7] = "sezioni/sezione_2_2_7_f203.php";
				$f[12] = "sezioni/sezione_2_2_13.php";
				$f[13] = "sezioni/sezione_2_2_14.php";
				$f[14] = '</tbody></table><div id="padding_lot_no_'.$_POST["param"]["item"].'" class="padding"></div>';
				break;
			case 'f22-3':
			case 'f22-4':
				$f = array();
				$f[0] = '<table id="lot_no_'.$_POST["param"]["item"].'" class="bordered"><tbody>';
				$f[1] = "sezioni/sezione_2_2_1.php";
				$f[2] = "sezioni/sezione_2_2_2.php";
				$f[3] = "sezioni/sezione_2_2_3.php";
				$f[4] = "sezioni/sezione_2_2_4.php";
				$f[6] = "sezioni/sezione_2_2_8.php";
				$f[12] = "sezioni/sezione_2_2_13.php";
				$f[13] = "sezioni/sezione_2_2_14.php";
				$f[14] = '</tbody></table><div id="padding_lot_no_'.$_POST["param"]["item"].'" class="padding"></div>';
				break;
			case 'f24':
				$f = array();
				$f[0] = '<table id="lot_no_'.$_POST["param"]["item"].'" class="bordered"><tbody>';
				$f[1] = "sezioni/sezione_2_2_1.php";
				$f[2] = "sezioni/sezione_2_2_2.php";
				$f[3] = "sezioni/sezione_2_2_3.php";
				$f[4] = "sezioni/sezione_2_2_4.php";
				$f[5] = "sezioni/sezione_2_2_5_f25.php";
				$f[6] = "sezioni/sezione_2_2_6.php";
				$f[7] = "sezioni/sezione_2_2_7_f203.php";
				$f[12] = "sezioni/sezione_2_2_13.php";
				$f[13] = "sezioni/sezione_2_2_14.php";
				$f[14] = '</tbody></table><div id="padding_lot_no_'.$_POST["param"]["item"].'" class="padding"></div>';
				break;
			case 'f25':
				$f = array();
				$f[0] = '<table id="lot_no_'.$_POST["param"]["item"].'" class="bordered"><tbody>';
				$f[1] = "sezioni/sezione_2_2_1.php";
				$f[2] = "sezioni/sezione_2_2_2.php";
				$f[3] = "sezioni/sezione_2_2_3.php";
				$f[4] = "sezioni/sezione_2_2_4.php";
				$f[5] = "sezioni/sezione_2_2_5_f25.php";
				$f[7] = "sezioni/sezione_2_2_7_f203.php";
				$f[12] = "sezioni/sezione_2_2_13.php";
				$f[13] = "sezioni/sezione_2_2_14.php";
				$f[14] = '</tbody></table><div id="padding_lot_no_'.$_POST["param"]["item"].'" class="padding"></div>';
				break;
			case 'f07-1':
				$f = array();
				$f[0] = '<table id="lot_no_'.$_POST["param"]["item"].'" class="bordered"><tbody>';
				$f[1] = "sezioni/sezione_2_2_2.php";
				$f[2] = "sezioni/sezione_2_2_3.php";
				$f[3] = "sezioni/sezione_2_2_4.php";
				$f[4] = "sezioni/sezione_2_2_5.php";
				$f[5] = "sezioni/sezione_2_2_8.php";
				$f[6] = "sezioni/sezione_2_2_13.php";
				$f[13] = '</tbody></table><div id="padding_lot_no_'.$_POST["param"]["item"].'" class="padding"></div>';
				break;
			case 'f01-2':
			case 'f01-3':
			case 'f04-2':
			case 'f04-3':
				$f = array();
				$f[0] = '<table id="lot_no_'.$_POST["param"]["item"].'" class="bordered"><tbody>';
				$f[1] = "sezioni/sezione_2_2_1.php";
				$f[2] = "sezioni/sezione_2_2_2.php";
				$f[3] = "sezioni/sezione_2_2_3.php";
				$f[4] = "sezioni/sezione_2_2_4.php";
				$f[5] = "sezioni/sezione_2_2_5.php";
				$f[6] = "sezioni/sezione_2_2_6.php";
				$f[7] = "sezioni/sezione_2_2_7.php";
				$f[9] = "sezioni/sezione_2_2_10_f01.php";
				$f[10] = "sezioni/sezione_2_2_11_f01.php";
				$f[11] = "sezioni/sezione_2_2_13.php";
				$f[12] = "sezioni/sezione_2_2_14.php";
				$f[13] = '</tbody></table><div id="padding_lot_no_'.$_POST["param"]["item"].'" class="padding"></div>';
				break;
			case 'f02':
				$f = array();
				$f[0] = '<table id="lot_no_'.$_POST["param"]["item"].'" class="bordered"><tbody>';
				$f[1] = "sezioni/sezione_2_2_1.php";
				$f[2] = "sezioni/sezione_2_2_2.php";
				$f[3] = "sezioni/sezione_2_2_3.php";
				$f[4] = "sezioni/sezione_2_2_4.php";
				$f[5] = "sezioni/sezione_2_2_5.php";
				$f[6] = "sezioni/sezione_2_2_6.php";
				$f[7] = "sezioni/sezione_2_2_7_f02.php";
				$f[8] = "sezioni/sezione_2_2_9.php";
				$f[9] = "sezioni/sezione_2_2_10_f02.php";
				$f[10] = "sezioni/sezione_2_2_11_f02.php";
				$f[11] = "sezioni/sezione_2_2_12.php";
				$f[12] = "sezioni/sezione_2_2_13.php";
				$f[13] = "sezioni/sezione_2_2_14.php";
				$f[14] = '</tbody></table><div id="padding_lot_no_'.$_POST["param"]["item"].'" class="padding"></div>';
				break;
			case 'f03':
			case 'f06':
				$f = array();
				$f[0] = '<table id="lot_no_'.$_POST["param"]["item"].'" class="bordered"><tbody>';
				$f[1] = "sezioni/sezione_2_2_1.php";
				$f[2] = "sezioni/sezione_2_2_2.php";
				$f[3] = "sezioni/sezione_2_2_3.php";
				$f[4] = "sezioni/sezione_2_2_4.php";
				$f[5] = "sezioni/sezione_2_2_5_f06.php";
				$f[10] = "sezioni/sezione_2_2_11_f02.php";
				$f[12] = "sezioni/sezione_2_2_13.php";
				$f[13] = "sezioni/sezione_2_2_14.php";
				$f[14] = '</tbody></table><div id="padding_lot_no_'.$_POST["param"]["item"].'" class="padding"></div>';
				break;
			default:
				$f = array();
				$f[0] = '<table id="lot_no_'.$_POST["param"]["item"].'" class="bordered"><tbody>';
				$f[1] = "sezioni/sezione_2_2_1.php";
				$f[2] = "sezioni/sezione_2_2_2.php";
				$f[3] = "sezioni/sezione_2_2_3.php";
				$f[4] = "sezioni/sezione_2_2_4.php";
				$f[5] = "sezioni/sezione_2_2_5.php";
				$f[6] = "sezioni/sezione_2_2_6.php";
				$f[7] = "sezioni/sezione_2_2_7_f02.php";
				$f[8] = "sezioni/sezione_2_2_9.php";
				$f[9] = "sezioni/sezione_2_2_10_f02.php";
				$f[10] = "sezioni/sezione_2_2_11_f02.php";
				$f[11] = "sezioni/sezione_2_2_12.php";
				$f[12] = "sezioni/sezione_2_2_13.php";
				$f[13] = "sezioni/sezione_2_2_14.php";
				$f[14] = '</tbody></table><div id="padding_lot_no_'.$_POST["param"]["item"].'" class="padding"></div>';
				break;
			}
		loadForm($f, $_POST["param"]["item"]);
	}
?>
