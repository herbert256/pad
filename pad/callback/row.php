<?php

  // Runs the tag's callback in its row phase, once per row.
  //
  // Reached from occurrence/occurrence.php for a streaming callback, and from
  // padCallbackBeforeRow for a before= callback; in both cases the row is in $row and
  // changes to it are kept.

  $padCallback = "row";

  include PAD . 'callback/callback.php';

?>