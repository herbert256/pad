<?php

  // Invokes a PAD tag as though it were a function: padTagAsFunction() rebuilds the source
  // {name 'parm',...}$value{/name} and runs it back through the engine, returning what it
  // renders. This is how an ordinary tag can appear in the middle of an expression or pipe.
  //
  // The piped value becomes the content of that source, so under $padProtectValues it goes
  // in protected and the tag works on it as text. Piping into {code} or {sandbox} is the
  // one way to ask for the opposite - run this value as PAD - and there it goes in whole.

  if ( $GLOBALS ['padProtectValues'] and ! in_array ( $name, [ 'code', 'sandbox' ] ) )
    $value = padProtect ( $value );

  // The arguments are values as well, written into that source between quotes: unprotected,
  // {echo 'x' | echo($v)} with $v = '{php:getcwd}' ran the PHP function, and a value ending
  // in a backslash escaped its own closing quote. Protected they are the text they hold.

  if ( $GLOBALS ['padProtectValues'] )
    $parm = array_map ( 'padProtect', $parm );

  return padTagAsFunction ( $name, $value, $parm );

?>