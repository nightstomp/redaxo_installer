<?php
    // Variablen
	$parent                 = 'installer';
	$mypage                 = 'modul_installer';
	$page                   = rex_request('page', 'string');
	$subpage                = rex_request('subpage', 'string');
	$func                   = rex_request('func', 'string');
	$overwrite              = rex_request('overwrite_addon', 'int');
	$process                = false;
	$valid                  = false;
	$path                   = false;
	$file_output            = '';
	$remote_file            = rex_request('remote_file', 'string');
    //ini_set('display_errors', 0);
?>

<div id="zip_installer_output">
<?php
    
    if($func)
	{
	    if ($_FILES['zip_file']['size'] > 0 OR $remote_file)
    	{
    	    if($_FILES['zip_file']['type'] == 'application/zip' OR $_FILES['zip_file']['type'] == 'application/x-zip-compressed' OR $remote_file)
            {
                
                if($remote_file){
                    try {
                        $path = $REX['INCLUDE_PATH']."/addons/installer/" . time() . ".zip";
                        $socket = rex_socket::createByUrl($remote_file);
                        $socket->doGet();
                        $res = @fopen($path, 'w');
                        $file_output = $socket->writeBodyTo($res);
                    } catch (rex_socket_exception $e) {
                        // fehlermeldung:
                         echo rex_warning($e->getMessage());
                         $file_output = false;
                         $valid = false;
                    }
                } else {
                    $file_output = false;
                }
                
                $connector = ($file_output ? $path : $_FILES['zip_file']['tmp_name']);
                
                if($connector){
                    // Zip Klasse laden
            		$zip = new dUnzip2($connector);
            		//$zip->debug = 1; // debug?
        		
            		// Inhalt vom ZIP-FILE laden. Liefert ARRAY zurück
            		$zipList = $zip->getList();
            	} else {
            	    $zipList = array();
            	}
        		
        		if($file_output){
        		    //tempfile wieder löschen
            		@unlink($path);
                }
        		
        		if($installname = zip_installer_check_if_file_is_addon($zipList)){
        		    //echo rex_info('AddOn Paket scheint gültig zu sein!');
        		    $valid = true;
        		} else {
        		    echo rex_warning('Kein gültiges AddOn. Installation abgebrochen');
        		    if($valid){
        		        $zip->__destroy();
        		    }
        		    $valid = false;
        		}
        		
        		if(Installer_check_if_addon_exists($REX['INCLUDE_PATH']."/addons/".$installname)){
        		    if($overwrite){
        		        $process = true;
        		    } else {
        		        $process = false;
        		    }
        		} else {
        		    $process = true;
        		}
    			
    			if($valid){
        			if($process){        			    
        			    // Inhalt vom ZIP-File im AddOn-Verzeichnis entpacken
            			$zip->unzipAll($REX['INCLUDE_PATH']."/addons/");

            			// Installation säubern (Temp Daten löschen, versteckte OSX-Daten etc.)
            			//Installer_clean_addon_setup($includeDir, $file);
            			
            			// File-Handler schließen und memory freigeben
            			$zip->__destroy();
            			Installer_clean_addon_setup($REX['INCLUDE_PATH']);
            			
            			echo rex_info('ZIP-Datei wurde entpackt und ins AddOns Verzeichnis geschrieben. Bitte wähle nun eine Aktion:');
        			    echo '
        			        <ul class="action_after_unzip">
        			            <li><a href="#" data-installname="'.str_replace('/', '', $installname).'" class="zip_install_activate">AddOn (re-)installieren und gleichzeitig aktivieren</a></li>
        			            <li><a href="#" data-installname="'.str_replace('/', '', $installname).'" class="zip_install">AddOn nur (re-)installieren</a></li>
        			            <li><a href="?page=addon">Ins AddOn-Verzeichnis wechseln</a></li>
        			            <li><a href="#" class="new_zip_upload">Neue ZIP-Datei hochladen</a></li>
        			        </ul>
        			    ';
            			
        			} else {
        			    echo rex_warning('AddOn wurde nicht installert, da es vorhanden ist, jedoch der Haken "überschreiben" nicht gesetzt wurde.');
        			    $zip->__destroy();
        			}
        		}
        		
        		$cleanFiles = array();   
        		if($REX["ADDON"]["installer"]["settings"]["SELECT"]['display_zip_packages'] && $valid){
            	  		
            		echo '
            		<div class="rex-addon-output package_detail">
                        <h2 class="rex-hl2">Paket-Inhalt '.str_replace('/', '', $installname).'</h2>
                        <div style="min-height: 20px; max-height: 100px; overflow: auto;">';
                            foreach($zipList as $dir){
                                if(preg_match("/__MACOSX/",$dir['file_name']) && $valid){
                                    $cleanFiles[] = $dir['file_name'];
                                    continue;
                                }
                                echo '<p style="padding: 5px 10px 5px; border-bottom: 1px solid #eee;">'.$dir['file_name'].'</p>';
                            }
                            echo '
                        </div>    
                    </div>';
                }
                
                if(count($cleanFiles) > 0 && $valid && $process && $REX["ADDON"]["installer"]["settings"]["SELECT"]['display_zip_packages']){
                    echo '
            		<div class="rex-addon-output package_detail">
                        <h2 class="rex-hl2">Diese Dateien wurden automatisch entfernt</h2>
                        <div style="height: 80px; overflow: auto;">';
                            foreach($cleanFiles as $garbage){
                                echo '<p style="padding: 5px 10px 5px; border-bottom: 1px solid #eee;">'.$garbage.'</p>';
                            }
                            echo '
                        </div>    
                    </div>';
            }

            } else {
                echo rex_warning('Es sind nur ZIP-Dateien erlaubt');
            }
        } else {
            echo rex_warning('Keine Datei ausgewählt!');
        }
    }
