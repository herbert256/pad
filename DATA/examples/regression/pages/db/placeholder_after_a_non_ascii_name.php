<?php

  // A statement with a name outside ASCII - naïef, which MySQL takes unquoted - fills its
  // placeholders as any other: the scanner took the byte for a letter under the locale,
  // found no word to read and stood still, a 500 at once and an endless loop under the
  // ignore and log actions.

  $code = db ( "field naïef from (select {0} as naïef) t", [ '007' ] );

?>
