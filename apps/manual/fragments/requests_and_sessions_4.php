<?php

  $product = padUrl ( 'products/42' );
  $orders  = padUrl ( 'orders', [ 'sort' => 'date', 'page' => 2 ] );
  $search  = padUrl ( 'search', [ 'q' => 'red shoes', 'tags' => [ 'new', 'sale' ] ] );
  $team    = padUrl ( 'about#team' );

?>
