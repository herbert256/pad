<?php

  // Type handler for a stored data array ({data:name}, or a bare tag whose name is in the data
  // store): returns $padDataStore [name] for the level to iterate. Arrays are put there by the
  // toData option.
  //
  // With the print option the print construct is appended to the content first, so the array
  // can be listed without writing a tag body.

  if ( padTagParm ('print') ) {
    $padGetName = padTagParm ('print');
    include PAD . 'options/print.php';
  }

  return $padDataStore [$padTag [$pad]];

?>