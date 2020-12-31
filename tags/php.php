<?php

  $pad_php     = $pad_content . "; return '';";
  $pad_content = '';

  ob_start();

  set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
  $pad_php_error_reporting = error_reporting (0);
  try {
    $pad_php_return = eval ( $pad_php );
  }
  catch (Throwable $e) {
    $pad_php_return = FALSE;
  }
  error_reporting ($pad_php_error_reporting);
  restore_error_handler ();

  $pad_php_ob = trim ( ob_get_clean() );

  if ( $pad_php_ob )
     $pad_php_return .= $pad_php_ob;

  return $pad_php_return;

?>