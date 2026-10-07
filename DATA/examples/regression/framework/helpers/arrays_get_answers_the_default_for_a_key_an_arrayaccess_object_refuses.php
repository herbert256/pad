<?php

  // An ArrayAccess object that takes integer keys only - SplFixedArray - throws a TypeError
  // when it is asked for a text key; that ended the request where a key it does not have
  // answers the default.

  $fixed = SplFixedArray::fromArray ( [ 'a', 'b' ] );
  $data  = [ 'list' => $fixed ];

  $r = json_encode ( [
    padArrGet ( $data, 'list.name', 'none' ),
    padArrGet ( $data, 'list.1' ),
    padArrGet ( $fixed, 5, 'past the end' ),
    padArrHas ( $data, 'list.name' ),
    padArrHas ( $data, 'list.0' ),
    padArrPluck ( [ $fixed ], 'name' )
  ] );

  unset ( $fixed, $data );

?>
