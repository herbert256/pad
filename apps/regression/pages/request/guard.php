<?php

  // A _guard.php decides for every page below its directory, before any of the page runs:
  // a request it lets through gets the page, one it refuses gets 403 and the reason, and a
  // {page} including the guarded page renders it as nothing when the guard says no. Before
  // this, a directory was open to anyone who knew a page's name.

  $guardOpen = padCurl ( $padGoExt . 'request/guarded/inside&padInclude&key=open' );
  $guardShut = padCurl ( $padGoExt . 'request/guarded/inside&padInclude' );
  $guardHold = padCurl ( $padGoExt . 'request/guardpage&padInclude' );
  $guardLets = padCurl ( $padGoExt . 'request/guardpage&padInclude&key=open' );

  $guardResult = 'open: '   . $guardOpen ['result'] . ' ' . trim ( $guardOpen ['data'] )
               . ' | shut: ' . $guardShut ['result'] . ' ' . trim ( $guardShut ['data'] )
               . ' | held: ' . $guardHold ['result'] . ' ' . trim ( $guardHold ['data'] )
               . ' | let: '  . $guardLets ['result'] . ' ' . trim ( $guardLets ['data'] );

?>
