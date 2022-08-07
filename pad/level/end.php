<?php

  if ( count ($pData[$p] ) )
    include PAD . 'occurrence/end.php';

  if ( next($pData[$p]) !== FALSE )
    return include PAD . 'occurrence/start.php';

  if ( $pad_walks [$p] == 'next' ) {
    include PAD . 'walk/next.php';
    if ( $pad_walk == 'next' )
      return include PAD . 'occurrence/start.php';
  }

  $pOccur [$p] = 0;

  if ( $pad_walks [$p] == 'end' )
    include PAD . 'walk/end.php';

  if ( isset($pPrmsTag [$p] ['callback']) and ! isset($pPrmsTag [$p] ['before']) )
    include PAD . 'callback/exit.php' ;

  include PAD . "options/go/end.php";

  pReset ($pad);

  if ( $pTrace ) 
    include 'trace/end.php';

  $pad--;
  
  if ($pad)
    pHtml ( $pResult[$p+1]);
  
?>