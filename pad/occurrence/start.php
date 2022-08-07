<?php

  $pOccur [$pad]++;
  $pHtml  [$pad] = $pBase[$pad];
  $pKey   [$pad] = key($pData[$pad]);

  $pCurrent [$pad] = $pData [$pad] [$pKey [$pad]];

  if ( $pad_walks [$pad] <> 'start' )
    $pad_walks_data [ $pad] [] = $pCurrent [$pad];

  if ( $pad > 1 ) {

    if ( pIs_default_data ($pData [$pad]) ) {
      if ( isset($pPrms_val[0]) )
        pSet_global ( $pName, $pPrms_val[0] );
    } else
      pSet_global ( $pName, $pCurrent [$pad] );

    foreach ( $pCurrent [$pad] as $pK => $pad_v )
      pSet_global ( $pK, $pad_v );

  }

  if ( isset($pPrms_tag ['callback']) and ! isset($pPrms_tag ['before']) )
    include PAD . 'callback/row.php' ;

  include PAD . 'occurrence/db.php';

  if ( $pTrace_occurence ) 
    include 'trace/start.php';

?>