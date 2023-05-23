<?php

  $inits_php  = padApp . "$padDir/_inits.php";
  $inits_html = padApp . "$padDir/_inits.html";

  $inits = ( padExists($inits_php) or padExists($inits_html) );

  $php_inits  = ( padExists($inits_php)  ) ? padColorsFile ($inits_php)  : '';
  $html_inits = ( padExists($inits_html) ) ? padColorsFile ($inits_html) : '';

  return TRUE;

?>