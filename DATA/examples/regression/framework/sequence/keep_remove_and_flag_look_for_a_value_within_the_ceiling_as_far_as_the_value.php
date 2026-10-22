<?php

  // A default reach of 100 candidates, so the values asked about stand past the terms made
  // first: each is looked for as far as itself, whatever the terms made so far would project.

  $padSeqDefaultTries = 100;

  // The walks run far - a second or more of candidates - and the gate fetches twelve cases at
  // once: the five seconds a sequence has by default ran out on a busy machine and the
  // answer came back short. The budget is not what this case asserts. Nor is PHP's own limit:
  // on a loaded machine the walk took more than its 30 seconds of processor time and the
  // case ended on "Maximum execution time exceeded".

  $padSeqMaxSeconds = 60;

  set_time_limit ( 120 );

?>
