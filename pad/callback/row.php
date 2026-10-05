<?php

  // Runs the tag's callback in its row phase, once per row.
  //
  // Reached from padCallbackBeforeRow for a before= callback, where the row is in $row and
  // changes to it are kept; and from occurrence/occurrence.php for a streaming callback,
  // after the row's fields were published, where the callback reads them as plain
  // variables ($salary) and what it sets ($total) is read back the same way - there is no
  // $row in that form.

  $padCallback = "row";

  include PAD . 'callback/callback.php';

?>