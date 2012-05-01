<?php

$mypage = 'zip_installer'; // only for this file
$parent = 'installer';

$REX['ADDON']['page'][$mypage] 		= $mypage;
$REX['ADDON']['rxid'][$mypage] 		= '823';
$REX['ADDON']['version'][$mypage] 	= '0.5';
$REX['ADDON']['author'][$mypage] 	= 'Hirbod Mirjavadi';
$REX['ADDON']['dir'][$mypage] 		= dirname(__FILE__);


	if ($REX['REDAXO']) {
  
	  $REX['ADDON']['installer_plugins'][$parent][$mypage]['subpages'][] =
	   array('zip_installer', "ZIP / Remote Installer");
	}

	if ($REX['REDAXO'] && $REX['USER'] && rex_request('subpage', 'string') == 'zip_installer')
	{
		require_once $REX['INCLUDE_PATH'].'/addons/installer/plugins/zip_installer/functions/zip_check.func.php';
	}
  
?>