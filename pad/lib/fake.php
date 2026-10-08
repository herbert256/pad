<?php

  // Fake data: names, addresses, text, numbers and dates that look real enough to fill a
  // development database, a seeder or a demo page - and the same ones again after the same
  // seed, so a test can show them.
  //
  //   padFakeSeed ( 42 );
  //   $name  = padFakeName ();                         Emma Walker
  //   $mail  = padFakeEmail ( $name );                 emma.walker@example.org
  //   $rows  = padFactory ( 'customers', 25, fn ( $i ) => [ 'name' => padFakeName (), 'city' => padFakeCity () ] );
  //
  // padFakeSeed       starts the sequence from a seed - a number or a text; NULL a random one
  // padFakeNumber     a whole number from $min to $max, both included
  // padFakeFloat      a number from $min to $max, rounded to $decimals
  // padFakeBool       TRUE with a chance of $chance percent
  // padFakePick       one value of an array; padFakePicks: $count different ones
  // padFakeFirstName  padFakeLastName  padFakeName      a person's name
  // padFakeEmail      an address at example.com, .org or .net (the domains reserved for
  //                   examples, so a message sent to one reaches nobody), from a name when given
  // padFakePhone      a number in the 555-01xx range kept for fiction
  // padFakeCompany    padFakeCity  padFakeCountry  padFakeStreet
  // padFakeWord       padFakeWords ( n )  padFakeSentence  padFakeParagraph   lorem ipsum text
  // padFakeDate       a date between two moments, formatted - 'Y-m-d' unless said otherwise
  // padFakeUuid       a version 4 UUID made from the sequence, so it repeats with the seed too
  // padFakeUnique     a value from a generator that it has not given before under that key -
  //                   padFakeUnique ( 'email', 'padFakeEmail' ); padFakeUniqueReset to forget
  // padFactory        inserts $count rows into a table through db (), each made by a function
  //                   of $i (1, 2, ...): the values go in as placeholders, never as SQL text.
  //                   The rows as inserted, each with 'id' - the id the insert answered -
  //                   when the row did not give one itself
  //
  // The sequence is PAD's own - xorshift32 in a static - not mt_rand, so seeding it leaves
  // the application's own mt_rand and rand alone, and a seeded run gives the same values on
  // every machine and PHP version. It is not for anything secret: padStrRandom and
  // padRandomString are. The word lists are small on purpose - enough to look varied, not a
  // dictionary.

  function padFakeSeed ( $seed = NULL ) {

    if ( $seed === NULL )
      $seed = random_int ( 1, 0xFFFFFFFF );
    elseif ( ! is_int ( $seed ) )
      $seed = crc32 ( (string) $seed );

    $state = ( $seed ^ 0x9E3779B9 ) & 0xFFFFFFFF;

    padFakeState ( $state ?: 0x6C078965 );

    // The first values after a small seed are small too: a few turns mix it in.

    for ( $i = 0; $i < 8; $i++ )
      padFakeNext ();

  }

  // The state, unseeded the first time it is asked for: a random start.

  function padFakeState ( $set = NULL ) {

    static $state = NULL;

    if ( $set !== NULL )
      $state = $set;
    elseif ( $state === NULL )
      $state = random_int ( 1, 0xFFFFFFFF );

    return $state;

  }

  // The next 32-bit value of the sequence.

  function padFakeNext () {

    $x  = padFakeState ();
    $x ^= ( $x << 13 ) & 0xFFFFFFFF;
    $x ^= $x >> 17;
    $x ^= ( $x << 5 ) & 0xFFFFFFFF;

    return padFakeState ( $x & 0xFFFFFFFF );

  }

  function padFakeNumber ( $min = 0, $max = 100 ) {

    if ( ! is_numeric ( $min ) or ! is_numeric ( $max ) or (int) $min > (int) $max ) {
      padError ( "padFakeNumber: the range is two whole numbers, the first not above the second - not "
                 . padMakeSafe ( var_export ( $min, TRUE ) . ', ' . var_export ( $max, TRUE ), 60 ) );
      return 0;
    }

    $min   = (int) $min;
    $range = (int) $max - $min + 1;

    if ( $range > 0xFFFFFFFF )
      return $min + (int) floor ( padFakeUnit () * $range );

    return $min + ( padFakeNext () % $range );

  }

  // A number from 0 up to, not including, 1.

  function padFakeUnit () {

    return padFakeNext () / 4294967296;

  }

  function padFakeFloat ( $min = 0, $max = 1, $decimals = 2 ) {

    if ( ! is_numeric ( $min ) or ! is_numeric ( $max ) or $min > $max ) {
      padError ( "padFakeFloat: the range is two numbers, the first not above the second" );
      return 0.0;
    }

    return round ( $min + padFakeUnit () * ( $max - $min ), (int) $decimals );

  }

  function padFakeBool ( $chance = 50 ) {

    return padFakeNext () % 100 < (int) $chance;

  }

  function padFakePick ( $values ) {

    if ( ! is_array ( $values ) or ! $values ) {
      padError ( 'padFakePick: the values are an array with at least one value' );
      return NULL;
    }

    $values = array_values ( $values );

    return $values [ padFakeNext () % count ( $values ) ];

  }

  function padFakePicks ( $values, $count ) {

    if ( ! is_array ( $values ) or (int) $count < 0 or (int) $count > count ( $values ) ) {
      padError ( 'padFakePicks: the count is a number from 0 to the number of values' );
      return [];
    }

    $values = array_values ( $values );

    for ( $i = count ( $values ) - 1; $i > 0; $i-- ) {
      $j = padFakeNext () % ( $i + 1 );
      [ $values [$i], $values [$j] ] = [ $values [$j], $values [$i] ];
    }

    return array_slice ( $values, 0, (int) $count );

  }

  function padFakeFirstName () {

    return padFakePick ( padFakeList ( 'first' ) );

  }

  function padFakeLastName () {

    return padFakePick ( padFakeList ( 'last' ) );

  }

  function padFakeName () {

    return padFakeFirstName () . ' ' . padFakeLastName ();

  }

  // An address from the name, or a name of its own: lower case, the common accents taken
  // off, a dot between the words.

  function padFakeEmail ( $name = NULL ) {

    $name  = ( $name === NULL or $name === '' ) ? padFakeName () : (string) $name;
    $name  = strtr ( $name, [ 'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'å' => 'a', 'ã' => 'a', 'é' => 'e', 'è' => 'e',
                              'ë' => 'e', 'ê' => 'e', 'í' => 'i', 'ï' => 'i', 'î' => 'i', 'ó' => 'o', 'ö' => 'o', 'ô' => 'o',
                              'ø' => 'o', 'õ' => 'o', 'ú' => 'u', 'ü' => 'u', 'û' => 'u', 'ñ' => 'n', 'ç' => 'c', 'ß' => 'ss',
                              'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ö' => 'o', 'Ú' => 'u', 'Ü' => 'u', 'Ä' => 'a' ] );
    $local = trim ( preg_replace ( '/[^a-z0-9]+/', '.', strtolower ( $name ) ), '.' );

    return ( $local === '' ? 'user' : $local ) . '@' . padFakePick ( [ 'example.com', 'example.org', 'example.net' ] );

  }

  function padFakePhone () {

    return sprintf ( '+1 %d-555-01%02d', padFakeNumber ( 201, 989 ), padFakeNumber ( 0, 99 ) );

  }

  function padFakeCompany () {

    $one = padFakeLastName ();

    return match ( padFakeNumber ( 0, 3 ) ) {
      0       => "$one " . padFakePick ( [ 'Inc.', 'Ltd.', 'Group', 'and Sons', 'Partners', 'Trading' ] ),
      1       => "$one & " . padFakeLastName (),
      2       => "$one " . padFakePick ( [ 'Logistics', 'Foods', 'Systems', 'Motors', 'Design', 'Media', 'Bakery' ] ),
      default => padFakeCity () . ' ' . padFakePick ( [ 'Works', 'Supplies', 'Holdings', 'Traders' ] ),
    };

  }

  function padFakeCity () {

    return padFakePick ( padFakeList ( 'city' ) );

  }

  function padFakeCountry () {

    return padFakePick ( padFakeList ( 'country' ) );

  }

  function padFakeStreet () {

    return padFakeNumber ( 1, 250 ) . ' ' . padFakeLastName () . ' ' . padFakePick ( [ 'Street', 'Road', 'Lane', 'Avenue', 'Square', 'Way' ] );

  }

  function padFakeWord () {

    return padFakePick ( padFakeList ( 'word' ) );

  }

  function padFakeWords ( $count = 3 ) {

    $words = [];

    for ( $i = 0; $i < max ( 0, (int) $count ); $i++ )
      $words [] = padFakeWord ();

    return implode ( ' ', $words );

  }

  function padFakeSentence ( $words = NULL ) {

    $text = padFakeWords ( $words === NULL ? padFakeNumber ( 4, 10 ) : max ( 1, (int) $words ) );

    return ucfirst ( $text ) . '.';

  }

  function padFakeParagraph ( $sentences = NULL ) {

    $list = [];

    for ( $i = 0, $n = ( $sentences === NULL ? padFakeNumber ( 3, 6 ) : max ( 1, (int) $sentences ) ); $i < $n; $i++ )
      $list [] = padFakeSentence ();

    return implode ( ' ', $list );

  }

  // A moment between $from and $to - anything padDateParse reads: '2026-01-01', '-1 year',
  // a timestamp - relative ones counted from padNow, so padNowFreeze holds them still.

  function padFakeDate ( $from = '-1 year', $to = 'now', $format = 'Y-m-d' ) {

    $one = padDateParse ( $from );
    $two = padDateParse ( $to );

    if ( $one === NULL or $two === NULL or $one > $two ) {
      padError ( 'padFakeDate: from and to are two dates, the first not after the second' );
      return '';
    }

    $stamp = padFakeNumber ( $one->getTimestamp (), $two->getTimestamp () );

    return $one->setTimestamp ( $stamp )->format ( $format );

  }

  function padFakeUuid () {

    $hex = '';

    for ( $i = 0; $i < 4; $i++ )
      $hex .= sprintf ( '%08x', padFakeNext () );

    $hex [12] = '4';
    $hex [16] = '89ab' [ hexdec ( $hex [16] ) & 3 ];

    return substr ( $hex, 0, 8 ) . '-' . substr ( $hex, 8, 4 ) . '-' . substr ( $hex, 12, 4 ) . '-'
         . substr ( $hex, 16, 4 ) . '-' . substr ( $hex, 20, 12 );

  }

  // A value the generator has not given under this key before: tried up to a thousand times,
  // then an error - the generator has run out of values that are new.

  function padFakeUnique ( $key, $generator, ...$args ) {

    global $padFakeUniqueSeen;

    if ( ! is_callable ( $generator ) ) {
      padError ( 'padFakeUnique: the generator is a function, like padFakeEmail' );
      return NULL;
    }

    for ( $try = 0; $try < 1000; $try++ ) {

      $value = $generator ( ...$args );
      $hash  = is_scalar ( $value ) ? (string) $value : serialize ( $value );

      if ( ! isset ( $padFakeUniqueSeen [$key] [$hash] ) ) {
        $padFakeUniqueSeen [$key] [$hash] = TRUE;
        return $value;
      }

    }

    padError ( "padFakeUnique: no new value for '" . padMakeSafe ( (string) $key, 40 ) . "' after 1000 tries" );

    return NULL;

  }

  function padFakeUniqueReset ( $key = NULL ) {

    global $padFakeUniqueSeen;

    if ( $key === NULL )
      $padFakeUniqueSeen = [];
    else
      unset ( $padFakeUniqueSeen [$key] );

  }

  // Rows into a table: $row is a function of $i, 1 to $count, answering a row - or an array
  // used for every row. The table and column names are held to plain SQL names, the values
  // go in as placeholders. On SQLite the rows go in in one transaction, unless one is open
  // already - a factory inside a migration - since a transaction per row is a disk write each.

  function padFactory ( $table, $count, $row ) {

    if ( ! is_string ( $table ) or ! preg_match ( '/^[A-Za-z_][A-Za-z0-9_]*$/D', $table ) ) {
      padError ( "padFactory: the table is a plain name like 'orders' - not '" . padMakeSafe ( is_scalar ( $table ) ? (string) $table : get_debug_type ( $table ), 40 ) . "'" );
      return [];
    }

    if ( ! is_numeric ( $count ) or (int) $count < 0 ) {
      padError ( 'padFactory: the count is a whole number, 0 or more' );
      return [];
    }

    if ( ! is_callable ( $row ) and ! is_array ( $row ) ) {
      padError ( 'padFactory: the row is a function of $i that answers a row, or an array' );
      return [];
    }

    $connect = padDbApp ();
    $own     = ( $connect instanceof PDO and ! $connect->inTransaction () and ! isset ( $GLOBALS ['padMigratePretend'] ) );
    $rows    = [];

    if ( $own )
      $connect->beginTransaction ();

    try {

      for ( $i = 1; $i <= (int) $count; $i++ ) {

        $one = is_array ( $row ) ? $row : $row ( $i );

        if ( ! is_array ( $one ) or ! $one ) {
          padError ( "padFactory: the row function answered no row for \$i = $i" );
          break;
        }

        $names = $places = $vars = [];

        foreach ( $one as $name => $value ) {

          if ( ! is_string ( $name ) or ! preg_match ( '/^[A-Za-z_][A-Za-z0-9_]*$/D', $name ) ) {
            padError ( "padFactory: the column '" . padMakeSafe ( (string) $name, 40 ) . "' is no plain name" );
            break 2;
          }

          if ( ! is_scalar ( $value ) and $value !== NULL ) {
            padError ( "padFactory: the column $name of row $i is " . get_debug_type ( $value ) . ' - a value is text, a number, a boolean or NULL' );
            break 2;
          }

          $places [] = '{' . count ( $vars ) . '}';
          $names  [] = $name;
          $vars   [] = $value;

        }

        $id = db ( "insert into $table (" . implode ( ', ', $names ) . ') values (' . implode ( ', ', $places ) . ')', $vars );

        if ( $id === FALSE )
          break;

        if ( ! array_key_exists ( 'id', $one ) )
          $one ['id'] = $id;

        $rows [] = $one;

      }

    } finally {

      if ( $own and $connect->inTransaction () )
        $connect->commit ();

    }

    return $rows;

  }

  // The word lists - modest on purpose.

  function padFakeList ( $which ) {

    static $lists = [

      'first' => [ 'Emma', 'Liam', 'Olivia', 'Noah', 'Ava', 'Lucas', 'Sophie', 'James', 'Mia', 'Daniel',
                   'Julia', 'Thomas', 'Anna', 'David', 'Laura', 'Peter', 'Sara', 'Max', 'Eva', 'Oscar',
                   'Nina', 'Samuel', 'Ruby', 'Leo', 'Grace', 'Adam', 'Chloe', 'Felix', 'Iris', 'Hugo',
                   'Maria', 'Jack', 'Lily', 'Ben', 'Zoe', 'Victor', 'Alice', 'Ruth', 'Omar', 'Hannah' ],

      'last'  => [ 'Smith', 'Johnson', 'Brown', 'Walker', 'Wilson', 'Taylor', 'Clark', 'Lewis', 'Young', 'Hall',
                   'Allen', 'King', 'Wright', 'Scott', 'Green', 'Baker', 'Adams', 'Nelson', 'Hill', 'Campbell',
                   'Mitchell', 'Roberts', 'Carter', 'Phillips', 'Evans', 'Turner', 'Parker', 'Collins', 'Edwards', 'Stewart',
                   'Morris', 'Murphy', 'Cook', 'Rogers', 'Bell', 'Bailey', 'Cooper', 'Reed', 'Ward', 'Brooks' ],

      'city'  => [ 'Amsterdam', 'Berlin', 'Boston', 'Brussels', 'Chicago', 'Copenhagen', 'Dublin', 'Edinburgh',
                   'Lisbon', 'London', 'Lyon', 'Madrid', 'Melbourne', 'Milan', 'Montreal', 'Oslo', 'Paris',
                   'Prague', 'Rotterdam', 'Seattle', 'Stockholm', 'Sydney', 'Tokyo', 'Toronto', 'Utrecht',
                   'Vienna', 'Warsaw', 'Wellington', 'Zurich', 'Denver' ],

      'country' => [ 'Australia', 'Austria', 'Belgium', 'Brazil', 'Canada', 'Denmark', 'Finland', 'France',
                     'Germany', 'Ireland', 'Italy', 'Japan', 'Mexico', 'Netherlands', 'New Zealand', 'Norway',
                     'Poland', 'Portugal', 'Spain', 'Sweden', 'Switzerland', 'United Kingdom', 'United States' ],

      'word'  => [ 'lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit', 'sed', 'do',
                   'eiusmod', 'tempor', 'incididunt', 'ut', 'labore', 'et', 'dolore', 'magna', 'aliqua', 'enim',
                   'ad', 'minim', 'veniam', 'quis', 'nostrud', 'exercitation', 'ullamco', 'laboris', 'nisi', 'aliquip',
                   'ex', 'ea', 'commodo', 'consequat', 'duis', 'aute', 'irure', 'in', 'reprehenderit', 'voluptate',
                   'velit', 'esse', 'cillum', 'fugiat', 'nulla', 'pariatur', 'excepteur', 'sint', 'occaecat', 'cupidatat' ],

    ];

    return $lists [$which];

  }

?>
