<?php

  // Predicate oracle for sequences built from a per-term random draw: steps(mode, min, max,
  // count), with mode 'add' or 'multiply'.
  //
  // The value is the rendered output - comma-terminated terms. The add and multiply types
  // combine each position with a fresh draw: term n is n plus the draw, or n times it. So
  // the draw a term used is recoverable - term minus position, or term divided by position -
  // and 'ok' means there are count integer terms and every recovered draw lies in
  // [min, max]. The digit-shape patterns these cases used before accepted any numbers at
  // all; this accepts only what a real draw can produce.

  $stepsMode  = $parm [0];
  $stepsMin   = (int) $parm [1];
  $stepsMax   = (int) $parm [2];
  $stepsCount = (int) $parm [3];

  $steps = array_values ( array_filter ( array_map ( 'trim', explode ( ',', $value ) ), 'strlen' ) );

  if ( count ( $steps ) != $stepsCount )
    return 'counted ' . count ( $steps ) . " terms, expected $stepsCount: $value";

  foreach ( $steps as $stepsIdx => $step ) {

    if ( ! ctype_digit ( $step ) )
      return "not an integer: $step";

    $stepsPos = $stepsIdx + 1;

    if ( $stepsMode == 'add' )
      $stepsDraw = $step - $stepsPos;
    else {
      if ( $step % $stepsPos )
        return "term $step is not a multiple of its position $stepsPos";
      $stepsDraw = $step / $stepsPos;
    }

    if ( $stepsDraw < $stepsMin or $stepsDraw > $stepsMax )
      return "term $step at position $stepsPos means a draw of $stepsDraw, outside $stepsMin..$stepsMax";

  }

  return 'ok';

?>
