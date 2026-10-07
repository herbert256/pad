<?php

  // A key an ArrayAccess object refuses for its type - SplFixedArray takes integers only -
  // is not there to remove: padArrForget and padArrExcept leave the object as it is, as
  // padArrGet and padArrHas answer it absent. They reported they could not remove it.

  $fixed = SplFixedArray::fromArray ( [ 'a', 'b' ] );
  $data  = [ 'list' => $fixed, 'n' => 1 ];

  padArrForget ( $data, 'list.name, n' );

  $r = json_encode ( [
    array_keys ( $data ),
    $fixed -> toArray (),
    array_keys ( padArrExcept ( [ 'list' => $fixed, 'm' => 2 ], 'list.name.first, m' ) ),
    padArrHas ( $data, 'list.name' )
  ] );

  unset ( $fixed, $data );

?>
