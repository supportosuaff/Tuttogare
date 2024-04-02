<?
  if (isset($_POST["id"])) {
    include_once("../../../config.php");
    $bind = array();
    $bind[":codice"] = $_POST["id"];
    $strsql = "SELECT * FROM b_elenco_prezzi WHERE codice = :codice";
    $risultato = $pdo->bindAndExec($strsql,$bind);
    if ($risultato->rowCount()>0) {
      $prezzo = $risultato->fetch(PDO::FETCH_ASSOC);
    } else {
      $prezzo = get_campi("b_elenco_prezzi");
      $target = explode("_",$_POST["target"]);
      $prezzo["codice_lotto"] = (isset($target[2])) ? $target[2] : 0;
    }
    $id = $_POST["id"];
  }
  if (isset($prezzo)) {
    ?>
    <tr id="prezzo_<?= $id ?>" class="prezzo">
      <td width="10">
        <select rel="S;5;6;A" name="prezzo[<? echo $id ?>][tipo]" title="Tipo" id="tipo_elenco_prezzi_<? echo $id ?>">
          <option value="">Seleziona...</option>
          <option value="corpo">Corpo</option>
          <option value="misura">Misura</option>
        </select>
        <script>
          $("#tipo_elenco_prezzi_<? echo $id ?>").val('<? echo $prezzo["tipo"] ?>');
        </script>
      </td>
      <td>
        <input type="hidden" name="prezzo[<? echo $id ?>][codice]" id="codice_elenco_prezzi_<? echo $id ?>" value="<? echo $id ?>">
        <input type="hidden" name="prezzo[<? echo $id ?>][codice_lotto]" id="codice_lotto_elenco_prezzi_<? echo $id ?>" value="<? echo $prezzo["codice_lotto"] ?>">
        <input title="Descrizione" rel="S;1;255;A" class="titolo_edit" name="prezzo[<? echo $id ?>][descrizione]" id="descrizione_elenco_prezzi_<? echo $id ?>" value="<? echo $prezzo["descrizione"] ?>">
      </td>
      <td width="50">
        <input title="Unit&agrave;" rel="S;0;0;A" name="prezzo[<? echo $id ?>][unita]" id="unita_elenco_prezzi_<? echo $id ?>" value="<? echo $prezzo["unita"] ?>">
      </td>
      <td width="50">
        <input title="Quantit&agrave;" rel="S;0;0;N" name="prezzo[<? echo $id ?>][quantita]" id="quantita_elenco_prezzi_<? echo $id ?>" value="<? echo $prezzo["quantita"] ?>">
      </td>
      <td width="10">
      <input type="image" onClick="elimina('<? echo $id ?>','gare/elenco_prezzi');return false;" src="/img/del.png" title="Elimina">
      </td>
  </tr>
  <? } ?>
