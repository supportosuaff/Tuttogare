<?
 if (isset($_POST["vatNumber"])) {
		$client = new SoapClient(
		'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl',
	    array(
        'location' => 'http://ec.europa.eu/taxation_customs/vies/services/checkVatService',
       	'trace' => 1,
        'use' => SOAP_LITERAL,
    )
);
$result = @$client->checkVat(array('countryCode' => $_POST["countryCode"],'vatNumber' => $_POST["vatNumber"]));
if (!$result->valid) {
	echo "Error";
}
}
?>