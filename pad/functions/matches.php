<?php

  // Pipe function matches(pattern): a regular expression test, '1' or '' - written infix in
  // a condition too, as like and in are: {if $code matches '/^[A-Z][A-Z][0-9]+$/'}. Inside a
  // PAD string a backslash escapes only \ ' " n r t, so a regex escape is written doubled:
  // '/@example\\.com$/', and a brace - a regex quantifier {2} - would open a tag: repeat the
  // class instead, or pass the pattern in from PHP. A pattern PHP cannot compile is an error
  // under the strict check.

  $pattern = (string) ( $parm [0] ?? '' );
  $found   = @preg_match ( $pattern, is_scalar ( $value ) ? (string) $value : '' );

  if ( $found === FALSE ) {

    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "matches: '" . padMakeSafe ( $pattern, 60 ) . "' is not a regular expression PHP can compile" );

    return '';

  }

  return $found ? '1' : '';

?>
