<?
  session_start();
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  if (!empty($_GET["cod"]) && isset($_SESSION["codice_utente"]) && check_permessi("user",$_SESSION["codice_utente"])) {
    $codice = $_GET["cod"];
    $bind = array();
    $bind[":codice"] = $codice;

    $s = "SELECT b_moduli.* FROM b_moduli WHERE b_moduli.tutti_utente = 'N' AND b_moduli.codice = :codice AND
            gerarchia >= '". $_SESSION["gerarchia"] . "'
            GROUP BY b_moduli.codice
            ORDER BY b_moduli.codice";
    $r = $pdo->bindAndExec($s,$bind);
    if ($r->rowCount()===1) {
      $re = $r->fetch(PDO::FETCH_ASSOC);
      $show = true;
      if ($re["ente"] == "S" && $re["tutti_ente"] == "N" && isset($_SESSION["ente"])) {
        $sql = "SELECT * FROM r_moduli_ente WHERE cod_modulo = ".$re["codice"]." AND cod_ente = " . $_SESSION["ente"]["codice"];
        $check_ente = $pdo->query($sql);
        if ($check_ente->rowCount() == 0) $show=false;
      }
      if ($show) {
          ?>
          <h1>
            <span class="<?= $re["glyph"] ?>"></span> <?= $re["titolo"] ?><br>
          </h1>
          <div class="box"><?= $re["descrizione"] ?></div>
          <?
            $bind = array();
            $bind[":codice"] = $re["codice"];
            $strsql = "SELECT b_utenti.*, r_moduli_utente.codice AS codiceP FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice ";
            $strsql .= " JOIN r_moduli_utente ON b_utenti.codice = r_moduli_utente.cod_utente ";
            if (((!isset($_SESSION["amministratore"])) || (isset($_SESSION["ente"])))) {
              $strsql .= "JOIN b_enti ON b_utenti.codice_ente = b_enti.codice ";
            }
            $strsql .= "WHERE r_moduli_utente.cod_modulo = :codice";
            if (((!isset($_SESSION["amministratore"])) || (isset($_SESSION["ente"])))) {
              $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
              $strsql.= " AND (b_enti.codice = :codice_ente";
              $strsql.= " OR b_enti.sua = :codice_ente) ";
              $bind[":gerarchia"] = $_SESSION["gerarchia"];
              $strsql.= " AND b_gruppi.gerarchia > :gerarchia";
            }
            $strsql .= " AND b_gruppi.gerarchia < 3 ";
            $risultato = $pdo->bindAndExec($strsql,$bind);
            if ($risultato->rowCount() > 0) {
              ?>
              <table width="100%" class="elenco">
              <?
              while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr id="permesso_<?= $record["codiceP"] ?>">
                  <td><strong><?= $record["cognome"] ?> <?= $record["nome"] ?></strong></td>
                  <?
                    if (empty($record["codice_ente"])) {
                      $denominazione = "AMMINISTRAZIONE";
                    } else {
                      $sql = "SELECT * FROM b_enti WHERE codice = :codice_ente ";
                      $ris_ente = $pdo->bindAndExec($sql,array(":codice_ente"=>$record["codice_ente"]));
                      if ($ris_ente->rowCount()==1) {
                        $denominazione =  $ris_ente->fetch(PDO::FETCH_ASSOC)["denominazione"];
                      }
                    }
                    ?>
                    <td width="30%"><?= $denominazione ?></td>
                    <?
                  ?>
                  <td width="120"><?= mysql2datetime($record["timestamp"]) ?></td>
                  <td width="5" style"text-align:center"><input type="image" onClick="elimina('<? echo $record["codiceP"] ?>','user/permessi');" src="/img/del.png" title="Elimina"></td>
                </tr>
                <?
              }
              ?>
            </table>
            <?
          }
        }
      }
    }
?>
