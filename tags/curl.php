<?php

  if ( ! isset($pad_parms_tag ['url']) )
    $pad_parms_tag ['url'] = $pad_parm;
    
  $pad_curl_result = pad_curl ( $pad_parms_tag , $pad_curl_output);

  if ( $pad_curl_result === FALSE)
    return FALSE;

  return $pad_curl_output ['data'];

?>