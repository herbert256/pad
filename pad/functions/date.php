<?php

  global $padFmtDate;

  if ( ! $value )
    $value = time ();

  if ( $count == 0 ) {

    $format = $padFmtDate;

  } elseif ( $count == 1 ) {

    $format = $parm [0];

  } else {

    $format = $parm [0];
    $value  = strtotime ( $parm [1], $value );

  }

  return date ($format, $value);

?>