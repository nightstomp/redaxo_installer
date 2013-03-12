
/* Author:

*/

var d = false, // debug var
		initalized = false,
		jsonXHR = null, // jsonXHR request
		ajaxXHR = null,
		updatetext = null;


	var installer               = installer || {};
	installer.init         		= installer.init || {};
	installer.get               = installer.get || {};
	installer.set               = installer.set || {};
	installer.register          = installer.register || {};
	installer.config			= installer.config || {};


	installer.init = function() {
		"use strict";

		// save some inital vars
		updatetext = jQuery('.showupdates').text()

		return installer.detectSiteFeatures();
	};

	installer.detectSiteFeatures = function() {
		"use strict";

		this.init.searchaddons();
		this.init.clickevents();
	};

	installer.init.clickevents = function () {
		"use strict";

		jQuery('#installer-addonlist tr, .install-addon-link')
		.die('click')
		.live('click', function(event){
			event.preventDefault();

			var el = jQuery(this);
			return installer.get.addon(el.attr('data-addon'));;
		});

		jQuery('.closebox')
		.die('click')
		.live('click', function(event){
		   event.preventDefault();
		   installer.set.closeLayerBox();
		   return false;
		});

		jQuery('.addon-on-redaxo a')
		.unbind('click')
		.bind('click', function(event){
			event.stopPropagation();
		});	

		// force version switch
		jQuery('.forceversionselect').change(function(event){
			jQuery(this).closest('form').submit();
		});

		// show updates
		jQuery('.showupdates').click(function(event){
			return installer.get.updates(event, jQuery(this));
		});

		// init some live-functions for installing
		installer.init.installactions();
	};

	installer.init.installactions = function(){

		jQuery(".zip_install, .zip_install_activate")
		.die('click')
		.live('click',function(event){
		    event.preventDefault();
		    
		   	jQuery(".package_detail").remove();
		    jQuery(".rex-info").html("<p><span>Versuche Installation...</span></p>");
		    
		    var linkEl = jQuery(this);
		    var linkParent = jQuery(this).parent();
		    jQuery(this).html("Bitte warten...");
		    
		    jQuery.ajax({
		      url: "?page=addon&addonname="+jQuery(this).attr("data-installname")+"&install=1",
		      success: function(data) {
		                        			                    
		          var res = jQuery(data);
		          var err = res.find(".rex-warning");
		          var cor = res.find(".rex-info");
		          
		          if(err.length > 0){
		              linkEl.parent().html("Fehler: <br /><br />" + err.html());
		              jQuery(".rex-info").removeClass("rex-info").addClass("rex-warning").html("<p><span>Installation leider fehlgeschlagen...</span></p>");
		          }

		          if(linkEl.attr("class") == "zip_install_activate" && cor.length > 0){
		              jQuery(".rex-info").html("<p><span>Install OK, versuche Aktivierung...</span></p>");
		              jQuery(".zip_install").parent().html(cor.html());
		              linkEl.parent().html("Aktiviere, bitte warten...");
		              
		              var xhr = jQuery.ajax({
		                url: "?page=addon&addonname="+linkEl.attr("data-installname")+"&activate=1",
		                success: function(data) {
		                  var res2 = jQuery(data);
		                  var cor2 = res2.find(".rex-info");
		                  linkParent.html(cor2.html());
		                  
		                  jQuery(".rex-info").html("<p><span>AddOn erfolgreich installert &amp; aktiviert! <a href=\"\">Seite reloaden?</a></span></p>");
		                  
		                  xhr.abort();
		                }
		              });
		              
		          } else {
		                 if(cor.length > 0){
		                     linkEl.parent().html(cor.html());
		                     jQuery(".rex-info").html("<p><span>Installation war erfolgreich :)</span></p>");
		                }
		          }
		      }
		    });
		});

		jQuery(".new_zip_upload")
		.die('click')
		.live('click',function(){
	    	jQuery(".zip_installer_output").fadeOut();
		});
	}

	installer.get.updates = function(event, element){
		event.preventDefault();

		if(element.hasClass('active')){
			element.removeClass('active');
			element.text(updatetext);
			jQuery('#installer-addonlist tr:not(#noresults)').show();
			jQuery('.addon-search-wrapper').show();

		} else {
			element.addClass('active');
			element.text('Alle anzeigen');

			jQuery('#installer-addonlist tr:not(.tableheadline,.updateAvailable)').hide();
			jQuery('.addon-search-wrapper').hide();
		}

		return false;
	};

	installer.init.searchaddons = function() {
		"use strict";

		var searchSel = jQuery("#search_addon");
		searchSel.val('');
		searchSel.focus();
		
		searchSel.quicksearch("table#installer-addonlist tbody tr.searchable", {
			noResults: '#noresults',
			stripeRows: ['odd', 'even'],
			loader: 'span.loading'
		});
	};
	
	installer.get.addon = function(url){
		"use strict";

		jQuery('body').addClass('overflowhidden');

		jQuery('#ajax-result')
		.empty()
		.show()
		.append('<div class="ajax-result-content"></div>');

		jQuery('.ajax-result-content')
		.html('<div class="spinnwrap"><img src="media/addons/installer/spinner.gif" alt="Loading" /> Lade Daten...</div>')
		.load(url + '&viaAjax=1', function() {
			if(jQuery('.force_reinstall').length){
				jQuery('.zip_install_activate').trigger('click');
			}
		});
		
		return false;
	};

	installer.set.closeLayerBox = function() {
		jQuery('#ajax-result').hide();
		jQuery('body').removeClass('overflowhidden');
	}
	
	jQuery(document).ready(function () {
		installer.init();
	});