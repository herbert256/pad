<?php
  
  $p++;
  $pCnt++;

  include 'setup.php';

  $pContent = $pTrue[$p];
  include 'type_go.php';
  $pTrue[$p] = $pContent;
  
  include 'options.php';
  include 'flags.php';
  include 'base.php';
  include 'data.php';

  $pDefault[$p]= pIs_default_data ( $pData[$p] );

  include PAD . "options/go/start.php";
  
  include 'trace/start.php';

  if ( isset($pPrmsTag[$p] ['callback']) and ! isset($pPrmsTag[$p] ['before']))
    include PAD . 'callback/init.php' ;

  if ( count ($pData[$p] ) )
    include PAD . 'occurrence/start.php';
  
?>