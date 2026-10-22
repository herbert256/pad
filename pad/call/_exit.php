<?php

  // Closing half of a call: moves the echoed output into $padCallOB and normalises the
  // included file's return value in $padCallPHP, so callers only ever see a string, an array
  // or a plain scalar - objects and resources are flattened with padToArray, NAN becomes NULL.
  //
  // INF is a value, as -INF is (lib/values.php): it was made NULL here too, from the time the
  // field lookups and the at/ subsystem answered INF for "not there" - padMiss now - and a tag
  // whose PHP answered it - an application tag, {at 's.m'} over a value INF, a pipe function -
  // printed nothing where one answering -INF printed -INF, and {$s.m} printed INF.

  $padCallOB = ob_get_clean();

  if     ( is_object   ( $padCallPHP ) and ! ( isset ( $padCallDate ) and $padCallPHP instanceof DateTimeInterface ) )
                                         $padCallPHP = padToArray( $padCallPHP );
  elseif ( is_resource ( $padCallPHP ) ) $padCallPHP = padToArray( $padCallPHP );
  elseif ( is_float($padCallPHP) && is_nan($padCallPHP) ) $padCallPHP = NULL;

 ?>