?>
</div>
    <div class="rex-addon-output">
        <div class="rex-form">
            <form method="post" action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="page" value="installer" />
                <input type="hidden" name="subpage" value="zip_installer" />
                <input type="hidden" name="func" value="upload" />
                <fieldset class="rex-form-col-1">
                    <legend>Dateiauswahl</legend>
                    <div class="rex-form-wrapper">
                        <div class="rex-form-row rex-form-element-v1" id="zip_file_wrapper">
                            <p class="rex-form-col-a rex-form-select">
                                <label>Entweder Datei auswählen:</label>
                                <input type="file" name="zip_file" id="zip_file" />
                            </p>
                        </div>
                        
                        <div class="rex-form-row rex-form-element-v1" id="remote_file_wrapper">
                            <p class="rex-form-col-a rex-form-select">
                                <label>oder Remote ZIP-File (URL inkl. http://) angeben:</label>
                                <input type="text" name="remote_file" id="remote_file" size="80" />
                            </p>
                        </div>
                        
                        <div class="rex-form-row rex-form-element-v2">
                            <p class="rex-form-col-a rex-form-select">
                                <label>Wenn das AddOn bereits vorhanden ist, überschreiben?</label>
                                <input type="checkbox" name="overwrite_addon" value="1" <?php echo ($REX["ADDON"]["installer"]["settings"]["SELECT"]['overwrite_zip_packages'] ? 'checked="checked"' : ''); ?>/>
                            </p>
                        </div>
                        <div class="rex-form-row rex-form-element-v2">
                            <p class="rex-form-submit">
                                <input type="submit" value="ZIP installieren" name="submit" id="submit" class="rex-form-submit">
                            </p>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
	<script type="text/javascript">
	    jQuery(document).ready(function(){
	        
            jQuery('#zip_file').change(function(){
	           if(jQuery(this).val() != ""){
	               jQuery("#remote_file_wrapper").hide();
	           } else {
	               jQuery("#remote_file_wrapper").show();

	           }
	       });
    	       
	       jQuery('#remote_file').bind('keyup keydown change', function(){
	           if(jQuery(this).val() != ""){
	               jQuery("#zip_file_wrapper").hide();
	           } else {
	               jQuery("#zip_file_wrapper").show();
	               
	           }
	       }); 
	    });
	</script>
