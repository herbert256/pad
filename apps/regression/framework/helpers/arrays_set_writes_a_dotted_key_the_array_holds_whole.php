<?php

  // A key that holds a dot itself is found as it is before the path is split - padArrGet,
  // padArrHas and padArrForget did so, padArrSet wrote a nested level beside it: the flat
  // settings padArrDot makes kept the old mail.host and padArrGet went on answering it.

  $config = padArrDot ( [ 'mail' => [ 'host' => 'old', 'port' => 25 ] ] );

  padArrSet ( $config, 'mail.host', 'new' );

  $nested = [ 'mail' => [ 'host' => 'old' ] ];

  padArrSet ( $nested, 'mail.host', 'new' );

  $object = (object) [ 'a.b' => 1 ];

  padArrSet ( $object, 'a.b', 2 );

  $r = json_encode ( [
    'flat'   => $config,
    'get'    => padArrGet ( $config, 'mail.host' ),
    'undot'  => padArrUndot ( $config ),
    'nested' => $nested,
    'object' => $object
  ] );

?>
