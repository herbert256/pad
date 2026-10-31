<?php

  // Type handler for a toy: {toy:life pattern='glider'/}, or a bare tag naming a file of
  // toys/types/ when nothing else has the name, {mandelbrot/}. The toy is the tag's name, and
  // it is set up as {toy 'life'} sets it up, by toys/toy.php - run as a built-in tag's PHP
  // half is run, types/_go/tag.php.

  $padToyKind = $padTag [$pad];

  $padTagGo = PAD . 'toys/toy';

  return include PAD . 'types/_go/tag.php';

?>
