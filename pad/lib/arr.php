<?php

  // Array helpers for the PHP of a page: reading and writing nested arrays by a dot path,
  // and the everyday work on a list of rows - one field of every row, the rows that pass a
  // test, the rows grouped, keyed, sorted, summed. A page's .php gets its rows from db(),
  // a JSON file or an API, and before these it wrote the same foreach loops and isset
  // ladders again on every page; templates had the handling options (sort, where, group)
  // but PHP had nothing. The names follow what PHP frameworks call them (Laravel's Arr).
  //
  // padArrGet       the value at a dot path - 'user.address.city' - or the default
  // padArrSet       sets a value at a dot path, making the levels on the way; the array
  // padArrHas       whether every dot path given exists - a NULL value exists
  // padArrForget    removes one or several dot paths; the array
  // padArrOnly      the top-level keys given, in the order given
  // padArrExcept    everything but the keys given - dot paths too
  // padArrPluck     one field of every row, optionally keyed by another field
  // padArrWhere     the rows that pass a comparison or a callback, keys kept
  // padArrFirst     the first value - or the first a callback passes - or the default
  // padArrLast      the last value - or the last a callback passes - or the default
  // padArrGroupBy   the rows in groups by a field, each group a list, first seen first
  // padArrKeyBy     the rows keyed by a field, a later row winning
  // padArrSortBy    the rows sorted by a field, stable, keys kept
  // padArrFlatten   the values, nested arrays flattened to a depth
  // padArrDot       a nested array as one level with dot path keys
  // padArrUndot     dot path keys back into a nested array
  // padArrWrap      a value as an array: NULL none, an array itself, anything else one item
  // padArrSum       the sum of the numbers - of the values, or a field of every row
  // padArrAvg       their average, NULL when there are none
  // padArrMin       the smallest, NULL when there are none
  // padArrMax       the largest, NULL when there are none
  //
  // A dot path names a value inside nested arrays: 'user.address.city', 'items.0.price'.
  // A segment may be an integer key, and a key that holds a dot itself - 'a.b' as one key -
  // is found as it is before the path is split. A '*' segment reads every item of that
  // level and answers a list ('items.*.price' is every price). Objects are read like
  // arrays: an ArrayAccess object through its offsets, any other object through its public
  // properties. A default that is a Closure is called only when it is needed.
  //
  // Where a set is expected, an array, a Traversable or an object's public properties are
  // the set; NULL, '' and any other plain value are an empty one, so the answer of a query
  // that found nothing goes through without a check. A callback is a Closure, an invokable
  // object or [ $object, 'method' ] - a string is always a dot path, as a field called
  // 'date' or 'count' would otherwise run PHP's function of that name. A user callback is
  // handed the row and its key; a PHP function given as a callback - intval(...) - only the
  // row, because PHP's own functions refuse an argument too many or read it as something
  // else (trim's second argument is the characters to trim).
  //
  // A wrong argument - a key that is no dot path, an unknown operator, a negative depth -
  // is reported with padError naming the function, and the function then answers an empty
  // value of its kind.

  // padArrGet reads the value at a dot path - padArrGet ( $order, 'customer.address.city' )
  // - where PHP needs an isset ladder or ?? at every level, and a value that is not there
  // answers the default instead of a warning. A value that is there and NULL is answered:
  // NULL, not the default. A NULL key answers the target itself.

  function padArrGet ( $target, $key, $default = NULL ) {

    if ( $key === NULL )
      return $target;

    $read = padArrGetter ( 'padArrGet', $key, FALSE );

    if ( $read ) {
      $value = $read ( $target, NULL, $found );
      if ( $found )
        return $value;
    }

    return padArrDefault ( $default );

  }

  // padArrSet writes the value at the dot path and makes every level on the way that is
  // missing, or that holds a plain value, an array. A '*' segment writes into every item
  // of that level. The array is passed by reference and answered too, so both
  // padArrSet ( $order, 'customer.name', 'Ann' ) and $x = padArrSet ( $x, ... ) read well.
  // An object on the path is written in place: an ArrayAccess through its offsets, a
  // stdClass or a public property as a property. A NULL key replaces the whole array.

  function padArrSet ( &$array, $key, $value ) {

    if ( $key === NULL )
      return $array = $value;

    $path = padArrPath ( 'padArrSet', $key );

    if ( $path !== NULL )
      $array = padArrWrite ( 'padArrSet', $array, $path, $value, TRUE );

    return $array;

  }

  // padArrHas is TRUE when every dot path given exists, a NULL value included - isset()
  // says no to a NULL, and that was exactly the difference a page needed. No paths at all
  // is FALSE. A '*' segment asks that the level has items and that every one of them has
  // the rest of the path.

  function padArrHas ( $array, $keys ) {

    $keys = padArrKeyList ( 'padArrHas', $keys );

    if ( ! $keys )
      return FALSE;

    foreach ( $keys as $key ) {

      if ( is_string ( $key ) and str_contains ( $key, '.' ) and padArrStep ( $array, $key, $value ) )
        continue;

      $path = padArrPath ( 'padArrHas', $key );

      if ( $path === NULL or ! padArrExists ( $array, $path ) )
        return FALSE;

    }

    return TRUE;

  }

  // padArrForget removes the dot paths given - an array of them, or a comma-separated
  // string - from the array passed by reference, and answers it. A path that is not there
  // is no error: what was asked is true afterwards. A '*' segment removes from every item
  // of that level; as the last segment it empties the level.

  function padArrForget ( &$array, $keys ) {

    $array = padArrRemoveKeys ( 'padArrForget', $array, $keys, FALSE );

    return $array;

  }

  // padArrOnly answers the top-level keys given, in the order they are given - the order
  // a form or a JSON answer is written in, not the order the array happened to have. A key
  // the array does not hold is left out. Keys are an array or a comma-separated string.

  function padArrOnly ( $array, $keys ) {

    // A Traversable that is no ArrayAccess - a generator, an IteratorAggregate - is read
    // through its items, as padArrExcept reads it: its keys are no properties, and it
    // answered nothing.

    if ( $array instanceof Traversable and ! $array instanceof ArrayAccess )
      $array = padArrItems ( $array );

    $only = [];

    foreach ( padArrKeyList ( 'padArrOnly', $keys ) as $key )
      if ( padArrStep ( $array, $key, $value ) )
        $only [$key] = $value;

    return $only;

  }

  // padArrExcept answers the array without the keys given, which may be dot paths - a
  // user row without 'password', an order without 'customer.email'. The array given is
  // not changed, not even an object inside it: an object the removal goes into is copied
  // first.

  function padArrExcept ( $array, $keys ) {

    return padArrRemoveKeys ( 'padArrExcept', padArrItems ( $array ), $keys, TRUE );

  }

  // padArrPluck answers one field of every row - padArrPluck ( $orders, 'customer.name' ) -
  // as a list, or keyed by another field of the row when $key is given: the id => name
  // pairs a select box wants. A row without the field gives NULL; a later row with the
  // same key wins. Both may be dot paths or callbacks.

  function padArrPluck ( $rows, $value, $key = NULL ) {

    $readValue = padArrGetter ( 'padArrPluck', $value );
    $readKey   = ( $key === NULL ) ? NULL : padArrGetter ( 'padArrPluck', $key );

    if ( ! $readValue or ( $key !== NULL and ! $readKey ) )
      return [];

    $plucked = [];

    foreach ( padArrItems ( $rows ) as $index => $row ) {

      $item = $readValue ( $row, $index );

      if ( ! $readKey ) {
        $plucked [] = $item;
        continue;
      }

      $name = padArrKeyOf ( 'padArrPluck', $readKey ( $row, $index ) );

      if ( $name !== NULL )
        $plucked [$name] = $item;

    }

    return $plucked;

  }

  // padArrWhere answers the rows that pass, with their keys kept. The forms:
  //
  //   padArrWhere ( $rows, fn ( $row, $key ) => ... )   the rows the callback is true for
  //   padArrWhere ( $rows, 'status', 'paid' )           the field equals the value
  //   padArrWhere ( $rows, 'total', '>', 100 )          the field compared with an operator
  //   padArrWhere ( $rows, 'active' )                   the field is true in PHP's sense
  //
  // Operators: = == === != <> !== < > <= >= (and PAD's eq ne lt gt le ge), in and
  // 'not in' with a list of values, like with SQL's % and _ wildcards, case-insensitive
  // (\% and \_ are the characters themselves). Equality is PHP's loose ==, so the string
  // '5' a database answers equals the 5 a page writes, as it does in SQL; === is strict.
  // An object compared with a plain value is not equal and neither smaller nor larger.

  function padArrWhere ( $rows, $key, $operator = NULL, $value = NULL ) {

    $rows = padArrItems ( $rows );

    if ( padArrIsCallback ( $key ) ) {

      $test = padArrCallback ( $key );
      $kept = [];

      foreach ( $rows as $index => $row )
        if ( $test ( $row, $index ) )
          $kept [$index] = $row;

      return $kept;

    }

    $arguments = func_num_args ();

    if ( $arguments <= 2 )
      $operator = 'true';
    elseif ( $arguments == 3 ) {
      $value    = $operator;
      $operator = '==';
    } else
      $operator = padArrOperator ( $operator, $value );

    $read = padArrGetter ( 'padArrWhere', $key, FALSE );

    if ( $operator === NULL or ! $read )
      return [];

    if ( $operator == 'like' )
      $value = padArrLike ( $value );
    elseif ( $operator == 'in' or $operator == 'not in' )
      $value = padArrItems ( $value );

    $kept = [];

    foreach ( $rows as $index => $row )
      if ( padArrTest ( $read ( $row, $index ), $operator, $value ) )
        $kept [$index] = $row;

    return $kept;

  }

  // padArrFirst answers the first value, or with a callback the first value the callback
  // is true for - given the value and its key - or else the default. Here a string may be
  // a callback ('is_numeric'), as nothing else can stand in this place.

  function padArrFirst ( $array, $callback = NULL, $default = NULL ) {

    return padArrFind ( 'padArrFirst', padArrItems ( $array ), $callback, $default );

  }

  // padArrLast is padArrFirst from the end.

  function padArrLast ( $array, $callback = NULL, $default = NULL ) {

    return padArrFind ( 'padArrLast', array_reverse ( padArrItems ( $array ), TRUE ), $callback, $default );

  }

  // padArrGroupBy answers [ value => [ rows ] ]: the rows in groups by a field or what a
  // callback answers for them, the groups in the order their first row came, each group a
  // list in the order of the rows. A missing or NULL field is the group ''. The template's
  // group option does the same for a tag; this is the PHP side, for a page that needs the
  // groups before rendering - a count per state, a menu per category.

  function padArrGroupBy ( $rows, $key ) {

    $read = padArrGetter ( 'padArrGroupBy', $key );

    if ( ! $read )
      return [];

    $groups = [];

    foreach ( padArrItems ( $rows ) as $index => $row ) {

      $group = padArrKeyOf ( 'padArrGroupBy', $read ( $row, $index ) );

      if ( $group !== NULL )
        $groups [$group] [] = $row;

    }

    return $groups;

  }

  // padArrKeyBy answers the rows keyed by a field or by what a callback answers for them -
  // the customers by id, to find one without a loop. A later row with the same key wins,
  // in the place the first one had.

  function padArrKeyBy ( $rows, $key ) {

    $read = padArrGetter ( 'padArrKeyBy', $key );

    if ( ! $read )
      return [];

    $keyed = [];

    foreach ( padArrItems ( $rows ) as $index => $row ) {

      $name = padArrKeyOf ( 'padArrKeyBy', $read ( $row, $index ) );

      if ( $name !== NULL )
        $keyed [$name] = $row;

    }

    return $keyed;

  }

  // padArrSortBy answers the rows sorted by a field, by what a callback answers for them,
  // or - with a NULL key - by the values themselves. The sort is stable: rows with the same
  // value keep their order, so sorting by one field and then by another sorts by the
  // second, then the first. Keys are kept, except that a list stays a list, numbered again
  // in the new order. Values compare as PHP compares them: numbers and numeric strings as
  // numbers, other text byte by byte. $descending may be TRUE or 'desc'.

  function padArrSortBy ( $rows, $key, $descending = FALSE ) {

    $items = padArrItems ( $rows );
    $read  = padArrGetter ( 'padArrSortBy', $key );

    if ( ! $read )
      return $items;

    if ( is_string ( $descending ) and in_array ( strtolower ( trim ( $descending ) ), [ 'asc', 'desc' ] ) )
      $descending = ( strtolower ( trim ( $descending ) ) == 'desc' );

    $sign   = $descending ? -1 : 1;
    $values = [];

    foreach ( $items as $index => $row )
      $values [$index] = $read ( $row, $index );

    uasort ( $values, fn ( $a, $b ) => $sign * padArrOrder ( $a, $b ) );

    $sorted = [];

    foreach ( $values as $index => $value )
      $sorted [$index] = $items [$index];

    return array_is_list ( $items ) ? array_values ( $sorted ) : $sorted;

  }

  // padArrFlatten answers the values as one list, every nested array opened up to $depth
  // levels deep: a depth of 1 opens one level, INF all of them, 0 none. An object inside
  // stays the one value it is.

  function padArrFlatten ( $array, $depth = INF ) {

    if ( ! is_int ( $depth ) and ! is_float ( $depth ) and ! ( is_string ( $depth ) and is_numeric ( $depth ) ) ) {
      padError ( 'padArrFlatten takes a number as its depth, not ' . padArrShow ( $depth ) );
      return [];
    }

    if ( $depth < 0 ) {
      padError ( 'padArrFlatten takes a depth of 0 or more, not ' . padArrShow ( $depth ) );
      return [];
    }

    return padArrFlat ( padArrItems ( $array ), $depth + 0 );

  }

  // padArrDot answers a nested array as one level, every key the dot path to its value:
  // [ 'user' => [ 'name' => 'Ann' ] ] becomes [ 'user.name' => 'Ann' ] - for a form's
  // field names, a flat settings file, a comparison of two nested arrays. An empty array
  // stays a value of its own; $prepend goes in front of every key.

  function padArrDot ( $array, $prepend = '' ) {

    if ( ! is_scalar ( $prepend ) and $prepend !== NULL ) {
      padError ( 'padArrDot takes a text to put in front of the keys, not ' . padArrShow ( $prepend ) );
      return [];
    }

    $dot = [];

    padArrDotInto ( $dot, padArrItems ( $array ), (string) $prepend );

    return $dot;

  }

  // padArrUndot is padArrDot the other way: the dot path keys made nested arrays again. A
  // '*' in a key is the character it is here, not every item.

  function padArrUndot ( $array ) {

    $nested = [];

    foreach ( padArrItems ( $array ) as $key => $value )
      $nested = padArrWrite ( 'padArrUndot', $nested, explode ( '.', (string) $key ), $value, FALSE );

    return $nested;

  }

  // padArrWrap makes a value an array: NULL is none, an array stays what it is, and
  // anything else is the one item of a list - for a parameter that takes one or several.

  function padArrWrap ( $value ) {

    if ( $value === NULL )
      return [];

    return is_array ( $value ) ? $value : [ $value ];

  }

  // padArrSum, padArrAvg, padArrMin and padArrMax work on the numbers among the values,
  // or among a field of every row (a dot path or a callback): an int, a float or a numeric
  // string - the strings a database answers - and nothing else, so a NULL, an empty field
  // or a 'n/a' is left out rather than counted as 0. The sum of no numbers is 0; the
  // average, the smallest and the largest of none are NULL, not a 0 that looks measured.

  function padArrSum ( $rows, $key = NULL ) {

    return array_sum ( padArrNumbers ( 'padArrSum', $rows, $key ) );

  }

  function padArrAvg ( $rows, $key = NULL ) {

    $numbers = padArrNumbers ( 'padArrAvg', $rows, $key );

    return $numbers ? array_sum ( $numbers ) / count ( $numbers ) : NULL;

  }

  function padArrMin ( $rows, $key = NULL ) {

    $numbers = padArrNumbers ( 'padArrMin', $rows, $key );

    return $numbers ? min ( $numbers ) : NULL;

  }

  function padArrMax ( $rows, $key = NULL ) {

    $numbers = padArrNumbers ( 'padArrMax', $rows, $key );

    return $numbers ? max ( $numbers ) : NULL;

  }

  // What follows are the pieces the functions above share.
  //
  // padArrPath turns a key into the segments of its dot path. An integer is one segment, a
  // whole float too; a Stringable object is its text. Anything else - an array, TRUE, an
  // object - is no path, and the author is told which function was handed what.

  function padArrPath ( $function, $key ) {

    if ( is_int ( $key ) )
      return [ $key ];

    if ( is_float ( $key ) and is_finite ( $key ) and floor ( $key ) == $key and abs ( $key ) < 9.0e15 )
      return [ (int) $key ];

    if ( $key instanceof Stringable )
      $key = (string) $key;

    if ( is_string ( $key ) )
      return explode ( '.', $key );

    padError ( "$function takes a dot path as its key, not " . padArrShow ( $key ) );

    return NULL;

  }

  // padArrGetter answers a function that reads a key from a row: a callback made callable
  // with the row and its key, NULL the row itself, a dot path read with padArrRead - the
  // key as one whole key first when it holds a dot. It is made once per call and used for
  // every row, so a wrong key is reported once, not once per row. $found, when the caller
  // asks for it, is set by the reader: whether the path was there.

  function padArrGetter ( $function, $key, $callbacks = TRUE ) {

    if ( $key === NULL )
      return function ( $row, $index = NULL, &$found = NULL ) { $found = TRUE; return $row; };

    if ( $callbacks and padArrIsCallback ( $key ) ) {
      $callback = padArrCallback ( $key );
      return function ( $row, $index = NULL, &$found = NULL ) use ( $callback ) { $found = TRUE; return $callback ( $row, $index ); };
    }

    $path = padArrPath ( $function, $key );

    if ( $path === NULL )
      return NULL;

    $whole = ( is_string ( $key ) and count ( $path ) > 1 );

    return function ( $row, $index = NULL, &$found = NULL ) use ( $key, $path, $whole ) {

      if ( $whole and padArrStep ( $row, $key, $value ) ) {
        $found = TRUE;
        return $value;
      }

      return padArrRead ( $row, $path, $found );

    };

  }

  // padArrRead walks the segments of a path into a target. A '*' segment reads the rest of
  // the path in every item of that level and answers the list - NULL for an item without
  // it, the way a missing field reads elsewhere in PAD - and a second '*' further on makes
  // one list of the lists. A level that is no array or object for a '*' is a path not
  // found; an empty one is there and answers an empty list.

  function padArrRead ( $target, $path, &$found ) {

    foreach ( $path as $at => $segment ) {

      if ( $segment === '*' ) {

        if ( ! is_array ( $target ) and ! is_object ( $target ) ) {
          $found = FALSE;
          return NULL;
        }

        $rest = array_slice ( $path, $at + 1 );
        $list = [];

        foreach ( padArrItems ( $target ) as $item )
          $list [] = padArrRead ( $item, $rest, $itemFound );

        if ( in_array ( '*', $rest, TRUE ) )
          $list = padArrCollapse ( $list );

        $found = TRUE;
        return $list;

      }

      if ( ! padArrStep ( $target, $segment, $target ) ) {
        $found = FALSE;
        return NULL;
      }

    }

    $found = TRUE;

    return $target;

  }

  // padArrExists is padArrRead's question without the value: is the path there. A '*'
  // segment needs items, and every item needs the rest of the path.

  function padArrExists ( $target, $path ) {

    foreach ( $path as $at => $segment ) {

      if ( $segment === '*' ) {

        $items = ( is_array ( $target ) or is_object ( $target ) ) ? padArrItems ( $target ) : [];

        if ( ! $items )
          return FALSE;

        $rest = array_slice ( $path, $at + 1 );

        foreach ( $items as $item )
          if ( ! padArrExists ( $item, $rest ) )
            return FALSE;

        return TRUE;

      }

      if ( ! padArrStep ( $target, $segment, $target ) )
        return FALSE;

    }

    return TRUE;

  }

  // padArrStep reads one key of an array or an object into $value and says whether it was
  // there. An ArrayAccess object answers through its offsets; any other object through its
  // public properties - a property holding NULL included - and then through __isset/__get.
  // A string is never indexed into: 'abc' has no key 0 here.

  function padArrStep ( $target, $segment, &$value ) {

    if ( is_array ( $target ) ) {

      if ( ! array_key_exists ( $segment, $target ) )
        return FALSE;

      $value = $target [$segment];
      return TRUE;

    }

    // A key of a type the object refuses - SplFixedArray takes integers only - is a key it
    // does not have: its TypeError ended the request.

    if ( $target instanceof ArrayAccess ) {

      try {

        if ( ! $target -> offsetExists ( $segment ) )
          return FALSE;

        $value = $target [$segment];

      } catch ( TypeError $e ) {

        return FALSE;

      }

      return TRUE;

    }

    if ( ! is_object ( $target ) )
      return FALSE;

    $properties = get_object_vars ( $target );

    if ( array_key_exists ( $segment, $properties ) ) {
      $value = $properties [$segment];
      return TRUE;
    }

    $segment = (string) $segment;

    if ( $segment !== '' and $segment [0] !== "\0" and isset ( $target -> $segment ) ) {
      $value = $target -> $segment;
      return TRUE;
    }

    return FALSE;

  }

  // padArrWrite answers the target with the value written at the path: every level that is
  // missing or plain becomes an array, a '*' segment (when $wild) writes into every item.
  // It answers a new array for an array - PHP's arrays are values - and the same object,
  // written in place, for an object.
  //
  // $wild is padArrSet's walk. padArrUndot's builds arrays only: a '*' is the character
  // it is, and an object on the way is replaced as a plain value is - written in place, the
  // object the caller handed to padArrUndot changed, or was an error without that property.

  function padArrWrite ( $function, $target, $path, $value, $wild ) {

    if ( ! $path )
      return $value;

    $segment = array_shift ( $path );

    if ( ! is_array ( $target ) and ( ! $wild or ! is_object ( $target ) ) )
      $target = [];

    if ( $wild and $segment === '*' ) {

      foreach ( padArrItems ( $target ) as $at => $item )
        $target = padArrPut ( $function, $target, $at, padArrWrite ( $function, $item, $path, $value, $wild ) );

      return $target;

    }

    if ( ! padArrStep ( $target, $segment, $child ) )
      $child = NULL;

    return padArrPut ( $function, $target, $segment, padArrWrite ( $function, $child, $path, $value, $wild ) );

  }

  // padArrPut writes one key of an array or an object and answers the target. An object
  // takes it through ArrayAccess, as a stdClass property, as a public property it has, or
  // through __set; one that can take it none of those ways - or refuses, a readonly
  // property - is reported, and left as it was. Writing back the very object that is
  // there already is skipped: it was changed in place, and a readonly property holding it
  // would refuse even that.

  function padArrPut ( $function, $target, $segment, $value ) {

    if ( is_array ( $target ) ) {
      $target [$segment] = $value;
      return $target;
    }

    if ( is_object ( $value ) and padArrStep ( $target, $segment, $now ) and $now === $value )
      return $target;

    $refused = '';

    try {

      if ( $target instanceof ArrayAccess )
        $target [$segment] = $value;
      elseif ( $target instanceof stdClass or array_key_exists ( $segment, get_object_vars ( $target ) ) or method_exists ( $target, '__set' ) )
        $target -> {$segment} = $value;
      else
        $refused = 'it has no public property of that name';

    } catch ( Throwable $e ) {

      $refused = $e -> getMessage ();

    }

    if ( $refused !== '' )
      padError ( "$function cannot write '" . padMakeSafe ( (string) $segment, 40 ) . "' into an object of class " . get_class ( $target ) . " - $refused" );

    return $target;

  }

  // padArrRemoveKeys removes every key of a key list from a target, for padArrForget and
  // padArrExcept: a key held whole - 'a.b' as one key - is removed as it is, any other is
  // a dot path. With $copy an object the removal goes into is copied first, so that
  // padArrExcept leaves what it was given as it was.

  function padArrRemoveKeys ( $function, $target, $keys, $copy ) {

    foreach ( padArrKeyList ( $function, $keys ) as $key ) {

      if ( is_string ( $key ) and str_contains ( $key, '.' ) and padArrStep ( $target, $key, $value ) ) {
        $target = padArrRemove ( $function, $target, [ $key ], $copy );
        continue;
      }

      $path = padArrPath ( $function, $key );

      if ( $path !== NULL )
        $target = padArrRemove ( $function, $target, $path, $copy );

    }

    return $target;

  }

  // padArrRemove answers the target without the path. A '*' segment removes from every
  // item of the level - all of the items when it is the last segment. A level that is not
  // there ends the walk: nothing to remove.

  function padArrRemove ( $function, $target, $path, $copy ) {

    if ( ! is_array ( $target ) and ! is_object ( $target ) )
      return $target;

    if ( $copy and is_object ( $target ) )
      $target = padArrClone ( $function, $target );

    $segment = array_shift ( $path );

    if ( $segment === '*' ) {

      foreach ( padArrItems ( $target ) as $at => $item )
        $target = $path
          ? padArrPut   ( $function, $target, $at, padArrRemove ( $function, $item, $path, $copy ) )
          : padArrUnset ( $function, $target, $at );

      return $target;

    }

    if ( ! $path )
      return padArrUnset ( $function, $target, $segment );

    if ( ! padArrStep ( $target, $segment, $child ) )
      return $target;

    $changed = padArrRemove ( $function, $child, $path, $copy );

    if ( $changed === $child )
      return $target;

    return padArrPut ( $function, $target, $segment, $changed );

  }

  // padArrUnset removes one key of an array or an object; an object that refuses - a
  // readonly property - is reported.

  function padArrUnset ( $function, $target, $segment ) {

    if ( is_array ( $target ) ) {
      unset ( $target [$segment] );
      return $target;
    }

    $refused = '';

    try {

      if ( $target instanceof ArrayAccess ) {
        if ( $target -> offsetExists ( $segment ) )
          $target -> offsetUnset ( $segment );
      } elseif ( array_key_exists ( $segment, get_object_vars ( $target ) ) )
        unset ( $target -> {$segment} );

    } catch ( Throwable $e ) {

      $refused = $e -> getMessage ();

    }

    if ( $refused !== '' )
      padError ( "$function cannot remove '" . padMakeSafe ( (string) $segment, 40 ) . "' from an object of class " . get_class ( $target ) . " - $refused" );

    return $target;

  }

  // padArrClone copies an object for padArrExcept; one PHP cannot copy - a Generator - is
  // reported and used as it is.

  function padArrClone ( $function, $object ) {

    try {
      return clone $object;
    } catch ( Throwable $e ) {
      $refused = $e -> getMessage ();
    }

    padError ( "$function cannot copy an object of class " . get_class ( $object ) . " - $refused" );

    return $object;

  }

  // padArrItems answers what a set holds as an array: an array itself, a Traversable's
  // items (a key that cannot be an array key is replaced by the next number), an object's
  // public properties, and nothing for NULL and every plain value.

  function padArrItems ( $set ) {

    if ( is_array ( $set ) )
      return $set;

    if ( $set instanceof Traversable ) {

      $items = [];

      foreach ( $set as $key => $value )
        if ( is_int ( $key ) or is_string ( $key ) )
          $items [$key] = $value;
        else
          $items [] = $value;

      return $items;

    }

    return is_object ( $set ) ? get_object_vars ( $set ) : [];

  }

  // padArrKeyList answers the keys of a key argument as a list: an array of keys, a
  // comma-separated string ('id, name, email' - trimmed, empty ones left out), or one
  // integer. NULL is no keys. A key that is no key - an array, TRUE - is reported.

  function padArrKeyList ( $function, $keys ) {

    if ( $keys === NULL )
      return [];

    if ( $keys instanceof Stringable )
      $keys = (string) $keys;

    if ( is_string ( $keys ) )
      return array_values ( array_filter ( array_map ( 'trim', explode ( ',', $keys ) ), fn ( $key ) => $key !== '' ) );

    if ( ! is_array ( $keys ) )
      $keys = [ $keys ];

    $list = [];

    foreach ( $keys as $key ) {

      if ( $key instanceof Stringable )
        $key = (string) $key;

      if ( is_float ( $key ) and is_finite ( $key ) and floor ( $key ) == $key and abs ( $key ) < 9.0e15 )
        $key = (int) $key;

      if ( is_int ( $key ) or is_string ( $key ) )
        $list [] = $key;
      else
        padError ( "$function takes keys as an array or a comma-separated string - " . padArrShow ( $key ) . ' is no key' );

    }

    return $list;

  }

  // padArrKeyOf makes a value an array key for padArrPluck, padArrGroupBy and padArrKeyBy:
  // NULL is '', TRUE and FALSE 1 and 0, a whole float its integer, an enum its value or
  // name, a Stringable its text. An array or another object can be no key: reported, and
  // NULL answered so that the row is left out.

  function padArrKeyOf ( $function, $value ) {

    if ( is_int ( $value ) or is_string ( $value ) )
      return $value;

    if ( $value === NULL )
      return '';

    if ( is_bool ( $value ) )
      return (int) $value;

    if ( is_float ( $value ) )
      return ( is_finite ( $value ) and floor ( $value ) == $value and abs ( $value ) < 9.0e15 ) ? (int) $value : (string) $value;

    if ( $value instanceof BackedEnum )
      return $value -> value;

    if ( $value instanceof UnitEnum )
      return $value -> name;

    if ( $value instanceof Stringable )
      return (string) $value;

    padError ( "$function needs a plain value to key a row by - a row answered " . padArrShow ( $value ) );

    return NULL;

  }

  // padArrIsCallback tells a callback from a dot path: a Closure, an invokable object or
  // [ $object, 'method' ]. A string never is - it is the name of a field.

  function padArrIsCallback ( $value ) {

    if ( $value instanceof Closure )
      return TRUE;

    if ( is_object ( $value ) )
      return is_callable ( $value );

    return is_array ( $value ) and count ( $value ) == 2 and is_object ( $value [0] ?? NULL ) and is_callable ( $value );

  }

  // padArrCallback answers a Closure that takes ( $value, $key ): a user's callback as it
  // is, one of PHP's own functions handed the value only - intval(...) would take the key
  // for its base, trim(...) for the characters to trim. A variadic one too: max(...) took
  // the key for a second value and answered the row, array_merge(...) refused it.

  function padArrCallback ( $callback ) {

    $closure = Closure::fromCallable ( $callback );
    $reflect = new ReflectionFunction ( $closure );

    if ( ! $reflect -> isInternal () )
      return $closure;

    if ( $reflect -> getNumberOfParameters () == 0 )
      return fn ( $value, $key ) => $closure ();

    return fn ( $value, $key ) => $closure ( $value );

  }

  // padArrFind is padArrFirst and padArrLast on items already in their order: the first
  // item, the first a callback is true for, or the default.

  function padArrFind ( $function, $items, $callback, $default ) {

    if ( $callback === NULL ) {

      foreach ( $items as $item )
        return $item;

      return padArrDefault ( $default );

    }

    if ( ! is_callable ( $callback ) ) {
      padError ( "$function takes a callback as its second argument, not " . padArrShow ( $callback ) );
      return padArrDefault ( $default );
    }

    $test = padArrCallback ( $callback );

    foreach ( $items as $key => $item )
      if ( $test ( $item, $key ) )
        return $item;

    return padArrDefault ( $default );

  }

  // padArrDefault answers a default, calling it when it is a Closure - so a default that
  // costs something, a database query, is made only when it is needed.

  function padArrDefault ( $default ) {

    return ( $default instanceof Closure ) ? $default () : $default;

  }

  // padArrOperator answers the operator padArrWhere compares with, in one spelling: case
  // and spaces do not matter, PAD's own eq ne lt gt le ge are the symbols. An operator
  // that does not exist, and in or 'not in' without a list, are reported: NULL.

  function padArrOperator ( $operator, $value ) {

    $names = [
      '='  => '==', '==' => '==', 'eq' => '==', '===' => '===',
      '!=' => '!=', '<>' => '!=', 'ne' => '!=', '!==' => '!==',
      '<'  => '<',  'lt' => '<',  '>'  => '>',  'gt'  => '>',
      '<=' => '<=', 'le' => '<=', '>=' => '>=', 'ge'  => '>=',
      'in' => 'in', 'not in' => 'not in', 'like' => 'like'
    ];

    $name = ( is_string ( $operator ) ) ? preg_replace ( '/\s+/', ' ', strtolower ( trim ( $operator ) ) ) : NULL;

    if ( $name === NULL or ! isset ( $names [$name] ) ) {
      padError ( 'padArrWhere has no operator ' . padArrShow ( $operator ) . ' - it knows = == === != <> !== < > <= >= in, not in and like' );
      return NULL;
    }

    if ( in_array ( $names [$name], [ 'in', 'not in' ] ) and ! is_array ( $value ) and ! ( $value instanceof Traversable ) ) {
      padError ( "padArrWhere: the operator '$name' needs a list of values, not " . padArrShow ( $value ) );
      return NULL;
    }

    return $names [$name];

  }

  // padArrTest compares a row's field with padArrWhere's value under an operator.

  function padArrTest ( $field, $operator, $value ) {

    switch ( $operator ) {

      case 'true':   return (bool) $field;
      case '===':    return $field === $value;
      case '!==':    return $field !== $value;
      case '==':     return padArrCompare ( $field, $value ) === 0;
      case '!=':     return padArrCompare ( $field, $value ) !== 0;
      case '<':      return padArrCompare ( $field, $value ) === -1;
      case '>':      return padArrCompare ( $field, $value ) === 1;
      case '<=':     return in_array ( padArrCompare ( $field, $value ), [ -1, 0 ], TRUE );
      case '>=':     return in_array ( padArrCompare ( $field, $value ), [ 0, 1 ], TRUE );
      case 'in':     return padArrIn ( $field, $value );
      case 'not in': return ! padArrIn ( $field, $value );
      case 'like':   return padArrMatches ( $field, $value );

    }

    return FALSE;

  }

  // padArrCompare compares two values as PHP's <=> does, -1, 0 or 1, where PHP itself
  // would warn: an object compared with a plain value is neither equal, smaller nor larger
  // (NULL); a Stringable object is its text, a backed enum its value.

  function padArrCompare ( $a, $b ) {

    if ( $a instanceof BackedEnum ) $a = $a -> value;
    if ( $b instanceof BackedEnum ) $b = $b -> value;

    if ( $a instanceof Stringable ) $a = (string) $a;
    if ( $b instanceof Stringable ) $b = (string) $b;

    if ( is_object ( $a ) !== is_object ( $b ) )
      return NULL;

    return $a <=> $b;

  }

  // padArrOrder is padArrCompare for sorting, where every pair needs an order: an object
  // comes after a plain value.

  function padArrOrder ( $a, $b ) {

    return padArrCompare ( $a, $b ) ?? ( is_object ( $a ) <=> is_object ( $b ) );

  }

  // padArrIn is the operator in: a value of the list loosely equal to the field.

  function padArrIn ( $field, $list ) {

    foreach ( padArrItems ( $list ) as $one )
      if ( padArrCompare ( $field, $one ) === 0 )
        return TRUE;

    return FALSE;

  }

  // padArrLike makes a like pattern a regular expression once, for every row: % is any
  // run of characters, _ one character, \% and \_ the characters themselves; the match is
  // the whole text, case-insensitive, unicode.

  function padArrLike ( $pattern ) {

    if ( $pattern instanceof Stringable )
      $pattern = (string) $pattern;

    if ( ! is_scalar ( $pattern ) ) {
      padError ( 'padArrWhere: the operator like needs a text pattern, not ' . padArrShow ( $pattern ) );
      return NULL;
    }

    $parts = preg_split ( '/(\\\\.|%|_)/su', mb_scrub ( (string) $pattern, 'UTF-8' ), -1, PREG_SPLIT_DELIM_CAPTURE );
    $regex = '';

    foreach ( $parts as $part )
      if ( $part === '%' )
        $regex .= '.*';
      elseif ( $part === '_' )
        $regex .= '.';
      elseif ( strlen ( $part ) > 1 and $part [0] == '\\' )
        $regex .= preg_quote ( substr ( $part, 1 ), '/' );
      else
        $regex .= preg_quote ( $part, '/' );

    return '/^' . $regex . '$/isu';

  }

  // padArrMatches is the operator like for one field: NULL, an array or an object that is
  // no text matches nothing, as NULL LIKE anything is not true in SQL.

  function padArrMatches ( $field, $regex ) {

    if ( $regex === NULL or $field === NULL )
      return FALSE;

    if ( $field instanceof Stringable )
      $field = (string) $field;

    if ( ! is_scalar ( $field ) )
      return FALSE;

    return preg_match ( $regex, (string) $field ) === 1;

  }

  // padArrNumbers answers the numbers among the values, or among a field of every row:
  // ints, floats and numeric strings (as numbers).

  function padArrNumbers ( $function, $rows, $key ) {

    $read = padArrGetter ( $function, $key );

    if ( ! $read )
      return [];

    $numbers = [];

    foreach ( padArrItems ( $rows ) as $index => $row ) {

      $value = $read ( $row, $index );

      if ( is_int ( $value ) or is_float ( $value ) )
        $numbers [] = $value;
      elseif ( is_string ( $value ) and is_numeric ( $value ) )
        $numbers [] = $value + 0;

    }

    return $numbers;

  }

  // padArrFlat opens nested arrays into one list, $depth levels deep.

  function padArrFlat ( $items, $depth ) {

    $flat = [];

    foreach ( $items as $item )
      if ( is_array ( $item ) and $depth >= 1 )
        foreach ( padArrFlat ( $item, $depth - 1 ) as $value )
          $flat [] = $value;
      else
        $flat [] = $item;

    return $flat;

  }

  // padArrDotInto writes the dot path keys of nested arrays into one array.

  function padArrDotInto ( &$dot, $items, $prefix ) {

    foreach ( $items as $key => $value )
      if ( is_array ( $value ) and $value )
        padArrDotInto ( $dot, $value, $prefix . $key . '.' );
      else
        $dot [$prefix . $key] = $value;

  }

  // padArrCollapse makes one list of a list of lists - for a path with a second '*'.

  function padArrCollapse ( $lists ) {

    $all = [];

    foreach ( $lists as $list )
      if ( is_array ( $list ) )
        foreach ( $list as $value )
          $all [] = $value;

    return $all;

  }

  // padArrShow names a wrong argument in an error message: its type, and a short text of
  // what it holds when it is a plain value.

  function padArrShow ( $value ) {

    if ( is_string ( $value ) )
      return "'" . padMakeSafe ( $value, 40 ) . "'";

    if ( is_int ( $value ) or is_float ( $value ) )
      return (string) $value;

    return get_debug_type ( $value );

  }

?>
