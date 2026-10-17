<?php

  // The votes are the session's: product id => [ the sum, the count ], started from a few
  // votes of others. The rating of a product is the visitor's own last vote; a click posts
  // the product and the stars, and the answer is the new average ($padExpose).

  $products = [ [ 'id' => 1, 'name' => 'Aurora Headphones', 'emoji' => '🎧' ],
                [ 'id' => 2, 'name' => 'Tactile Keyboard',  'emoji' => '⌨️' ],
                [ 'id' => 3, 'name' => 'Ember Desk Lamp',   'emoji' => '💡' ] ];

  $votes = padSession ( 'wcVotes', [ 1 => [ 'sum' => 18, 'count' => 4, 'mine' => 0 ],
                                     2 => [ 'sum' => 9,  'count' => 2, 'mine' => 0 ],
                                     3 => [ 'sum' => 7,  'count' => 2, 'mine' => 0 ] ] );

  // Asked for its JSON without a vote, the page answers no rating; a vote for a product it
  // does not have is a 404 - where both were a 500, the one for a $rating never set, the
  // other for the votes of product 99.

  $rating = NULL;

  if ( padRequestIs ( 'POST' ) ) {

    $id    = (int) padRequest ( 'id', 0 );
    $value = (int) padRequest ( 'value', 0 );

    if ( ! isset ( $votes [$id] ) )
      padAbort ( 404, 'There is no such product' );

    if ( $value >= 1 and $value <= 5 ) {
      $votes [$id] ['sum']  += $value - $votes [$id] ['mine'];
      $votes [$id] ['count'] += $votes [$id] ['mine'] ? 0 : 1;
      $votes [$id] ['mine']  = $value;
      padSessionPut ( 'wcVotes', $votes );
    }

    $rating = [ 'average' => round ( $votes [$id] ['sum'] / max ( 1, $votes [$id] ['count'] ), 1 ),
                'votes'   => $votes [$id] ['count'] ];

  }

  foreach ( $products as $at => $product )
    $products [$at] += [ 'mine'    => $votes [ $product ['id'] ] ['mine'],
                         'average' => round ( $votes [ $product ['id'] ] ['sum'] / $votes [ $product ['id'] ] ['count'], 1 ),
                         'votes'   => $votes [ $product ['id'] ] ['count'] ];

  $padExpose = [ 'rating' ];

?>
