<?php
  
  $pad++;
  $pLvl_cnt++;

  $pContent = $pTrue [$pad];
  include 'type_go.php';
  $pTrue [$pad] = $pContent;
  
  include 'options.php';
  include 'flags.php';
  include 'base.php';
  include 'data.php';

  $pParms [$pad] ['default'] = pIs_default_data ( $pData [$pad] );

  include PAD . "options/go/start.php";
  
  include 'trace/start.php';

  if ( isset($pPrms_tag ['callback']) and ! isset($pPrms_tag ['before']))
    include PAD . 'callback/init.php' ;

  if ( count ($pData[$pad] ) )
    include PAD . 'occurrence/start.php';
  
?>