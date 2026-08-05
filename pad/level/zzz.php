<?php

  // Unused: nothing in the engine includes this file, and the unconditional return on the
  // first line makes the lines below unreachable. It sketches an alternative treatment of a
  // bare {expression} tag - evaluate it first and splice the result back, unless it came
  // out as an array.

return padLevel ( $padBetweenOrg );

  $padReturn = padEval ( $padBetweenOrg );

  if ( is_array ( $padReturn ) )
    return padLevel ( $padBetweenOrg );
  else
    return padLevel ( $padReturn );

?>