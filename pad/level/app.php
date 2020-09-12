<?php 

  if ( ! file_exists($pad_include_file) )
    return '';

  $pad_app_start = microtime(TRUE);
 
  pad_timing_start ('app');

  ob_start();
  $pad_app_return = include ($pad_include_file);
  $pad_app_ob = ob_get_clean();

  pad_timing_end ('app');

  if (  is_array($pad_app_return) or is_object($pad_app_return) or is_resource($pad_app_return) )
    return $pad_app_return;

  if ( $pad_app_return === 1 )
    $pad_app_return = '' ;

  if ( $pad_app_return !== TRUE and $pad_app_return !== FALSE and $pad_app_return !== NULL ) {
    if ( $pad_app_return or $pad_app_ob )
      $pad_app_return = "{location '$pad_include_file'}" . $pad_app_return . $pad_app_ob . '{/location}';
    $pad_app_ob = '';
  }

  return $pad_app_return;

?> 