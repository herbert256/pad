<?php

  $pOpen_close = [];
  $pOpen_close [$pTag [$p]] = TRUE;

  $pPos = strpos($pTrue [$p], '{/', 0);

  while ($pPos !== FALSE) {
    $pPos2 = strpos($pTrue [$p], '}', $pPos);
    if ( $pPos2 !== FALSE ) {
      $pPos3 = strpos($pTrue [$p], ' ', $pPos);
      if ($pPos3 !== FALSE and $pPos3 < $pPos2 )
        $pPos2 = $pPos3;      
      $pCheckTag = substr($pTrue [$p], $pPos+2, $pPos2-$pPos-2);
      if ( pValid ($pCheckTag) )
        $pOpen_close [$pCheckTag] = TRUE;
    }
    $pPos = strpos($pTrue [$p], '{/', $pPos+1);
  }

  $pPos = -1;

go: $pPos++;
    $pPos = strpos($pTrue [$p], '{else}', $pPos);

  if ( $pPos === FALSE )
    return '';
  
  $pCheck = substr($pTrue [$p],0,$pPos);

  foreach ( $pOpen_close as $pCheckTag => $pDummy_var )
    if ( ( substr_count($pCheck, '{'.$pCheckTag.' ' ) + substr_count($pCheck, '{'.$pCheckTag.'}' ) )
           <> 
         ( substr_count($pCheck, '{/'.$pCheckTag.' ') + substr_count($pCheck, '{/'.$pCheckTag.'}') ) )
      goto go;

  $pTrue [$p] = substr ( $pTrue [$p], 0, $pPos );

  return substr ( $pTrue [$p], $pPos+6  );

?>