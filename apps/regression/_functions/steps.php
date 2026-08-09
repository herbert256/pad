<?php

  // Predicate oracle for sequences built from a per-term random draw: steps(mode, min, max,
  // count), with mode 'add', 'multiply', 'ceil' or 'divide'.
  //
  // The value is the rendered output - comma-terminated terms - and 'ok' means there are
  // count terms and each is one a real draw can produce. The types combine each candidate
  // with a fresh draw from [min, max]:
  //
  //   add       term n is n plus the draw - the draw is term minus position
  //   multiply  term n is n times the draw - the draw is term divided by position
  //   ceil      term n is n rounded up to the next multiple of the draw, so the term must
  //             equal ceil(n/b)*b for some b in [min, max]; only sound when min is at
  //             least 1, because a drawn zero skips its candidate and shifts the positions
  //   divide    term is candidate/draw, and a drawn zero skips its candidate - so the
  //             positions are unknowable, and what remains checkable is that some draw d
  //             of at least 1 makes term*d a whole candidate number
  //
  // The digit-shape patterns these cases used before accepted any numbers at all; this
  // accepts only what the documented build rules can emit.

  $stepsMode  = $parm [0];
  $stepsMin   = (int) $parm [1];
  $stepsMax   = (int) $parm [2];
  $stepsCount = (int) $parm [3];

  $steps = array_values ( array_filter ( array_map ( 'trim', explode ( ',', $value ) ), 'strlen' ) );

  if ( count ( $steps ) != $stepsCount )
    return 'counted ' . count ( $steps ) . " terms, expected $stepsCount: $value";

  foreach ( $steps as $stepsIdx => $step ) {

    $stepsPos = $stepsIdx + 1;

    if ( $stepsMode == 'divide' ) {

      if ( ! is_numeric ( $step ) or $step <= 0 )
        return "not a positive number: $step";

      for ( $stepsDraw = max ( $stepsMin, 1 ); $stepsDraw <= $stepsMax; $stepsDraw++ )
        if ( abs ( $step * $stepsDraw - round ( $step * $stepsDraw ) ) < 1e-6 and round ( $step * $stepsDraw ) >= 1 )
          continue 2;

      return "term $step is not any candidate divided by a draw from 1..$stepsMax";

    }

    if ( ! ctype_digit ( $step ) )
      return "not an integer: $step";

    if ( $stepsMode == 'ceil' ) {

      for ( $stepsDraw = $stepsMin; $stepsDraw <= $stepsMax; $stepsDraw++ )
        if ( $stepsDraw and ceil ( $stepsPos / $stepsDraw ) * $stepsDraw == $step )
          continue 2;

      return "term $step at position $stepsPos is not its position rounded up to a multiple of $stepsMin..$stepsMax";

    }

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