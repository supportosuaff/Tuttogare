<?
    if (isset($_POST["id"])) {
        session_start();
        include("../../../config.php");
        include_once($root."/inc/funzioni.php");
;
        $step = get_campi("r_step_valutazione");
        $id_step = $_POST["id"];
        $step["codice_criterio"] = str_replace("#list_step_","",$_POST["target"]);
    }

?>          <tr>
              <td style="text-align:center">
                <input type="hidden" name="step_valutazione[<? echo $id_step ?>][codice_criterio]" id="codice_criterio_step_valutazione_<? echo $id_step ?>" value="<? echo $step["codice_criterio"] ?>">
                <input type="text" name="step_valutazione[<? echo $id_step ?>][minimo]" size="10" title="Mimino" id="minimo_step_valutazione_<? echo $id_step ?>" value="<? echo $step["minimo"] ?>" rel="S;0;0;N">
              </td>
              <td style="text-align:center">
                <input type="text" name="step_valutazione[<? echo $id_step ?>][massimo]" size="10" title="Massimo" id="massimo_step_valutazione_<? echo $id_step ?>" value="<? echo $step["massimo"] ?>" rel="S;0;0;N">
              </td>
              <td style="text-align:center">
                <input type="text" name="step_valutazione[<? echo $id_step ?>][punteggio]" size="10" title="Punteggio" id="punteggio_step_valutazione_<? echo $id_step ?>" value="<? echo $step["punteggio"] ?>" rel="S;0;0;N">
              </td>
              <td style="text-align:center">
                <input type="image" onClick="$(this).parents('tr').first().remove();check_steps(<?= $step["codice_criterio"] ?>);" src="/img/del.png" title="Elimina">
              </td>
            </tr>
