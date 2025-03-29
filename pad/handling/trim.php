<?php

  $padHandBoth  = $padPrm [$pad] ['both']  ?? 0;
  $padHandLeft  = $padPrm [$pad] ['left']  ?? 0;
  $padHandRight = $padPrm [$pad] ['right'] ?? 0;

  if ( ! $padHandBoth and ! $padHandLeft and ! $padHandRight )
    $padHandBoth = 1;

  if ( $padHandBoth ) {
    $padData [$pad] = padHandTruncate  ( $padData [$pad], 'left',  $padHandCnt );
    $padData [$pad] = padHandTruncate  ( $padData [$pad], 'right', $padHandCnt );
  }

  if ( $padHandLeft )
    $padData [$pad] = padHandTruncate  ( $padData [$pad], 'left',  $padHandCnt );

  if ( $padHandRight ) 
    $padData [$pad] = padHandTruncate  ( $padData [$pad], 'right', $padHandCnt );
  
?>