<?
  session_start();
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  if (isset($_SESSION["codice_utente"])) {
    if (check_permessi("operatori_economici",$_SESSION["codice_utente"])) {
      $iFilteredTotal = 0;

      $bind =array();
      $strsql  = "SELECT b_utenti.codice, b_utenti.cognome, b_utenti.nome, b_gruppi.gruppo AS tipo,
                  b_operatori_economici.ragione_sociale, b_operatori_economici.partita_iva, b_operatori_economici.codice_fiscale_impresa,
                  b_utenti.email, b_utenti.pec
                  FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice 
                  JOIN b_operatori_economici ON b_utenti.codice = b_operatori_economici.codice_utente ";
      if (isset($_SESSION["ente"])) $strsql.="JOIN r_enti_operatori ON r_enti_operatori.cod_utente = b_utenti.codice ";
      $strsql.= "WHERE (b_gruppi.gerarchia = 3 OR b_gruppi.gerarchia = 4) ";
      if (isset($_SESSION["ente"])) {
         $bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
         $strsql.=" AND r_enti_operatori.cod_ente = :codice_ente";
      }
      $strsql .= " GROUP BY b_operatori_economici.codice 
                   ORDER BY ragione_sociale ASC";
      $risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
      if ($risultato->rowCount()>0) {
				header('Content-Type: application/excel');
				header('Content-Disposition: attachment; filename="export.csv"');
				$risultato = $risultato->fetchAll(PDO::FETCH_ASSOC);
				$first = reset($risultato);
				$keys = array_keys($first);
				$fp = fopen('php://output', 'w');
				fputcsv($fp, $keys,";",'"');
				foreach($risultato AS $oe) {
					fputcsv($fp, $oe,";",'"');
				}
				fclose($fp);
			} else {
				?>
				<h1>Nessun record disponibile</h1>
				<?
      }
    }
  }
?>
