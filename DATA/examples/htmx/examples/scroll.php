<?php

  // Ninety orders, fifteen at a time. The page shows the first fifteen; the last row of every
  // batch carries hx-get for the next batch, sent when the row scrolls into view. That row
  // has no id, so the request names its fragment itself: &padFragment=rows - the answer is
  // the next rows, the last of them asking for the batch after it, and hx-swap="afterend"
  // puts them below the row that asked.

  $page  = max ( 1, (int) padRequest ( 'p', 1 ) );
  $per   = 15;
  $total = 90;
  $pages = (int) ceil ( $total / $per );
  $page  = min ( $page, $pages );

  $people = [ 'Ada', 'Bram', 'Chen', 'Dalia', 'Emeka', 'Freya', 'Goran', 'Hana' ];
  $states = [ 'paid', 'shipped', 'open', 'paid', 'returned' ];

  $orders = [];

  for ( $n = ( $page - 1 ) * $per + 1; $n <= min ( $total, $page * $per ); $n++ )
    $orders [] = [ 'number'   => 1000 + $n,
                   'customer' => $people [ ( $n * 3 ) % count ( $people ) ],
                   'amount'   => 20 + ( $n * 37 ) % 480,
                   'status'   => $states [ $n % count ( $states ) ],
                   'next'     => ( $n == $page * $per and $page < $pages ) ? $page + 1 : 0 ];

?>
