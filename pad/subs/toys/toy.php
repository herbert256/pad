<?php

  // The shared body of toy:kind tags, such as {toy:life}. The caller sets
  // $padToyKind; registered kinds are files in subs/toys/types/.
  //
  // A toy runs in the browser: the server checks the options it is given and writes the
  // frame, the toy's script animates it - once per page, with the CSP nonce, as the
  // interactive tags write theirs (lib/widget.php). The pattern of a Game of Life, the rules
  // of an L-system stand between its tags as they are written - taken before the level walks
  // them, so a field or a tag there is not filled in.

  include_once PAD . 'subs/toys/_lib/toy.php';

  if ( ! padToyType ( $padToyKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no toy named '" . padMakeSafe ( $padToyKind, 20 ) . "' - " . implode ( ', ', padToyTypes () ) );
    return '';
  }

  // The toy shown goes on the xref record, which the reference application shows: the pages
  // that show each toy.

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'toy', $padToyKind );

  $padToySource = $padContent;
  $padContent   = '';

  // The toy's markup is a value: a level loops over the data a data= option named, and would
  // write the toy once per row - so the level gets its one default occurrence back.

  $padToyHtml = padToyTag ( $padToyKind, $padToySource );

  $padData [$pad] = padDefaultData ();

  return ( $padToyHtml === '' ) ? FALSE : $padToyHtml;

?>
