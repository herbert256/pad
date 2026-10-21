<?php

  // A page that exposes its data and fails in its PHP: asked for the data, the answer is
  // the plain line, not the HTML of _errors/500.pad.

  $x         = 1;
  $padExpose = [ 'x' ];

  $y = intdiv ( 1, 0 );

?>
