<? if (isset($_SESSION["codice_utente"])) {

  function DGUEversions() {
    return [
      "2016-50" => [
        "label" => "D.lgs 50/2016",
        "inizio" => null,
        "fine" => '2023-06-30 23:59:59'
      ],
      "2023-36" => [
        "label" => "D.lgs 36/2023",
        "inizio" => '2023-07-01 00:00:00',
        "fine" => null
      ],
    ];
  }

  function findDGUEVersion($date = null) {
    $return = "2023-36";
    $versions = DGUEversions();
    if (!empty($versions)) {
      $cDate = mysql2date($date);
      if (!empty($date) && !empty($cDate)) {
        $time = strtotime($date);
        foreach($versions AS $key => $version) {
          $start = null;
          $end = null;
          if (!empty($version["inizio"])) {
            $start = strtotime($version["inizio"]);
          }
          if (!empty($version["fine"])) {
            $end = strtotime($version["fine"]);
          }
          if ((empty($start) || $start < $time) && (empty($end) || $end > $time)) {
            $return = $key;
          }
        }
      }
    }
    return $return;
  }

  function getDGUECriteria($version = null) {
    global $pdo;
    if (empty($version)) {
      $version = findDGUEVersion();
    }
    if ($version != "2016-50") {
      $pdo->go("SET NAMES utf8");
    }
    return $pdo->go("SELECT b_dgue_settings.*
						FROM b_dgue_settings
						WHERE b_dgue_settings.attivo = 'S' AND version = :version
						ORDER BY b_dgue_settings.codifica_criterio",[":version"=>$version])->fetchAll(PDO::FETCH_ASSOC);
  }


  function getSelectedCriteriaFromRequest($riferimento,$sezione) {
    global $pdo;
    return $pdo->go("SELECT codice_form FROM r_dgue_gare WHERE r_dgue_gare.codice_gara = :codice_riferimento AND r_dgue_gare.sezione = :sezione",
                        [":codice_riferimento"=>$riferimento,":sezione"=>$sezione])->fetchAll(PDO::FETCH_COLUMN);
  }

  function getVersionFromSelectedCriteria($checked) {
    $version = findDGUEVersion();
    global $pdo;
    if (!empty($checked)) {
      $checkVersion = $pdo->go("SELECT `version` FROM b_dgue_settings WHERE codice = :codice LIMIT 0,1",[":codice"=>$checked[0]])->fetch(PDO::FETCH_COLUMN);
      if (!empty($checkVersion)) {
        $version = $checkVersion;
      }
    }
    return $version;
  }

  function getDGUERequestedCriteria($riferimento,$sezione) {
    $checked = getSelectedCriteriaFromRequest($riferimento,$sezione);
    $version = getVersionFromSelectedCriteria($checked);
    $return = [];
    $allCriteria = getDGUECriteria($version);
    if (!empty($allCriteria)) {
      foreach($allCriteria AS $criteria) {
        if ($criteria["obbligatorio"] == "S" || in_array($criteria["codice"],$checked)) {
          $return[] = $criteria;
        }
      }
    }
    return $return;
  }
  function getDGUETranslateGruppi($version = null) {
    if (empty($version)) {
      $version = findDGUEVersion();
    }
    $dgue_translate_gruppi = array();
    $dgue_translate_gruppi["OTHER"]["it"] = "Parte II - Operatore Economico";
    $dgue_translate_gruppi["OTHER_FINAL"]["it"] = "Parte V - Riduzione del numero di candidati qualificati (Articolo 91 del codice)";
    $dgue_translate_gruppi["OTHER_FINAL"]["EO_DATA"]["it"] = $dgue_translate_gruppi["OTHER_FINAL"]["it"];
    $dgue_translate_gruppi["EXCLUSION"]["it"] = "Parte III - Motivi di esclusione (Articolo 80 del Codice)";
    $dgue_translate_gruppi["SELECTION"]["it"] = "Parte IV - Criteri di selezione";
    $dgue_translate_gruppi["OTHER"]["EO_DATA"]["it"] = "";
    $dgue_translate_gruppi["EXCLUSION"]["CONVICTIONS"]["it"] = "A: Motivi legati a condanne penali";
    $dgue_translate_gruppi["EXCLUSION"]["CONTRIBUTIONS"]["it"] = "B: Motivi legati al pagamento di imposte o contributi previdenziali";
    $dgue_translate_gruppi["EXCLUSION"]["SOCIAL"]["it"] = "C: Motivi legati a insolvenza, conflitti di interessi o illeciti professionali";
    $dgue_translate_gruppi["EXCLUSION"]["NATIONAL"]["it"] = "D: Motivi di esclusione previsti esclusivamente dalla legislazione nazionale";
    $dgue_translate_gruppi["SELECTION"]["ALL_SATISFIED"]["it"] = "<font face=\"symbol\">a</font>: Indicazione generale per tutti i criteri di selezione";
    $dgue_translate_gruppi["SELECTION"]["SUITABILITY"]["it"] = "A: Idoneit&agrave;";
    $dgue_translate_gruppi["SELECTION"]["ECONOMIC_FINANCIAL_STANDING"]["it"] = "B: Capacit&agrave; economica e finanziaria";
    $dgue_translate_gruppi["SELECTION"]["TECHNICAL_PROFESSIONAL_ABILITY"]["it"] = "C: Capacit&agrave; tecniche e professionali";
    if ($version == "2023-36") {
      $dgue_translate_gruppi["OTHER_FINAL"]["it"] = "Parte V - Riduzione del numero di candidati qualificati (Articolo 70 comma 6 del codice)";
      $dgue_translate_gruppi["EXCLUSION"]["it"] = "Parte III - Motivi di esclusione (Articoli da 94 a 98 del Codice)";
    }
    return $dgue_translate_gruppi;
  }
  
  $dgue_translate_gruppi = getDGUETranslateGruppi();

  $paesi = array();
  $paesi[""] = "";
  $paesi["AT"] = "Austria";
  $paesi["BE"] = "Belgio";
  $paesi["BG"] = "Bulgaria";
  $paesi["CY"] = "Cipro";
  $paesi["HR"] = "Croazia";
  $paesi["DK"] = "Danimarca";
  $paesi["EE"] = "Estonia";
  $paesi["FI"] = "Finlandia";
  $paesi["FR"] = "Francia";
  $paesi["DE"] = "Germania";
  $paesi["GR"] = "Grecia";
  $paesi["IE"] = "Irlanda";
  $paesi["IT"] = "Italia";
  $paesi["LV"] = "Lettonia";
  $paesi["LT"] = "Lituania";
  $paesi["LU"] = "Lussemburgo";
  $paesi["MT"] = "Malta";
  $paesi["NL"] = "Paesi Bassi";
  $paesi["PL"] = "Polonia";
  $paesi["PT"] = "Portogallo";
  $paesi["GB"] = "Regno Unito";
  $paesi["CZ"] = "Repubblica ceca";
  $paesi["RO"] = "Romania";
  $paesi["SK"] = "Slovacchia";
  $paesi["SI"] = "Slovenia";
  $paesi["ES"] = "Spagna";
  $paesi["SE"] = "Svezia";
  $paesi["HU"] = "Ungheria";
  $paesi["NO"] = "Norvegia";
  $paesi["CH"] = "Svizzera";

  $valute = array();
  $valute[""] = "";
  $valute["EUR"] = "EUR (Euro)";
  $valute["ALL"] = "ALL (Albanian lek)";
  $valute["AMD"] = "AMD (Armenian dram)";
  $valute["AZN"] = "AZN (Azerbaijani manat)";
  $valute["BAM"] = "BAM (Bosnian convertible mark)";
  $valute["BGN"] = "BGN (Bulgarian lev)";
  $valute["BYR"] = "BYR (Belarusian ruble)";
  $valute["CHF"] = "CHF (Swiss franc)";
  $valute["CZK"] = "CZK (Czech koruna)";
  $valute["DKK"] = "DKK (Danish krone)";
  $valute["GBP"] = "GBP (pound sterling)";
  $valute["GEL"] = "GEL (Georgian lari)";
  $valute["HRK"] = "HRK (Croatian kuna)";
  $valute["HUF"] = "HUF (Hungarian forint)";
  $valute["ISK"] = "ISK (Icelandic króna)";
  $valute["MDL"] = "MDL (Moldovan krone)";
  $valute["PLN"] = "PLN (Polish zloty)";
  $valute["RON"] = "RON (New Romanian leu)";
  $valute["RSD"] = "RSD (Serbian dinar)";
  $valute["RUB"] = "RUB (Russian ruble)";
  $valute["SEK"] = "SEK (Swedish krona)";
  $valute["TRY"] = "TRY (Turkish lira)";
  $valute["UAH"] = "UAH (Ukrainian hryvnia)";
  $valute["USD"] = "USD (US dollar)";

  function writeForm($id) {
    global $forms;
    global $root;
    global $dgue;
    global $dgue_translate_gruppi;
    $sub_group = "";
    foreach ($forms AS $form) {
      unset($values);
      if ($form["livello1"] == $id) {
        if ($form["livello2"] != $sub_group) {
          $sub_group = $form["livello2"];
          if (!empty($dgue_translate_gruppi[$form["livello1"]][$form["livello2"]]["it"])) {
            $sub_titolo = $dgue_translate_gruppi[$form["livello1"]][$form["livello2"]]["it"];
          ?>
          <h2 class="<?= $form["livello1"] ?> <?= $form["livello2"] ?>"><?= $sub_titolo ?></h2>
          <?
          }
        }
        if (!empty($form["template"]) && file_exists($root."/dgue/templates/".$form["template"]."/form.php")) {
        ?>
        <div class="box padding <?= $form["livello1"] ?> <?= $form["livello2"] ?>">
          <div class="dgue_label">
            <div class="padding">
              <strong><?= $form["nome"] ?></strong><br>
              <?= $form["descrizione"] ?>
            </div>
          </div>
          <div class="dgue_form">
            <div id="form_<?= $form["uuid"] ?>">
            <?
                $json = false;
                if (file_exists($root."/dgue/templates/".$form["template"]."/definition.json")) {
                  $json = file_get_contents($root."/dgue/templates/".$form["template"]."/definition.json");
                  $json = json_decode($json,true);
                  if (is_array($json)) {
                    $values = findValues(@$dgue["ccv:Criterion"][$form["uuid"]],$json);
                  }
                }
                include($root."/dgue/templates/".$form["template"]."/form.php");
            ?>
            </div>
            <? if ($form["ripeti"] == "S") {
              ?>
              <button style="font-size:14px;"
                class="aggiungi"
                onClick="aggiungi('/dgue/templates/<?= $form["template"] ?>/form.php','#form_<?= $form["uuid"] ?>');return false;">
                  <img src="/img/add.png" alt="Aggiungi elemento"> <strong>Aggiungi elemento</strong>
                </button>
              <?
            }
            ?>
          </div>
          <div class="clear"></div>
        </div>
        <?
        }
      }
    }
  }

  function showDGUE($id,$all_satisfied = false) {
    global $forms;
    global $root;
    global $dgue;
    global $styles;
    global $paesi;
    global $valute;
    global $dgue_translate_gruppi;
    $sub_group = "";

    foreach ($forms AS $form) {
      unset($values);
      if ($form["livello1"] == $id) {
        $print_row = true;
        $show_empty = false;
        if ($form["livello1"] == "SELECTION" && $form["livello2"] != "ALL_SATISFIED" && $all_satisfied) $print_row = false;
        if (empty($dgue["ccv:Criterion"][$form["uuid"]])) {
          $print_row = false;
        } else {
          if ($dgue["ccv:Criterion"][$form["uuid"]] === "show_empty") {
            $show_empty = true;
          }
        }
        if ($print_row && !empty($form["template"])) {
          if ($form["livello2"] != $sub_group) {
            $sub_group = $form["livello2"];
            if (!empty($dgue_translate_gruppi[$form["livello1"]][$form["livello2"]]["it"])) {
              $sub_titolo = $dgue_translate_gruppi[$form["livello1"]][$form["livello2"]]["it"];
            ?>
            <h2><?= $sub_titolo ?></h2>
            <?
            }
          }
          if (!empty($form["template"]) && file_exists($root."/dgue/templates/".$form["template"]."/show.php")) {
          ?>
          <table>
            <tr nobr="true">
              <th width="30%" style="<?= $styles["th"]; ?>">
                  <h3><?= $form["nome"] ?></h3>
                  <?= $form["descrizione"] ?>
              </th>
              <td width="70%">
                <?
                  $json = false;
                  if (file_exists($root."/dgue/templates/".$form["template"]."/definition.json")) {
                    $json = file_get_contents($root."/dgue/templates/".$form["template"]."/definition.json");
                    $json = json_decode($json,true);
                    if (is_array($json)) {
                      $values = findValues(@$dgue["ccv:Criterion"][$form["uuid"]],$json);
                    }
                  }
                  include($root."/dgue/templates/".$form["template"]."/show.php");
                ?>
              </td>
            </tr>
          </table>
          <?
          }
        }
      }
    }
  }

  function convertDate($array) {
    foreach($array AS $key => $value) {
      if (is_array($value)) {
        $array[$key] = convertDate($value);
      } else {
        if (stripos($key, "date") !== false) $array[$key] = date2mysql($value);
      }
    }
    return $array;
  }
  function removePersID($array) {
    foreach($array AS $key => $value) {
      if (is_array($value)) {
        if (!empty($value["cbc:ID"])) {
          $unset = false;
          if (is_array($value["cbc:ID"])) {
            foreach ($value["cbc:ID"] AS $valore) {
              if (strpos($valore,"PERS_")===0) $unset = true;
            }
          } else {
            if (strpos($value["cbc:ID"],"PERS_")===0) $unset = true;
          }
          if ($unset) {
            unset($array[$key]);
          } else {
            $array[$key] = removePersID($value);
          }
        } else {
          $array[$key] = removePersID($value);
        }
      }
    }
    return $array;
  }

  // ccv:Requirement

  function filterRequirement($array) {
    $requirements = array();
    if (is_array($array)) {
      foreach($array AS $chiave => $valore) {
        if (is_array($valore)) {
          if ($chiave == "ccv:Requirement") {
            if (empty($valore["cbc:ID"])) {
              $requirements = array_merge($requirements,$valore);
            } else {
              $requirements[] = $valore;
            }
          }
          $requirements = array_merge($requirements,filterRequirement($valore));
        }
      }
      return $requirements;
    }
  }

  function retrieveResponse($array,$search) {
    $requirements = filterRequirement($array);
    $return = false;
    if (count($requirements) > 0) {
      foreach($requirements AS $requirement) {
        if (!empty($requirement["cbc:ID"])) {
          $id = $requirement["cbc:ID"];
          if (!empty($id["$"])) $id = $id["$"];
          if ($id == $search) $return = $requirement;
        }
      }
    }
    return $return;
  }

  function findValues($array,$definition) {
    $values = "";
    foreach($definition AS $key => $object) {
      $values[$key] = array();
      foreach($object AS $sub_key => $settings) {
        $valore = "";
        if (!empty($settings["id"]) && !empty($settings["index"])) {
          $element = retrieveResponse($array,$settings["id"]);
          if (!empty($element["ccv:Response"]) && is_array($element["ccv:Response"])) {
            $response = $element["ccv:Response"];
            foreach($settings["index"] AS $indice) {
              if (!empty($response[$indice])) $response = $response[$indice];
            }
            if (!empty($response) && !is_array($response)) $valore = $response;
          }
        }
        $values[$key][$sub_key] = $valore;
      }
    }
    return $values;
  }
} ?>
