<?php

  // Classifies the tag's return value into this level's four state flags: $padNull for
  // NULL, INF or NAN, $padElse for FALSE, '' or an empty array, $padHit for anything else,
  // and $padArray when a hit returned an array, which makes it the level's iteration data.
  //
  // The notOk, null and else options fire from here. Also re-run by walk/next.php and
  // walk/end.php, which call the tag handler again and need the flags refreshed.

  if     ( $padTagResult === NULL ) $padNull [$pad] = TRUE;
  elseif ( $padTagResult === INF  ) $padNull [$pad] = TRUE;
  elseif ( is_float($padTagResult) && is_nan($padTagResult) ) $padNull [$pad] = TRUE;
  else                              $padNull [$pad] = FALSE;

  if     ( is_array($padTagResult) and ! count($padTagResult) ) $padElse [$pad] = TRUE;
  elseif ( is_array($padTagResult) and   count($padTagResult) ) $padElse [$pad] = FALSE;
  elseif ( $padTagResult === FALSE                            ) $padElse [$pad] = TRUE;
  elseif ( $padTagResult === ''                               ) $padElse [$pad] = TRUE;
  else                                                          $padElse [$pad] = FALSE;

  if     ( $padNull [$pad] ) $padHit [$pad] = FALSE;
  elseif ( $padElse [$pad] ) $padHit [$pad] = FALSE;
  else                       $padHit [$pad] = TRUE;

  if     ( $padHit [$pad] and is_array($padTagResult) ) $padArray [$pad] = TRUE;
  else                                                  $padArray [$pad] = FALSE;

  if     ( ! $padHit  [$pad] and padTagParm ( 'notOk' ) ) include PAD . 'options/notOk.php';
  if     (   $padNull [$pad] and padTagParm ( 'null'  ) ) include PAD . 'options/null.php';
  elseif (   $padElse [$pad] and padTagParm ( 'else'  ) ) include PAD . 'options/else.php';

  if ( $padInfo )
    include PAD . 'events/flags.php';

?>