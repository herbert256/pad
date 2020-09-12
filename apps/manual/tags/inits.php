<?php

  $php_file  = PAD_APP . 'pages/' . $dir . "/inits.php";
  $html_file = PAD_APP . 'pages/' . $dir . "/inits.html";

  $php  = ( file_exists($php_file) )  ? pad_colors_file ($php_file)  : '';
  $html = ( file_exists($html_file) ) ? pad_colors_file ($html_file) : '';

  $php_file  = ($php)  ? str_replace(PAD_APPS, '', $php_file)  : '';
  $html_file = ($html) ? str_replace(PAD_APPS, '', $html_file) : '';

  if ( $php or $html )
    return TRUE;
  else
    return NULL;
  
?>