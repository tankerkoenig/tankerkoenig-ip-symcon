<?
/*** TANKERKOENIG.DE (http://www.tankerkoenig.de) ***
*** Script v1.3 by Bayaro ***

https://www.symcon.de/forum/threads/28346-Tankerkoenig-de-%28Spritpreise-mit-Umkreissuche-oder-Detailabfrage%29

Ihr müsst euch nur einen API-Key per E-Mail zuschicken lassen,
dann die entsprechenden Konfigurationen hier am Anfang
des Skriptes eintragen und dann das Skript einmal ausführen.
Es werden, unterhalb des Skriptes dann Kategorien und
Dummy-Module angelegt, in welchen die entsprechenden Daten
der jeweiligen Tankstelle(n) abgespeichert werden.

API Key hier anfordern (kommt sofort per E-Mail):
https://creativecommons.tankerkoenig.de/#register

Dokumentation zur API befindet sich hier:
https://creativecommons.tankerkoenig.de/#techInfo

API Daten stehen unter der Creative-Commons-Lizenz “CC BY 4.0”
https://creativecommons.org/licenses/by/4.0/deed.de
*/


$APIkey = "****************";  // API Key, den ihr per E-Mail bekommen habt )
$UpdateIntervall = 15;  // Abfrageintervall in Minuten
$Logging = true;           // Aktiviert beim 1. Anlegen der Variablen auch direkt das Logging (nur für die Preise)
$NamenMitID = false;   // Wird gebraucht, wenn man 2 Tankstellen mit dem selben Namen im selben Ort hat


/***** KONFIGURATION FÜR DIE UMKREISSUCHE ********************************************************************/
$lat = 50.26;      // Latitude
$lng = 8.96;       // Longitude
$radius = 5;       // Radius in Kilometern (max. 25km Umkreis)
$sort = "dist";     // Sortieren nach Preis oder Distanz (price, dist)
$type = "diesel";  // Spritsorte (e5, e10, diesel)
/*************************************************************************************************************/
Tankerkoenig_Umkreissuche($APIkey, $lat, $lng, $radius, $sort, $type);  // Auskommentieren, wenn nicht gewünscht


/***** KONFIGURATION FÜR DIE DETAILABFRAGE *******************************************************************/
// ID(s) eurer Tankstelle(n) eintragen (z.B. über Umkreissuche auslesen lassen)
// Diese 3 Eintrage sind nur als Beispiele gedacht. Ihr könnt 1 oder mehr solcher Einträge/Zeilen anlegen.
$Tankstellen[] = "c596bf7e-7845-4b1d-9755-d339692560b0";
$Tankstellen[] = "c1b456c8-b782-41d8-a960-466c4088a463";
$Tankstellen[] = "5ff6dfba-0932-4407-ac6e-9c5b96324005";
/*************************************************************************************************************/
Tankerkoenig_Detailabfrage($APIkey, $Tankstellen);  // Auskommentieren, wenn nicht gewünscht






/******** AB HIER NICHTS MEHR ÄNDERN ********/
IPS_SetScriptTimer($_IPS['SELF'], $UpdateIntervall * 60);


