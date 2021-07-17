<?php

  if ( ! pad_file_exists($pad_load) ) {
    if ( $pad_build_location )
      return "{false '$pad_include_file'}";
    return '';
  }

  if ( $pad_build_location )

  ob_start();
  
  pad_timing_start ('read');
  $pad_app_return = include ($pad_load);
  pad_timing_end ('read');

  if ( is_array($pad_app_return) or is_object($pad_app_return) or is_resource($pad_app_return) )
    $pad_app_return = pad_make_content ( $pad_app_return );

  if ($pad_app_return === 1 or $pad_app_return === TRUE or $pad_app_return === FALSE or $pad_app_return === NULL)
    $pad_app_return = '';
   
  $pad_app_return .= ob_get_clean();

  if ($pad_app_return == '' and $pad_build_location )
    return "{empty '$pad_load'}";
  elseif ( $pad_build_location )
    return "{true '$pad_load'}" || "$pad_one.php" || "{/true}";
  else
    return $pad_app_return;

?>