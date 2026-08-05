<?php

  // Tries the tag's own name as a stored sequence, a sequence type or an action, which covers
  // both the bare forms {mySeq}, {prime}, {sum} and the name after a prefix, {keep:prime}.
  // Only fills what find/prefix.php left empty, and prefers a store over a type over an action.

  if     ( isset ( $pqStore [$pqTag] ) ) $pqPull   = $pqTag;
  elseif ( pqSeq ( $pqTag )            ) $pqSeq    = $pqTag;
  elseif ( pqAction ( $pqTag )         ) $pqAction = $pqTag;

?>