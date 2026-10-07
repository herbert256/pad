<?php

  // Applies the opening tag's pipe. $padBase [$pad] - the level's content as the tag
  // handler left it - is run through $padPipeBefore [$pad] before any occurrence is
  // rendered, which is what makes {echo $x | upper} produce a transformed value.
  //
  // A base that is the tag's answer alone is protected here, once the pipe has had the
  // real value - level/go.php leaves it to this point under $padProtectValues.

  if ( $padPipeBefore [$pad] ) {

    // The result becomes the content the level scans, so a value the pipe writes into it -
    // {tag | @ . $v} with $v a tag or a PHP call - would run as template code, where the
    // closing pipe's result (level/pipes/after.php) and a tag's {echo} answer are kept as
    // text. The content's own braces travel through the pipe as markers (padPipeMask), every
    // brace left in the result is then one the pipe added and becomes its &open;/&close;
    // stand-in, which prints as itself, and the markers turn back into the author's braces:
    // {items | trim}<li>{$name}</li>{/items} still renders the rows. A base that is the
    // tag's answer alone is protected whole below, so it needs no mask.

    $padPipeMasked = ( $padProtectValues and ! $padBaseValue [$pad] and is_string ( $padBase [$pad] ) );

    if ( $padPipeMasked )
      $padBase [$pad] = padPipeMask ( $padBase [$pad] );

    $padBase [$pad] = padEval ( $padPipeBefore [$pad], $padBase [$pad], TRUE );

    // The result is the content the level scans and the @start@/@end@ check searches, so it
    // has to be a string: a pipe that answered NULL - a function with no return - left it
    // NULL and strpos() in padOpenClosePos ended the request (level/start.php). A scalar
    // becomes its text, anything else empty.

    if ( ! is_string ( $padBase [$pad] ) )
      $padBase [$pad] = is_scalar ( $padBase [$pad] ) ? (string) $padBase [$pad] : '';

    if ( $padPipeMasked )
      $padBase [$pad] = padPipeUnmask ( str_replace ( [ '{', '}' ], [ '&open;', '&close;' ], $padBase [$pad] ) );

  }

  if ( $padBaseValue [$pad] )
    $padBase [$pad] = padProtect ( $padBase [$pad] );

?>
