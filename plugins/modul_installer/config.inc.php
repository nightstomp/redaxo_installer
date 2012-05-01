<?php

	$parent = 'installer';
	$mypage = 'modul_installer';
	
	$REX['ADDON']['version'][$mypage] = '0.6';
	$REX['ADDON']['author'][$mypage] = 'Hirbod Mirjavadi';
	$REX['ADDON']['dir'][$mypage] = dirname(__FILE__);
	$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';
	
	$moduleApi				= 'http://www.redaxo.de/de/_system/_webservice/_modules.html?v='.$REX['VERSION'].'.'.$REX['SUBVERSION'];
	$moduleDetailUrl		= 'http://www.redaxo.de/180-0-addon-details.html?addon_id=';


  if ($REX['REDAXO']) {
    
    $REX['ADDON']['installer_plugins'][$parent][$mypage]['subpages'][] =
     array('modul_installer', "Module Installieren");
  }
 

?>