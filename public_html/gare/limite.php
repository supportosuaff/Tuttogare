<?php

    include_once("../../config.php");
    include_once("{$root}/layout/top.php");

    if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
        if(! is_operatore()) {
            ?>
            <h1>LIMITE PER LE PROCEDURE DI GARA</h1>
            <h3 class="ui-state-error">
                E' stato raggiuto il limite massimo di procedure gestibili dalla piattaforma.
                <br><strong>Si prega di contattare il servizio di HelpDesk.</strong>
            </h3>
            <?
        }
    }

    include_once("{$root}/layout/bottom.php");

?>