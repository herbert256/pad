<?php

  // Pipe function substr(start [, length]): PHP substr with a 0-based start, using the
  // argument count $count to tell the one- and two-argument forms apart. A negative start
  // counts back from the end. mid is the 1-based variant. Positions count characters, not
  // bytes, so a multibyte letter is never cut in half.

  if ( $count == 1 )

    return mb_substr ($value, $parm [0], NULL, 'UTF-8');

  else

    return mb_substr ($value, $parm [0], $parm [1], 'UTF-8');

?>