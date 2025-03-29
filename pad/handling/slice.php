<?php

  $padExplode = explode ( '|', $padHandParm, 2 ); 
  $padHandP1  = $padExplode [0] ?? 0;
  $padHandP2  = $padExplode [1] ?? 0;   

  if ( $padHandP2 )
    $padData [$pad] = array_slice ( $padData [$pad], $padHandP1, $padHandP2 );
  else
    $padData [$pad] = array_slice ( $padData [$pad], $padHandP1             );

?>