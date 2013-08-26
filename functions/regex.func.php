<?php

function Installer_url_to_link($text){
  // force http: on www.
  $text = str_replace( "www\.", "http://www.", $text );
  // eliminate duplicates after force
  $text = str_replace( "http://http://www\.", "http://www.", $text );
  $text = str_replace( "https://http://www\.", "https://www.", $text );

  // The Regular Expression filter
  $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
  // Check if there is a url in the text
  if(preg_match($reg_exUrl, $text, $url)) {
     // make the urls hyper links
     $text = preg_replace($reg_exUrl, '<a href="'.$url[0].'" onclick="window.open(this.href); return false;">'.$url[0].'</a>', $text);
  }    // if no urls in the text just return the text
  return ($text);
}
