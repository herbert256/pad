<?php

  $pPrms_tag ['url']   = $pLocation . trim($pParm);
  $pPrms_tag ['cache'] = FALSE;

  $pPrms_tag ['cookies'] ['PADSESSID'] = $GLOBALS['PADSESSID'];   
  $pPrms_tag ['cookies'] ['PADREQID']  = $GLOBALS['PADREQID'];    

  if ( ! pTag_parm ('complete') )
    $pPrms_tag ['get'] ['pInclude'] = 1;

  return include PAD . 'tags/curl.php';

?>