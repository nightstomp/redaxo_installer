<?php

// ADDON PARAMETER AUS URL HOLEN
////////////////////////////////////////////////////////////////////////////////
$myself    = rex_request('page'   , 'string');
$subpage   = rex_request('subpage', 'string');
$minorpage = rex_request('minorpage', 'string');
$func      = rex_request('func'   , 'string');

// ADDON RELEVANTES AUS $REX HOLEN
////////////////////////////////////////////////////////////////////////////////
$myREX = $REX['ADDON'][$myself];


// FORMULAR PARAMETER SPEICHERN
////////////////////////////////////////////////////////////////////////////////
if ($func == 'savesettings')
{
  $content = '';
  foreach($_GET as $key => $val)
  {
    if(!in_array($key,array('page','subpage','minorpage','func','submit','PHPSESSID')))
    {
      $myREX['settings'][$key] = $val;
      if(is_array($val))
      {
        $content .= '$REX["ADDON"]["'.$myself.'"]["settings"]["'.$key.'"] = '.var_export($val,true).';'."\n";
      }
      else
      {
        if(is_numeric($val))
        {
          $content .= '$REX["ADDON"]["'.$myself.'"]["settings"]["'.$key.'"] = '.$val.';'."\n";
        }
        else
        {
          $content .= '$REX["ADDON"]["'.$myself.'"]["settings"]["'.$key.'"] = \''.$val.'\';'."\n";
        }
      }
    }
  }

  $file = $REX['INCLUDE_PATH'].'/addons/'.$myself.'/config.inc.php';
  rex_replace_dynamic_contents($file, $content);
  echo rex_info('Einstellungen wurden gespeichert.');
}

// SELECT BOX
////////////////////////////////////////////////////////////////////////////////
$id = "display_legend";                                       // ID dieser Select Box
$tmp = new rex_select();                                      // rex_select Objekt initialisieren
$tmp->setSize(1);                                             // 1 Zeilen = normale Selectbox
$tmp->setName('SELECT['.$id.']');
$tmp->addOption('nein',0);                                    // Beschreibung ['string'], Wert [int|'string']
$tmp->addOption('ja',1);
$tmp->setSelected($myREX['settings']['SELECT'][$id]);         // gespeicherte Werte einsetzen
$select1 = $tmp->get();                                        // HTML in Variable speichern


$id = "nl2br_overview";                                                        // ID dieser Select Box
$tmp = new rex_select();                                      // rex_select Objekt initialisieren
$tmp->setSize(1);                                             // 1 Zeilen = normale Selectbox
$tmp->setName('SELECT['.$id.']');
$tmp->addOption('nein',0);                                    // Beschreibung ['string'], Wert [int|'string']
$tmp->addOption('ja',1);
$tmp->setSelected($myREX['settings']['SELECT'][$id]);         // gespeicherte Werte einsetzen
$select3 = $tmp->get();

$id = "nl2br_versions";                                       // ID dieser Select Box
$tmp = new rex_select();                                      // rex_select Objekt initialisieren
$tmp->setSize(1);                                             // 1 Zeilen = normale Selectbox
$tmp->setName('SELECT['.$id.']');
$tmp->addOption('nein',0);                                    // Beschreibung ['string'], Wert [int|'string']
$tmp->addOption('ja',1);
$tmp->setSelected($myREX['settings']['SELECT'][$id]);         // gespeicherte Werte einsetzen
$select4 = $tmp->get();

$id = "linkconvert_overview";                                                         // ID dieser Select Box
$tmp = new rex_select();                                      // rex_select Objekt initialisieren
$tmp->setSize(1);                                             // 1 Zeilen = normale Selectbox
$tmp->setName('SELECT['.$id.']');
$tmp->addOption('nein',0);                                    // Beschreibung ['string'], Wert [int|'string']
$tmp->addOption('ja',1);
$tmp->setSelected($myREX['settings']['SELECT'][$id]);         // gespeicherte Werte einsetzen
$select5 = $tmp->get();

$id = "linkconvert_versions";                                 // ID dieser Select Box
$tmp = new rex_select();                                      // rex_select Objekt initialisieren
$tmp->setSize(1);                                             // 1 Zeilen = normale Selectbox
$tmp->setName('SELECT['.$id.']');
$tmp->addOption('nein',0);                                    // Beschreibung ['string'], Wert [int|'string']
$tmp->addOption('ja',1);
$tmp->setSelected($myREX['settings']['SELECT'][$id]);         // gespeicherte Werte einsetzen
$select6 = $tmp->get();

