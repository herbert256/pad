<?php

  // One page, two answers. The browser asking ?examples/search gets search.pad with the
  // first result in it; the component asks the same page with padFormat=json and gets
  // $result as JSON - the templates never run for that request. $padExpose is the list of
  // what may leave the server: nothing else of this page does.

  $query    = trim ( (string) padRequest ( 'q', '' ) );
  $category = (string) padRequest ( 'category', '' );
  $order    = (string) padRequest ( 'sort', 'name' );
  $perPage  = 6;

  $products   = reactData ( 'products' );
  $categories = array_values ( array_unique ( array_column ( $products, 'category' ) ) );

  $found = array_values ( array_filter ( $products, fn ( $product ) =>
             ( $category === '' or $product ['category'] === $category ) and
             ( $query    === '' or stripos ( $product ['name'] . ' ' . $product ['blurb'], $query ) !== FALSE ) ) );

  usort ( $found, match ( $order ) {
    'cheap'  => fn ( $a, $b ) => $a ['price']  <=> $b ['price'],
    'dear'   => fn ( $a, $b ) => $b ['price']  <=> $a ['price'],
    'rating' => fn ( $a, $b ) => $b ['rating'] <=> $a ['rating'],
    default  => fn ( $a, $b ) => strcasecmp ( $a ['name'], $b ['name'] )
  } );

  $pages  = max ( 1, (int) ceil ( count ( $found ) / $perPage ) );
  $pageNo = min ( $pages, max ( 1, (int) padRequest ( 'p', 1 ) ) );

  $result = [ 'items' => array_slice ( $found, ( $pageNo - 1 ) * $perPage, $perPage ),
              'total' => count ( $found ),
              'page'  => $pageNo,
              'pages' => $pages ];

  $start = [ 'result' => $result, 'categories' => $categories ];
  $items = $result ['items'];

  $padExpose = [ 'result' ];

?>
