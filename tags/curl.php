<?php

  if ( ! isset ($pad_parms_tag ['url']) )
    $pad_parms_tag ['url'] = $pad_parm;

  if ( ! $pad_parms_tag ['url'] )
    return pad_error ("Curl: No URL given");

  return pad_curl_extra ( $pad_parms_tag, $pad_curl_output );

?>