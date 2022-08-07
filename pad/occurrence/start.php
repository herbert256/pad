<?php

  $pOccur [$p]++;
  $pHtml  [$p] = $pBase[$p];
  $pKey   [$p] = key($pData[$p]);

  $pCurrent [$p] = $pData [$p] [$pKey [$p]];

  if ( $pad_walks [$p] <> 'start' )
    $pad_walks_data [ $pad] [] = $pCurrent [$p];

  if ( $pad > 1 ) {

    if ( pIs_default_data ($pData [$p]) ) {
      if ( isset($pPrmsVal [$p][0]) )
        pSet_global ( $pName [$p], $pPrmsVal [$p][0] );
    } else
      pSet_global ( $pName [$p], $pCurrent [$p] );

    foreach ( $pCurrent [$p] as $pK => $pad_v )
      pSet_global ( $pK, $pad_v );

  }

  if ( isset($pPrmsTag [$p] ['callback']) and ! isset($pPrmsTag [$p] ['before']) )
    include PAD . 'callback/row.php' ;

  include PAD . 'occurrence/db.php';

  if ( $pTrace_occurence ) 
    include 'trace/start.php';

?>