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

  if ( $padPhpReturn !== FALSE )
    $padPhpReturn .= ob_get_clean();
  else
    ob_get_clean();

  return $padPhpReturn;

?>