<?php


  /**
   * Filters array to keep only items in range.
   *
   * @param array &$vars  Array to filter (modified in place).
   * @param int   $start  First item to keep.
   * @param int   $end    Last item to keep.
   *
   * @return void
   */
  function padDataFilterGo (&$vars, $start, $end) {

    $now = 0;
    foreach ( $vars as $key => $value ) {
      $now++;
      if ($now < $start or $now > $end)
        unset($vars [$key]);
    }

  }


  /**
   * Filters array with range and optional count limit.
   *
   * @param array &$vars  Array to filter (modified in place).
   * @param int   $start  First item to keep.
   * @param int   $end    Last item to keep.
   * @param int   $count  Max items (0 = unlimited).
   *
   * @return void
   */
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
