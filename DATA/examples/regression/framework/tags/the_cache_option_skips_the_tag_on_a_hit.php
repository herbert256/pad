<?php

  // The option form keys on the page and the tag as written, so the two tags below are one
  // entry: the second is a hit, and its handler - which counts - never runs. vary= makes the
  // entry this request's own, so a run never meets an earlier run's copy.

  $calls = 0;
  $token = uniqid ( '', TRUE );

  function fwStaffRows () {
    global $calls;
    $calls++;
    return [ [ 'name' => 'joe' ], [ 'name' => 'jim' ] ];
  }

?>
