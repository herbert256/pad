<?php

  $padPhp     = $padContent . "; return '';";
  $padContent = '';

  ob_start();

  set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
  $padPhpErrorReporting = error_reporting (0);
  try {
    $padPhpReturn = eval ( $padPhp );
  }
  catch (Throwable $e) {
    $padPhpReturn = FALSE;
  }
  error_reporting ($padPhpErrorReporting);
  restore_error_handler ();

  $padPhpOb = trim ( ob_get_clean() );

  if ( $padPhpOb )
     $padPhpReturn .= $padPhpOb;

  return $padPhpReturn;

?>