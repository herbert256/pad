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

  // The arguments are values as well, written into that source between quotes: as they
  // were, {echo 'x' | echo($v)} with $v = '{php:getcwd}' ran the PHP function. Their braces
  // go in as &open; and &close;, which the quoted parameter hands the tag as the braces
  // themselves - text, never a tag - and padTagAsFunction escapes the backslash and the
  // quote. They went in wholly protected for a while, and the tag was handed the stand-ins
  // of = ' , @ in place of the characters: a field("... 1=1 ...") sent 1\ue03d1 to the
  // database and a curl('...?a=1') asked for a=\ue03d1.

  if ( $GLOBALS ['padProtectValues'] )
    $parm = array_map ( fn ( $one ) => is_string ( $one ) ? str_replace ( [ '{', '}' ], [ '&open;', '&close;' ], $one ) : $one, $parm );

  return padTagAsFunction ( $name, $value, $parm );

?>
