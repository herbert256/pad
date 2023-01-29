<?php

  $padath = PAD . "pad/reference/pages/" . $padPrm [$pad] [0];

  $inits_php  = "$padath/inits.php";
  $inits_html = "$padath/inits.html";

  $inits = ( file_exists($inits_php) or file_exists($inits_html) );

  $php_inits  = ( file_exists($inits_php)  ) ? padColorsFile ($inits_php)  : '';
  $html_inits = ( file_exists($inits_html) ) ? padColorsFile ($inits_html) : '';

  return TRUE;

?>