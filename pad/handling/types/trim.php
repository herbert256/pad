<?php

  // Handles the trim option: drops $padHandCnt rows from the front and/or the back of the
  // tag's data set.
  //
  // Which end is trimmed comes from the accompanying both, left and right options; both is
  // assumed when none of them is given. pqTruncate() does the actual cutting.

  $padHandBoth  = $padPrm [$pad] ['both']  ?? 0;
  $padHandLeft  = $padPrm [$pad] ['left']  ?? 0;
  $padHandRight = $padPrm [$pad] ['right'] ?? 0;

  if ( ! $padHandBoth and ! $padHandLeft and ! $padHandRight )
    $padHandBoth = 1;

  if ( $padHandBoth ) {
    $padData [$pad] = pqTruncate  ( $padData [$pad], 'left',  $padHandCnt );
    $padData [$pad] = pqTruncate  ( $padData [$pad], 'right', $padHandCnt );
  }

  if ( $padHandLeft )
    $padData [$pad] = pqTruncate  ( $padData [$pad], 'left',  $padHandCnt );

  if ( $padHandRight )
    $padData [$pad] = pqTruncate  ( $padData [$pad], 'right', $padHandCnt );

?>