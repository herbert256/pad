<?php

  // Applies the closing tag's pipe. Run from level/end.php just before the level is torn
  // down, so $padPipeAfter [$pad] transforms $padResult [$pad] - every occurrence already
  // rendered and concatenated.

  if ( ! $padPipeAfter [$pad] )
    return;

  $padResult [$pad] = padEval ( $padPipeAfter [$pad], $padResult [$pad], TRUE );

  // The result is spliced into the parent's text, so it has to be a string: a pipe that ends
  // on a list - {d}x{/d | sequence:sum([1,2,3])} - handed padLevel an array and the request
  // ended on "Array to string conversion". A scalar becomes its text, anything else empty,
  // as the opening pipe's result does (level/pipes/before.php).

  if ( ! is_string ( $padResult [$pad] ) )
    $padResult [$pad] = is_scalar ( $padResult [$pad] ) ? (string) $padResult [$pad] : '';

  // The result is rendered text on its way into the parent's scan: a brace in it now is one
  // the pipe made - an entity decoder turning &#123; into { - and under $padProtectValues
  // it is kept as the literal character instead of opening a tag.

  if ( $padProtectValues and is_string ( $padResult [$pad] ) )
    $padResult [$pad] = str_replace ( [ '{', '}' ], [ '&open;', '&close;' ], $padResult [$pad] );

?>
