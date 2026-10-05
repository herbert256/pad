<?php

  // Handles the first and last options: keeps only the first (or last) $padHandCnt rows of
  // the tag's data set, leaving shorter sets alone.
  //
  // handling/types/last.php simply includes this file, so the two share one handler and
  // are told apart on $padHandName.

  // last=0 keeps no row, as first=0 does: a slice from -0 is a slice from the front, and
  // last=0 kept every row - and last=0, negative none of them.

  if ( count($padData [$pad]) > $padHandCnt )
    if ( $padHandName == 'first')
      $padData [$pad] = array_slice ( $padData [$pad], 0, $padHandCnt );
    elseif ( $padHandCnt )
      $padData [$pad] = array_slice ( $padData [$pad], $padHandCnt * -1 );
    else
      $padData [$pad] = [];

?>