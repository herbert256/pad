<?php

  if ( ! isset ($pad_parms_tag ['url']) )
    $pad_parms_tag ['url'] = $pad_parm;
    
  return pad_curl ( $pad_parms_tag , $pad_curl_output );

?>