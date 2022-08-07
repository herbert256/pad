<?php

  if ( count ($pData[$p] ) )
    include PAD . 'occurrence/end.php';

  if ( next($pData[$p]) !== FALSE )
    return include PAD . 'occurrence/start.php';

  if ( $pWalks[$p] == 'next' ) {
    include PAD . 'walk/next.php';
    if ( $pWalk == 'next' )
      return include PAD . 'occurrence/start.php';
  }

  $pOccur[$p] = 0;

  if ( $pWalks[$p] == 'end' )
    include PAD . 'walk/end.php';

  if ( isset($pPrmsTag[$p] ['callback']) and ! isset($pPrmsTag[$p] ['before']) )
    include PAD . 'callback/exit.php' ;

  include PAD . "options/go/end.php";

  pReset ($pad);

  if ( $pTrace ) 
    include 'trace/end.php';

  $pad--;
  
  if ($pad)
    pHtml ( $pResult[$p+1]);
  
?>