<?php

  // Predicate oracle for the random sequence cases: draws(from, to, count, flags...).
  //
  // The value is the rendered output of a case - comma-terminated draws, whitespace from the
  // template's layout still in - and the answer is 'ok' only when every assertion holds: the
  // right number of draws, each an integer inside [from, to], plus 'unique' and 'ascending'
  // when asked for. Anything else answers with what went wrong, so a failing case shows the
  // violation instead of a shrug. A digit-shape pattern accepted 99,99,99 for a randomize
  // and 9,1,8,2 for an orderly; this does not.

  $drawsFrom  = (int) $parm [0];
  $drawsTo    = (int) $parm [1];
  $drawsCount = (int) $parm [2];
  $drawsFlags = array_slice ( $parm, 3 );

  $draws = array_values ( array_filter ( array_map ( 'trim', explode ( ',', $value ) ), 'strlen' ) );

  if ( count ( $draws ) != $drawsCount )
    return 'counted ' . count ( $draws ) . " draws, expected $drawsCount: $value";

  foreach ( $draws as $draw ) {

    if ( ! ctype_digit ( $draw ) )
      return "not an integer: $draw";

    if ( $draw < $drawsFrom or $draw > $drawsTo )
      return "out of range $drawsFrom..$drawsTo: $draw";

  }

  if ( in_array ( 'unique', $drawsFlags ) and count ( array_unique ( $draws ) ) != count ( $draws ) )
    return 'duplicate draw: ' . implode ( ',', $draws );

  if ( in_array ( 'ascending', $drawsFlags ) )
    for ( $drawsIdx = 1; $drawsIdx < count ( $draws ); $drawsIdx++ )
      if ( $draws [$drawsIdx] <= $draws [$drawsIdx - 1] )
        return 'not ascending: ' . implode ( ',', $draws );

  return 'ok';

?>
