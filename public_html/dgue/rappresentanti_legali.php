<?
  if (isset($_POST["id"])) {
    session_start();
    $id_rappresentante = $_SESSION["id_rappresentante"]+1;
  }
?>
<table id="rappresentante_<?= $id_rappresentante ?>" width="100%">
  <tr>
    <td class="etichetta">Nome</td>
    <td>
      <input title="Nome" type="text" rel="S;3;0;A" name="espd[espd-cac:EconomicOperatorParty][espd-cac:RepresentativeNaturalPerson][<?= $id_rappresentante ?>][cac:PowerOfAttorney][cac:AgentParty][cac:Person][cbc:FirstName]" id="espd-cac_EconomicOperatorParty_espd-cac_RepresentativeNaturalPerson_<?= $id_rappresentante ?>_cac_PowerOfAttorney_cac_AgentParty_cac_Person_cbc_FirstName" value="<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:FirstName"])) ? $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:FirstName"] : "" ?>" class="dgue_input">
    </td>
    <td class="etichetta">Cognome</td>
    <td>
      <input title="Cognome" type="text" rel="S;3;0;A" name="espd[espd-cac:EconomicOperatorParty][espd-cac:RepresentativeNaturalPerson][<?= $id_rappresentante ?>][cac:PowerOfAttorney][cac:AgentParty][cac:Person][cbc:FamilyName]" id="espd-cac_EconomicOperatorParty_espd-cac_RepresentativeNaturalPerson_<?= $id_rappresentante ?>_cac_PowerOfAttorney_cac_AgentParty_cac_Person_cbc_FamilyName" value="<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:FamilyName"])) ? $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:FamilyName"] : "" ?>" class="dgue_input">
    </td>
    <td width="10" rowspan="7" style="text-align:center"><input type="image" onClick="$('#rappresentante_<?= $id_rappresentante ?>').remove();" src="/img/del.png" title="Elimina"></td>
  </tr>
  <tr>
    <td class="etichetta">Data di nascita</td>
    <td>
      <input title="Data di nascita" type="text" rel="S;10;10;D" class="datepick" name="espd[espd-cac:EconomicOperatorParty][espd-cac:RepresentativeNaturalPerson][<?= $id_rappresentante ?>][cac:PowerOfAttorney][cac:AgentParty][cac:Person][cbc:BirthDate]" id="espd-cac_EconomicOperatorParty_espd-cac_RepresentativeNaturalPerson_<?= $id_rappresentante ?>_cac_PowerOfAttorney_cac_AgentParty_cac_Person_cbc_BirthDate" value="<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:BirthDate"])) ? mysql2date($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:BirthDate"]) : "" ?>" class="dgue_input">
    </td>
    <td class="etichetta">Luogo di nascita</td>
    <td>
      <input title="Luogo di Nascita" type="text" rel="S;3;0;A" name="espd[espd-cac:EconomicOperatorParty][espd-cac:RepresentativeNaturalPerson][<?= $id_rappresentante ?>][cac:PowerOfAttorney][cac:AgentParty][cac:Person][cbc:BirthplaceName]" id="espd-cac_EconomicOperatorParty_espd-cac_RepresentativeNaturalPerson_<?= $id_rappresentante ?>_cac_PowerOfAttorney_cac_AgentParty_cac_Person_cbc_BirthplaceName" value="<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:BirthplaceName"])) ? $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:BirthplaceName"] : "" ?>" class="dgue_input">
    </td>
  </tr>
  <tr>
    <td class="etichetta">Via e numero civico:</td>
    <td>
      <input type="text" title="Via e numero civico" rel="S;3;0;A" name="espd[espd-cac:EconomicOperatorParty][espd-cac:RepresentativeNaturalPerson][<?= $id_rappresentante ?>][cac:PowerOfAttorney][cac:AgentParty][cac:Person][cac:ResidenceAddress][cbc:StreetName]" id="espd-cac_EconomicOperatorParty_espd-cac_RepresentativeNaturalPerson_<?= $id_rappresentante ?>_cac_PowerOfAttorney_cac_AgentParty_cac_Person_cac_ResidenceAddress_cbc_StreetName" value="<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:StreetName"])) ? $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:StreetName"] : "" ?>" class="dgue_input">
    </td>
    <td class="etichetta">E-mail:</td>
    <td>
      <input title="E-mail" type="text" rel="S;3;0;E" name="espd[espd-cac:EconomicOperatorParty][espd-cac:RepresentativeNaturalPerson][<?= $id_rappresentante ?>][cac:PowerOfAttorney][cac:AgentParty][cac:Person][cac:Contact][cbc:ElectronicMail]" id="espd-cac_EconomicOperatorParty_espd-cac_RepresentativeNaturalPerson_<?= $id_rappresentante ?>_cac_PowerOfAttorney_cac_AgentParty_cac_Person_cac_Contact_cbc_ElectronicMail" value="<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:Contact"]["cbc:ElectronicMail"])) ? $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:Contact"]["cbc:ElectronicMail"] : "" ?>"  class="dgue_input">
    </td>
  </tr>

  <tr>
    <td class="etichetta">CAP</td>
    <td>
      <input title="CAP" type="text" rel="S;3;0;A" name="espd[espd-cac:EconomicOperatorParty][espd-cac:RepresentativeNaturalPerson][<?= $id_rappresentante ?>][cac:PowerOfAttorney][cac:AgentParty][cac:Person][cac:ResidenceAddress][cbc:Postbox]" id="espd-cac_EconomicOperatorParty_espd-cac_RepresentativeNaturalPerson_<?= $id_rappresentante ?>_cac_PowerOfAttorney_cac_AgentParty_cac_Person_cac_ResidenceAddress_cbc_Postbox" value="<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:Postbox"])) ? $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:Postbox"] : "" ?>" class="dgue_input">
    </td>
    <td class="etichetta">Telefono</td>
    <td>
      <input title="Telefono" type="text" rel="N;3;0;A" name="espd[espd-cac:EconomicOperatorParty][espd-cac:RepresentativeNaturalPerson][<?= $id_rappresentante ?>][cac:PowerOfAttorney][cac:AgentParty][cac:Person][cac:Contact][cbc:Telephone]" id="espd-cac_EconomicOperatorParty_espd-cac_RepresentativeNaturalPerson_<?= $id_rappresentante ?>_cac_PowerOfAttorney_cac_AgentParty_cac_Person_cac_Contact_cbc_Telephone" value="<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:Contact"]["cbc:Telephone"])) ? $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:Contact"]["cbc:Telephone"] : "" ?>" class="dgue_input">
    </td>
  </tr>
  <tr>
    <td class="etichetta">Citt&agrave;</td>
    <td>
      <input title="Citta" type="text" rel="S;3;0;A" name="espd[espd-cac:EconomicOperatorParty][espd-cac:RepresentativeNaturalPerson][<?= $id_rappresentante ?>][cac:PowerOfAttorney][cac:AgentParty][cac:Person][cac:ResidenceAddress][cbc:CityName]" id="espd-cac_EconomicOperatorParty_espd-cac_RepresentativeNaturalPerson_<?= $id_rappresentante ?>_cac_PowerOfAttorney_cac_AgentParty_cac_Person_cac_ResidenceAddress_cbc_CityName" value="<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:CityName"])) ? $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:CityName"] : "" ?>" class="dgue_input">
    </td>
    <td class="etichetta">Posizione/Titolo ad agire:</td>
    <td>
      <input title="Posizione" type="text" rel="S;3;0;A" name="espd[espd-cac:EconomicOperatorParty][espd-cac:RepresentativeNaturalPerson][<?= $id_rappresentante ?>][espd-cbc:NaturalPersonRoleDescription]" id="espd-cac_EconomicOperatorParty_espd-cac_RepresentativeNaturalPerson_<?= $id_rappresentante ?>_espd-cbc_NaturalPersonRoleDescription" value="<?= (!empty($rappresentante["espd-cbc:NaturalPersonRoleDescription"])) ? $rappresentante["espd-cbc:NaturalPersonRoleDescription"] : "" ?>" class="dgue_input">
    </td>
  </tr>
  <tr>
    <td class="etichetta">Paese:</td>
    <td colspan="3">
      <select title="Paese Rappresentante" rel="N;1;0;A"  name="espd[espd-cac:EconomicOperatorParty][espd-cac:RepresentativeNaturalPerson][<?= $id_rappresentante ?>][cac:PowerOfAttorney][cac:AgentParty][cac:Person][cac:ResidenceAddress][cac:Country][cbc:IdentificationCode][$]"
      id="espd-cac_EconomicOperatorParty_espd-cac_RepresentativeNaturalPerson_<?= $id_rappresentante ?>_cac_PowerOfAttorney_cac_AgentParty_cac_Person_cac_ResidenceAddress_cac_Country_cbc_IdentificationCode" class="dgue_input">
        <option value="" selected="selected">---</option>
        <optgroup label="EU">
          <option value="AT">Austria</option>
          <option value="BE">Belgio</option>
          <option value="BG">Bulgaria</option>
          <option value="CY">Cipro</option>
          <option value="HR">Croazia</option>
          <option value="DK">Danimarca</option>
          <option value="EE">Estonia</option>
          <option value="FI">Finlandia</option>
          <option value="FR">Francia</option>
          <option value="DE">Germania</option>
          <option value="GR">Grecia</option>
          <option value="IE">Irlanda</option>
          <option value="IT">Italia</option>
          <option value="LV">Lettonia</option>
          <option value="LT">Lituania</option>
          <option value="LU">Lussemburgo</option>
          <option value="MT">Malta</option>
          <option value="NL">Paesi Bassi</option>
          <option value="PL">Polonia</option>
          <option value="PT">Portogallo</option>
          <option value="GB">Regno Unito</option>
          <option value="CZ">Repubblica ceca</option>
          <option value="RO">Romania</option>
          <option value="SK">Slovacchia</option>
          <option value="SI">Slovenia</option>
          <option value="ES">Spagna</option>
          <option value="SE">Svezia</option>
          <option value="HU">Ungheria</option>
        </optgroup>
        <optgroup label="EFTA">
          <option value="NO">Norvegia</option>
          <option value="CH">Svizzera</option>
        </optgroup>
        </select>
        <? if (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cac:Country"]["cbc:IdentificationCode"]['$'])) {
          ?>
          <script>
            $("#espd-cac_EconomicOperatorParty_espd-cac_RepresentativeNaturalPerson_<?= $id_rappresentante ?>_cac_PowerOfAttorney_cac_AgentParty_cac_Person_cac_ResidenceAddress_cac_Country_cbc_IdentificationCode").val("<?= $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cac:Country"]["cbc:IdentificationCode"]['$'] ?>");
          </script>
          <?
        }
        ?>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="etichetta">
      Se necessario, fornire precisazioni sulla rappresentanza (forma, portata, scopo...):
    </td>
    <td colspan="2">
      <textarea rel="N;3;0;A" title="Precisazioni sulla rappresentanza" name="espd[espd-cac:EconomicOperatorParty][espd-cac:RepresentativeNaturalPerson][<?= $id_rappresentante ?>][cac:PowerOfAttorney][cbc:Description]" id="espd-cac_EconomicOperatorParty_espd-cac_RepresentativeNaturalPerson_<?= $id_rappresentante ?>_cac_PowerOfAttorney_cbc_Description" class="dgue_input " rows="3"><?= (!empty($rappresentante["cac:PowerOfAttorney"]["cbc:Description"])) ? $rappresentante["cac:PowerOfAttorney"]["cbc:Description"] : "" ?></textarea>
    </td>
  </tr>
</table>
<? $_SESSION["id_rappresentante"] = $id_rappresentante; ?>
