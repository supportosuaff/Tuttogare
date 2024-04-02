<?
  if (isset($elenco_comunicazioni) && $elenco_comunicazioni->rowCount() > 0 && isset($_SESSION["gerarchia"]) && $_SESSION["gerarchia"] < 3) {
?>
    <table style="font-size:12px" width="100%" class="elenco">
      <thead>
        <tr>
          <td>Data</td>
          <td>Oggetto</td>
          <td>Destinatari</td>
        </tr>
      </thead>
      <tbody>
        <?
          $destinatari = $pdo->prepare("SELECT b_operatori_economici.ragione_sociale, r_comunicazioni_utenti.codice AS protocollo_interno, r_comunicazioni_utenti.sync, r_comunicazioni_utenti.timestamp AS data_interna, r_comunicazioni_utenti.protocollo, r_comunicazioni_utenti.data_protocollo, r_comunicazioni_utenti.letto FROM b_operatori_economici JOIN r_comunicazioni_utenti ON b_operatori_economici.codice_utente = r_comunicazioni_utenti.codice_utente WHERE r_comunicazioni_utenti.codice_comunicazione = :codice");
          $user = $pdo->prepare("SELECT CONCAT(cognome,' ',nome) AS user FROM b_utenti WHERE codice = :codice");
          while ($comunicazione = $elenco_comunicazioni->fetch(PDO::FETCH_ASSOC)) { 
            ?>
          <tr>
            <td width="13%"><? echo mysql2datetime($comunicazione["timestamp"]) ?></td>
            <td width="58%">
              <a href="/comunicazioni/pa/content.php?codice=<? echo $comunicazione["codice"] ?>&codice_gara=<? echo $comunicazione["codice_gara"] ?>"><? echo substr($comunicazione["oggetto"],0,180) . "..." ?></a>
              <div style="text-align:right">
                <small>Utente: <? $user->bindValue(":codice",$comunicazione["utente_modifica"]); $user->execute(); echo $user->fetch(PDO::FETCH_ASSOC)["user"]; ?></small>
              </div>
            </td>
            <td width="30%">
              <?
              $destinatari->bindValue(":codice",$comunicazione["codice"]);
  						$destinatari->execute();
  						if ($destinatari->rowCount()>0) {
                if ($destinatari->rowCount()>1) echo "
                <button class='submit' onclick='$(\"#list_".$comunicazione["codice"]."\").slideToggle(\"fast\");' style='cursor:pointer'><span class='fa fa-list'></span> Destinatari multipli</button>
                <div style='display:none' id='list_".$comunicazione["codice"]."'>";
                while($destinatario = $destinatari->fetch(PDO::FETCH_ASSOC)) {
                  if ($destinatari->rowCount()>1) echo "<div class='box'>";
                  ?><strong><? echo $destinatario["ragione_sociale"] ?></strong><br><?
                  if($destinatario["sync"] == "S") {
                    $hash = simple_encrypt($destinatario["protocollo_interno"], "ricevute-pec");
                    $hash = base64_encode($hash);
                    ?><a href="/comunicazioni/download-ricevute.php?ricevuta=<?= $hash ?>" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;<?
                  }
                  if (!empty($destinatario["protocollo"])) { ?>
                    <small>Prot. n. <?= $destinatario["protocollo"] ?> del <?= mysql2date($destinatario["data_protocollo"]) ?></small><?
                  } else {
                    ?><small>Prot. n. <?= $destinatario["protocollo_interno"] ?> del <?= mysql2date($destinatario["data_interna"]) ?> - Assegnato dal sistema</small><?
                  }
                  if ($destinatari->rowCount()>1) echo "</div>";
                }
                if ($destinatari->rowCount()>1) echo "</div>";
              }
              ?>
            </td>
          </tr>
      <? }  ?>
      </tbody>
  </table>
<? } ?>