function Tankerkoenig_Umkreissuche($APIkey, $lat, $lng, $radius, $sort, $type) {
      Global $NamenMitID;
    $KategorieID_Umreissuche = Kategorie_GetOrSet("Umkreissuche", $_IPS['SELF']);
    
    // API abfragen und dekodieren
    $json = SYS_GetURLContent("https://creativecommons.tankerkoenig.de/json/list.php?lat=$lat&lng=$lng&rad=$radius&sort=$sort&type=$type&apikey=$APIkey");
    $data = json_decode($json);
    print_r($data);

    // Daten der Tankstellen in Array speichern
    $TankstellenAR = $data->stations;

    // Daten der Tankstellen aus Array auslesen
    $i = 0;
    foreach ($TankstellenAR as $TankstelleAR) {
        $TankstelleName = utf8_decode($TankstellenAR[$i]->name);
        $TankstelleMarke = utf8_decode($TankstellenAR[$i]->brand);
        $TankstelleDistanz = (float)utf8_decode($TankstellenAR[$i]->dist);
        $TankstellePreis = (float)utf8_decode($TankstellenAR[$i]->price);
        $TankstelleID = utf8_decode($TankstellenAR[$i]->id);
        $TankstelleStrasse = utf8_decode($TankstellenAR[$i]->street);
        $TankstelleHausnummer = utf8_decode($TankstellenAR[$i]->houseNumber);
        $TankstellePLZ = utf8_decode($TankstellenAR[$i]->postCode);
        $TankstelleOrt = utf8_decode($TankstellenAR[$i]->place);
        $TankstelleAnschrift = $TankstellePLZ." ".$TankstelleOrt.", ".$TankstelleStrasse." ".$TankstelleHausnummer;
        
        if ($NamenMitID == true) {
            $DummyName = utf8_decode($TankstellenAR[$i]->brand)."-".utf8_decode($TankstellenAR[$i]->place)."_".substr($TankstelleID, -5);
        }
        else {
           $DummyName = utf8_decode($TankstellenAR[$i]->brand)."-".utf8_decode($TankstellenAR[$i]->place);
        }
        $DummyTankstelle = Dummy_GetOrSet($DummyName, $KategorieID_Umreissuche);
        Variable_GetOrSet("Marke", $DummyTankstelle, $TankstelleMarke);
        Variable_GetOrSet("Distanz", $DummyTankstelle, $TankstelleDistanz);
        Variable_GetOrSet("Preis", $DummyTankstelle, $TankstellePreis);
        Variable_GetOrSet("ID", $DummyTankstelle, $TankstelleID);
        Variable_GetOrSet("Anschrift", $DummyTankstelle, $TankstelleAnschrift);
        $i++;
    }
}



function Tankerkoenig_Detailabfrage($APIkey, $TankstellenAR) {
    Global $NamenMitID;
    $KategorieID_Detailabfrage = Kategorie_GetOrSet("Detailabfrage", $_IPS['SELF']);

    // Daten der Tankstelle(n) auslesen
    foreach ($TankstellenAR as $TankstelleID) {
       $json = SYS_GetURLContent("https://creativecommons.tankerkoenig.de/json/detail.php?id=$TankstelleID&apikey=$APIkey");
        $Tankstelle = json_decode($json);
        $TankstelleName = utf8_decode($Tankstelle->station->name);
        $TankstelleMarke = utf8_decode($Tankstelle->station->brand);
        $TankstellePreisE5 = (float)utf8_decode($Tankstelle->station->e5);
        $TankstellePreisE10 = (float)utf8_decode($Tankstelle->station->e10);
        $TankstellePreisDIESEL = (float)utf8_decode($Tankstelle->station->diesel);
        $TankstelleGeoffnet = (boolean)utf8_decode($Tankstelle->station->isOpen);
        $TankstelleGeoffnetVon = utf8_decode($Tankstelle->station->openingTimes[0]->start);
        $TankstelleGeoffnetBis = utf8_decode($Tankstelle->station->openingTimes[0]->end);
        $TankstelleID = utf8_decode($Tankstelle->station->id);
        $TankstelleStrasse = utf8_decode($Tankstelle->station->street);
        $TankstelleHausnummer = utf8_decode($Tankstelle->station->houseNumber);
        $TankstellePLZ = utf8_decode($Tankstelle->station->postCode);
        $TankstelleOrt = utf8_decode($Tankstelle->station->place);
        $TankstelleAnschrift = $TankstellePLZ." ".$TankstelleOrt.", ".$TankstelleStrasse." ".$TankstelleHausnummer;
        
        if ($NamenMitID == true) {
            $DummyName = utf8_decode($Tankstelle->station->brand)."-".utf8_decode($Tankstelle->station->place)."_".substr($TankstelleID, -5);
        }
        else {
           $DummyName = utf8_decode($Tankstelle->station->brand)."-".utf8_decode($Tankstelle->station->place);
        }
        $DummyTankstelle = Dummy_GetOrSet($DummyName, $KategorieID_Detailabfrage);
        Variable_GetOrSet("Marke", $DummyTankstelle, $TankstelleMarke);
        Variable_GetOrSet("Preis_E5", $DummyTankstelle, $TankstellePreisE5);
        Variable_GetOrSet("Preis_E10", $DummyTankstelle, $TankstellePreisE10);
        Variable_GetOrSet("Preis_Diesel", $DummyTankstelle, $TankstellePreisDIESEL);
        Variable_GetOrSet("Geöffnet", $DummyTankstelle, $TankstelleGeoffnet);
        Variable_GetOrSet("Geöffnet_von", $DummyTankstelle, $TankstelleGeoffnetVon);
        Variable_GetOrSet("Geöffnet_bis", $DummyTankstelle, $TankstelleGeoffnetBis);
        Variable_GetOrSet("ID", $DummyTankstelle, $TankstelleID);
        Variable_GetOrSet("Anschrift", $DummyTankstelle, $TankstelleAnschrift);
    }
}



