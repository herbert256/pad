<?php

  $pad_prms_tag ['url']   = $pad_location . trim($pad_parm);
  $pad_prms_tag ['cache'] = FALSE;

  $pad_prms_tag ['cookies'] ['PADSESSID'] = $GLOBALS['PADSESSID'];   
  $pad_prms_tag ['cookies'] ['PADREQID']  = $GLOBALS['PADREQID'];    

  if ( ! pad_tag_parm ('complete') )
    $pad_prms_tag ['get'] ['pad_include'] = 1;

  return include PAD . 'tags/curl.php';

?>