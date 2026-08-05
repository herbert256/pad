<?php

  // Runs the tag's callback in its exit phase, once after the last row. Reached from
  // level/end.php when the callback is the streaming kind (no before= option).

  $padCallback = "exit";

  include PAD . 'callback/callback.php';

?>