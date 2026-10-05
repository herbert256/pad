<?php

  // Pipe function ago(from): a moment in words, counted from now - {$created | ago} is
  // '5 minutes ago', 'yesterday', '3 weeks ago', or for a moment to come 'in 2 days'. The
  // work is padAgo's (lib/date.php): the value is a Unix timestamp, a date in text or a
  // date object, and now is padNow's, frozen when a test froze it. A parameter counts from
  // that moment instead: {$start | ago($end)}.
  //
  // An empty value has no age and answers empty. A value that is no date answers empty
  // too, and strict mode names it, as the date pipe does.

  if ( $value === NULL or ( is_string ( $value ) and trim ( $value ) === '' ) )
    return '';

  if ( padDateParse ( $value ) === NULL ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the ago function cannot read '" . padMakeSafe ( is_scalar ( $value ) ? (string) $value : get_debug_type ( $value ), 40 ) . "' as a date" );
    return '';
  }

  $padAgoFrom = $parm [0] ?? NULL;

  if ( $padAgoFrom !== NULL and $padAgoFrom !== '' and padDateParse ( $padAgoFrom ) === NULL ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the ago function cannot read '" . padMakeSafe ( is_scalar ( $padAgoFrom ) ? (string) $padAgoFrom : get_debug_type ( $padAgoFrom ), 40 ) . "' as the moment to count from" );
    return '';
  }

  return padAgo ( $value, $padAgoFrom );

?>
