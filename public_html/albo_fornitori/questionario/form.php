<?
  if (!empty($domande)) {
    echo "<table id='questionario-albo' width='100%'>";
    foreach($domande AS $idDomanda => $domanda) {
      $trAttributes = "";
      $chainClass = "";
      if (!empty($domanda["visualizzaSe"])) {
        foreach($domanda["visualizzaSe"] AS $field => $value) { 
          $chainClass .= " hideable-{$field} hideable-{$field}-{$value} ";
        }
      }
      if ($domanda["type"] !== "p") {
        $obbligatorio = "N";
        $class = (isset($domanda["class"])) ? $domanda["class"] : "";
        if (!empty($domanda["obbligatorio"])) {
          if (is_array($domanda["obbligatorio"])) {
            foreach($domanda["obbligatorio"] AS $field => $value) {
              $chainClass .= " domanda-{$field} domanda-{$field}-{$value} ";
            }
          } else {
            $obbligatorio = "S";
          }
        }
        $rel = $obbligatorio . ";" . $domanda["rel"];
        if (!empty($domanda["obbligatorio"]) && $domanda["type"] == "checkbox") {
          $trAttributes .= "class=\"valida {$chainClass}\" title=\"{$idDomanda}\" rel=\"{$obbligatorio};0;0;checked;group_validate\"";
        }
      }
      if (!empty($chainClass) && empty($trAttributes)) {
        $trAttributes .= "class=\"{$chainClass}\" ";
      }
      echo "<tr {$trAttributes}>";
      if ($domanda["type"] == "p") {
        echo "<td style=\"width:100%; border: 1px solid #AAA\" colspan='2'>{$domanda["content"]}</td>";
      } else {
        $name = "questionario[{$idDomanda}]";
        $asterisk = "";
        if (!empty($domanda["obbligatorio"])) {
          $asterisk = "*";
        }
        echo "<td style=\"width:50%; border: 1px solid #AAA\" class='etichetta'>{$domanda["label"]} {$asterisk}</td>";
        echo "<td style=\"border: 1px solid #AAA; padding:5px\">";
          if (!empty($view)) {
            if (isset($risposte[$idDomanda])) {
              if ($domanda["type"] == "checkbox") {
                echo "[X]";
              } else if ($domanda["type"] == "select") {
                echo (!empty($domanda["options"][$risposte[$idDomanda]])) ? $domanda["options"][$risposte[$idDomanda]] : "";
              } else {
                echo $risposte[$idDomanda];
              }
            } else {
              if ($domanda["type"] == "checkbox") {
                echo "[ ]";
              } 
            }
          } else {
            if ($domanda["type"] == "text") {
              ?>
              <input name="<?= $name ?>" rel="<?= $rel ?>" class="valida <?= $class . " " . $chainClass ?>" id="domanda-<?= $idDomanda ?>" value="<?= (isset($risposte[$idDomanda])) ? $risposte[$idDomanda] : "" ?>" title="<?= $domanda["label"] ?>">
              <?
            } else if ($domanda["type"] == "textarea") {
              ?>
              <textarea name="<?= $name ?>" rel="<?= $rel ?>" class="valida <?= $class . " " . $chainClass ?>" style="width:95%" rows="5" id="domanda-<?= $idDomanda ?>" title="<?= $domanda["label"] ?>"><?= (isset($risposte[$idDomanda])) ? $risposte[$idDomanda] : "" ?></textarea>
              <?
            } else if ($domanda["type"] == "checkbox") {
              ?>
              <input name="<?= $name ?>" type="checkbox" class="valida" value="<?= $domanda["value"] ?>" id="domanda-<?= $idDomanda ?>" <?= (!empty($risposte[$idDomanda])) ? "checked" : "" ?> title="<?= $domanda["label"] ?>">
              <?
            } else if ($domanda["type"] == "select") {
              ?>
              <select name="<?= $name ?>" rel="<?= $rel ?>" class="valida <?= $class . " " . $chainClass ?>" id="domanda-<?= $idDomanda ?>" title="<?= $domanda["label"] ?>">
                <option value="">Seleziona...</option>
                <?
                  foreach($domanda["options"] AS $opV => $opL) {
                    $selected = "";
                    if (isset($risposte[$idDomanda]) && $risposte[$idDomanda] == $opV) {
                      $selected = "selected";
                    }
                    ?>
                    <option <?= $selected ?> value="<?= $opV ?>"><?= $opL ?></option>
                    <?
                  }
                ?>
              </select>
              <?
            }
          }
        echo "</td>";
      }
      echo "</tr>";
    }
    echo "</table>";
    if (empty($view)) {
      ?>
      <script>
        $(".valida","#questionario-albo").change(function() {
          if (typeof $(this).attr('id') != 'undefined') {
            var id = $(this).attr('id');
            var hideClass = id.replace('domanda','hideable');
            var chained = $("."+id);
            var hideable = $("."+hideClass);
            if (hideable.length > 0) {
              hideable.hide();
              hideable.find(":input").attr("disabled","disabled").trigger("chosen:updated");
            }
            if (chained.length > 0) {
              chained.each(function() {
                if (typeof $(this).attr("rel") != 'undefined') {
                  rel = $(this).attr("rel").substring(1);
                  rel = "N" + rel;
                  $(this).attr("rel",rel);
                }
              });
            }
            if ($(this).attr('type') == 'checkbox') {
              if (typeof $(this).prop('checked') != "undefined") {
                if ($(this).prop('checked')) {
                  selector = id + "-" + $(this).val();
                } else {
                  selector = id + "-";
                }
              } else if (typeof $(this).attr('checked') != "undefined") {
                if ($(this).attr('checked')) {
                  selector = id + "-" + $(this).val();
                } else {
                  selector = id + "-";
                }
              }
            } if ($(this).find('option').length > 0) {
              selector = id + "-" + $(this).val();
            }
            if (typeof selector != "undefined") {
              var showClass = selector.replace('domanda','hideable');
              var showable = $("."+showClass);
              if (showable.length > 0) {
                showable.not('select').show();
                showable.find(":input").removeAttr("disabled").trigger("chosen:updated");
              }
              chained = $("."+selector);
              if (chained.length > 0) {
                chained.each(function() {
                  if (typeof $(this).attr("rel") != 'undefined' && !$(this).is(":hidden")) {
                    rel = $(this).attr("rel").substring(1);
                    rel = "S" + rel;
                    $(this).attr("rel",rel);
                  }
                });
              }
            }
          }
        });
        $(".valida","#questionario-albo").trigger('change');
      </script>
      <?
    }
  }
?>