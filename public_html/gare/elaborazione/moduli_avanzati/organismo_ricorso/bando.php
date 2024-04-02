<?
$bind=array();
$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
$sql_organismo = "SELECT * FROM b_organismi_ricorso WHERE codice_ente = :codice_ente";
$ris_organismo = $pdo->bindAndExec($sql_organismo,$bind);
if ($ris_organismo->rowCount() > 0) {
		$record_organismo = $ris_organismo->fetch(PDO::FETCH_ASSOC);
		$html.= "<table width=\"100%\">";
		$html.= "<tbody>";
		$html.= "<tr>";
		$html.= "<td>Denominazione</td>";
		$html.= "<td><strong>" . $record_organismo["denominazione"] . "</strong></td>";
		$html.= "</tr>";
		$html.= "<tr>";
		$html.= "	<td>Indirizzo postale</td>";
		$html.= "	<td><strong>" . $record_organismo["indirizzo"] . "</strong></td>";
		$html.= "</tr>";
		$html.= "<tr>";
		$html.= "	<td>Citta</td>";
		$html.= "	<td><strong>" . $record_organismo["citta"] . " (" . $record_organismo["provincia"] . ") " . $record_organismo["cap"] . " " . $record_organismo["stato"] . "</strong></td>";
		$html.= "</tr>";
		$html.= "<tr>";
		$html.= "	<td><strong>Contatti</strong></td><td>";
		if ($record_organismo["telefono"] != "") $html.= "Tel. " . $record_organismo["telefono"] . "<br />";
		if ($record_organismo["fax"] != "") $html.= "	Fax. " . $record_organismo["fax"] . "<br />";
		$html.= "	Email: <a>" . $record_organismo["email"] . "</a>";
		$html.= "</td></tr>";
		$html.= "<tr>";
		$html.= "	<td><strong>PEC</strong></td>";
		$html.= "	<td><a>" . $record_organismo["pec"] . "</a></td>";
		$html.= "</tr>";
		if ($record_organismo["url"] != "") {
			$html.= "<tr>";
			$html.= "	<td><strong>Sito web</strong></td>";
			$html.= "	<td><a>" . $record_organismo["url"] . "</a></td>";
			$html.= "</tr>";
		}
		$html.= "</tbody></table>";
}
