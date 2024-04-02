<?
  include_once '../../../config.php';
  include_once "{$root}/layout/top.php";
  if(empty($_SESSION["codice_utente"]) || !check_permessi("scadenzario/gestione",$_SESSION["codice_utente"])) {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  } else {
    $codice_ente = 0;
    if (isset($_SESSION["ente"])) {
      $codice_ente = !empty($_SESSION["record_utente"]["codice_ente"]) ? $_SESSION["record_utente"]["codice_ente"] : $_SESSION["ente"]["codice"];
    }
    $scadenza = get_campi("b_scadenze");
    $scadenza["codice_ente_destinatario"] = 0;
    if(!empty($_GET["codice"])) {
      $bind = array();
      $bind[":codice"] = $_GET["codice"];
      $sql = "SELECT * FROM b_scadenze WHERE codice = :codice ";
      if ($_SESSION["gerarchia"]!=="0") {
        if ($_SESSION["gerarchia"] > 1) {
          $bind[":codice_utente"] = $_SESSION["record_utente"]["codice"];
          $sql .= " AND codice_utente = :codice_utente ";
        } else {
          $bind[":codice_ente"] = $_SESSION["record_utente"]["codice_ente"];
          $sql .= " AND codice_ente = :codice_ente ";
        }
      }

      $ris = $pdo->bindAndExec($sql, $bind);
      if($ris->rowCount() == 1) {
        $scadenza = $ris->fetch(PDO::FETCH_ASSOC);
      } else {
        echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
      }
    }
    ?>
    <h1>GESTIONE PROMEMORIA</h1>
    <link rel="stylesheet" href="/contratti/css.css" media="screen" title="Button Style">
    <form name="box" method="post" action="save.php" rel="validate">
  		<input type="hidden" name="codice" value="<?= $scadenza["codice"]; ?>">
  		<div class="comandi">
  			<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
  		</div>
      <table class="box" style="width:100%">
        <tr>
          <td class="etichetta">Data e ora scadenza *:</td>
          <td width="25%"><input type="text" name="data" value="<?= mysql2datetime($scadenza["data"]) ?>" rel="S;16;16;DT" title="Data e ora" class="datetimepick"></td>
          <? if ($_SESSION["gerarchia"] < 2) { ?>
            <td width="25%" class="etichetta">Tipologia Utente *:</td>
            <td width="25%">
              <select name="codice_gerarchia" id="select_codice_gerarchia" title="Tipologia Utente" rel="S;1;0;A" onchange="update_utenti();update_modulo();">
                <option value="0">Tutti</option>
                <?
                $bind = array();
                $bind = array(':gerarchia' => $_SESSION["gerarchia"]);
                $sql = "SELECT * FROM b_gruppi WHERE gerarchia >= :gerarchia AND id <> 'CON' AND disponibile = 'S'";
                if (isset($_SESSION["ente"])) $sql .= " AND gerarchia > 0 ";
                $ris = $pdo->bindAndExec($sql,$bind);
                if($ris->rowCount() > 0) {
                  while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
                    ?><option value="<?= $rec["codice"] ?>"><?= $rec["gruppo"] ?></option><?
                  }
                }
                ?>
              </select>
              <script>
                $('#select_codice_gerarchia').val('<?= $scadenza["codice_gerarchia"] ?>');
              </script>
            </td>
            <? } else { ?>
              <td colspan="2">
                <input type="hidden" name="codice_gerarchia" id="select_codice_gerarchia" title="Tipologia Utente" value="<?= $_SESSION["gerarchia"] ?>">
              </td>
            <? } ?>
        </tr>
        <?
        $destinatario = FALSE;
        if($_SESSION["gerarchia"] === "0" && !isset($_SESSION["ente"])) {
          $destinatario = TRUE;
          ?>
          <tr>
            <td class="etichetta">Ente *:</td>
            <td>
              <?
              $sql  = "SELECT * FROM b_enti WHERE attivo = 'S' AND sua IS NULL OR sua = 0";
              $ris = $pdo->bindAndExec($sql);
              ?>
              <select name="codice_ente" id="select_codice_ente" rel="S;1;0;N" title="Ente" onchange="update_ente_destinatario();update_utenti();update_modulo();">
                <option value="0">Tutti gli enti</option>
                <?
                while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
                  ?><option value="<?= $rec["codice"] ?>"><?= $rec["denominazione"] ?></option><?
                }
                ?>
              </select>
              <script>
                $('#select_codice_ente').val('<?= $scadenza["codice_ente"] ?>');
                </script>

            </td>
          <?
        }
        if(!$destinatario) { ?>
          <input type="hidden" id="select_codice_ente" name="codice_ente" value="<?= $_SESSION["ente"]["codice"] ?>">
        <? }
        if($_SESSION["gerarchia"] === "0" || ($_SESSION["gerarchia"] === "1" && $_SESSION["ente"]["codice"] == $_SESSION["record_utente"]["codice_ente"])) {
          if(!$destinatario) echo '<tr>';
          ?>
            <td class="etichetta">
              <?= !$destinatario ? "Ente" : "Ente destinatario"; ?> *:
            </td>
            <td <?= !$destinatario ? 'colspan="3"' : null ?>>
              <select name="codice_ente_destinatario" id="codice_ente_destinatario" rel="S;1;0;N" title="Ente" onchange="update_utenti();update_modulo();">
                <option value="0">Tutti gli enti</option>
                <?
                $bind = array();
    						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
    						$sql  = "SELECT * FROM b_enti WHERE (codice = :codice_ente OR sua = :codice_ente) ";
    						if ($_SESSION["gerarchia"] > 0) {
    							$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
    							$sql .= " AND (codice = :codice_ente_utente OR sua = :codice_ente_utente)";
    						}
    						$sql .= "ORDER BY codice, denominazione";
    						$ris = $pdo->bindAndExec($sql,$bind);
                if($ris->rowCount() > 0) {
                  while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
                    ?><option value="<?= $rec["codice"] ?>"><?= $rec["denominazione"] ?></option><?
                  }
                }
                ?>
              </select>
              <script>
                $('#codice_ente_destinatario').val('<?= $scadenza["codice_ente_destinatario"] ?>');
              </script>
            </td>
          </tr>
          <?
        } else { ?>
          <input type="hidden" id="select_codice_ente" name="codice_ente" value="<?= $_SESSION["ente"]["codice"] ?>">
          <input type="hidden" id="codice_ente_destinatario" name="codice_ente_destinatario" value="<?= $_SESSION["record_utente"]["codice_ente"] ?>">
        <? }
        ?>
        <tr>
          <td class="etichetta">Destinatari: *</td>
          <td colspan="3">
            <? if ($_SESSION["gerarchia"] < 2) { ?>
              <select id="select_utente" name="codice_utente" title="Visibilit&agave;" rel="S;1;0;N" onchange="if ($(this).val() > '0') { $('#modulo').hide(); $('#select_codice_modulo').val('0').trigger('chosen:updated') } else { $('#modulo').show(); }"><option value="0">A tutti gli utenti filtrati secondo i criteri stabiliti</option>
              <optgroup id="utenti" label="Utenti Specifici"></optgroup>
              <option value="<?= $_SESSION["codice_utente"] ?>">Solo a me</option>
            </select>
            <? } else { ?>
              <input type="hidden" id="codice_utente" name="codice_utente" value="<?= $_SESSION["record_utente"]["codice"] ?>">
              Solo a me
            <? } ?>

          </td>
        </tr>
        <? if ($_SESSION["gerarchia"] < 2) { ?>
          <tr id="modulo" <? if (!empty($scadenza["codice_utente"])) { ?>style='display:none' <? } ?>>
            <td class="etichetta">Modulo di riferimento *:</td>
            <td colspan="3">
              <select name="codice_modulo" id="select_codice_modulo" title="Modulo di riferimento" rel="S;1;0;A">
                <option value="0">Tutti</option>
                <optgroup id="moduli_filtrati" label="Moduli Disponibili">
                </optgroup>
              </select>
            </td>
          </tr>
        <? } ?>
        <tr>
          <td class="etichetta">Titolo: *</td>
          <td colspan="3"><input type="text" class="titolo_edit" name="oggetto" title="Oggetto" value="<?= $scadenza["oggetto"] ?>" rel="S;3;500;A"></td>
        </tr>
        <tr>
          <td colspan="4">
            <textarea name="descrizione" title="Descrizione" rel="S;2;0;A" class="ckeditor_simple"><?= $scadenza["descrizione"] ?></textarea>
          </td>
        </tr>
      </table>
      <div id="promemoria" style="width:100%">
        <?
        $sql = "SELECT * FROM b_alert_scadenze WHERE codice_scadenza = :codice_scadenza";
        $ris = $pdo->bindAndExec($sql, array(':codice_scadenza' => $scadenza["codice"]));
        if($ris->rowCount() > 0) {
          while($rec_promemoria = $ris->fetch(PDO::FETCH_ASSOC)) {
            include 'promemoria.php';
          }
        }
        ?>
      </div>
      <button type="button" class="submit_big button-highlight" onclick="aggiungi('promemoria.php','#promemoria');return false;">Aggiungi alert</button>
      <input type="submit" class="submit_big" value="Salva">
    </form>
    <script type="text/javascript">
      function update_utenti() {
        var data = {
          codice_gerarchia: $('#select_codice_gerarchia').val(),
          <? if($_SESSION["gerarchia"] === "0" || ($_SESSION["gerarchia"] >= 1 && $_SESSION["ente"]["codice"] == $_SESSION["record_utente"]["codice_ente"])) { ?>codice_ente_destinatario: $('#codice_ente_destinatario').val(),<? echo PHP_EOL; } ?>
          <? if($_SESSION["gerarchia"] > 0) {?>codice_ente: '<?= $_SESSION["ente"]["codice"] ?>',<? echo PHP_EOL; } else {?>codice_ente: $('#select_codice_ente').val(),<? echo PHP_EOL; }?>
        };
        var currentValue_utente = '<?= !empty($scadenza["codice_utente"]) ? $scadenza["codice_utente"] : 0 ?>';
        $.ajax({
          url: 'utenti.php',
          type: 'POST',
          dataType: 'html',
          async: false,
          data: data,
          beforeSend: function(e) {
            if($('#select_utente').val() > 0) {
              currentValue_utente = $('#select_utente').val();
            }
            $('#utenti').html('');
            $('#wait').show();
          }
        })
        .done(function(data) {
          $('#utenti').html(data);
        })
        .fail(function() {
          jalert('Non è stato possibile aggiornare i bandi. Si è verificato un problema, si prega di riprovare.');
        })
        .always(function() {
          $("#select_utente").chosen("destroy").val(currentValue_utente).chosen({width: '100%'});
          $('#wait').hide();
        });
      }

      <? if($_SESSION["gerarchia"] === "0") { ?>
        function update_ente_destinatario() {
          var data = {
            <? if($_SESSION["gerarchia"] > 0) {?>codice_ente: '<?= $_SESSION["ente"]["codice"] ?>',<? echo PHP_EOL; } else {?>codice_ente: $('#select_codice_ente').val(),<? echo PHP_EOL; }?>
          };
          var currentValue = '<?= $scadenza["codice_ente_destinatario"] ?>';
          $.ajax({
            url: 'enti.php',
            type: 'POST',
            async: false,
            dataType: 'html',
            data: data,
            beforeSend: function(e) {
              $('#wait').show();
              if($('#codice_ente_destinatario').val() > 0) {
                currentValue = $('#codice_ente_destinatario').val();
              }
            }
          })
          .done(function(data) {
            $('#codice_ente_destinatario').find('option').not(':first').remove();
            $('#codice_ente_destinatario').append(data).val(currentValue).trigger('chosen:updated');
          })
          .fail(function() {
            jalert('Non è stato possibile aggiornare i bandi. Si è verificato un problema, si prega di riprovare.');
          })
          .always(function() {
            $('#wait').hide();
          });
        }
      <? } ?>

      function update_modulo() {
        var data = {

          <? if($_SESSION["gerarchia"] === "0" || ($_SESSION["gerarchia"] >= 1 && $_SESSION["ente"]["codice"] == $_SESSION["record_utente"]["codice_ente"])) { ?>codice_ente_destinatario: $('#codice_ente_destinatario').val(),<? echo PHP_EOL; } ?>
          <? if($_SESSION["gerarchia"] > 0) {?>codice_ente: '<?= $_SESSION["ente"]["codice"] ?>',<? echo PHP_EOL; } else {?>codice_ente: $('#select_codice_ente').val(),<? echo PHP_EOL; }?>
        };
        var currentValue_user = '<?= $scadenza["codice_utente"] ?>';

        $.ajax({
          url: 'moduli.php',
          type: 'POST',
          dataType: 'html',
          data: data,
          async: false,
          beforeSend: function(e) {
            currentValue_user = $('#select_codice_modulo').val();
            $('#moduli_filtrati').html('');
            $('#wait').show();
          }
        })
        .done(function(data) {
          $('#moduli_filtrati').html(data);
        })
        .fail(function() {
          jalert('Non è stato possibile aggiornare i bandi. Si è verificato un problema, si prega di riprovare.');
        })
        .always(function() {
          $("#select_codice_modulo").chosen("destroy");
          $("#select_codice_modulo").chosen({width: '100%'});
          $("#select_codice_modulo").val(currentValue_user);
          $("#select_codice_modulo").trigger('chosen:updated');
          $('#wait').hide();
        });
      }
      update_utenti();
      $('#select_utente').val('<?= $scadenza["codice_utente"] ?>');
      update_modulo();
      $('#select_codice_modulo').val('<?= $scadenza["codice_modulo"] ?>');


      $(document).ready(function() {
        $('#select_codice_gerarchia').trigger('change');
        <? if($_SESSION["gerarchia"] < 1) {?>$('#select_codice_ente').trigger('change');<?= PHP_EOL; } ?>
        <? if($_SESSION["gerarchia"] === "0" || ($_SESSION["gerarchia"] >= 1 && $_SESSION["ente"]["codice"] == $_SESSION["record_utente"]["codice_ente"])) { ?>$('#codice_ente_destinatario').trigger('change');<? echo PHP_EOL; } ?>
        $('#select_codice_modulo').trigger('change');
      });
      </script>
    <?
  }
  include_once "{$root}/layout/bottom.php";
?>
