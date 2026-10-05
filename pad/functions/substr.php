<?php

  // Pipe function substr(start [, length]): PHP substr with a 0-based start, using the
  // argument count $count to tell the one- and two-argument forms apart. A negative start
  // counts back from the end. mid is the 1-based variant. Positions count characters, not
  // bytes, so a multibyte letter is never cut in half.
  //
  // The positions are read as numbers, as left, right and mid read theirs: handed on as they
  // came, an empty one - substr(1, $len) with $len empty - or a word was a TypeError out of
  // mb_substr, and 1.5 a deprecation, either one the end of the request.

  if ( $count == 1 )

    return mb_substr ( $value, (int) $parm [0], NULL, 'UTF-8' );

  else

    return mb_substr ( $value, (int) $parm [0], (int) $parm [1], 'UTF-8' );

?>
