<?php

  // Handles the trim option: drops $padHandCnt rows from the front and/or the back of the
  // tag's data set.
  //
  // Which end is trimmed comes from the accompanying both, left and right options; both is
  // assumed when none of them is given. The cut is pqTruncate's, with the keys of rows keyed
  // by number kept - pqTruncate renumbered them, and {&key} of a set keyed by id named no row;
  // a list stays a list.

  $padHandBoth  = $padPrm [$pad] ['both']  ?? 0;
  $padHandLeft  = $padPrm [$pad] ['left']  ?? 0;
  $padHandRight = $padPrm [$pad] ['right'] ?? 0;

  if ( ! $padHandBoth and ! $padHandLeft and ! $padHandRight )
    $padHandBoth = 1;

  $padTrimKeys = ! array_is_list ( $padData [$pad] );
  $padTrimCnt = (int) $padHandCnt;

  if ( $padTrimCnt >= 1 ) {

    if ( $padHandBoth ) {
      $padData [$pad] = array_slice ( $padData [$pad], $padTrimCnt, NULL, $padTrimKeys );
      $padData [$pad] = array_slice ( $padData [$pad], 0, $padTrimCnt * -1, $padTrimKeys );
    }

    if ( $padHandLeft )
      $padData [$pad] = array_slice ( $padData [$pad], $padTrimCnt, NULL, $padTrimKeys );

    if ( $padHandRight )
      $padData [$pad] = array_slice ( $padData [$pad], 0, $padTrimCnt * -1, $padTrimKeys );

  }

?>
