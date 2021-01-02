<?php

  if ( ! isset ($pad_parms_tag ['url']) )
    $pad_parms_tag ['url'] = $pad_parm;

  pad_trace ('tag/curl', 'start: ' . $pad_parms_tag ['url']);
    
  return pad_curl_extra ( $pad_parms_tag, $pad_curl_output );

?>