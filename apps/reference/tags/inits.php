<?php

  $path = APP . "pages/$pad_parm";

  $lib_php    = "$path/__LIB.php";
  $lib_html   = "$path/__LIB.html";
  $inits_php  = "$path/inits.php";
  $inits_html = "$path/inits.html";

  $inits = ( pad_file_exists($inits_php) or pad_file_exists($inits_html) or  pad_file_exists($lib_php) or pad_file_exists($lib_html) );

  $php_lib    = ( pad_file_exists($lib_php)    ) ? pad_colors_file ($lib_php)    : '';
  $html_lib   = ( pad_file_exists($lib_html)   ) ? pad_colors_file ($lib_html)   : '';
  $php_inits  = ( pad_file_exists($inits_php)  ) ? pad_colors_file ($inits_php)  : '';
  $html_inits = ( pad_file_exists($inits_html) ) ? pad_colors_file ($inits_html) : '';

  return TRUE;

?>