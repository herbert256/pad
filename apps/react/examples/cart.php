<?php

  // The cart is the session's: product id => quantity. A post adds one (op=add), takes one
  // off (op=remove) or empties it (op=clear), and the answer to it - like the page itself -
  // is the cart as the islands show it: lines, count and total. Stock is the server's to
  // check: a product sold out cannot be added, however the button was pressed.

  $products = array_column ( reactData ( 'products' ), NULL, 'id' );
  $counts   = padSession ( 'reactCart', [] );

  if ( padRequestIs ( 'POST' ) ) {

    $op = padRequest ( 'op', '' );
    $id = (int) padRequest ( 'id', 0 );

    if ( $op == 'clear' )
      $counts = [];
    elseif ( $op == 'add' and isset ( $products [$id] ) and $products [$id] ['stock'] > ( $counts [$id] ?? 0 ) )
      $counts [$id] = ( $counts [$id] ?? 0 ) + 1;
    elseif ( $op == 'remove' and isset ( $counts [$id] ) and --$counts [$id] <= 0 )
      unset ( $counts [$id] );

    padSessionPut ( 'reactCart', $counts );

  }

  $cart  = reactCart ( $counts );
  $shelf = [ 'products' => array_slice ( array_values ( $products ), 0, 9 ), 'cart' => $cart ];

  $padExpose = [ 'cart' ];

?>
