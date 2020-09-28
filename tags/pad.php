<?php

  $pad_parms_tag ['url']   = $pad_location . trim($pad_parm);
  $pad_parms_tag ['cache'] = FALSE;

  $pad_parms_tag ['get'] ['PADSESSID'] = $PADSESSID;

  if ( ! pad_tag_parm('complete') )
    $pad_parms_tag ['get'] ['pad_include'] = 1;

  return include PAD_HOME . 'tags/curl.php';

?>