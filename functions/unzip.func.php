<?php

// Funktion zum Entpacken des ZIP-Pakets von Redaxo.de
// Das File wurde zuvor im TEMP-Verzeichnis von Redaxo.de gespeichert
// Ruft verschiedene Funktionen bzgl. Sicherheit auf.
function Installer_unzip_file_to_addon_dir($file, $includeDir, $installname)
{
  // Zip Klasse laden
  $zip = new dUnzip2($includeDir."/".$file);
  //$zip->debug = 1; // debug?

  // Inhalt vom ZIP-FILE laden. Liefert ARRAY zurück
  $zipList = $zip->getList();


  // Ruft die Sicherheitsfunktion auf, um zu prüfen, ob es sich
  // um ein richtiges AddOn handelt, wenn ja, true, ansonsten false
  if(Installer_check_if_file_is_addon($zipList, $installname))
  {
    // Inhalt vom ZIP-File im AddOn-Verzeichnis entpacken
    $zip->unzipAll($includeDir. '/addons/');

    // Installation säubern (Temp Daten löschen, versteckte OSX-Daten etc.)
    Installer_clean_addon_setup($includeDir, $file);

    // File-Handler schließen und memory freigeben
    $zip->__destroy();

    // Alles ok, weiter gehts!
    return true;

  } else {
    // File-Handler schließen und memory freigeben
    $zip->__destroy();

    // Check fehlgeschlagen - false ausgeben
    return false;
  }

}


// Prüft AddOn auf Unbenklichkeit (Fast-Mode)
function Installer_fast_file_check($file, $installname, $includeDir)
{
  // Prüfen, ob das Ergebnis nicht bereits in einer Session steht
  // um Dauerverbindungen zu Redaxo.de zu vermeiden.
  if(isset($_SESSION['fastcheck'][$file]))
  {
    // Session exisitert - um overhead zu sparen, wird das Ergebnis der
    // letzten Session ausgegeben
    return $_SESSION['fastcheck'][$file];

  } else { // Session besteht noch nicht, also First-Run OK!

    // Pfad zum TEMP-Verzeichnis
    // TODO: Variablen ersetzen!
    $path = $includeDir."/addons/installer/temp/".md5($file).".zip";

          $file_output = '';

          try {
              $socket = rex_installer_socket::createByUrl($file);
              $socket->doGet();
              $res = fopen($path, 'w');
              $file_output = $socket->writeBodyTo($res);

          } catch (rex_installer_socket_exception $e) {
              // fehlermeldung:
              echo rex_warning($e->getMessage());
              return FALSE;
          }

          if(!$file_output) {
              echo rex_warning("Das AddOn konnte aufgrund von Serverproblemen nicht überprüft werden! Fast-Check Ergebnisse sind deswegen fehlerhaft (Alle fallen durch, obwohl das AddOn in Ordnung sein könnte). Korrete AddOns lassen sich normalerweise dennoch ohne Probleme installieren. Eine zweite Sicherheitsroutine wird versuchen die Sicherheit weiterhin zu gewährleisten. Für weitere Versuche klicke auf das Plus-Symbol");
              return FALSE;
          }

    // Zip Klasse laden
    $zip = new dUnzip2($path);

    // Inhalt vom ZIP-FILE laden. Liefert ARRAY zurück
    $zipList = $zip->getList();

    // Ruft die Sicherheitsfunktion auf, um zu prüfen, ob es sich
    // um ein richtiges AddOn handelt, wenn ja, true, ansonsten false
    if(Installer_check_if_file_is_addon($zipList, $installname))
    {

      $zip->__destroy();  // fclose && memory free
      @unlink($path);    // Datei löschen

      // Um ständige Verbindungen zu Redaxo.de zu unterbinden,
      // wird das Ergebnis in eine Session geschrieben
      $_SESSION['fastcheck'][$file] = true;

      return true;

    } else {

      $zip->__destroy();  // fclose && memory free
      @unlink($path);    // Datei löschen

      // Um ständige Verbindungen zu Redaxo.de zu unterbinden,
      // wird das Ergebnis in eine Session geschrieben
      $_SESSION['fastcheck'][$file] = false;

      return false;
    }
  }
}


// Funktion, welche überprüft, ob es sich hierbei um ein Addon handelt
// Es wird geprüft, ob Dateien wie confic.inc.php, istall.inc.php existieren
// und ob der erste Rückgabewert aus dem ZIP ein Verzeichnis ist.
function Installer_check_if_file_is_addon($zipList, $installname)
{
  $returnValue = '';
  $i = 1; // ja, $i ist 1 :)
  foreach($zipList as $el)
  {
    // Wir brauchen nur das erste Element
    if($i == 1)
    {
      // Prüft, ob der erste Rückgabewert (im Normalfall Ordner des Addon)
      // mit dem AddOn-Key, welcher auf Redaxo.de eingetragen wurde, übereinstimmt
      if(trim($el['file_name']) != $installname."/")
      {
        // Falsch - Check bricht an dieser Stelle ab!
        // Wir wollen jedoch noch einen weiteren Check machen, manchmal liefert
        // die ZipList ein falsches ergebnis
        if(preg_match('/^'.$installname.'/', $el['file_name'])){
            $returnValue = true;
        } else {
            $returnValue = true;
        }

      } else {
        $returnValue = true;
        // Jo, alles gut :)
      }

    } else {

      // Alles danach ist unwichtig - break!
      break;
    }

    $i++;
  } // end foreach

  if($returnValue) {
    // Prüft, ob wichtige Dateien, welche von einem Addon benötigt werden, exisitieren
    // Wenn nicht, handelt es sich um ein AddOn, sondern eventuell um einen Path,
    // oder eine Modifikation. Diese Daten können nicht vom Installer genutzt werden
    if(Installer_array_search_key($installname.'/config.inc.php',$zipList) == false OR
      Installer_array_search_key($installname.'/install.inc.php',$zipList) == false OR
      Installer_array_search_key($installname.'/uninstall.inc.php',$zipList) == false)
    {
      // Fehler, false zurück
      $returnValue = false;

    } else {
      $returnValue = true;
      // Jo, alles gut :)
    }

    // Wenn die Funktion nicht vorher schon durch ein return false abgebrochen wurde,
    // waren die Checks erfolgreich :) - Dann darf das AddOn auch installiert werden
    return $returnValue;
  }
}

// Funktion um die Installation zu reinigen
// Löscht die Temp-Files und von OSX und Windows angelegte unsichtbare Dateien
// welche sich gerne in ZIP-Files einschleichen
// TODO: SVN-Schrott löschen
function Installer_clean_addon_setup($includeDir, $file = false)
{
    if($file){
        // Temporäre ZIP-Datei nach dem Entpacken wieder löschen!
      unlink($includeDir."/".$file);
    }

  // Prüft, ob Versteckte Verzeichnisse von MACOSX im ZIP abgelegt worden sind
  // TODO: Windows thumbs.db ebenfalls löschen
  // TODO: SVN Dateien löschen
  // Dafür muss eine rekrusive Funktion geschrieben werden

  if(is_dir($includeDir. '/addons/__MACOSX'))
  {
    // Wenn Mac-Ordner existieren, werden diese gelöscht.
    // Rufe Redaxos deleteDir Funktion auf
    rex_deleteDir($includeDir. '/addons/__MACOSX', true);

  } else{
    // return
    return false;
  }
}
