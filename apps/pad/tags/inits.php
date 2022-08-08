<?php

  $path = APPS . "reference/pages/" . $pPrm [$p];

  $inits_php  = "$path/inits.php";
  $inits_html = "$path/inits.html";

  $inits = ( file_exists($inits_php) or file_exists($inits_html) );

  $php_inits  = ( file_exists($inits_php)  ) ? pColors_file ($inits_php)  : '';
  $html_inits = ( file_exists($inits_html) ) ? pColors_file ($inits_html) : '';

  return TRUE;

?>