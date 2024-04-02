<?
  include_once "../../../config.php";
  include_once "{$root}/layout/top.php";

  if (! empty($_GET["cod"]) && ! empty($_SESSION["ente"]["codice"])) {
    $sql = "SELECT * FROM b_bandi_albo  WHERE codice = :codice AND manifestazione_interesse <> 'S' AND visualizza_elenco <> 'N' AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) AND pubblica = '2'";
    $ris_albo = $pdo->bindAndExec($sql, array(':codice' => $_GET["cod"], ':codice_ente' => $_SESSION["ente"]["codice"]));
    if($ris_albo->rowCount() > 0) {
      $albo = $ris_albo->fetch(PDO::FETCH_ASSOC);
      ?><h1>ELENCO FORNITORI - ID <?= $albo["id"] ?></h1><?
      $sql = "SELECT b_operatori_economici.ragione_sociale, b_operatori_economici.codice_fiscale_impresa, b_operatori_economici.partita_iva
              FROM b_operatori_economici
              JOIN r_partecipanti_albo ON b_operatori_economici.codice = r_partecipanti_albo.codice_operatore
              WHERE r_partecipanti_albo.codice_bando = :codice
              AND r_partecipanti_albo.ammesso = 'S'
              AND r_partecipanti_albo.valutato = 'S'";
      $operatori = $pdo->bindAndExec($sql, array(':codice' => $albo["codice"]));
      if($operatori->rowCount() > 0) {
        ?>
        <br>
        <div class="box" style="width: 48%; float: left;">
          <table style="width: 100%; table-layout: fixed;">
            <thead>
              <tr>
                <th width="15" style="text-align: left;">#</th>
                <th style="text-align: left; width: 100px">P.IVA</th>
                <th style="text-align: left; width: 140px">C.F.</th>
                <th style="text-align: left;">RAGIONE SOCIALE</th>
              </tr>
            </thead>
            <tbody>
              <?
              $divided = FALSE;
              $half = intval($operatori->rowCount() / 2);
              $i = 0;
              while ($operatore = $operatori->fetch(PDO::FETCH_ASSOC)) {
                $i++;
                ?>
                <tr>
                  <td><?= $i ?></td>
                  <td><?= $operatore["partita_iva"] ?></td>
                  <td><?= $operatore["codice_fiscale_impresa"] ?></td>
                  <td><?= $operatore["ragione_sociale"] ?></td>
                </tr>
                <?
                if(! $divided && $i > $half) {
                  $divided = TRUE;
                  ?>
                  </tbody></table></div>
                  <div class="box" style="width: 48%; float: right;"><table style="width: 100%; table-layout: fixed;"><thead><tr><th width="15" style="text-align: left;">#</th><th style="text-align: left;  width: 100px">P.IVA</th><th style="text-align: left; width: 140px;">P.IVA</th><th style="text-align: left;" width="100">RAGIONE SOCIALE</th></tr></thead><tbody>
                  <?
                }
              }
              ?>
            </tbody>
          </table>
        </div>
        <div class="clear"></div>
        <?
      } else {
        ?><br><div class="box"><h3 class='error'>Nessun operatore presente in questo albo</h3></div><?
      }
    } else {
      echo "<h1>Gara inesistente o privilegi insufficienti</h1>";
    }
  }
  include_once "{$root}/layout/bottom.php";
?>