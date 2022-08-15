<?php

  $padPhp     = $padContent . "; return '';";
  $padContent = '';

  ob_start();

  set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
  $padPhp_error_reporting = error_reporting (0);
  try {
    $padPhp_return = eval ( $padPhp );
  }
  catch (Throwable $e) {
    $padPhp_return = FALSE;
  }
  error_reporting ($padPhp_error_reporting);
  restore_error_handler ();

  $padPhp_ob = trim ( ob_get_clean() );

  if ( $padPhp_ob )
     $padPhp_return .= $padPhp_ob;

  return $padPhp_return;

?>