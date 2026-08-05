<?php

  // Tries the tag's prefix as the name of a stored sequence, a sequence type or an action, so
  // that in {prime:keep} or {mySeq:sum} the part before the colon is what gets resolved. A
  // store wins over a type, and a type over an action.

  if ( $pqPrefix )
    if     ( isset ( $pqStore [$pqPrefix] ) ) $pqPull   = $pqPrefix;
    elseif ( pqSeq ( $pqPrefix )            ) $pqSeq    = $pqPrefix;
    elseif ( pqAction ( $pqPrefix )         ) $pqAction = $pqPrefix;

?>