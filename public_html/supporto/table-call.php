<?
  session_start();
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  if (isset($_SESSION["codice_utente"])) {
    if (check_permessi("supporto",$_SESSION["codice_utente"]) && in_array($_SESSION["tipo_utente"], array('SAD', 'SUP')) && empty($_SESSION["ente"])) {
        $strsql  = "SELECT b_call_plus.*, b_operatori_economici.ragione_sociale, b_operatori_economici.partita_iva, 
                    b_operatori_economici.codice_fiscale_impresa  
                    FROM b_call_plus
                    JOIN b_operatori_economici ON b_call_plus.codice_utente = b_operatori_economici.codice_utente 
                    WHERE b_call_plus.annullata = 'N' ";
        if ($_POST["archivio"]!="S") $strsql  .= " AND b_call_plus.data_appuntamento >= curdate()  AND b_call_plus.stato < 10 ";
        $strsql .= " ORDER BY b_call_plus.data_appuntamento, b_call_plus.ora_appuntamento, b_call_plus.minuto_appuntamento ";

      $risultato  = $pdo->bindAndExec($strsql);
      $iFilteredTotal = $iTotal = 0;
      $data = array();
      if ($risultato->rowCount() > 0) {
        $iTotal = $risultato->rowCount();
        $iFilteredTotal = $iTotal;
        if ( isset( $_POST['start'] ) && $_POST['length'] != '-1' && is_numeric( $_POST['start'] ) && is_numeric( $_POST['length'] ))
        {
          $start = (int)$_POST['start'];
          $lenght = (int)$_POST['length'];
          $strsql .= " LIMIT " . $start . ", " . $lenght;
        }
        $risultato  = $pdo->bindAndExec($strsql); //invia la query contenuta in $strsql al database apero e connesso
        if ($risultato->rowCount() > 0) {
            while($call = $risultato->fetch(PDO::FETCH_ASSOC)) {
                $columns = [];
                switch ($call["stato"]) {
                    case "0": $color = "#09C"; break;
                    case "10": $color = "#FC0"; break;
                    case "20": $color = "#0C0"; break;
                    case "30": $color = "#C00"; break;
                }
                $columns[] = "<div class=\"status_indicator\" id=\"flag_{$call["codice"]}\" style=\"background-color:{$color}\"></div>";
                $columns[] = $call["codice"];
                $columns[] = mysql2date($call["data_appuntamento"]) . " " . $call["ora_appuntamento"] . ":" . $call["minuto_appuntamento"];
                $columns[] = $call["riferimento"];
                $columns[] = $call["telefono"];
                    ob_start();
                    ?>
                    <a href="/operatori_economici/edit.php?cod=<?= $call["codice_utente"] ?>" target="_blank"><?= $call["ragione_sociale"] ?></a><br>
                    Partita IVA: <?= $call["partita_iva"] ?> - Codice Fiscale: <?= $call["codice_fiscale_impresa"] ?><br>
                    <br>Enti:<br>
                    <?
                        $bind = array(":codice_utente"=>$call["codice_utente"]);
                        $sql = "SELECT b_enti.denominazione, b_enti.dominio FROM b_enti JOIN r_enti_operatori ON r_enti_operatori.cod_ente = b_enti.codice 
                                        WHERE r_enti_operatori.cod_utente = :codice_utente";
                        $ris = $pdo->bindAndExec($sql,$bind);
                        if ($ris->rowCount()>0) {
                            $enti = "";
                            while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
                                $enti .= "<a target='_blank' href='https://{$rec["dominio"]}'>{$rec["denominazione"]}</a><br>";
                            }
                            echo $enti;
                        }
                $columns[] = ob_get_clean();
                $note = $call["note"] . "<hr>" . $call["note_interne"];
                if (!empty($call["note_interne"])) {
                  $note .= "<br><div style='text-align:right; font-weight:bold'>" . mysql2datetime($call["timestamp"]) . " - " . $pdo->go('SELECT concat(cognome, " ", nome) AS nominativo FROM b_utenti WHERE codice = :codice_utente',[":codice_utente"=>$call["utente_modifica"]])->fetch(PDO::FETCH_ASSOC)["nominativo"] . "</div>";
                }
                $columns[] = $note;
                    ob_start();
                    if ($call["stato"] == 0) {
                    ?>
                      <button class="btn btn-round" onClick="iniziaCall(<?= $call["codice"] ?>)" style="background-color:#FC0" title="Prendi in carico"><span class="fa fa-phone"></span></button>
                    <?
                    } else if ($call["stato"] == 10) {
                    ?>
                     <button class="btn btn-round" onClick="chiudiCall(<?= $call["codice"] ?>)" style="background-color:#0C0" title="Chiudi"><span class="fa fa-check"></span></button>
                    <?
                    }
                $columns[] = ob_get_clean();
                $data[] = $columns;
            }
         }
      }
      $output = array(
				"sEcho" => intval($_POST['draw']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $data
			);
      echo json_encode( $output );
    
    }
  }
?>
