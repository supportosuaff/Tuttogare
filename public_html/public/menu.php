<?
$strsql  = "SELECT sezione FROM b_pagina WHERE attivo = 'S' AND sezione <> '' ";
if (isset($_SESSION["ente"])) {
  $bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
  $strsql.= " AND codice_ente = :codice_ente ";
} else {
  $strsql.= " AND codice_ente = 0 ";
}
$strsql .= " GROUP BY sezione ORDER BY ordinamento";
$ris = $pdo->bindAndExec($strsql,$bind);
if ($ris->rowCount() > 0) {
  while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
    $sezione = $rec["sezione"];
    echo "<li class=\"dropdown\"><a href='#' class='dropdown-toggle' data-toggle='dropdown'>" . $sezione . " <span class='caret'></span></a><ul  class='dropdown-menu'>";
    $bind_sezione = array(":sezione"=>$sezione);
    $strsql = "SELECT * FROM b_pagina WHERE attivo ='S' AND sezione = :sezione ORDER BY ordinamento";
    $risultato = $pdo->bindAndExec($strsql,$bind_sezione);

    if ($risultato->rowCount() > 0) {
      while($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
        $codice	= $record["codice"];
        $titolo = $record["titolo"];
        $href = "/pagine/id".$codice."-".$titolo;
        $href = str_replace('"',"",$href);
        $href = str_replace(' ',"-",$href);
        $link = $record["link"];
        $tipologia = $record["tipologia"];
        switch($tipologia) {
          case "HTML": echo "<li><a href=\"" . $href ."\" title=\"" . $titolo . "\">" . $titolo . "</a></li>"; break;
          case "Link": echo "<li><a href=\"" . $link ."\" title=\"" . $titolo . "\">" . $titolo . "</a></li>"; break;
        }
      }
    }
    echo "</ul></li>";
  }
}
$strsql = "SELECT * FROM b_pagina WHERE attivo ='S' AND (sezione = '' OR sezione IS NULL) ";
if (isset($_SESSION["ente"])) {
  $strsql.= " AND codice_ente = :codice_ente ";
} else {
  $strsql.= " AND codice_ente = 0 ";
}
$strsql.= " ORDER BY ordinamento";
$risultato = $pdo->bindAndExec($strsql,$bind);
if ($risultato->rowCount() > 0) {
  while($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
    $codice	= $record["codice"];
    $titolo = $record["titolo"];
    $href = "/pagine/id".$codice."-".$titolo;
    $href = str_replace('"',"",$href);
    $href = str_replace(' ',"-",$href);
    $link = $record["link"];
    $tipologia = $record["tipologia"];
    $ordinamento = $record["ordinamento"];
    switch($tipologia) {
      case "HTML": echo "<li class=\"first_level\" ord=\"".$ordinamento."\"><a id=\"menu_pag_" . $codice . "\" href=\"" . $href ."\" title=\"" . $titolo . "\">" . $titolo . "</a></li>"; break;
      case "Link": echo "<li class=\"first_level\" ord=\"".$ordinamento."\"><a id=\"menu_pag_" . $codice . "\" href=\"" . $link ."\" title=\"" . $titolo . "\">" . $titolo . "</a></li>"; break;
    }

  }

}
$strsql = "SELECT b_moduli.* FROM b_moduli WHERE b_moduli.attivo = 'S' AND b_moduli.menu = 'S'";
if (!isset($_SESSION["ente"])) {
   $strsql.= " AND b_moduli.admin = 'S' ";
} else {
  $strsql.= " AND (b_moduli.ente = 'S' AND (b_moduli.tutti_ente = 'S' OR (b_moduli.tutti_ente = 'N' AND b_moduli.codice IN (SELECT cod_modulo FROM r_moduli_ente WHERE cod_ente = :codice_ente))))";
}

$risultato = $pdo->bindAndExec($strsql,$bind);
if ($risultato->rowCount() > 0) {
  while($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
    $radice = $record["radice"];
    $titolo = $record["titolo"];
    $ordinamento = $record["ordinamento"];
    $descrizione = $record["descrizione"];
    echo "<li class=\"first_level\"  ord=\"".$ordinamento."\"><a id=\"menu_mod_".$radice."\" href=\"/". $radice . "/\" title=\"" . $descrizione . "\">";
    echo $titolo;
    echo "</a></li>";
  }

}
?>
