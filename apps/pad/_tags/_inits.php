<?php

  $inits_php = padApp . "$padDir/_inits.php";
  $inits_pad = padApp . "$padDir/_inits.pad";

  $inits = ( padExists($inits_php) or padExists($inits_pad) );

  $php_inits = ( padExists($inits_php) ) ? padColorsFile ($inits_php)  : '';
  $pad_inits = ( padExists($inits_pad) ) ? padColorsFile ($inits_pad) : '';

  return TRUE;

?>