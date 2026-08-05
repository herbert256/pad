<?php

  // Runs the tag's callback in its init phase, once before the first row. Reached from
  // level/callback.php when the callback is the streaming kind (no before= option).

  $padCallback = "init";

  include PAD . 'callback/callback.php';

?>