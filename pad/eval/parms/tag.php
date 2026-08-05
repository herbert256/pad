<?php

  // Invokes a PAD tag as though it were a function: padTagAsFunction() rebuilds the source
  // {name 'parm',...}$value{/name} and runs it back through the engine, returning what it
  // renders. This is how an ordinary tag can appear in the middle of an expression or pipe.

  return padTagAsFunction ( $name, $value, $parm );

?>