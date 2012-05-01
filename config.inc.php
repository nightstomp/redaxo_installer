<?php

$mypage = 'installer'; // only for this file
$myself = 'installer'; // only for this file

$REX['ADDON']['page'][$mypage] 		= $mypage;
$REX['ADDON']['rxid'][$mypage] 		= '823';
$REX['ADDON']['version'][$mypage] 	= '1.5.2';
$REX['ADDON']['author'][$mypage] 	= 'Hirbod Mirjavadi';
$REX['ADDON']['dir'][$mypage] 		= dirname(__FILE__);
$REX['ADDON']['navigation'][$mypage] = array('block'=>'system');
$REX['ADDON']['name'][$mypage] 		= 'Installer';
$REX['ADDON']['perm'][$mypage] = 'admin[]';


$addonApi			= 'http://www.redaxo.org/de/_system/_webservice/addons/?v='.$REX['VERSION'].'.'.$REX['SUBVERSION'];
$addonDetailUrl		= 'http://www.redaxo.org/de/download/addons/?addon_id=';
$addonDir			= 'addons/'.$mypage;
$tmpDir				= 'temp';
$tmpFile			= 'temp.zip';



// Addon-Subnavigation für das REDAXO-Menue
$REX['ADDON'][$mypage]['SUBPAGES'] = array ();

// Da die Navigation des Installers anhand von Modulen generiert wird, müssen wir naträglich andocken
$REX['ADDON'][$mypage]['SUBPAGES_DOCKED'] = array (
  //     subpage    ,label                 ,perm   ,params               ,attributes
   array ('settings'         ,'Einstellungen'               ,''     ,''                   ,''),
  array ('help'         ,'Hilfe'               ,''     ,''                   ,'')
  //array ('connector','Connector (faceless subpage)',''     ,array('faceless'=>1) ,array('class'=>'jsopenwin'))
);

  // register subpages of plugins
  rex_register_extension(
    'ADDONS_INCLUDED',
    create_function(
      '',
      '
        global $REX;
        if(!empty($REX[\'ADDON\'][\'installer_plugins\'][\''.$mypage.'\']))
        {
          foreach($REX[\'ADDON\'][\'installer_plugins\'][\''.$mypage.'\'] as $plugin => $pluginsettings)
		  {
            if(!empty($pluginsettings[\'subpages\']))
              $REX[\'ADDON\'][\''.$mypage.'\'][\'SUBPAGES\'] = array_merge($REX[\'ADDON\'][\''.$mypage.'\'][\'SUBPAGES\'], $pluginsettings[\'subpages\']);
		  }
		  
		  $REX[\'ADDON\'][\''.$mypage.'\'][\'SUBPAGES\'] = array_merge($REX[\'ADDON\'][\''.$mypage.'\'][\'SUBPAGES\'], $REX[\'ADDON\'][\''.$mypage.'\'][\'SUBPAGES_DOCKED\']);
        }
      '
    )
  );
  
  
  // DYNAMISCHE SETTINGS
////////////////////////////////////////////////////////////////////////////////
/* dynamisch: Werte kommen aus dem "Einstellungen" Formular */
// --- DYN
$REX["ADDON"]["installer"]["settings"]["SELECT"] = array (
  'display_legend' => '0',
  'display_information' => '1',
  'nl2br_overview' => '1',
  'nl2br_versions' => '1',
  'linkconvert_overview' => '1',
  'linkconvert_versions' => '1',
  'overwrite_zip_packages' => '1',
  'display_zip_packages' => '1',
);
// --- /DYN

// HIDDEN SETTINGS
////////////////////////////////////////////////////////////////////////////////
$REX['ADDON'][$myself]['settings']['rex_list_pagination'] = 20;


if ($REX['REDAXO'] && $REX['USER'] && rex_request('page', 'string') == 'installer')
{
	if(!class_exists('Services_JSON')) {
		require_once $REX['INCLUDE_PATH'].'/addons/installer/classes/json-php4.class.php';
	}
	
	if(!class_exists('dUnzip2'))
	{
		require_once $REX['INCLUDE_PATH'].'/addons/installer/classes/unzip.class.php';
	}
    
    if(!class_exists('rex_socket'))
	{
        require_once $REX['INCLUDE_PATH'].'/addons/installer/classes/socket.class.php';
	}
	
	// functions einbinden
	require_once $REX['INCLUDE_PATH'].'/addons/installer/functions/global.func.php';
	require_once $REX['INCLUDE_PATH'].'/addons/installer/functions/filehandling.func.php';
	require_once $REX['INCLUDE_PATH'].'/addons/installer/functions/unzip.func.php';
	require_once $REX['INCLUDE_PATH'].'/addons/installer/functions/api.func.php';
	require_once $REX['INCLUDE_PATH'].'/addons/installer/functions/urls.func.php';
	require_once $REX['INCLUDE_PATH'].'/addons/installer/functions/regex.func.php';
	
	rex_register_extension('PAGE_HEADER', 'Installer_add_assets');

}


?>