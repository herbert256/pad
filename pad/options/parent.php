<?php

  $pad--;

  $pData [$pad] = $pData [$pad+1];
  reset ( $pData[ $pad] );
  $pKey [$pad] = key($pData[$pad]);

  $pParent_start = strpos ( $pBase[$pad], '{'.$pTag );
  $pParent_end   = strpos ( $pBase[$pad], "}", $pParent_start) ;

  $pBase [$pad] = substr ( $pBase[$pad], 0, $pParent_start )
                       . substr ( $pBase[$pad], $pParent_end + 1 );

  $pCurrent [$pad] = [];
  $pOccur   [$pad] = 0;
  $pResult  [$pad] = '';
  $pHtml    [$pad] = '';

?>