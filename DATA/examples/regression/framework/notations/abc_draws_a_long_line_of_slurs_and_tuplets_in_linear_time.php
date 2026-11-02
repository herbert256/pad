<?php

  // A tune pasted by a visitor, a line of 16 KB: every slur looked for its notes among every
  // note of the line, every beamed tuplet counted every item of it, and a chord was named for
  // every character in it that is no note - 16 KB of slurs took four seconds, of tuplets as
  // long, and a chord of 16 000 unreadable characters half a second. The lenient walk: the
  // chord is a problem the strict check names.

  $padCheckSyntax = FALSE;

  $tune = "K:C\n" . str_repeat ( '(AB)', 4000 ) . "\n" . str_repeat ( '(3ABC', 3200 ) . "\n[C" . str_repeat ( '&', 16000 ) . "E]\n";

  $start = hrtime ( TRUE );

?>
