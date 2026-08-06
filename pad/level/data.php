<?php

  // Turns the tag's result into the level's iteration data.
  //
  // A null result iterates nothing; an else result is trimmed to a single occurrence so the
  // @else@ branch renders exactly once; a hit that returned an array becomes the data
  // itself. The outcome is normalised by padData().

  if ( $padNull [$pad] )

    $padData [$pad] = [];

  elseif ( $padElse [$pad] )

    if     ( $padArray [$pad] and count ( $padTagResult ) ) $padData [$pad] = array_slice ( $padTagResult,   0, 1 );
    elseif ( count ( $padData [$pad] ) )                    $padData [$pad] = array_slice ( $padData [$pad], 0, 1 );
    else                                                    $padData [$pad] = padDefaultData ();

  elseif ( $padArray [$pad] )

    $padData [$pad] = $padTagResult;

  $padData [$pad] = padData ( $padData [$pad] );

?>