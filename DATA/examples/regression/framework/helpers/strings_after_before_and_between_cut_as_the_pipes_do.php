<?php

  $cuts = [];

  foreach ( [ [ 'a/b/c', '/' ], [ 'a::b::c', '::' ], [ 'a/b/c', 'x' ], [ 'abc', '' ], [ 'één/twee/drie', '/' ],
              [ NULL, 'x' ], [ 12345, 3 ], [ '0', '0' ] ] as [ $text, $search ] )
    $cuts [] = [ padStrAfter ( $text, $search ), padStrAfterLast ( $text, $search ),
                 padStrBefore ( $text, $search ), padStrBeforeLast ( $text, $search ) ];

  $r = json_encode ( $cuts, JSON_UNESCAPED_UNICODE );

  $b = json_encode ( [
    padStrBetween ( 'This is my name', 'This', 'name' ),
    padStrBetween ( '[a] and [b]', '[', ']' ),
    padStrBefore ( padStrAfter ( '[a] and [b]', '[' ), ']' ),
    padStrBetween ( 'abc', 'x', 'y' ),
    padStrBetween ( 'abc', 'a', 'y' ),
    padStrBetween ( 'abc', '', '' ),
    padStrBetween ( NULL, 'a', 'b' )
  ], JSON_UNESCAPED_UNICODE );

  $path = 'shop/orders/list.pad';

?>
