<?php

  $padHandCount = (int) count ( $padData [$pad] );

  $padExplode = explode ( '|', $padHandParm, 2 ); 
  $padHandP1  = (int)  $padExplode [0] ?? 0;
  $padHandP2  = (int) ($padExplode [1] ?? 0);   

  if ( $padHandName == 'splice' )
    if ( $padHandP2 )
      array_splice ( $padData [$pad], $padHandP1, $padHandP2 );
    else
      if ( $padHandP1 > 0 )
        array_splice ( $padData [$pad], 0, $padHandP1 );
      else
        $padData [$pad] = array_slice ( $padData [$pad], 0, $padHandCount + $padHandP1 );
  else
    if ( $padHandP2 )
      $padData [$pad] = array_slice ( $padData [$pad], $padHandP1, $padHandP2, TRUE );
    else
      if ( $padHandP1 > 0 )
        $padData [$pad] = array_slice ( $padData [$pad], 0, $padHandP1, TRUE );
      else
        $padData [$pad] = array_slice ( $padData [$pad], $padHandCount + $padHandP1, NULL, TRUE );

?>