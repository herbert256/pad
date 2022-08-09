<?php

  $pOpenClose = [];
  $pOpenClose [$pTag [$p]] = TRUE;

  $p1 = strpos($pTrue [$p], '{/', 0);

  while ($p1 !== FALSE) {

    $p2 = strpos($pTrue [$p], '}', $p1);

    if ( $p2 !== FALSE ) {

      $p3 = strpos($pTrue [$p], ' ', $p1);
      if ($p3 !== FALSE and $p3 < $p2 )
        $p2 = $p3;      

      $pCheckTag = substr($pTrue [$p], $p1+2, $p2-$p1-2);
      if ( pValid ($pCheckTag) )
        $pOpenClose [$pCheckTag] = TRUE;

    }

    $p1 = strpos($pTrue [$p], '{/', $p1+1);

  }

  $p1 = -1;

  while (1) {

    $p1 = strpos($pTrue [$p], '{else}', ++$p1);

    if ( $p1 === FALSE )
      return TRUE;
    
    $pCheck = substr($pTrue [$p],0,$p1);

    foreach ( $pOpenClose as $pCheckTag => $pDummy )
      if ( ( substr_count($pCheck, '{'.$pCheckTag.' ' ) + substr_count($pCheck, '{'.$pCheckTag.'}' ) )
             <> 
           ( substr_count($pCheck, '{/'.$pCheckTag.' ') + substr_count($pCheck, '{/'.$pCheckTag.'}') ) )
        continue 2;

    break;

  }

  $pFalse [$p] = substr ( $pTrue [$p], $p1+6  );
  $pTrue  [$p] = substr ( $pTrue [$p], 0, $p1 );

  return TRUE;

?>