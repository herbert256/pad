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

  $padEchoValue = padEval ( $padOpt [$pad] [0] );

  return ( $padEchoValue === TRUE ) ? '1' : $padEchoValue;

?>
