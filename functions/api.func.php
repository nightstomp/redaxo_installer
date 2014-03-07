<?php
	
	// Stellt Verbindung zu REDAXO.de auf und lädt Rückgabe der API
	// Übermittelt einen ISO-Kodierten json-string zurück
	// Damit das Script funktionsfähig ist, muss es utf-8 encoded werden
	// $pathToApi ist in der config.inc.php definiert
	function Installer_getAddons($pathToApi)
	{
    
        try {
            $socket = rex_installer_socket::createByUrl($pathToApi);
            $socket->doGet();
            $file_output = $socket->getBody();
            
        } catch (rex_installer_socket_exception $e) {
            // fehlermeldung:
             echo rex_warning($e->getMessage());
        }
        
		if($file_output) {
			$addons = json_decode(utf8_encode($file_output));
			return $addons;
		} else {
			return FALSE;
		}
	}

?>
