<?
$bind = array();
$bind[":codice_gara"] = $record_gara["codice"];
$sql_classificazioni  = "SELECT b_categorie_progettazione.id as id, b_categorie_progettazione.corrispondenze_143 as classe, b_categorie_progettazione.complessita as complessita,b_categorie_progettazione.descrizione as descrizione, b_qualificazione_progettazione.importo as importo FROM b_qualificazione_progettazione ";
$sql_classificazioni .= "JOIN b_categorie_progettazione ON b_categorie_progettazione.codice = b_qualificazione_progettazione.codice_categoria ";
$sql_classificazioni .= "WHERE b_qualificazione_progettazione.codice_gara = :codice_gara";
$ris_classificazioni = $pdo->bindAndExec($sql_classificazioni,$bind);
if ($ris_classificazioni->rowCount()>0) {
	$html .="<table style=\"width:100%\"><tr><td>Decennio di riferimento: dal </td><td>__/__/20__</td><td>al</td><td>__/__/20__</td></tr>";
	$html .="</tbody></table><table style=\"width:100%\"><thead><tr><th scope=\"col\" style=\"width:5%\">ID</th><th scope=\"col\" style=\"width:5%\">Classe e Categoria</th><th scope=\"col\" style=\"width:10%\">Grado di complessit&agrave;</th><th scope=\"col\" style=\"width:70%\">Declaratoria sintetica</th><th scope=\"col\" colspan=\"3\" style=\"width:10%\" ><table><tr><td  colspan=\"3\">Importi x 1.000</td></tr><tr><td colspan=\"2\">Lavori da bando</td><td><table><tr><td colspan=\"2\">Requisito minimo:</td></tr><tr><td>Lettera b)</td><td>Lettera c)</td></tr></table></td></tr></table></th></tr></thead><tbody>";
while ($rec_qualificazione = $ris_classificazioni->fetch(PDO::FETCH_ASSOC)) {
	$html.= "<tr><td>" . $rec_qualificazione["id"] . "</td><td>" . $rec_qualificazione["classe"] . "</td><td>" . number_format($rec_qualificazione["complessita"],2,",",".") . "</td><td>" . $rec_qualificazione["descrizione"] . "</td><td>".number_format($rec_qualificazione["importo"],2,",",".")."</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
}
$html.= "</tbody></table>";
}
?>
