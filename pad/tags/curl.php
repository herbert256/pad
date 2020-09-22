<?php

  if ( ! isset($pad_parms_pad ['url']) )
    $pad_parms_pad ['url'] = $pad_parm;
    
  $pad_curl_result = pad_curl ( $pad_parms_pad , $pad_curl_output);

  if ( $pad_curl_result === FALSE)
    return FALSE;

  return $pad_curl_output ['data'];

?>