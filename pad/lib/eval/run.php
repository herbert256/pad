<?php

  // The evaluator proper: walks the tree lib/eval/tree.php made of an expression, with the
  // value piped in, running the segments in turn, each one's answer the input of the next
  // (padEvalSegments). $leaf holds what the tree's leaves are, each read when the walk gets
  // to it (padEvalLeaf).

  function padEvalSegments ( $tree, $value ) {

    foreach ( $tree ['segments'] as $segment )
      $value = padEvalNode ( $segment, $value, $tree ['leaves'] );

    return $value;

  }

  // A field, option, property, hex value or constant of the expression, read where the walk
  // reaches it - not in a branch a ternary does not take, nor on the right of an and, or or
  // ?? the left side decides, which were all read first, in the order written: {echo 1 ?
  // 'yes' : $missing} named the missing field, and a field read after a function that set
  // it gave its value from before. Under the strict check a field that is not there is named
  // - except one that may be missing (padEvalTreeMissing) - and one holding a list or an
  // object is named as what it is.

  function padEvalLeaf ( $leaf ) {

    global $padCheckSyntax;

    [ $one, $coalesce ] = $leaf;

    switch ( $one [1] ) {

      case 'other': return padConstant ( $one [0] );
      case 'prop':  return padPropertyValue ( $one [0] );
      case '&':     return padTagValue ( $one [0], 1 );
      case '#':     return padOptValue ( $one [0], 1 );

      // An odd number of digits has its high nibble left out, as 0xABC is 0x0ABC.

      case 'hex':   return hex2bin ( ( strlen ( $one [0] ) % 2 ? '0' : '' ) . $one [0] );

    }

    $name = $one [0];
    $via  = '';

    // $$x reads the field whose name the field x holds. The name must be an application
    // field or a path into one, as {$$x} has it (padValueName): a value naming one of the
    // engine's globals reads nothing.

    if ( $one [1] == '$$n' ) {

      if ( $padCheckSyntax and ! $coalesce )
        padEvalFieldReport ( $one [0], '$' . $one [0] );

      $name = padFieldValue ( $one [0] );
      $name = ( is_scalar ( $name ) and ! is_bool ( $name ) ) ? (string) $name : '';
      $via  = " (the name \$\${$one[0]} holds)";

      if ( ! padValueName ( $name ) ) {

        if ( $padCheckSyntax and ! $coalesce and ! padStrHidden ( $one [0] ) )
          padError ( "Expression error: the name '" . padMakeSafe ( $name, 40 ) . "' that \$\${$one[0]} takes from a value is no application variable" );

        return '';

      }

    }

    // Only an empty answer can be a field that is not there: one with a value is not looked
    // up a second time to be checked.

    $value = padFieldValue ( $name );

    if ( $padCheckSyntax and ! $coalesce and $value === '' )
      padEvalFieldReport ( $name, '$' . $name, $via );

    return $value;

  }

  // The value of a node, $me being the value piped in.

  function padEvalNode ( $node, $me, $leaf ) {

    switch ( $node [0] ) {

      case 'lit':  return $node [1];
      case 'leaf': return padEvalLeaf ( $leaf [ $node [1] ] );
      case 'me':   return $me;

      // and, or and ?? leave their right side alone when the left one decides: 0 and
      // tick() calls nothing, 'ok' ?? 7 / 0 is ok - where both sides were worked out first.

      case 'bin':

        $left = padEvalNode ( $node [2], $me, $leaf );

        if ( $node [1] == 'AND' or $node [1] == 'OR' ) {
          $true = is_array ( $left ) ? count ( $left ) > 0 : (bool) padDateText ( $left );
          if ( $node [1] == 'AND' and ! $true ) return '';
          if ( $node [1] == 'OR'  and   $true ) return 1;
        } elseif ( $node [1] == '??' and ! padEvalEmpty ( $left ) )
          return padEvalApply ( '??', $left, '', $node [4] );

        return padEvalApply ( $node [1], $left, padEvalNode ( $node [3], $me, $leaf ), $node [4] );

      case 'un':   return padEvalApply ( $node [1], NULL, padEvalNode ( $node [2], $me, $leaf ), $node [3] );

      case 'call': return padEvalCallNode ( $node, $me, $leaf );
      case 'look': return padEvalLook ( $node [1], $node [2], $node [3] );
      case 'tern': return padEvalTernary ( $node, $me, $leaf );
      case 'join': return padEvalJoin ( $node, $me, $leaf );
      case 'list': return padEvalList ( $node, $me, $leaf );
      case 'fmt':  return padEvalFormatted ( $node [1], $me );

      // A pipeline inside brackets: the head takes the value piped into the brackets, each
      // part after it the answer of the one before. An empty part is named and passes the
      // value on.

      case 'pipe':

        foreach ( $node [1] as $part )
          if ( $part )
            $me = padEvalNode ( $part, $me, $leaf );
          elseif ( $GLOBALS ['padCheckSyntax'] )
            padError ( "Expression error: an empty pipe segment inside brackets" );

        return $me;

      case 'seq':

        foreach ( $node [1] as $one )
          padEvalNode ( $one, $me, $leaf );

        return padEvalNode ( $node [2], $me, $leaf );

      // Reported under the strict check; the lenient walk goes on with what is there.

      case 'err':

        if ( $GLOBALS ['padCheckSyntax'] )
          foreach ( $node [1] as $message )
            padError ( $message );

        return $node [2] ? padEvalNode ( $node [2], $me, $leaf ) : '';

      case 'none':

        if ( $GLOBALS ['padCheckSyntax'] )
          padError ( "Expression error: there is no value in '" . trim ( $node [1] ) . "'" );

        return '';

      case 'more':

        if ( $GLOBALS ['padCheckSyntax'] )
          padError ( "More than one result back: {$node[1]}" );

        return '';

    }

  }

  // cond ? then : else. The condition counts as PAD counts a value - an array when it has
  // elements, anything else by PHP's truth. With no then branch - $title ?: 'none' - a true
  // condition is the answer itself. An else with nothing in it is reported whichever branch
  // is taken, and answers empty when it is.

  function padEvalTernary ( $node, $me, $leaf ) {

    $cond = padEvalNode ( $node [1], $me, $leaf );
    $true = is_array ( $cond ) ? count ( $cond ) > 0 : (bool) $cond;

    if ( $node [3] === NULL ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "a branch of an inline ternary is empty" );
      if ( ! $true )
        return '';
    }

    if ( $node [2] === NULL and $true )
      return $cond;

    return padEvalNode ( $true ? $node [2] : $node [3], $me, $leaf );

  }

  // Values side by side are joined as text - a date object as its text (padDateText). A list
  // among them joins nothing, and is named.

  function padEvalJoin ( $node, $me, $leaf ) {

    $values = [];

    foreach ( $node [1] as $one )
      $values [] = padDateText ( padEvalNode ( $one, $me, $leaf ) );

    foreach ( $values as $value )
      if ( is_array ( $value ) ) {
        if ( $GLOBALS ['padCheckSyntax'] )
          padError ( "Expression error: a list and another value stand side by side with no operator between them: {$node[2]}" );
        return '';
      }

    $text = array_shift ( $values );

    foreach ( $values as $value )
      $text = $text . $value;

    return $text;

  }

  function padEvalList ( $node, $me, $leaf ) {

    $items = [];

    foreach ( $node [1] as $entry )
      $items [] = ( $entry [0] == '=>' ) ? [ '=>', 'arrow' ] : [ padEvalNode ( $entry [1], $me, $leaf ), 'VAL' ];

    return padEvalArrayItems ( $items );

  }

  // A segment that is a printf format: the value piped in, formatted - a date object as its
  // text. A format sprintf cannot fill with the one value - %s and %s, a conversion it does
  // not know - is named under the strict check and empty in the lenient walk.

  function padEvalFormatted ( $format, $value ) {

    try {
      return sprintf ( $format, padDateText ( $value ) );
    } catch ( ValueError | ArgumentCountError $padEvalFormatError ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "the format '" . padMakeSafe ( $format, 40 ) . "' is not one sprintf can fill with the one value" );
      return '';
    }

  }

  // The list the values of one [ ] make: each value an element, and a value followed by =>
  // and another value a key with its value - [ 'title' => 'Minutes', 'n' => 2 ], mixed with
  // plain items as PHP mixes them. A key is one value; a => with no key before it, or no
  // value after it, is named under the strict check and left out.

  function padEvalArrayItems ( $items ) {

    global $padCheckSyntax;

    $list  = [];
    $count = count ( $items );

    for ( $n = 0; $n < $count; $n++ ) {

      if ( $items [$n] [1] == 'arrow' ) {
        if ( $padCheckSyntax )
          padError ( "Expression error: a => in a list with no key before it" );
        continue;
      }

      if ( ( $items [$n+1] [1] ?? '' ) != 'arrow' ) {
        $list [] = $items [$n] [0];
        continue;
      }

      $pairKey = $items [$n] [0];
      $n      += 2;

      if ( $n >= $count or $items [$n] [1] == 'arrow' ) {
        if ( $padCheckSyntax )
          padError ( "Expression error: the key '" . padMakeSafe ( is_scalar ( $pairKey ) ? (string) $pairKey : gettype ( $pairKey ), 40 ) . "' of a list has no value after its =>" );
        $n--;
        continue;
      }

      if ( $pairKey !== NULL and ! is_scalar ( $pairKey ) ) {
        if ( $padCheckSyntax )
          padError ( "Expression error: a key of a list is one value, not " . ( is_array ( $pairKey ) ? 'a list' : gettype ( $pairKey ) ) );
        continue;
      }

      $list [ is_bool ( $pairKey ) ? (int) $pairKey : (string) $pairKey ] = $items [$n] [0];

    }

    return $list;

  }

  // A call: the value it works on is the one before it, or else the value piped in. A
  // built-in written as a call where nothing is piped into it - at the head of an expression
  // that is no pipe body, or as an argument of another call - reads its first argument as the
  // value, as PHP's functions of those names read theirs: {echo upper($s)}, {if left($code,
  // 2) eq 'NL'}, replace('l', upper('x')); date keeps PHP's order - the format, then the
  // moment: date('Y', 0) is 1970. A pipe - {$s | left(2)} - still hands its value in, and so
  // does an application's function, exclaim('ab') reading ab.

  function padEvalCallNode ( $node, $me, $leaf ) {

    [ , $kind, $name, $input, $args, $brackets, $first ] = $node;

    $value = ( $input === NULL ) ? $me : padEvalNode ( $input, $me, $leaf );

    $parm = $literal = [];

    foreach ( $args as $arg ) {
      $parm    [] = padEvalNode ( $arg, $me, $leaf );
      $literal [] = ( $arg [0] == 'lit' and $arg [2] );
    }

    $shifted = FALSE;

    if ( $input === NULL and $brackets and $first and $parm and in_array ( $kind, [ 'pad', 'app', 'function' ], TRUE ) ) {

      $shifted = TRUE;

      if ( $kind != 'app' and in_array ( $name, [ 'date', 'time', 'timestamp' ] ) ) {
        $value = $parm [1] ?? '';
        if ( is_numeric ( $value ) )
          $value = new DateTimeImmutable ( '@' . (int) $value );
        unset ( $parm [1] );
        $parm = array_values ( $parm );
      } else
        $value = array_shift ( $parm );

    }

    return padEvalCall ( $kind, $name, $value, $parm, $literal, $shifted );

  }

  // A function of kind $kind - a built-in, an application's, PHP's, a tag, a sequence ... -
  // by its handler in eval/parms/, which reads $value, $parm and $count. $padEvalLiteral says
  // of each argument whether the template wrote it as a quoted string, and $padEvalShifted
  // that the value was the first argument. A NAN it answers - php:fdiv(0, 0) - is empty.

  function padEvalCall ( $kind, $name, $value, $parm, $padEvalLiteral = [], $padEvalShifted = FALSE ) {

    global $padInfo;

    $count      = count ( $parm );
    $padGetName = $name;

    if ( $padInfo )
      include PAD . 'events/functionParms.php';

    // A kind that is only ever a tag - select, common, keep, make, remove - has no handler
    // here. Named for what it is: a kind that only tags have, or no kind at all.

    if ( ! padValid ( $kind ) or ! file_exists ( PAD . "eval/parms/$kind.php" ) ) {

      global $padCheckSyntax;

      if ( $padCheckSyntax )
        if ( padValid ( $kind ) and file_exists ( PAD . "types/$kind.php" ) )
          padError ( "Expression error: the prefix '$kind:' belongs to tags and can not be used inside an expression - $kind:$name" );
        else
          padError ( "Expression error: there is no prefix '$kind:' - $kind:$name" );

      return '';

    }

    $value = include PAD . "eval/parms/$kind.php";

    if ( is_float ( $value ) and is_nan ( $value ) )
      $value = '';

    return $value;

  }

  // A reference that takes no arguments - field:x, data:rows, array:items, a bare word naming
  // a field ... - by its handler in eval/single/. $padEvalCoalesce says it may be missing, its
  // value going into a fallback (padEvalTreeMissing).

  function padEvalLook ( $kind, $name, $padEvalCoalesce = FALSE ) {

    global $padInfo;

    $padGetName = $name;

    if ( $padInfo )
      include PAD . 'events/functionSingle.php';

    $value = include PAD . "eval/single/$kind.php";

    if ( is_float ( $value ) and is_nan ( $value ) )
      $value = '';

    return $value;

  }

  // One operator applied: $left is NULL for a prefix one.

  function padEvalApply ( $opr, $left, $right, $written ) {

    // The empty-coalescing operator: the left value unless it is empty - '', NULL, FALSE, an
    // empty list or a field that is not there - and then the right one. 0 is a value. Either
    // is answered as it is: a date object made text here lost its zone, and the date pipe
    // after it read the text in the application's zone - hours off.

    if ( $opr == '??' ) {
      $now = padEvalEmpty ( $left ) ? $right : $left;
      return ( is_float ( $now ) and is_nan ( $now ) ) ? '' : $now;
    }

    // A date object is its text to an operator (padDateText): compared as an object it stood
    // above every string. Two date objects compared with each other are the moments they
    // hold, as PHP compares them.

    if ( ! ( in_array ( $opr, [ 'LT', 'LE', 'EQ', 'GE', 'GT', 'NE' ], TRUE )
             and $left instanceof DateTimeInterface and $right instanceof DateTimeInterface ) ) {
      $left  = padDateText ( $left );
      $right = padDateText ( $right );
    }

    if ( in_array ( $opr, padEval_one ) )
      $now = padEvalUnary ( $opr, $right );
    elseif ( ! is_array ( $left ) and ! is_array ( $right ) )
      $now = padEvalScalar ( $opr, $left, $right );
    elseif ( is_array ( $left ) and is_array ( $right ) )
      $now = padEvalArrays ( $opr, $left, $right );
    else
      $now = padEvalMixed ( $opr, $left, $right, $written );

    // A NAN - INF - INF, (-8) ** (1/3) - is no value the rest can use: PHP 8.5 warns when it
    // is turned into text or a truth. It is empty, as a NAN field is (padField).

    if ( is_float ( $now ) and is_nan ( $now ) )
      $now = '';

    return $now;

  }

  // Whether a value is empty to a fallback - ?? and default: '', NULL, FALSE or an empty
  // list. 0 and '0' are values.

  function padEvalEmpty ( $value ) {

    return $value === NULL or $value === '' or $value === FALSE or $value === [];

  }

  // ! and not invert a truth into PAD's string booleans, '1' for a false operand and '' for a
  // true one - an array is true when it has elements. The signs read the operand as a number
  // and negate it or keep it; an array is one value for them as for the binary operators: a
  // list of one is that value, a list of numbers their sum.

  function padEvalUnary ( $opr, $right ) {

    if ( $opr != 'NEG' and $opr != 'POS' )
      return ( is_array ( $right ) ? count ( $right ) : $right ) ? '' : '1';

    if ( is_array ( $right ) )
      if ( count ( $right ) == 1 )
        $right = reset ( $right );
      elseif ( padEvalNumeric ( $right ) )
        $right = array_sum ( $right );
      else {
        if ( $GLOBALS ['padCheckSyntax'] )
          padError ( ( $opr == 'NEG' ? '-' : '+' ) . " on an array of " . count ( $right ) . ": only an array of numbers sums to one value" );
        $right = '';
      }

    return ( $opr == 'NEG' ) ? - padEvalNumber ( $right, '-' ) : + padEvalNumber ( $right, '+' );

  }

  // scalar OP scalar. Comparison and logical operators answer PAD's booleans, 1 or '', and .
  // concatenates; the rest is arithmetic on the numbers the operands spell (padEvalNumber).

  function padEvalScalar ( $opr, $left, $right ) {

    // A boolean compares as PAD writes it, TRUE as 1 and FALSE as empty, and NULL as empty:
    // the bare word true is PHP's TRUE, and 'no' eq true was true because 'no' == TRUE is.

    if ( in_array ( $opr, [ 'LT', 'LE', 'EQ', 'GE', 'GT', 'NE' ] ) ) {
      if ( is_bool ( $left  ) or $left  === NULL ) $left  = $left  ? '1' : '';
      if ( is_bool ( $right ) or $right === NULL ) $right = $right ? '1' : '';
    }

    switch ( $opr ) {
      case 'LT':  return ( $left <   $right ) ? 1 : '';
      case 'LE':  return ( $left <=  $right ) ? 1 : '';
      case 'EQ':  return ( $left ==  $right ) ? 1 : '';
      case 'GE':  return ( $left >=  $right ) ? 1 : '';
      case 'GT':  return ( $left >   $right ) ? 1 : '';
      case 'NE':  return ( $left !=  $right ) ? 1 : '';
      case 'AND': return ( $left AND $right ) ? 1 : '';
      case 'OR':  return ( $left OR  $right ) ? 1 : '';
      case 'XOR': return ( $left XOR $right ) ? 1 : '';
      case '.':   return $left . $right;
    }

    $left  = padEvalNumber ( $left,  $opr );
    $right = padEvalNumber ( $right, $opr );

    // % works on whole numbers, each operand cast to an int: a divisor that casts to 0 -
    // 5 % 0.5 - divides by zero as 5 % 0 does, and a float beyond PHP's int range has no int
    // - 1e20 % 3. 0 ** -2 divides by zero too, which PHP deprecates.

    $big = fn ( $n ) => is_float ( $n ) and ! ( $n >= PHP_INT_MIN and $n < PHP_INT_MAX );

    if ( $opr == '%' and ( $big ( $left ) or $big ( $right ) ) ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "'" . ( $big ( $left ) ? $left : $right ) . "' is too large for %" );
      return '';
    }

    if ( ( $opr == '/' and $right == 0 ) or ( $opr == '%' and (int) $right == 0 )
         or ( $opr == '**' and $left == 0 and $right < 0 ) ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "a division by zero in $opr" );
      return '';
    }

    switch ( $opr ) {
      case '**': return $left ** $right;
      case '+':  return $left + $right;
      case '-':  return $left - $right;
      case '*':  return $left * $right;
      case '/':  return $left / $right;
      case '%':  return (int) $left % (int) $right;
    }

  }

  // array OP array. Equality compares the two arrays whole, the ordering operators by PHP's
  // own array order - fewer elements is smaller, one size element by element - and the
  // logical ones by an array's truth, true when it has elements. Concatenation and
  // arithmetic need one value a side, which padEvalReduce makes of each array.

  function padEvalArrays ( $opr, $left, $right ) {

    switch ( $opr ) {
      case 'EQ':  return ( $left ==  $right ) ? 1 : '';
      case 'NE':  return ( $left !=  $right ) ? 1 : '';
      case 'LT':  return ( $left <   $right ) ? 1 : '';
      case 'LE':  return ( $left <=  $right ) ? 1 : '';
      case 'GT':  return ( $left >   $right ) ? 1 : '';
      case 'GE':  return ( $left >=  $right ) ? 1 : '';
      case 'AND': return ( count ( $left ) and count ( $right ) ) ? 1 : '';
      case 'OR':  return ( count ( $left ) or  count ( $right ) ) ? 1 : '';
      case 'XOR': return ( count ( $left ) xor count ( $right ) ) ? 1 : '';
    }

    return padEvalScalar ( $opr, padEvalReduce ( $left ), padEvalReduce ( $right ) );

  }

  // array OP scalar, either way round. The logical operators read the array's truth. Else the
  // array has to become one value: a list of one is that element, a longer one the sum of
  // its elements when they and the other operand are all numbers - and anything else is an
  // error, named with the operator as the template wrote it.

  function padEvalMixed ( $opr, $left, $right, $written ) {

    $arrayLeft = is_array ( $left );
    $array     = $arrayLeft ? $left  : $right;
    $other     = $arrayLeft ? $right : $left;

    if ( in_array ( $opr, [ 'AND', 'OR', 'XOR' ] ) )
      $array = count ( $array ) > 0;
    elseif ( count ( $array ) == 1 )
      $array = padDateText ( reset ( $array ) );
    elseif ( padEvalNumeric ( $array ) and is_numeric ( $other ) )
      $array = array_sum ( $array );
    else {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( $arrayLeft
          ? "$written on an array of " . count ( $array ) . " and a value: only an array of numbers, against a number, sums to one value"
          : "$written on a value and an array of " . count ( $array ) . ": only an array of numbers, against a number, sums to one value" );
      return '';
    }

    return $arrayLeft ? padEvalScalar ( $opr, $array, $other ) : padEvalScalar ( $opr, $other, $array );

  }

  // Whether every element of an array is a number - an empty one passes, its sum being 0.

  function padEvalNumeric ( $array ) {

    foreach ( $array as $one )
      if ( ! is_numeric ( $one ) )
        return FALSE;

    return TRUE;

  }

  // An array as one value, for two arrays: the sum of its leaves when they are all numbers,
  // else its leaves run together. Nested rows are walked to their leaves.

  function padEvalReduce ( $array ) {

    $flat = [];

    array_walk_recursive ( $array, function ( $one ) use ( &$flat ) { $flat [] = padDateText ( $one ); } );

    if ( padEvalNumeric ( $flat ) )
      return array_sum ( $flat );

    $text = '';

    foreach ( $flat as $one )
      $text .= $one;

    return $text;

  }

  // Under the strict check: a field that is no value named as what it is - an object, a
  // list, or not there - with the field as the template spelled it, $x or field:x, and $via
  // saying where the name came from. A name a sandbox hid misses quietly (padStrHidden).

  function padEvalFieldReport ( $name, $spelled, $via = '' ) {

    if ( padFieldCheck ( $name ) or padStrHidden ( $name ) )
      return;

    if ( padFieldObject ( $name ) )
      padError ( "Expression error: the field '$spelled'$via is an object, not a value" );
    elseif ( padArrayCheck ( $name ) )
      padError ( "Expression error: the field '$spelled'$via is a list, not a value" );
    else
      padError ( "Expression error: there is no field named '$spelled'$via" );

  }

  // Whether a field that is no value holds an object - a stdClass record, a model. A date
  // object and one that writes itself as text are values (padField) and never get here.

  function padFieldObject ( $name ) {

    if ( str_contains ( $name, ':' ) )
      return FALSE;

    $raw = ( str_contains ( $name, '@' ) or str_contains ( $name, '.' ) ) ? padFieldAt ( $name, 0 ) : padFieldLevel ( $name, 2 );

    return is_object ( $raw );

  }

?>
