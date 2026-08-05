<?php

  // Pipe function substr(start [, length]): PHP substr with a 0-based start, using the
  // argument count $count to tell the one- and two-argument forms apart. A negative start
  // counts back from the end. mid is the 1-based variant.

  if ( $count == 1 )

    return substr ($value, $parm [0]);

  else

    return substr ($value, $parm [0], $parm [1]);

?>