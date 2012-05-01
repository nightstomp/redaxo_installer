<?php

  // Name
  $addonname = 'installer';
  // AUTOINSTALL THESE PLUGINS
  $autoinstall = array('addon_installer','modul_installer', 'zip_installer');
  $msg = '';
  
	$REX['ADDON']['install']['installer'] = true;
	
	if (!extension_loaded('zLib')) {
	    if (!dl('zlib.so')) {
	        $REX['ADDON']['install']['installer'] = 0;
			$REX['ADDON']['installmsg']['installer'] = "Dieses Addon benötigt die zLib und kann ohne nicht installiert werden";
	    }
	} else {
	}
	
	if (version_compare(PHP_VERSION, '5.0.0', '>=')) {
	} else {
		$REX['ADDON']['install']['installer'] = 0;
		$REX['ADDON']['installmsg']['installer'] = "Dieses Addon benötigt mindestens PHP 5.0.0 (5.3.0 empfohlen) , auf diesem System ist jedoch ".PHP_VERSION." installiert.";
	}
    	

	if (!rex_is_writable(dirname(__FILE__).'/temp/')) {
		$REX['ADDON']['install']['installer'] = 0;
		$REX['ADDON']['installmsg']['installer'] = "Das Verzeichnis /temp/ im Addonverzeichnis hat keine Schreibrechte. Bitte wechsle ins Installer-Verzeichnis und setze die Schreibrechte für das Verzeichnis auf CHMOD 777";
	}

  // GET ALL ADDONS & PLUGINS
  $all_addons = rex_read_addons_folder();
  $all_plugins = array();
  foreach($all_addons as $_addon) {
    $all_plugins[$_addon] = rex_read_plugins_folder($_addon);
  }

  // DO AUTOINSTALL
  $pluginManager = new rex_pluginManager($all_plugins, $addonname);
  foreach($autoinstall as $pluginname) {
    // INSTALL PLUGIN
    if(($instErr = $pluginManager->install($pluginname)) !== true)
    {
      $msg = $instErr;
    }

    // ACTIVATE PLUGIN
    if ($msg == '' && ($actErr = $pluginManager->activate($pluginname)) !== true)
    {
      $msg = $actErr;
    }

    if($msg != '')
    {
      break;
    }
  }
	
	
	$source_dir		= $REX['INCLUDE_PATH'] . '/addons/installer/media';
	$dest_dir		= $REX['HTDOCS_PATH'] . '/redaxo/media/addons/installer';
	$start_dir		= $REX['HTDOCS_PATH'] . '/redaxo/media';
	
	if (is_dir($source_dir))
	{
		if (!is_dir($start_dir))
		{
			mkdir($start_dir);
		}
		if(!rex_copyDir($source_dir, $dest_dir , $start_dir))
		{
			$REX['ADDON']['installmsg']['installer'] = 'Verzeichnis '.$source_dir.' konnte nicht nach '.$dest_dir.' kopiert werden!';
			$REX['ADDON']['install']['installer'] = 0;
		}
	}


?>