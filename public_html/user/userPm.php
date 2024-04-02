<?
  session_start();
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  if (!empty($_GET["cod"]) && isset($_SESSION["codice_utente"]) && check_permessi("user",$_SESSION["codice_utente"])) {
    $codice = $_GET["cod"];
    $bind = array();
    $bind[":codice"] = $codice;
    $strsql = "SELECT b_utenti.* FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice ";
    if (((!isset($_SESSION["amministratore"])) || (!$_SESSION["amministratore"]))) {
      $strsql .= "JOIN b_enti ON b_utenti.codice_ente = b_enti.codice ";
    }
    $strsql .= "WHERE b_utenti.codice = :codice";
    if (((!isset($_SESSION["amministratore"])) || (!$_SESSION["amministratore"]))) {
      $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
      $strsql.= " AND (b_enti.codice = :codice_ente";
      $strsql.= " OR b_enti.sua = :codice_ente) ";
      $bind[":gerarchia"] = $_SESSION["gerarchia"];
      $strsql.= " AND b_gruppi.gerarchia > :gerarchia";
    }
    $strsql .= " AND b_gruppi.gerarchia < 3 ";
    $risultato = $pdo->bindAndExec($strsql,$bind);
    if ($risultato->rowCount() > 0) {
      $record = $risultato->fetch(PDO::FETCH_ASSOC);
      ?>
      <h1><?= $record["cognome"] . " " . $record["nome"] ?></h1>
      <div style="text-align:right">
        Ultima Modifica <?= mysql2datetime($record["timestamp"]) ?>
      </div>
      <?
      $sql = "SELECT b_moduli.* FROM b_moduli JOIN r_moduli_utente ON b_moduli.codice = r_moduli_utente.cod_modulo WHERE r_moduli_utente.cod_utente = :codice_utente ORDER BY titolo";
      $ris_perm = $pdo->bindAndExec($sql,array(":codice_utente"=>$record["codice"]));
      if ($ris_perm->rowCount()) {
        ?>
        <table width="100%" class="elenco">
        <?
        while($modulo = $ris_perm->fetch(PDO::FETCH_ASSOC)) {
          ?>
          <tr>
            <td width="5" style="text-align:center"><span class="fa-2x <?= $modulo["glyph"] ?>"></span></td>
            <td><strong><?= $modulo["titolo"] ?></strong><br><?= $modulo["descrizione"] ?></td>
          </tr>
          <?
        }
        ?>
      </table>
      <?
      }
    }
  }
?>
