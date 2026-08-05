<?php

  // Row-window helpers that trim a data array in place before it is iterated.
  //
  // padDataFilterGo  keeps occurrences $start..$end and drops the rest (currently unused)
  // padHandGo        the same for the handling tags (handling/types/row|start|page.php),
  //                  additionally cutting off after $count kept rows; it leaves its
  //                  counters behind in the globals $now and $hit

  function padDataFilterGo (&$vars, $start, $end) {

    $now = 0;
    foreach ( $vars as $key => $value ) {
      $now++;
      if ($now < $start or $now > $end)
        unset($vars [$key]);
    }

  }

  function padHandGo ( &$vars, $start, $end, $count=0 ) {

    global $hit, $now;

    $now = $hit = 0;

    foreach ( $vars as $key => $value ) {

      $now++;

      if ( $now < $start or $now > $end )
        unset ( $vars [$key] );
      else
        $hit++;

      if ( $count and $hit > $count )
        unset ( $vars [$key] );

    }

  }

?>