<?php

  $inits_php = APP . "$padDir/_inits.php";
  $inits_pad = APP . "$padDir/_inits.pad";

  $inits = ( file_exists($inits_php) or file_exists($inits_pad) );

  $php_inits = ( file_exists($inits_php) ) ? padColorsFile ($inits_php)  : '';
  $pad_inits = ( file_exists($inits_pad) ) ? padColorsFile ($inits_pad) : '';

  return TRUE;

?>