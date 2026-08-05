<?php

  // Handles the first and last options: keeps only the first (or last) $padHandCnt rows of
  // the tag's data set, leaving shorter sets alone.
  //
  // handling/types/last.php simply includes this file, so the two share one handler and
  // are told apart on $padHandName.

  if ( count($padData [$pad]) > $padHandCnt )
    if ( $padHandName == 'first')
      $padData [$pad] = array_slice ( $padData [$pad], 0, $padHandCnt );
    else
      $padData [$pad] = array_slice ( $padData [$pad], $padHandCnt * -1 );

?>