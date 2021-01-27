<?php

  if ( ! file_exists($pad_call) ) {
    if ( $pad_add_location )
      return "{false '$pad_call'/}";
    else
      return '';
  }

  ob_start();
  
  pad_timing_start ('app');
  $pad_call_return = include ($pad_call);
  pad_timing_end ('app');

  if ( is_array($pad_call_return) or is_object($pad_call_return) or is_resource($pad_call_return) )
    $pad_call_return = pad_make_content ( $pad_call_return );
  elseif ($pad_call_return === 1 or $pad_call_return === TRUE or $pad_call_return === FALSE or $pad_call_return === NULL)
    $pad_call_return = '';

  $pad_call_return .= ob_get_clean();

  return pad_build_location ( $pad_call, $pad_call_return );

?>