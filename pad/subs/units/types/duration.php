<?php
  return [ 'canonical' => 's', 'units' => [
    's' => [ 'scale' => 1, 'offset' => 0, 'aliases' => [ 'second' ] ],
    'ms' => [ 'scale' => 0.001, 'offset' => 0, 'aliases' => [  ] ],
    'µs' => [ 'scale' => 1e-06, 'offset' => 0, 'aliases' => [ 'us' ] ],
    'min' => [ 'scale' => 60, 'offset' => 0, 'aliases' => [ 'minute' ] ],
    'h' => [ 'scale' => 3600, 'offset' => 0, 'aliases' => [ 'hour' ] ],
    'd' => [ 'scale' => 86400, 'offset' => 0, 'aliases' => [ 'day' ] ],
    'wk' => [ 'scale' => 604800, 'offset' => 0, 'aliases' => [ 'week' ] ],
  ] ];
?>
