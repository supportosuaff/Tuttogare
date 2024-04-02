<?
  session_start();
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	if (!isset($_SESSION["codice_utente"]) || !isset($_SESSION["ente"])) {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
	  die();
	}
	if (isset($_GET["codice"])) {

		$bind = array();
		$bind[":codice"] = $_GET["codice"];
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$bind[":codice_gara"] = $_GET["codice_gara"];

		$strsql = "SELECT b_comunicazioni.* FROM b_comunicazioni WHERE b_comunicazioni.codice = :codice AND b_comunicazioni.codice_ente = :codice_ente AND codice_gara = :codice_gara";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount()>0){
			$record = $risultato->fetch(PDO::FETCH_ASSOC);
      switch ($record["sezione"]) {
        case "gara":
        	$esito = check_permessi_gara(52,$record["codice_gara"],$_SESSION["codice_utente"]);
    			$edit = $esito["permesso"];
    			break;
        case "concorsi":
        	$esito = check_permessi_concorso(1,$record["codice_gara"],$_SESSION["codice_utente"]);
    			$edit = $esito["permesso"];
    			break;
        case "esecuzione":
        	$esito = check_permessi_esecuzione(1,$record["codice_gara"],$_SESSION["codice_utente"]);
    			$edit = $esito["permesso"];
    			break;
        case "albo":
          $edit = check_permessi("albo_fornitori",$_SESSION["codice_utente"]);
        	break;
        case "dialogo":
          $edit = check_permessi("dialogo_competitivo",$_SESSION["codice_utente"]);
        	break;
        case "sda":
          $edit = check_permessi("sda",$_SESSION["codice_utente"]);
        	break;
        case "mercato":
          $edit = check_permessi("mercato_elettronico",$_SESSION["codice_utente"]);
        	break;
      }
      if (!$edit) {
        echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
        die();
      }
			if ($record["cod_allegati"] != "" && preg_match("/^[0-9\;]+$/",$record["cod_allegati"])) {
				$allegati = explode(";",$record["cod_allegati"]);
				$str_allegati = ltrim(implode(",",$allegati),",");
				$sql = "SELECT * FROM b_allegati WHERE codice IN (" . $str_allegati . ")";
				$ris_allegati = $pdo->query($sql);
			}
			?>
      <h1>DETTAGLIO COMUNICAZIONE - <?= mysql2datetime($record["timestamp"]) ?></h1>
      <strong><? echo $record["oggetto"] ?></strong><br><br>
      <? echo $record["corpo"] ?>
      <? if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) { ?>
            <div class="box"><h2>Allegati</h2>
              <table width="100%" id="tab_allegati">
              <?
              while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
                include($root."/allegati/tr_allegati.php");
              }
              ?>
              </table>
            </div>
        <?
  			}
						$bind = array();
						$bind[":codice"] = $record["codice"];
						$sql  = "SELECT b_operatori_economici.*,r_comunicazioni_utenti.protocollo,r_comunicazioni_utenti.data_protocollo FROM b_operatori_economici JOIN r_comunicazioni_utenti ON b_operatori_economici.codice_utente = r_comunicazioni_utenti.codice_utente ";
						$sql .= " WHERE r_comunicazioni_utenti.codice_comunicazione = :codice";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount()>0) {
							?>
              <div class="box">
                <h2>Destinatari</h2>
                <table width="100%">
                  <?
    							while($record_operatore = $ris->fetch(PDO::FETCH_ASSOC)) {
    								?>
                    <tr>
                      <? if (!empty($record_operatore["protocollo"])) { ?>
                        <td width="200">
                          Prot. n. <strong><?= $record_operatore["protocollo"] ?></strong> del <strong><?= mysql2date($record_operatore["data_protocollo"]) ?></strong>
                        </td>
                      <? } else { ?>
                        <td width="1"></td>
                      <? } ?>
                      <td width="1"><? echo $record_operatore["codice_fiscale_impresa"] ?></td>
                      <td><strong><? echo $record_operatore["ragione_sociale"] ?></strong></td>
                    </tr>
                    <?
    							}
    							?>
                </table>
              </div>
              <?
            }
				} else {
					echo "<h1>Comunicazione inesistente</h1>";
				}
		} else {
			echo "<h1>Comunicazione inesistente</h1>";
		}
	include_once($root."/layout/bottom.php");
	?>
