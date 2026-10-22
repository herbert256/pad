<?php

  // {echo <expression>} evaluates an expression and prints the result - the general
  // purpose output tag, and the one to put in front of a pipe. It evaluates $padOpt
  // [$pad] [0], the unparsed option text, rather than a parsed parameter, so the whole
  // expression reaches padEval() as it was written.

  // An {echo} with nothing in front of the first pipe - or nothing at all - prints
  // nothing, silently. Strict mode asks what it was meant to say.

  if ( $padCheckSyntax ) {

    list ( $padEchoHead ) = padPipeSplit ( $padOpt [$pad] [0] );

    if ( trim ( $padEchoHead ) == '' )
      padError ( "the {echo} has no expression" );

  }

  // TRUE is written as PAD writes it everywhere else - {$flag} prints 1 - where the level
  // read it as "render my content", which an {echo} has none of: {echo $flag} and
  // {echo php:is_numeric('5')} printed nothing.

  // A field the tag's pipe lets be missing - | default('-'), | pad:default('-'), | ?? '-',
  // | optional - is no error, as it is not in {$x | default('-')} (level/var.php) and in
  // ($x | default('-')): {echo $x | default('-')} named the field missing. Only the field
  // that is the whole answer: the strict check was off for all of it, and {echo 7 / 0 |
  // default('x')} answered x, {echo $missing + 1 | default('x')} 1.

  $padEchoValue = padEval ( $padOpt [$pad] [0], '', FALSE, padEvalFallbackPipe ( $padPipeBefore [$pad] ?? '' ) );

  // A list or a date object is a value the tag's pipe takes as it is - {echo [3, 1, 2] |
  // php:max}, {echo php:explode(',', $tags) | php:count}, {echo $created | ago} - and a date
  // object is written as the moment it holds when nothing made it text. Answered to the
  // level, either was read as data and the pipe was handed the level's empty content: max()
  // and count() ended the page on a string given, ago answered empty.

  // So is a float past the range, INF or -INF: answered to the level it is the text 'INF',
  // which an arithmetic pipe takes for no number - {echo $neg | + 1} over -INF ended the page
  // on "'-INF' is not a number for +" - and once piped it is written as that text, as {$big}
  // writes it.

  if ( is_array ( $padEchoValue ) or $padEchoValue instanceof DateTimeInterface
       or ( is_float ( $padEchoValue ) and is_infinite ( $padEchoValue ) ) ) {

    if ( $padPipeBefore [$pad] ?? '' ) {
      $padEchoValue         = padEval ( $padPipeBefore [$pad], $padEchoValue, TRUE );
      $padPipeBefore [$pad] = '';
    }

    if ( $padEchoValue instanceof DateTimeInterface )
      $padEchoValue = $padEchoValue->format ( 'Y-m-d H:i:s' );
    elseif ( is_float ( $padEchoValue ) and is_infinite ( $padEchoValue ) )
      $padEchoValue = (string) $padEchoValue;

  }

  return ( $padEchoValue === TRUE ) ? '1' : $padEchoValue;

?>
