<?php

  $pOccur [$p]++;
  $pHtml  [$p] = $pBase[$p];
  $pKey   [$p] = key($pData[$p]);

  $pCurrent [$p] = $pData [$p] [$pKey [$p]];

  if ( $pad_walks [$p] <> 'start' )
    $pad_walks_data [ $pad] [] = $pCurrent [$p];

  if ( $pad > 1 ) {

    if ( pIs_default_data ($pData [$p]) ) {
      if ( isset($pPrms_val[0]) )
        pSet_global ( $pName, $pPrms_val[0] );
    } else
      pSet_global ( $pName, $pCurrent [$p] );

    foreach ( $pCurrent [$p] as $pK => $pad_v )
      pSet_global ( $pK, $pad_v );

  }

  if ( isset($pPrms_tag ['callback']) and ! isset($pPrms_tag ['before']) )
    include PAD . 'callback/row.php' ;

  include PAD . 'occurrence/db.php';

  if ( $pTrace_occurence ) 
    include 'trace/start.php';

?>