<?php

  // Past 4096 bytes right() and substr() from the end read a window at the end of the text,
  // not the whole of it: a run of flags, whose pairs count from the run's start, and an
  // accent written apart must come out whole there too.

  $flags  = str_repeat ( "\u{1F1F3}\u{1F1F1}", 3001 );
  $accent = str_repeat ( "e\u{0301}", 3000 ) . "ab";

?>
