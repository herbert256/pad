<?php

  if ( ! isset ($pad_parms_tag ['url']) )
    $pad_parms_tag ['url'] = $pad_parm;

  if ( ! $pad_parms_tag ['url'] )
    return pad_error ("Curl: No URL given");

  pad_trace ('tag/curl', 'start: ' . $pad_parms_tag ['url']);
    
  $pad_return = pad_curl_extra ( $pad_parms_tag, $pad_curl_output );

  pad_trace ('tag/curl', 'end: ' . $pad_return);

  return $pad_return;

?>