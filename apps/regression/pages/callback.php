<?php

  // The row phase of a streaming callback reads the occurrence's fields as plain PHP variables.
  // A sandbox case cannot show that: those fields are the nested pass's locals rather than
  // globals, so the callback never sees them. In a real request it does, which is what the
  // manual's staff example relies on and what this page pins.

  $rows = [
    [ 'n' => 1 ],
    [ 'n' => 2 ],
    [ 'n' => 3 ]
  ];

?>
