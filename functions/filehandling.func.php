<?php

// Lädt das Addon von Redaxo.de auf den Server und speichert diesen im Temp-Ordner ab
function Installer_download_file_from_redaxo($file, $path)
{
  $file_output = '';

  try {
    $socket = rex_installer_socket::createByUrl($file);
    $socket->doGet();
    $res = fopen($path, 'w');
    $file_output = $socket->writeBodyTo($res);

  } catch (rex_installer_socket_exception $e) {
      // fehlermeldung:
       echo rex_warning($e->getMessage());
  }


  // Prüfen, ob Daten empfangen wurden, wenn nicht, false, ansonsten true
  if ($file_output)
  {
    return true;

  } else {

    return false;
  }
}

// Prüft, ob ein Addon bereits existiert
// und gibt Rückgabewert true or false zurück
function Installer_check_if_addon_exists($installname)
{
  if(is_dir($installname))
  {
    return true;
  } else {
    return false;
  }
}
