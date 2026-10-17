<?php

  // Whether a page array is a record db() answered costs about the same however many
  // records the request read: after 1500 orders read one RECORD at a time, every padData of
  // an associative array walked the thousand rows kept - a list keyed by id, and an array
  // holding the columns and one more, compared value by value - eight thousand of each took
  // seconds. A record with a field added, or a value cast, is still known; one read too long
  // ago is not.

  $GLOBALS ['padSqlConnect'] = padDbSqlite ( ':memory:' );

  db ( "create table orders (id integer primary key, customer text, city text, total real, note text)" );

  for ( $i = 1; $i <= 1500; $i++ )
    db ( "insert into orders values ({0}, {1}, {2}, {3}, {4})", [ $i, "customer $i", "city $i", $i + 0.5, "note $i" ] );

  for ( $i = 1; $i <= 1500; $i++ )
    $order = db ( "record * from orders where id = {0}", [ $i ] );

  $other = [ 'id' => 'x', 'customer' => 'x', 'city' => 'x', 'total' => 'x', 'note' => 'x', 'label' => 'x' ];
  $byId  = array_fill_keys ( range ( 1, 50 ), [ 'name' => 'x' ] );

  $start = hrtime ( TRUE );

  for ( $i = 0; $i < 8000; $i++ ) {
    padDataIsRecord ( $other );
    padDataIsRecord ( $byId );
  }

  $time = ( hrtime ( TRUE ) - $start < 1e9 ) ? 'in time' : 'too slow';

  $first = [ 'id' => 1, 'customer' => 'customer 1', 'city' => 'city 1', 'total' => 1.5, 'note' => 'note 1' ];

  $known = implode ( ' ', array_map ( fn ( $one ) => padDataIsRecord ( $one ) ? 'yes' : 'no', [
    $order + [ 'label' => 'added' ],
    [ 'total' => '1500.50', 'label' => 'x' ] + $other,
    [ 'id' => '1500', 'label' => 'x' ] + $other,
    $first,
    $first + [ 'label' => 'added' ],
    $other,
    $byId
  ] ) );

?>
