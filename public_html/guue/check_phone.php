<?
	if(!empty($_POST["valore"]))
	{
		$value = trim($_POST["valore"]);
		if(substr_count($value, " ") > 1) {
			echo "Attenzione, &egrave; consentito solo uno spazion tra il codice internazionale e il resto delle cifre";
			die();
		} else if (substr_count($value, " ") < 1) {
			echo "Il prefisso internazionale (es. +39) deve essere separato dal resto del numero da uno spazio (es. +39 123456789)";
			die();
		}
		if(strpos('+',$value) === FALSE) {
			$value = '+'.$value;
		}
		$regex = '/(\+\d{1,3}\s\d+(\-\d+)*((\s)?\/(\s)?(\+\d{1,3}\s)?\d+(\-\d+)*)*)/';
		if(preg_match($regex, $value) == 0)
		{
			echo "Il numero deve contenere il prefisso internazionale es. +39 separato dal resto del numero da uno spazio (es. +39 123456789)";
			die();
		}
	}
?>