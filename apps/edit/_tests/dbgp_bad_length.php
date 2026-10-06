<?php

  // Whatever connects to the debugger's port is read as Xdebug - any process of this
  // machine can. A packet whose length is negative, "-4\0", cut nothing from the buffer and
  // was taken again and again: an endless loop that grew the list of packets until the
  // debugger ran out of memory. It is thrown away with what follows it.

  $buffer = "-4\0<a/>\0";
  $frames = count ( dbgpFrames ( $buffer ) );
  $rest   = strlen ( $buffer );

?>
