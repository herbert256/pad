<?php

  $pOccur[$p]++;
  $pHtml [$p] = $pBase[$p];
  $pKey  [$p] = key($pData[$p]);

  $pCurrent[$p] = $pData[$p] [$pKey[$p]];

  if ( $pWalks[$p] <> 'start' )
    $pWalks_data [ $pad] [] = $pCurrent[$p];

  if ( $pad > 1 ) {

    if ( pIs_default_data ($pData[$p]) ) {
      if ( isset($pPrmsVal[$p][0]) )
        pSet_global ( $pName[$p], $pPrmsVal[$p][0] );
    } else
      pSet_global ( $pName[$p], $pCurrent[$p] );

    foreach ( $pCurrent[$p] as $pK => $pV )
      pSet_global ( $pK, $pV );

  }

  if ( isset($pPrmsTag[$p] ['callback']) and ! isset($pPrmsTag[$p] ['before']) )
    include PAD . 'callback/row.php' ;

  include PAD . 'occurrence/db.php';

  if ( $pTrace_occurence ) 
    include 'trace/start.php';

?>