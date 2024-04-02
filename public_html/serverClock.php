<?php
	session_start();
	if (!isset($_SESSION["language"])) $_SESSION["language"] = "IT";
	if ($_SESSION["language"] != "IT") {
		setlocale(LC_TIME, 'ita', 'us_EN');
	} else {
		setlocale(LC_TIME, 'ita', 'it_IT');	
	}
	if(! empty($_GET["format"]) && $_GET["format"] == 'json') {
		$response = array(
			'year' => date('Y'),
			'month' => date('m'),
			'day' => date('d'),
			'hour' => date('H'),
			'minute' => date('i'),
			'second' => date('s'),
			'datestring' => mb_convert_encoding(strftime("%A %d %B %Y"), 'UTF-8'),
		);
		$response['datestring'] = explode(" ",$response['datestring']);
		$response['datestring'][0] = $response['datestring'][0]."<br>";
		$response['datestring'][2] = $response['datestring'][2];
		$response['datestring'] = ucwords(implode(" ",$response['datestring']));
		echo json_encode($response, JSON_FORCE_OBJECT);
		die();
	}
	$response = array('date' => ucwords(mb_convert_encoding(strftime("%A<br>%d %B %Y"), 'UTF-8')), 'time' => array('h' =>  date('H'), 'm' =>  date('i')));
	echo json_encode($response);
	session_write_close();
	die();
?>
