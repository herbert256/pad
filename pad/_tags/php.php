<?php

  $padPhp     = $padContent . "; return '';";
  $padContent = '';

  ob_start();

  set_error_handler ( 'padErrorThrow' );
  
  try {
    $padPhpReturn = eval ( $padPhp );
  }
  catch (Throwable $e) {
    $padPhpReturn = FALSE;
  }

  restore_error_handler ();

  if ( $padPhpReturn !== FALSE )
    $padPhpReturn .= ob_get_clean();
  else
    ob_get_clean();

  return $padPhpReturn;

?>