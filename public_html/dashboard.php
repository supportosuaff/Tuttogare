<?
  if (isset($_SESSION["codice_utente"]) && $_SESSION["gerarchia"] === "0" && !isset($_SESSION["ente"])) {

    include_once($root."/inc/p7m.class.php");

    $sql_enti = "SELECT codice FROM b_enti WHERE attivo = 'S' AND b_enti.ambienteTest = 'N' AND (b_enti.dominio IS NOT NULL OR b_enti.dominio <> '')";
    $ris_enti = $pdo->query($sql_enti);
    /* $sql_enti_requested_ssl = "SELECT codice FROM b_enti WHERE requested_ssl = 'N' AND (b_enti.dominio IS NOT NULL OR b_enti.dominio <> '')";
    $ris_enti_requested_ssl = $pdo->query($sql_enti_requested_ssl); */
    $sql_operatori = "SELECT b_operatori_economici.codice FROM b_operatori_economici
                      JOIN r_enti_operatori ON b_operatori_economici.codice_utente = r_enti_operatori.cod_utente
                      JOIN b_utenti ON b_operatori_economici.codice_utente = b_utenti.codice
                      JOIN b_enti ON r_enti_operatori.cod_ente = b_enti.codice
                      WHERE b_utenti.attivo = 'S' AND  b_enti.attivo = 'S' AND b_enti.ambienteTest = 'N' GROUP BY b_operatori_economici.codice";

    $ris_operatori = $pdo->query($sql_operatori);
    $sql_gare = "SELECT b_gare.codice FROM b_gare JOIN b_enti ON b_gare.codice_gestore = b_enti.codice WHERE b_enti.ambienteTest = 'N'";
    $ris_gare = $pdo->query($sql_gare);



    $strsql = "SELECT * FROM b_coda WHERE (inviata = 'N' OR inviata = 'W')";
    $ris_coda = $pdo->query($strsql);

    ?>
      <h1>DASHBOARD</h1>
      <h2 class="box" style="text-align:right">
      <? /* Richieste SSL Residue: <?= $ris_enti_requested_ssl->rowCount() ?> -  */ ?>
      Comunicazioni in coda: <strong><?= $ris_coda->rowCount() ?></strong>
      <button onclick="$('#wait').show(); $.ajax('/elabora_coda.php',{complete:function(){window.location.href=window.location.href}})">
        Elabora Coda
      </button>
      </h2>
      <? include($root."/elabora_coda/dashboard.php") ?>

      <table style="width:100%">
        <tr>
          <td width="33%"><h2 style="text-align:center;">GARE</h2></td>
          <td width="33%"><h2 style="text-align:center;">ENTI</h2></td>
          <td width="33%"><h2 style="text-align:center;">OPERATORI ECONOMICI</h2></td>
        </tr>
        <tr>
          <td><h2 style="text-align:center; font-size:48px;"><?= $ris_gare->rowCount() ?></h2></td>
          <td><h2 style="text-align:center; font-size:48px;"><?= $ris_enti->rowCount() ?></h2></td>
          <td><h2 style="text-align:center; font-size:48px;"><?= $ris_operatori->rowCount() ?></h2></td>
        </tr>
      </table>
    <?
    $sql = "SELECT b_buste.*, r_partecipanti.codice_gara, r_partecipanti.codice_lotto FROM b_buste JOIN r_partecipanti ON b_buste.codice_partecipante = r_partecipanti.codice WHERE b_buste.aperto = 'S' ";
    // AND DATE(b_buste.timestamp) < '".date('Y-m-d',strtotime('-6 month'))."'";
    $ris_buste_aperte = $pdo->query($sql);
    if ($ris_buste_aperte->rowCount() > 0) {
      $size = 0;
      while($busta = $ris_buste_aperte->fetch(PDO::FETCH_ASSOC)) {
        if (file_exists($config["doc_folder"] . "/" . $busta["codice_gara"] . "/" . $busta["codice_lotto"] . "/" . $busta["nome_file"])) {
          $size += filesize($config["doc_folder"] . "/" . $busta["codice_gara"] . "/" . $busta["codice_lotto"] . "/" . $busta["nome_file"]);
        }
      }
      echo "<br><h1>Spazio occupato da buste aperte: ";
      echo human_filesize($size);
      echo "</h1>";
    }
    echo "<br><h1>Spazio residuo " . human_filesize(disk_free_space("/")). "</h1>";
  }
?>
