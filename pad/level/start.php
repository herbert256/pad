<?php
  
  $pad++;
  $pLvl_cnt++;

  $pContent = $pTrue [$p];
  include 'type_go.php';
  $pTrue [$p] = $pContent;
  
  include 'options.php';
  include 'flags.php';
  include 'base.php';
  include 'data.php';

  $pParms [$p] ['default'] = pIs_default_data ( $pData [$p] );

  include PAD . "options/go/start.php";
  
  include 'trace/start.php';

  if ( isset($pPrms_tag ['callback']) and ! isset($pPrms_tag ['before']))
    include PAD . 'callback/init.php' ;

  if ( count ($pData[$p] ) )
    include PAD . 'occurrence/start.php';
  
?>