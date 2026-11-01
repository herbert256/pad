<?php

  // Type handler for a toy: {toy:life pattern='glider'/}, or a bare tag naming a file of
  // subs/toys/types/ when nothing else has the name, {mandelbrot/}. The toy is the tag's name, and
  // it is set up as {toy 'life'} sets it up, by subs/toys/toy.php - run as a built-in tag's PHP
  // half is run, types/_go/tag.php.

  $padToyKind = $padTag [$pad];

  $padTagGo = PAD . 'subs/toys/toy';

  return include PAD . 'types/_go/tag.php';

?>
