<?php

  $path = APPS . "reference/pages/$pad_parm";

  $inits_php  = "$path/inits.php";
  $inits_html = "$path/inits.html";

  $inits = ( pad_file_exists($inits_php) or pad_file_exists($inits_html) );

  $php_inits  = ( pad_file_exists($inits_php)  ) ? pad_colors_file ($inits_php)  : '';
  $html_inits = ( pad_file_exists($inits_html) ) ? pad_colors_file ($inits_html) : '';

  return TRUE;

?>