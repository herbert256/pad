<?php

  $pPrmsTag[$p] ['url']   = $pLocation . trim($pParm[$p]);
  $pPrmsTag[$p] ['cache'] = FALSE;

  $pPrmsTag[$p] ['cookies'] ['PADSESSID'] = $GLOBALS['PADSESSID'];   
  $pPrmsTag[$p] ['cookies'] ['PADREQID']  = $GLOBALS['PADREQID'];    

  if ( ! pTag_parm ('complete') )
    $pPrmsTag[$p] ['get'] ['pInclude'] = 1;

  return include PAD . 'tags/curl.php';

?>