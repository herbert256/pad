<?php

  $pPhp     = $pContent . "; return '';";
  $pContent = '';

  ob_start();

  set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
  $pPhp_error_reporting = error_reporting (0);
  try {
    $pPhp_return = eval ( $pPhp );
  }
  catch (Throwable $e) {
    $pPhp_return = FALSE;
  }
  error_reporting ($pPhp_error_reporting);
  restore_error_handler ();

  $pPhp_ob = trim ( ob_get_clean() );

  if ( $pPhp_ob )
     $pPhp_return .= $pPhp_ob;

  return $pPhp_return;

?>