<?php

  $pad_parms_pad ['url']   = $pad_location . trim($pad_parm);
  $pad_parms_pad ['cache'] = FALSE;

  $pad_parms_pad ['get'] ['PADSESSID'] = $PADSESSID;

  if ( ! pad_tag_parm('complete') )
    $pad_parms_pad ['get'] ['pad_include'] = 1;

  return include PAD_HOME . 'tags/curl.php';

?>