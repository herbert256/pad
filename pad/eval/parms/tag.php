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

  return padTagAsFunction ( $name, $value, $parm );

?>