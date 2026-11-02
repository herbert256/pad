<?php

  // Tries the tag's own name as a stored sequence, a sequence type or an action, which covers
  // both the bare forms {pull:mySeq}, {sequence:prime}, {action:sum} and the name after a prefix, {keep:prime}.
  // Only fills what find/prefix.php left empty, and prefers a store over a type over an action.

  // auto leaves no tag name; a lenient resume may have an empty store key.
  if ( $pqTag === '' )
    return;

  if     ( isset ( $pqStore [$pqTag] ) ) $pqPull   = $pqTag;
  elseif ( pqSeq ( $pqTag )            ) $pqSeq    = $pqTag;
  elseif ( pqAction ( $pqTag )         ) $pqAction = $pqTag;

  // A type the tag names is the bare type, as {sequence:add} is: its parameter is the TRUE of
  // a bare option, which the types read as the 1 they default to. It was the '' of no
  // parameter at all, and {sequence:add rows=3} ended the request on int + string where {sequence:add // rows=3} gives 2 3 4, and {sequence:and rows=3} gave 0 0 0 where {sequence:and rows=3} gives 1 0 1.
  // A first parameter the tag is given takes its place afterwards, parm/primary.php.

  if ( $pqSeq === $pqTag and $pqParm === '' )
    $pqParm = TRUE;

?>
