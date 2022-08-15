<?php

  $padath = APPS . "reference/pages/" . $padPrm [$pad];

  $inits_php  = "$padath/inits.php";
  $inits_html = "$padath/inits.html";

  $inits = ( file_exists($inits_php) or file_exists($inits_html) );

  $php_inits  = ( file_exists($inits_php)  ) ? pColors_file ($inits_php)  : '';
  $html_inits = ( file_exists($inits_html) ) ? pColors_file ($inits_html) : '';

  return TRUE;

?>