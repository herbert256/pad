<?php

  $pad--;

  $pData[$p] = $pData [$p+1];
  reset ( $pData[ $pad] );
  $pKey[$p] = key($pData[$p]);

  $pParent_start = strpos ( $pBase[$p], '{'.$pTag[$p]);
  $pParent_end   = strpos ( $pBase[$p], "}", $pParent_start) ;

  $pBase[$p] = substr ( $pBase[$p], 0, $pParent_start )
                       . substr ( $pBase[$p], $pParent_end + 1 );

  $pCurrent[$p] = [];
  $pOccur  [$p] = 0;
  $pResult [$p] = '';
  $pHtml   [$p] = '';

?>