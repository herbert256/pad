<?php

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
