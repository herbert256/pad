<?php

  // The cart is the session's: product id => quantity. The three islands get the products and
  // the cart as their props; a post - op=add or op=remove with an id - changes the session's
  // cart and answers it ($padExpose), and the island that posted puts the answer into the
  // store the three share.

  $counts = padSession ( 'islandsCart', [] );

  if ( padRequestIs ( 'POST' ) ) {

    $id = (int) padRequest ( 'id', 0 );
    $op = (string) padRequest ( 'op', '' );

    if ( $op == 'add' and in_array ( $id, array_column ( islandsData ( 'products' ), 'id' ) ) )
      $counts [$id] = ( $counts [$id] ?? 0 ) + 1;
    elseif ( $op == 'remove' and isset ( $counts [$id] ) and --$counts [$id] <= 0 )
      unset ( $counts [$id] );

    padSessionPut ( 'islandsCart', $counts );

  }

  $cart      = islandsCart ( $counts );
  $shelf     = [ 'products' => islandsData ( 'products' ), 'cart' => $cart ];
  $badge     = [ 'cart' => $cart ];
  $padExpose = [ 'cart' ];

?>
