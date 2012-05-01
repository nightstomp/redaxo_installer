<?php

$mypage = 'addon_installer'; // only for this file
$parent = 'installer';

$REX['ADDON']['page'][$mypage] 		= $mypage;
$REX['ADDON']['rxid'][$mypage] 		= '823';
$REX['ADDON']['version'][$mypage] 	= '1.3';
$REX['ADDON']['author'][$mypage] 	= 'Hirbod Mirjavadi';
$REX['ADDON']['dir'][$mypage] 		= dirname(__FILE__);


$addonApi			= 'http://www.redaxo.org/de/_system/_webservice/addons/?v='.$REX['VERSION'].'.'.$REX['SUBVERSION'];
$addonDetailUrl		= 'http://www.redaxo.org/de/download/addons/?addon_id=';
$addonDir			= 'addons/'.$mypage;
$tmpDir				= 'temp';
$tmpFile			= 'temp.zip';


	if ($REX['REDAXO']) {
  
	  $REX['ADDON']['installer_plugins'][$parent][$mypage]['subpages'][] =
	   array('addon_installer', "Addons installieren");
	}

	if ($REX['REDAXO'] && $REX['USER'] && rex_request('subpage', 'string') == 'addon_installer')
	{

		require_once $REX['INCLUDE_PATH'].'/addons/installer/plugins/addon_installer/functions/version.func.php';
		require_once $REX['INCLUDE_PATH'].'/addons/installer/plugins/addon_installer/functions/array_sort.func.php';

		//rex_register_extension('PAGE_HEADER', 'Installer_add_assets');

	}
  
?>