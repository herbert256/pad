<?php

  $path = APPS . "reference/pages/$pad_parm";

  $inits_php  = "$path/inits.php";
  $inits_html = "$path/inits.html";

  $inits = ( file_exists($inits_php) or file_exists($inits_html) );

  $php_inits  = ( file_exists($inits_php)  ) ? pad_colors_file ($inits_php)  : '';
  $html_inits = ( file_exists($inits_html) ) ? pad_colors_file ($inits_html) : '';

  return TRUE;

?>