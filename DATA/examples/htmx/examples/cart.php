<?php

  // The cart is the session's: product id => quantity. A button posts its product - op=add
  // or op=remove - and the answer is the {fragment 'item'} of that product: the card with its
  // new count, then the parts elsewhere on the page that changed too, each marked
  // hx-swap-oob - htmx puts the card back where the button was and the rest where their ids
  // are: the count in the header, the summary beside the shelf. Emptying the cart is an
  // ordinary {form} post that ends in a redirect, the page coming back whole.

  $counts   = padSession ( 'htmxCart', [] );
  $products = array_slice ( htmxData ( 'products' ), 0, 8 );
  $posted   = padRequestIs ( 'POST' );

  if ( $posted ) {

    $id    = (int) padRequest ( 'id', 0 );
    $op    = (string) padRequest ( 'op', '' );
    $stock = array_column ( $products, 'stock', 'id' );

    if ( $op == 'clear' ) {
      padSessionPut ( 'htmxCart', [] );
      padRedirect ();
    }

    if ( $op == 'add' and ( $stock [$id] ?? 0 ) > ( $counts [$id] ?? 0 ) )
      $counts [$id] = ( $counts [$id] ?? 0 ) + 1;
    elseif ( $op == 'remove' and isset ( $counts [$id] ) and --$counts [$id] <= 0 )
      unset ( $counts [$id] );

    padSessionPut ( 'htmxCart', $counts );

    $products = array_values ( array_filter ( $products, fn ( $product ) => $product ['id'] == $id ) );

  }

  foreach ( $products as $at => $product )
    $products [$at] ['inCart'] = $counts [ $product ['id'] ] ?? 0;

  $cart      = htmxCart ( $counts );
  $cartCount = $cart ['count'];
  $lines     = $cart ['lines'];
  $total     = $cart ['total'];

?>
