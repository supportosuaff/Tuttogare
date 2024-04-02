<style type="text/css">
	label {
		font-size: 14px;
	}
	h3, h2 {
		padding: 5px;
		margin-top: 15px;
	}
	table.bordered {
		border-collapse: collapse;
		width: 100%;
	}
	table.bordered, table.bordered th, table.bordered td {
		border: 1px solid #000;
	}
	table.bordered td {
		padding: 5px;
	}
	table.bordered tr.odd, table.bordered  tr.even {
		background-color: transparent;
	}
	tr.noBorder td {
		border: 0;
		padding: 2px;
	}
	tr.bottomBorder td {
		border-bottom: 1px solid black;
	}
	input[type="text"] {
		width: 100%;
		box-sizing : border-box;
		font-family: Tahoma, Geneva, sans-serif;
		font-size: 1em
	}
	input[type="text"]:disabled {
    background: #dddddd;
	}
	.additional_content {
		display: none;
	}
	.submit_bozza {
			background-color: #09F;
			color: #FFF;
			border: 1px solid #aaa;
			font-weight: bold;
			display: inline;
			text-align: center;
			text-decoration: none;
			position: absolute;
			height: 20px;
			top: 5;
			right: 35px;
			cursor: pointer;
			background-color:#0C0;
			border:none;
			color:#fff;
			border-radius: 5px
		}
		.cpv_selection_element {
			display: none;
		}
</style>
<?
	if(!empty($_POST["codice"])) {
		include_once("../../config.php");
		if($_SESSION["developEnviroment"]) {
			if (strpos($_POST["codice"], "..") == false && file_exists($root."/guue/forms/2_0_9_S3/{$_POST["codice"]}.php")) include_once "forms/2_0_9_S3/{$_POST["codice"]}.php";
		} else {
			if (strpos($_POST["codice"], "..") == false && file_exists($root."/guue/forms/2_0_9_S3/{$_POST["codice"]}.php")) include_once "forms/2_0_9_S3/{$_POST["codice"]}.php";
		}

	}
?>
