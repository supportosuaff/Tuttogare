<?
  if (isset($_SESSION["codice_utente"])) {
    $error = true;
    if (!empty($_SESSION["ente"]["codice"]) && !empty($feedback_codice_riferimento) && !empty($feedback_soggetti) && !empty($feedback_tipologia)) {
      $criteri = getCriteriFeedBack();
      if (!isset($feedback_dettaglio_riferimento)) $feedback_dettaglio_riferimento = 0;
      if (!empty($criteri)) {
        $error = false;
        ?>
          <style type="text/css">
            tbody > tr > th {color: #000 !important; text-align: left;}
            thead > tr > td {text-align: center !important;}
          </style>
        <?
        $ris = $pdo->prepare("SELECT * FROM b_feedback WHERE codice_riferimento = :riferimento AND dettaglio_riferimento = :dettaglio AND tipologia = :tipologia 
                              AND codice_utente = :codice_utente AND codice_operatore = :codice_operatore AND codice_punteggio = :codice_punteggio AND utente_modifica = :utente");
        $ris->bindValue(":riferimento",$feedback_codice_riferimento);
        $ris->bindValue(":dettaglio",$feedback_dettaglio_riferimento);
        $ris->bindValue(":tipologia",$feedback_tipologia);
        $ris->bindValue(":utente",$_SESSION["codice_utente"]);
        foreach($feedback_soggetti AS $num => $soggetto) {
          $somma = 0;
          $displayVote = true;
          $ris->bindValue(":codice_utente",$soggetto["codice_utente"]);
          $ris->bindValue(":codice_operatore",$soggetto["codice_operatore"]);
          ?>
          <div class="box">
            <table width="100%">
              <tbody>
                <tr>
                  <th class="etichetta" width="20%">Ragione Sociale</th>
                  <td><? echo $soggetto["ragione_sociale"]; ?></td>
                  <th class="etichetta" width="20%">P.IVA</th>
                  <td><?= $soggetto["partita_iva"] ?></td>
                </tr>
              </tbody>
            </table>
            <input type="hidden" name="soggetto[<?= $num ?>][codice_utente]" value="<?= $soggetto["codice_utente"] ?>">
            <input type="hidden" name="soggetto[<?= $num ?>][codice_operatore]" value="<?= $soggetto["codice_operatore"] ?>">
          </div>
          <?
          foreach($criteri AS $criterio) {
            $ris->bindValue(":codice_punteggio",$criterio["codice"]);
            $ris->execute();
            $valutazione = 0;
            if ($ris->rowCount() == 1) $valutazione = $ris->fetch(PDO::FETCH_ASSOC)["valutazione"];
            if ($valutazione > 0) {
              $somma += $valutazione * $criterio["ponderazione"];
            } else {
              $displayVote = false;
            }
            ?>
            <table width="100%">
              <thead>
                <tr>
                  <th class="etichetta" colspan="11"><?= $criterio["titolo"] ?><br><small><?= $criterio["descrizione"] ?></small></th>
                </tr>
              </thead>
              <tbody>
              <tr style="background-color:#999; color:#FFF;">
                <?
                for ($i=0; $i <= 10; $i++)
                {
                  ?><td width="7%" style="text-align:center"><?= $i == 0 ? "Non Valutato" : $i ?></td><?
                }
                ?>
              </tr>
              <?
								for ($i=0; $i <= 10; $i++)
								{
									?>
									<td style="text-align:center">
										<label>
											<img src="/img/<?= $i ?>.png" alt="<?= $i ?>"><br>
											<input type="radio" name="soggetto[<?= $num ?>][<?= $criterio["codice"] ?>]" value="<?= $i ?>" <? if( $valutazione == $i )  echo 'checked="checked"' ?>>
										</label>
									</td>
									<?
								}
							?>
              </tbody>
            </table>
            <?
          }
          if ($displayVote) { ?>
            <div class="box" style="text-align:center">
              <strong>Punteggio registrato</strong><br>
              <? $feed = number_format($somma / count($criteri),2); ?>
              <img src="/img/<?= number_format($feed,0) ?>.png" alt="<?= $feed ?>"><br>
              <h1 style="text-align:center"><?= $feed ?></h1>
            </div>
          <?
          }
        }
      }
    } 
    if ($error) {
      echo "<h3>Errore di configurazione, si prega di conttatare il servizio Help desk.</h3>";
    }
  }

?>