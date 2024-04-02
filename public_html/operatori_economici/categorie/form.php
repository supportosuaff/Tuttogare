<style type="text/css">
    .submit_cpv, .cerca_cpv {
        width: 100%;
        box-sizing: border-box;
        font-family: Tahoma, Geneva, sans-serif;
        font-size: 1em;
    }
</style>
<input type="hidden" name="cpv" id="cpv" value="<? echo $string_cpv ?>" title="Categorie merceologiche" rel="<?= (isset($obbligatorio)) ? $obbligatorio : "S" ?>;1;0;A">
<table style="width: 100%">
    <tr>
        <td style="width: 70%">
            <input type="text" class="cerca_cpv" rel="all" url="categorie/categoria.php" title="Cerca...">
        </td>
        <td>
            <input type="button" class="submit submit_cpv" value="Scegli da lista" onClick="visualizza_cpv_disponibili();return false">
        </td>
    </tr>
</table>
<div id="list_all" style="display:none">
	<?
    if ($_SESSION["language"] == "IT") {
      $sql_categorie = "SELECT b_cpv.*
                        FROM b_cpv
                        WHERE LENGTH(codice)= 2 ORDER BY codice";
    } else {
      $sql_categorie = "SELECT b_cpv.*, b_cpv_dict.{$_SESSION["language"]} AS descrizione
                        FROM b_cpv JOIN b_cpv_dict ON b_cpv.codice_completo = b_cpv_dict.codice_completo
                        WHERE LENGTH(b_cpv.codice)= 2 ORDER BY b_cpv.codice";
    }
    $ris_categorie = $pdo->query($sql_categorie);
    if ($ris_categorie->rowCount()>0) {
       $lista = "all";
       foreach ($ris_categorie as $rec_categorie) {
          include("categorie/categoria.php");
      }
  }
?>
</div>
<div class="box">
	<div id="list_in" style="height:auto !important;">
		<?
        if (isset($risultato_cpv) && count($risultato_cpv)>0) {
            foreach ($risultato_cpv as $rec_categorie) {
                $lista = "in";
                include("categorie/categoria.php");
            }
        }
      ?>
	</div>
</div>
<script>check_categorie();</script>
