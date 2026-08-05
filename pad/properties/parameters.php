<?php

  // The parameters@tag property: every positional parameter of level $padIdx - the values
  // after the tag name, as in {switch 'odd', 'even'} - as an iterable data set.
  //
  // Entry 0 of $padOpt is dropped first because it holds the raw tag text rather than a
  // parameter; padDataForcePad() then turns the rest into name/value rows. parameter.php
  // picks out a single one.

  global $padOpt;

  $padTagParmsResult = $padOpt [$padIdx];

  unset ( $padTagParmsResult[0] );

  $padTagParmsResult = padDataForcePad ($padTagParmsResult);

  return $padTagParmsResult;

?>