function Kategorie_GetOrSet($Kategorie_Name, $ParentID) {
    $Kategorie_ID = @IPS_GetCategoryIDByName($Kategorie_Name, $ParentID);
    if ($Kategorie_ID === false) {
       $Kategorie_ID = IPS_CreateCategory();
        IPS_SetName($Kategorie_ID, $Kategorie_Name);
        IPS_SetParent($Kategorie_ID, $ParentID);
    }
    return $Kategorie_ID;
}


function Dummy_GetOrSet($name, $parent) {
    $DummyID = @IPS_GetObjectIDByName($name, $parent);
    if (!$DummyID) {
       $DummyID = IPS_CreateInstance("{485D0419-BE97-4548-AA9C-C083EB82E61E}");
        IPS_SetParent($DummyID, $parent);
        IPS_SetName($DummyID, $name);
    }
    return $DummyID;
}


function Variable_GetOrSet($name, $parent, $value) {
    global $Logging;
    $VarID = @IPS_GetVariableIDByName($name, $parent);
    if (!$VarID) {
      if (($name == "Name") OR ($name == "Marke") OR ($name == "ID") OR ($name == "Anschrift")) {
            $VarID = IPS_CreateVariable(3);
        }
        elseif (($name == "Geöffnet_von") OR ($name == "Geöffnet_bis")) {
            $VarID = IPS_CreateVariable(3);
            $ProfilName = "GeoffnetVonBis_Tankstelle_TK";
            if (!IPS_VariableProfileExists($ProfilName)) {
               IPS_CreateVariableProfile($ProfilName, 3);
            IPS_SetVariableProfileText($ProfilName, "", " Uhr");
            IPS_SetVariableProfileIcon($ProfilName,  "Clock");
            }
            IPS_SetVariableCustomProfile($VarID, $ProfilName);
        }
        elseif ($name == "Geöffnet") {
            $VarID = IPS_CreateVariable(0);
         $ProfilName = "Geoeffnet_Tankstelle_TK";
            if (!IPS_VariableProfileExists($ProfilName)) {
               IPS_CreateVariableProfile($ProfilName, 0);
            IPS_SetVariableProfileAssociation($ProfilName, 0, "Geschlossen", "", -1);
                IPS_SetVariableProfileAssociation($ProfilName, 1, "Geöffnet", "", -1);
            IPS_SetVariableProfileIcon($ProfilName,  "Information");
            }
            IPS_SetVariableCustomProfile($VarID, $ProfilName);
        }
        elseif ($name == "Distanz") {
            $VarID = IPS_CreateVariable(2);
         $ProfilName = "Distanz_Tankstelle_TK";
            if (!IPS_VariableProfileExists($ProfilName)) {
               IPS_CreateVariableProfile($ProfilName, 2);
            IPS_SetVariableProfileText($ProfilName, "", "km");
            IPS_SetVariableProfileDigits($ProfilName, 1);
            IPS_SetVariableProfileIcon($ProfilName,  "Distance");
            }
            IPS_SetVariableCustomProfile($VarID, $ProfilName);
        }
        elseif (($name == "Preis") OR ($name == "Preis_E5") OR ($name == "Preis_E10") OR ($name == "Preis_Diesel")) {
           if ($value === NULL) {
               return;
           }
            $VarID = IPS_CreateVariable(2);
            $ProfilName = "Euro_Tankstelle_TK";
            if (!IPS_VariableProfileExists($ProfilName)) {
               IPS_CreateVariableProfile($ProfilName, 2);
            IPS_SetVariableProfileText($ProfilName, "", "€");
            IPS_SetVariableProfileDigits($ProfilName, 3);
            IPS_SetVariableProfileIcon($ProfilName,  "Euro");
            }
            IPS_SetVariableCustomProfile($VarID, $ProfilName);
         if ($Logging) {
                $ArchiveHandlerID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
                AC_SetLoggingStatus($ArchiveHandlerID, $VarID, true);
                IPS_ApplyChanges($ArchiveHandlerID);
            }
        }
        IPS_SetName($VarID, $name);
        IPS_SetParent($VarID, $parent);
    }
    SetValue($VarID, $value);
}
?>
