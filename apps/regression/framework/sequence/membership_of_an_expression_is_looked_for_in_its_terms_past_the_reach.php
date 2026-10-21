<?php

  // The walks run far - up to two million candidates - and the gate fetches twelve cases at
  // once: the five seconds a sequence has by default ran out on a busy machine and the
  // answer came back short. The budget is not what this case asserts.

  $padSeqMaxSeconds = 60;

?>
