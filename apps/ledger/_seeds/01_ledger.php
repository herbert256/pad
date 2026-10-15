<?php

  // Half a year of a household's money, April to September 2026 - the same every time: the
  // fake values start from a seed, and the dates are fixed, so the pages show the same.

  padFakeSeed ( 2026 );

  foreach ( [ [ 'Current account', 'bank', 1840 ], [ 'Savings', 'bank', 6200 ], [ 'Credit card', 'card', 0 ], [ 'Cash', 'cash', 80 ] ] as [ $name, $kind, $opening ] )
    db ( "INSERT INTO accounts (name, kind, opening) VALUES ('{0}', '{1}', {2})", [ $name, $kind, $opening ] );

  $categories = [
    [ 'Salary', 'income', '#16a34a' ], [ 'Side work', 'income', '#22c55e' ],
    [ 'Rent', 'expense', '#0f766e' ], [ 'Groceries', 'expense', '#0ea5e9' ], [ 'Dining out', 'expense', '#f97316' ],
    [ 'Transport', 'expense', '#8b5cf6' ], [ 'Utilities', 'expense', '#64748b' ], [ 'Fun', 'expense', '#ec4899' ],
    [ 'Health', 'expense', '#ef4444' ], [ 'Home', 'expense', '#a16207' ],
    [ 'Transfer', 'transfer', '#94a3b8' ]
  ];

  foreach ( $categories as [ $name, $kind, $color ] )
    db ( "INSERT INTO categories (name, kind, color) VALUES ('{0}', '{1}', '{2}')", [ $name, $kind, $color ] );

  // A database made by this version has the budgets table already - the migration that adds
  // it found no categories to give a budget then. One made by a version before it gets its
  // budgets from that migration, when the version that has it first runs.

  if ( db ( "CHECK sqlite_master WHERE type = 'table' AND name = 'budgets'" ) )
    foreach ( [ 'Groceries' => 450, 'Dining out' => 160, 'Transport' => 120, 'Utilities' => 190, 'Fun' => 120, 'Health' => 60, 'Home' => 100, 'Rent' => 1250 ] as $name => $monthly )
      db ( "INSERT INTO budgets (category_id, monthly) SELECT id, {1} FROM categories WHERE name = '{0}'", [ $name, $monthly ] );

  $category = array_column ( db ( "ARRAY name, id FROM categories" ), 'id', 'name' );
  $owed     = [ 3 => 0, 4 => 0 ];
  $book     = function ( $day, $account, $name, $amount, $note ) use ( $category, &$owed ) {
                if ( isset ( $owed [$account] ) and $name != 'Transfer' ) $owed [$account] += round ( $amount, 2 );
                db ( "INSERT INTO transactions (day, account_id, category_id, amount, note) VALUES ('{0}', {1}, {2}, {3}, '{4}')",
                     [ $day, $account, $category [$name], round ( $amount, 2 ), $note ] );
              };

  foreach ( [ '04', '05', '06', '07', '08', '09' ] as $month ) {

    $days = (int) date ( 't', strtotime ( "2026-$month-01" ) );
    $at   = fn ( $day ) => sprintf ( "2026-%s-%02d", $month, min ( $day, $days ) );

    $book ( $at ( 1 ),  1, 'Rent',      -1250, 'Rent' );
    $book ( $at ( 25 ), 1, 'Salary',    3200,  'Salary' );
    $book ( $at ( 12 ), 1, 'Utilities', -padFakeFloat ( 140, 210, 2 ), padFakePick ( [ 'Power and gas', 'Water and power', 'Energy bill' ] ) );
    $book ( $at ( 18 ), 1, 'Utilities', -38.5, 'Phone and internet' );

    if ( padFakeBool ( 50 ) )
      $book ( $at ( padFakeNumber ( 5, 27 ) ), 1, 'Side work', padFakeFloat ( 180, 640, 0 ), padFakePick ( [ 'Translation', 'A website for the bakery', 'Photos for a wedding' ] ) );

    for ( $week = 0; $week < 4; $week++ )
      $book ( $at ( 3 + $week * 7 ), padFakeBool ( 80 ) ? 3 : 4, 'Groceries', -padFakeFloat ( 62, 128, 2 ), padFakePick ( [ 'Market', 'Supermarket', 'Farm shop', 'Bakery and greens' ] ) );

    for ( $n = padFakeNumber ( 2, 4 ); $n > 0; $n-- )
      $book ( $at ( padFakeNumber ( 2, 28 ) ), 3, 'Dining out', -padFakeFloat ( 18, 74, 2 ), padFakePick ( [ 'Pizza', 'Lunch with Ann', 'Thai on the corner', 'Coffee and cake', 'Dinner out' ] ) );

    $book ( $at ( 2 ),  1, 'Transport', -padFakeFloat ( 49, 62, 2 ), 'Train pass' );
    $book ( $at ( padFakeNumber ( 8, 26 ) ), 3, 'Transport', -padFakeFloat ( 28, 70, 2 ), padFakePick ( [ 'Fuel', 'Bike repair', 'Taxi home' ] ) );

    for ( $n = padFakeNumber ( 1, 3 ); $n > 0; $n-- )
      $book ( $at ( padFakeNumber ( 4, 28 ) ), 3, 'Fun', -padFakeFloat ( 12, 95, 2 ), padFakePick ( [ 'Cinema', 'Concert tickets', 'Books', 'Board game', 'Museum' ] ) );

    if ( padFakeBool ( 45 ) )
      $book ( $at ( padFakeNumber ( 3, 27 ) ), 1, 'Health', -padFakeFloat ( 15, 85, 2 ), padFakePick ( [ 'Pharmacy', 'Dentist', 'Physio' ] ) );

    if ( padFakeBool ( 55 ) )
      $book ( $at ( padFakeNumber ( 3, 27 ) ), 3, 'Home', -padFakeFloat ( 20, 160, 2 ), padFakePick ( [ 'Plants', 'Lamp', 'Paint', 'Kitchen things' ] ) );

    // The month closes: the card paid off and the cash filled up from the current account,
    // and what is left over put aside - transfers, which are neither income nor spending.

    $book ( $at ( 28 ), 1, 'Transfer', $owed [3], 'Card paid off' );
    $book ( $at ( 28 ), 3, 'Transfer', -$owed [3], 'Card paid off' );
    $book ( $at ( 26 ), 1, 'Transfer', -( 120 + $owed [4] ), 'Cash for the month' );
    $book ( $at ( 26 ), 4, 'Transfer', 120 + $owed [4], 'Cash for the month' );
    $book ( $at ( 27 ), 1, 'Transfer', -400, 'Put aside' );
    $book ( $at ( 27 ), 2, 'Transfer', 400, 'Put aside' );

    $owed = [ 3 => 0, 4 => 0 ];

  }

?>
