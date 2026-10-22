<?php

  // The walks run far - up to two million candidates - and the gate fetches twelve cases at
  // once: the five seconds a sequence has by default ran out on a busy machine and the
  // answer came back short. The budget is not what this case asserts. Nor is PHP's own limit:
  // on a loaded machine the walk took more than its 30 seconds of processor time and the
  // case ended on "Maximum execution time exceeded".

  $padSeqMaxSeconds = 60;

  set_time_limit ( 120 );

?>