$id = "overwrite_zip_packages";                                 // ID dieser Select Box
$tmp = new rex_select();                                      // rex_select Objekt initialisieren
$tmp->setSize(1);                                             // 1 Zeilen = normale Selectbox
$tmp->setName('SELECT['.$id.']');
$tmp->addOption('nein',0);                                    // Beschreibung ['string'], Wert [int|'string']
$tmp->addOption('ja',1);
$tmp->setSelected($myREX['settings']['SELECT'][$id]);         // gespeicherte Werte einsetzen
$select7 = $tmp->get();


$id = "display_zip_packages";                                 // ID dieser Select Box
$tmp = new rex_select();                                      // rex_select Objekt initialisieren
$tmp->setSize(1);                                             // 1 Zeilen = normale Selectbox
$tmp->setName('SELECT['.$id.']');
$tmp->addOption('nein',0);                                    // Beschreibung ['string'], Wert [int|'string']
$tmp->addOption('ja',1);
$tmp->setSelected($myREX['settings']['SELECT'][$id]);         // gespeicherte Werte einsetzen
$select8 = $tmp->get();

// MULTISELECT BOX
////////////////////////////////////////////////////////////////////////////////
$id = 1;                                                      // ID dieser MultiSelect Box
$tmp = new rex_select();                                      // rex_select Objekt initialisieren
$tmp->setSize(4);                                             // angezeigte Zeilen, Rest wird gescrollt
$tmp->setMultiple(true);
$tmp->setName('MULTISELECT['.$id.'][]');                      // abschließendes [] wichtig!
$tmp->addOption('rot',0);                                     // Beschreibung ['string'], Wert [int|'string']
$tmp->addOption('grün',1);
$tmp->addOption('blau','blau');
if(isset($myREX['settings']['MULTISELECT'][$id]))             // evtl. keine Werte -> prüfen ob was gespeichert
{
  $tmp->setSelected($myREX['settings']['MULTISELECT'][$id]);  // gespeicherte Werte einsetzen
}
$multiselect = $tmp->get();                                   // HTML in Variable speichern



echo '
<div class="rex-addon-output">
  <div class="rex-form">

  <form action="index.php" method="get" id="settings">
    <input type="hidden" name="page" value="'.$myself.'" />
    <input type="hidden" name="subpage" value="'.$subpage.'" />
    <input type="hidden" name="func" value="savesettings" />

        <fieldset class="rex-form-col-1">
          <legend>Einstellungen für Installer</legend>
          <div class="rex-form-wrapper">

            <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-select">
                <label for="select">Legende auf der Startseite anzeigen?</label>
                '.$select1.'
              </p>
            </div><!-- .rex-form-row -->

      <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-select">
                <label for="select">Zeilenumbrüche in Beschreibungstext (Übersicht)?</label>
                '.$select3.'
              </p>
            </div><!-- .rex-form-row -->

      <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-select">
                <label for="select">Zeilenumbrüche in Beschreibungstext (Versionen)?</label>
                '.$select4.'
              </p>
            </div><!-- .rex-form-row -->

      <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-select">
                <label for="select">Links im Beschreibungstext umwandeln? (Übersicht)</label>
                '.$select5.'
              </p>
            </div><!-- .rex-form-row -->

      <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-select">
                <label for="select">Links im Beschreibungstext umwandeln? (Versionen)</label>
                '.$select6.'
              </p>
            </div><!-- .rex-form-row -->
            <legend>ZIP Installer</legend>
            <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-select">
                <label for="select">AddOns überschreiben vorselektiert?</label>
                '.$select7.'
              </p>
            </div><!-- .rex-form-row -->

            <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-select">
                <label for="select">Paketinhalte anzeigen?</label>
                '.$select8.'
              </p>
            </div><!-- .rex-form-row -->

      <div class="rex-form-row rex-form-element-v2">
              <p class="rex-form-submit">
                <input class="rex-form-submit" type="submit" id="submit" name="submit" value="Einstellungen speichern" />
              </p>
            </div><!-- .rex-form-row -->

          </div><!-- .rex-form-wrapper -->
        </fieldset>
  </form>

  </div><!-- .rex-form -->
</div><!-- .rex-addon-output -->
';
