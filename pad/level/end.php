<?php

  if ( count ($pData [$p] ) )
    include PAD . 'occurrence/end.php';

  if ( next($pData [$p]) !== FALSE )
    return include PAD . 'occurrence/start.php';

  if ( $pWalk [$p] == 'next' ) {
    include PAD . 'walk/next.php';
    if ( $pWalk [$p] == 'next' )
      return include PAD . 'occurrence/start.php';
  }

  $pOccur [$p] = 0;

  if ( $pWalk [$p] == 'end' )
    include PAD . 'walk/end.php';

  if ( isset($pPrmsTag [$p] ['callback']) and ! isset($pPrmsTag [$p] ['before']) )
    include PAD . 'callback/exit.php' ;

  include PAD . "options/go/end.php";

  if ( $pTrace ) 
    include 'trace/end.php';

  $p--;
  
  if ($p)
    pHtml ( $pResult[$p+1]);
  
?>