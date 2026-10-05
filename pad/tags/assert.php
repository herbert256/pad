<?php

  // {assert $total eq 42} - and {assert $total eq 42, 'the cart total'} with a message: a
  // condition a test run holds the page to. With $padAssert on - pad test turns it on for
  // its run - a false condition is an error, and the test fails with the condition and the
  // message as its report. With it off, the default, the tag is silent: the condition is
  // not even evaluated, so an {assert} can stay in a page that goes to production.
  //
  // The items are read raw (lib/attrs.php, padParmsRaw): the condition is an expression
  // with its own operators and must not be evaluated before the switch has been asked.

  if ( ! $padAssert )
    return '';

  $padAssertCond = trim ( $padParms [$pad] [0] ['padPrmOrg'] ?? '' );
  $padAssertText = trim ( $padParms [$pad] [1] ['padPrmOrg'] ?? '' );

  if ( $padAssertCond === '' )
    return padError ( "the {assert} needs a condition - {assert \$total eq 42}" );

  if ( padEval ( $padAssertCond ) )
    return '';

  if ( $padAssertText !== '' )
    $padAssertText = ' - ' . padEval ( $padAssertText );

  return padError ( "assert failed: $padAssertCond$padAssertText" );

?>
