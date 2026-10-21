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

  // An array that holds no rows once it is read - a page's ['results' => []], the empty
  // answer of an API kept in a variable - is a tag that came back empty, as [] itself is:
  // judged by the array as the tag answered it, the level was a hit that iterated nothing,
  // so neither its @else@ nor else= showed. Judged again as the empty answer, its template
  // is settled again too - the @else@ branch, or what else= put in place.

  if ( $padArray [$pad] and ! count ( $padData [$pad] ) ) {

    $padTagResult = [];

    include PAD . 'level/flags.php';
    include PAD . 'level/base.php';
    include PAD . 'level/pipes/before.php';

    if ( $padElse [$pad] )
      $padData [$pad] = padData ( padDefaultData () );

  }

  // The data may be a copy of another level's array - {data@users} hands back the whole set
  // the enclosing {users} loop is on - and a PHP array copy keeps the internal pointer where
  // the original stood, so occurrence/init.php read key() from the middle and the pair
  // iterated from the current row onward: {data@u} inside {u} gave a:abc b:bc c:c. Rewound
  // here, before the first occurrence, so it walks the whole set every time.

  reset ( $padData [$pad] );

?>
