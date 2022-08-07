<?php

  if ( count ($pData[$pad] ) )
    include PAD . 'occurrence/end.php';

  if ( next($pData[$pad]) !== FALSE )
    return include PAD . 'occurrence/start.php';

  if ( $pad_walks [$pad] == 'next' ) {
    include PAD . 'walk/next.php';
    if ( $pad_walk == 'next' )
      return include PAD . 'occurrence/start.php';
  }

  $pOccur [$pad] = 0;

  if ( $pad_walks [$pad] == 'end' )
    include PAD . 'walk/end.php';

  if ( isset($pPrms_tag ['callback']) and ! isset($pPrms_tag ['before']) )
    include PAD . 'callback/exit.php' ;

  include PAD . "options/go/end.php";

  pReset ($pad);

  if ( $pTrace ) 
    include 'trace/end.php';

  $pad--;
  
  if ($pad)
    pHtml ( $pResult[$pad+1]);
  
?>