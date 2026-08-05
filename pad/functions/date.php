<?php

  // Pipe function date(format, modifier), also reached under the names time and timestamp:
  // formats the Unix timestamp piped in as the value. An empty or zero value means now.
  // With no argument the format is the $padFmtDate global ('Y-m-d' by default); a second
  // argument is a strtotime modifier applied to the value first, so date('Y-m-d', '+1 week')
  // shifts the timestamp before formatting it.

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